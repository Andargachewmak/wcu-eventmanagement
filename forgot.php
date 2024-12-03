<?php
session_start();
$error = array();
require "mail.php";
include("../Equip/Connection.php");

use ./PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';
// require 'path/to/PHPMailer/src/Exception.php';

// Initialize $mode variable
$mode = "enter_email";
if(isset($_GET['mode'])){
    $mode = $_GET['mode'];
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch ($mode) {
        case 'enter_email':
            $email = $_POST['email'];
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error[] = "Please enter a valid email";
            } elseif(!valid_email($email)){
                $error[] = "That email was not found";
            } else {
                send_email($email);
                $_SESSION['forgot']['email'] = $email;
                header("Location: forgot.php?mode=enter_code");
                exit;
            }
            break;

        case 'enter_code':
            $code = $_POST['code'];
            $result = is_code_correct($code);
            if($result == "the code is correct"){
                $_SESSION['forgot']['code'] = $code;
                header("Location: forgot.php?mode=enter_password");
                exit;
            } else {
                $error[] = $result;
            }
            break;

        case 'enter_password':
            $password = $_POST['password'];
            $password2 = $_POST['password2'];

            if($password !== $password2){
                $error[] = "Passwords do not match";
            } else {
                save_password($password);
                unset($_SESSION['forgot']);
                header("Location: login.php");
                exit;
            }
            break;
    }
}

function send_email($email){
    global $conn;

    $expire = time() + (60 * 10); // Code valid for 10 minutes
    $code = rand(10000, 99999);

    $query = $conn->prepare("INSERT INTO codes (email, code, expire) VALUES (?, ?, ?)");
    $query->bind_param("ssi", $email, $code, $expire);
    $query->execute();

    $_SESSION['forgot']['code'] = $code;

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com'; // Your Gmail address
        $mail->Password   = 'your_password'; // Your Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('your_email@gmail.com', 'Your Name');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password reset';
        $mail->Body    = "Your code is " . $code;

        $mail->send();
    } catch (Exception $e) {
        // Log error message
        error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    }
}

function save_password($password){
    global $conn;

    $password_hashed = md5($password); // Using MD5 for hashing (not recommended)
    $email = $_SESSION['forgot']['email'];

    $query = $conn->prepare("UPDATE users SET password = ? WHERE email = ? LIMIT 1");
    $query->bind_param("ss", $password_hashed, $email);
    $query->execute();
}

function valid_email($email){
    global $conn;

    $query = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    return $result->num_rows > 0;
}

function is_code_correct($code){
    global $conn;

    $expire = time();
    $email = $_SESSION['forgot']['email'];

    $query = $conn->prepare("SELECT * FROM codes WHERE code = ? AND email = ? ORDER BY id DESC LIMIT 1");
    $query->bind_param("ss", $code, $email);
    $query->execute();
    $result = $query->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if($row['expire'] > $expire){
            return "the code is correct";
        } else {
            return "the code is expired";
        }
    }

    return "the code is incorrect";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
    <style>
        * {
            font-family: tahoma;
            font-size: 13px;
        }
        form {
            width: 100%;
            max-width: 200px;
            margin: auto;
            border: solid thin #ccc;
            padding: 10px;
        }
        .textbox {
            padding: 5px;
            width: 180px;
        }
    </style>
</head>
<body>

<?php 
switch ($mode) {
    case 'enter_email':
        ?>
        <form method="post" action="forgot.php?mode=enter_email"> 
            <h1>Forgot Password</h1>
            <h3>Enter your email below</h3>
            <span style="font-size: 12px; color: red;">
                <?php foreach ($error as $err) echo $err . "<br>"; ?>
            </span>
            <input class="textbox" type="email" name="email" placeholder="Email" required><br><br>
            <input type="submit" value="Next"><br><br>
            <div><a href="login.php">Login</a></div>
        </form>
        <?php
        break;

    case 'enter_code':
        ?>
		<form method="post" action="forgot.php?mode=enter_code"> 
			<h1>Forgot Password</h1>
			<h3>Enter the code sent to your email</h3>
			<span style="font-size: 12px; color: red;">
				<?php foreach ($error as $err) echo $err . "<br>"; ?>
			</span>
			<input class="textbox" type="text" name="code" placeholder="12345" required><br><br>
			<input type="submit" value="Next" style="float: right;">
			<a href="forgot.php"><input type="button" value="Start Over"></a><br><br>
			<div><a href="login.php">Login</a></div>
		</form>
		<?php
		break;

	case 'enter_password':
		?>
		<form method="post" action="forgot.php?mode=enter_password"> 
			<h1>Forgot Password</h1>
			<h3>Enter your new password</h3>
			<span style="font-size: 12px; color: red;">
				<?php foreach ($error as $err) echo $err . "<br>"; ?>
			</span>
			<input class="textbox" type="password" name="password" placeholder="Password" required><br>
			<input class="textbox" type="password" name="password2" placeholder="Retype Password" required><br><br>
			<input type="submit" value="Next" style="float: right;">
			<a href="forgot.php"><input type="button" value="Start Over"></a><br><br>
			<div><a href="login.php">Login</a></div>
		</form>
		<?php
		break;
}
?>

</body>
</html>