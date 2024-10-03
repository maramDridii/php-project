<?php
session_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php'; 

$query = $query = "SELECT t.id, t.name AS team_name, tor.name AS tournament_name, t.members, t.status
                   FROM teams t
                   JOIN tournaments tor ON t.tournament_id = tor.id
                   WHERE t.user_id = :user_id"; 
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Teams</title>
</head>
<body>
    <h1>Your Teams</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Team Name</th>
                <th>Tournament</th>
                <th>Members</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teams as $team): ?>
                <tr>
                    <td><?php echo htmlspecialchars($team['id']); ?></td>
                    <td><?php echo htmlspecialchars($team['team_name']); ?></td>
                    <td><?php echo htmlspecialchars($team['tournament_name']); ?></td>
                    <td><?php echo htmlspecialchars($team['members']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($team['status'])); ?></td>
                    <td>
                        <a href="editTeam.php?id=<?php echo $team['id']; ?>">Edit</a>
                        <a href="teamController.php?action=delete&id=<?php echo $team['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
