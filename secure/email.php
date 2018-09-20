<?php
class email{
    function generateToken($length){
        $characters = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";

        $charactersLength = strlen($characters);

        $token = '';

        for ($i=0; $i < $length; $i++) { 
            $token .= $characters[rand(0,$charactersLength - 1)];
        }

        return $token;
    }

    function confirmationTemplate(){
        $file = fopen("templates/confirmationTemplate.html","r") or die("Unable to open file");

        $template = fread($file, filesize("templates/confirmationTemplate.html"));

        fclose($file);
        return $template;
    }

    function sendEmail($detail){
        $subject = $detail["subject"];
        $to = $detail["to"];
        $formName = $detail["formName"];
        $formEmail = $detail["formEmail"];
        $body = $detail["body"];
        $header = "MIME-Version: 1.0"."\r\n";
        $header .= "Content-type: text/html;content=UTF-8"."\r\n";
        $header .= "From: ".$formName." <".$formEmail.">"." \r\n";

        mail($to,$subject,$body,$header);
        
    }

}


?>