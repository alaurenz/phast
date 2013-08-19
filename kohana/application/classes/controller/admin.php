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

class Controller_Admin extends Controller {
    
    public function before() {
        parent::before();

        if(Auth::instance()->logged_in() == 0)
            Request::instance()->redirect('account/signin');
        
        $this->model_admin = new Model_Admin;
        
        $loggedin_user_id = Auth::instance()->get_user();
        if(!$this->model_admin->user_is_admin($loggedin_user_id))
            Request::instance()->redirect('account/index'); 
    }    
    
    public static function user_is_admin() {
        $model_admin = new Model_Admin;
        $loggedin_user_id = Auth::instance()->get_user();
        return $model_admin->user_is_admin($loggedin_user_id);
    }
    
    public function action_index($language_id = "")
    {   
        $view = View::factory('template');
        $view->nav_active = "admin";
        $view->page_title = "Admin Panel";
        $view->page_content = View::factory('pages/admin_panel');
        $view->page_content->title = "Admin Panel";
        
        $model_misc = new Model_Misc;  
        
        $view->page_content->languages = $model_misc->get_languages();
        $this->request->response = $view;
    }
    
    function action_tou_pp() 
    {
        $view = View::factory('template');
        $view->page_title = "Terms of Use and Privacy Policy";
        $view->nav_active = "";
        $view->page_content = View::factory('pages/edit_tou_pp');
        
        $edit_successful = FALSE;
        $errors = array();
        if ($_POST)
        {
            $post = Validate::factory($_POST)
                            ->filter(TRUE, 'trim')
                            ->rule('terms_of_use', 'not_empty')
                            ->rule('privacy_policy', 'not_empty');
            $field_data = $post->as_array();
            if ($post->check())
            {
                $this->model_admin->update_terms_of_use($field_data['terms_of_use']);
                $this->model_admin->update_privacy_policy($field_data['privacy_policy']);
                $edit_successful = TRUE;
            }
            else {
                $errors = $post->errors('admin');
            }
        } else {
            $field_data = array(
                'terms_of_use' => $this->model_admin->get_terms_of_use(),
                'privacy_policy' => $this->model_admin->get_privacy_policy()
            );
        }
        $view->page_content->edit_successful = $edit_successful;
        $view->page_content->field_data = $field_data;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }

    function action_create_agency() 
    {
        $view = View::factory('template');
        $view->page_title = "Create Agency";
        $view->nav_active = "";
        $view->page_content = View::factory('pages/create_agency');
        
        $empty_field_data = array(
            'agency_name' => '',
            'email' => ''
        );
        $errors = array();
        $new_agency_created = "";
        if ($_POST)
        {
            $post = Validate::factory($_POST)
                            ->filter(TRUE, 'trim')
                            ->rule('agency_name', 'not_empty')
                            ->rule('email', 'not_empty')
                            ->rule('email', 'email')
                            ->rule('email', 'Model_Account::email_unique');
                            
            $field_data = $post->as_array();
            if ($post->check())
            {
                $account_model = new Model_Account();
                $agency_model = new Model_Agency();
                
                $agency_data = array(
                    'agency_name' => $field_data['agency_name']
                );
                $new_agency_id = $agency_model->insert_agency($agency_data);
                
                $new_username = $account_model->generate_username($field_data['email']);
                
                // Generate a temp password
                $temp_password = uniqid();
                $temp_password_hashed = Auth::instance()->hash_password($temp_password);              
                
                // Add new user to database (but do not add 'login' role because user account not yet activated)
                $new_user_data = array(
                    'email' => $field_data['email'],
                    'username' => $new_username,
                    'password' => $temp_password_hashed,
                    'agency_id' => $new_agency_id,
                    'date_created' => time()
                );
                $new_user_id = $account_model->insert_new_user($new_user_data);
                
                $agency_model->set_agency_admin($new_agency_id, $new_user_id);
                
                // send email w/ activation link
                $activation_link = Url::base(TRUE)."account/activate/$new_user_id";
                $expiration_time_days = round(Kohana::config('mainconf.time_until_activation_expires') / (60 * 60 * 24));
                $subject = "Activate your agency admin account";
                $message = "An an agency administrator account has been created for you for '".$field_data['agency_name']."'. To activate your account use the account details below:\n\nUsername: $new_username\nPassword: $temp_password\n\nClick following link to activate your account (this link will expire in $expiration_time_days days):\n\n$activation_link\n\nIf the link is not clickable copy and paste it into your web browser.";
                $headers = "From: noreply@phastsystem.org";
                if (!mail($field_data['email'], $subject, $message, $headers)) {
                    $errors['email'] = "Failed to send activation email to <strong>".$field_data['email']."</strong>";
                    // Delete the user and agency that was just added                    
                    $account_model->delete_user($new_user_id);
                    $agency_model->delete_agency($new_agency_id);
                } else {
                    // Activation email sent successfully
                    $new_agency_created = 1;
                    $field_data = $empty_field_data;
                }
            }
            else {
                $errors = $post->errors('account');
            }
        } else {
            $field_data = $empty_field_data;
        }
        $view->page_content->new_agency_created = $new_agency_created;
        $view->page_content->field_data = $field_data;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }
    
