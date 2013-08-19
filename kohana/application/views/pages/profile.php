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
<table width="100%">
  <tr>
    <td valign="top" width="50%">
      <h3><?= $user_data['username'] ?></h3>
      <table cellpadding="5">
        <tr>
          <td><strong>Email</strong></td>
          <td><?= ($user_data['hide_email'] == 1) ? '<em>hidden</em>' : $user_data['email'] ?></td>
        </tr>
        <tr>
          <td><strong>Agency</strong></td>
          <td><?= $user_data['agency_name'] ?></td>
        </tr>
        <tr>
          <td><strong>User since</strong></td>
          <td><?= date(Kohana::config('mainconf.date_format'), $user_data['date_created']) ?></td>
        </tr>
      </table>
    </td>

    <td valign="top" width="50%">
      <? if(Auth::instance()->get_user() == $user_data['id']) { ?>
        <h3>Update My Account</h3>
        <? if($account_updated) { ?> 
        <div class="alert alert-success">
          <strong>You account information has been updated.</strong>
          <a class="close" data-dismiss="alert" href="#">&times;</a>
        </div>
        <? } 
          echo form::open('account/profile/'.$user_data['id']) ?>
          <table>
            <tr>
              <td>Current Password</td>
              <td>
                <? $current_password_textfield = form::password('current_password','',array('id' => 'current_password'));
                if(array_key_exists('current_password', $errors)) { ?>
                <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
                    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['current_password'] ?></p>
                    <?= $current_password_textfield ?>
                </div>
                <? } else {
                    echo $current_password_textfield; 
                } ?>
              </td>
            </tr>
            <tr>
              <td>New Password</td>
              <td>
                <? $password_textfield = form::password('password','',array('id' => 'password'));
                if(array_key_exists('password', $errors)) { ?>
                <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
                    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['password'] ?></p>
                    <?= $password_textfield ?>
                </div>
                <? } else {
                    echo $password_textfield; 
                } ?>
              </td>
            </tr>
            <tr>
              <td>Confirm New Password &nbsp; </td>
              <td>
                <? $password_confirm_textfield = form::password('password_confirm','',array('id' => 'password_confirm'));
                if(array_key_exists('password_confirm', $errors)) { ?>
                <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
                    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['password_confirm'] ?></p>
                    <?= $password_confirm_textfield ?>
                </div>
                <? } else {
                    echo $password_confirm_textfield; 
                } ?>
              </td>
            </tr>
          </table>

          <label class="checkbox">
            <input type="checkbox" name="hide_email" <? if($user_data['hide_email'] == 1) echo ' checked="checked"'; ?>> Hide my email address from other users &nbsp;<a title="Hide my email" data-content="If this is checked your email address will be hidden from all users. If this is unchecked only users with an account can see your email address." class="popover_right"><img src="<?= Kohana::config('mainconf.url.images') ?>/icon_info.png" border="0" class="info-margin"></a>
          </label>

          <input type="submit" value="Update Account" class="btn">
      <?php echo form::close() ?>
      <? } ?>
    </td>
  </tr>
</table>

