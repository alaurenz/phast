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

class Controller_Account extends Controller {
    
    public function before() {
        parent::before();
        $this->model_account = new Model_Account();
    }

    public function action_index($language_id = "")
    {
        if(Auth::instance()->logged_in() == 0)
            Request::instance()->redirect('account/signin');
        else
            Request::instance()->redirect('account/profile/'.Auth::instance()->get_user());
    }

    public function action_claimed_documents($language_id = "")
    {
        if(Auth::instance()->logged_in() == 0)
            Request::instance()->redirect('account/signin');
        
        $view = View::factory('template');
        $view->nav_active = "claimed_documents";
        $view->page_title = "My claimed documents";
        $view->page_content = View::factory('helpers/doc_list');
        $view->page_content->hide_filter_controls = TRUE;
        
        $loggedin_user_id = Auth::instance()->get_user();
        
        $model_misc = new Model_Misc;  
        
        $view->page_content->languages = $model_misc->get_languages(); 
        $view->page_content->user_data = $this->model_account->get_user_data($loggedin_user_id);
        $view->page_content->docs = $this->model_account->get_claimed_docs($loggedin_user_id);   
        $this->request->response = $view;
    }

    public function action_profile($user_id)
    {
        if(Auth::instance()->logged_in() == 0)
            Request::instance()->redirect('account/signin');
        
        $logged_in_user_id = Auth::instance()->get_user();
        $user_data = $this->model_account->get_user_data($user_id);

        $view = View::factory('template');
        $view->nav_active = ($logged_in_user_id == $user_data['id']) ? "account" : "";
        $view->page_title = "Viewing Profile: ".$user_data['username'];
        $view->page_content = View::factory('pages/profile');
        
        $account_updated = FALSE;
        $errors = array();
        if ($_POST && $logged_in_user_id == $user_id) {
            $post = Validate::factory($_POST)
                            ->filter(TRUE, 'trim')
                            ->rule('current_password', 'not_empty')
                            ->rule('password', 'min_length', array('5'))
                            ->rule('password_confirm', 'matches', array('password'));
            $field_data = $post->as_array();
            
            if ($post->check()) {
                // ensure temp password is correct
                if(!$this->model_account->password_is_correct($logged_in_user_id, $field_data['current_password'], $user_data['password'])) {
                    $errors['temp_password'] = "Current password is incorrect";
                } else {
                    // update user account info
                    $hide_email = (array_key_exists('hide_email', $field_data)) ? 1 : 0;
                    $updated_user_data = array(
                        'password' => Auth::instance()->hash_password($field_data['password']),
                        'hide_email' => $hide_email
                    );
                    $this->model_account->update_user_data($user_id, $updated_user_data);
                    $user_data = $this->model_account->get_user_data($user_id);
                    $account_updated = TRUE;
                }
            } else {
                $errors = $post->errors('account');
            }
        }
        $view->page_content->account_updated = $account_updated;
        $view->page_content->errors = $errors;
        $view->page_content->user_data = $user_data;
        $this->request->response = $view;
    }
    
    /*
     * Logs user into system with special guest account
     * NOTE: for this to work there must be an existing
     *       account with username 'guest' and password 
     *       'guest'
     */
    public function action_guest_login()
    {
        Auth::instance()->login('guest', 'guest');
        Request::instance()->redirect('browse/index');
    }
    
    public function action_signin()
    {
        if(Auth::instance()->logged_in() != 0)
            Request::instance()->redirect('account/index');

        $view = View::factory('template');
        $view->page_title = "Login";
        $view->page_content = View::factory('login');	

        $errors = array();
        if ($_POST) {
            $user = ORM::factory('user');

            // Authenticate user
            $status = $user->login($_POST);
            
            // If the post data validates using the rules setup in the user model
            if ($status) {
                Request::instance()->redirect('browse/index');
            } else {
                $errors = $_POST->errors('account');
            }
        }
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }
    
