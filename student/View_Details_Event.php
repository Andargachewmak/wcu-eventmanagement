<?php
session_start();
include("../Equip/Connection.php");

// Retrieve event details for viewing
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $event_id = $_GET['id'];

    $sql = "SELECT event_id, title, description, event_date, event_time, location, created_by, created_at FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $description = $row['description'];
        $event_date = $row['event_date'];
        $event_time = $row['event_time'];
        $location = $row['location'];
        $created_by = $row['created_by'];
        $created_at = $row['created_at'];
    } else {
        echo "Event not found.";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .event-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            color: green;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>View Event Details</h1>
        <?php if (isset($_SESSION['message'])) : ?>
            <p class="message"><?php echo $_SESSION['message']; ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <label for="title">Event Title:</label>
        <input type="text" id="title" name="event_title" value="<?php echo htmlspecialchars($title); ?>" disabled><br>

        <label for="description">Event Description:</label>
        <textarea id="description" name="event_description" rows="12" cols="50" disabled><?php echo htmlspecialchars($description); ?></textarea><br>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($event_date); ?>" disabled><br>

        <label for="time">Time:</label>
        <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($event_time); ?>" disabled><br>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" disabled><br>

        <label for="created_by">Created By:</label>
        <input type="text" id="created_by" name="created_by" value="<?php echo htmlspecialchars($created_by); ?>" disabled><br>

        <label for="created_at">Created At:</label>
        <input type="text" id="created_at" name="created_at" value="<?php echo htmlspecialchars($created_at); ?>" disabled><br>
    </div>
</body>

</html>