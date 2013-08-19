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
<p><a class="btn btn-small btn-primary btn-margin" href="<?= Url::base(TRUE) ?>admin"><i class="icon-arrow-left icon-white"></i> Back</a></p>

<form name="modify_tou_pp" id="modify_tou_pp" action="" method="post">

<? if($edit_successful) { ?> 
<div class="alert alert-success">
  <strong>Terms of Use and/or Privacy Policy were modified.</strong>
  <a class="close" data-dismiss="alert" href="#">&times;</a>
</div>
<? } ?>

<table cellpadding="4">
<tr>
    <td valign="top">Terms of Use</td>
    <td>
    <? $terms_of_use_textfield = Form::textarea('terms_of_use', $field_data['terms_of_use'], array('rows' => 8,  'style' => 'width:500px;'));
    if(array_key_exists('terms_of_use', $errors)) { ?>
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['terms_of_use'] ?></p>
        <?= $terms_of_use_textfield ?>
    </div>
    <? } else {
        echo $terms_of_use_textfield; 
    } ?>
    </td>
</tr>

<tr>
    <td valign="top">Privacy Policy</td>
    <td>
    <? $privacy_policy_textfield = Form::textarea('privacy_policy', $field_data['privacy_policy'], array('rows' => 8, 'style' => 'width:500px;'));
    if(array_key_exists('privacy_policy', $errors)) { ?>
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['privacy_policy'] ?></p>
        <?= $privacy_policy_textfield ?>
    </div>
    <? } else {
        echo $privacy_policy_textfield; 
    } ?>
    </td>
</tr>

</table>

<br>
<input id="modify_btn" name="modify_btn" type="submit" class="btn" value="Modify">

</form>