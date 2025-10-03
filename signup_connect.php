<?php
require_once('DBconnect.php');
session_start();

if (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['address'], $_POST['phone'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $check = "SELECT * FROM user WHERE Email='$email' LIMIT 1";
    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0){
        echo "<script>alert('Email already registered. Please login.'); window.location.href='index.php';</script>";
        exit();
    }
    $signup_credit_bonus=3000;
    $sql = "INSERT INTO user (Name, Password, Email, Adress, Phone, Credits) 
            VALUES ('$name', '$password', '$email', '$address', '$phone', '$signup_credit_bonus')";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Sign up successful! Please login.'); window.location.href='index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: Could not register user.'); window.location.href='signup.php';</script>";
        exit();
    }

} else {
    header("Location: signup.php");
    exit();
}
?>
