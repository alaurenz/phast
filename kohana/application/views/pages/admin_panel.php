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
<a href="<?= Url::base(TRUE) ?>admin/create_agency" class="btn">Create Agency</a>
<br><br>

<a href="<?= Url::base(TRUE) ?>admin/tou_pp" class="btn">Terms of Use and Privacy Policy</a>
<br><br>

<a href="<?= Url::base(TRUE) ?>admin/translation_models" class="btn">Translation Models</a>
<br><br>

<div class="btn-group">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Dump translation data
    <span class="caret"></span>
  </a>
  
  <ul class="dropdown-menu"> 
      <? foreach($languages as $language) {
        echo '<li><a href="'.Url::base(TRUE).'admin/dump/'.$language['lang_id'].'">'.$language['lang_name'].'</a></li>';
      } ?>
  </ul>
</div>
<br>
<strong>NOTE:</strong> This dumps post-edited text from completed documents only. The original MT data used during the post-editing was from whatever language model was set as the default at the time.
