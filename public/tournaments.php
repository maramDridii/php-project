<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php'; 

// Pagination settings
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Calculate offset

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchCondition = $search ? "WHERE t.name LIKE :search" : '';

// Total tournaments count with search
$totalQuery = "SELECT COUNT(*) as total FROM tournaments t $searchCondition";
$totalStmt = $pdo->prepare($totalQuery);
if ($search) {
    $searchParam = "%" . $search . "%";
    $totalStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}
$totalStmt->execute();
$totalCount = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalCount / $limit); // Calculate total pages

// Main query with pagination and search
$query = "SELECT t.id, t.name AS tournament_name, t.date, t.status, g.name AS game_name, t.image
          FROM tournaments t
          JOIN games g ON t.game_id = g.id
          $searchCondition
          ORDER BY t.date ASC
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournaments</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300;500;700&family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/tournaments.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <main>
    <header>
    <h1>Tournaments List</h1>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Tournament name..." value="<?php echo htmlspecialchars($search); ?>" />
        <input type="hidden" name="page" value="<?php echo $page; ?>" />
        <button type="submit">Search</button>
    </form>
</header>

        <section>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Game</th>
                        <th>Tournament Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tournaments as $tournament): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tournament['id']); ?></td>
                        <td>
                            <?php if (!empty($tournament['image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($tournament['image']); ?>" alt="<?php echo htmlspecialchars($tournament['tournament_name']); ?>" width="50" />
                            <?php else: ?>
                                <img src="uploads/default.png" alt="No Image" width="50" />
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($tournament['game_name']); ?></td>
                        <td><?php echo htmlspecialchars($tournament['tournament_name']); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($tournament['date']))); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($tournament['status'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        
        <!-- Pagination Controls -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">&laquo; Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php 
include 'footer.php';
?>
