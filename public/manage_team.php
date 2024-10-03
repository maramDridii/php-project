<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM teams WHERE user_id = :user_id"; 
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$team = $stmt->fetch(PDO::FETCH_ASSOC);

$tournamentsQuery = "SELECT id, name FROM tournaments";
$tournamentsStmt = $pdo->query($tournamentsQuery);
$tournaments = $tournamentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams</title>
    <link rel="stylesheet" href="css/manageTeams.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <main>
        <h1>Manage Your Team</h1>
        
        <?php if ($team): ?>
            <form method="POST" action="../app/controllers/teamController.php?action=edit">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($team['id']); ?>">
                <label for="team_name">Team Name:</label>
                <input type="text" id="team_name" name="team_name" value="<?php echo htmlspecialchars($team['name']); ?>" required>

                <label for="members">Members:</label>
                <input type="text" id="members" name="members" value="<?php echo htmlspecialchars($team['members']); ?>" required>

                <input type="submit" value="Update Team">
            </form>

            <form method="POST" action="../app/controllers/teamController.php?action=delete" style="margin-top: 20px;">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($team['id']); ?>" />
                <button class="delete" type="submit" onclick="return confirm('Are you sure you want to delete this team?');">Delete Team</button>
            </form>

            <h2>Manage Images</h2>
            <form method="POST" action="../app/controllers/teamController.php?action=uploadLogo" enctype="multipart/form-data">
                <label for="logo">Change Logo:</label>
                <input type="file" id="logo" name="logo" accept="image/*" />
                <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['id']); ?>" />
                <input type="submit" value="Upload Logo">
            </form>

            <form method="POST" action="../app/controllers/teamController.php?action=uploadImages" enctype="multipart/form-data">
                <label for="images">Add Images:</label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple />
                <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['id']); ?>" />
                <input type="submit" value="Upload Images">
            </form>

            <h3>Current Logo</h3>
            <?php if (!empty($team['logo'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($team['logo']); ?>" alt="<?php echo htmlspecialchars($team['name']); ?>" width="100" />
            <?php else: ?>
                <p>No logo available.</p>
            <?php endif; ?>

            <h3>Current Images</h3>
            <div>
                <?php 
                $images = json_decode($team['images']);
                if ($images && count($images) > 0): 
                    foreach ($images as $image): ?>
                        <div style="display: inline-block; margin: 10px;">
                            <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Team Image" width="100" />
                            <form method="POST" action="../app/controllers/teamController.php?action=deleteImage" style="display:inline;">
                                <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['id']); ?>" />
                                <input type="hidden" name="image" value="<?php echo htmlspecialchars($image); ?>" />
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this image?');">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; 
                else: ?>
                    <p>No images available.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No team found. Please create a team first.</p>
        <?php endif; ?>

        <h2>Apply for a Tournament</h2>
        <form method="POST" action="../app/controllers/teamController.php?action=apply">
            <label for="tournament_id">Select Tournament:</label>
            <select name="tournament_id" id="tournament_id" required>
                <option value="">Select a tournament</option>
                <?php foreach ($tournaments as $tournament): ?>
                    <option value="<?php echo htmlspecialchars($tournament['id']); ?>" <?php if ($tournament['id'] == $team['tournament_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($tournament['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team['id']); ?>" />
            <input type="submit" value="Apply for Tournament">
        </form>

        <h3>Your Tournament Application</h3>
        <?php if ($team['tournament_id']): ?>
            <p>
                You have applied for tournament ID: <?php echo htmlspecialchars($team['tournament_id']); ?> - 
                Status: <?php echo htmlspecialchars($team['tournament_status']); ?>
            </p>
        <?php else: ?>
            <p>No applications found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
