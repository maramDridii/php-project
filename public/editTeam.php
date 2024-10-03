<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once '../../config/database.php';

$team_id = $_GET['id'];
$query = "SELECT * FROM teams WHERE id = :team_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':team_id' => $team_id]);
$team = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Team</title>
</head>
<body>
    <h1>Edit Team</h1>
    <form method="POST" action="teamController.php?action=update">
        <input type="hidden" name="id" value="<?php echo $team['id']; ?>" />
        
        <label for="name">Team Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($team['name']); ?>" required />

        <label for="members">Members (comma-separated):</label>
        <input type="text" name="members" value="<?php echo htmlspecialchars($team['members']); ?>" required />

        <button type="submit">Update Team</button>
    </form>
</body>
</html>
