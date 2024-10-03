<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['team_id']) && isset($_POST['status'])) {
        $team_id = intval($_POST['team_id']);
        $status = htmlspecialchars(trim($_POST['status']));

        if ($team_id > 0 && !empty($status)) {
            $query = "UPDATE teams SET tournament_status = :status WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['status' => $status, 'id' => $team_id]);

            $_SESSION['message'] = 'Status updated successfully.';
        } else {
            $_SESSION['message'] = 'Invalid input.';
        }
    } else {
        $_SESSION['message'] = 'Missing parameters.';
    }
    header("Location: ../../public/admin/teams.php");
    exit();
}
