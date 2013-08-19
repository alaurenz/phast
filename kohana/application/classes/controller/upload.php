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

class Controller_Upload extends Controller {
    
    public function before() {
        parent::before();

        if(Auth::instance()->logged_in() == 0) {
            Request::instance()->redirect('account/signin');
        }
        
        $this->model_upload = new Model_Upload;
    }
    
    public function action_index()
    {
        $view = View::factory('template');
        $view->nav_active = "upload";
        $view->page_title = "Upload Document";
        $view->page_content = View::factory('pages/upload');
        
        $model_misc = new Model_Misc;
        
        $empty_field_data = array(
            'doc_title' => '',
            'doc_topic' => '',
            'doc_audience' => '',
            'doc_reading_level' => '',
            'other_notes' => '',
            'languages' => array()
        );
        $upload_successful = FALSE;
        $errors = array();
        
        if($_POST || $_FILES) {
            $post = Validate::factory(array_merge($_POST, $_FILES))
                        ->rule('doc_title', 'not_empty')
                        ->rule('doc_title', 'max_length', array(180))
                        ->rule('doc_topic', 'not_empty')
                        ->rule('doc_topic', 'max_length', array(100))
                        ->rule('doc_upload', 'Upload::not_empty')
                        ->rule('doc_upload', 'Upload::type', array(array('txt', 'docx'))) //'htm', 'html'
                        ->rule('doc_upload', 'Upload::size', array('5M'))
                        ->rule('doc_upload', 'Upload::valid')
                        ->rule('languages', 'not_empty');
                  
            $field_data = $post->as_array();
            
            if ($post->check()) {
                $uploaded_doc_exten = $this->get_file_extension($_FILES['doc_upload']['name']);
                $tmp_uploaded_doc_filename = uniqid().".".$uploaded_doc_exten;
                // Upload source document w/ tmp filename
                $en_sentences = $this->upload_source_document($tmp_uploaded_doc_filename, $_FILES['doc_upload']);

                // Get translations for selected languages
                $doc_ids_uploaded = array();
                foreach ($field_data['languages'] as $language_id_code) {
                    $language_id_code_s = explode("_", $language_id_code);
                    $language_id = $language_id_code_s[0];
                    $language_code = $language_id_code_s[1];
                    
                    // Get generic translated sentences
                    try {
                        $translated_sentences_generic = $this->translate_array($en_sentences, $language_code);
                    } catch (Exception $e) {
                        echo "Exception: " . $e->getMessage() . PHP_EOL;
                    }                    
                    $translation_error = $this->validate_translation($en_sentences, $translated_sentences_generic);
                    if($translation_error != "") {
                        $errors['doc_upload'] = $translation_error;
                    }
                    
                    // determine default translation model
                    $default_translation_model = $this->model_upload->get_translation_model_data($model_misc->get_default_translation_model_id()); 
                    if($default_translation_model['category_code'] == "") {
                        // Only use generic translation model (generic model is default)
                        $translated_sentences = $translated_sentences_generic;
                    } else {
                        // Get non-generic MT-translated sentences
                        try {
                            $translated_sentences = $this->translate_array($en_sentences, $language_code, $default_translation_model['category_code']);
                        } catch (Exception $e) {
                            echo "Exception: " . $e->getMessage() . PHP_EOL;
                        }
                        $translation_error = $this->validate_translation($en_sentences, $translated_sentences);
                        if($translation_error != "") {
                            $errors['doc_upload'] = $translation_error;
                        }
                    }
                    
                    if(!$errors) {
                        // Store the document and translations                        
                        $doc_data = array(
                            'translation_model_id' => $default_translation_model['model_id'],
                            'doc_title' => $field_data['doc_title'],
                            'doc_language' => $language_id, 
                            'doc_topic' => $field_data['doc_topic'],
                            'doc_audience' => $field_data['doc_audience'],
                            'doc_reading_level' => $field_data['doc_reading_level'],
                            'other_notes' => $field_data['other_notes'],
                            'total_sentences' => count($en_sentences),
                            'date_uploaded' => time()
                        );
                        $new_doc_id = $this->model_upload->insert_upload($doc_data);
                        $this->model_upload->insert_translation($new_doc_id, $en_sentences, $translated_sentences);
                        // store generic translated sentences if generic model is NOT the default
                        if($default_translation_model['category_code'] != "") {
                            $this->model_upload->insert_generic_translation($new_doc_id, $translated_sentences_generic);    
                        }
                        array_push($doc_ids_uploaded, $new_doc_id);                  
                   }
                }
                
                if($errors) {
                    foreach($doc_ids_uploaded as $doc_id_uploaded) {
                        $this->model_upload->delete_doc($doc_id_uploaded);
                    }
                    unlink(Kohana::config('mainconf.path.docs')."/$tmp_uploaded_doc_filename");
                } else {
                    $upload_successful = TRUE;
                    // Rename uploaded document file to reflect each of the doc_ids 
                    // that were added to the database
                    $uploaded_doc_filename = "";
                    foreach($doc_ids_uploaded as $doc_id_uploaded) {
                        $uploaded_doc_filename .= $doc_id_uploaded."_";
                    }
                    $uploaded_doc_filename .= ".".$uploaded_doc_exten;
                    rename(Kohana::config('mainconf.path.docs')."/$tmp_uploaded_doc_filename", Kohana::config('mainconf.path.docs')."/$uploaded_doc_filename");
                    $field_data = $empty_field_data;
                }
                
            } else {
                $errors = $post->errors('upload');
            } 
        
        } else {
            $field_data = $empty_field_data;
        } 
         
        $view->page_content->edit_doc_id = 0;
        $view->page_content->field_data = $field_data;
        $view->page_content->languages = $model_misc->get_languages();
        $view->page_content->topics = $model_misc->get_topics();
        $view->page_content->upload_successful = $upload_successful;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }
    
