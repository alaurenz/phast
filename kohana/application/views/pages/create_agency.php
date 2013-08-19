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
<? if($new_agency_created) { ?> 
<div class="alert alert-success">
  <strong>New agency was created successfully!</strong>
  <a class="close" data-dismiss="alert" href="#">&times;</a>
</div>
<? } ?>

<span class="required"><strong>*</strong> Required</span>

<?php echo form::open('admin/create_agency') ?>
  <table cellpadding="4">
    <tr>
      <td>Agency Name <span class="required"><strong>*</strong></span></td>
      <td>
        <? $agency_name_textfield = form::input('agency_name', $field_data['agency_name'], array('id' => 'agency_name'));
        if(array_key_exists('agency_name', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['agency_name'] ?></p>
            <?= $agency_name_textfield ?>
        </div>
        <? } else {
            echo $agency_name_textfield; 
        } ?>
      </td>
    </tr>
    <tr>
      <td>Agency Admin Email <span class="required"><strong>*</strong></span></td>
      <td>
        <? $email_textfield = form::input('email', $field_data['email'], array('id' => 'email'));
        if(array_key_exists('email', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['email'] ?></p>
            <?= $email_textfield ?>
        </div>
        <? } else {
            echo $email_textfield; 
        } ?>
      </td>
    </tr>
  </table>
  <input type="submit" value="Create Agency" class="btn">
  &nbsp;&nbsp;
  <strong>NOTE:</strong> this will send the agency admin an email with details on how to active their administrator account.

<?php echo form::close() ?>
