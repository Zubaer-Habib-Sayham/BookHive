<?php
require_once('DBconnect.php');
session_start();


if (!isset($_SESSION['UserID'])) {
   header("Location: index.php");
   exit();
}


$userID = $_SESSION['UserID'];
$today = date('Y-m-d');

$creditQuery = "SELECT Credits FROM user WHERE UserID = $userID LIMIT 1";
$creditResult = mysqli_query($conn, $creditQuery);
$credits = 0;
if ($creditResult && mysqli_num_rows($creditResult) > 0) {
    $row = mysqli_fetch_assoc($creditResult);
    $credits = $row['Credits'];
}



$pendingReviewCheck = "
SELECT 1
FROM exchange e
WHERE e.End_Date <= '$today'
  AND e.Completed = 1
  AND e.Canceled = 0
  AND (
       (e.User1ID = $userID AND NOT EXISTS (SELECT 1 FROM user_rating r WHERE r.ExID = e.ExID AND r.User1ID = $userID))
    OR (e.User2ID = $userID AND NOT EXISTS (SELECT 1 FROM user_rating r WHERE r.ExID = e.ExID AND r.User1ID = $userID))
    OR (e.User1ID = $userID AND NOT EXISTS (SELECT 1 FROM book_rating br WHERE br.ExID = e.ExID AND br.UserID = $userID))
    OR (e.User2ID = $userID AND NOT EXISTS (SELECT 1 FROM book_rating br WHERE br.ExID = e.ExID AND br.UserID = $userID))
  )
LIMIT 1;
";
;


$checkResult = mysqli_query($conn, $pendingReviewCheck);



if(mysqli_num_rows($checkResult) > 0){
   header("Location: exchange/review_form.php");
   exit();
}



$keyword = '';
$order = "ASC"; 
$sortField = "Title"; 


if (isset($_GET['search'])) {
   $keyword = strtolower(mysqli_real_escape_string($conn, $_GET['search']));
}
if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), ["ASC", "DESC"])) {
   $order = strtoupper($_GET['order']);
}
if (isset($_GET['sort']) && in_array($_GET['sort'], ["Title", "Author", "Genre"])) {
   $sortField = $_GET['sort'];
}



if (!empty($keyword)) {
   $sql = "SELECT BookID, Title, Author, Genre, Rating
           FROM book
           WHERE (
               LOWER(Title) LIKE '%$keyword%'
               OR LOWER(Author) LIKE '%$keyword%'
               OR LOWER(Genre) LIKE '%$keyword%'
               OR LOWER(Language) LIKE '%$keyword%'
           )
           AND ExID IS NULL
           ORDER BY LEFT($sortField, 1) $order, $sortField $order";
} else {
   $sql = "SELECT BookID, Title, Author, Genre, Rating
           FROM book
           WHERE ExID IS NULL
           ORDER BY LEFT($sortField, 1) $order, $sortField $order";
}


$books = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BookHive Home</title>
<link rel="stylesheet" href="style.css">
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
 .sort-controls {
   text-align: center;
   margin: 15px;
 }
 .sort-controls select, .sort-controls button {
   padding: 6px;
   margin: 0 5px;
 }
</style>
</head>
<body class="home_page">


<header>
   <nav>
       <div class="nav_logo">
           <a href="home.php"><img src="BookHive_Logo_Woody.png" alt="BookHive"></a>
       </div>



<form method="get" action="home.php" class="search_bar">
   <input type="text" name="search" placeholder="Search by Name, Author or Genre"
          value="<?php echo htmlspecialchars($keyword); ?>" class="search_input">
  
   <label for="sort" class="sort_label">Sort by:</label>
   <select name="sort" id="sort" class="sort_select">
       <option value="Title" <?php if($sortField=="Title") echo "selected"; ?>>Book Name</option>
       <option value="Author" <?php if($sortField=="Author") echo "selected"; ?>>Author</option>
       <option value="Genre" <?php if($sortField=="Genre") echo "selected"; ?>>Genre</option>
   </select>

   <select name="order" id="order" class="sort_select">
       <option value="ASC" <?php if($order=="ASC") echo "selected"; ?>>Ascending</option>
       <option value="DESC" <?php if($order=="DESC") echo "selected"; ?>>Descending</option>
   </select>

   <button type="submit" class="search_btn">Search</button>
</form>


<style>

 .search_bar {
   display: flex;
   flex-wrap: wrap;
   align-items: center;
   justify-content: flex-start;
   gap: 10px;
   margin: 15px 0;
 }


 .search_input {
   padding: 6px 10px;
   border-radius: 8px;
   border: 1px solid #ccc;
   flex: 1;
   min-width: 200px;
 }


 .sort_label {
   font-weight: bold;
   margin-left: 5px;
 }


 .sort_select {
   padding: 6px 8px;
   border-radius: 8px;
   border: 1px solid #ccc;
   background-color:white;
 }


 .search_btn {
   padding: 6px 12px;
   border-radius: 8px;
   border: none;
   background-color: #4B2E2E;
   color: white;
   cursor: pointer;
   transition: background-color 0.2s;
 }


 .search_btn:hover {
   background-color: #3a1f1f;
 }




</style>

       <ul class="nav_link">
           <li><a href="book/add_book.php">Add Books</a></li>
           <li><a href="exchange/exchange.php">Exchanges</a></li>
           <li><a href="credit_offers/credit_offers.php">Credit Offers</a></li>
           <li style="color: Brown; font-weight: bold; padding: 5px; border-radius: 8px; background-color:white">Credits: <?php echo $credits; ?></li>
           <li><a href="logout.php">Log Out</a></li>
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
           </tr>
       </thead>
       <tbody>
           <?php if($books && mysqli_num_rows($books) > 0): ?>
               <?php while($row = mysqli_fetch_assoc($books)): ?>
                   <tr>
                       <td>
                           <a href="book/book_profile.php?BookID=<?php echo $row['BookID']; ?>">
                               <?php echo htmlspecialchars($row['Title']); ?>
                           </a>
                       </td>
                       <td><?php echo htmlspecialchars($row['Author']); ?></td>
                       <td><?php echo htmlspecialchars($row['Genre']); ?></td>
                       <td><?php echo htmlspecialchars($row['Rating']); ?></td>
                   </tr>
               <?php endwhile; ?>
           <?php else: ?>
               <tr>
                   <td colspan="4" style="text-align:center;">No available books right now.</td>
               </tr>
           <?php endif; ?>
       </tbody>
   </table>
</main>
</body>
</html>