    public function action_edit_metadata($doc_id)
    {
        $view = View::factory('template');
        $view->nav_active = "";
        $view->page_title = "Edit Document Metadata";
        $view->page_content = View::factory('pages/upload');
        
        $model_postedit = new Model_Postedit;
        $doc_upload_data = $model_postedit->get_doc_upload_data($doc_id);
        if(!$doc_upload_data) {
            echo "Document with this ID does not exist.";
        } else {
            $field_data = array(
                'doc_title' => $doc_upload_data['doc_title'],
                'doc_topic' => $doc_upload_data['doc_topic'],
                'doc_audience' => $doc_upload_data['doc_audience'],
                'doc_reading_level' => $doc_upload_data['doc_reading_level'],
                'other_notes' => $doc_upload_data['other_notes']
            );
            $errors = array();
            
            if($_POST) {
                $post = Validate::factory($_POST)
                            ->rule('doc_title', 'not_empty')
                            ->rule('doc_title', 'max_length', array(180))
                            ->rule('doc_topic', 'not_empty')
                            ->rule('doc_topic', 'max_length', array(100));
                      
                $field_data = $post->as_array();
                
                if ($post->check()) {
                    
                    $doc_data = array(
                        'doc_title' => $field_data['doc_title'],
                        'doc_topic' => $field_data['doc_topic'],
                        'doc_audience' => $field_data['doc_audience'],
                        'doc_reading_level' => $field_data['doc_reading_level'],
                        'other_notes' => $field_data['other_notes']
                    );
                    $this->model_upload->update_doc_metatdata($doc_id, $doc_data);
                    
                    Request::instance()->redirect('postedit/view_edit/'.$doc_id);              
                    
                } else {
                    $errors = $post->errors('upload');
                } 
            
            }
            $model_misc = new Model_Misc;  
            
            $view->page_content->edit_doc_id = $doc_id;
            $view->page_content->field_data = $field_data;
            $view->page_content->topics = $model_misc->get_topics();
            $view->page_content->upload_successful = FALSE;
            $view->page_content->errors = $errors;
            $this->request->response = $view;
        }
    } 
    
