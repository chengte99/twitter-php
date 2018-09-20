<?php
class access{
    var $host = null;
    var $user = null;
    var $pass = null;
    var $name = null;
    var $conn = null;

    function __construct($dbhost,$dbuser,$dbpass,$dbname){
        $this->host = $dbhost;
        $this->user = $dbuser;
        $this->pass = $dbpass;
        $this->name = $dbname;
    }

    function connect(){
        $this->conn = mysqli_connect($this->conn,$this->user,$this->pass,$this->name);

        if (mysqli_connect_errno()){
            echo "Connected Failed";
        }

        $this->conn->set_charset("utf8");
    }

    function disconnect(){
        if ($this->conn != null){
            mysqli_close($this->conn);
        }
    }

    function register($username,$password,$salt,$email,$fullname){
        $sql = "INSERT INTO users (account,password,salt,email,fullname) VALUES (?,?,?,?,?)";

        $stmt = mysqli_prepare($this->conn,$sql);

        if(!$stmt){
            throw new Exception($stmt->error);
        }

        $stmt->bind_param("sssss",$username,$password,$salt,$email,$fullname);
        $result = $stmt->execute();

        // $result = mysqli_query($this->conn, $sql);
        if ($result){
            $last_id = mysqli_insert_id($this->conn);
            echo "New record created successfully. Last inserted ID is : $last_id"."<br>";
        }else{
            echo "Insert Data Error"."<br>";
        }

        return $result;

        // return $result;
    }

    function selectUser($username){
        $sql = "SELECT * FROM users WHERE account='$username'";

        $result = mysqli_query($this->conn,$sql);
        if($result != null && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
            if(!empty($row)){

                return $row;
            }
        }
    }

    function saveToken($table,$id,$token){
        $sql = "INSERT INTO $table SET id=?, token=?";

        $stmt = mysqli_prepare($this->conn,$sql);

        if(!$stmt){
            throw new Exception($stmt->error);
        }

        $stmt->bind_param("is",$id,$token);
        $result = $stmt->execute();

        return $result;
    }

    function getUserID($table,$token){
        $returnArray = array();

        // $sql = "SELECT id FROM $table WHERE token=?";

        // $stmt = mysqli_prepare($this->conn,$sql);
        // if(!$stmt){
        //     throw new Exception($stmt->error);
        // }
        // $stmt->bind_param("s",$token);
        // $stmt->execute();
        // $result = $stmt->get_result();

        // if($result != null && mysqli_num_rows($result) > 0){
        //     $row = mysqli_fetch_array($result,MYSQLI_ASSOC);

        //     if(!empty($row)){
        //         $returnArray = $row;
        //     }
        // }

        $sql = "SELECT id FROM $table WHERE token='".$token."'";
        $result = mysqli_query($this->conn,$sql);

        if($result != null && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
            if(!empty($row)){
                
                $returnArray = $row;
            }
        }

        return $returnArray;
    }

    function updateEmailConfirmation($status,$id){
        $sql = "UPDATE users SET emailConfirmed=? WHERE id=?";

        $stmt = mysqli_prepare($this->conn,$sql);
        if(!$stmt){
            throw new Exception($stmt->error);
        }

        $stmt->bind_param("ii",$status,$id);
        $result = $stmt->execute();

        return $result;
    }

    function deleteToken($table,$token){
        $sql = "DELETE FROM $table WHERE token=?";

        $stmt = mysqli_prepare($this->conn,$sql);
        if(!$stmt){
            throw new Exception($stmt->error);
        }

        $stmt->bind_param("s",$token);
        $result = $stmt->execute();

        return $result;
    }
    
    function deleteUser($username){
        $sql = "DELETE FROM users WHERE account='$username'";

        $result = mysqli_query($this->conn,$sql);

        if($result){
            echo "Delete account($username) success!";
        }
    }

    function updateUser($username,$email,$fullname){
        $sql = "UPDATE users SET email='$email', fullname='$fullname' WHERE account='$username'";

        $result = mysqli_query($this->conn,$sql);
        if($result){
            echo "Update account($username) success!";
        }
    }
}
?>