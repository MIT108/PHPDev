<?php
require '../Database/connection.php';

class Backend{
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

//=====================================================================================================//
//*******************************  Beginning of update functions  **************************************/
//=====================================================================================================//

    //function to update a table name
    function updateTableName($oldName,$newName){
        try{
            $this->db->exec("RENAME TABLE ".$oldName." TO ".$newName." ");
            return  $this->returnExecQueryMessage('Table '.$oldName.' updated',true);
        }catch(Exception $e){
            return $this->returnExecQueryMessage("Unexpected server error couldn't upadte table",false);
        }
    }

    //function to update  column name
    function updateColumnName($tableName,$oldName,$newName,$dataType){
        $oldDataType = "";
        $isDataTypeCompatiple = false;
        $con = $this->db->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME ='$tableName' ");
        foreach ($con as $key) {
            if($key['COLUMN_NAME']==$oldName){
                $oldDataType = $key['DATA_TYPE'];

                //check if data type is compatible with old field name
                if(substr($oldDataType,0,3)===substr($dataType,0,3)){
                    $isDataTypeCompatiple = true;
                }
                break;
            }
        }

        if($oldDataType!=""){
            if($isDataTypeCompatiple){
                try{
                    $combineNewNameWithDataType = $newName." ".$dataType;
                    $this->db->exec("alter table ".$tableName." change ".$oldName." ".$combineNewNameWithDataType." ");
                    
                    return $this->returnExecQueryMessage('Column updated successfully',true);

                }catch(Exception $e){

                    return $this->returnExecQueryMessage('Unexpected server error',false);
                }
            }else{

                return $this->returnExecQueryMessage('Incompatible datatype with old field name',false);
            }
        }else{
            return $this->returnExecQueryMessage("This column doesn't exit in table ".$tableName,false);
        }
    }

    //function to update the data in a given column
    function updateColumnData($tableName,$columnName,$columnData,$key,$value){
        try{
            $this->db->exec("update ".$tableName." set ".$columnName."= '$columnData' where ".$key."= '$value'");

            return $this->returnExecQueryMessage('Updated '.$columnName.' sucessfully',false);
        }catch(Exception $e){
            return $this->returnExecQueryMessage('Unexpected server error update falied',false);
        }
    }

    //function to add a new column
    function addColumn($tableName,$columnName,$dataType,$default=null){
        try{
            if(is_null($default)){
                $this->db->exec("alter table ".$tableName." add column ".$columnName." ".$dataType." ");
            }else{
                $this->db->exec("alter table ".$tableName." add column ".$columnName." ".$dataType." default '$default' ");
            }

            return $this->returnExecQueryMessage('Added column '.$columnName.' to '.$tableName ,true);
        }catch(Exception $e){
            return $this->returnExecQueryMessage('Unexpected server error failed to create column '.$columnName.' to '.$tableName,false);
        }
    }

//=====================================================================================================//
//***********************************End of update functions********************************************/
//=====================================================================================================//


    //function to personalise messages
    function returnExecQueryMessage($message,$isSuccess){
        if($isSuccess){
            return ['message'=> $message.' !!!','success'=>true];
        }else{
            return ['message'=> $message.' !!!','success'=>false];
        }
    }
}

?>