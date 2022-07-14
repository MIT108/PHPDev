<?php
require '../Database/connection.php';

class Backend{
    private $db;

    //constructor
    function __construct(){
        $this->db = connect_to_db();
    }

//=====================================================================================================//
//*******************************  Beginning of the other functions  **************************************/
//=====================================================================================================//

function imageDetail($imagePath){
    
    $as = getimagesize($imagePath);
    $word = explode('/', $imagePath);
    foreach($word as $img){
        $imagName=$img;
    }
    $imgName=explode('.', $imagName);
    $ass= Array(
        'extension'=>$imgName[1],
        'width'=>$as[0],
        'height'=>$as[1],
        'name'=>$imagName
    );

    return $ass;
}

//=====================================================================================================//
//***********************************End of other functions********************************************/
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

$b = new Backend();
$ass=$b->imageDetail("20201130_145445.jpg");
echo "<pre>";
print_r($ass);
echo "</pre>";

?>