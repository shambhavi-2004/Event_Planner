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

// Fetch all requests (approved and denied)
$sql = "SELECT status, COUNT(*) as count FROM BookingRequests WHERE status IN ('approved', 'denied') GROUP BY status";
$result = $conn->query($sql);

// Initialize counts
$totalRequests = 0;
$approvedCount = 0;
$deniedCount = 0;

// Calculate counts
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'approved') {
            $approvedCount = $row['count'];
        } else if ($row['status'] == 'denied') {
            $deniedCount = $row['count'];
        }
        $totalRequests += $row['count'];
    }
}

// Calculate percentages
$approvedPercentage = $totalRequests > 0 ? ($approvedCount / $totalRequests) * 100 : 0;
$deniedPercentage = $totalRequests > 0 ? ($deniedCount / $totalRequests) * 100 : 0;

// Fetch all approved and denied requests
$sql = "SELECT * FROM BookingRequests WHERE status IN ('approved', 'denied') ORDER BY date1 ASC";
$result = $conn->query($sql);
// Fetch lab request counts
$labSql = "SELECT lab_no, COUNT(*) as count FROM BookingRequests WHERE status IN ('approved', 'denied') GROUP BY lab_no ORDER BY lab_no ASC";
$labResult = $conn->query($labSql);

$labs = [];
$labCounts = [];

if ($labResult->num_rows > 0) {
    while ($row = $labResult->fetch_assoc()) {
        $labs[] = "Lab " . $row['lab_no'];
        $labCounts[] = $row['count'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Scheduled Events</title>
    <link rel="stylesheet" href="view_scheduled_events.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="container1">
        <div class="requests-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $statusClass = $row["status"] == "approved" ? "approved" : "denied";
                    echo "<div class='request $statusClass'>";
                    echo "<p><strong>Event Name:</strong> " . $row["event_name"] . "</p>";
                    echo "<p><strong>Venue:</strong> " . $row["lab_no"] . "</p>";
                    echo "<p><strong>Date:</strong> " . $row["date1"] . "</p>";
                    echo "<p><strong>From:</strong> " . $row["from_time"] . "</p>";
                    echo "<p><strong>To:</strong> " . $row["to_time"] . "</p>";
                    echo "<p><strong>Email:</strong> " . $row["email"] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "No scheduled events.";
            }
            $conn->close();
            ?>
        </div>
        <div class="statistics">
            <h2>Denied or Approved Requests</h2>
            <canvas id="myPieChart"></canvas>
            <div class="inrow">
                <p>Total Requests: <?php echo $totalRequests; ?></p>
                <p>Approved: <?php echo $approvedCount; ?> (<?php echo number_format($approvedPercentage, 2); ?>%)</p>
                <p>Denied: <?php echo $deniedCount; ?> (<?php echo number_format($deniedPercentage, 2); ?>%)</p>
            </div>
        </div>
        <div class="bar-chart-container">
            <h2>Lab Requests Frequency</h2>
            <canvas id="myBarChart"></canvas>
        </div>
    </div>
    <div class="button-class">
        <a class="view-button" href="admin_review.php">Back to Admin Review</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('myPieChart').getContext('2d');
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Approved', 'Denied'],
                    datasets: [{
                        data: [<?php echo $approvedCount; ?>, <?php echo $deniedCount; ?>],
                        backgroundColor: ['#6fde80', '#f25858'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    let label = tooltipItem.label || '';
                                    let value = tooltipItem.raw;
                                    let total = <?php echo $totalRequests; ?>;
                                    let percentage = ((value / total) * 100).toFixed(2);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Bar Chart
            var ctxBar = document.getElementById('myBarChart').getContext('2d');
            var myBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labs); ?>,
                    datasets: [{
                        label: 'Number of Requests',
                        data: <?php echo json_encode($labCounts); ?>,
                        backgroundColor: '#6da8db',
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>



</body>

</html>