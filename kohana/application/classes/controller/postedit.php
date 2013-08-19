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
defined('SYSPATH') or die('No direct script access.');

class Controller_Postedit extends Controller {
    
    public function before() {
        parent::before();
        if(Auth::instance()->logged_in() == 0) {
            Request::instance()->redirect('account/signin');
        }
        $this->model_postedit = new Model_Postedit;
        $this->active_user_id = Auth::instance()->get_user();
    }
    
    public function action_index()
    {
        Request::instance()->redirect('browse');
    }
    
    public function action_view_edit($doc_id)
    {
        $view = View::factory('template');
        $view->nav_active = "";
        
        $doc_upload_data = $this->model_postedit->get_doc_upload_data($doc_id);
        $sentence_pairs = $this->model_postedit->get_sentence_pairs($doc_id);
        if($doc_upload_data['translation_model_id'] == 0) {
            // generic model is default translation model 
            $mt_sentences_generic = "";
        } else {
            $mt_sentences_generic = $this->model_postedit->get_mt_sentences_generic($doc_id);
        }        
        
        if($doc_upload_data['claimed_user_id'] == $this->active_user_id) {
            $view->page_content = View::factory('pages/edit');
            $view->page_title = $doc_upload_data['doc_title']." (Postediting)";
            $view->page_content->doc_version = '<i class="icon-edit"></i> Postedited';
        } else {
            $view->page_content = View::factory('pages/view_static');
            
            if($doc_upload_data['postedited'] == 1) {
                $view->page_title = $doc_upload_data['doc_title']." (Postedited)";
                $view->page_content->doc_version = '<i class="icon-edit"></i> Postedited';
            } else {
                $view->page_title = $doc_upload_data['doc_title']." (Uploaded)";
                $view->page_content->doc_version = '<i class="icon-file"></i> Uploaded (machine-translated)';
            }
        }
            $view->page_content->doc_upload_data = $doc_upload_data;
        
        $view->page_content->doc_header = View::factory('helpers/doc_header');
        $view->page_content->doc_header->doc_upload_data = $doc_upload_data;
        // get document revisions (including the original)
        $orig_doc_id = ($doc_upload_data['orig_doc_id'] == 0) ? $doc_id : $doc_upload_data['orig_doc_id'];       
        $view->page_content->doc_header->doc_revisions = $this->model_postedit->get_document_revisions($orig_doc_id);
        
        $model_account = new Model_Account;
        
        $view->page_content->discussion = View::factory('helpers/discussion');
        $view->page_content->discussion->user_data = $model_account->get_user_data($this->active_user_id);        
        $view->page_content->discussion->doc_id = $doc_id;
        $view->page_content->discussion->comments = $this->model_postedit->get_comments($doc_id);
        
        $view->page_content->sentence_pairs = $sentence_pairs;
        $view->page_content->mt_sentences_generic = $mt_sentences_generic;
        $view->page_content->doc_upload_data = $doc_upload_data;
        $this->request->response = $view;
    }
    
    public function action_view_mt($doc_id)
    {
        $view = View::factory('template');
        $view->nav_active = "";
        
        $doc_upload_data = $this->model_postedit->get_doc_upload_data($doc_id);
        $sentence_pairs_mt = $this->model_postedit->get_sentence_pairs_mt($doc_id);
        
        $view->page_title = $doc_upload_data['doc_title']." (Uploaded)";
        $view->page_content = View::factory('pages/view_static');
        
        $view->page_content->doc_header = View::factory('helpers/doc_header');
        $view->page_content->doc_header->doc_upload_data = $doc_upload_data;
        // get document revisions (including the original)
        $orig_doc_id = ($doc_upload_data['orig_doc_id'] == 0) ? $doc_id : $doc_upload_data['orig_doc_id'];       
        $view->page_content->doc_header->doc_revisions = $this->model_postedit->get_document_revisions($orig_doc_id);
        
        $model_account = new Model_Account;
        
        $view->page_content->discussion = View::factory('helpers/discussion');
        $view->page_content->discussion->user_data = $model_account->get_user_data($this->active_user_id);        
        $view->page_content->discussion->doc_id = $doc_id;
        $view->page_content->discussion->comments = $this->model_postedit->get_comments($doc_id);        
        
        $view->page_content->doc_version = '<i class="icon-file"></i> Uploaded (machine-translated)';
        $view->page_content->sentence_pairs = $sentence_pairs_mt;
        $view->page_content->doc_upload_data = $doc_upload_data;
        $this->request->response = $view;
    }
    
    public function action_view_complete($doc_id)
    {
        $doc_upload_data = $this->model_postedit->get_doc_upload_data($doc_id);
        
        if($doc_upload_data['completed'] != 1)
            Request::instance()->redirect('postedit/view_edit/'.$doc_id);

        $view = View::factory('template');
        $view->nav_active = "";
        
        $sentence_pairs = $this->model_postedit->get_sentence_pairs($doc_id);
        
        $view->page_title = $doc_upload_data['doc_title']." (Completed)";
        $view->page_content = View::factory('pages/view_complete');
        
        $view->page_content->doc_header = View::factory('helpers/doc_header');
        $view->page_content->doc_header->doc_upload_data = $doc_upload_data;
        // get document revisions (including the original)
        $orig_doc_id = ($doc_upload_data['orig_doc_id'] == 0) ? $doc_id : $doc_upload_data['orig_doc_id'];       
        $view->page_content->doc_header->doc_revisions = $this->model_postedit->get_document_revisions($orig_doc_id);
        
        $view->page_content->doc_version = '<i class="icon-ok"></i> Completed';
        $view->page_content->sentence_pairs = $sentence_pairs;
        $view->page_content->doc_upload_data = $doc_upload_data;
        $this->request->response = $view;
    }
    
