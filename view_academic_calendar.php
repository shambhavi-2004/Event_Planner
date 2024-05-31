<!DOCTYPE html>
<html>
<head>
    <title>Academic Calendar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-top: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .old-event {
            background-color: #e0f7fa;
        }
        .new-event {
            background-color: #ffebee;
        }
    </style>
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
