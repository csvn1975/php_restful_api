<?php

namespace App\Controllers;

class HomeController extends \Core\BaseController
{

    public function __construct()
    {
    }

    function index()
    {
        include VIEW_FOLDER . 'home.php';    
    }
    
}