    public function action_download_doc($doc_id, $format)
    {
        $doc_upload_data = $this->model_postedit->get_doc_upload_data($doc_id);
        if($doc_upload_data['completed'] == 1) {
            $file_download = Kohana::config('mainconf.path.docs_download').'/'.uniqid('doc'.$doc_id.'_').'.'.$format;
            $sentence_pairs = $this->model_postedit->get_sentence_pairs($doc_id);
            
            if($format == 'txt') {     
                $fh_download_file = fopen($file_download, 'w') or die("$file_download: cannot open file for writing");
                foreach ($sentence_pairs as $sentence) {
                    fwrite($fh_download_file, $sentence['text']."\n");
                }
                fclose($fh_download_file);
                Request::instance()->redirect(Kohana::config('mainconf.url.base').'/download.php?file='.$file_download.'&name='.$doc_upload_data['doc_title'].'.txt&type=text/plain&delete_file=1');
            } else if($format == 'docx') {
                require_once Kohana::config('mainconf.path.phpdocx').'/classes/CreateDocx.inc';
                $docx = new CreateDocx();
                foreach ($sentence_pairs as $sentence) {
                    $docx->addText($sentence['text']);
                    $docx->addBreak('line');
                }
                $file_download_no_ext = substr($file_download, 0, -5);
                $docx->createDocx($file_download_no_ext);
                Request::instance()->redirect(Kohana::config('mainconf.url.base').'/download.php?file='.$file_download.'&name='.$doc_upload_data['doc_title'].'.docx&type=application/vnd.openxmlformats-officedocument.wordprocessingml.document&delete_file=1');
            }    
            /* else if($format == 'pdf') {
                require_once Kohana::config('mainconf.path.phpdocx').'/classes/TransformDoc.inc';
                $pdf = new TransformDoc();
                $pdf->setStrFile($file_download);
                $pdf->generatePDF();

                // *** remove .docx file
            } */
               
        }
    }
    
    public function action_claim($doc_id)
    {
        $doc_upload_data = $this->model_postedit->get_doc_upload_data($doc_id);
        if($doc_upload_data['claimed_user_id'] == 0) {
            $this->model_postedit->set_doc_claimed($doc_id, $this->active_user_id);
        }
        Request::instance()->redirect("postedit/view_edit/$doc_id");
    }
    
    public function action_unclaim($doc_id, $redirect = FALSE)
    {
        $doc_upload_data = $this->model_postedit->get_doc_upload_data($doc_id);
        if($doc_upload_data['claimed_user_id'] == $this->active_user_id) {
            $this->model_postedit->set_doc_unclaimed($doc_id);
        }
        if($redirect) {
            Request::instance()->redirect("account/claimed_documents");
        } else {
            Request::instance()->redirect("postedit/view_edit/$doc_id");
        }
    }
    
    // called by AJAX to save a postedited sentence
    public function action_save()
    {
        if($_POST) {
            $post = Validate::factory($_POST)->as_array();
            $doc_upload_data = $this->model_postedit->get_doc_upload_data($post['doc_id']);
            
            if(count($doc_upload_data) > 0) {
                if($doc_upload_data['claimed_user_id'] == $this->active_user_id AND $post['sentence_id'] > 0) {
                    // NOTE: hack to ensure no saved postedited sentence stores the empty string 
                    $postedited_text = $post['text'];
                    if($postedited_text == "") {
                        $postedited_text = " ";
                    }
                    $this->model_postedit->save_sentence($post['sentence_id'], $postedited_text, $doc_upload_data);
                    
                    if($doc_upload_data['postedited'] == 0) {
                        $this->model_postedit->mark_postedited($post['doc_id']);
                    }
                }
            }
        }
    }
    
    // set document as completed or not completed (pass 1 to set as complete or 0 to unmark as complete)
    public function action_update_completed_status($doc_id, $completed)
    {
         $completed = ($completed) ? 1 : 0;
         $this->model_postedit->update_completed_status($doc_id, $completed);
         Request::instance()->redirect("postedit/view_edit/$doc_id");
    }
    
    // add comment to discussion for $doc_id given in POST request (method called by AJAX)
    public function action_add_comment()
    {
        if($_POST) {
            $post = Validate::factory($_POST)->as_array();
            if($post['comment_text']) {
                $doc_upload_data = $this->model_postedit->get_doc_upload_data($post['doc_id']);
                
                if(count($doc_upload_data) > 0) {
                    $data = array(
                        'doc_id' => $post['doc_id'],
                        'user_id' => $this->active_user_id,
                        'comment_text' => $post['comment_text']
                    );
                    $this->model_postedit->insert_comment($data);
                }
            }
        }
    }
}