<?php
require '../Database/connection.php';

class Backend{
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

    //function to get all data or get a rows
    //the id is optional here
    function findAll($tableName, $key=null, $value= null){
        if(is_null($value)){
            $data = $this->db->query("select * from ".$tableName."");
        }else{
            $data = $this->db->query("select * from ".$tableName." where ".$key."=".$value."");
        }
        
        $arr = Array();
        foreach ($data as $row){
            array_push($arr,$row);
        }

        return $arr;
    }

    //function to select one or more column in the database
    function findOne($tableName, $columnName, $key=null, $value= null){
        $set = Array(); 
        for($i=0; $i<count($columnName); $i++){
            if(is_null($value)){
                $data = $this->db->query("select ".$columnName[$i]." from ".$tableName."");    
            }else{
                $data = $this->db->query("select ".$columnName[$i]." from ".$tableName." where ".$key."=".$value."");
            }
            
            foreach ($data as $row){
                array_push($set,$row);
            }
        }
        
        return $set;
    }

    //function to verify if a table exit
    function checkTables($tableName){
        $data=$this->db->query("show tables");
        $arr=Array();
        $i=0;
        $t=false;

        foreach($data as $row){
            array_push($arr,$row);
            $i++;
        }

        for($j=0; $j<$i; $j++){
            if($tableName==$arr[$j][0]){
                $t=true;
            }
        }
        
        return $t;
    }
}

$b = new Backend();
?>