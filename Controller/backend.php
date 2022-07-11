<?php
require '../Database/connection.php';

class Backend{
    private $db;

    function __construct(){
        $this->db = connect_to_db();
    }

    //function to greet user\
    function hello(){
        echo "\nHello Users !!!\n";
    }
}

$b = new Backend();
$b->hello();

?>