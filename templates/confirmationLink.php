<?php

//step 1, get token on this page
$token = htmlentities($_GET["token"]);

if(empty($token)){
    echo "Missing required infomation";
}

//step2, make connection DB
$file = parse_ini_file("../../../../php.ini");
$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

require("../secure/access.php");
$access = new access($host,$user,$pass,$name);
$access->connect();

//step3, get user id via token
$idArray = $access->getUserID("emailTokens",$token);
if(empty($idArray)){
    echo "User with this token is not found";
    return;
}

//step4, change emailConfirmation via user id
$result = $access->updateEmailConfirmation(1,$idArray["id"]);
if($result){
    echo "Thank you! Your email is now confirmed";

    //step5, delete this token on table 'emailTokens'
    $access->deleteToken("emailTokens",$token);
}

//step6, close connection
$access->disconnect();

?>