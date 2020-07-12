<?php
    session_start();
    $dbhost = 'mylibrary.cn6fzragcwuf.us-west-1.rds.amazonaws.com';
    $dbuser = 'root';
    $dbpassword = 'Houston16';
    $dbname = 'FuelDatabase';
    $conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

   
    $from = $_POST['from'];
    $dc=date_create($from);
    $formated_date=date_format($dc,"Y-m-d");
    $gallons_requested  = $_POST['gallon_requested'];

    $sessions = $_SESSION['$userid'];
    $Suggested_price  = "";
    $Total_Amount_Due = "";
    $curremt_price_per_gallon = 1.5;
    $Texas_location_factor = 0.02;
    $Other_location_factor = 0.04;
    $Rate_History_Factor = 0.01; //not empty
    $No_History_Rate_Factor = 0.00; //empty
    $Gallons_Requested_Factor = 0.02;//more than 1000 gallons
    $Gallons_Requested_Factor2 = 0.03; 
    $Company_Profit_factor = 0.01;
    $SELECT = "SELECT * FROM Users WHERE UserId = ?";
    $stmt = $conn->prepare($SELECT);
        $stmt->bind_param("s", $sessions);
        $stmt->execute();
        $rnum = $stmt->num_rows;
        $result = $stmt->get_result();
        $user = $result->fetch_array(MYSQLI_ASSOC);
        $state = $user['State'];
        $Address1 =$user['Address1'];
        $stmt->close();



    if (isset($_POST['price'])) { //Get Quote

 //"Y-m-d" is the date format
 
        //Prepare statement
        
   /* function Margin_Calculation($email, $Gallons_Requested,$Delivery_Address,$Delivery_Date, $Suggested_Price, $Total_Amount_Due){

        $email = escape($email);
        $Gallons_Requested = escape($Gallons_Requested);
        $Delivery_Address = escape($Delivery_Address);
        $Delivery_Date= escape($Delivery_Date);
        $Suggested_Price = escape($Suggested_Price);
        $Total_Amount_Due = escape($Total_Amount_Due);*/


        if($gallons_requested > 1000){
            if($state == "TX"){
                //if history is not empty
                if($rnum > 0){
                    if($gallons_requested > 1000){
                        $Margin = $curremt_price_per_gallon * ($Texas_location_factor - $Rate_History_Factor + $Gallons_Requested_Factor + $Company_Profit_factor);
                    }
                    else{
                        $Margin = $curremt_price_per_gallon * ($Texas_location_factor - $Rate_History_Factor + $Gallons_Requested_Factor2 + $Company_Profit_factor);
                    }
                }
                else{
                    if($gallons_requested > 1000){
                        $Margin = $curremt_price_per_gallon * ($Texas_location_factor - $No_History_Rate_Factor+ $Gallons_Requested_Factor + $Company_Profit_factor);
                    }
                    else{
                        $Margin = $curremt_price_per_gallon * ($Texas_location_factor - $No_History_Rate_Factor+ $Gallons_Requested_Factor2 + $Company_Profit_factor);
                    }
                }
            }
            else{
                if($rnum > 0){
                    if($gallons_requested > 1000){
                        $Margin = $curremt_price_per_gallon * ($Other_location_factor - $Rate_History_Factor + $Gallons_Requested_Factor + $Company_Profit_factor);
                    }
                    else{
                        $Margin = $curremt_price_per_gallon * ($Other_location_factor - $Rate_History_Factor + $Gallons_Requested_Factor2 + $Company_Profit_factor);
                    }
                }
                else{
                    if($gallons_requested > 1000){
                        $Margin = $curremt_price_per_gallon * ($Other_location_factor - $No_History_Rate_Factor+ $Gallons_Requested_Factor + $Company_Profit_factor);
                    }
                    else{
                        $Margin = $curremt_price_per_gallon * ($Other_location_factor - $No_History_Rate_Factor+ $Gallons_Requested_Factor2 + $Company_Profit_factor);
                    }
                }
            
        }
    
    }

    $Margin = $curremt_price_per_gallon * ($Other_location_factor - $No_History_Rate_Factor+ $Gallons_Requested_Factor2 + $Company_Profit_factor);
        $Suggested_price = $curremt_price_per_gallon + $Margin;
        $Total_Amount_Due = $gallons_requested * $Suggested_price;

        
        $_SESSION['$Suggested_price']=$Suggested_price; //need to store it first, make it global, because after click get qoate button, it will refresh a page, variables change, it will become 0
        $_SESSION['$Total_Amount_Due']=$Total_Amount_Due;
        echo '<script>alert("Please check your cart ")</script>';
        }

            if (isset($_POST['submit'])) {
                $Suggested_price = $_SESSION['$Suggested_price']; //grobal
                $Total_Amount_Due = $_SESSION['$Total_Amount_Due']; //grobal
                if($conn === false){
                    die("ERROR: Could not connect. " . mysqli_connect_error());
                }
                if(empty($gallons_requested) || empty($formated_date)|| empty($Suggested_price || empty($Total_Amount_Due))){
                // insert data to mysql
                echo '<script>alert("Please fill missing information ")</script>';}
                else {
                $sql = "INSERT INTO Fuelquotes (UserId, Gallons, DeliveryAddress, DeliveryDate, SuggestPrice, TotalAmount) VALUES ('$sessions', '$gallons_requested','$Address1','$formated_date','$Suggested_price','$Total_Amount_Due')";
                //reset variable
                $gallons_requested = NULL; 
                $formated_date = NULL; 
                unset ($_SESSION["$Suggested_price"]);
                unset ($_SESSION["$Total_Amount_Due"]);
                if(mysqli_query($conn, $sql)){
                    
                    echo '<script>alert("You have make a order ")</script>';
                } else{
                    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                }

                // Close connection
                mysqli_close($conn);
            }

            
          }
