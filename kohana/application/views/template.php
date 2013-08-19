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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?= $page_title ?> - <?= Kohana::config('mainconf.app_name') ?></title>
    <meta http-equiv="Content-Type" content="text/html; utf-8">
    <meta charset="utf-8">
    
    <script src="<?= Kohana::config('mainconf.url.jquery') ?>/js/jquery-1.8.2.js"></script>
    <script src="<?= Kohana::config('mainconf.url.jquery') ?>/js/jquery-ui-1.9.0.custom.js"></script>
    <script src="<?= Kohana::config('mainconf.url.bootstrap') ?>/js/bootstrap.min.js"></script>
    <script src="<?= Kohana::config('mainconf.url.base') ?>/extra.js"></script>
    
    <link href="<?= Kohana::config('mainconf.url.bootstrap') ?>/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="<?= Kohana::config('mainconf.url.jquery') ?>/css/smoothness/jquery-ui-1.9.0.custom.css" rel="stylesheet">
    <link href="<?= Kohana::config('mainconf.url.base') ?>/extra.css" rel="stylesheet">
</head>
<body>

<script type="text/javascript">
$(document).ready(function(){
    $(".popover_bottom").popover({
        placement: 'bottom',
        trigger: 'hover'
    }); 
    $(".popover_right").popover({
        placement: 'right',
        trigger: 'hover'
    });
});
</script>

<? if(Auth::instance()->logged_in()) { ?>

<div class="navbar">
  <div class="navbar-inner">
    <ul class="nav">
      <li<? if($nav_active == "upload") { echo ' class="active"'; } ?>><a href="<?= Url::base(TRUE) ?>upload">Upload document</a></li>
      <li<? if($nav_active == "all_documents") { echo ' class="active"'; } ?>><a href="<?= Url::base(TRUE) ?>browse">View all documents</a></li>
      <li<? if($nav_active == "account") { echo ' class="active"'; } ?>><a href="<?= Url::base(TRUE) ?>account">My account</a></li>
      <li<? if($nav_active == "claimed_documents") { echo ' class="active"'; } ?>><a href="<?= Url::base(TRUE) ?>account/claimed_documents">My claimed documents</a></li>
      <? if(Controller_Admin::user_is_admin()) { ?>
        <li<? if($nav_active == "admin") { echo ' class="active"'; } ?>><a href="<?= Url::base(TRUE) ?>admin">Admin panel</a></li>
      <? } ?>
      <? $agency_id = Model_Agency::get_admin_agency_id(Auth::instance()->get_user());
         if($agency_id != 0) { ?>
          <li<? if($nav_active == "agency") { echo ' class="active"'; } ?>><a href="<?= Url::base(TRUE) ?>agency">Agency admin panel</a></li>
      <? } ?>
      <li><a href="<?= Url::base(TRUE) ?>account/signout">Logout</a></li>
    </ul>
    <div class="pull-right">
        <a href="<?= Url::base(TRUE) ?>help" class="btn btn-small btn-primary"><i class="icon-question-sign icon-white"></i> Help</a>
    </div>
  </div>
  
</div>

<? } ?>

<?= $page_content ?>

<div class="copyright_footer">PHAST<sup>TM</sup> &copy; <?= date("Y") ?></div>

</body>
</html>