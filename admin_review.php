<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_planning";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check lab availability
function isLabAvailable($conn, $lab_no, $from_time, $to_time)
{
    $sql = "SELECT * FROM Lab_Timetable WHERE lab_no = ? AND (from_time < ? AND to_time > ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $lab_no, $to_time, $from_time);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows == 0;  // Lab is available if no overlapping bookings
}

// Function to get all available labs
function getAvailableLabs($conn, $from_time, $to_time)
{
    $availableLabs = [];
    for ($lab_no = 301; $lab_no <= 310; $lab_no++) {
        if (isLabAvailable($conn, (string) $lab_no, $from_time, $to_time)) {
            $availableLabs[] = $lab_no;
        }
    }
    return $availableLabs;
}

// Fetch all pending requests
$sql = "SELECT * FROM BookingRequests WHERE status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Review Page</title>
    <link rel="stylesheet" href="admin_review.css">
    <script>
        function hideAvailabilityMessage(requestId) {
            document.getElementById('availability-message-' + requestId).style.display = 'none';
        }
    </script>
</head>

<body>
    <h1>Review Pending Requests</h1>
    <div class="container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $labAvailable = isLabAvailable($conn, $row["lab_no"], $row["from_time"], $row["to_time"]);
                $availabilityText = $labAvailable ? "Requested Lab is available" : "Requested Lab is not available";
                $availabilityClass = $labAvailable ? "available" : "not-available";

                echo "<div class='request'>";
                echo "<p><strong>Event Name:</strong> " . $row["event_name"] . "</p>";
                echo "<p><strong>Venue:</strong> " . $row["lab_no"] . "</p>";
                echo "<p><strong>Date:</strong> " . $row["date1"] . "</p>";
                echo "<p><strong>From:</strong> " . $row["from_time"] . "</p>";
                echo "<p><strong>To:</strong> " . $row["to_time"] . "</p>";
                echo "<p><strong>Email:</strong> " . $row["email"] . "</p>";
                echo "<p id='availability-message-{$row['id']}' class='$availabilityClass'>$availabilityText</p>";

                echo '<form method="POST" action="process_admin_decision.php">';
                echo '<input type="hidden" name="request_id" value="' . $row["id"] . '">';

                if (!$labAvailable) {
                    $availableLabs = getAvailableLabs($conn, $row["from_time"], $row["to_time"]);
                    if (!empty($availableLabs)) {
                        echo "<p>Select an available lab:</p>";
                        echo '<select name="selected_lab" onchange="hideAvailabilityMessage(' . $row['id'] . ')">';
                        foreach ($availableLabs as $lab) {
                            echo "<option value='$lab'>Lab $lab</option>";
                        }
                        echo '</select>';
                    } else {
                        echo "<p>No labs are available at this requested time slot.</p>";
                    }
                } else {
                    echo '<input type="hidden" name="selected_lab" value="' . $row["lab_no"] . '">';
                }

                echo '<input type="submit" name="action" value="Approve">';
                echo '<input type="submit" name="action" value="Deny">';
                echo '</form>';
                echo "</div>";
            }
        } else {
            echo "No pending requests.";
        }
        $conn->close();
        ?>
    </div>
    <div class="buttons">
        <a class="view-button" href="academic_calendar.php">View Academic Calendar</a>
        <a class="view-button" href="view_scheduled_events.php">View Events History</a>
    </div>
</body>

</html>