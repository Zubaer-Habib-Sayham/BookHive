<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BookHive Sign Up</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login_page">
<header>
      <nav>
        <div class="nav_logo">
          <a href="index.php"><img src="BookHive_Logo_Woody.png" alt="BookHive"></a>
          
        </div>
        <ul class="nav_link">
          <li><a href="index.php">Log In</a><li>
        </ul>
      </nav>
</header>
<main>
    <section class="login">
        <div class="login_box">
            <h1>Sign Up</h1>
            <form class="login_form" action="signup_connect.php" method="post">
                <input type="text" name="name" placeholder="Username" required>
                <input type="text" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="address" placeholder="Address" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="submit" value="Sign Up">
            </form>
            <p style="text-align:center;">Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </section>
</main>
</body>
</html>
