<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$userID = $_SESSION['UserID'];

$reqSQL = "SELECT e.ExID, 
                  b.BookID AS YourBookID, b.Title AS YourBookTitle, b.Author AS YourBookAuthor, b.Genre AS YourBookGenre, b.Rating AS YourBookRating,
                  u.Name AS RequesterName, u.Rating AS RequesterRating,
                  b1.BookID AS OfferedBookID, b1.Title AS OfferedBookTitle, b1.Author AS OfferedBookAuthor, b1.Genre AS OfferedBookGenre, b1.Rating AS OfferedBookRating,
                  e.Start_Date, e.End_Date
           FROM exchange e
           JOIN book b ON e.Book1ID = b.BookID      
           JOIN book b1 ON e.Book2ID = b1.BookID    
           JOIN user u ON e.User2ID = u.UserID      
           WHERE e.User1ID = $userID AND e.Pending = 1";

$requests = mysqli_query($conn, $reqSQL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BookHive Exchange Requests</title>
<link rel="stylesheet" href="../style.css">
<style>
 .book_table {
    width: 95%;
    margin: 30px auto;
    border-collapse: collapse;
    background-color: #4B2E2E; 
    color: white;
 }
 .book_table th, .book_table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #6b4b4b;
 }
 .book_table th {
    background-color: #3a1f1f;
 }
 .book_table tr:hover {
    background-color: #5c3b3b;
 }
 .book_table a {
    color: #FFD700;
    text-decoration: none;
 }
 .book_table a:hover {
    text-decoration: underline;
 }
 button {
    background-color: #FFD700;
    border-radius: 4px;
    margin: 5px auto;
    padding: 5px 10px;
    cursor: pointer;
    color: black;
 }
 button:hover {
    background-color: #e6c200;
 }
 form { margin: 0; }
 h2 { margin-top: 50px; text-align: center; }
</style>
</head>
<body class="home_page">

<header>
    <nav>
        <div class="nav_logo">
            <a href="../home.php"><img src="../BookHive_Logo_Woody.png" alt="BookHive"></a>
        </div>

        <ul class="nav_link">
            <li><a href="../book/add_book.php">Add Books</a></li>
            <li><a href="exchange.php">Exchanges</a></li>
            <li><a href="my_library.php">My Library</a></li>
            <li><a href="../credit_offers/credit_offers.php">Credit Offers</a></li>
        </ul>
    </nav>
</header>

<main>
<table class="book_table">
<thead>
<tr>
    <th>Your Book</th>
    <th>Requester</th>
    <th>Requester Rating</th>
    <th>Offered Book</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php if($requests && mysqli_num_rows($requests) > 0): ?>
    <?php while($req = mysqli_fetch_assoc($requests)): ?>
        <tr>
            <td><a href="../book/book_profile.php?BookID=<?php echo $req['YourBookID']; ?>"><?php echo htmlspecialchars($req['YourBookTitle']); ?></a></td>
            <td><?php echo htmlspecialchars($req['RequesterName']); ?></td>
            <td><?php echo htmlspecialchars($req['RequesterRating']); ?></td>
            <td><a href="../book/book_profile.php?BookID=<?php echo $req['OfferedBookID']; ?>"><?php echo htmlspecialchars($req['OfferedBookTitle']); ?></a></td>
            <td><?php echo htmlspecialchars($req['Start_Date']); ?></td>
            <td><?php echo htmlspecialchars($req['End_Date']); ?></td>
            <td>
                <form method="post" action="handle_exchange.php">
                    <input type="hidden" name="ExID" value="<?php echo $req['ExID']; ?>">
                    <button type="submit" name="action" value="confirm">Confirm</button>
                    <button type="submit" name="action" value="cancel">Cancel</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="7" style="text-align:center;">No pending requests.</td></tr>
<?php endif; ?>
</tbody>
</table>
</main>
</body>
</html>