    public function action_upload_revision($doc_id)
    {
        $view = View::factory('template');
        $view->nav_active = "";
        $view->page_title = "Upload Document Revision";
        $view->page_content = View::factory('pages/upload_revision');
        
        $most_recent_revision = $this->model_upload->get_most_recent_revision($doc_id); 
        $orig_doc_id = ($most_recent_revision['orig_doc_id'] == 0) ? $doc_id : $most_recent_revision['orig_doc_id'];
        
        // ensure most recent revision of this document has been completed (complted == 1) otherwise redirect
        if($most_recent_revision['completed'] != 1)
            Request::instance()->redirect('postedit/view_edit/'.$doc_id);  
        
        $model_postedit = new Model_Postedit;
        $orig_doc_data = $model_postedit->get_doc_upload_data($orig_doc_id);
        assert($orig_doc_data['orig_doc_id'] == 0);      
        
        $errors = array();
        
        if($_POST || $_FILES) {
            $post = Validate::factory(array_merge($_POST, $_FILES))
                        ->rule('doc_upload', 'Upload::not_empty')
                        ->rule('doc_upload', 'Upload::type', array(array('txt', 'docx'))) //'htm', 'html'
                        ->rule('doc_upload', 'Upload::size', array('5M'))
                        ->rule('doc_upload', 'Upload::valid');
            
            $field_data = $post->as_array();
            
            if ($post->check()) {
                $uploaded_doc_exten = $this->get_file_extension($_FILES['doc_upload']['name']);
                $tmp_uploaded_doc_filename = uniqid().".".$uploaded_doc_exten;
                // Upload source document w/ tmp filename
                $new_en_sentences = $this->upload_source_document($tmp_uploaded_doc_filename, $_FILES['doc_upload']);
                
                $language_code = $this->model_upload->get_target_language_code($orig_doc_data['doc_language']);
                // Get new generic translated sentences
                try {
                    //$new_translated_sentences = $this->translate_array($new_en_sentences, $language_code);
                    $new_translated_sentences_generic = $this->translate_array($new_en_sentences, $language_code);
                } catch (Exception $e) {
                    echo "Exception: " . $e->getMessage() . PHP_EOL;
                }
                $translation_error = $this->validate_translation($new_en_sentences, $new_translated_sentences_generic);
                if($translation_error != "") {
                    $errors['doc_upload'] = $translation_error;
                }
                
                // determine default translation model
                $model_misc = new Model_Misc;
                $default_translation_model = $this->model_upload->get_translation_model_data($model_misc->get_default_translation_model_id()); 
                if($default_translation_model['category_code'] == "") {
                    // Only use generic translation model (generic model is default)
                    $new_translated_sentences = $new_translated_sentences_generic;
                } else {
                    // Get non-generic MT-translated sentences
                    try {
                        $new_translated_sentences = $this->translate_array($new_en_sentences, $language_code, $default_translation_model['category_code']);
                    } catch (Exception $e) {
                        echo "Exception: " . $e->getMessage() . PHP_EOL;
                    }
                    $translation_error = $this->validate_translation($new_en_sentences, $new_translated_sentences);
                    if($translation_error != "") {
                        $errors['doc_upload'] = $translation_error;
                    }
                }
                
                if($errors) {
                    // delete uploaded file
                    unlink(Kohana::config('mainconf.path.docs')."/$tmp_uploaded_doc_filename");
                } else {
                    // get array of post-edited sentences from most recent document revision 
                    // that remain the same in this new revision being uploaded
                    $old_postedited_sentences = $this->get_old_postedited_sentences($model_postedit->get_sentence_pairs($most_recent_revision['doc_id']), $new_en_sentences);
                    
                    // count number of sentences that did not change in the revised document;
                    // these sentences are to be marked completed in revision
                    $num_unchanged_sentences = 0;
                    foreach($old_postedited_sentences as $old_sent) {
                        if($old_sent != "")
                            $num_unchanged_sentences++;
                    }               
                    
                    if($num_unchanged_sentences == count($new_en_sentences)) {
                        // revision being uploaded is identical to existing document
                        unlink(Kohana::config('mainconf.path.docs')."/$tmp_uploaded_doc_filename");
                        $errors['doc_upload'] = 'You selected a document that is identical to the most recent document version.';
                    } else {
                        $doc_data = array(
                            'translation_model_id' => $default_translation_model['model_id'],
                            'orig_doc_id' => $orig_doc_id,
                            'doc_title' => $orig_doc_data['doc_title'],
                            'doc_language' => $orig_doc_data['doc_language'], 
                            'doc_topic' => $orig_doc_data['doc_topic'], 
                            'doc_audience' => $orig_doc_data['doc_audience'],
                            'doc_reading_level' => $orig_doc_data['doc_reading_level'],
                            'sentences_completed' => $num_unchanged_sentences,
                            'total_sentences' => count($new_en_sentences),
                            'date_uploaded' => time()
                        );
                        if($num_unchanged_sentences > 0)
                            $doc_data['postedited'] = 1;
                        
                        $new_doc_id = $this->model_upload->insert_upload($doc_data);
                        $this->model_upload->insert_translation($new_doc_id, $new_en_sentences, $new_translated_sentences, $old_postedited_sentences);
                        // store generic translated sentences if generic model is NOT the default
                        if($default_translation_model['category_code'] != "") {
                            $this->model_upload->insert_generic_translation($new_doc_id, $new_translated_sentences_generic);    
                        }

                        // rename uploaded document to reflect doc_id
                        $uploaded_doc_filename = $new_doc_id.".".$uploaded_doc_exten;
                        rename(Kohana::config('mainconf.path.docs')."/$tmp_uploaded_doc_filename", Kohana::config('mainconf.path.docs')."/$uploaded_doc_filename");

                        // redirect to newly uploaded revision (& show doc uploaded message)
                        Request::instance()->redirect('postedit/view_edit/'.$new_doc_id);   
                    }
               }
                
            } else {
                $errors = $post->errors('upload');
            }
        }
        
        $view->page_content->orig_doc_id = $orig_doc_id;
        $view->page_content->errors = $errors;
        $this->request->response = $view;
    }
    
