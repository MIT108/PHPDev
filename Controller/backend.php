<?php
require '../Database/connection.php';

class Backend{
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

    //function to get all data
    function findAll($tablename){
        $data = $this->db->query("select * from ".$tablename."");
        $arr = Array();
        foreach ($data as $row){
            array_push($arr,$row);
        }

        return $arr;
    }

    
}

$b = new Backend();
$b->findAll('users');

?>