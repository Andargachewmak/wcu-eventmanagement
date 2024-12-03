<?php
session_start();
include("../Equip/Connection.php");

// Check if the session variables are set
if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['role'])) {
    header('Location: ../index.php');
    exit();
}

// Check if user_id is provided
if (!isset($_GET['event_id'])) {
    header('Location: Admin_Dashboard.php');
    exit();
}

$event_id = $_GET['event_id'];
$sql = "SELECT * FROM event WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Event Not Found";
    exit();
}

$event = $result->fetch_assoc();
$title = $event['title'];
$description = $event['description'];
$event_date = $event['event_date'];
$event_time = $event['event_time'];
$location = $event['location'];
$created_by = $event['created_by'];
$created_at = $event['created_at'];
$image_url = $event['image']; // Assuming the column name is 'image_url'
$preference = $event['preference']; // Assuming the column name is 'preference'
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Staff Dashboard</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1,
        .navbar p {
            margin: 0;
        }

        .sidebar {
            width: 200px;
            background-color: #444;
            color: white;
            position: fixed;
            top: 60px;
            left: 0;
            height: calc(100% - 60px);
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: red;
        }

        .main-content {
            margin-left: 220px;
            margin-right: 20px;
            padding: 80px 20px 20px 20px;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
            position: fixed;
            top: 60px;
            width: calc(100% - 220px);
            margin-left: 220px;
            z-index: 999;
        }

        .card {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
        }

        .form-container {
            width: 80%;
            max-width: 600px;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            width: 200px;
            margin-right: 30px;
            text-align: right;
        }

        .form-control {
            width: 80%;
            max-width: 600px;
            padding: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #555;
        }

        .logout-button {
            background-color: red;
            color: white;
            font-size: 30px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .logout-button:hover {
            background-color: darkred;
        }

        .btn-create {
            background-color: green;
            color: white;
            font-size: 25px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            cursor: pointer;
            text-align: center;
        }

        .btn-create:hover {
            background-color: darkgreen;
        }

        .btn-cancel {
            background-color: yellowgreen;
            color: white;
            font-size: 25px;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            cursor: pointer;
            text-align: center;
        }

        .btn-cancel:hover {
            background-color: darkred;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
            }

            .sidebar a {
                float: left;
                text-align: center;
                width: 100%;
            }

            .main-content {
                margin-left: 0;
                padding-top: 140px;
            }

            .header {
                width: 100%;
                margin-left: 0;
                top: 60px;
            }

            .form-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-group label {
                width: 100%;
                margin-bottom: 5px;
                text-align: left;
            }

            .form-control {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 10px;
                flex-direction: column;
            }

            .navbar h1 {
                font-size: 18px;
            }

            .header h2 {
                font-size: 16px;
            }

            .card {
                padding: 15px;
            }
        }

        table {
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            text-align: left;
            background-color: #f2f2f2;
        }

        .event-image-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            height: 400px;
            background-size: cover;
            background-position: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .event-description {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 5px;
        }

        .event-details label,
        .event-details input[type="text"],
        .event-details input[type="date"],
        .event-details input[type="time"],
        .event-details textarea {
            display: block;
            margin-bottom: 10px;
        }

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
            color: white;
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

    <div class="navbar">
        <h1>College Staff Dashboard</h1>
        <p id="current-time"></p>
    </div>

    <div class="sidebar">
        <a href="College_Dashboard.php"> College Dashboard</a>
        <a href="Create_Event.php">Create Event</a>
        <a href="Edit_Event_form.php">Edit and Delete Event</a>
        <a href="manage_profile.php">Manage Your Profile</a>
        <br><br><br><br><br><br><br><br>
        <a href="../Equip/logout.php" class="logout-button">Logout</a>
    </div>

    <div class="main-content">
        <h1 style="color:black">View Event Details</h1>
        <?php if (isset($_SESSION['message'])) : ?>
            <p class="message"><?php echo $_SESSION['message']; ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <div class="event-details">
            <label for="title">Event Title:</label>
            <input type="text" id="title" name="event_title" value="<?php echo htmlspecialchars($title); ?>" disabled>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($event_date); ?>" disabled>

            <label for="time">Time:</label>
            <input type="time" id="time" name="time" value="<?php echo htmlspecialchars($event_time); ?>" disabled>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" disabled>

            <label for="created_at">Created At:</label>
            <input type="text" id="created_at" name="created_at" value="<?php echo htmlspecialchars($created_at); ?>" disabled>

            <label for="preference">Preference:</label>
            <input type="text" id="preference" name="preference" value="<?php echo htmlspecialchars($preference); ?>" disabled>

            <div class="event-image-container" style="background-image: url('<?php echo htmlspecialchars($image_url); ?>');">
            <label for="description">Event Description:</label>
            <div class="event-description">
                    
              <?php echo htmlspecialchars($description); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to display the current date and time
        function displayCurrentTime() {
            const now = new Date();
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: true
            };
            const formattedTime = now.toLocaleString('en-US', options);
            document.getElementById('current-time').textContent = formattedTime;
        }

        // Call the function to display the current time
        displayCurrentTime();

        // Update the current time every second
        setInterval(displayCurrentTime, 1000);
    </script>

</body>

</html>
