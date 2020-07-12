<?php
//ECHO "ONE";
session_start();
    $userid = $_POST['userid'];
    $password = $_POST['password'];
    $confirm_password = $_POST['password2'];
    $Fullname = $_POST['FullName'];
    $Address1 = $_POST['Address1'];
    $Address2 = $_POST['Address2'];
    $City = $_POST['City'];
    $State = $_POST['State'];
    $zip = $_POST['zip'];
 
    //echo $position;
    //ECHO "TWO";
  
    
    $host = "mylibrary.cn6fzragcwuf.us-west-1.rds.amazonaws.com";
    $dbusername = "root";
    $dbpassword = "Houston16";
    $dbname = "FuelDatabase";
//echo"4";
    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname); 
    //echo "5";
    if (mysqli_connect_error())
    {
        echo "It failed";
        die('Connection Failed: '.mysqli_connect_error());
    }
    else{
        echo "connect";
        if($password != $confirm_password){
            $_SESSION['message'] = "Password did not match, Please try again";
            header("location: Register_error.php");
            exit();
        }
        
       
            
        $SELECT = "SELECT ID FROM Users WHERE UserId = ? LIMIT 1";
        $INSERT = "INSERT INTO Users (UserId, Password, FullName, Address1, Address2, City, State, ZipCode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        //ECHO"CHECK2";        
    
   
        

        //Prepare statement
        $stmt = $conn->prepare($SELECT);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->bind_result($userid);
        $stmt->store_result();
        $rnum = $stmt->num_rows;

        if($rnum == 0){
            $stmt->close();
            $stmt = $conn->prepare($INSERT);
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            //$hashPassword2 = password_hash($confirm_password, PASSWORD_DEFAULT);

            $stmt->bind_param("ssssssss", $userid, $hashPassword,$Fullname,$Address1,$Address2,$City,$State,$zip );
            $stmt->execute();
            $stmt->close();

            $_SESSION['message'] = "You have Successfully Registered, Please Login";
            header("location:register_success.php");
           
        }
        else{

            $_SESSION['message'] = "An Account is Already Associated with this userid, Please Login";
            header("location:Register_error.php");


            
        }
        



    }
    $conn->close();
   
?>