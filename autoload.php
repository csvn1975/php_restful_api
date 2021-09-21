<?php

spl_autoload_register(function($classname){

    $classname = str_replace("\\", DIRECTORY_SEPARATOR , $classname);   
    $filename = __DIR__ . DIRECTORY_SEPARATOR . $classname . ".php";

    if (file_exists($filename)) {
        include_once $filename;
    }
    else {
        die("$filename is not exists");
    }
});
