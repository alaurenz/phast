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

<? if($errors) { ?> 
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p>
    <? foreach($errors as $error) { ?>
          <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $error ?><br>
    <? } ?></p>
    </div><br>
<? } ?>

<form action="<?= Url::base(TRUE) ?>account/signin" method="post" accept-charset="utf-8" class="form-inline">
  <input name="username" id="username" type="text" class="input-small" placeholder="Username">
  <input name="password" id="password" type="password" class="input-small" placeholder="Password">
  <? /*<label class="checkbox">
    <input type="checkbox"> Remember me
  </label>*/ ?>
  <button type="submit" class="btn">Login</button> 
   &nbsp; <small><a href="<?= Url::base(TRUE) ?>account/forgot_password">Forgot your password?</a></small>
</form>

<div class="hero-unit">

  <div class="row-fluid">
      <div class="span3">
          <img src="<?= Kohana::config('mainconf.url.images') ?>/phast_logo_transparent.png" border="0" alt="PHAST">      
      </div>
      <div class="span9">
          <span style="font-size:48px;"><strong>Public Health Automatic System for Translation (PHAST)<sup>TM</sup></strong></span>
          <p>Collaborative translation management system to enable public health workers to use machine translation for creating multilingual health materials</p>
      </div>
  </div>
  
</div>
