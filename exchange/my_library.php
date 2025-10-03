<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$currentUserID = $_SESSION['UserID'];

$sql = "
SELECT b.BookID, b.Title, b.Author,
       CASE WHEN e.User1ID = $currentUserID THEN u2.Name ELSE u1.Name END AS FromUser,
       e.Start_Date, e.End_Date
FROM exchange e
JOIN book b 
     ON (b.BookID = e.Book2ID AND e.User1ID = $currentUserID)
     OR (b.BookID = e.Book1ID AND e.User2ID = $currentUserID)
JOIN user u1 ON e.User1ID = u1.UserID
JOIN user u2 ON e.User2ID = u2.UserID
WHERE e.Completed = 1
";

$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Library</title>
<link rel="stylesheet" href="../style.css">
<style>
 .book_table { width: 90%; margin: 30px auto; border-collapse: collapse; background-color: #4B2E2E; color: white;}
 .book_table th, .book_table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #6b4b4b; }
 .book_table th { background-color: #3a1f1f; }
 .book_table tr:hover { background-color: #5c3b3b; }
 .book_table a { color: #FFD700; text-decoration: none; }
 .book_table a:hover { text-decoration: underline; }
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
            <li><a href="../credit_offers/credit_offers.php">Credit Offers</a></li>
        </ul>
    </nav>
</header>

<main>
    <table class="book_table">
        <thead>
            <tr>
                <th>Book Name</th>
                <th>Author</th>
                <th>Borrowed From</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($res) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Title']) ?></td>
                        <td><?= htmlspecialchars($row['Author']) ?></td>
                        <td><?= htmlspecialchars($row['FromUser']) ?></td>
                        <td><?= htmlspecialchars($row['Start_Date']) ?></td>
                        <td><?= htmlspecialchars($row['End_Date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">You don't have any books in your library right now.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>
