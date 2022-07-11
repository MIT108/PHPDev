<?php

function connect_to_db(){
    try{
        $pdo = new PDO("mysql:host=localhost;dbname=phpdev","root","");
        return $pdo;

    }catch(PDOException $e){
        die("Database Error: ".$e->getMessage());
    }
}

// $pdo = new PDO("mysql:host=localhost;dbname=bouy-task","root","");
// $db = $pdo;

// $con = $db->query("select * from ")

?>