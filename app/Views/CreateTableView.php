<?php

namespace App\Views;

class CreateTableView {

    function __construct() {
      // seems there is nothing to construct?
    }
  
    public function show_the_form(){
      echo 
        '<h3>On this page we can create a table in DB.</h3>
        <form action="index.php" method="post">
        <label for="tname">New table name:</label><br>
        <input type="text" id="tname" name="tname" value="A new awesome table"><br>
        <input type="submit" value="Create">
        </form>';
        // <p><br>Below there is an SQLSTATE error after submitting, I have left it as is so far as long as table name saves successfully.<p>';
    }
    public function show_the_message($t){
      echo "Table $t created successfully";
    }
  
  }