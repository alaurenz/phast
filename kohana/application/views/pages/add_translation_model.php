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
<p><a class="btn btn-small btn-primary btn-margin" href="<?= Url::base(TRUE) ?>admin/translation_models"><i class="icon-arrow-left icon-white"></i> Back</a></p>

<span class="required"><strong>*</strong> Required</span>

<?php echo form::open('admin/add_translation_model') ?>
  <table cellpadding="4">
    <tr>
      <td>Descriptive Name <span class="required"><strong>*</strong></span></td>
      <td>
        <? $descriptive_name_textfield = form::input('descriptive_name', $field_data['descriptive_name'], array('id' => 'descriptive_name', 'class' => 'input-xxlarge'));
        if(array_key_exists('descriptive_name', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['descriptive_name'] ?></p>
            <?= $descriptive_name_textfield ?>
        </div>
        <? } else {
            echo $descriptive_name_textfield; 
        } ?>
      </td>
    </tr>
    <tr>
      <td>Category Code <span class="required"><strong>*</strong></span></td>
      <td>
        <? $category_code_textfield = form::input('category_code', $field_data['category_code'], array('id' => 'category_code', 'class' => 'input-xxlarge'));
        if(array_key_exists('category_code', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['category_code'] ?></p>
            <?= $category_code_textfield ?>
        </div>
        <? } else {
            echo $category_code_textfield; 
        } ?>
      </td>
    </tr>
    <tr>
      <td valign="top">Target language <span class="required"><strong>*</strong></span></td>
      <td>
        <? if(array_key_exists('target_lang_id', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['target_lang_id'] ?></p>
        <? } ?>
        
        <? foreach($languages as $language) { ?> 
            <label><input type="radio" name="target_lang_id" value="<?= $language['lang_id'] ?>"<? if(array_key_exists('target_lang_id', $field_data) AND $field_data['target_lang_id'] == $language['lang_id']) echo ' checked="checked"'; ?> /> <?= $language['lang_name'] ?></label>
        <? } ?>
        
        <? if(array_key_exists('target_lang_id', $errors)) { ?>
        </div>
        <? } ?>
      </td>
    </tr>
  </table>
  <input type="submit" value="Add model" class="btn">
  
<?php echo form::close() ?>
