<?php
require '../Database/connection.php';

class Delete{
    
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

    //function to insert a new user

    //function to return a random token 
    function generateRandomToken($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return hash('sha256', $randomString);
    }

}

?>