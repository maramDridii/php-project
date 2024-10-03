<?php
require_once '../app/controllers/AuthController.php';
$authController = new AuthController($pdo);
$authController->register();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css"> <!-- Use the same CSS file -->
</head>
<body>
    <div id="particles-js" class="snow"></div>
    <main>
        <div class="left-side"></div>
        <div class="right-side">
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Enter Username" required />

                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Enter Email" required />

                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Enter Password" required />

                <button type="submit" class="login-btn">Register</button>
                <div class="links">
                    <p><a href="login.php">Already have an account? Login here.</a></p>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="./js/particles.js"></script>
</body>
</html>
