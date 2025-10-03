<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$buyerID = $_SESSION['UserID'];

if (!isset($_POST['BookID'])) {
    die("Invalid request.");
}

$bookID = (int)$_POST['BookID'];

$bookQuery = "SELECT * FROM book WHERE BookID = $bookID LIMIT 1";
$bookResult = mysqli_query($conn, $bookQuery);
if (!$bookResult || mysqli_num_rows($bookResult) == 0) {
    die("Book not found.");
}

$book = mysqli_fetch_assoc($bookResult);
$ownerID = $book['SharerID'];
$creditPrice = $book['CreditPrice'] ?? 0;

if ($ownerID == $buyerID) {
    die("You cannot buy your own book.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buy with Credits - <?php echo htmlspecialchars($book['Title']); ?></title>
<link rel="stylesheet" href="../style.css">
<style>
.main_form { max-width: 500px; margin: 50px auto; background: #f4f4f4; padding: 20px; border-radius: 10px; }
.main_form h2 { text-align: center; margin-bottom: 20px; }
.main_form label { display: block; margin-bottom: 5px; font-weight: bold; }
.main_form input[type="date"], .main_form input[type="submit"] { width: 100%; padding: 8px; margin-bottom: 15px; }
.main_form input[type="submit"] { background-color: #4b2e2e; color: white; border: none; cursor: pointer; border-radius: 5px; }
.main_form input[type="submit"]:hover { background-color: #673a3a; }
</style>
</head>
<body>
<main class="main_form">
<h2>Buy "<?php echo htmlspecialchars($book['Title']); ?>" with Credits</h2>
<p><strong>Credit Price:</strong> <?php echo $creditPrice; ?> credits</p>

<form method="post" action="process_credit_purchase.php">
    <input type="hidden" name="BookID" value="<?php echo $bookID; ?>">
    <label for="StartDate">Start Date:</label>
    <input type="date" name="StartDate" required>
    <label for="EndDate">End Date:</label>
    <input type="date" name="EndDate" required>
    <input type="submit" name="confirm" value="Confirm Purchase">
</form>
</main>
</body>
</html>