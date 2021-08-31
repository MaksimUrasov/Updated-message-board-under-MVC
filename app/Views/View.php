<?php
//HTML or PDF output can only be generated and output in one place - the View.

namespace App\Views;

use App\Models\GetPdo;
use PDOException;
use App\Views\OldMessage;

session_start();


// require_once "app/view/html_body.php"; // iskeliant i virsu nebeveikia, using $this when not in object context
// require_once __DIR__ . '/../models/GetPdo.php'; 
// require_once __DIR__ . '/old_message.php'; 


class View {

    function __construct(){  

        $this->number_of_posts_per_page = 10; //  uzkraus desimt old messages per page
        $this->pdo = GetPdo::get_connection();
        $this->table_name = GetPdo::get_table_name();
        $this->page_to_show = $this->get_current_page() ; // kad cia nebutu logikos, iskeliau i atskira funkcija. Tik jau nebesigavo array function.
        $this->amount_of_entries = $this->download_amount_of_old_messages();
        $this->apply_additional_css();
    }

    public function setModel($model){
        $this->model = $model;
    }

    private function get_current_page(){
        if (array_key_exists("page",$_GET)) {
           return intval($_GET["page"]);
        } else {
            return 1;
        }
    }
    
    // private function calculate_offset(){
    //     return $this->page_to_show * $this->number_of_posts_per_page - $this->number_of_posts_per_page;
    // }

    // private function calculate_amount_of_pages(){
    //     return ceil($this->amount_of_entries / $this->number_of_posts_per_page); 
    // }
    

    public function get_session_value($key){ //this function is needed for each form input value.
        if(array_key_exists($key,$_SESSION)){
            echo $_SESSION[$key];
        } else{
            echo "";
        }
    }
    
    public function load_html_body(){
        include __DIR__ . "/HtmlBody.php";  // it is not good to includ in the middle of a file, but so far I do not have better option. 
    }


    
    public function show_server_message_ok(){
        if(array_key_exists("DB_updated",$_SESSION)){
            echo $_SESSION["DB_updated"];   
        }

    }      
    public function show_server_message_err(){
        if(array_key_exists("DB_error",$_SESSION)){
            echo $_SESSION["DB_error"];  
        }

    }   
        
    
    
    public function  apply_additional_css(){

        foreach ($_SESSION as $key => $value) {
            if (str_contains($key, "_err") && isset($value) ) { //ther is error message
                $this->add_css_error($key);
            } elseif(str_contains($key, "_err")) { // no errors
                $key_without_err = str_replace("_err", "",$key);

                $this->add_css_complete($key_without_err);
            } // do nothing with the input values in Session, we need only to change appearance according to error messages in session.
        }
    }
        
    public function add_css_error($field){
        echo "<style type='text/css'>
        .$field {
            color: red;
            font-size: 100%; 
        }   
        </style>"; // font size under the submit fields originally is small, so have to increase it to 100%, means to normal
    }

    public function add_css_complete($field){
        echo "<style type='text/css'>
        .$field {
            pointer-events:none;
            border-color:green;
        }   
        </style>"; //disabled="disabled" can not be used here, as it prevents form data to be sent on later stage.
                    // in JS I have used "disabled"= true, because info is being sent not via POST;
    }
    



    public function download_old_messages(){
        
        try {
            $calculated_offset = $this->page_to_show * $this->number_of_posts_per_page - $this->number_of_posts_per_page;

            $sql = "SELECT * FROM $this->table_name ORDER BY id DESC LIMIT $this->number_of_posts_per_page OFFSET $calculated_offset";
            $result = $this->pdo->query($sql);


            foreach($result as $k=>$v) {
                new OldMessage($v["email"],$v["name"],$v["birth_date"],$v["message"]);
                // $old_message->set_variables($v["name"],$v["birth_date"],$v["message"]);  // I better use __construct for to pass arguments
            }

        } catch(PDOException $e) {
            echo "Error of downloading messages from DB: " . $e->getMessage();
        }
    }

    public function download_amount_of_old_messages(){ // download number of entries in DB table
        // global $table_name;
        try {
            $sql = "SELECT count(*) FROM $this->table_name";
            $result = $this->pdo->query($sql);
            $number_of_entries = $result->fetchColumn();
            return $number_of_entries;
        } catch(PDOException $e) {
            echo "Error of downloading amount of messages from DB: " . $e->getMessage();
        }  
        
    }  
    
    public function create_page_links(){
        
        $amount_of_pages = ceil($this->amount_of_entries / $this->number_of_posts_per_page); 

        for ($i = 1; $i <= $amount_of_pages; $i++) {

            if($i == $this->page_to_show) { // if we are on a page one, there shall be no link to itself.
                echo "<p>$i</p>"; 
            }else {
                // page number is sent to $page_to_show variable via GET method, all index.php is reloaded:
                echo "<p><a href='http://message.vienasmedis.lt/index.php?page=$i,tag=#messages'>$i</a></p>"; 
            }                                                                                                    
        }

    }

    public function proceed_json($result_json){  // JS will take this JSON message
        echo $result_json;
    }

}


