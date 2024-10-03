<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Pagination
$perPage = 10; // Number of teams per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Query to get teams
$query = "SELECT id, name, members, logo, images, status FROM teams LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user has their own team
$userTeamsQuery = "SELECT COUNT(*) FROM teams WHERE user_id = :user_id";
$userTeamsStmt = $pdo->prepare($userTeamsQuery);
$userTeamsStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$userTeamsStmt->execute();
$userHasTeam = $userTeamsStmt->fetchColumn() > 0;

$totalTeamsQuery = "SELECT COUNT(*) FROM teams";
$totalTeamsStmt = $pdo->prepare($totalTeamsQuery);
$totalTeamsStmt->execute();
$totalTeams = $totalTeamsStmt->fetchColumn();
$totalPages = ceil($totalTeams / $perPage);

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Teams</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300;500;700&family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/teams.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <main>
        <header>
            <h1>Teams</h1>
            <div class="button-container">
                <?php if (!$userHasTeam): ?>
                <button class="action-button"><a href="add_team.php">Add one</a></button>
                <?php else: ?>
                <button class="action-button"><a href="manage_team.php?id=<?php echo $teams[0]['id']; ?>">Manage your team</a></button>
                <?php endif; ?>
            </div>
        </header>
        <section>
            <div class="teams-container">
                <?php foreach ($teams as $team): ?>
                    <div class="team-card" style="background-image: url('uploads/<?php echo htmlspecialchars(json_decode($team['images'])[0] ?? 'default.jpg'); ?>');">
                        <div class="overlay"></div>
                        <div class="team-header">
                            <?php if (!empty($team['logo'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($team['logo']); ?>" alt="Team Logo" class="team-logo" />
                            <?php endif; ?>
                            <h2><?php echo htmlspecialchars($team['name']); ?></h2>
                        </div>
                        <p><strong>Members:</strong>
                            <?php 
                            $members = json_decode($team['members']) ?? [];
                            if (is_array($members) && !empty($members)) {
                                echo implode(', ', array_map('htmlspecialchars', $members));
                            } else {
                                echo "No members listed.";
                            }
                            ?>
                        </p>
                        <div class="image-carousel">
                            <?php 
                            $images = json_decode($team['images']) ?? [];
                            foreach ($images as $image): ?>
                                <img src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Team Image" class="carousel-image" />
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination">
                <?php if ($totalPages > 1): ?>
                    <ul>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li><a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a></li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        $(document).ready(function() {
            $('.team-card').each(function() {
                const $this = $(this);
                const $images = $this.find('.carousel-image');
                if ($images.length > 0) {
                    $images.eq(0).show();
                }
                let index = 0;
                if ($images.length > 1) {
                    setInterval(function() {
                        index = (index + 1) % $images.length;
                        $images.hide().eq(index).show();
                    }, 3000);
                }
            });
        });
    </script>
</body>
</html>
<?php 
include 'footer.php';
?>