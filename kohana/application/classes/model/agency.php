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

class Model_Agency extends Model {
    
    /**
     * @return 0 if this user is NOT an agency admin, otherwise 
     *         return agency_id of agency for which given user
     *         is administrator  
     */
    public static function get_admin_agency_id($user_id)
    {
        $user_data_rows = DB::select()->from('agencies')
                           ->where('admin_user_id','=',$user_id)
                           ->limit(1)->execute()->as_array();
        if(count($user_data_rows) == 1) {
            return $user_data_rows[0]['agency_id'];
        } else {
            return 0;
        }
    }

    public function get_agency_users($agency_id)
    {
        $users = DB::select()->from('users')
                           ->join('roles_users', 'left outer')->on('users.id','=','roles_users.user_id')
                           ->where('users.agency_id','=',$agency_id)
                           ->execute()->as_array();
        for($i = 0; $i < count($users); $i++) {
            $expired = 0;
            if($users[$i]['role_id'] != 1) {
                $expired = (Model_Misc::activation_is_expired($users[$i]['date_created'])) ? 1 : 0;
            }
            $users[$i]['expired'] = $expired;
        }
        return $users;
    }
    
    public function insert_agency($data)
    {
        list($inserted_agency_id) = DB::insert('agencies', array_keys($data))->values(array_values($data))->execute();
        return $inserted_agency_id;
    }
    
    public function delete_agency($agency_id) {
        DB::delete('agencies')->where('agency_id','=',$agency_id)->execute();
    }
    
    public function set_agency_admin($agency_id, $user_id)
    {
        $data = array("admin_user_id" => $user_id);
        DB::update('agencies')->set($data)->where('agency_id','=',$agency_id)->execute();
    }
}