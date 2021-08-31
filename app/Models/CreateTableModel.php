<?php
namespace App\Models;

use PDOException;

class CreateTableModel {
    // private $view;
    function __construct($view) {
      // $this->view = $view;
      $this->pdo = GetPdo::get_connection();
    }
    
  
    public function save_table_name($new_tname){
      // echo $new_name;
      $clean_table_name =  preg_replace('/\s+/', '_', $new_tname);
  
      try {
        
        $sql = "CREATE TABLE {$clean_table_name} (
            id INT(6) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
            name TEXT(30) COLLATE utf8_general_ci NOT NULL,
            birth_date DATE NOT NULL,
            email TEXT(50) COLLATE utf8_general_ci,
            message TEXT(500) COLLATE utf8_general_ci NOT NULL
            );";
            
        $this->pdo->exec($sql); // use exec() because no results are returned
  
        return $clean_table_name; 
        // $this->view->show_the_message($clean_table_name);  
  
      } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
      }
  
      unset($_POST); // this does not help as long as I send POST data to the same file.
      
    }
  
  }
  
