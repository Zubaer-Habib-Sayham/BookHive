<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: credit_offers.php");
    exit();
}

$buyerID = (int)$_SESSION['UserID'];
$bookID  = isset($_POST['BookID']) ? (int)$_POST['BookID'] : 0;
$startDate = isset($_POST['StartDate']) ? $_POST['StartDate'] : null;
$endDate   = isset($_POST['EndDate']) ? $_POST['EndDate'] : null;

if ($bookID <= 0) {
    die("Invalid request.");
}

if (!$startDate || !$endDate) {
    die("Start and End dates are required.");
}
if ($endDate < $startDate) {
    die("End date must be on or after start date.");
}
$today = date('Y-m-d');
if ($startDate < $today) {
    die("Start date cannot be in the past.");
}

mysqli_begin_transaction($conn);

try {
    $bookQuery = "SELECT BookID, SharerID, CreditPrice FROM book WHERE BookID = $bookID FOR UPDATE";
    $bookResult = mysqli_query($conn, $bookQuery);
    if (!$bookResult || mysqli_num_rows($bookResult) === 0) {
        throw new Exception("Book not found.");
    }
    $book = mysqli_fetch_assoc($bookResult);

    $sellerID = (int)$book['SharerID'];
    $creditPrice = (int)$book['CreditPrice'];

    if ($sellerID === $buyerID) {
        throw new Exception("You cannot buy your own book.");
    }
    if ($creditPrice <= 0) {
        throw new Exception("This book is not available for credit purchase.");
    }

    $buyerQuery = "SELECT Credits FROM user WHERE UserID = $buyerID FOR UPDATE";
    $buyerRes = mysqli_query($conn, $buyerQuery);
    $buyerRow = mysqli_fetch_assoc($buyerRes);
    $buyerCredits = (int)$buyerRow['Credits'];

    if ($buyerCredits < $creditPrice) {
        throw new Exception("Insufficient credits to buy this book.");
    }

    $sellerQuery = "SELECT Credits FROM user WHERE UserID = $sellerID FOR UPDATE";
    $sellerRes = mysqli_query($conn, $sellerQuery);
    $sellerRow = mysqli_fetch_assoc($sellerRes);

    $q1 = "UPDATE user SET Credits = Credits - $creditPrice WHERE UserID = $buyerID";
    if (!mysqli_query($conn, $q1)) throw new Exception("Failed to deduct buyer credits: " . mysqli_error($conn));

    $q2 = "UPDATE user SET Credits = Credits + $creditPrice WHERE UserID = $sellerID";
    if (!mysqli_query($conn, $q2)) throw new Exception("Failed to credit seller: " . mysqli_error($conn));

    $stmt = $conn->prepare("INSERT INTO credits (ByCredit, BookID, UserID, StartDate, EndDate) VALUES (1, ?, ?, ?, ?)");
    $stmt->bind_param("iiss", $bookID, $buyerID, $startDate, $endDate);
    if (!$stmt->execute()) throw new Exception("Failed to log credits transaction: " . $stmt->error);

    $stmt2 = $conn->prepare("UPDATE book SET SharerID = ? WHERE BookID = ?");
    $stmt2->bind_param("ii", $buyerID, $bookID);
    if (!$stmt2->execute()) throw new Exception("Failed to update book ownership: " . $stmt2->error);

    mysqli_commit($conn);

    header("Location: credit_offers.php?success=1");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn);
    $msg = htmlspecialchars($e->getMessage());
    die("Purchase failed: $msg");
}
?>