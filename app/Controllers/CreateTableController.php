<?php

namespace App\Controllers;

use App\Models\CreateTableModel;
use App\Views\CreateTableView;

class CreateTableController {
  private $view;
  private $model;
  function __construct() {
    $this->view = new CreateTableView();
    $this->model = new CreateTableModel($this->view);
    // $this->view = $view;
    // $this->model = $model;
    $this->show_the_form();
    $this->save_table_name();
  }

  
    public function show_the_form(){
      $this->view->show_the_form();
    }


    public function save_table_name(){
        if (isset($_POST["tname"])) {  // as long as I send POST data to file inself, POST data keeps hanging :(
            $result_from_model = $this->model->save_table_name($_POST["tname"]);
            if ($result_from_model){
              $this->view->show_the_message($result_from_model); 
            }           
        }
      
    }


}