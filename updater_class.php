<?php
/*
The MIT License (MIT)
Copyright (c) 2015 Jan Knipper <j.knipper@part.berlin>
Copyright (c) 2021 CS-Digital UG <info  @cs  -  digital-   ug . ~~ de >
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
SOURCE: https://github.com/dmd2222/ ###
   */

class updater{


    private $filepath;
    private $update_path;
    private $no_cache;
    private $make_backup_file;
    private $check_response;
    private $only_update_if_updates_are_at_least_percentage_similar;

  
    //Options in the __construct - Constructore


    //Public functions

    //Getter Setter

    //### check inputs of setter methodes and throw exception if wrong
    public function getFilepath(){
		return $this->filepath;
	}

	public function setFilepath($filepath){
		$this->filepath = $filepath;
	}

	public function getUpdate_path(){
		return $this->update_path;
	}

	public function setUpdate_path($update_path){
		$this->update_path = $update_path;
	}

	public function getNo_cache(){
		return $this->no_cache;
	}

	public function setNo_cache($no_cache){
		$this->no_cache = $no_cache;
	}

	public function getMake_backup_file(){
		return $this->make_backup_file;
	}

	public function setMake_backup_file($make_backup_file){
		$this->make_backup_file = $make_backup_file;
	}

	public function getCheck_response(){
		return $this->check_response;
	}

	public function setCheck_response($check_response){
		$this->check_response = $check_response;
	}

	public function getOnly_update_if_updates_are_at_least_percentage_similar(){
		return $this->only_update_if_updates_are_at_least_percentage_similar;
	}

	public function setOnly_update_if_updates_are_at_least_percentage_similar($only_update_if_updates_are_at_least_percentage_similar){
		$this->only_update_if_updates_are_at_least_percentage_similar = $only_update_if_updates_are_at_least_percentage_similar;
	}


    //OTHER




    public function __construct($filepath,$update_path,$no_cache = true, $make_backup_file=false,$check_response=true,$only_update_if_updates_are_at_least_percentage_similar =50)
{

      //Options
      define("DEBUGGING_MODE",true);


    //set all vars
    $this->setFilepath($filepath);
    $this->setUpdate_path($update_path);
    $this->setNo_cache($no_cache);
    $this->setMake_backup_file($make_backup_file);
    $this->setCheck_response($check_response);
    $this->setOnly_update_if_updates_are_at_least_percentage_similar($only_update_if_updates_are_at_least_percentage_similar);



    //Update Updater Class
    var_dump(array("Updater_class: Update myself:",self::check_new_update_if_new_inform_admin("updater_class.php","https://raw.githubusercontent.com/dmd2222/php-update-class/main/updater_class.php","",true,50)));
}




    static public function do_file_update($filepath="",$update_path="",$no_cache = true, $make_backup_file=false,$check_response=true,$only_update_if_updates_are_at_least_percentage_similar =50){

        #$filepath,$update_path,$no_cache = true, $make_backup_file=false,$check_response=true,$only_update_if_updates_are_at_least_percentage_similar =50
       


        //check vars
        if(is_string($filepath)<>true)return("Not a valid file path.");
        if(is_string($update_path)<>true)return("Not a valid update path.");

        //file exist
        if (file_exists($filepath)==false)  throw new Exception("updater_class: do_file_update: Fiel not found. " . $filepath);

        //get values
        $response_result = self::download_file_text($update_path,$no_cache);
        $contents = self::read_file($filepath);
        
        self::debugging_fkt(array("response_result: " ,$response_result ,"contents: " ,$contents,array_shift(debug_backtrace())));//for debugging

        //check response
        if($check_response<>false){
            if($response_result =="" || $contents == ""){
                $err="updater_class: do_file_update: Local file or update is empty. If this is correct, please update manually.";
                var_dump($err);
                throw new Exception($err);
            }
        }

        //check similarity
        $percentage_similar=self::wordSimilarity($contents,$response_result);
        if($percentage_similar<=$only_update_if_updates_are_at_least_percentage_similar){
                //not high enought similarity
                $err=array("updater_class: do_file_update: check similarity - not high enought similarity","File: ",basename($filepath),"percentage_similar: ",$percentage_similar . "%");
                var_dump($err);
                throw new Exception("updater_class: do_file_update: " . $err);
                
        }
        
        //compare values
        if($contents<>$response_result){

            //make backup?
            if($make_backup_file==true){
                    //make backup of file
                    $backup_file_name="backup_" . time() . "_" . basename($filepath) . ".php";

                    //copy file
                    if (!copy($filepath, $backup_file_name)) {
                        throw new Exception("update_class: do_file_update: copy $filepath failed...\n");
                    }

                    //protect file
                    chmod($backup_file_name,0600);
            }


            $a = file_put_contents($filepath, $response_result);
            if($contents<>$response_result){
                return("Error: Update needed! Please update first.");
            }else{
                return true;
            }
        }else{
            return true;
        }


}




static public function check_new_update($filepath="",$update_path="",$no_cache = true){

        
        //check vars
        if(is_string($filepath)<>true)return("Not a valid file path.");
        if(is_string($update_path)<>true)return("Not a valid update path.");


        //get values
        $response_result = self::download_file_text($update_path,$no_cache);
        $contents = self::read_file($filepath);
    
    
    if($contents<>$response_result){
        //new update available
        return true;
    }else{
        //no update available
        return false;
    }


}


static public function check_new_update_if_new_inform_admin($filepath,$update_path,$admin_email = "",$no_cache = true){

    //check inputs
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL) || $admin_email=="") {
        #throw new Exception("updater_class: check_new_update_if_new_inform_admin: Invalid admin email format");
                //create admin email, if not given
                $domain = $_SERVER['SERVER_NAME'];
                $admin_email= "admin@" . $domain;

                self::debugging_fkt(array($domain,$admin_email,array_shift(debug_backtrace())));//for debugging
      }

      
      $update_result= self::check_new_update($filepath,$update_path,$no_cache);
      if($update_result==true){
            //infor admin
            $subject="Update required:" .  time();
            $actual_path=dirname(__FILE__);
            $message=$subject . " <br> " . "My file:" . self::hash_my_file($filepath,"sha256") . " File on server: " . self::hash_update_file($update_path,"sha256",true) . " <br> " . "May the service stops working if you dont update the file." . " <br> " . " Location: " . $actual_path;
            mail($admin_email, $subject ,$message);
            
            self::debugging_fkt(array($admin_email, $subject ,$message,array_shift(debug_backtrace())));//for debugging

      }


    return $update_result;


}




