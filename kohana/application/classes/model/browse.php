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

class Model_Browse extends Model {
    
    public function search_docs($seach_query, $topic) {
        if($seach_query) {
            $sql = "SELECT d.*, l.lang_name, u.username, 
                MATCH (d.doc_title) AGAINST ('".$seach_query."') AS score
                FROM doc_uploads d 
                JOIN target_languages l ON (l.lang_id = d.doc_language)
                LEFT OUTER JOIN users u ON (u.id = d.claimed_user_id)
                WHERE MATCH (d.doc_title) AGAINST ('".$seach_query."')";

            if($topic)
               $sql .= " AND d.doc_topic = '".$topic."'";
        } else {
            $sql = "SELECT d.*, l.lang_name, u.username
                FROM doc_uploads d 
                JOIN target_languages l ON (l.lang_id = d.doc_language)
                LEFT OUTER JOIN users u ON (u.id = d.claimed_user_id)";

            if($topic)
               $sql .= " WHERE d.doc_topic = '".$topic."'";
        }
        return DB::Query(Database::SELECT, $sql)->execute()->as_array();
    }
    
    public function get_documents($language_id = "", $items_per_pg = "", $offset = "")
    {
        $query = DB::select('doc_uploads.*','target_languages.lang_name','users.username')->from('doc_uploads')
                           ->join('target_languages')->on('target_languages.lang_id','=','doc_uploads.doc_language')
                           ->join('users', 'LEFT OUTER')->on('users.id','=','doc_uploads.claimed_user_id');
        
        if($language_id)
            $query->where('doc_uploads.doc_language','=',$language_id);
        if($items_per_pg)
            $query->limit($items_per_pg);
        if($offset)
  			$query->offset($offset);
        
        $query->order_by('doc_id', 'DESC');
        
        return $query->execute()->as_array();
    }
    
    public function get_num_documents($language_id = "")
    {
        $query = DB::select(DB::expr('COUNT(*) AS doc_count'))->from('doc_uploads');
        
        if($language_id)
            $query->where('doc_language','=',$language_id);
        
        return $query->execute()->get('doc_count');
    }
}