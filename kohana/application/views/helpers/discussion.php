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
    // Add comment dynamically to discussion
    $("#add_comment_btn").click(function() {
        if($("#comment_text").val()) {
            var dataString = 'doc_id=<?= $doc_id ?>&comment_text='+ $("#comment_text").val();
            $.ajax({
                type: "POST",
                url: "<?= Url::base(TRUE)."postedit/add_comment" ?>",
                data: dataString,
                success: function() {
    		        $("#discussion_none").hide();
                    $('#discussion_table > tbody:last').append('<tr><td><a href="#"><?= $user_data['username'] ?></a> says:<br>' + $("#comment_text").val() + '</td></tr>');
                    $("#comment_text").val("");
                }
            });
            return false;
        }
    });
});
</script>

<table id="discussion_table" class="table table-striped">
<tbody>
<? if(count($comments) > 0) { 
    foreach($comments as $comment) {  ?>
    <tr>
        <td><a href="#"><?= $comment['username'] ?></a> says:<br><?= $comment['comment_text'] ?></td>
    </tr>
<? }
} ?>
</tbody>
</table>

<? if(count($comments) == 0) { ?>
<div id="discussion_none" class="well well-small">No comments yet</div>
<? } ?>

<textarea id="comment_text" name="comment_text" class="comment_textarea pull-right" rows="3" placeholder="Enter a comment..."></textarea> 

<button type="button" id="add_comment_btn" name="add_comment_btn" class="btn pull-right"><i class="icon-comment"></i> Add comment</button>

