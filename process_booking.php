<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_planning";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $lab_no = $_POST['lab_no'];
    $date1 = $_POST['date1'];
    $from_time = $_POST['from_time'];
    $to_time = $_POST['to_time'];
    $email = $_POST['email'];

    // Insert the request into BookingRequests table
    $sql = "INSERT INTO BookingRequests (event_name, lab_no, date1, from_time, to_time, email) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $event_name, $lab_no, $date1, $from_time, $to_time, $email);

    if ($stmt->execute()) {
        echo "Request submitted! You will get notified soon!";
        // Additional code to send email notification can be added here
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
