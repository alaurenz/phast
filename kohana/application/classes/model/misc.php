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

class Model_Misc extends Model {
    
    public function get_languages() 
    {
        return DB::select()->from('target_languages')->order_by('lang_id', 'ASC')->execute()->as_array();    
    }
    
    public function get_topics() 
    {
        return DB::select()->from('topics')->order_by('topic_id', 'ASC')->execute()->as_array();    
    }
    
    public function get_default_translation_model_id()
    {
        $row = DB::select('value')->from('config_settings')
                           ->where('setting_name','=','default_lang_model_id')
                           ->limit(1)->execute()->as_array();
        return $row[0]['value'];
    }

    /**
     * @param int $date_user_created UNIX timestamp (seconds) when user 
     *         account was created
     * @return true if the given date when some user was created translates 
     *         to an expired account, false otherwise 
     */
    public static function activation_is_expired($date_user_created)
    {
        $time_until_activation_expires_seconds = Kohana::config('mainconf.time_until_activation_expires_days') * (60 * 60 * 24);
        $expiration_date = $date_user_created + $time_until_activation_expires_seconds;
        return (time() > $expiration_date);
    }
}