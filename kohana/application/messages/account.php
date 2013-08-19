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
defined('SYSPATH') OR die('No direct access allowed.'); 
 
return array 
(
    'email' => array(
        'not_empty' => 'You must enter a valid email address',
        'email' => 'Email address is invalid',
        'email_available'  => 'An account already exists with that email. Did you already register?',
        'Model_Account::email_exists' => 'No account found with that email address.',
        'Model_Account::email_unique' => 'An account already exists with that email.'
        //'default'  => 'Email is invalid',
    ),
    'username' => array(
        'not_empty' => 'You must enter a username',
        'username_available' => 'An account already exists with that username. You must choose another.',
        'invalid' => 'Incorrect username or password.'
        //'max_length' => 'Title is too long. Must be 180 characters or less',
        //'default'  => 'Email is invalid',
    ),
    'authentication_code' => array(
        'not_empty' => 'You must enter a valid authentication code',
        'Model_User::code_valid' => 'The code you entered is invalid. Try again.',
        'default'  => 'Authentication code is invalid',
    ),
    'accept_terms_of_use' => array(
        'not_empty' => 'You must accept the Terms of Use to activate your account.',
        'default'  => 'Terms of Use acceptance needed',
    ),
    'accept_privacy_policy' => array(
        'not_empty' => 'You must accept the Privacy Policy to activate your account.',
        'default'  => 'Terms of Use acceptance needed',
    )
    
);