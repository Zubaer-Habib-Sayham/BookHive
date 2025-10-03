<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$currentUserID = $_SESSION['UserID'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ExID'], $_POST['action'])) {
    $ExID = intval($_POST['ExID']);
    $action = $_POST['action'];
    $checkSQL = "SELECT User1ID, Book1ID, Book2ID FROM exchange WHERE ExID = $ExID";
    $checkRes = mysqli_query($conn, $checkSQL);

    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        $row = mysqli_fetch_assoc($checkRes);

        if ($row['User1ID'] != $currentUserID) {
            $_SESSION['msg'] = "You are not allowed to update this request.";
            header("Location: requests.php");
            exit();
        }

        $Book1ID = $row['Book1ID'];
        $Book2ID = $row['Book2ID'];
        if ($action === 'confirm') {
            $sql = "UPDATE exchange 
                    SET Pending = 0, Completed = 1 
                    WHERE ExID = $ExID";
            mysqli_query($conn, $sql);
            mysqli_query($conn, "UPDATE book SET ExID = $ExID WHERE BookID IN ($Book1ID, $Book2ID)");

            $_SESSION['msg'] = "Request confirmed.";

        } elseif ($action === 'cancel') {
            $sql = "UPDATE exchange 
                    SET Pending = 0, Canceled = 1, Completed = 0
                    WHERE ExID = $ExID";
            mysqli_query($conn, $sql);
            mysqli_query($conn, "UPDATE book SET ExID = NULL WHERE ExID = $ExID");

            $_SESSION['msg'] = "Request canceled.";

        } else {
            $_SESSION['msg'] = "Invalid action.";
            header("Location: requests.php");
            exit();
        }

    } else {
        $_SESSION['msg'] = "Exchange not found.";
    }

    header("Location: requests.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['Book1ID'], $_POST['User1ID'], $_POST['Book2ID'], $_POST['StartDate'], $_POST['EndDate'])) {

    $Book1ID = intval($_POST['Book1ID']); 
    $User1ID = intval($_POST['User1ID']);
    $Book2ID = intval($_POST['Book2ID']); 
    $User2ID = $currentUserID;         
    $StartDate = mysqli_real_escape_string($conn, $_POST['StartDate']);
    $EndDate = mysqli_real_escape_string($conn, $_POST['EndDate']);

    $insertSQL = "INSERT INTO exchange 
                  (Book1ID, User1ID, Book2ID, User2ID, Start_Date, End_Date, Pending, Completed, Canceled) 
                  VALUES 
                  ($Book1ID, $User1ID, $Book2ID, $User2ID, '$StartDate', '$EndDate', 1, 0, 0)";

    if (mysqli_query($conn, $insertSQL)) {
        $ExID = mysqli_insert_id($conn);

        mysqli_query($conn, "UPDATE book SET ExID = $ExID WHERE BookID IN ($Book1ID, $Book2ID)");

        $_SESSION['msg'] = "Exchange request sent successfully!";
    } else {
        $_SESSION['msg'] = "Failed to create exchange request: " . mysqli_error($conn);
    }

    header("Location: exchange.php");
    exit();
}

$_SESSION['msg'] = "Invalid request.";
header("Location: exchange.php");
exit();
?>
