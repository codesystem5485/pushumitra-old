<?php
namespace App\Http\Controllers;
set_time_limit(0);
use Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class TestController extends Controller{

    public function create_user(){
       
    }
    public function process(){ 
        
        echo "process done";      
    }
}

?>