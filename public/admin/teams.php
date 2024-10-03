<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../config/database.php';
include 'navbar.php';

// Fetch all team applications with tournament names
$query = "
    SELECT 
        teams.id, 
        teams.name AS team_name, 
        teams.tournament_id, 
        teams.tournament_status, 
        tournaments.name AS tournament_name 
    FROM teams 
    JOIN tournaments ON teams.tournament_id = tournaments.id 
    WHERE teams.tournament_id IS NOT NULL
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Teams Applications</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300;500;700&family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/tournaments.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <main>
        <header>
            <h1>Admin - Teams Applications</h1>
        </header>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Team Name</th>
                    <th>Tournament ID</th>
                    <th>Tournament Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['id']); ?></td>
                    <td><?php echo htmlspecialchars($application['team_name']); ?></td>
                    <td><?php echo htmlspecialchars($application['tournament_id']); ?></td>
                    <td><?php echo htmlspecialchars($application['tournament_name']); ?></td>
                    <td>
                        <form method="POST" action="update_status.php">
                            <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($application['id']); ?>">
                            <select name="status">
                                <option value="In Progress" <?php echo $application['tournament_status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Withdrawn" <?php echo $application['tournament_status'] === 'Withdrawn' ? 'selected' : ''; ?>>Withdrawn</option>
                                <option value="Accepted" <?php echo $application['tournament_status'] === 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                <option value="Rejected" <?php echo $application['tournament_status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </main>
</body>
</html>
<?php 
include '../footer.php';
?>
