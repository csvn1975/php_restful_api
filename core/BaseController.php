<?php
namespace Core;

class BaseController {
    
    
    public function redirect($route) {
        header('location:' . $route);
    }
    
    public function goBack() {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

   
    /**
     * @ $path: view-file with folder from folder views
     * $data: input-parameter in array
     */

    protected function loadView($path, $data = []) {
        
        /**
         * convert array in variable-name
         * view-file in format: folder.name 
         */ 
        // foreach ( $data as $key => $value ) {
        //     $$key = $value;
        // }

        extract($data);
        
        require VIEW_FOLDER . str_replace("." , "/", $path) . ".php";
    }

    protected function loadModel($path) {
        require_once MODEL_FOLDER . str_replace(".", "/", $path) . ".php";
    }

}

?>