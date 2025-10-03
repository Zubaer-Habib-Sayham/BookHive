<?php
require_once('DBconnect.php');
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM user WHERE Name='$username' AND Password='$password' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['UserID'] = $user['UserID'];  
        $_SESSION['Name'] = $user['Name'];

        header("Location: home.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='index.php';</script>";
    }
}
?>