    /*
     * Given sentences from a document 
     *
     * @param $old_sentence_pairs sentences from an existing document 
     *
     * @return array of old sentences where only unchanged (in revision) sentences remain
               (all others are empty and will be replaced with new MT sentences)
               if all source sentences in $old_sentence_pairs are identical to those in 
               $en_sentences return "" (means revision was identical to existing document)
               
     */
    private function get_old_postedited_sentences($old_sentence_pairs, $en_sentences)
    {
        $num_old_sentence_pairs = count($old_sentence_pairs);
        $old_postedited_sentences = array(); // postedited sentences found to match sentences from original source document
        $i = 0;
        foreach ($en_sentences as $new_en_sentence) {
            $new_en_sentence = trim($new_en_sentence);
            $j = 1;
            foreach ($old_sentence_pairs as $old_sentence_pair) {
                $old_en_sentence = trim($old_sentence_pair['source_text']);
                
                if($new_en_sentence == $old_en_sentence) {
                    // add new sentence w/ translation from $old_sentence_pair (& mark sentence as complete)
                    $old_postedited_sentences[$i] = $old_sentence_pair['text']; // set postedit_text to $old_sentence_pair['text'] which marks this sentence as done (since the source sentence has not changed)
                    // mt_text will be set to $new_translated_sentences[$i] (already done)
                    
                    // TODO: (for loop efficiency) remove current old sentence pair from $old_sentence_pairs 
                    break;
                }
                // else: keep looking for matching sentence
                
                // no matching sentence found in previous document version
                // -> so add new sentence normally [w/o translation from $old_sent...instead use new machine translation] (marks sentence as NOT complete)
                if($j == $num_old_sentence_pairs) {
                    // set mt_text to $new_translated_sentences[$i]
                    $old_postedited_sentences[$i] = "";
                }
                $j++;   
            }
            $i++;    
        }
        return $old_postedited_sentences;
    }    
    
    private function get_file_extension($filename)
    {    
        $doc_upload_split = explode('.', $filename);
        return end($doc_upload_split);
    }
    
