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

// select user ID with matching credentials
$sql = "SELECT user_id FROM user WHERE (first_name = '" . $first_name . "' AND last_name = '" . $last_name . "' AND password = '" . $pass . "')";
$res = $conn->query($sql);

if (mysqli_num_rows($res) == 0) { // if no matching user ID exists
    exit("Credentials are invalid!");
} else { // if a matching user ID exists
    $auth_code = rand(-9223372036854775808, 9223372036854775807); // large random number used to prevent bruteforcing
    // insert new entry in auth with user ID, authentication code, and an expiry time 24 hours later
    $sql = "INSERT INTO auth VALUES (" . mysqli_fetch_array($res)["user_id"] . ", " . $auth_code . ", ADDTIME('" . date('Y-m-d H:i:s') . "', '1 00:00:00')) ON DUPLICATE KEY UPDATE auth_code = " . $auth_code . ", expiry_time = ADDTIME('" . date('Y-m-d H:i:s') . "', '1 00:00:00')";

    if ($conn->query($sql) == TRUE) {
        setcookie('auth_code', $auth_code, time() + 86400, "/"); // set authentication code cookie expiring in 24 hours
        header('Location: '. "./overview.php"); // redirect to overview page
    } else {
        exit("Error inserting record: " . $conn->error);
    }
}
?>
