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

class Controller_Agency extends Controller {
    
    public function before() {
        parent::before();
        if(Auth::instance()->logged_in() == 0) {
            Request::instance()->redirect('account/signin');
        }
        
        $this->agency_model = new Model_Agency();
        $this->agency_id = $this->agency_model->get_admin_agency_id(Auth::instance()->get_user());
        // Ensure this user is an agency admin
        if($this->agency_id == 0) {
            Request::instance()->redirect('account/index');
        }
    }
    
    public function action_index($activation_email = "")
    {
        $view = View::factory('template');
        $view->page_title = "Create User Account";
        $view->nav_active = "agency";
        $view->page_content = View::factory('pages/create_view_users');
        
        $empty_field_data = array(
            'email' => ''
        );
        $errors = array();
        $new_user_created = "";
        if ($_POST)
        {
            $post = Validate::factory($_POST)
                            ->filter(TRUE, 'trim')
                            ->rule('email', 'not_empty')
                            ->rule('email', 'email')
                            ->rule('email', 'Model_Account::email_unique');
                            
            $field_data = $post->as_array();
            if ($post->check())
            {
                $account_model = new Model_Account();

                $new_username = $account_model->generate_username($field_data['email']);
                
                // Generate a temp password
                $temp_password = uniqid();
                $temp_password_hashed = Auth::instance()->hash_password($temp_password);
                
                // Add new user to database (but do not add 'login' role because user account not yet activated)
                $new_user_data = array(
                    'email' => $field_data['email'],
                    'username' => $new_username,
                    'password' => $temp_password_hashed,
                    'agency_id' => $this->agency_id,
                    'date_created' => time()
                );
                $new_user_id = $account_model->insert_new_user($new_user_data);
                
                // send email w/ activation link
                $subject = "Activate your PHAST account";
                $message = $this->get_activation_email_text($new_user_id, $new_username, $temp_password);
                $headers = "From: noreply@phastsystem.org";
                if (!mail($field_data['email'], $subject, $message, $headers)) {
                    $errors['email'] = "Failed to send activation email to <strong>".$field_data['email']."</strong>";
                    // Delete the user who was just added                    
                    $account_model->delete_user($new_user_id);
                } else {
                    // Activation email sent successfully
                    $new_user_created = 1;
                    $field_data = $empty_field_data;
                }
            }
            else {
                $errors = $post->errors('account');
            }
        } else {
            $field_data = $empty_field_data;
        }
        $view->page_content->activation_email = $activation_email;
        $view->page_content->new_user_created = $new_user_created;
        $view->page_content->all_agency_users = $this->agency_model->get_agency_users($this->agency_id);
        $view->page_content->field_data = $field_data;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }

    public function action_resend_activation_email($user_id)
    {
        $account_model = new Model_Account();
        if($account_model->account_is_active($user_id))
            Request::instance()->redirect('agency/index');

        $user_data = $account_model->get_user_data($user_id);

        // Generate a temp password
        $temp_password = uniqid();
        $temp_password_hashed = Auth::instance()->hash_password($temp_password);
        
        // Add new user to database (but do not add 'login' role because user account not yet activated)
        $updated_user_data = array(
            'password' => $temp_password_hashed,
            'date_created' => time()
        );
        $account_model->update_user_data($user_id, $updated_user_data);
        
        // TODO remove
        echo "pass: $temp_password";
        
        // send email w/ activation link
        $subject = "Activate your PHAST account";
        $message = $this->get_activation_email_text($user_id, $user_data['username'], $temp_password);
        $headers = "From: noreply@phastsystem.org";
        if (!mail($user_data['email'], $subject, $message, $headers)) {
            // error sending email
            Request::instance()->redirect('agency/index/failure');
        } else {
            // Activation email sent successfully
            Request::instance()->redirect('agency/index/success');
        }
        
    }

    private function get_activation_email_text($user_id, $username, $password)
    {
        $activation_link = Url::base(TRUE)."account/activate/$user_id";
        return "To activate your account use the account details below:\n\nUsername: $username\nPassword: $password\n\nClick following link to activate your account (this link will expire in ".Kohana::config('mainconf.time_until_activation_expires_days')." days):\n\n$activation_link\n\nIf the link is not clickable copy and paste it into your web browser.";
    }
} 