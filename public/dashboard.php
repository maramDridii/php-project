<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300;500;700&family=Montserrat:wght@500&family=Space+Mono:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>


</head>
<body>
<div id="particles-js" style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; z-index: -1;"></div>


<main>
    <!--<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>This is your dashboard. Navigate through the links provided.</p>-->
    <section class="features-highlight">
    <h2>Explore</h2>
    <div class="features-container">
    <li><a href="tournaments.php">
        <div class="feature-card tournaments">
            <h3>Tournaments</h3>
            <p>Join exciting tournaments and showcase your skills. Compete for amazing prizes!</p>
        </div>
        </a></li>
        <li><a href="teams.php">
        <div class="feature-card teams">
            <h3>Teams</h3>
            <p>Connect with fellow gamers, form teams, and strategize for victories together!</p>
        </div>
        </a></li>
        <li><a href="leaderboard.php">
        <div class="feature-card statics">
            <h3>Leaderboard and Statics</h3>
            <p>Track your performance and analyze stats to improve your gameplay.</p>
        </div>
        </a></li>
    </div>
</section>

    <h1>GAMES LIST</h1>
    <div class="games-container">
        <div class="game-card" style="margin-top: 30px">
            <img src="images/valorant.jpg" alt="Valorant" />
        </div>
        <div class="game-card" style="height: 550px; width: 350px; margin-top : -18px;">
            <img src="images/lol.webp" alt="LOL" />
        </div>
        <div class="game-card">
            <img src="images/fortnite.jpg" alt="Fortnite" />
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="./js/particles.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/three@0.153.0/build/three.min.js"></script>
<script src="./js/threemin.js"></script>-->
</body>
</html>
<?php 
include 'footer.php';
?>
