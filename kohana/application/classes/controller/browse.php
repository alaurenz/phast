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

class Controller_Browse extends Controller {
    
    public function before() {
        parent::before();
        if(Auth::instance()->logged_in() == 0) {
            Request::instance()->redirect('account/signin');
        }
        $this->model_browse = new Model_Browse;
        $this->active_user_id = Auth::instance()->get_user();
        
        $this->docs_per_page = 20;
    }
    
    public function action_index($language_id = "")
    {
        $view = View::factory('template');
        $view->nav_active = "all_documents";
        $view->page_title = "All uploaded documents";
        $view->page_content = View::factory('helpers/doc_list');
        $view->page_content->hide_filter_controls = FALSE;
         
        $pagination = Pagination::factory(array(
  		   'total_items'    => $this->model_browse->get_num_documents($language_id),
  		   'items_per_page' => $this->docs_per_page,
  		   'view'           => 'pagination/bootstrap'
  	    ));
  		$doc_results = $this->model_browse->get_documents($language_id, $pagination->items_per_page, $pagination->offset);
        
        $model_misc = new Model_Misc; 
        
        $view->page_content->title = "All uploaded documents";
        $view->page_content->docs = $doc_results;
        $view->page_content->language_id = $language_id;
        $view->page_content->languages = $model_misc->get_languages();
        $view->page_content->topics = $model_misc->get_topics();
        $view->page_content->pagination = $pagination->render();
        $view->page_content->page_offset = $pagination->offset;
        $this->request->response = $view;
    }
    
    public function action_search()
    {
        if($_POST) {
            $post = Validate::factory($_POST)
                        ->filter(TRUE, 'trim')
                        ->rule('search_keywords', 'alpha_numeric');
            
            if ($post->check()) {
                $field_data = $post->as_array();
                $keywords = (array_key_exists('search_keywords', $field_data)) ? trim($field_data['search_keywords']) : '';
                $topic = (array_key_exists('search_topic', $field_data)) ? trim($field_data['search_topic']) : '';
                if(!$keywords && !$topic)
                    Request::instance()->redirect('browse/index');            
                
                $doc_results = $this->model_browse->search_docs($keywords, $topic);
            } else {
                $doc_results = array();
            }
            
            $model_misc = new Model_Misc; 
            
            $view = View::factory('template');
            $view->nav_active = "all_documents";
            $view->page_title = "Search results";
            $view->page_content = View::factory('helpers/doc_list');
            $view->page_content->hide_filter_controls = FALSE;            
            
            $view->page_content->title = "Search results";
            $view->page_content->docs = $doc_results;
            $view->page_content->language_id = 0; //$language_id;
            $view->page_content->languages = $model_misc->get_languages();
            $view->page_content->topics = $model_misc->get_topics();
            $view->page_content->pagination = "";
            $view->page_content->page_offset = 0;
            $this->request->response = $view;
        
        } else {
            Request::instance()->redirect('browse/index');
        }
    }
}