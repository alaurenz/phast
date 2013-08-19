<table class="table">
    <thead>
    <tr>
        <th>Title</th>
        <th>Topic</th>
        <th>Language</th>
        <th>Uploaded</th>
        <th>Claimed</th>
        <th>Postedited</th>
        <th>Completed</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $doc_upload_data['doc_title'] ?><br>
        <a title="Edit metadata" data-content="Edit the metadata of this document (title, topic, intended audience, etc)" class="btn btn-mini btn-margin popover_right" href="<?= Url::base(TRUE) ?>upload/edit_metadata/<?= $doc_upload_data['doc_id'] ?>"><i class="icon-pencil"></i> edit metadata</a>
        </td>
        
        <td><?= $doc_upload_data['doc_topic'] ?></td>
        
        <td><?= $doc_upload_data['lang_name'] ?></td>
        
        <td><?= date(Kohana::config('mainconf.date_format'), $doc_upload_data['date_uploaded']) ?><br>
        <a title="View uploaded version" data-content="Show original machine-translated version of this document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE) ?>postedit/view_mt/<?= $doc_upload_data['doc_id'] ?>"><i class="icon-eye-open"></i> view</a>
        </td>
        
        <td> <? if($doc_upload_data['claimed_user_id'] != 0) { ?>
            <div class="icon-placeholder">
                <a href="<?= Url::base(TRUE).'account/profile/'.$doc_upload_data['claimed_user_id'] ?>"><?= $doc_upload_data['username'] ?></a>
            </div><br>
            <? if($doc_upload_data['claimed_user_id'] == Auth::instance()->get_user()) { ?>
                <a title="Unclaim document" data-content="If you cannot complete postediting this document unclaim it so that someone else can claim it and finish postediting" class="btn btn-inverse btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE).'postedit/unclaim/'.$doc_upload_data['doc_id'] ?>"><i class="icon-remove-sign icon-white"></i> unclaim</a>
            <? } ?>
        <? } else {  ?>
            <img src="<?= Kohana::config('mainconf.url.images') ?>/icon_available_mini.png" alt="Not claimed">
            <? if($doc_upload_data['claimed_user_id'] == 0) { ?>
                <br><a title="Claim document" data-content="Commit to postediting this document which prevents other users from editing it" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE).'postedit/claim/'.$doc_upload_data['doc_id'] ?>"><i class="icon-plus-sign"></i> claim</a>
            <? }
        } ?>
        </td>
        
        <td>
        <div id="icon_postedited_top" style="display:none"> 
            <? if($doc_upload_data['total_sentences'] != 0) {
        			$percent_complete = round(($doc_upload_data['sentences_completed'] / $doc_upload_data['total_sentences']), 2) * 100;
        		} else {
        			$percent_complete = 100;
        		} ?>
				<div style="width:100px; height:24px;"><div class="progress">
                <div class="bar" style="width: <?= $percent_complete ?>%;"><?= $percent_complete ?>&#037;</div>
                </div></div>
            
            <? if($doc_upload_data['claimed_user_id'] == Auth::instance()->get_user()) { ?>
                <a title="Postedit" data-content="Postedit the document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE).'postedit/view_edit/'.$doc_upload_data['doc_id'] ?>"><i class="icon-edit"></i> postedit</a>
            <? } else { ?>
                <a title="View postedited version" data-content="Show postedited version of the document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE).'postedit/view_edit/'.$doc_upload_data['doc_id'] ?>"><i class="icon-eye-open"></i> view</a>
           <? } ?>
        </div>
        <div id="icon_postedited_bot">
            <div style="width:100px; height:24px;"><div class="progress">
            <div class="bar" style="width: <?= $percent_complete ?>%;"><?= $percent_complete ?>&#037;</div>
            </div></div>
        <? if($doc_upload_data['postedited'] == 1) { ?>
            
            <? if($doc_upload_data['claimed_user_id'] == Auth::instance()->get_user()) { ?>
                <a title="Postedit" data-content="Postedit the document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE).'postedit/view_edit/'.$doc_upload_data['doc_id'] ?>"><i class="icon-edit"></i> postedit</a>
            <? } else { ?>
                <a title="View postedited version" data-content="Show postedited version of the document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE).'postedit/view_edit/'.$doc_upload_data['doc_id'] ?>"><i class="icon-eye-open"></i> view</a>
            <? }
            
        } else { ?>
            <? if($doc_upload_data['claimed_user_id'] == Auth::instance()->get_user()) { ?>
                <a title="Postedit" data-content="Postedit the document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE).'postedit/view_edit/'.$doc_upload_data['doc_id'] ?>"><i class="icon-edit"></i> postedit</a>
            <? }
        } ?>
        </div>
        </td>
        
        <td>
        <div id="icon_completed_top" style="display:none">
            <img id="icon_completed_top" src="<?= Kohana::config('mainconf.url.images') ?>/icon_complete.png" alt="Complete">
            <br>
            
            <div class="pull-left">
                <a title="View complete version" data-content="Show complete postedited version of document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE) ?>postedit/view_complete/<?= $doc_upload_data['doc_id'] ?>"><i class="icon-eye-open"></i> view</a>&nbsp;
            </div>

            <div class="btn-group pull-left">
                <a class="btn btn-mini dropdown-toggle btn-margin" data-toggle="dropdown" href="#"><i class="icon-download-alt"></i> download <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= Url::base(TRUE) ?>postedit/download_doc/<?= $doc_upload_data['doc_id'] ?>/txt">as .TXT</a></li>
                    <li><a href="<?= Url::base(TRUE) ?>postedit/download_doc/<?= $doc_upload_data['doc_id'] ?>/docx">as .DOCX</a></li>
                </ul>
            </div>
        </div>
        
        <div id="icon_completed_bot">
        <? if($doc_upload_data['completed'] == 1) { ?>
            <img src="<?= Kohana::config('mainconf.url.images') ?>/icon_complete.png" alt="Complete">
            <br>
            
            <div class="pull-left">
                <a title="View complete version" data-content="Show complete postedited version of document" class="btn btn-mini btn-margin popover_bottom" href="<?= Url::base(TRUE) ?>postedit/view_complete/<?= $doc_upload_data['doc_id'] ?>"><i class="icon-eye-open"></i> view</a>&nbsp;
            </div>

            <div class="btn-group pull-left">
                <a class="btn btn-mini dropdown-toggle btn-margin" data-toggle="dropdown" href="#"><i class="icon-download-alt"></i> download <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= Url::base(TRUE) ?>postedit/download_doc/<?= $doc_upload_data['doc_id'] ?>/txt">as .TXT</a></li>
                    <li><a href="<?= Url::base(TRUE) ?>postedit/download_doc/<?= $doc_upload_data['doc_id'] ?>/docx">as .DOCX</a></li>
                </ul>
            </div>
             
        <? } else { ?>
            <img id="icon_completed_bot" src="<?= Kohana::config('mainconf.url.images') ?>/icon_incomplete.png" alt="Not completed">
        <? } ?>
        </div>
        </td>
    </tr>
    </tbody>
