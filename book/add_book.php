<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../DBconnect.php'); 
session_start();

if (!isset($_SESSION['UserID'])) {
   header("Location: ../index.php");
   exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (
       isset($_POST['title']) &&
       isset($_POST['author']) &&
       isset($_POST['language']) &&
       isset($_POST['genre']) &&
       isset($_POST['description']) &&
       isset($_POST['condition'])
   ) {
       $title = mysqli_real_escape_string($conn, $_POST['title']);
       $author = mysqli_real_escape_string($conn, $_POST['author']);
       $language = mysqli_real_escape_string($conn, $_POST['language']);
       $genre = mysqli_real_escape_string($conn, $_POST['genre']);
       $description = mysqli_real_escape_string($conn, $_POST['description']);
       $condition = mysqli_real_escape_string($conn, $_POST['condition']);
       $sharerID = $_SESSION['UserID'];

       $credit_price = isset($_POST['credit_price']) && is_numeric($_POST['credit_price']) 
                       ? intval($_POST['credit_price']) 
                       : 0;
       if ($credit_price < 0) $credit_price = 0;

       $sql = "INSERT INTO book (Title, Author, Language, Genre, Description, `Condition`, SharerID, CreditPrice)
               VALUES ('$title', '$author', '$language', '$genre', '$description', '$condition', '$sharerID', $credit_price)";

       $result = mysqli_query($conn, $sql);

       if ($result && mysqli_affected_rows($conn) > 0) {
           header("Location: ../home.php");
           exit();
       } else {
           echo "Error inserting book: " . mysqli_error($conn);
       }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Book - BookHive</title>
   <link rel="stylesheet" href="../style.css"> 
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
   <section class="login_box">
       <h1>Add a New Book</h1>
       <form method="post" class="login_form">
           <input type="text" name="title" placeholder="Book Title" required>
           <input type="text" name="author" placeholder="Author" required>
           <input type="text" name="language" placeholder="Language" required>
           <input type="text" name="genre" placeholder="Genre" required>
           <input type="text" name="description" placeholder="Book Description" rows="4">
           <select style="padding: 10px; margin-bottom:10px; border-radius:4px" name="condition" required>
               <option value="">Select Book Condition</option>
               <option value="New">New</option>
               <option value="Good">Good</option>
               <option value="Old">Old</option>
           </select>
           <input type="number" style="padding: 10px; margin-bottom:10px; border-radius:4px" name="credit_price" placeholder="Credit Price (optional)" min="0">
           <input type="submit" value="Add Book">
       </form>
   </section>
</main>
</body>
</html>