    function action_dump($language_id) 
    {
        $file_download_source = uniqid('dump_en_').'.txt';
        $file_download_target = uniqid('dump_target_').'.txt';
        
        $fh_source = fopen(Kohana::config('mainconf.path.docs_download').'/'.$file_download_source, 'w') or die("$file_download_source: cannot open file for writing");
        $fh_target = fopen(Kohana::config('mainconf.path.docs_download').'/'.$file_download_target, 'w') or die("$file_download_target: cannot open file for writing");
        $sentences = $this->model_admin->get_all_completed_doc_sentences($language_id);
        foreach ($sentences as $sentence) {
            fwrite($fh_source, $sentence['source_text']."\n");
            fwrite($fh_target, $sentence['postedit_text']."\n");
        }
        fclose($fh_source); fclose($fh_target);
        
        chdir(Kohana::config('mainconf.path.docs_download'));
        $files_to_zip = array(
          $file_download_source,
          $file_download_target
        );
        //if true, good; if false, zip creation failed
        
        $zip_file_download = Kohana::config('mainconf.path.docs_download').'/'.uniqid('dump_'.date("M-d-Y")).'.zip';
        $result = $this->create_zip($files_to_zip, $zip_file_download);
        
        unlink(Kohana::config('mainconf.path.docs_download').'/'.$file_download_source); 
        unlink(Kohana::config('mainconf.path.docs_download').'/'.$file_download_target);       
        
        Request::instance()->redirect(Kohana::config('mainconf.url.base').'/download.php?file='.$zip_file_download.'&name=dump_'.date("M-d-Y").'.zip&type=application/zip&delete_file=1');
    }    
    
    // Code from http://davidwalsh.name/create-zip-php
    private function create_zip($files = array(),$destination = '',$overwrite = false) {
      //if the zip file already exists and overwrite is false, return false
      if(file_exists($destination) && !$overwrite) { return false; }
      $valid_files = array();
      if(is_array($files)) {
        foreach($files as $file) {
          if(file_exists($file)) {
            $valid_files[] = $file;
          }
        }
      }
      if(count($valid_files)) {
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
          return false;
        }
        foreach($valid_files as $file) {
          $zip->addFile($file,$file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
        $zip->close();
        return file_exists($destination);
      }
      else
      {
        return false;
      }
    }
    
    function action_translation_models() 
    {
        $view = View::factory('template');
        $view->page_title = "Translation Models";
        $view->nav_active = "";
        $view->page_content = View::factory('pages/translation_models');
        $view->page_content->title = "Translation Models";
        
        $model_misc = new Model_Misc;
        $view->page_content->default_model_id = $model_misc->get_default_translation_model_id(); 
        $view->page_content->translation_models = $this->model_admin->get_translation_models();   
        $this->request->response = $view; 
    }    
    
    function action_set_default_translation_model($model_id) 
    {
        $this->model_admin->set_default_translation_model($model_id);
        Request::instance()->redirect('admin/translation_models');
    }    
    
    function action_add_translation_model() 
    {
        $view = View::factory('template');
        $view->page_title = "Add Translation Model";
        $view->nav_active = "";
        $view->page_content = View::factory('pages/add_translation_model');
        
        $empty_field_data = array(
            'descriptive_name' => '',
            'category_code' => '',
            'target_lang_id' => ''
        );
        $errors = array();
        if ($_POST)
        {
            $post = Validate::factory($_POST)
                            ->rule('descriptive_name', 'not_empty')
                            ->rule('descriptive_name', 'max_length', array(145))
                            ->rule('category_code', 'not_empty')
                            ->rule('category_code', 'max_length', array(100))
                            ->rule('target_lang_id', 'not_empty')
                            ->rule('target_lang_id', 'digit');
            $field_data = $post->as_array();
            
            if ($post->check())
            {
                $model_data = array(
                    'descriptive_name' => $field_data['descriptive_name'],
                    'category_code' => $field_data['category_code'],
                    'target_lang_id' => $field_data['target_lang_id'],
                    'date_added' => time()
                );
                $this->model_admin->add_translation_model($model_data);
                
                Request::instance()->redirect('admin/translation_models');
            }
            else {
                $errors = $post->errors('admin');
            }
        } else {
            $field_data = $empty_field_data;
        }
        
        $model_misc = new Model_Misc;  

        $view->page_content->languages = $model_misc->get_languages();
        $view->page_content->field_data = $field_data;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }
    
} 