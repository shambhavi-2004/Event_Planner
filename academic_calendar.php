<!DOCTYPE html>
<html>
<head>
    <title>Academic Calendar</title>
    <link rel="stylesheet" href="academic_calendar.css">
</head>
<body>
    <h1>Academic Calendar</h1>
    <div class="container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Event Name</th>
                <th>Venue</th>
                <th>Timing</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $file = fopen("data.csv", "r");
        if ($file !== false) {
            while (($data = fgetcsv($file, 1000, ",")) !== false) {
                $class = isset($data[4]) && $data[4] === 'new' ? 'new-event' : 'old-event';
                echo "<tr class='$class'>";
                echo "<td>" . htmlspecialchars($data[0]) . "</td>";
                echo "<td>" . htmlspecialchars($data[1]) . "</td>";
                echo "<td>" . htmlspecialchars($data[2]) . "</td>";
                echo "<td>" . htmlspecialchars($data[3]) . "</td>";
                echo "</tr>";
            }
            fclose($file);
        } else {
            echo "<tr><td colspan='4'>Unable to read the academic calendar file.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    </div>
</body>
</html>
