<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "db";

function validate($var_name) {
    if (!isset($_POST[$var_name])) {
        exit("Variable " . $var_name . " is invalid!");
    }
}

validate('first_name');
validate('last_name');
validate('pass');

$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$pass = $_POST['pass'];

// create connections
$conn = new mysqli($servername, $username, $password, $db);

// check conn_names
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// select user ID with matching inputted credentials
$sql = "SELECT user_id FROM user WHERE (first_name = '" . $first_name . "' AND last_name = '" . $last_name . "')";
$res = $conn->query($sql);

if (mysqli_num_rows($res) == 0) { // if no matching user ID exists
    // insert new user with inputted credentials
    $sql = "INSERT INTO user (first_name, last_name, password) VALUES ('" . $first_name . "', '" . $last_name . "', '" . $pass . "')";

    if ($conn->query($sql) === TRUE) {
        print("User created successfully!");
    } else {
        exit("Error inserting record: " . $conn->error);
    }
} else { // if a matching user ID exists
    exit("User already exists. Please try a different name.");
}

$conn->close();
?>
