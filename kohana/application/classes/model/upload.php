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

class Model_Upload extends Model {
    
    public function get_translation_model_data($model_id)
    {
        $rows = DB::select()->from('translator_hub_models')
                ->where('model_id','=',$model_id)
                ->limit(1)->execute()->as_array();
        return array_pop($rows);
    } 
    
    public function get_most_recent_revision($doc_id)
    {
        // check if this document is the original & no revisions exist
        $check_orig_rows = DB::select('doc_id','orig_doc_id','completed')->from('doc_uploads')->where('doc_id','=',$doc_id)->execute()->as_array();
        $check_orig = array_pop($check_orig_rows);
        $num_revisions = count(DB::select('orig_doc_id')->from('doc_uploads')->where('orig_doc_id','=',$doc_id)->execute());
        if($check_orig['orig_doc_id'] == 0) {
            $orig_doc_id = $doc_id;
            if($num_revisions == 0)
                return $check_orig;
        } else {
            $orig_doc_id = $check_orig['orig_doc_id'];  
        }
        
        // otherwise find and return most recent revision
        $rows = DB::select('doc_id','orig_doc_id','completed')->from('doc_uploads')
                ->where('orig_doc_id','=',$orig_doc_id)
                ->order_by('date_uploaded', 'DESC')
                ->limit(1)->execute()->as_array();
        return array_pop($rows);
    }    
    
    public function get_target_language_code($lang_id)
    {
        $row = DB::select('lang_code')->from('target_languages')->where('lang_id','=',$lang_id)->execute()->as_array();
        $column = array_pop($row);
        return $column['lang_code'];
    }
    
    public function insert_upload($data)
    {
        list($insert_id) = DB::insert('doc_uploads', array_keys($data))->values(array_values($data))->execute();
        return $insert_id;
    } 
    
    // Inserts new translation (for revised documents OR new original documents)
    // When a translation revision is being inserted $old_postedited_sentences are passed in
    public function insert_translation($doc_id, $source_sentences, $translated_sentences, $old_postedited_sentences = "")
    {
        assert(count($source_sentences) == count($translated_sentences));
        
        for($i = 0; $i < count($source_sentences); $i++) {
            $source_sentence_data = array(
                "doc_id" => $doc_id,
                "source_text" => $source_sentences[$i]
            );
            list($source_sentence_id) = DB::insert('source_sentences', array_keys($source_sentence_data))->values(array_values($source_sentence_data))->execute();
            
            $target_sentence_data = array(
                "sentence_id" => $source_sentence_id,
                "doc_id" => $doc_id,
                "mt_text" => $translated_sentences[$i]
            );
            if($old_postedited_sentences)
                $target_sentence_data['postedit_text'] = $old_postedited_sentences[$i];
                
            DB::insert('translated_sentences', array_keys($target_sentence_data))->values(array_values($target_sentence_data))->execute();
        }
    }
    
    // Inserts a generic translation for comparison to the "default" 
    // PRECONDITION: there must exist a set of sentences (corresponding to $translated_sentences_generic) 
    //               in the table 'translated_sentences' that have the same doc_id as given $doc_id
    public function insert_generic_translation($doc_id, $translated_sentences_generic)
    {
        // get all sentences for this document from 'translated_sentences' and ORDER BY sentence_id ASC
        $translated_sentences_default = DB::select('sentence_id')->from('translated_sentences')
                    ->where('doc_id','=',$doc_id)
                    ->order_by('sentence_id', 'ASC')->execute()->as_array();        
        
        assert(count($translated_sentences_default) == count($translated_sentences_generic));
        
        for($i = 0; $i < count($translated_sentences_generic); $i++) {
            $target_sentence_data = array(
                "sentence_id" => $translated_sentences_default[$i]['sentence_id'],
                "doc_id" => $doc_id,
                "mt_text_generic" => $translated_sentences_generic[$i]
            );
            DB::insert('translated_sentences_generic', array_keys($target_sentence_data))->values(array_values($target_sentence_data))->execute();
        }
    }
    
    public function update_doc_metatdata($doc_id, $data)
    {
        DB::update('doc_uploads')->set($data)->where('doc_id','=',$doc_id)->execute();
    }
    
    public function delete_doc($doc_id) {
        DB::delete('doc_uploads')->where('doc_id','=',$doc_id)->execute();
        // TODO: delete associated translations (if they exist) 
    }
}