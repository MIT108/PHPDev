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

    //function to select one or more column in the database
    function findOne($tableName, $columnName, $key=null, $value= null){
        $set = Array(); 
        try{
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
        }catch(Exception $e){
            return $this->returnExecQueryMessage("Unexpected server error could not fine the data", false);
        }
        
    }

    //function to verify if a table exit
    function checkTables($tableName){
        try{
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
        }catch(Exception $e){
            return $this->returnExecQueryMessage("An Error occured doing verification", false);
        }
       
    }

    //function to verify if an id in a table exist
    function isExistId($tableName,$key,$value){
        try{

            $data = $this->db->query("select count(".$key.") from ".$tableName." where ".$key."= '$value' as count_value");
            foreach ($data as $key) {
                if($key['count_value'] > 0){
                    return $this->returnExecQueryMessage("$value in $key found sucessfully", true);
                }
            }

        }catch(Exception $e){
            return $this->returnExecQueryMessage("Oops the $key ".$value." doesn't exist in the $tableName table", false);
        }
    }

    //---------------- Functions to insert data ----------------------//
    
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


    //----------------- Update functions ----------------------------//
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

            return $this->returnExecQueryMessage("Updated $columnName sucessfully",false);
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

    //----------- Delete functions ------------------------------------//
    //function to delete a database
    function deleteDatabase($databaseName){
        try {
            $data = $this->db->query("drop database ".$databaseName."");
            return $this->returnExecQueryMessage("Database $databaseName deleted Successful", true);
        } catch (\Throwable $th) {
            return $this->returnExecQueryMessage("Database $databaseName does Not Exist", false);
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

    //------------ Authentication System -------------------//
    //function to encrypt a user password
    function encryptPassword($password){
        if(strlen($password)>0){
            return password_hash($password,PASSWORD_BCRYPT);
        }
        return null;
    }

    //function to check if password equals it's hash
    function decryptPassword($password,$hash){
        if(password_verify($password,$hash))
            return true;
        else
            return false;
    }

    //function to login a user
    function loginUser($tableName,$column1,$value1,$column2="password",$value2){
        $allData = $this->findAll($tableName);
        foreach($allData as $row){
            if($row[$column1]==$value1 && $this->decryptPassword($value2,$row[$column2])){
                $array = [];
                $row["token"]=$this->generateRandomToken();
                array_push($array,$row);
                array_push($array,$this->returnExecQueryMessage('User Login successfull !!!',true));

                break;
            }
            continue;
        }
    }

    //function to personalise messages
    function returnExecQueryMessage($message,$isSuccess){
        if($isSuccess){
            return ['message'=> $message.' !!!','success'=>true];
        }else{
            return ['message'=> $message.' !!!','success'=>false];
        }
    }

    //function to return a random token 
    function generateRandomToken($length = 16) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        do{
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        }while($this->checkValidityOfToken($randomString));

        return hash('sha256', $randomString);
    }

    //function to check if token already exist with boolean return 
    function checkValidityOfToken($token){
        $allToken = $this->findAll('token');
        foreach($allToken as $row){
            if($row['token']==$token)
                return true;
            else
                continue; 
        }
        
        return false;
    }

    //function to check if token exist in db with message return type
    function isExistOfToken($token){
        $allToken = $this->findAll('token');
        foreach($allToken as $row){
            if($row['token']===$token){
                return $this->returnExecQueryMessage('Access granted',true);
                exit();
            }  
            else{
                return $this->returnExecQueryMessage('Not authenticated user',false);
                exit();
            }
        }
    }
    
}

$b = new Backend();
?>