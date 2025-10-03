<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap"
      rel="stylesheet"
    />
    <title>BookHive</title>
  </head>
  <body class="login_page">
    <header>
      <nav>
        <div class="nav_logo">
          <a href="index.php"><img src="BookHive_Logo_Woody.png" alt="BookHive"></a>
          
        </div>
        <ul class="nav_link">
          <li><a href="signup.php">Sign Up</a><li>
        </ul>
      </nav>
    </header>
    <main>
      <section class="login">
        <div class="login_box">
          <h1>Login</h1>
          <form class="login_form" action="login.php" method="post">
            <input
              type="text"
              id="username"
              name="username"
              placeholder="username"
              required
            />
            <input
              type="password"
              id="password"
              name="password"
              placeholder="password"
              required
            />
            <input type="submit" value="Submit" />
          </form>
          <p style="text-align:center;">Don't have an account? <a href="signup.php">Sign Up here</a></p>
        </div>
      </section>
    </main>
  </body>
</html>