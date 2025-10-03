<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$userID = (int)$_SESSION['UserID'];


$creditQuery = "SELECT Credits FROM user WHERE UserID = $userID LIMIT 1";
$creditResult = mysqli_query($conn, $creditQuery);
$credits = 0;
if ($creditResult && mysqli_num_rows($creditResult) > 0) {
    $row = mysqli_fetch_assoc($creditResult);
    $credits = $row['Credits'];
}



$query = "
SELECT b.BookID, b.Title, b.Author, b.Genre, b.Rating, u.Name AS Owner, b.CreditPrice
FROM book b
JOIN user u ON b.SharerID = u.UserID
WHERE b.CreditPrice > 0
  AND b.SharerID != $userID
";

$keyword = '';
$order = "ASC"; 
$sortField = "Title"; 

if (isset($_GET['search'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['search']);
}

if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ["ASC", "DESC"])) {
    $order = strtoupper($_GET['order']);
}

if (isset($_GET['sort']) && in_array($_GET['sort'], ["Title", "Author", "Genre", "Rating", "CreditPrice"])) {
    $sortField = $_GET['sort'];
}

if (!empty($keyword)) {
    $query = " AND (b.Title LIKE '%$keyword%' 
                OR b.Author LIKE '%$keyword%' 
                OR b.Genre LIKE '%$keyword%')";
}

$query .= " ORDER BY $sortField $order";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Credit Offers - BookHive</title>
<link rel="stylesheet" href="../style.css">
<style>
 table {
    width: 90%;
    margin: 30px auto;
    border-collapse: collapse;
    border-radius: 15px;
    font-family: Arial, sans-serif;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
 }

 th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: center;
 }

 th {
    background-color: #26110D;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 0.05em;
 }

 tr{
    background-color:#4C231A;
    color: white;
 }

 tr:hover {
    background-color:rgba(134, 55, 55, 0.75);
    transition: background-color 0.3s ease;
 }

 td.credit {
    color: Gold;
    font-weight: bold;
 }

 a.book_link {
    text-decoration: none;
    color: Gold;
    font-weight: bold;
 }

 a.book_link:hover {
    text-decoration: underline;
    color: #673a3a;
 }

 .btn {
    padding: 6px 12px;
    background-color:Gold;
    color: black;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
 }
 .btn:hover {
    background-color: #673a3a;
 }

 .search_bar {
    display: flex;
    gap: 10px;
    align-items: center;
    margin: 20px auto;
    justify-content: center;
 }
 .search_input {
    padding: 5px;
    border-radius: 5px;
 }
 .sort_select {
    padding: 5px;
    border-radius: 5px;
 }
 .search_btn {
    background-color: #FFD700;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
    border-radius: 5px;
 }
 .search_btn:hover {
    background-color: #e6c200;
 }
</style>

</head>
<body>
<header>
<nav>
    <div class="nav_logo">
        <a href="../home.php"><img src="../BookHive_Logo_Woody.png" alt="BookHive"></a>
    </div>
<form method="get" action="credit_offers.php" class="search_bar">
    <input type="text" name="search" placeholder="Search Name, Author, Genre"
           value="<?php echo htmlspecialchars($keyword); ?>" class="search_input">

    <label for="sort">Sort by:</label>
    <select name="sort" id="sort" class="sort_select">
        <option value="Title" <?php if($sortField=="Title") echo "selected"; ?>>Book Name</option>
        <option value="Author" <?php if($sortField=="Author") echo "selected"; ?>>Author</option>
        <option value="Genre" <?php if($sortField=="Genre") echo "selected"; ?>>Genre</option>
        <option value="Rating" <?php if($sortField=="Rating") echo "selected"; ?>>Book Rating</option>
        <option value="CreditPrice" <?php if($sortField=="CreditPrice") echo "selected"; ?>>Credits</option>
    </select>

    <select name="order" id="order" class="sort_select">
        <option value="ASC" <?php if($order=="ASC") echo "selected"; ?>>Ascending</option>
        <option value="DESC" <?php if($order=="DESC") echo "selected"; ?>>Descending</option>
    </select>

    <button type="submit" class="search_btn">Search</button>
</form>
    <ul class="nav_link">
        <li><a href="../exchange/exchange.php">Exchanges</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="credit_offers.php">Credit Offers</a></li>
        <li style="color: Brown; font-weight: bold; padding: 5px; border-radius: 8px; background-color:white">
            Credits: <?php echo $credits; ?>
        </li>
    </ul>
</nav>
</header>

<main>
<h1 style="text-align:center; margin-top:30px;">Credit Offers</h1>

<?php if(mysqli_num_rows($result) > 0): ?>
<table>
    <thead>
        <tr>
            <th>Book Name</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Rating</th>
            <th>Owner</th>
            <th>Credit Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td>
                <a class="book_link" href="../book/book_profile.php?BookID=<?php echo $row['BookID']; ?>">
                    <?php echo htmlspecialchars($row['Title']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($row['Author']); ?></td>
            <td><?php echo htmlspecialchars($row['Genre']); ?></td>
            <td><?php echo $row['Rating'] ?? 'N/A'; ?></td>
            <td><?php echo htmlspecialchars($row['Owner']); ?></td>
            <td class="credit"><?php echo $row['CreditPrice']; ?> credits</td>
            <td>
                <form method="post" action="buy_credit_form.php">
                    <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                    <input type="submit" class="btn" value="Buy with Credits">
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <p style="text-align:center; margin-top:50px;">No credit offers available at the moment.</p>
<?php endif; ?>
</main>
</body>
</html>
