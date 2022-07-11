<?php
require '../Database/connection.php';

class Backend{
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

//=====================================================================================================//
//*******************************  Beginning of insert functions  **************************************/
//=====================================================================================================//

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
        for($i=0;$i<count($columns);$i++){
            $data = $this->insertSingleData($tableName,$columns[$i],$values[$i]);
            if($data['success']){
                continue;
            }else{
                return $this->returnExecQueryMessage("Unexcepected server error check data types and content",false);
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



//===================================================================================================================//
//**************************************** End of the insert functions  *********************************************//
//===================================================================================================================//

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
$b->getTableColumns('users');

?>