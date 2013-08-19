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

class Model_Admin extends Model {
    
    public function user_is_admin($user_id)
    {
        return DB::select(array(DB::expr('COUNT(user_id)'), 'total'))
                        ->from('roles_users')
                        ->where('user_id','=',$user_id)->where('role_id','=',2)
                        ->execute()->get('total');
    }
    
    public function get_all_completed_doc_sentences($language_id)
    {
        return DB::query(Database::SELECT, 
                           'SELECT s.source_text, t.postedit_text
                            FROM doc_uploads d, source_sentences s, translated_sentences t
                            WHERE s.doc_id = d.doc_id
                            AND t.doc_id = s.doc_id
                            AND s.sentence_id = t.sentence_id
                            AND d.completed = 1
                            AND d.doc_language = '.$language_id.'
                            GROUP BY s.sentence_id, t.sentence_id
                            ORDER BY d.doc_id ASC')->execute()->as_array();
    }    
    
    public function add_translation_model($data)
    {
        DB::insert('translator_hub_models', array_keys($data))->values(array_values($data))->execute();
    }
    
    public function set_default_translation_model($model_id)
    {
        // ensure model_id exists
        $model_id_exists = DB::select(array(DB::expr('COUNT(model_id)'), 'total'))
                                ->from('translator_hub_models')
                                ->where('model_id', '=', $model_id)
                                ->execute()->get('total');
        $config_data = array('value' => $model_id);
        if($model_id_exists) {
            DB::update('config_settings')->set($config_data)
                ->where('setting_name','=','default_lang_model_id')->execute();
        }
    }  
    
    public function get_translation_models()
    {
        return DB::select('translator_hub_models.*','target_languages.lang_name')->from('translator_hub_models')
                           ->join('target_languages', 'LEFT OUTER')->on('target_languages.lang_id','=','translator_hub_models.target_lang_id')
                           ->order_by('translator_hub_models.model_id', 'ASC')->execute()->as_array();
    }

    public function update_terms_of_use($new_tou) {
        $config_data = array("value" => $new_tou);
        DB::update('config_settings')->set($config_data)
                ->where('setting_name','=','terms_of_use')->execute();
    }

    public function update_privacy_policy($new_pp) {
        $config_data = array("value" => $new_pp);
        DB::update('config_settings')->set($config_data)
                ->where('setting_name','=','privacy_policy')->execute();
    }

    public function get_terms_of_use() {
        $row = DB::select('value')->from('config_settings')
                           ->where('setting_name','=','terms_of_use')
                           ->limit(1)->execute()->as_array();
        return $row[0]['value'];
    }

    public function get_privacy_policy() {
        $row = DB::select('value')->from('config_settings')
                           ->where('setting_name','=','privacy_policy')
                           ->limit(1)->execute()->as_array();
        return $row[0]['value'];
    }
}