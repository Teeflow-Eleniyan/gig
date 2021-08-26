<?php 

session_start();
 
// Include config file
require_once "database/db_login.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: index.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!link rel="stylesheet" href="../Expense.payroll/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login-page</title>
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        body{
            background-image: url(assets/img/earth2.jpeg);
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .body{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form{
            margin-right: auto;
            position: absolute;
            top: -50%;
            overflow: hidden;
            background-color: red;
            width: 350px;
            height: 40vh;
            border-radius: 5px;
            box-shadow: 0px 9px 15px rgba(73, 132, 187,0.6);
            animation: animate 3s ease forwards;
        }
        h1{
            letter-spacing: 2px;
            font-weight: 600;
            font-size: 30px;
            text-align: center;
            padding: 2px;
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            color: white;
            margin-bottom: 18px;
            margin-top: 5px;
        }
        div.sub1,.sub2{
            position: relative;
            height: 20px;
            margin-bottom: 20px;
        }
        label{
            opacity: .1;
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            font-weight: 400;
            color: rgb(80, 64, 64);
            transition: 1s;
        }
        input{
            outline: none;
            width: 100%;
            transition: 1s;
        }
        button{
            cursor: pointer;
            position: relative;
            top: 2px;
            margin-left: 80%;
            padding: 8px;
            font-weight: 600;
            font-family: Arial, Helvetica, sans-serif;
            color: whitesmoke;
            border-radius: 5px;
            outline: none;
            border: none;
            text-transform: uppercase;
            transition: 1s;
            background: linear-gradient(to left , rgb(18, 135, 57) ,rgb(53,280,44),rgb(84,165,57));
        }
        button:hover{
            transition: 1s;
            background-position: 102px 200px;
        }
        input:focus{
            border: 2px solid rgb(232, 179, 35);
            transition: 1s;
        }
        input:focus + label{
            top: -13px;
            left: 4px;
            transition: 1s;
            z-index: 2;
            font-size: 15px;
            font-weight: 600;
            color: white;
            opacity: 1;
            text-transform: capitalize;
        }
        small{
            margin-left: 3px;
            font-size: 18px;
            color: whitesmoke;
        }
        a{
            text-decoration: none;
            font-size: 19px;
        }
        @keyframes animate {
            100%{
                top: 180px;
            };
        }
    </style>
</head>
<body>
    <div class="body">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
            <h1>login</h1>
            <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>
            <div class="sub1">
                <input type="text"  name="username">
                <label for="name">username</label></div>
                <div class="sub2">
                    <input type="password" name="password">
                    <label for="password">password</label></div>
                <button type="submit" class="login">Login</button>
                <small>Don't have an account?</small><a href="register.php">Sign-Up</a>
            </form>
    </div> 
</body>
</html>
