<?php
// Include config file
require_once "config.php";
// Define variables and initialize with empty values
$firstname = $lastname = $username = $password = $confirm_password = $gender = $birthdate = "";
$firstname_err = $lastname_err = $username_err = $password_err = $confirm_password_err = $gender_err = $birthdate_err = "";
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate firstname
    if(empty(trim($_POST["firstname"]))){
        $firstname_err = "Please enter your first name.";
    } else{
        $firstname = trim($_POST["firstname"]);
    }
    // Validate lastname
    if(empty(trim($_POST["lastname"]))){
        $lastname_err = "Please enter your last name.";
    } else{
        $lastname = trim($_POST["lastname"]);
    }
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = trim($_POST["username"]);
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    // Validate gender
    if(empty(trim($_POST["gender"]))){
        $gender_err = "Please select your gender.";
    } else{
        $gender = trim($_POST["gender"]);
    }
    // Validate birthdate
    if(empty(trim($_POST["birthdate"]))){
        $birthdate_err = "Please enter your birthdate.";
    } else{
        $birthdate = trim($_POST["birthdate"]);
    }
    // Check input errors before inserting in database
    if(empty($firstname_err) && empty($lastname_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($gender_err) && empty($birthdate_err)){
       // Prepare an insert statement
$sql = "INSERT INTO users (username, password, firstname, lastname, gender, birthdate) VALUES (?, ?, ?, ?, ?, ?)";
if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_firstname, $param_lastname, $param_gender, $param_birthdate);
    // Set parameters
    $param_username = $username;
    $param_password = password_hash($password, PASSWORD_DEFAULT);
    $param_firstname = $firstname;
    $param_lastname = $lastname;
    $param_gender = $gender;
    $param_birthdate = $birthdate;
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        // Redirect to login page
        header("location: login.php");
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
<title>Sign Up</title>
<link rel="stylesheet" href="bootstrap.min.css">
<style>
    *{font-family:Verdana;}
    body{ font: 14px sans-serif; background-color:grey; }
    .wrapper{ width: 360px; padding: 20px; border:1px solid; margin:auto; background-color:white;}
</style>
</head>
<body>
<div class="wrapper">
            <h2>Sign Up</h2>
            <p>Please fill this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
    <span class="invalid-feedback"><?php echo $username_err; ?></span>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password;?>">
        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
    </div>
    <div class="form-group">
            <label>First Name</label>
            <input type="text" name="firstname" class="form-control <?php echo (!empty($firstname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $firstname; ?>">
            <span class="invalid-feedback"><?php echo $firstname_err; ?></span>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="lastname" class="form-control <?php echo (!empty($lastname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lastname; ?>">
            <span class="invalid-feedback"><?php echo $lastname_err; ?></span>
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control <?php echo (!empty($gender_err)) ? 'is-invalid' : ''; ?>">
                <option value="">Please select</option>
                <option value="male" <?php if($gender === "male") echo "selected"; ?>>Male</option>
                <option value="female" <?php if($gender === "female") echo "selected"; ?>>Female</option>
                <option value="other" <?php if($gender === "other") echo "selected"; ?>>Other</option>
            </select>
            <span class="invalid-feedback"><?php echo $gender_err; ?></span>
        </div>
        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="birthdate" class="form-control <?php echo (!empty($birthdate_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $birthdate; ?>">
            <span class="invalid-feedback"><?php echo $birthdate_err; ?></span>
        </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <input type="reset" class="btn btn-secondary ml-2" value="Reset">
    </div>
    
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>
</html>