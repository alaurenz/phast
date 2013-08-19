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
defined('SYSPATH') OR die('No direct access allowed.'); 
 
return array 
( 
    'doc_title' => array(
        'not_empty' => 'You must enter a title',
        'max_length' => 'Title is too long. Must be 180 characters or less',
        'default'  => 'Title is invalid',
    ),
    'doc_topic' => array(
        'not_empty' => 'You must choose a topic',
        'max_length' => 'Topic is too long. Must be 100 characters or less',
        'default'  => 'Topic is invalid',
    ),
    'doc_topic_other' => array(
        'not_empty' => 'You must enter a topic',
        'max_length' => 'Topic is too long. Must be 100 characters or less',
        'default'  => 'Topic is invalid',
    ),
    'doc_upload' => array(
        'Upload::not_empty' => 'You must select a file',
        'Upload::type' => 'Invalid file type. Must be .docx or .txt',
        'Upload::size' => 'File is too large (5 MB maximum file size)',
        'default'  => 'Document upload is invalid',
    ),
    'languages' => array(
        'not_empty' => 'You must select at least one language',
        'default'  => 'Language(s) selected are invalid',
    ),
);