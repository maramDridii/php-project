<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once '../../config/database.php';

$tournamentsQuery = "SELECT id, name FROM tournaments WHERE status = 'upcoming'";
$tournamentsStmt = $pdo->query($tournamentsQuery);
$tournaments = $tournamentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Team</title>
</head>
<body>
    <h1>Register Team</h1>
    <form method="POST" action="teamController.php?action=register">
        <label for="name">Team Name:</label>
        <input type="text" name="name" required />

        <label for="members">Members (comma-separated):</label>
        <input type="text" name="members" required />

        <label for="tournament_id">Select Tournament:</label>
        <select name="tournament_id" required>
            <?php foreach ($tournaments as $tournament): ?>
                <option value="<?php echo $tournament['id']; ?>">
                    <?php echo htmlspecialchars($tournament['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Register Team</button>
    </form>
</body>
</html>
