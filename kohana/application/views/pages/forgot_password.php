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
<p><a class="btn btn-small btn-primary btn-margin" href="<?= Url::base(TRUE) ?>"><i class="icon-arrow-left icon-white"></i> Back</a></p>

<? if($reset_email_sent) { ?> 
<div class="alert alert-success">
  A link to reset your password has been emailed to <strong><?= $reset_email_sent ?></strong>. Be sure to check your spam folder for the email.
  <a class="close" data-dismiss="alert" href="#">&times;</a>
</div>
<? } 

if($errors) { ?> 
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
    <p><? foreach($errors as $error) { ?>
          <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?= $error ?><br>
    <? } ?></p>
    </div><br>
<? } ?>

<?php echo form::open('account/forgot_password') ?>
  <table>
    <tr>
      <td><?php echo form::label('email','Email') ?></td>
      <td>&nbsp;</td>
      <td><?php echo form::input('email','',array('id' => 'email')) ?></td>
    </tr>
  </table>
  <input type="submit" value="Reset password" class="btn">
<?php echo form::close() ?>