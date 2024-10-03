<?php
session_start(); 
require_once '../app/controllers/AuthController.php';
$authController = new AuthController($pdo);
$authController->login();
$username = '';
if (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div id="particles-js" class="snow"></div>
    <main>
        <div class="left-side"></div>
        <div class="right-side">
            <form method="POST">
                <div class="btn-group">
                    
                </div>


                <label for="username">Username</label>
                <input type="text" name="username" placeholder="Enter Username" required value="<?php echo htmlspecialchars($username); ?>" />

                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Enter Password" required />

                <button type="submit" class="login-btn">Login</button>
                <div class="links">
                    <!--<a href="#">Forgot password?</a>-->
                    <a href="register.php">Don't have an account? Register here.</a>
                </div>
            </form>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="./js/particles.js"></script>


    
</body>
</html>