    public function action_activate($user_id)
    {
        if(Auth::instance()->logged_in() != 0)
            Request::instance()->redirect('account/index');
        
        $model_admin = new Model_Admin;
        
        // Ensure account is not yet active
        if($this->model_account->account_is_active($user_id))
            Request::instance()->redirect('account/index');
        
        $view = View::factory('template');
        $view->page_title = "Activate account";
        $view->page_content = View::factory('pages/activate');
        
        // ensure it is not too late to active the account
        $activation_is_expired = $this->model_account->activation_is_expired($user_id);
        if(!$activation_is_expired) {
            $user_data = $this->model_account->get_user_data($user_id);        
            
            $errors = array();
            if ($_POST)
            {
                $post = Validate::factory($_POST)
                                ->filter(TRUE, 'trim')
                                ->rule('password', 'not_empty')
                                ->rule('password', 'min_length', array('5'))
                                ->rule('password_confirm', 'matches', array('password'))
                                ->rule('accept_terms_of_use', 'not_empty')
                                ->rule('accept_privacy_policy', 'not_empty');
                $field_data = $post->as_array();
                
                if ($post->check())
                {
                    // ensure temp password is correct
                    if(!$this->model_account->password_is_correct($user_data['id'], $field_data['temp_password'], $user_data['password'])) {
                        $errors['temp_password'] = "Temporary password is incorrect";
                    } else {
                        // update user account info
                        $hide_email = (array_key_exists('hide_email', $field_data)) ? 1 : 0;
                        $new_user_data = array(
                            'password' => Auth::instance()->hash_password($field_data['password']),
                            'hide_email' => $hide_email
                        );
                        $this->model_account->update_user_data($user_id, $new_user_data);
                        $user_languages = (array_key_exists('languages', $field_data)) ? $field_data['languages'] : array();
                        $this->model_account->add_user_languages($user_id, $user_languages);
                        
                        // activate account (by adding login role)
                        $this->model_account->activate_account($user_id);
                        
                        // log user in
                        Auth::instance()->login($user_data['username'], $field_data['password']);                        
                        Request::instance()->redirect('account/index');
                    }
                } else {
                    $errors = $post->errors('account');
                }
            } else {
                $field_data = array(
                    'languages' => array()
                );
            }
        
            $model_misc = new Model_Misc;
            $view->page_content->user_id = $user_data['id'];
            $view->page_content->languages = $model_misc->get_languages();
            $view->page_content->terms_of_use = $model_admin->get_terms_of_use();
            $view->page_content->privacy_policy = $model_admin->get_privacy_policy();
            $view->page_content->field_data = $field_data;
            $view->page_content->errors = $errors;
        }
        $view->page_content->activation_is_expired = $activation_is_expired;
        $this->request->response = $view;  
    }
    
    public function action_signout()
    {
        Auth::instance()->logout();
        Request::instance()->redirect('account/signin');		
    }
    
    public function action_forgot_password()
    {
        if(Auth::instance()->logged_in() != 0)
            Request::instance()->redirect('account/index');
        
        $view = View::factory('template');
        $view->page_title = "Forgot password";
        $view->page_content = View::factory('pages/forgot_password');
        
        $errors = array();
        $reset_email_sent = "";
        if ($_POST) {
            $post = Validate::factory($_POST)
                        ->filter(TRUE, 'trim')
                        ->rule('email', 'not_empty')
                        ->rule('email', 'email')
                        ->rule('email', 'Model_Account::email_exists');
                        
            $field_data = $post->as_array();
            
            if ($post->check()) {
                $reset_email_sent = $field_data['email'];
                $reset_pass_token = uniqid();
                
                // delete existing row with this email and create reset pass token row w/ expire timestamp
                $this->model_account->generate_reset_pass($reset_email_sent, $reset_pass_token);
                
                $reset_pass_link = Url::base(TRUE)."account/reset_password/$reset_pass_token";
                
                // send email w/ token
                $subject = "Reset password link";
                $message = "Use the following link to reset your password:\n\n$reset_pass_link\n\nIf the link is not clickable copy and paste it into your web browser.";
                $headers = "From: noreply@transphorm.net46.net";
                if (!mail($reset_email_sent, $subject, $message, $headers)) {
                    $errors['email'] = "Failed to send reset password email to <strong>$reset_email_sent</strong>";
                    $reset_email_sent = "";
                }
            } else {
                $errors = $post->errors('account');
            }
        }
        $view->page_content->reset_email_sent = $reset_email_sent;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }

    public function action_reset_password($token)
    {
        if(Auth::instance()->logged_in() != 0)
            Request::instance()->redirect('account/index');
        
        $view = View::factory('template');
        $view->page_title = "Reset password";
        $view->page_content = View::factory('pages/reset_password');
        
        // validate code/timestamp
        $user_id = $this->model_account->validate_reset_pass($token);
        $errors = array();
        if($user_id) {
            if ($_POST) {
                $post = Validate::factory($_POST)
                            ->filter(TRUE, 'trim')
                            ->rule('new_password', 'not_empty')
                            ->rule('new_password', 'min_length', array('5'))
                            ->rule('new_password_confirm', 'matches', array('new_password'));
                            
                $field_data = $post->as_array();
                if ($post->check()) {
                    $this->model_account->change_password($user_id, $field_data['new_password']);
                    $this->model_account->delete_reset_pass_token($token);
                    
                    Request::instance()->redirect('account/signin');
                } else {
                    $errors = $post->errors('account');
                }
            }
        } else {
            // this token has expired or does not exist
            $token = "";
        }
        $view->page_content->token = $token;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }
    
} 