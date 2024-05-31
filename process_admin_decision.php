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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST["request_id"];
    $action = $_POST["action"];
    $selected_lab = $_POST["selected_lab"];

    if ($action == "Approve") {
        $sql = "SELECT * FROM BookingRequests WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Read existing events from data.csv
            $events = [];
            if (($file = fopen("data.csv", "r")) !== false) {
                while (($data = fgetcsv($file, 1000, ",")) !== false) {
                    $events[] = $data;
                }
                fclose($file);
            }

            // Append the new event with the marker
            $newEvent = [
                $row["date1"],
                $row["event_name"],
                $selected_lab,
                $row["from_time"] . " - " . $row["to_time"],
                "new"  // Marker for new entries
            ];
            $events[] = $newEvent;

            // Sort events by date
            usort($events, function($a, $b) {
                return strtotime($a[0]) - strtotime($b[0]);
            });

            // Write the sorted events back to data.csv
            if (($file = fopen("data.csv", "w")) !== false) {
                foreach ($events as $event) {
                    fputcsv($file, $event);
                }
                fclose($file);
            }

            // Update request status to approved
            $updateSql = "UPDATE BookingRequests SET status = 'approved', lab_no = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $selected_lab, $request_id);
            $updateStmt->execute();
        }
    } elseif ($action == "Deny") {
        // Update request status to denied
        $updateSql = "UPDATE BookingRequests SET status = 'denied' WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $request_id);
        $updateStmt->execute();
    }

    header("Location: admin_review.php");
    exit();
}

$conn->close();
?>