    /**
     * $file_uploaded is the existing $_FILES['some_file']
     */ 
    private function upload_source_document($upload_filename_to_save, $file_uploaded) 
    {
        Upload::save($file_uploaded, $upload_filename_to_save, Kohana::config('mainconf.path.docs'));
        $upload_file_path = Kohana::config('mainconf.path.docs')."/$upload_filename_to_save";
        
        $file_exten = $this->get_file_extension($upload_filename_to_save);
        if($file_exten == "txt") {
            $en_sentences = $this->extract_sentences_txt($upload_file_path);
        } 
        else if($file_exten == "docx") {
            require_once Kohana::config('mainconf.path.phpdocx').'/classes/CreateDocx.inc';
            
            $new_textfile_path = Kohana::config('mainconf.path.docs')."/$upload_filename_to_save.txt";
            $docx = new CreateDocx();
            $docx->docx2txt($upload_file_path, $new_textfile_path);
            $en_sentences = $this->extract_sentences_txt($new_textfile_path);
            // delete temporary text file
            unlink($new_textfile_path);
        }
        return $en_sentences;
    }

    /**
     * Ensurese machine-translation input does not exceed limits and
     * and translation was performed successfully
     * 
     * @param $en_sentences array of source english sentences
     * @param 
     * @return error message or the empty string if there are no errors 
     */
    private function validate_translation($en_sentences, $translated_sentences)
    {
        // check if it exceeds max total chars
        $total_chars = 0;
        foreach($en_sentences as $sent) {
            $total_chars += strlen($sent);   
        }        
        if($total_chars > Kohana::config('mainconf.max_chars_translate_arr')) {
            return "Document contains too many characters to translate.";
        } 
        // check if it exceeds max total lines
        if(count($en_sentences) > Kohana::config('mainconf.max_items_translate_arr')) {
            return "Document contains too many sentences to translate.";
        }
        if(count($en_sentences) != count($translated_sentences)) {
            return "There was a problem contacting the Microsoft Translator API. Try again but if this error occurs again the query limit may have been exceeded.";
        }
        return "";
    }
    
    /** 
     * Extracts and cleans text from given text file and returns an 
     * array of the "sentences" from the file (each sentence is 
     * assumed to be each non-blank line in the file)
     *
     * @param $file absolute path to text file 
     * @return array of cleaned text where each array element is a
     *         single line from the text file
     */
    private function extract_sentences_txt($file) 
    {
        $raw_text = file_get_contents($file);
        $sentences = array();
        $text_lines = explode("\n", $raw_text);
        foreach ($text_lines as $line) {
            $line = trim($line);
            if($line != "") { //  && preg_match("/([a-zA-Z])/", $line)
                array_push($sentences, $line);
            }
        }
        return $sentences;
    }
    
    /** 
     * Submit given array of english sentences to Microsoft translate using 
     * TranslateArray (http://msdn.microsoft.com/en-us/library/ff512422.aspx)
     * 
     * @param $inputStrArr array of sentences to translate
     * @param $toLanguage Microsoft Translator API language code of language 
     *                    to translate to (target language) 
     * @param $categoryCode code for specifying a customized translation model
     *                      if set to "" use the generic model (default)
     * @return array of machine-translated sentences returned by Microsoft
     *         Translator API
     */
    private function translate_array($inputStrArr, $toLanguage, $categoryCode = "")
    {
        $translated_sentences = array();
        try {
            //Client ID of the application.
            $clientID     = Kohana::config('mainconf.microsoft.clientID');
            //Client Secret key of the application.
            $clientSecret = Kohana::config('mainconf.microsoft.clientSecret');
            $authUrl      = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
            $scopeUrl     = "http://api.microsofttranslator.com";
            $grantType    = "client_credentials";
        
            //Get the Access token.
            $accessToken  = $this->get_tokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
            $authHeader = "Authorization: Bearer ". $accessToken;
        
            //Set the params.
            $fromLanguage = "en";
            //$toLanguage   = "es"; NOTE: GIVEN AS PARAMETER ABOVE
            $contentType  = 'text/plain';
        
            //Get the Request XML Format.
            $requestXml = $this->create_req_xml($fromLanguage,$toLanguage,$contentType,$inputStrArr,$categoryCode);
        
            //HTTP TranslateMenthod URL.
            $translateUrl = "http://api.microsofttranslator.com/v2/Http.svc/TranslateArray";
            
            $curlResponse = $this->curl_request($translateUrl, $authHeader, $requestXml);
        
            //Interprets a string of XML into an object.
            $xmlObj = simplexml_load_string($curlResponse);
            
            // Put results into array
            foreach($xmlObj->TranslateArrayResponse as $translatedArrObj) {
                array_push($translated_sentences, "".$translatedArrObj->TranslatedText);
            }
            
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage() . PHP_EOL;
        }
        
        return $translated_sentences;
    }
    
