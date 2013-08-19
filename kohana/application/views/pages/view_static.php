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

<?= $doc_header ?>

<div class="row-fluid">

    <div class="span9">
        <span class="large-font"><strong>Version: </strong> <?= $doc_version ?></span>
    </div>
    <div class="span3">
        <button id="show_hide_discussion" class="btn btn-small pull-right" type="button"><i class="icon-chevron-right"></i> hide panel</button>
    </div>

</div>

<div class="row-fluid">

<div id="posteditor" class="span9">

    <div class="well large-font">
    <? foreach($sentence_pairs as $sentence) { ?>
    
    <p>
    <? if($doc_version != '<i class="icon-ok"></i> Completed') { echo $sentence['source_text'].'<br>'; } ?>
    
    <?= $sentence['text'] ?>
    </p>
    
    <? } ?>
    </div>

</div>

<div id="discussion" class="span3">
    <p>
    <? if($doc_upload_data['doc_audience']) { ?><strong>Intended audience:</strong> <?= $doc_upload_data['doc_audience'] ?>
    <br><? } ?>
    <? if($doc_upload_data['doc_reading_level']) { ?><strong>Desired reading level:</strong> <?= $doc_upload_data['doc_reading_level'] ?>
    <br><? } ?>
    <? if($doc_upload_data['other_notes']) { ?><strong>Other notes:</strong> <?= $doc_upload_data['other_notes'] ?>
    <br><? } ?>
    </p>
    
    <?= $discussion ?>
</div>

</div>

<br>