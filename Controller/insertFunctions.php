<?php
require '../Database/connection.php';

class Backend{
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

    function findAll($tableName, $key=null, $value= null,$order='asc',$limit=0){
        try{
            $data = [];
            if(is_null($value)){
                if($limit>0){
                    $data = $this->db->query("select * from ".$tableName." order by id ".$order." limit ".$limit." ");
                }else{
                    $data = $this->db->query("select * from ".$tableName." order by id ".$order."");
                } 
            }else{
                if($limit>0){
                    $data = $this->db->query("select * from ".$tableName." where ".$key."=".$value." order by id ".$order." limit ".$limit." ");
                }else{
                    $data = $this->db->query("select * from ".$tableName." where ".$key."=".$value." order by id ".$order."");
                } 
            }
            $arr = Array();
            foreach($data as $row){
                array_push($arr,$row);
            }
    
            return $arr;
        }catch(Exception $e){
            return $this->returnExecQueryMessage("Unexpected server error could not fine the data", false);
        }
       
    }

     //function to update the data in a given column
     function updateColumnData($tableName,$columnName,$columnData,$key,$value){
        try{
            $this->db->exec("update ".$tableName." set ".$columnName."= '$columnData' where ".$key."= '$value'");

            return $this->returnExecQueryMessage("Updated $columnName sucessfully",false);
        }catch(Exception $e){
            return $this->returnExecQueryMessage('Unexpected server error update falied',false);
        }
    }


//function to insert data single data
function insertSingleData($tableName,$columnName,$value){
        try{
            $this->db->exec("insert into ".$tableName." (".$columnName.") values ('$value') ");
            return $this->returnExecQueryMessage("Inserted $columnName in table $tableName",true);
        }catch(Exception $e){
            return $this->returnExecQueryMessage("Unexcepected server error couldn't insert data",false);
        }
}

//function to insert multiple data in a given table
function insertMultipleData($tableName,$columns,$values){
    if(count($columns)==count($values)){
        $allData = $this->findAll($tableName,null,null,'desc',1);
        foreach($allData as $row){
            $lastId = (int)$row[$this->getPrimaryKey($tableName)]+1;
            break;
        }
        $res = $this->insertSingleData($tableName,$columns[0],$values[0]);
        if($res['success']){
            for ($i=1; $i < count($values); $i++) { 
                $response = $this->updateColumnData($tableName,$columns[$i],$values[$i],$this->getPrimaryKey($tableName),$lastId);
                if($response['success'])
                    continue;
                else
                    return $this->returnExecQueryMessage("Unexcepected server error",false);
            }
          
        }

        return $this->returnExecQueryMessage("Inserted row into $tableName successfully",true);
    }else{
        return $this->returnExecQueryMessage("Columns and data values are not of same length",false);
    }
}

//function to insert row data
function insertRowData($tableName,$values){
    $columns = $this->getTableColumns($tableName);
    $this->insertMultipleData($tableName,$columns,$values);
}

//function to get columns of the table
function getTableColumns($tableName){
    try{
        $fields = Array();
        $data = $this->db->query("desc ".$tableName." ");
        foreach ($data as $key => $value) {
            array_push($fields,$value[0]);
        }
        return $fields;
    }catch(Exception $e){
        return $this->returnExecQueryMessage("No column found for table $tableName",false); 
    }
}


//function to get primary key of a the table
function getPrimaryKey($tableName){
    try{
        $primaryKey = "";
        $data = $this->db->query("desc ".$tableName." ");
        foreach ($data as $key => $value) {
            if($value['Key']=="PRI"){
                $primaryKey = $value[0];
                break;
            }else{ continue; }
        }
        return $primaryKey;
    }catch(Exception $e){
        return $this->returnExecQueryMessage("Oops Primary key of $tableName not found",false); 
    }
}

    //function to personalise messages
    function returnExecQueryMessage($message,$isSuccess){
        if($isSuccess){
            echo $message;
            return ['message'=> $message.' !!!','success'=>true];
        }else{
            echo $message;
            return ['message'=> $message.' !!!','success'=>false];
        }
    }
}

$b = new Backend();
$b->insertMultipleData('users',['name','age'],["test","36"]);

?>