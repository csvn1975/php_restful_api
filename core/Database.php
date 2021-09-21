<?php

namespace Core;

class Database {  

    static $connectCount = 0;
    protected $dbcon;

    public function __construct()
    {
            $this->dbcon = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME );
            // Check connection
            if ($this->dbcon -> connect_errno) {
                echo "Failed to connect to MySQL: " . $this->dbcon->connect_error;
                exit();
            }
            
            $this->dbcon->set_charset("utf8");
    }
    
    public function __destruct()
    {
        $this->dbcon->close();
    } 

}

?>