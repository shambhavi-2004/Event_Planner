<!DOCTYPE html>
<html>
<head>
    <title>Academic Calendar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Academic Calendar</h1>
    <table>
        <tr>
            <th>Date</th>
            <th>Event Name</th>
            <th>Venue</th>
            <th>Timing</th>
        </tr>
        <?php
        $csv_file = "data.csv";  // Path to your CSV file

        if (($handle = fopen($csv_file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($data[0]) . "</td>";
                echo "<td>" . htmlspecialchars($data[1]) . "</td>";
                echo "<td>" . htmlspecialchars($data[2]) . "</td>";
                echo "<td>" . htmlspecialchars($data[3]) . "</td>";
                echo "</tr>";
            }
            fclose($handle);
        } else {
            echo "<tr><td colspan='4'>Unable to read the calendar data.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
