<?php
include("../Equip/Connection.php");
$password = md5('1q1q!Q!Q');
echo $password;
 // Check credentials against the database
 $sql = "INSERT INTO `user` (`user_id`, `fname`, `lname`, `gender`, `age`, `coll`, `department`, `username`, `password`, `phone`, `role`, `date`) VALUES (NULL, 'were', 'are', 'male', '32', '', '', 'were', '$password', '0987654321', 'admin', '21/2/2024');";
 $result = mysqli_query($conn, $sql);
 print_r($result);
?>