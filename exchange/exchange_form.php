<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['BookID'])) {
    echo "No book selected for exchange.";
    exit();
}

$bookID = mysqli_real_escape_string($conn, $_GET['BookID']);
$userID = $_SESSION['UserID'];


$sql = "SELECT b.BookID, b.Title, b.Author, b.Genre, b.Rating AS BookRating, u.UserID AS OwnerID, u.Name AS OwnerName, u.Rating AS OwnerRating
        FROM book b
        JOIN user u ON b.SharerID = u.UserID
        WHERE b.BookID='$bookID' LIMIT 1";

$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Book not found.";
    exit();
}
$book = mysqli_fetch_assoc($result);
$myBooksResult = mysqli_query($conn, "SELECT BookID, Title FROM book WHERE SharerID='$userID' AND EXID IS NULL");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Exchange - BookHive</title>
<link rel="stylesheet" href="../style.css">
<style>
 .exchange_form {
    max-width: 600px;
    margin: 50px auto;
    background-color: rgba(75, 46, 46, 0.9); 
    color: white;
    padding: 20px;
    border-radius: 10px;
 }
 .exchange_form h2 {
    margin-bottom: 15px;
 }
 .exchange_form label {
    display: block;
    margin: 10px 0 5px;
 }
 .exchange_form input, .exchange_form select, .exchange_form button {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
 }
</style>
</head>
<body>
<header>
    <nav>
        <div class="nav_logo">
            <a href="../home.php"><img src="../BookHive_Logo_Woody.png" alt="BookHive"></a>
        </div>
        <ul class="nav_link">
            <li><a href="../home.php">Home</a></li>
            <li><a href="../book/add_book.php">Add Books</a></li>
            <li><a href="exchange.php">Exchanges</a></li>
            <li><a href="my_library.php">My Library</a></li>
        </ul>
    </nav>
</header>

<main>
<div class="exchange_form">
    <h2>Request Exchange</h2>

    <p><strong>Book you want to exchange:</strong> <?php echo htmlspecialchars($book['Title']); ?></p>
    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['Author']); ?></p>
    <p><strong>Owner:</strong> <?php echo htmlspecialchars($book['OwnerName']); ?> (Rating: <?php echo htmlspecialchars($book['OwnerRating']); ?>)</p>

    <form method="post" action="handle_exchange.php">
        <input type="hidden" name="Book1ID" value="<?php echo $book['BookID']; ?>">
        <input type="hidden" name="User1ID" value="<?php echo $book['OwnerID']; ?>">
        <input type="hidden" name="User2ID" value="<?php echo $book['OwnerID']; ?>">

        <label for="Book2ID">Select your book to offer:</label>
        <select name="Book2ID" required>
            <option value="">--Select your book--</option>
            <?php while($myBook = mysqli_fetch_assoc($myBooksResult)): ?>
                <option value="<?php echo $myBook['BookID']; ?>"><?php echo htmlspecialchars($myBook['Title']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="StartDate">Start Date:</label>
        <input type="date" name="StartDate" required>

        <label for="EndDate">End Date:</label>
        <input type="date" name="EndDate" required>

        <button type="submit" name="action" value="request">Send Exchange Request</button>
    </form>
</div>
</main>
</body>
</html>
