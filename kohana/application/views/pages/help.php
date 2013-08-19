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
    $('#help_tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	<? if($selected_tab) { ?>
		$('#help_tabs a[href="#<?= $selected_tab ?>"]').tab('show');
	<? } ?>
});
</script>

<h1>Help <small>How-to&apos;s</small></h1>
<br>
<ul class="nav nav-tabs" id="help_tabs">
  <li class="active"><a href="#basic_usage" data-toggle="tab">Basic usage</a></li>
  <li><a href="#document_revisions" data-toggle="tab">Document revisions</a></li>
  <li><a href="#agency_admin" data-toggle="tab">Agency admin</a></li>
  <li><a href="#other" data-toggle="tab">Other</a></li>
</ul>
 
<div class="tab-content">
  <div class="tab-pane active" id="basic_usage">
  	<p>
	<h3>Upload a document</h3>
	Click &apos;Upload document&apos; on the navigation bar at the top. Fill in the required information and select the file containing the text to be translated. Upon completion the document will be machine translated and added to the system.
	</p>
	<hr>
	<p>
	<h3>Claim a document</h3>
	Click &apos;View all documents&apos; to view all documents that have been uploaded to the system. Click on the document&apos;s title to view it. Claim the document by clicking <button class="btn btn-mini btn-margin" type="button"><i class="icon-plus-sign"></i> claim</button> which sets you as the posteditor and prevents others from making postedits to the document. You can unclaim a document at any time by clicking <button class="btn btn-inverse btn-mini btn-margin" type="button"><i class="icon-remove-sign icon-white"></i> unclaim</button>. This will allow another user to claim the document to complete the postediting. To see all documents you have claimed click &apos;My claimed documents&apos;.
	</p>
	<hr>
	<p>
	<h3>Postedit a document</h3>
	After you have claimed a document you can postedit it. You can make changes to one machine-translated sentence/paragraph at a time. Once you are finished making changes to a sentence/paragraph click <button class="save_button btn" type="button"><i class="icon-hdd"></i> Save</button> or press the TAB key to save your changes and move on to the next sentence/paragraph. You may go back to a sentence/paragraph and change it later by clicking the source text for that sentence/paragraph. You do not need to postedit all sentences/paragraphs in one session, you can logout and finish postediting later.
	</p>
	<hr>
	<p>
	<h3>Mark a document as completed</h3>
	After you have postedited all of the sentences/paragraphs of a document the <button class="btn btn-success"><i class="icon-ok icon-white"></i> Mark document as completed</button> button will appear. To finalize the postedited translation you must click this button which will mark the document as completed and allow any user to download the completed translation. You can also click <button class="btn btn-warning" type="button"><i class="icon-remove icon-white"></i> Unmark document as completed</button> at any time which revert it to uncompleted and prevent it from being downloaded but otherwise the document will remain the unchanged.
	</p>
	<hr>
	<p>
	<h3>Download a completed document</h3>
	After all sentences/paragraphs have been saved (indicated by green) the document will be marked as completed and <button class="btn btn-mini dropdown-toggle btn-margin" data-toggle="dropdown" href="#"><i class="icon-download-alt"></i> download <span class="caret"></span></button> will appear which allows you or anyone else with access to the system to download the postedited document in a variety of formats.
	</p>
  </div>
  
  <div class="tab-pane" id="document_revisions">
  	<p>The document revision system should be used when changes have been made to the source document and you wish to translate the modified document without having to retranslate the entire revised document. The system will automatically detect which sentences/paragraphs were changed in the revised version and pull in those previously translated/postedited sentences/paragraphs that were unchanged so that postediting only needs to be performed on the sentences/paragraphs that were modified in the revised version.</p>
  	<hr>
	<p>
	<h3>Uploading a document revision</h3>
	...
	</p>
  	<hr>
	<p>
	<h3>Viewing previous revisions</h3>
	...
	</p>
  </div>

  <div class="tab-pane" id="agency_admin">
  	<p>
	This section is only relevant to agency administrators. Each registered agency has one person who is designated as the agency administrator. All user accounts must be created by the agency administrator and then activated by the specific user.
	</p>
	<hr>
	<p>
	<h3>Create a new user</h3>
	Click on the &quot;Agency admin panel&quot; link on the navigation bar (if you do not see this link then you are not an agency administrator). At the top you will see a text field with &quot;Enter new user email...&quot; inside. Enter the email address of the person you wish to create an account for and then click <button class="btn">Create New User</button>. This will send an email to the address you entered with instructions the new user should follow to activate their account. <em>The new user MUST activate their account within <?= Kohana::config('mainconf.time_until_activation_expires_days') ?> days or their account will expire.</em> If an account expires you can resend the activation email (see section on &quot;Resending activation emails&quot; below).
	</p>
	<hr>
	<p>
	<h3>Account expiration / Resending activation emails</h3>
	All accounts created within the &quot;Agency admin panel&quot; will expire if they are not activated within <?= Kohana::config('mainconf.time_until_activation_expires_days') ?> days. Any expired account will be noted in the status column in the list of all accounts for your agency (in the &quot;Agency admin panel&quot;). You can resend the activation email for a given account by clicking <button class="btn btn-small"><i class="icon-envelope"></i> Resend Activation Email</button> (NOTE: activation emails can be resent for accounts that are pending activation or expired). Each time an activation email is sent (the first is sent upon creating the account in the &quot;Agency admin panel&quot;) a new temporary password is generated and sent to the email address associated with the account along with instructions on how to activate the account. Previously generated temporary passwords will not work for activation, only the most recently generated temporary password will work.
	</p>
  </div>
  
  <div class="tab-pane" id="other">
  	<p>
	<h3>Edit document metadata (title, topic, etc.)</h3>
	...
	</p>
  </div>
</div>