</table>

<form class="form-inline"> 
<? 
$most_recent_revision = $doc_revisions[0];  
if(count($doc_revisions) > 1) { ?>   
    <span class="help-inline">Toggle revisions:</span>&nbsp;&nbsp;
        <div class="btn-group">
          <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            <? echo date(Kohana::config('mainconf.date_format'), $doc_upload_data['date_uploaded']);
            if($doc_upload_data['doc_id'] == $most_recent_revision['doc_id']) { echo ' (most recent)'; } ?>
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
              <? 
              for($i = 0; $i < count($doc_revisions); $i++) {
                  $doc_revision = $doc_revisions[$i]; 
                  
                  if($i == 0) {
                      if($doc_upload_data['doc_id'] != $most_recent_revision['doc_id']) { ?> 
                          <li><a href="<?= Url::base(TRUE).'postedit/view_complete/'.$doc_revision['doc_id'] ?>"><?= date(Kohana::config('mainconf.date_format'), $doc_revision['date_uploaded']) ?> (most recent)</a></li>
                          <li class="divider"></li>   
                   <? }
                  } else { 
                      if($doc_revision['doc_id'] != $doc_upload_data['doc_id']) { ?>
                          <li><a href="<?= Url::base(TRUE).'postedit/view_complete/'.$doc_revision['doc_id'] ?>"><?= date(Kohana::config('mainconf.date_format'), $doc_revision['date_uploaded']) ?></a></li>
                      <? } ?>
              <?  }
              } ?>
          </ul>
        </div> &nbsp; 
<? } 

if($doc_upload_data['doc_id'] == $most_recent_revision['doc_id'] && $doc_upload_data['completed'] == 1) { ?>
    <a href="<?= Url::base(TRUE) ?>upload/upload_revision/<?= $doc_upload_data['doc_id'] ?>" class="btn"><i class="icon-file"></i> Upload new revision</a>
<? } ?>
</form>