    /**
     * Create Request XML Format.
     *
     * @param string $fromLanguage   Source language Code.
     * @param string $toLanguage     Target language Code.
     * @param string $contentType    Content Type.
     * @param string $inputStrArr    Input String Array.
     * @param string categoryCode    code corresponding to language model 
     *                               (if not set, generic model will be used).
     * @return string formatted XML request
     */
    private function create_req_xml($fromLanguage,$toLanguage,$contentType,$inputStrArr,$categoryCode = "") {
        $requestXml = "<TranslateArrayRequest>".
            "<AppId/>".
            "<From>$fromLanguage</From>". 
            "<Options>";

        if($categoryCode != "") {
            $requestXml .= "<Category xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\">$categoryCode</Category>";
        } else {
            $requestXml .= "<Category xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />";
        }
        
        $requestXml .= "<ContentType xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\">$contentType</ContentType>" .
              "<ReservedFlags xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
              "<State xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
              "<Uri xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
              "<User xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
        "</Options>" . 
        "<Texts>";
        foreach ($inputStrArr as $inputStr) {
            $requestXml .=  "<string xmlns=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\">$inputStr</string>";
        }
        $requestXml .= "</Texts>".
            "<To>$toLanguage</To>" .
        "</TranslateArrayRequest>";
        return $requestXml;
    }
    
    /*
    * Create and execute the HTTP CURL request.
    * 
    * @param string $url        HTTP Url.
    * @param string $authHeader Authorization Header string.
    * @param string $postData   Data to post.
    *
    * @return string.
    *
    */
    private function curl_request($url, $authHeader, $postData='') {
        //Initialize the Curl Session.
        $ch = curl_init();
        //Set the Curl url.
        curl_setopt ($ch, CURLOPT_URL, $url);
        //Set the HTTP HEADER Fields.
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array($authHeader,"Content-Type: text/xml"));
        //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, False);
        if($postData) {
            //Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        //Execute the  cURL session. 
        $curlResponse = curl_exec($ch);
        //Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);
        if ($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }
        //Close a cURL session.
        curl_close($ch);
        return $curlResponse;
    }

    /*
     * Get the access token.
     *
     * @param string $grantType    Grant type.
     * @param string $scopeUrl     Application Scope URL.
     * @param string $clientID     Application client ID.
     * @param string $clientSecret Application client ID.
     * @param string $authUrl      Oauth Url.
     *
     * @return string.
     */
    private function get_tokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl) {
        try {
            //Initialize the Curl Session.
            $ch = curl_init();
            //Create the request Array.
            $paramArr = array (
                 'grant_type'    => $grantType,
                 'scope'         => $scopeUrl,
                 'client_id'     => $clientID,
                 'client_secret' => $clientSecret
            );
            //Create an Http Query.//
            $paramArr = http_build_query($paramArr);
            //Set the Curl URL.
            curl_setopt($ch, CURLOPT_URL, $authUrl);
            //Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArr);
            //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //Execute the  cURL session.
            $strResponse = curl_exec($ch);
            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if($curlErrno){
                $curlError = curl_error($ch);
                throw new Exception($curlError);
            }
            //Close the Curl Session.
            curl_close($ch);
            //Decode the returned JSON string.
            $objResponse = json_decode($strResponse);
            
            // REMOVED FOR KOHANA (otherwise causes an error)
            //if ($objResponse->error){
            //    echo "Error $objResponse->error_description";
            //}
            
            return $objResponse->access_token;
        } catch (Exception $e) {
            echo "Exception-".$e->getMessage();
        }
    } 
} 

