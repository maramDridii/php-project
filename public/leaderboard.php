<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'navbar.php';

// Define API URLs and keys
$valorantApiUrl = 'https://dgxfkpkb4zk5c.cloudfront.net/leaderboards/affinity/AP/queue/competitive/act/97b6e739-44cc-ffa7-49ad-398ba502ceb0?startIndex=0&size=10';

// Function to fetch API data using cURL
function fetchApiData($url, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => curl_error($ch)];
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

// Fetch Valorant Leaderboard Data
$valorantLeaderboard = fetchApiData($valorantApiUrl, ["X-Riot-Token: $riotApiKey"]);

// Check if the API returned data
if (isset($valorantLeaderboard['error'])) {
    $errorMessage = "Failed to fetch leaderboard data: " . htmlspecialchars($valorantLeaderboard['error']);
    $valorantLeaderboard = [];
} elseif (empty($valorantLeaderboard['players'])) {
    $errorMessage = "No data returned. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300;500;700&family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/leaderboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <main>
        <header>
            <h1>Valorant Leaderboard</h1>
        </header>

        <section>
            <?php if (empty($valorantLeaderboard)): ?>
                <p><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Rank</th>
                            <th>Score</th>
                            <th>Wins</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($valorantLeaderboard['players'] as $player): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($player['gameName'] . " #" . $player['tagLine']); ?></td>
                            <td><?php echo htmlspecialchars($player['leaderboardRank']); ?></td>
                            <td><?php echo htmlspecialchars($player['rankedRating']); ?></td>
                            <td><?php echo htmlspecialchars($player['numberOfWins']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
<?php 
include 'footer.php';
?>