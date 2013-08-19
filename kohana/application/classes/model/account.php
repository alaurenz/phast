<?php
/*******************************************************************
PHAST Software
Copyright (c)  2013, University of Washington. All rights reserved.

Author: Adrian Laurenzi

PHAST Software
Copyright (c)  2013, University of Washington. All rights reserved.

User, through installation and use of the Phast software (the 
"Software"), hereby accepts, a restricted, non-exclusive, 
non-transferable license to use the Software for academic, research, 
and internal business purposes only, and not for commercial use. 
Commercial use of the Software requires a separately executed written 
license agreement. Please contact phast-project@uw.edu if you are 
interested in a commercial license.

Permission is granted, free of charge, to any person to use the 
Software, and to copy, modify, merge, and publish it provided that 
all copies or modifications of the source code retain the following 
copyright notice and a copy of this License. 

PHAST Software
Copyright (c)  2013, University of Washington. All rights reserved.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*******************************************************************/
defined('SYSPATH') or die('No direct script access.');

class Model_Account extends Model {
    
    /* MY CLAIMED DOCS */
    public function get_claimed_docs($user_id)
    {
        return DB::select('doc_uploads.*','users.username','target_languages.lang_name')->from('doc_uploads')
                           ->join('target_languages')->on('target_languages.lang_id','=','doc_uploads.doc_language')
                           ->join('users', 'LEFT OUTER')->on('users.id','=','doc_uploads.claimed_user_id')
                           ->where('doc_uploads.claimed_user_id','=',$user_id)
                           ->order_by('doc_uploads.doc_id', 'DESC')->execute()->as_array();
    }
    
    /* USER */
    public function get_user_data($user_id)
    {
        $user_data_rows = DB::select('users.*', 'agencies.agency_name')->from('users')
                       ->join('agencies', 'left outer')->on('users.agency_id','=','agencies.agency_id')
                       ->where('users.id','=',$user_id)
                       ->limit(1)->execute()->as_array();
        return $user_data_rows[0];
    }

    public function update_user_data($user_id, $data)
    {
        DB::update('users')->set($data)->where('id','=',$user_id)->execute();
    }    
    
    public function insert_new_user($data) {
        list($inserted_user_id) = DB::insert('users', array_keys($data))->values(array_values($data))->execute();
        return $inserted_user_id;
    }
    
    public function delete_user($user_id) {
        DB::delete('users')->where('id','=',$user_id)->execute();
    }
    
    public function add_user_languages($user_id, $languages)
    {   
        foreach ($languages as $language) {
            $lang_data = array(
                'user_id' => $user_id,
                'lang_id' => $language
            );
            DB::insert('user_languages', array_keys($lang_data))->values(array_values($lang_data))->execute();
        }
    }
    
    /**
     * Finds available username derived from email address
     */
    public function generate_username($email) 
    {
        $email_s = explode("@", $email);
        $new_username = $email_s[0];
        $new_username_orig = $new_username;
        $i = 2;
        while($this->username_exists($new_username)) {
            $new_username = $new_username_orig . $i;
            $i++;
        }
        return $new_username;
    }    
    
    public static function password_is_correct($user_id, $test_password, $existing_password_hash)
    {
        $salt = Auth::instance()->find_salt($existing_password_hash);
        $test_password_hash = Auth::instance()->hash_password($test_password, $salt);
        return DB::select(array(DB::expr('COUNT(id)'), 'total'))->from('users')
                        ->where('id', '=', $user_id)
                        ->where('password', '=', $test_password_hash)
                        ->execute()->get('total');
    }
    
    public static function email_exists($email)
    {
        return DB::select(array(DB::expr('COUNT(email)'), 'total'))
                        ->from('users')->where('email', '=', $email)
                        ->execute()->get('total');
    }
    
    public static function email_unique($email)
    {
        return ! DB::select(array(DB::expr('COUNT(email)'), 'total'))
                        ->from('users')->where('email', '=', $email)
                        ->execute()->get('total');
    }
    
    public function username_exists($username)
    {
        return DB::select(array(DB::expr('COUNT(username)'), 'total'))
                        ->from('users')->where('username', '=', $username)
                        ->execute()->get('total');
    }
    
    public function generate_reset_pass($email, $token)
    {
        // delete any existing row with this email
        DB::delete('user_pass_resets')->where('email','=',$email)->execute();
        
        // add new passreset entry that expires in 2 hr
        $expire_timestamp = time() + Kohana::config('mainconf.time_until_reset_pass_expires');
        $data = array(
            'email' => $email,
            'token' => $token,
            'expire_timestamp' => $expire_timestamp
        );
        DB::insert('user_pass_resets', array_keys($data))->values(array_values($data))->execute();
    }
    
    public function delete_reset_pass_token($token)
    {
        DB::delete('user_pass_resets')->where('token','=',$token)->execute();
    }
    
    public function change_password($user_id, $new_password)
    {
        $data = array(
            'password' => Auth::instance()->hash_password($new_password)
        );
        DB::update('users')->set($data)->where('id','=',$user_id)->execute();
    }
    
    public function validate_reset_pass($token)
    {
        $token_data_row = DB::select('users.id', 'user_pass_resets.*')
                   ->from('user_pass_resets')
                   ->join('users')->on('users.email','=','user_pass_resets.email')
                   ->where('user_pass_resets.token','=',$token)->limit(1)->execute()->as_array();
        if(count($token_data_row) == 1) {
            $token_data = array_pop($token_data_row);
            if($token_data['expire_timestamp'] >= time()) {
                return $token_data['id'];
            } else {
                return FALSE;
            } 
        } else {
            return FALSE;
        }  
    }
    
    /**
     * @return true if user account has existed without being activated
     *         for longer than the allowed time, false otherwise
     */
    public function activation_is_expired($user_id) {
        $user_data = $this->get_user_data($user_id);
        return (Model_Misc::activation_is_expired($user_data['date_created']));
    }    
    
    public function activate_account($user_id) {
        if(!$this->account_is_active($user_id)) {
            $role_data = array(
                'user_id' => $user_id,
                'role_id' => 1
            );
            DB::insert('roles_users', array_keys($role_data))->values(array_values($role_data))->execute();
        }
    }
    
    public function account_is_active($user_id)
    {
        return DB::select(array(DB::expr('COUNT(user_id)'), 'total'))->from('roles_users')
            ->where('user_id', '=', $user_id)
            ->where('role_id', '=', 1)
            ->execute()->get('total');
    }
}