?>

<!DOCTYPE html>
<html>
<head>
        <title> Admin Dashboard</title>
        <link rel = "stylesheet" href="style.css" type="text/css">
        <meta name="Resources" content="width=device-width, initial-scale=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
  </head>

  <header>
    <div class = "div">
      <img src = " " atl="logo" class = "logo">
      <h1> Welcome to FuelCal-App</h1>
      <nav>
    
            
      <div class = "Navigation">
            <a href ="index.html" ><tile><strong>Logout</strong></tile></a>
            <h1>Fuel Quotes Calculation</h1>
</div>
</nav>
     
    </div>
  </header>


  


  
<body>
<div id="mySidebar" class="sidebar">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
  <a  href="Home.php">Home/Fuel Quote</a>
<a  href="Fuel_Quote_History.php">Fuel Quote History</a>
              <a href="profile.php">Profile</a>
           
  
</div>

<div id="main">
  <button class="openbtn" onclick="openNav()">☰ Side Bar</button>  
</div>
</body>
<div id="Fuel" class>
            
          
           <form class="model-resource animate" action="Home.php" method="post"> 
            <div class = center>
              <br>
              <br>
              <br>
              <br>
              <br>
              <div>
                <label>Gallon requested</label>
                <input type="text" name="gallon_requested" value="<?php echo htmlspecialchars($_POST['gallon_requested'] ?? '', ENT_QUOTES); ?>">
               <?php // <input type= "text" name = "gallon_requested" class="input-field" placeholder=" input" required> ?>
               
                <p>
</p>
               <label>Delivery Address</label>
               <a style =" color:green;"> 
                <?php echo $Address1 ?>
                </a>
<p>
</p>
                <label>Date</label>
                
                <input type="date" id="datepicker" name='from' size='9' value="<?php echo htmlspecialchars($_POST['from'] ?? '', ENT_QUOTES); ?>">
                <?php // <input type="date" name="name" value="<?php echo $_POST['name'] ?? ''; ?>
    <p>
</p>

            


<!-- The Modal -->


</div>
            


