<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$userID = $_SESSION['UserID'];

$creditQuery = "SELECT Credits FROM user WHERE UserID = $userID LIMIT 1";
$creditResult = mysqli_query($conn, $creditQuery);
$credits = 0;
if ($creditResult && mysqli_num_rows($creditResult) > 0) {
    $row = mysqli_fetch_assoc($creditResult);
    $credits = $row['Credits'];
}

$query = "
SELECT b.BookID, b.Title, b.Author, c.StartDate, c.EndDate, b.CreditPrice
FROM book b
JOIN credits c ON b.BookID = c.BookID
WHERE c.UserID = $userID AND c.ByCredit = 1
ORDER BY c.StartDate DESC
";

$result = mysqli_query($conn, $query);
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Purchased Books - Dashboard</title>
<link rel="stylesheet" href="../style.css">
<style>
 table { width: 90%; margin: 30px auto; border-collapse: collapse; }
 th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: center; }
 th { background-color: #4b2e2e; color: white; }
 td.credit { color: Gold; font-weight: bold; }
 a.book_link { text-decoration: none; color: #4b2e2e; font-weight: bold; }
 a.book_link:hover { text-decoration: underline; }
 .active { background-color:rgba(72, 173, 75, 0.65); } 
 .past { background-color:rgba(247, 56, 72, 0.69); }   
</style>
</head>
<body>
<header>
<nav>
    <div class="nav_logo">
        <a href="../home.php"><img src="../BookHive_Logo_Woody.png" alt="BookHive"></a>
    </div>
    <ul class="nav_link">
        <li><a href="dashboard.php">My Books</a></li>
        <li><a href="../exchange/exchange.php">Exchanges</a></li>
        <li><a href="credit_offers.php">Credit Offers</a></li>
        <li style="color: Brown; font-weight: bold; padding: 5px; border-radius: 8px; background-color:white">Credits: <?php echo $credits; ?></li>
    </ul>
</nav>
</header>

<main>
<h1 style="text-align:center; margin-top:30px;">My Purchased Books</h1>

<?php if(mysqli_num_rows($result) > 0): ?>
<table>
    <thead>
        <tr>
            <th>Book Name</th>
            <th>Author</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Credit Price</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = mysqli_fetch_assoc($result)): 
        $rowClass = ($today >= $row['StartDate'] && $today <= $row['EndDate']) ? 'active' : 'past';
    ?>
        <tr class="<?php echo $rowClass; ?>">
            <td>
                <a class="book_link" href="../book/book_profile.php?BookID=<?php echo $row['BookID']; ?>">
                    <?php echo htmlspecialchars($row['Title']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($row['Author']); ?></td>
            <td><?php echo htmlspecialchars($row['StartDate']); ?></td>
            <td><?php echo htmlspecialchars($row['EndDate']); ?></td>
            <td class="credit"><?php echo $row['CreditPrice']; ?> credits</td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <p style="text-align:center; margin-top:50px;">You haven't purchased any books yet.</p>
<?php endif; ?>
</main>
</body>
</html>