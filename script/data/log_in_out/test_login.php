<?php
// Test login functionality
include "../../library/db_connection.php";

echo "<h2>Test Login System</h2>";

// Test database connection
if ($db_connection) {
    echo "✓ Database connection: <b style='color:green'>OK</b><br>";
} else {
    echo "✗ Database connection: <b style='color:red'>FAILED</b><br>";
    die();
}

// Test user exists
$user_name = "admin";
$password = "123456";

$query = "SELECT * FROM users WHERE users_name='$user_name'";
$result = mysqli_query($db_connection, $query);
$total = mysqli_num_rows($result);

echo "Users found with username 'admin': <b>$total</b><br>";

if ($total > 0) {
    $user = mysqli_fetch_array($result);
    echo "User ID: " . $user['users_id'] . "<br>";
    echo "User Name: " . $user['users_name'] . "<br>";
    echo "User Level: " . $user['users_level'] . "<br>";
    echo "User Status: " . $user['users_status'] . "<br>";
    echo "Password Hash in DB: " . $user['users_password'] . "<br>";
    echo "MD5('123456'): " . md5('123456') . "<br>";
    
    if ($user['users_password'] == md5('123456')) {
        echo "<b style='color:green'>✓ Password match!</b><br>";
    } else {
        echo "<b style='color:red'>✗ Password mismatch!</b><br>";
    }
}

// Test with password
$query2 = "SELECT * FROM users WHERE users_name='$user_name' AND users_password=md5('$password')";
$result2 = mysqli_query($db_connection, $query2);
$total2 = mysqli_num_rows($result2);

echo "<br>Login query result: <b>$total2</b> users found<br>";

if ($total2 > 0) {
    echo "<b style='color:green'>✓ Login should work!</b><br>";
} else {
    echo "<b style='color:red'>✗ Login will fail!</b><br>";
}

// Test branches
$q_branch = "SELECT * FROM branch";
$r_branch = mysqli_query($db_connection, $q_branch);
$total_branch = mysqli_num_rows($r_branch);

echo "<br>Total branches: <b>$total_branch</b><br>";
while ($branch = mysqli_fetch_array($r_branch)) {
    echo "- " . $branch['branch_code'] . ": " . $branch['branch_name'] . "<br>";
}
?>
