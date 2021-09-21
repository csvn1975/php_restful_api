<?php
namespace Core;

class App{

    protected $controller = "";
    protected $action="all";
    protected $params=[];

    function __construct(){
        $arr = $this->UrlProcess();
        #api 
        if (isset($arr[0]) && strtolower($arr[0]) === 'api') {
            array_shift($arr);

            # controller not exist 
            if ( !isset($arr[0])) {
                die('NOT FOUND');
            } 

            /**
            *  @ url : api/user/... =>  file  UserController.php
            *  Folder api, 
            *  user => controller UserController.php
            */
           
            $controllerPath = CONTROLLER_API_FOLDER ; 

            $controllerFile = $controllerPath . ucfirst($arr[0]) . "Controller.php";        
            
            // check file if it not exist => break 
            if( !file_exists($controllerFile) ){
                die("That controller $controllerFile is not exists");
            }

            $controllerName = '\\App\\Controllers\\Apis\\' . ucfirst($arr[0]) . 'Controller';
            $this->controller = new $controllerName;
            array_shift($arr);
            
            // Action ---------------------------
            // if ( isset($arr[0]) ) {            
            //     $this->action = $arr[0];
            //     if ( !method_exists( $this->controller , $this->action) ) {
            //         die("That method $arr[0] does not exist in the controller \"$controllerName\"");
            //     }
            //     array_shift($arr);
            // } 

            $this->action = 'index';
            $this->params = $arr ? array_values($arr) : [];
            //call controller/method
            call_user_func_array([ $this -> controller, $this -> action], $this -> params );

        } 
        else {    
            $controllerPath = CONTROLLER_FOLDER ;
            $controllerFile = $controllerPath . "HomeController.php"; 

            if( !file_exists($controllerFile) ){
                die("That controller $controllerFile is not exists");
            }

            $controllerName = '\\App\\Controllers\\HomeController';
            $this->controller = new $controllerName();
            $this->action = 'index';

            $this->params = $arr ? array_values($arr) : [];
            //call controller/method
            call_user_func_array([ $this -> controller, $this -> action], $this -> params );
        }
       
    }


    /**
     * @ return Array
     */
    function UrlProcess(){

        if( isset($_GET["url"]) ){ 
            $url = filter_var(trim($_GET["url"], "/"));
        } 

        else {
            $url = preg_replace( "/^\//", '', $_SERVER['REQUEST_URI']);
        }

        if ($url)
           return explode("/", $url);
    }

}
