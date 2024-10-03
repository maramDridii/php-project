<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../config/database.php';
$results_per_page = 7;

$total_query = "SELECT COUNT(*) AS total FROM tournaments";
$total_stmt = $pdo->prepare($total_query);
$total_stmt->execute();
$total_result = $total_stmt->fetch(PDO::FETCH_ASSOC);
$total_tournaments = $total_result['total'];

$number_of_pages = ceil($total_tournaments / $results_per_page);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_limit = ($page - 1) * $results_per_page;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchCondition = $search ? "WHERE t.name LIKE :search" : '';

$query = "SELECT t.id, t.name AS tournament_name, t.date, t.status, g.name AS game_name, t.image
          FROM tournaments t
          JOIN games g ON t.game_id = g.id
          $searchCondition
          ORDER BY t.date ASC
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
if ($search) {
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $results_per_page, PDO::PARAM_INT);
$stmt->bindParam(':offset', $start_limit, PDO::PARAM_INT);
$stmt->execute();
$tournaments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tournaments</title>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@300;500;700&family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/tournaments.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/footer.css">


    <style>
        .modal {
            display: none;
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
        }
        .modal-content {
            background-color: #919c8e;
            margin: 10% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-content img {
            max-width: 100%; 
            height: auto; 
            display: block; 
            margin: 0 auto; 
        }
    </style>
</head>
<body>
    <main>
        <header>
            <h1>Admin - Manage Tournaments</h1>
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Tournament name..." value="<?php echo htmlspecialchars($search); ?>" />
                <input type="hidden" name="page" value="<?php echo $page; ?>" />
                <button type="submit">Search</button>
            </form>
        </header>
        <button id="addTournamentBtn">Add Tournament</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Game</th>
                    <th>Tournament Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tournaments as $tournament): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tournament['id']); ?></td>
                        <td>
                            <?php if (!empty($tournament['image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($tournament['image']); ?>" alt="<?php echo htmlspecialchars($tournament['tournament_name']); ?>" width="50" />
                            <?php else: ?>
                                <img src="../uploads/default.png" alt="No Image" width="50" />
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($tournament['game_name']); ?></td>
                        <td><?php echo htmlspecialchars($tournament['tournament_name']); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($tournament['date']))); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($tournament['status'])); ?></td>
                        <td>
                            <button onclick="openEditModal(<?php echo $tournament['id']; ?>)">Edit</button>
                            <form method="POST" action="../../app/controllers/TourController.php?action=delete" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $tournament['id']; ?>" />
                                <button type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php if ($number_of_pages > 1): ?>
                <?php for ($i = 1; $i <= $number_of_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>

        <div id="addTournamentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h2>Add Tournament</h2>
                <form method="POST" action="../../app/controllers/TourController.php?action=save" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="" />
                    <label for="game_id">Game:</label>
                    <select name="game_id" required>
                        <?php
                        $gamesQuery = "SELECT id, name FROM games";
                        $gamesStmt = $pdo->query($gamesQuery);
                        $games = $gamesStmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($games as $game): ?>
                            <option value="<?php echo $game['id']; ?>">
                                <?php echo htmlspecialchars($game['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="name">Tournament Name:</label>
                    <input type="text" name="name" required />
                    <label for="date">Date:</label>
                    <input type="datetime-local" name="date" required />
                    <label for="image">Tournament Image:</label>
                    <input type="file" name="image" accept="image/*" required />
                    <button type="submit">Save Tournament</button>
                </form>
            </div>
        </div>

        <div id="editTournamentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h2>Edit Tournament</h2>
                <form id="editTournamentForm" method="POST" action="../../app/controllers/TourController.php?action=save" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editTournamentId" value="" />
                    <label for="edit_game_id">Game:</label>
                    <select name="game_id" id="edit_game_id" required>
                        <?php foreach ($games as $game): ?>
                            <option value="<?php echo $game['id']; ?>"><?php echo htmlspecialchars($game['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="edit_name">Tournament Name:</label>
                    <input type="text" name="name" id="edit_name" required />
                    <label for="edit_date">Date:</label>
                    <input type="datetime-local" name="date" id="edit_date" required />
                    <label for="edit_image">Tournament Image:</label>
                    <input type="file" name="image" accept="image/*" />
                    <button type="submit">Update Tournament</button>
                </form>
                <p>Current Image:</p>
                <img id="editCurrentImage" src="" alt="Current Tournament Image" width="100" />
            </div>
        </div>
    </main>

    <script>
    document.getElementById('addTournamentBtn').onclick = function() {
        document.getElementById('addTournamentModal').style.display = "block";
    }

    function closeAddModal() {
        document.getElementById('addTournamentModal').style.display = "none";
    }

    function openEditModal(tournamentId) {
        fetch(`../../app/controllers/TourController.php?action=get&id=${tournamentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('editTournamentId').value = data.id;
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_date').value = data.date;
                document.getElementById('editCurrentImage').src = `../uploads/${data.image}`;
                document.getElementById('edit_game_id').value = data.game_id; 
                document.getElementById('editTournamentModal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching tournament data:', error);
            });
    }

    function closeEditModal() {
        document.getElementById('editTournamentModal').style.display = "none";
    }

    window.onclick = function(event) {
       if (event.target.classList.contains('modal')) {
           if (event.target.id === 'addTournamentModal') {
               closeAddModal();
            } else if (event.target.id === 'editTournamentModal') {
                closeEditModal();
            }
        }
    }
    </script>

</body>
</html>
<?php 
include '../footer.php';
?>
