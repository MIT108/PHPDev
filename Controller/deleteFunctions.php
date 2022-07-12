<?php
require '../Database/connection.php';

class Delete{
    
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

    function returnExecQueryMessage($message, $isSuccess){
        if ($isSuccess) {
            return [
                "message" => $message,
                "success" => true
            ];
        }else {
            return [
                "message" => $message,
                "success" => false
            ];
        }
    }


    //===================================================================================================================//
    //**************************************** Beginning of the delete functions  ***************************************//
    //===================================================================================================================//

    //function to delete a database
    function deleteDatabase($databaseName){
        try {
            $data = $this->db->query("drop database ".$databaseName."");
            return $this->returnExecQueryMessage("Database $databaseName deleted Successful", true);
        } catch (\Throwable $th) {
            return $this->returnExecQueryMessage("Database $databaseName does Not Exist", false);
            //throw $th;
        }
    }

    //delete a table or tables
    function deleteTable($tableArray){
        try {
            foreach ($tableArray as $table) {
                $data = $this->db->query("drop table ".$table."");
            }
            if (count($tableArray) > 1) {
                return $this->returnExecQueryMessage("Tables deleted successfully", true);
            }else{
                return $this->returnExecQueryMessage("$tableArray[0] table deleted successfully", true);
            }
        } catch (\Throwable $th) {
            return $this->returnExecQueryMessage("Unexpected server Error", false);
        }
    }


    //delete column or column of a particular table
    function deleteTableColumn($table, $columnArray){

        try {
            foreach ($columnArray as $column) {
                $data = $this->db->query("alter table ".$table." drop column ".$column."");
            }
            
            if (count($columnArray) > 1) {
                return $this->returnExecQueryMessage("Columns from table $table deleted successfully", true);
            }else{
                return $this->returnExecQueryMessage("Column $columnArray[0] from table $table deleted successfully", true);
            }
        } catch (\Throwable $th) {
            return $this->returnExecQueryMessage("Unexpected server Error", false);
        }
    }

    
    
    //delete row or rows given the table name and the column name
    function deleteTableRow($table, $key, $valueArray){
        try {
            foreach ($valueArray as $value) {
                $data = $this->db->query("delete from ".$table." where ".$key." = '".$value."'");
            }
            
            if (count($valueArray) > 1) {
                return $this->returnExecQueryMessage("Rows deleted from $table successfully", true);
            }else{
                return $this->returnExecQueryMessage("Row with $key $valueArray[0] deleted from $table successfully", true);
            }
        } catch (\Throwable $th) {
            return $this->returnExecQueryMessage("Unexpected server Error", false);
        }
    }

    
    //===================================================================================================================//
    //**************************************** End of the delete functions  *********************************************//
    //===================================================================================================================//
}

?>