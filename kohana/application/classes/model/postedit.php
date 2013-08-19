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

class Model_Postedit extends Model {
    
    // Returns the set of document revisions (including original) corresponding to given doc_id of original
    // Given doc_id must be original!
    public function get_document_revisions($orig_doc_id) 
    {
        $check_if_orig = $this->get_doc_upload_data($orig_doc_id);
        assert($check_if_orig['orig_doc_id'] == 0);
        
        return DB::select('doc_id','orig_doc_id','completed','date_uploaded')->from('doc_uploads')
                ->where('orig_doc_id','=',$orig_doc_id)->or_where('doc_id', '=', $orig_doc_id)
                ->order_by('date_uploaded', 'DESC')
                ->execute()->as_array();
    }
    
    public function get_doc_upload_data($doc_id)
    {
        $translation_data_rows = DB::select('doc_uploads.*','users.username','target_languages.lang_name')->from('doc_uploads')
            ->join('target_languages')->on('target_languages.lang_id','=','doc_uploads.doc_language')
            ->join('users', 'LEFT OUTER')->on('users.id','=','doc_uploads.claimed_user_id')
            ->where('doc_id','=',$doc_id)->limit(1)->execute()->as_array();
        return array_pop($translation_data_rows);
    }
    
    public function set_doc_claimed($doc_id, $user_id)
    {
        $doc_data = array(
            'claimed_user_id' => $user_id,
            'date_claimed' => time()
        );
        DB::update('doc_uploads')->set($doc_data)->where('doc_id','=',$doc_id)->execute();
    }
    
    public function set_doc_unclaimed($doc_id)
    {
        $doc_data = array('claimed_user_id' => 0);
        DB::update('doc_uploads')->set($doc_data)->where('doc_id','=',$doc_id)->execute();
    }
    
    public function save_sentence($sentence_id, $text, $doc_upload_data) 
    {
        // If sentence is being edited for the first time add 1 to count of completed sentences
        $sentence_r = DB::select('postedit_text')->from('translated_sentences')->where('sentence_id', '=', $sentence_id)->execute()->as_array();
        if($sentence_r[0]['postedit_text'] == "") {
            $num_completed_sentences = $doc_upload_data['sentences_completed'] + 1;
            $data = array('sentences_completed' => $num_completed_sentences);
            DB::update('doc_uploads')->set($data)->where('doc_id','=',$doc_upload_data['doc_id'])->execute();
        }
        
        $data = array('postedit_text' => $text);
        DB::update('translated_sentences')->set($data)->where('sentence_id','=',$sentence_id)->execute();
    }
    
    // Return true if all sentences have been post-edited, false otherwise
    public function check_doc_complete($doc_id) 
    {
        $all_sentences = DB::select()->from('translated_sentences')
            ->where('doc_id', '=', $doc_id)->where('postedit_text', '=', '')
            ->execute()->as_array();
        $all_sentences_completed = 1;
        foreach($all_sentences as $sent) {
            if($sent == "") {
                $all_sentences_completed = 0;
                break;
            }
        }

        if($all_sentences_completed) {
            return TRUE;
        } else {
            return FALSE;
        } 
    }
    
    // If $completed == 0, mark given doc as not complete
    // If $completed == 1, mark doc as completed iff all sentences have been postedited, otherwise do nothing
    public function update_completed_status($doc_id, $completed) 
    {
        if($completed) {
            if($this->check_doc_complete($doc_id)) {
                $doc_data = array(
                    'completed' => 1,
                    'date_completed' => time()
                );
                DB::update('doc_uploads')->set($doc_data)->where('doc_id','=',$doc_id)->execute();
            }
        } else {
            $doc_data = array(
                'completed' => $completed,
                'date_completed' => 0
            );
            DB::update('doc_uploads')->set($doc_data)->where('doc_id','=',$doc_id)->execute();
        }
        
    }
    
    public function mark_postedited($doc_id) {
        $doc_data = array(
            'postedited' => 1,
            'date_postedited' => time()
        );
        DB::update('doc_uploads')->set($doc_data)->where('doc_id','=',$doc_id)->execute();
    }
    
    // Returns all sentence pairs of given doc where each sentence has 
    // {''source_text', 'text' (postedit/mt text), and 'edited'}
    // IMPORTANT: in ascending order by sentence_id (required so that they align with generic MT sentences)    
    public function get_sentence_pairs($doc_id)
    {
        $sentences = DB::select()->from('source_sentences')->where('doc_id','=',$doc_id)->order_by('sentence_id', 'ASC')->execute()->as_array();
        $translated_sentences = DB::select()->from('translated_sentences')->where('doc_id','=',$doc_id)->order_by('sentence_id', 'ASC')->execute()->as_array();
        $num_completed_sentences = 0;        
        for($i = 0; $i < count($sentences); $i++) {
            if($translated_sentences[$i]['postedit_text'] != "") {
                $sentences[$i]['text'] = $translated_sentences[$i]['postedit_text'];
                $sentences[$i]['edited'] = TRUE;
                $num_completed_sentences++;
            } else {
                $sentences[$i]['text'] = $translated_sentences[$i]['mt_text'];
                $sentences[$i]['edited'] = FALSE;
            } 
        }
        
        $doc_upload_data = $this->get_doc_upload_data($doc_id);
        assert($doc_upload_data['total_sentences'] == count($sentences));
        assert($doc_upload_data['sentences_completed'] == $num_completed_sentences);        
        
        return $sentences;
    }

    // IMPORTANT: in ascending order by sentence_id (required so that they align with generic MT sentences)
    public function get_sentence_pairs_mt($doc_id)
    {
        $sentences = DB::select()->from('source_sentences')->where('doc_id','=',$doc_id)->order_by('sentence_id', 'ASC')->execute()->as_array();
        $translated_sentences = DB::select()->from('translated_sentences')->where('doc_id','=',$doc_id)->order_by('sentence_id', 'ASC')->execute()->as_array();
        for($i = 0; $i < count($sentences); $i++) {
            $sentences[$i]['text'] = $translated_sentences[$i]['mt_text'];
        }
        return $sentences;
    }

    // IMPORTANT: in ascending order by sentence_id (required so that they align 
    // with MT sentences translated with non-generic model)
    public function get_mt_sentences_generic($doc_id)
    {
        return DB::select()->from('translated_sentences_generic')->where('doc_id','=',$doc_id)->order_by('sentence_id', 'ASC')->execute()->as_array();;
    }

    /*
     * Discussion/comment system
     */
    public function insert_comment($data)
    {
        DB::insert('discussions', array_keys($data))->values(array_values($data))->execute();
    }

    public function get_comments($doc_id)
    {
        return DB::select('discussions.*', 'users.username')->from('discussions')
                    ->join('users')->on('users.id','=','discussions.user_id')
                    ->where('discussions.doc_id','=',$doc_id)->order_by('discussions.comment_id', 'ASC')->execute()->as_array();
    }
}