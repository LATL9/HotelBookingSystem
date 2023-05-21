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
            <label for="first_name">Bookings:</label><br><br>
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

    // validate that variable exists as a cookie
    function validate_cookie($var_name) {
        if (!isset($_COOKIE[$var_name])) {
            exit("You are not logged in!");
        }
    }

    validate_cookie('auth_code');
    $auth_code = $_COOKIE['auth_code'];

    // select user ID whith a matching and unexpired authentication code
    $sql = "SELECT user_id FROM auth WHERE (auth_code = " . (int)$auth_code . " AND expiry_time > '" . date('Y-m-d H:i:s') . "')";
    $res = $conn->query($sql);

    if ($res == TRUE) {
        if (mysqli_num_rows($res) == 0) { // if no matching user ID exists
            exit("Invalid/expired authentication code!");
        } else { // if a matching user ID exists
            $user_id = mysqli_fetch_array($res)["user_id"];

            // select bookings from logged in user
            $sql = "SELECT booking_id, room_id, start_time, end_time FROM booking WHERE (user_id = '" . $user_id . "')";
            $res = $conn->query($sql);

            if ($res == TRUE) {
                // for all bookings, print their IDs, room names, start time, and end time
                $i = 0;
                while ($row = mysqli_fetch_array($res)) {
                    $i += 1;
                    print("Booking ID: " . $row['booking_id'] . "<br>");
                    print("Room: " . "Room " . $row['room_id'] + 1 . "<br>");
                    print("Start time: " . $row['start_time'] . "<br>");
                    print("End time: " . $row['end_time'] . "<br>");
                    print('<br>');
                }
            } else {
                exit("Error: " . $conn->error);
            }
        }
    } else {
        exit("Error: " . $conn->error);
    }
?>
            <button id='button' onclick="window.location.href='book.html'">New booking</button>
        </div>
    </body>
</html>
