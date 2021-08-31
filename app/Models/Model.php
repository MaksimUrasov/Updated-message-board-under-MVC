<?php
//Data validation and business rules can only be applied in one place - the Model.
//SQL statements can only be generated and executed in one place 

namespace App\Models;

use App\Models\GetPdo;

// connect to DB

// require_once  __DIR__. '/GetPdo.php';  // this one is not necessary with composer autoload.



class Model {
   
    public function __construct(){
        // $this->table_name = GetPdo::$table_name;
        $this->pdo = GetPdo ::get_connection();
        $this->table_name = GetPdo::get_table_name();
        // $this->table_name = "Posts3";

        $this->information_to_client_message_saved = "Your message has been saved. Thank you!"; 
        $this->information_to_client_message_not_saved ="Saving to DB has failed";    
    }

    public function proceed_the_data($indexOne_ajaxZero){

        session_start();
        session_destroy(); // This is necessary to delete old data, from previous "send message". All fresh data will be received via POST method.
        session_start();   //destroying the Session from starting a new session, so I have to start session again
 

        if ($indexOne_ajaxZero) {// true means info came from index.php, then we have to take POST data:
            $validation_result_object = $this->validate_input($_POST["first_name"],$_POST["last_name"],$_POST["birth"],$_POST["email"],$_POST["message"]);

            if($validation_result_object->contains_errors){// if there are some errors, have to save each POST value to $_SESSION to be shown again on index.php page and return to index.php 
                $this->save_info_to_session($validation_result_object);
          
            } else {//if there are no errors, wll run send_info_to_DB
                
                // I can not prepare data before, f.e. on data validation step, because if the data contains error, it has to be returned for correction in the same state, not prepared for DB.
                $prepared_data_object = $this->prepare_data_for_DB($validation_result_object);
                // var_dump($prepared_data_object);
                // echo "<br>";

                $sending_to_DB_result = $this->send_info_to_DB($prepared_data_object); // this function not only saves to DB, but as confirmation also returns an object.
                // var_dump( $sending_to_DB_result);

                if($sending_to_DB_result->success_or_not){ 
                    //as long as we redirect, have to save messages to Session:
                    $_SESSION['DB_updated'] = $sending_to_DB_result->text_message;
                    echo $_SESSION['DB_updated'];
                }else{
                    $_SESSION['DB_error'] = $sending_to_DB_result->text_message . "<br>" . "Request: ". $sending_to_DB_result->sql . "<br>" . $sending_to_DB_result->error_message;
                    echo $_SESSION['DB_error'];
                    
                };
            }

            header("Location: index.php");
            exit();

        } else {//  info was sent to me by AJAX in JSON format, so lets use it!
            $data_from_JSON = json_decode(file_get_contents("php://input"));
            $validation_result_object = $this->validate_input($data_from_JSON->first_name,$data_from_JSON->last_name,$data_from_JSON->birth,$data_from_JSON->email,$data_from_JSON->message);
      
            if($validation_result_object->contains_errors){
                $validation_result_object->errror_message = "That is very weird, but seems you have passed the wrong data in input fields. PHP input validator has found some mistakes.
                Try to refresh a page and resubmit a form. <br> Values and Errors: ";
                return json_encode($validation_result_object);

              // var_dump($_SESSION);
            } else {
                
                $prepared_data_object = $this->prepare_data_for_DB($validation_result_object);
                $sending_to_DB_result = $this->send_info_to_DB($prepared_data_object); // this function not only saves to DB, but as confirmation also returns an object.
        
                return json_encode($sending_to_DB_result);
            };
            // there is no need to redirect back to index.php, it is enough to echo the result in JSON.
        }
    }

    

