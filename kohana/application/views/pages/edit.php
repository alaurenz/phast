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
    var completed_sentences = <?= $doc_upload_data['sentences_completed'] ?>;
    var total_sentences = <?= $doc_upload_data['total_sentences'] ?>;    
        
    // Show tool tip about pressing TAB
    $('.save_button').tooltip({
        title: 'You can also press the TAB key to Save',
        placement: 'right'
    });
    
    // Listen for TAB key pressed
    $('textarea').live('keydown', function(e) { 
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            var class_split = $(this).attr('class').split(/\s+/);
            var id_split = class_split[0].split("_");
            var sentence_id = id_split[1];
            var textarea_id_split = $(this).attr('id').split("_");
            var next_textarea_id = parseInt(textarea_id_split[1]) + 1;
            saveSentence(sentence_id, next_textarea_id);
        }
    });

    $("#all_sentences").accordion({
        collapsible: true,
        activate: function( event, ui ) {
            // Show set doc completed toggle button
            if(completed_sentences == total_sentences) {
                $(".toggle_doc_completed").show();
            }
        }
        //active: 0, // active: false
        //header: "h3"
    });
    
    $(".save_button").click(function() {
        var sentence_id = $(this).attr('id');
        saveSentence(sentence_id, "");
    });
    
    // Update UI to show sentence was saved (and go to next sentence)
    // Code obtained from http://net.tutsplus.com/tutorials/javascript-ajax/submit-a-form-without-page-refresh-using-jquery/
    function saveSentence(sentence_id, next_textarea_id) {
        var dataString = 'doc_id=<?= $doc_upload_data['doc_id'] ?>&sentence_id='+ sentence_id + '&text=' + $(".sentence_" + sentence_id).val();
        $.ajax({
            type: "POST",
            url: "<?= Url::base(TRUE)."postedit/save" ?>",
            data: dataString,
            success: function() {
				// Update postedited percentage completed
				if(!$("#header_" + sentence_id).hasClass("completed")) {
                    completed_sentences += 1;
                    var percent_completed = Math.round((completed_sentences / total_sentences)*100);                     
                    $(".bar").css('width', percent_completed + '%');
                    $(".bar").html(percent_completed + "&#037");
				}
                
                $("#header_" + sentence_id).addClass("completed");
                
                $("#icon_postedited_bot").hide();
                $("#icon_postedited_top").show();
                /*if($(".completed").length == <?= count($sentence_pairs) ?> ) {
                    $("#icon_completed_bot").hide();
                    $("#icon_completed_top").show();
                }*/
                // Go to next sentence
                var active_id = $("#all_sentences").accordion("option", "active");
                $("#all_sentences").accordion("option", "active", active_id+1);
                
                // Set focus (place cursor) in next sentence textarea
                if(next_textarea_id != "") {
                    $("#textarea_" + next_textarea_id).focus();
                }
                
                
            }
        });
        return false;
    }
});
</script>

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
    
    <div class="toggle_doc_completed"<? if($doc_upload_data['sentences_completed'] < $doc_upload_data['total_sentences']) { echo ' style="display:none"'; } ?>> 
    <? if($doc_upload_data['completed']) { ?>
        <a href="<?= Url::base(TRUE) ?>postedit/update_completed_status/<?= $doc_upload_data['doc_id'] ?>/0" class="btn btn-warning" type="button"><i class="icon-remove icon-white"></i> Unmark document as completed</a>
    <? } else { ?>
        <a href="<?= Url::base(TRUE) ?>postedit/update_completed_status/<?= $doc_upload_data['doc_id'] ?>/1" class="btn btn-success" type="button"><i class="icon-ok icon-white"></i> Mark document as completed</a>
    <? } ?>
    </div>
    <br>
    
    <div id="all_sentences">
    <? $i = 0;
    foreach($sentence_pairs as $sentence) {
        $header_class = ($sentence['edited']) ? ' class="completed"' : ''; ?>
        
        <h3 id="header_<?= $sentence['sentence_id'] ?>"<?= $header_class ?>><?= $sentence['source_text'] ?></h3>
        
        <? if($mt_sentences_generic) { ?>
        <div  class="tabbable">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1_<?= $sentence['sentence_id'] ?>" data-toggle="tab">Customized</a></li>
            <li><a href="#tab2_<?= $sentence['sentence_id'] ?>" data-toggle="tab">Generic</a></li>
          </ul>
          <div class="tab-content">
                <div class="tab-pane active" id="tab1_<?= $sentence['sentence_id'] ?>">
                <textarea id="textarea_<?= $i ?>" class="sentence_<?= $sentence['sentence_id'] ?> postedit_textarea" rows="4"><?= $sentence['text'] ?></textarea>
                <br> 
                <button id="<?= $sentence['sentence_id'] ?>" class="save_button btn" type="button"><i class="icon-hdd"></i> Save</button>
            </div>
            <div class="tab-pane" id="tab2_<?= $sentence['sentence_id'] ?>">
               <?= $mt_sentences_generic[$i]['mt_text_generic'] ?>
               <? /**
                   * if this sentenced has not yet been Saved:
                   *    ask user Do you prefer the generic translation (above) to the domain-specific? Yes/No
                   *    USE Radio buttons (and only record answer if one is selected)
                   */ ?>
            </div>
          </div>
        </div>
        <? } else { // The default model is the generic model ?>
            <div>            
            <textarea id="textarea_<?= $i ?>" class="sentence_<?= $sentence['sentence_id'] ?> postedit_textarea" rows="4"><?= $sentence['text'] ?></textarea>
            <br> 
            <button id="<?= $sentence['sentence_id'] ?>" class="save_button btn" type="button"><i class="icon-hdd"></i> Save</button>
            </div>
        <? } ?>
    <? $i++; 
    } ?>
    </div>
    
    <br>
    <div class="toggle_doc_completed"<? if($doc_upload_data['sentences_completed'] < $doc_upload_data['total_sentences']) { echo ' style="display:none"'; } ?>> 
    <? if($doc_upload_data['completed']) { ?>
        <a href="<?= Url::base(TRUE) ?>postedit/update_completed_status/<?= $doc_upload_data['doc_id'] ?>/0" class="btn btn-warning" type="button"><i class="icon-remove icon-white"></i> Unmark document as completed</a>
    <? } else { ?>
        <a href="<?= Url::base(TRUE) ?>postedit/update_completed_status/<?= $doc_upload_data['doc_id'] ?>/1" class="btn btn-success" type="button"><i class="icon-ok icon-white"></i> Mark document as completed</a>
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