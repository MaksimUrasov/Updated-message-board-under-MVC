<?php
// below code is necessary only once, to create a new table in DB, by simply running this file.

// DB was created in serveriai.lt phpMyAdmin, as long as user (!root) has no privileges to create a new table.

// I have created CreateTableModel separate from Model (same for View and Controller), because otherwise 
// Model View and Controllers will be bigger without need- table in DB has to be creates only once in a lifetime.



// require_once '/GetPdo.php'; 

use App\Models\CreateTableModel;
use App\Views\CreateTableView;
use App\Controllers\CreateTableController;



// ok, seems we have all we need, so below we run the file: 

$view = new CreateTableView();
$model = new CreateTableModel($view);
$controller = new CreateTableController($view,$model);

