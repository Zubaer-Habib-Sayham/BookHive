<?php
require_once('../DBconnect.php');
session_start();

if (!isset($_SESSION['UserID'])) {
    header("Location: ../index.php");
    exit();
}

$userID = $_SESSION['UserID'];
$today = date('Y-m-d');

$query = "
SELECT e.ExID, e.User1ID, e.User2ID, e.Book1ID, e.Book2ID,
       u1.Name AS User1Name, u2.Name AS User2Name,
       b1.Title AS Book1Title, b2.Title AS Book2Title
FROM exchange e
JOIN user u1 ON e.User1ID = u1.UserID
JOIN user u2 ON e.User2ID = u2.UserID
JOIN book b1 ON e.Book1ID = b1.BookID
JOIN book b2 ON e.Book2ID = b2.BookID
WHERE DATE(e.End_Date) <= CURDATE()
  AND e.Completed = 1
  AND e.Canceled = 0
  AND ($userID = e.User1ID OR $userID = e.User2ID) 
  AND NOT EXISTS (
      SELECT 1
      FROM user_rating r
      WHERE r.ExID = e.ExID AND r.User1ID = $userID
  )
ORDER BY e.ExID ASC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review Form</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>

<main>
<?php if(mysqli_num_rows($result) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)): 
        $exID = $row['ExID'];

        $otherUserID = ($row['User1ID'] == $userID) ? $row['User2ID'] : $row['User1ID'];
        $otherUserName = ($row['User1ID'] == $userID) ? $row['User2Name'] : $row['User1Name'];

        $bookID = ($row['User1ID'] == $userID) ? $row['Book2ID'] : $row['Book1ID'];
        $bookTitle = ($row['User1ID'] == $userID) ? $row['Book2Title'] : $row['Book1Title'];
    ?>
        <div class="login_box">
            <h1>Review <?php echo htmlspecialchars($otherUserName); ?> & their book: "<?php echo htmlspecialchars($bookTitle); ?>"</h1>
            <form class="login_form" method="POST" action="submit_review.php">
                <input type="hidden" name="ExID" value="<?php echo $exID; ?>">
                <input type="hidden" name="OtherUserID" value="<?php echo $otherUserID; ?>">
                <input type="hidden" name="BookID" value="<?php echo $bookID; ?>">

                <label>User Rating (1-5):</label>
                <input type="number" name="UserRating" min="1" max="5" required>

                <label>User Review:</label>
                <textarea name="UserReview" placeholder="Optional"></textarea>

                <label>Book Rating (1-5):</label>
                <input type="number" name="BookRating" min="1" max="5" required>

                <label>Book Review:</label>
                <textarea name="BookReview" placeholder="Optional"></textarea>

                <input type="submit" class="btn" value="Submit Review">
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center; margin-top:50px;">No reviews pending.</p>
<?php endif; ?>
</main>

</body>
</html>
