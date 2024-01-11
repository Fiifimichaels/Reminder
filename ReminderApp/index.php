<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reminderdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Reminder and Delete Reminder
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["no"]) && isset($_POST["description"]) && isset($_POST["start-date"]) && isset($_POST["end-date"])) {
        // Add Reminder
        $car_no = $_POST["no"];
        $description = $_POST["description"];
        $reg_date = $_POST["start-date"];
        $expiry = $_POST["end-date"];

        $stmt = $conn->prepare("INSERT INTO reminder (car_no, description, reg_date, expiry) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $car_no, $description, $reg_date, $expiry);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST["delete_id"])) {
        // Delete Reminder
        $delete_id = $_POST["delete_id"];
        $stmt = $conn->prepare("DELETE FROM reminder WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch existing reminders
$sql = "SELECT * FROM reminder";
$result = $conn->query($sql);
$reminders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder App</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Reminder App</h1>
        <form id="reminder-form" method="POST">
            <label for="no">Car No.</label>
            <select id="no" name="no" required>
                <option value="" disabled selected>Select Car Number</option>
                <option value="GB 3644 - 20">GB 3644 - 20</option>
                <option value="GM 3806 - 14">GM 3806 - 14</option>
                <option value="GC 8354 - 20">GC 8354 - 20</option>
                <option value="GR 3762 - 22">GR 3762 - 22</option>
                <option value="GG 3478 - 17">GG 3478 - 17</option>
                <option value="GS 7971 - 19">GS 7971 - 19</option>
                <option value="GC 1235 - 18">GC 1235 - 18</option>
                <option value="GT 7501 - 23">GT 7501 - 23</option>
                <option value="GT 7502 - 23">GT 7502 - 23</option>
                <option value="GT 7503 - 23">GT 7503 - 23</option>
            </select>
            <br>
            <label for="description">Description</label>
            <select id="description" name="description" required>
                <option value="" disabled selected>Select Description</option>
                <option value="Road Worthy">Road Worthy</option>
                <option value="Insurance">Insurance</option>
                <option value="TMA Sticker">TMA Sticker</option>
                <option value="VIT Sticker">VIT Sticker</option>
            </select>
            <br>
            <label for="start-date">Reg Date</label>
            <input type="date" id="start-date" name="start-date" required>
            <label for="end-date">Expiry Date</label>
            <input type="date" id="end-date" name="end-date" required>
            <button type="submit">Add Reminder</button>
        </form>
        <table id="reminder-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Car No.</th>
                    <th>Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Count Down</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reminders as $reminder): ?>
                    <tr>
                        <td><?= $reminder['id']; ?></td>
                        <td><?= $reminder['car_no']; ?></td>
                        <td><?= $reminder['description']; ?></td>
                        <td><?= $reminder['reg_date']; ?></td>
                        <td><?= $reminder['expiry']; ?></td>
                        <td><?= calculateCountdown($reminder['expiry']); ?></td>
                        <td><button name="delete" class="delete-btn" onclick="deleteReminder(<?= $reminder['id']; ?>)">Delete</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        function deleteReminder(reminderId) {
            if (confirm('Are you sure you want to delete this reminder?')) {
                // Send an AJAX request to the server to delete the reminder
                fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'delete_id=' + reminderId,
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Delete response:', data);
                    // Reload the page to update the table
                    location.reload();
                })
                .catch(error => {
                    console.error('Error deleting reminder:', error);
                });
            }
        }
    </script>
</body>
</html>

<?php
function calculateCountdown($endDate) {
    $endDateTime = strtotime($endDate);
    $currentDateTime = time();
    $timeDifference = $endDateTime - $currentDateTime;

    $days = floor($timeDifference / (60 * 60 * 24));
    $hours = floor(($timeDifference % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($timeDifference % (60 * 60)) / 60);
    $seconds = $timeDifference % 60;

    return "{$days}d {$hours}h {$minutes}m {$seconds}s";
}
?>