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
?>
<? if($new_user_created) { ?> 
<div class="alert alert-success">
  <strong>New user was created successfully!</strong>
  <a class="close" data-dismiss="alert" href="#">&times;</a>
</div>
<? } ?>

<form class="form-inline" action="<?= Url::base(TRUE) ?>agency/index" method="post">

<? if(array_key_exists('email', $errors)) { ?>
<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['email'] ?></p>
<? } ?>
    <?= form::input('email', $field_data['email'], array('id' => 'email', 'placeholder' => 'Enter new user email...')) ?>
    <input type="submit" value="Create New User" class="btn">
    
    &nbsp; &nbsp; &nbsp;
    <a href="<?= Url::base(TRUE) ?>help/index/agency_admin" class="btn btn-small btn-primary"><i class="icon-question-sign icon-white"></i> Agency Admin Help</a>
    
<? if(array_key_exists('email', $errors)) {
    echo '</div>';
} ?>
</form>

<? if($activation_email == 'success') { ?> 
<div class="alert alert-success">
  <strong>Activation email sent successfully!</strong>
  <a class="close" data-dismiss="alert" href="#">&times;</a>
</div>
<? } else if($activation_email == 'failure') { ?>
<div class="alert alert-error">
  <strong>Failed sending activation email! Try again.</strong>
  <a class="close" data-dismiss="alert" href="#">&times;</a>
</div>
<? } ?>

<? if(count($all_agency_users) == 0) { ?>
No agency users exist.
<? } else { ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>Username</th>
        <th>Email</th>
        <th>Account Created</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
<? $i = 1;
foreach($all_agency_users as $user) { ?>
    <tr>
        <td><?= $i ?></td>
        
        <td><?= $user['username'] ?></td>
        
        <td><?= $user['email'] ?></td>
        
        <td><?= date(Kohana::config('mainconf.date_format'), $user['date_created']) ?></td>

        <td>
        	<? if($user['role_id']) {
        		echo "Activated";
        	} else {
        		echo ($user['expired'] == 1) ? 'Expired' : 'Pending Activation';
        	} ?>
        </td>
        
        <td><? if(!$user['role_id']) { ?>
        	<a title="Resend Activation Email" class="btn btn-small" href="<?= Url::base(TRUE) ?>agency/resend_activation_email/<?= $user['id'] ?>"><i class="icon-envelope"></i> Resend Activation Email</a>
        	<? } ?>
        </td>
    </tr>
<? $i++;
} ?>
    </tbody>
</table>
<? } ?>