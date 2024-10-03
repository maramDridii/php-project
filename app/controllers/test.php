<?php
require_once '../../config/database.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die("Invalid ID");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
if ($stmt->execute([$id])) {
    $tournament = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($tournament) {
        header('Content-Type: application/json');
        echo json_encode($tournament);
    } else {
        echo json_encode(['error' => 'Tournament not found']);
    }
} else {
    echo json_encode(['error' => 'Query execution failed']);
}
?>
