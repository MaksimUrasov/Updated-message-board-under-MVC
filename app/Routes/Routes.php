<?


use App\Controllers\CreateTableController;
use App\Controllers\Controller;


if(isset($_REQUEST['tname'])){    //http://message.vienasmedis.lt/?tname // nepatogu, bet laikinai palikau taip

    $controller = new CreateTableController();
 
}else{
    $controller = new Controller();
}