    public function validate_input($fn, $ln, $b, $e, $m){ // JS makes same validation in browser, but it is better to recheck data on server      
  
        $first_name = $this->test_input($fn); 
        $last_name = $this->test_input($ln);  
        $birth = $this->test_input($b); // we save birth date to DB, exact age of customer will be calculated on loading the message.
        $email = $this->test_input($e);
        $message = $this->test_input($m);

        $there_is_an_error= false;

        if(!preg_match("/^[a-zA-Z-' ]*$/",$first_name)){
            $first_name_err =  "shall contain only letters and whitespaces."; 
            array_push($errors,$first_name_err);
            $there_is_an_error= true;
        };

        if(!preg_match("/^[a-zA-Z-' ]*$/",$last_name)){
            $last_name_err = "shall contain only letters and whitespaces.";
            array_push($errors,$last_name_err);
            $there_is_an_error= true;
        };


        if($birth > date("Y-m-d")){
            $birth_err = " can not be in the future!"; // the beginning of the sentence " *Your date of birth" is already displayed.
            array_push($errors,$birth_err);
            $there_is_an_error= true;
        };


        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $email_err = ": Seems there is a typing error.";  
            array_push($errors,$email_err);
            $there_is_an_error= true;
        };

        if(strlen($message)<3){
            $message_err =  "shall contain at least 3 characters";
            array_push($errors,$message_err);
            $there_is_an_error= true;
        } else if(strlen($message)>500){
            $message_err =  "shall contain less than 500 characters";  // this is DB limitation I have set for this field.
            array_push($errors,$message_err);
            $there_is_an_error= true;
        };

        $result = new \stdClass();
        $result->contains_errors = $there_is_an_error;

        $result->first_name = $first_name; 
        $result->last_name = $last_name;  
        $result->birth = $birth;
        $result->email = $email;
        $result->message = $message;

        $result->first_name_err = $first_name_err ?? null; 
        $result->last_name_err = $last_name_err ?? null;
        $result->birth_err = $birth_err ?? null;
        $result->email_err = $email_err ?? null;
        $result->message_err = $message_err ?? null;

        
        return $result;
    }

    


    public function test_input($data) {
        $data = trim($data); //Strip unnecessary characters (extra space, tab, newline)
        $data = stripslashes($data); //Remove backslashes (\) 
        $data = htmlspecialchars($data); //converts special characters to HTML entities.
        return $data;
    }

    public function save_info_to_session($validation_result_object){

        // $_SESSION['first_name']= $validation_result_object['first_name']; 
        // $_SESSION['last_name']= $validation_result_object['last_name'];  
        // $_SESSION['birth']= $validation_result_object['birth'];
        // $_SESSION['email']= $validation_result_object['email'];
        // $_SESSION['message']= $validation_result_object['message'];

        // $_SESSION['first_name_err'] = $validation_result_object['first_name_err']; // Session will contain empty keys if there are no errors
        // $_SESSION['last_name_err'] = $validation_result_object['last_name_err'];
        // $_SESSION['birth_err'] = $validation_result_object['birth_err'];
        // $_SESSION['email_err'] = $validation_result_object['email_err'];
        // $_SESSION['message_err'] = $validation_result_object['message_err'];

        foreach ($validation_result_object as $key => $value) {
            $_SESSION[$key]= $value;
        }


    }


    public function prepare_data_for_DB($object){
        $result = new \stdClass();

        $result->name = $object->first_name . " " . $object->last_name;
        $result->birth = $object->birth;
        $result->email = $object->email ?: "NULL"; // to save text "NULL" to DB on later stage
        $result->message = $object->message;

        return $result;
    }

    public function send_info_to_DB($object){
    
        // insert row into DB table
        try {
               
            $sql = "INSERT INTO $this->table_name (id, name, birth_date, email, message)
            VALUES (NULL, ?, ?, ?, ?)";

            $array_to_insert = array($object->name,$object->birth,$object->email,$object->message);
            $result = $this->pdo->prepare($sql);
            $result->execute($array_to_insert);
            

            //prepare the output:
            $result_object = new \stdClass();
            $result_object->success_or_not = true;
            $result_object->text_message = $this->information_to_client_message_saved;
            
            return $result_object; // this object will be visible in console log

        } catch(\PDOException $e) {
                        
            $result_object = new \stdClass();
            $result_object->success_or_not = false;
            $result_object->text_message = $this->information_to_client_message_saved;
            $result_object->sql = $sql;
            $result_object->error_message = $e->getMessage();
            
            return $result_object; // this object will be visible in console log

            

        }
        
        //echo "message sent to DB";
    }
    
};