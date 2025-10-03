<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$userID = $_SESSION['UserID'];
$exID = intval($_POST['ExID']);
$otherUserID = intval($_POST['OtherUserID']);
$bookID = intval($_POST['BookID']);
$userRating = intval($_POST['UserRating']);
$userReview = mysqli_real_escape_string($conn, $_POST['UserReview']);
$bookRating = intval($_POST['BookRating']);
$bookReview = mysqli_real_escape_string($conn, $_POST['BookReview']);

$checkQuery = "SELECT 1 FROM user_rating WHERE ExID = $exID AND User1ID = $userID LIMIT 1";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    die("You have already submitted a review for this exchange.");
}

$sql1 = "INSERT INTO user_rating (User1ID, User2ID, Review, Rating, ExID) 
         VALUES ($userID, $otherUserID, '$userReview', $userRating, $exID)";
if (mysqli_query($conn, $sql1)) {
    $ratingID_user = mysqli_insert_id($conn);
    mysqli_query($conn, "INSERT INTO user_rates_user (UserID, RatingID) VALUES ($userID, $ratingID_user)");
} else {
    die("Error inserting user rating: " . mysqli_error($conn));
}

$sql2 = "INSERT INTO book_rating (UserID, BookID, Review, Rating, ExID) 
         VALUES ($userID, $bookID, '$bookReview', $bookRating, $exID)";
if (mysqli_query($conn, $sql2)) {
    $ratingID_book = mysqli_insert_id($conn);
    mysqli_query($conn, "INSERT INTO user_rates_book (UserID, RatingID) VALUES ($userID, $ratingID_book)");
} else {
    die("Error inserting book rating: " . mysqli_error($conn));
}

$freeBooks = "UPDATE book SET ExID = NULL WHERE ExID = $exID";
mysqli_query($conn, $freeBooks);
mysqli_query($conn, "
    UPDATE user u
    SET u.Rating = (
        SELECT AVG(Rating)
        FROM user_rating
        WHERE User2ID = u.UserID
    )
");
mysqli_query($conn, "
    UPDATE book b
    SET b.Rating = (
        SELECT AVG(Rating)
        FROM book_rating
        WHERE BookID = b.BookID
    )
");

header("Location: ../home.php");
exit();
?>
