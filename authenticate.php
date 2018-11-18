<?php
// Initialize the session
session_start();
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.html");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$UID = $password = "";
$UID_err = $password_err = "";

if(isset($_GET['UID'])){ $UID = $_GET['UID']; }

if(isset($_GET['password'])){ $password = $_GET['password']; }


 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "GET"){
 
    // Check if UID is empty
    if(empty(trim($UID))){
        $UID_err = "Please enter UID.";
    } else{
        $UID = trim($_GET["UID"]);
    }
    
    // Check if password is empty
    if(empty(trim($password))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_GET["password"]);
    }
    
    // Validate credentials
    if(empty($UID_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, UID, password FROM users WHERE UID = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_UID);
            
            // Set parameters
            $param_UID = $UID;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if UID exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $UID, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["UID"] = $UID;                            
                            
                            // Redirect user to welcome page
                            header("location: index.html");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if UID doesn't exist
                    $UID_err = "No account found with that UID.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Tangerine:700">

<style>

h1{
    vertical-align: middle;
    text-align :center;
    color: #ffffff;
    font-family: 'Tangerine';
    font-size:  80px;
   
}

h3{
    vertical-align: middle;
    text-align :center;
    color: #ffffff;
    font-family: 'Tangerine';
    font-size:  50px;
   
}

body, html {
    
    margin: 0;
    background-color: #00000c;
}


/* Full-width input fields */
input[type=text], input[type=password] {
    width: 100%;
    padding: 15px;
    margin: 5px 0 15px 0;
    border: none;
    background: #f1f1f1;
}

input[type=text]:focus, input[type=password]:focus {
    background-color: #ddd;
    outline: none;
}

/* Set a style for the submit button */
.btn {
    background-color: #1d1e22;
    color: white;
    padding: 16px 16px;
    border: none;
    cursor: pointer;
    width: 100%;
    opacity: 0.7;
}

.btn:hover {
    opacity: 1;
}

.centered {
    position: absolute;
    top: 60%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 16px;
    font-family: sans-serif;
}
</style>
</head>
<body>
    <div>
        <div>
             <h1> GRADE IT </h1>
        </div>
        <div>
            <h3> KTU Activity Point Management System </h3>
        </div>
             
    </div>


  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"class="centered" method="get">

    <input type="text" name="UID" id="UID" class="form-control" value="<?php echo $UID; ?>">
    <span class="help-block"><?php echo $UID_err; ?></span>
    <br>
    
    <input type="password" name="password" id="password" class="form-control">
    <span class="help-block"><?php echo $password_err; ?></span>
    <br>
    <br>
    
    <button type="submit" class="btn">Login</button>
  </form>

</div>
</div>






</body>
</html>