<!--
      <button type="submit" name = "price"><strong>Get Quote</strong></button>

        <br>
        <button type="submit" name = "submit"><strong>Submit</strong></button>
        </form>
        
  
        
            <div id="FuelCal">
            
            <button class="getquote" onclick="getquote()">Get Quote</button> 
            <button class="submit" onclick="submit()">Submit</button> 
            </div>
            
        <button type="getquote" name = "getquote"><strong>Get Quote</strong></button>
        
      <button  type="submit" name = "submit"><strong>Submit</strong></button>
-->
        <br>
          

  </body>
</html>



<script>

// Get the modal
function toggleText() {
  var modal = document.getElementById("myModal");

// Get the button that opens the modal
  var btn = document.getElementById("myBtn");

  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];

  // When the user clicks the button, open the modal 
  btn.onclick = function() {
    modal.style.display = "block";
  }

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }

}
</script>

<div id="myModal" class="modal"style='display: none'>
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <form class="model-resource animate" action="Home.php" method="post"> 
    <label>Suggested Price</label>
        <a style='color:red;'><?php 
        
        if (isset($_POST['price'])) {

          echo $_SESSION['$Suggested_price'];
             }
        ?></a>
                <p>
</p>
                <label>Total Amount Due</label>
                <a style='color:red;'><?php 
                if (isset($_POST['price'])) {
      
                  echo $_SESSION['$Total_Amount_Due'];
                     }
                     ?></a>
              
                </form>

  </div>
    <div class="modal-footer">
    <button  type="submit" name = "submit"><strong>Submit</strong></button>
    </div>
  </div>

<button type="submit" name = "price"><strong>Get Quote</strong></button>

<button type='button' name = '1' id="myBtn" onclick="toggleText()">Show Cart</button>  


<script>


/*
function getquote() {
 
  document.getElementById("FuelCal").disable = false;
}
*/

function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}
</script>
<style>
/* The Modal (background) */
.modal {
  /*display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

/* The Close Button */
.modal .close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.modal-footer {
  margin:auto;
  padding: 20px;
  border: 1px solid #888;
  width:80%;
  background-color: #5cb85c;
  color: white;
}

h3{
    color: red;
}
      label{
          color:orange;
      }

    h1{
        color: red;
    }
      title{
  background-color: rgb(0,128,255);
  color: rgb(255, 255, 255);
  padding: 10px 10px;
  margin: 10px 0;
  border: none;
  cursor: pointer;
  width: 50%;
}




body
 {
     font-family: Arial, Helvetica, sans-serif;
     background-image: url(https://clipartart.com/images/clipart-plain-background-1.gif);  
     margin: 100;
    
    height: 80%;
    background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
 }

 
 button{
        background-color: orange;
      color: rgb(233, 19, 144);
      padding: 14px;
      margin: 8px 0;
      border: none;
         border-radius: 50px;
      border: none;
      cursor: pointer;
      width: 10%;
    }
    
    .containerAdmin{
        float: left;
     
    }
    .center{
        text-align: center;
        
        }
   
.Navigation
{
  background-color: #333;
    overflow: hidden;
}
.Navigation a {
  float: right;
  color: #f2f2f2;
  text-align: center;
  padding: 16px 16px;
  text-decoration: none;
  font-size: 17px;
}
.Navigation a:hover {
  background-color: #ddd;
  color: black;
}
.Navigation a.active {
  background-color: #4CAF50;
  color: white;
} 
        

    
.sidebar {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #111;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
}

.sidebar a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 25px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

.sidebar a:hover {
  color: #f1f1f1;
}

.sidebar .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

.openbtn {
  font-size: 20px;
  cursor: pointer;
  background-color: #111;
  color: white;
  padding: 10px 15px;
  border: none;
}

.openbtn:hover {
  background-color: #444;
}

#main {
  transition: margin-left .5s;
  padding: 16px;
}

/* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
@media screen and (max-height: 450px) {
  .sidebar {padding-top: 15px;}
  .sidebar a {font-size: 18px;}
}

  </style>



