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
<p><a class="btn btn-small btn-primary btn-margin" href="<?= Url::base(TRUE) ?>"><i class="icon-arrow-left icon-white"></i> Back</a></p>

<? if($activation_is_expired) { ?>
<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
    This account has expired because it was not activated within the time period allowed.<br>
    Please contact your agency administrator to have them resend the activation email. 
    </p>
</div>
<? } else { ?>
<span class="required"><strong>*</strong> Required</span>

<?php echo form::open('account/activate/'.$user_id) ?>
  <table cellpadding="4">
    <tr>
      <td>Temporary Password <span class="required"><strong>*</strong></span></td>
      <td>
        <? $temp_password_textfield = form::password('temp_password','',array('id' => 'temp_password'));
        if(array_key_exists('temp_password', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['temp_password'] ?></p>
            <?= $temp_password_textfield ?>
        </div>
        <? } else {
            echo $temp_password_textfield; 
        } ?>
      </td>
    </tr>
    <tr>
      <td>New Password <span class="required"><strong>*</strong></span></td>
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
      <td>Confirm New Password <span class="required"><strong>*</strong></span></td>
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
    <tr>
      <td valign="top">Language expertise</td>
      <td>
        <? if(array_key_exists('languages', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['languages'] ?></p>
        <? } ?>

        <? foreach($languages as $language) { ?> 
            <label class="checkbox">
            <input type="checkbox" name="languages[]" value="<?= $language['lang_id'] ?>"<? if(!empty($field_data['languages']) AND in_array($language['lang_id'], $field_data['languages'])) echo ' checked="checked"'; ?>> <?= $language['lang_name'] ?>
            </label>
        <? } ?>        
        
        <? if(array_key_exists('languages', $errors)) { ?>
        </div>
        <? } ?>
      </td>
    </tr>
  </table>

  <label class="checkbox">
    <input type="checkbox" name="hide_email"<? if(array_key_exists('hide_email', $field_data)) echo ' checked="checked"'; ?>> Hide my email address from other users <a title="Hide my email" data-content="If this is checked your email address will be hidden from all users. If this is unchecked only users with an account can see your email address." class="popover_right"><img src="<?= Kohana::config('mainconf.url.images') ?>/icon_info.png" border="0" class="info-margin"></a>
  </label>

<p>
<? if(array_key_exists('accept_terms_of_use', $errors)) { ?>
<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['accept_terms_of_use'] ?></p>
<? } ?>
    <label class="checkbox">
      <input type="checkbox" name="accept_terms_of_use"<? if(array_key_exists('accept_terms_of_use', $field_data)) echo ' checked="checked"'; ?>> I have read and accept the <a href="#terms_of_use_modal" data-toggle="modal">Terms of Use</a> <span class="required"><strong>*</strong></span>
    </label>
<? if(array_key_exists('accept_terms_of_use', $errors)) { ?>
</div>
<? } ?>

<div id="terms_of_use_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="terms_of_use_modal_label" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="terms_of_use_modal_label">Terms of Use</h3>
  </div>
  <div class="modal-body">
    <p><?= $terms_of_use ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>

<? if(array_key_exists('accept_privacy_policy', $errors)) { ?>
<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['accept_privacy_policy'] ?></p>
<? } ?>
    <label class="checkbox">
      <input type="checkbox" name="accept_privacy_policy"<? if(array_key_exists('accept_privacy_policy', $field_data)) echo ' checked="checked"'; ?>> I have read and accept the <a href="#privacy_policy_modal" data-toggle="modal">Privacy Policy</a> <span class="required"><strong>*</strong></span>
    </label>
<? if(array_key_exists('accept_privacy_policy', $errors)) { ?>
</div>
<? } ?>

<div id="privacy_policy_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="privacy_policy_modal_label" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="privacy_policy_modal_label">Privacy Policy</h3>
  </div>
  <div class="modal-body">
    <p><?= $privacy_policy ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>
</p>

<input type="submit" value="Activate Account" class="btn">
  
<?php echo form::close() ?>

<? } ?>