<?php
$username = htmlentities($_REQUEST["username"]);
$password = htmlentities($_REQUEST["password"]);
$email = htmlentities($_REQUEST["email"]);
$fullname = htmlentities($_REQUEST["fullname"]);

if(empty($username) || empty($password) || empty($email) || empty($fullname)){
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required infomation!";
    echo json_encode($returnArray)."<br>";
    return;
}

$salt = openssl_random_pseudo_bytes(20);
$secured_password = sha1($password.$salt);

$file = parse_ini_file("../../../php.ini");
$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

require("secure/access.php");

$access = new access($host,$user,$pass,$name);
$access->connect();

$result = $access->register($username,$secured_password,$salt,$email,$fullname);
if ($result){
    $info = $access->selectUser($username);
    $returnArray["status"] = "200";
    $returnArray["message"] = "Successfully registered.";
    $returnArray["id"] = $info["id"];
    $returnArray["account"] = $info["account"];
    $returnArray["email"] = $info["email"];
    $returnArray["fullname"] = $info["fullname"];

    require("secure/email.php");
    $email = new email();
    $token = $email->generateToken(20);

    $access->saveToken("emailTokens",$info["id"],$token);

    $detail = array();
    $detail["subject"] = "Email confirmation on twitter";
    $detail["to"] = $info["email"];
    $detail["formName"] = "Kevin TEST formName";
    $detail["formEmail"] = "kevinabc99@gmail.com";
    $template = $email->confirmationTemplate();
    $template = str_replace("{token}",$token,$template);
    $detail["body"] = $template;

    $email->sendEmail($detail);

}else{
    $returnArray["status"] = "400";
    $returnArray["message"] = "Could not register with provided infomation";
}

// $access->deleteUser($username);

// $access->updateUser($username,$email,$fullname);

// $access->disconnect();

echo json_encode($returnArray)."<br>";

?>