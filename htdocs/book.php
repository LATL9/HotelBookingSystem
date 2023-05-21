<html>
    <link rel="stylesheet" href="styles.css">
    <title>
        Hotel Booking System™
    </title>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div class="creds">
            <header>
                <h1>
                    <label for="title"/>Hotel Booking System™</label>
                </h1>
            </header>
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db = "db";

    // create connections
    $conn = new mysqli($servername, $username, $password, $db);

    // check conn_names
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // validate that variable exists in POST request
    function validate($var_name) {
        if (!isset($_POST[$var_name])) {
            exit("Variable " . $var_name . " is invalid!");
        }
    }
    // validate that variable exists as a cookie
    function validate_cookie($var_name) {
        if (!isset($_COOKIE[$var_name])) {
            exit("You are not logged in!");
        }
    }

    validate('room_id');
    validate('start_time');
    validate('end_time');
    validate_cookie('auth_code');

    $room_id = $_POST['room_id'] - 1; // substracted by 1 so names (1-10) are in the range of internal IDs (0-9)
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $auth_code = $_COOKIE['auth_code'];

    // select user ID whith a matching and unexpired authentication code
    $sql = "SELECT user_id FROM auth WHERE (auth_code = " . (int)$auth_code . " AND
expiry_time > '" . date('Y-m-d H:i:s') . "')";
    $res = $conn->query($sql);

    if ($res == TRUE) {
        if (mysqli_num_rows($res) == 0) { // if no matching user ID exists
            print("Invalid/expired authentication code!");
        } else { // if a matching user ID exists
            $user_id = mysqli_fetch_array($res)['user_id'];
            // check for overlapping times by selecting bookings with a matching room ID and an overlapping time
            $sql = "SELECT booking_id, room_id, start_time, end_time FROM booking WHERE (room_id = " . $room_id . " AND end_time >= '" . $start_time . "' AND '" . $end_time . "' >= start_time)";
            $res = $conn->query($sql);

            if ($res == TRUE) {
                if (mysqli_num_rows($res) == 0) { // if no overlapping bookings exist
                    // insert new booking into table
                    $sql = "INSERT INTO booking VALUES (NULL, " . $user_id . ", " . $room_id . ", '" . $start_time . "', '" . $end_time . "')";
                    $res = $conn->query($sql);

                    if ($res == TRUE) {
                        print("Booking successfully added!<br><br>");
                    } else {
                        print("Error: " . $conn->error);
                    }
                } else { // if overlapping bookings exist
                    print("Booking not created, as the following booking(s) are overlapping:<br><br>");
                    // for all overlapping bookings, print their ID, start time, and end time
                    $i = 0;
                    while ($row = mysqli_fetch_array($res)) {
                        $i += 1;
                        print("Booking ID: " . $row['booking_id'] . "<br>");
                        print("Start time: " . $row['start_time'] . "<br>");
                        print("End time: " . $row['end_time'] . "<br>");
                        print('<br>');
                    }
                }
            } else {
                exit("Error: " . $conn->error);
            }
        }
    } else {
        exit("Error: " . $conn->error);
    }
?>
            <button id='button' onclick="window.location.href='overview.php'">Overview</button>
            <button id='button' onclick="window.location.href='book.html'">New booking</button>
        </div>
    </body>
</html>
