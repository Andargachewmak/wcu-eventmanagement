<?php
session_start();
include("../Equip/Connection.php");

if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['role'])) {
    header('Location: ../index.php');
    exit();
}

$roles = ['Department head', 'Academic staff', 'Registrar staff', 'College Staff', 'Instructor', 'Research and community service staff'];
$userCounts = [];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

foreach ($roles as $role) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM events 
        JOIN user ON events.created_by = user.user_id 
        WHERE user.role = ?
    ");
    
    // Check if prepare() failed
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("s", $role);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $userCounts[$role] = $row['count'];
    
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Staff Dashboard</title>
    <style>
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

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 40px;
        }

        .card {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex: 1 1 300px;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card h3 {
            margin-top: 0;
            color: #333;
            font-size: 1.5em;
            font-weight: bold;
        }

        .card p {
            color: #666;
            margin: 10px 0;
            font-size: 1.2em;
        }

        .card button {
            background-color: #000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: auto;
            font-weight: bold;
            font-size: 1.1em;
        }

        .card button:hover {
            background-color: #555;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
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
    </style>
</head>
<body>


<div class="navbar">
        <h1>Research and Community</h1>
        <p id="current-time"></p>
    </div>

    <div class="sidebar">
    <a href="Research and Community_Dashboard.php">Dashboard</a>
    <a href="Create_Event.php">Create Event </a>
    <a href="Edit_Event_form.php">Edit and Delete Event</a>
    <a href="manage_profile.php">Manage Your Profile</a>
    <br><br><br><br><br><br><br><br>
    <a href="../Equip/logout.php" class="logout-button">Logout</a>
</div>


<div class="main-content">
    <div class="card-container">
        <?php foreach ($roles as $role): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($role); ?></h3>
                <p>Number of Events: <?php echo $userCounts[$role]; ?></p>
                <button onclick="showDetails('<?php echo htmlspecialchars($role); ?>')">See Details</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function updateTime() {
        var now = new Date();
        var formattedTime = now.toLocaleString();
        document.getElementById('current-time').textContent = formattedTime;
    }
    setInterval(updateTime, 1000);
    updateTime();

    function showDetails(role) {
        window.location.href = 'Event_Details.php?role=' + encodeURIComponent(role);
    }
</script>

</body>
</html>
