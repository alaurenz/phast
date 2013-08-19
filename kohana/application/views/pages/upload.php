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
<script type="text/javascript">
    $(document).ready(function(){
        $('#translate_doc').submit(function() {
            $('#upload_btn').attr('value', 'Uploading...'); 
            $('#upload_btn').attr('disabled', 'disabled'); // Disable submit button
        });
        
        $('#doc_topic').change(function() {
            if(this.value == 'Other') {
                $('#topic_other').show();
            } else {
                $('#topic_other').hide();
            }
        });
    });
</script>

<? $form_action = ($edit_doc_id == 0) ? 'upload' : 'upload/edit_metadata/'.$edit_doc_id; ?>
<form name="translate_doc" id="translate_doc" action="<?= Url::base(TRUE).$form_action ?>" method="post" enctype="multipart/form-data">

<? if($upload_successful) { ?> 
<div class="alert alert-success">
  <strong>Upload successful!</strong>
  <a class="close" data-dismiss="alert" href="#">&times;</a>
</div>
<? } ?>

<span class="required"><strong>*</strong> Required</span>

<table cellpadding="4">
<tr>
    <td><span class="required"><strong>*</strong></span> Title </td>
    <td>
    <? $title_textfield = Form::input('doc_title', $field_data['doc_title']);
    if(array_key_exists('doc_title', $errors)) { ?>
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['doc_title'] ?></p>
        <?= $title_textfield ?>
    </div>
    <? } else {
        echo $title_textfield; 
    } ?>
    </td>
</tr>

<tr>
    <td><span class="required"><strong>*</strong></span> Topic &nbsp;<a title="Topic" data-content="The topic or general category of this document" class="popover_right"><img src="<?= Kohana::config('mainconf.url.images') ?>/icon_info.png" border="0" class="info-margin"></a></td>
    <td>
      
    <? if(array_key_exists('doc_topic', $errors)) { ?>
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['doc_topic'] ?></p>
    <? } ?>
    
    <script type="text/javascript">
    jQuery(document).ready(function() {
        var all_topics = [<? foreach($topics as $topic) { echo "'".$topic['topic']."',"; } ?>];
        $('#doc_topic').typeahead({source: all_topics, items:6});
    });
    </script>                
    
    <input name="doc_topic" id="doc_topic" type="text" data-provide="typeahead" autocomplete="off" value="<?= $field_data['doc_topic'] ?>">    
        
    <? /* DEPRECIATED (topic selection dropdown)
    <select name="doc_topic" id="doc_topic">
		<option value=""<? if($field_data['doc_topic'] == '') echo ' selected'; ?> style="color:grey;">Choose topic...</option>
		<? foreach($topics as $topic) { ?>
		<option value="<?= $topic['topic'] ?>"<? if($field_data['doc_topic'] == $topic['topic']) echo ' selected'; ?>><?= $topic['topic'] ?></option>
		<? } ?>
		<option value="Other"<? if($field_data['doc_topic'] == 'Other') echo ' selected'; ?>>Other</option>
	</select>
    
    <div id="topic_other"<? if($field_data['doc_topic'] != 'Other') { echo ' style="display:none;"'; } ?>>
        <input name="doc_topic_other" id="doc_topic_other" type="text" placeholder="Enter a topic..." value="<?= $field_data['doc_topic_other'] ?>">
    </div>	*/ ?>
	
    <? if(array_key_exists('doc_topic', $errors)) { ?>
    </div>
    <? } ?>
    </td>
</tr>
<tr>
    <td>Intended audience &nbsp;<a title="Intended audience" data-content="The audience the translated version of this document is intended for" class="popover_right"><img src="<?= Kohana::config('mainconf.url.images') ?>/icon_info.png" border="0" class="info-margin"></a></td>
    <td>
    <? $audience_textfield = Form::input('doc_audience', $field_data['doc_audience']);
    if(array_key_exists('doc_audience', $errors)) { ?>
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['doc_audience'] ?></p>
        <?= $audience_textfield ?>
    </div>
    <? } else {
        echo $audience_textfield; 
    } ?>
    </td>
</tr>
<tr>
    <td>Desired reading level &nbsp;<a title="Desired reading level" data-content="The desired reading level of the translated version of this document" class="popover_right"><img src="<?= Kohana::config('mainconf.url.images') ?>/icon_info.png" border="0" class="info-margin"></a></td>
    <td>
    <? $reading_level_textfield = Form::input('doc_reading_level', $field_data['doc_reading_level']);
    if(array_key_exists('doc_reading_level', $errors)) { ?>
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['doc_reading_level'] ?></p>
        <?= $reading_level_textfield ?>
    </div>
    <? } else {
        echo $reading_level_textfield; 
    } ?>
    </td>
</tr>
<tr>
    <td valign="top">Other notes &nbsp;<a title="Other notes" data-content="Other notes you wish to pass on to the post-editor" class="popover_right"><img src="<?= Kohana::config('mainconf.url.images') ?>/icon_info.png" border="0" class="info-margin"></a></td>
    <td>
    <? $other_notes_textfield = Form::textarea('other_notes', $field_data['other_notes'], array('rows' => 3));
    if(array_key_exists('other_notes', $errors)) { ?>
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['other_notes'] ?></p>
        <?= $other_notes_textfield ?>
    </div>
    <? } else {
        echo $other_notes_textfield; 
    } ?>
    </td>
</tr>

<? // Show only if uploading new document
if($edit_doc_id == 0) { ?>

<tr>
    <td><span class="required"><strong>*</strong></span> File</td>
    <td>
        <? 
        $doc_filefield = '<input type="file" name="doc_upload"><p>Accepted formats: <em>.docx</em>, <em>.txt</em></p>';
        if(array_key_exists('doc_upload', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['doc_upload'] ?></p>
            <?= $doc_filefield ?>
        </div>
        <? } else {
            echo $doc_filefield; 
        } ?>
    </td>
</tr>

<tr>
    <td valign="top"><span class="required"><strong>*</strong></span> Translate to &nbsp;<a title="Translate to" data-content="Select one or more languages you wish to have this document translated to" class="popover_right"><img src="<?= Kohana::config('mainconf.url.images') ?>/icon_info.png" border="0" class="info-margin"></a></td>
    <td>
    <? if(array_key_exists('languages', $errors)) { ?>
        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $errors['languages'] ?></p>
    <? } ?>
    
    <? foreach($languages as $language) { 
        $lang_id_code = $language['lang_id'].'_'.$language['lang_code']; ?> 
        <label class="checkbox">
        <input type="checkbox" name="languages[]" value="<?= $lang_id_code ?>"<? if(!empty($field_data['languages']) AND in_array($lang_id_code, $field_data['languages'])) echo ' checked="checked"'; ?>> <?= $language['lang_name'] ?>
        </label>
    <? } ?>
    <? if(array_key_exists('languages', $errors)) { ?>
        </div>
    <? } ?>
    </td>
</tr>

<? } ?>

</table>

<br>
<? $submit_btn_text = ($edit_doc_id == 0) ? 'Upload &amp; translate' : 'Edit metadata'; ?>
<input id="upload_btn" name="upload_btn" type="submit" class="btn" value="<?= $submit_btn_text ?>">

</form>