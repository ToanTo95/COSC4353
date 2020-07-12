<?php
session_start();

    $userid = $_POST['userid'];
    $password = $_POST['password'];
    $_SESSION['$userid']=$userid;




$dbhost = 'mylibrary.cn6fzragcwuf.us-west-1.rds.amazonaws.com';
$dbuser = 'root';
$dbpassword = 'Houston16';
$dbname = 'FuelDatabase';
$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

function redirect($location){
    return header("Location: {$location}");
}
function fetch_array($result){

	return mysqli_fetch_array($result);
}


if(mysqli_connect_error())
    die("Connection Failed".mysqli_connect_error());
else
    echo "connected";




$query = "SELECT * FROM Users WHERE UserId = $userid LIMIT 1";


$result = mysqli_query($con,$query);
$rnum = $result->num_rows;
if($rnum == 0){
    
    $_SESSION['message'] = "Make sure you are using a valid userid or Register if you haven't done so";
    redirect("Login_error.php");
    
}
else{
    $result_array = fetch_array($result);
    if($userid === $result_array['UserId'] && password_verify($password, $result_array['Password']))
    {   
     
       
        $id = $result_array['UserId'];
        
         
        // This is how we'll know the user is logged in
        $_SESSION['logged_in'] = true;
        
        if($id == $userid)
        {
            redirect("Home.php");
        }
      
        
    }
    else{
       
        $_SESSION['message'] = "You have entered an incorrect userid or Password. Please Try Again";
        redirect("Login_error.php");
       
    }
    
}



        

?>
