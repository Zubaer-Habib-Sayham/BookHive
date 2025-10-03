<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['BookID'])) {
    echo "No book selected.";
    exit();
}



$bookID = mysqli_real_escape_string($conn, $_GET['BookID']);
$sql = "SELECT * FROM book WHERE BookID='$bookID' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Book not found.";
    exit();
}

$book = mysqli_fetch_assoc($result);

$userID = $_SESSION['UserID'];
$userQuery = "SELECT Credits FROM user WHERE UserID=$userID LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);
$userCredits = $user['Credits'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($book['Title']); ?> - Book Profile</title>
<link rel="stylesheet" href="../style.css">
<style>
 .book_profile {
    max-width: 600px;
    margin: 50px auto;
    background-color: rgba(75, 46, 46, 0.9); 
    color: white;
    padding: 20px;
    border-radius: 10px;
 }
 .book_profile h1 { margin-bottom: 15px; }
 .book_profile p { margin-bottom: 10px; }
 button {
    background-color: #FFD700;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    color: black;
    font-weight: bold;
    margin-top: 15px;
 }
 button:hover { background-color: #e6c200; }
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
            <li><a href="../exchange/exchange.php">Exchanges</a></li>
            <li><a href="../credit_offers/credit_offers.php">Credit Offers</a></li>
           
        </ul>
    </nav>
</header>

<main>
    <section class="book_profile">
        <h1><?php echo htmlspecialchars($book['Title']); ?></h1>
        <p><strong>Author:</strong> <?php echo htmlspecialchars($book['Author']); ?></p>
        <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['Genre']); ?></p>
        <p><strong>Language:</strong> <?php echo htmlspecialchars($book['Language']); ?></p>
        <p><strong>Condition:</strong> <?php echo htmlspecialchars($book['Condition']); ?></p>
        <p><strong>Rating:</strong> <?php echo htmlspecialchars($book['Rating']); ?></p>
        <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($book['Description'])); ?></p>

        <?php if(isset($book['CreditPrice']) && $book['CreditPrice'] > 0): ?>
            <p style="color: #FFD700; font-weight: bold;"><strong>Credit Price:</strong> <?php echo htmlspecialchars($book['CreditPrice']); ?> credits</p>
            <?php if($userCredits >= $book['CreditPrice']): ?>
                <form method="post" action="../credit_offers/buy_credit_form.php">
                    <input type="hidden" name="BookID" value="<?php echo $bookID; ?>">
                    <input type="hidden" name="CreditPrice" value="<?php echo $book['CreditPrice']; ?>">
                    <button type="submit">Buy with Credits</button>
                </form>
            <?php else: ?>
                <p style="color: #ff9999;">You do not have enough credits to buy this book.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>
</body>
</html>