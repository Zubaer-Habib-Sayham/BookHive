<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$userID = $_SESSION['UserID'];



$keyword = '';
$order = "ASC"; 
$sortField = "Title"; 

if (isset($_GET['search'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['search']);
}

if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ["ASC", "DESC"])) {
    $order = strtoupper($_GET['order']);
}

if (isset($_GET['sort']) && in_array($_GET['sort'], ["Title", "Author", "Genre", "BookRating", "OwnerRating"])) {
    $sortField = $_GET['sort'];
}

$sql = "SELECT b.BookID, b.Title, b.Author, b.Genre, b.Rating AS BookRating, 
               u.Name AS OwnerName, u.Rating AS OwnerRating
        FROM book b
        JOIN user u ON b.SharerID = u.UserID
        WHERE b.SharerID != $userID 
          AND b.EXID IS NULL";

if (!empty($keyword)) {
    $sql .= " AND (b.Title LIKE '%$keyword%' 
              OR b.Author LIKE '%$keyword%' 
              OR b.Genre LIKE '%$keyword%' 
              OR b.Language LIKE '%$keyword%')";
}

$sql .= " ORDER BY $sortField $order";

$books = mysqli_query($conn, $sql);
//
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BookHive Exchanges</title>
<link rel="stylesheet" href="../style.css">
<style>
 .book_table {
    width: 90%;
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
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    color: black;
 }
 button:hover {
    background-color: #e6c200;
 }
 form { margin: 0; }
 h2 { margin-top: 50px; text-align: center; }
 .search_bar {
    display: flex;
    gap: 10px;
    align-items: center;
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
<body class="home_page">

<header>
    <nav>
        <div class="nav_logo">
            <a href="../home.php"><img src="../BookHive_Logo_Woody.png" alt="BookHive"></a>
        </div>

        <form method="get" action="exchange.php" class="search_bar">
            <input type="text" name="search" placeholder="Search by Name, Author, Genre or Language"
                   value="<?php echo htmlspecialchars($keyword); ?>" class="search_input">

            <label for="sort">Sort by:</label>
            <select name="sort" id="sort" class="sort_select">
                <option value="Title" <?php if($sortField=="Title") echo "selected"; ?>>Book Name</option>
                <option value="Author" <?php if($sortField=="Author") echo "selected"; ?>>Author</option>
                <option value="Genre" <?php if($sortField=="Genre") echo "selected"; ?>>Genre</option>
                <option value="BookRating" <?php if($sortField=="BookRating") echo "selected"; ?>>Book Rating</option>
                <option value="OwnerRating" <?php if($sortField=="OwnerRating") echo "selected"; ?>>Owner Rating</option>
            </select>

            <select name="order" id="order" class="sort_select">
                <option value="ASC" <?php if($order=="ASC") echo "selected"; ?>>Ascending</option>
                <option value="DESC" <?php if($order=="DESC") echo "selected"; ?>>Descending</option>
            </select>

            <button type="submit" class="search_btn">Search</button>
        </form>

        <ul class="nav_link">
            <li><a href="../book/add_book.php">Add Books</a></li>
            <li><a href="requests.php">Requests</a></li>
            <li><a href="my_library.php">My Library</a></li>
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
    <th>Genre</th>
    <th>Rating</th>
    <th>Owner</th>
    <th>Owner Rating</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php if($books && mysqli_num_rows($books) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($books)): ?>
        <tr>
            <td><a href="../book/book_profile.php?BookID=<?php echo $row['BookID']; ?>"><?php echo htmlspecialchars($row['Title']); ?></a></td>
            <td><?php echo htmlspecialchars($row['Author']); ?></td>
            <td><?php echo htmlspecialchars($row['Genre']); ?></td>
            <td><?php echo htmlspecialchars($row['BookRating']); ?></td>
            <td><?php echo htmlspecialchars($row['OwnerName']); ?></td>
            <td><?php echo htmlspecialchars($row['OwnerRating']); ?></td>
            <td>
                <form method="get" action="exchange_form.php">
                    <input type="hidden" name="BookID" value="<?php echo $row['BookID']; ?>">
                    <button type="submit">Exchange</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="7" style="text-align:center;">No books available for exchange.</td></tr>
<?php endif; ?>
</tbody>
</table>

</main>
</body>
</html>
