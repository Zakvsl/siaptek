<?php
session_start();
include "../../library/db_connection.php";

// Debug mode
$debug = true;

if ($debug) {
    echo "<pre>POST Data: ";
    print_r($_POST);
    echo "</pre>";
}

if (isset($_POST['btn_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($debug) {
        echo "<p>Username: $username</p>";
        echo "<p>Password: $password</p>";
        echo "<p>MD5 Password: " . md5($password) . "</p>";
    }
    
    $query = "SELECT * FROM users WHERE users_name='$username' AND users_password=md5('$password')";
    
    if ($debug) {
        echo "<p>Query: $query</p>";
    }
    
    $result = mysqli_query($db_connection, $query);
    
    if (!$result) {
        echo "<p style='color:red'>Query Error: " . mysqli_error($db_connection) . "</p>";
    } else {
        $total = mysqli_num_rows($result);
        echo "<p>Rows found: $total</p>";
        
        if ($total > 0) {
            $user = mysqli_fetch_array($result);
            echo "<p style='color:green'><b>LOGIN SUCCESS!</b></p>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
            
            // Set session
            $_SESSION['ses_siaptek_admin'] = $user['users_name'];
            $_SESSION['ses_user_id'] = $user['users_id'];
            $_SESSION['ses_user_naming'] = $user['users_names'];
            $_SESSION['ses_user_level'] = $user['users_level'];
            
            echo "<p><a href='../../index/index.php'>Go to Dashboard</a></p>";
        } else {
            echo "<p style='color:red'><b>LOGIN FAILED - User not found or wrong password</b></p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Login Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .form-box { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; }
        input[type="submit"] { width: 100%; padding: 10px; background: #0054a6; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Simple Login Test</h2>
        <form method="post" action="">
            <label>Username:</label>
            <input type="text" name="username" value="admin" required>
            
            <label>Password:</label>
            <input type="password" name="password" value="123456" required>
            
            <input type="submit" name="btn_login" value="Login">
        </form>
        
        <hr>
        <p><small>Default: admin / 123456</small></p>
        <p><a href="../../index/index.php">Back to Main Login</a></p>
    </div>
</body>
</html>
