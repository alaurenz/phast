<? if(!$hide_filter_controls) { ?>

<script type="text/javascript">
jQuery(document).ready(function() {
    // prepare Typehead for doc topics
    var all_topics = [<? foreach($topics as $topic) { echo "'".$topic['topic']."',"; } ?>];
    $('#search_topic').typeahead({source: all_topics, items:6});
    
    // search bar behavior...
    $('li.search_by_dropdown').click(function() {
        // disable AND hide both fields -> test if this works with validation, etc
        $('#search_keywords').hide(); $('#search_keywords').prop('disabled', true);
        $('#search_topic').hide(); $('#search_topic').prop('disabled', true);
        // show AND enable the field(s) corresponding to selection        
        if($(this).text() == "by title" || $(this).text() == "by title and topic") {
            $('#search_keywords').show(); $('#search_keywords').prop('disabled', false);
        }
        if($(this).text() == "by topic" || $(this).text() == "by title and topic") {
            $('#search_topic').show(); $('#search_topic').prop('disabled', false);
        }       

        $('li.search_by_dropdown').show();
        $('#search_by_btn').html('Search ' + $(this).text() + ' <span class="caret"></span>');
        $(this).hide();
    });
});
</script>  

<div class="row-fluid">
  <div class="span4">

<?  $language_list_html = "";
    $language_id_name = array();
    foreach($languages as $language) { 
        $language_id_name[$language['lang_id']] = $language['lang_name']; 
        if($language_id != $language['lang_id'])    
            $language_list_html .= '<li><a href="'.Url::base(TRUE).'browse/index/'.$language['lang_id'].'">'.$language['lang_name'].'</a></li>';
    } ?>
    <div class="btn-group">
      <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
        <?= ($language_id > 0) ? $language_id_name[$language_id] : 'All languages' ?>
        <span class="caret"></span>
      </a>
      <ul class="dropdown-menu">
          <? if($language_id > 0) { ?> 
          <li><a href="<?= Url::base(TRUE).'browse/index' ?>">All languages</a></li>
          <li class="divider"></li>   
          <? } ?>   
          <?= $language_list_html ?>
      </ul>
    </div>
    
  </div>
  <div class="span8">
    
    <form class="form-inline pull-right" action="<?= Url::base(TRUE).'browse/search' ?>" method="post">

        <div class="input-prepend input-append">
          <div class="btn-group">
            <button type="button" id="search_by_btn" class="btn dropdown-toggle" data-toggle="dropdown">
              Search by title
              <span class="caret"></span>
            </button>
            <ul class="search_by_dropdown dropdown-menu">
              <li class="search_by_dropdown" style="display:none"><a tabindex="-1" href="#">by title</a></li>
              <li class="search_by_dropdown"><a tabindex="-1" href="#">by topic</a></li>
              <li class="search_by_dropdown"><a tabindex="-1"  href="#">by title and topic</a></li>
            </ul>
          </div>
        
          <input name="search_keywords" id="search_keywords" type="text" class="input-medium search-query" placeholder="Enter keyword(s)...">              
                
          <input name="search_topic" id="search_topic" type="text" class="input-medium" placeholder="Enter topic..." data-provide="typeahead" autocomplete="off" style="display:none" disabled>
          
          <button name="search_go" id="search_go" type="submit" class="btn">Go</button>
        
        </div>  

    </form>
    
  </div>
  
</div>
<? 
    echo $pagination;
}

if(count($docs) == 0) { ?>
No documents to display.
<? } else { ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
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
<? $i = 1;
if(!$hide_filter_controls) {
    $i += $page_offset;
}
 
foreach($docs as $doc) { ?>
    <tr>
        <td><?= $i ?></td>
        
        <td><? if($doc['completed'] == 1) {
            echo '<a href="'.Url::base(TRUE)."postedit/view_complete/".$doc['doc_id'].'">'.$doc['doc_title'].'</a>';
        } else {
            echo '<a href="'.Url::base(TRUE)."postedit/view_edit/".$doc['doc_id'].'">'.$doc['doc_title'].'</a>';
        } ?></td>
        
        <td><?= $doc['doc_topic'] ?></td>
        
        <td><?= $doc['lang_name'] ?></td>
        
        <td><?= date(Kohana::config('mainconf.date_format'), $doc['date_uploaded']) ?>
        <? /*<img src="<?= Kohana::config('mainconf.url.images') ?>/icon_uploaded_24.png" alt="Uploaded"> */ ?></td>
        
        <td><? if($doc['claimed_user_id'] != 0) { ?> 
            <a href="<?= Url::base(TRUE).'account/profile/'.$doc['claimed_user_id'] ?>"><?= $doc['username']?></a>
            <? if($hide_filter_controls) { ?>
                <a class="btn btn-inverse btn-mini btn-margin" href="<?= Url::base(TRUE).'postedit/unclaim/'.$doc['doc_id'].'/redirect' ?>"><i class="icon-remove-sign icon-white"></i> unclaim</a>
            <? } ?>
        <? } else { ?>
             <img src="<?= Kohana::config('mainconf.url.images') ?>/icon_available_mini.png" alt="Not claimed">
        <? } ?></td>
        
        <td><? if($doc['total_sentences'] != 0) {
        			$percent_complete = round(($doc['sentences_completed'] / $doc['total_sentences']), 2) * 100;
        		} else {
        			$percent_complete = 100;
        		} ?>
            <div style="width:100px; height:24px;"><div class="progress">
                <div class="bar" style="width: <?= $percent_complete ?>%;"><?= $percent_complete ?>&#037;</div>
            </div></div>
        </td>
        
        <td>&nbsp;<?= ($doc['completed'] == 1) ? '<img src="'.Kohana::config('mainconf.url.images').'/icon_complete.png" alt="Complete">' : '<img src="'.Kohana::config('mainconf.url.images').'/icon_incomplete.png" alt="Not completed">'; ?></td>
    </tr>
<? $i++;
} ?>
    </tbody>
</table>

<? 
if(!$hide_filter_controls)
    echo $pagination;

} ?>