static public function html_output($filepath,$update_path,$no_cache = true){

 
    if(self::check_new_update($filepath,$update_path,$no_cache)==true){
        //new update available
        return "New update available for: " . $filepath . ".";
    }else{
        //no update available
        return "No new update available.";
    }

}


static public function hash_my_file($filepath, $hash_type = "sha256"){
            //check vars
            if(is_string($filepath)<>true)return("Not a valid file path.");
            if(is_string($hash_type)<>true)return("Not a valid hash type.");
            //get values
            $contents = self::read_file($filepath);
            return hash($hash_type,$contents);
}


static public function hash_update_file($update_path,$hash_type = "sha256",$no_cache = true){
            //check vars
            if(is_string($update_path)<>true)return("Not a valid update path.");
            if(is_string($hash_type)<>true)return("Not a valid hash type.");
            //get values
            $response_result = self::download_file_text($update_path,$no_cache);
            return hash($hash_type,$response_result);

}


//Private functions

# example: self::debugging_fkt(array(array_shift(debug_backtrace())));//for debugging
private static function debugging_fkt($debug_it_thing){
          //example: if(DEBUGGING_MODE == true) var_dump(array("Debugging: ",array_shift(debug_backtrace())));//for debugging
          if(DEBUGGING_MODE == true){
                echo "<br>";
                var_dump(array("Debugging: ",$debug_it_thing));//for debugging
                echo "<br>";
          }
}


private static function wordSimilarity($s1,$s2) {

    $words1 = preg_split('/\s+/',$s1);
    $words2 = preg_split('/\s+/',$s2);
    $diffs1 = array_diff($words2,$words1);
    $diffs2 = array_diff($words1,$words2);

    $diffsLength = strlen(join("",$diffs1).join("",$diffs2));
    $wordsLength = strlen(join("",$words1).join("",$words2));
    if(!$wordsLength) return 0;

    $differenceRate = ( $diffsLength / $wordsLength );
    $similarityRate = 1 - $differenceRate;
    return $similarityRate;

}

static private function download_file_text($update_path,$no_cache = true){

     //check vars
     if(is_string($update_path)<>true)return("Not a valid update path.");
     
     if($no_cache==true){
             $update_path=$update_path . "?" . mt_rand();   
     }
    
     $response_result = file_get_contents($update_path);

     //check response
     if(is_string($response_result)<>true)return("Not a valid response result.");
     
     //precheck result
     if($response_result ==false){
        return("Error: was not able to check update.(Update path: " . $update_path . ")<br>");
     }
     return $response_result;
}


static private function read_file($filepath){

        //check vars
        if(is_string($filepath)<>true)return("Not a valid file path.");

        $contents="";
        $fs = fopen( $filepath, "a+" );
        while (!feof($fs)) {
            $contents .= fgets($fs, 1024);
        }
        fclose($fs);



        return $contents;
}




}


?>
