<?php
session_start();
include("../Equip/Connection.php");
// Check if the form fields are set
if (isset($_POST['user-name']) && isset($_POST['Password'])) {
    $user_name = mysqli_real_escape_string($conn, $_POST['user-name']);
    $password = mysqli_real_escape_string($conn, $_POST['Password']);
    $password = md5($password);
    // Check if the role is admin and the credentials match
   
        // Check credentials against the database
        $sql = "SELECT * FROM `user` WHERE `username`='$user_name' and `password`='$password'";
        $result = mysqli_query($conn, $sql);
        print_r($_POST);
        echo $password;

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify hashed password
            print_r($row);

           
                // Store user data in session

                $_SESSION["username"] = $row['username'];
                $_SESSION["role"] = $row['role'];
                $_SESSION["password"] = $row['password'];
                $role = $row['role'];

                // Get user ID
                $userId = $row['user_id'];
                $_SESSION['user_id'] = $userId;

                $current_date = date('Y-m-d H:i:s');
                $insertLoginQuery = "INSERT INTO login (logged_by, date) VALUES ('$userId', '$current_date')";
                mysqli_query($conn, $insertLoginQuery);

                // Check if it's the user's first login
                $loginCountQuery = "SELECT COUNT(*) AS login_count FROM login WHERE logged_by = '$userId'";
                $loginCountResult = mysqli_query($conn, $loginCountQuery);
                $loginCountRow = mysqli_fetch_assoc($loginCountResult);
                $loginCount = $loginCountRow['login_count'];

                if ($loginCount == 1) {
                    ?>
                    <script>
                        window.location.href = "change_username.php";
                    </script>
                    <?php
                    exit();
                } else {
                    // Not first login, redirect to appropriate dashboard based on role
                    switch ($role) {
                        case 'student':
                            header("Location: ../Student/Student_Dashboard.php");
                            break;
                        case 'admin':
                            header("Location: ../Admin/Admin_Dashboard.php");
                            break;
                        case 'Instructor':
                            header("Location: ../Instructor/Instructor_Dashboard.php");
                            break;
                        case 'Academic Staff':
                            header("Location: ../Academic Staff/Acadamic_Dashboard.php");
                            break;
                        case 'College Staff':
                            header("Location: ../College/College_Dashboard.php");
                            break;
                        case 'Registrar Staff':
                            header("Location: ../Registrar/Registrar_Dashboard.php");
                            break;
                        case 'Research and Community Service Staff':
                            
                            header("Location: ../Research and Community Service/Research and Community_Dashboard.php");
                            break;
                        case 'Department Head':
                            header("Location: ../Department Head/Department_Dashboard.php");
                            break;
                        default:
                            header("Location: loginform.php?error=Invalid role");
                    }
                    exit();
                }
           
        } else {
            // Invalid username or user not found
           // header("Location: loginform.php?error=Invalid username or user not found");
            //exit();
        }
    }

?>
