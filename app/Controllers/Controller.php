<?php

namespace App\Controllers;

// spl_autoload_register(function($className){
//   require_once 'libraries/'.$className.'.php';
// });

// require_once __DIR__ . "/../views/View.php"; 
// require_once __DIR__ . "/../models/Model.php"; 

use App\Models\Model;
use App\Views\View;


class Controller {

  // private $view;   do I need to declare them here? 
  // private $model;
  // public $view = "View class is not initialized so far";
  // private $view;
  public function __construct() {
        // $this->check_who_triggered_me();
    $this->model = new Model(); 
    $this->view = new View(); 
    $this->run();

    
  }



  // public function check_who_triggered_me(){ 
  public function run(){ 
    
    if (array_key_exists("first_name", $_POST)) { 
      
      $result_json = $this->model->proceed_the_data(1); // true means INDEX.php triggered me, lets process the POST data, no need to load whole view
      $this->view->proceed_json($result_json);
      // $this->view = new View(10);  
      // $this->view->setModel($this->model); //? do I really need model in views?

    }elseif ( json_decode(file_get_contents("php://input")) ) { 
      
      $result_json = $this->model->proceed_the_data(0); // false means AJAX triggered me, lets process the data, no need to load whole view
      $this->view->proceed_json($result_json);

    } else { // there is no data to process, lets load normal html:
      $this->view->load_html_body();

    }

  }




}