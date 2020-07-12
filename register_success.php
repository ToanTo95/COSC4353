<?php
/* Displays all error messages */
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Registration Success</title>
  <?php include 'css/css.html'; ?>
</head>
<body>
<div class="form">
    <h1>Registration Successful</h1>
    <p>
    <?php 
    if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ): 
        echo $_SESSION['message'];    
    else:
        header( "location: index.html" );
    endif;
    ?>
    </p>     
    <a href="index.html"><button class="button button-block"/>Go To Login</button></a>
</div>
</body>
</html>
