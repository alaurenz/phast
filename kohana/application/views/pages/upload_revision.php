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
        $('#upload_doc_revision').submit(function() {
            $('#upload_btn').attr('value', 'Uploading...'); 
            $('#upload_btn').attr('disabled', 'disabled'); // Disable submit button
        });
    });
</script>

<form name="upload_doc_revision" id="upload_doc_revision" action="<?= Url::base(TRUE) ?>upload/upload_revision/<?= $orig_doc_id ?>" method="post" enctype="multipart/form-data">

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

<!-- The revision will be translated to <strong></strong> -->

<br>
<input id="upload_btn" name="upload_btn" type="submit" class="btn" value="Upload &amp; translate">

</form>