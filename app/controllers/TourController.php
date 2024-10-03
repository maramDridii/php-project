<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die("You are not logged in.");
}

if ($_SESSION['role'] !== 'admin') {
    die("You are not authorized to access this page.");
}

require_once '../../config/database.php';

class TourController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createTournament($game_id, $name, $date, $status, $image) {
        $stmt = $this->pdo->prepare("INSERT INTO tournaments (game_id, name, date, status, image) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$game_id, $name, $date, $status, $image]);
    }

    public function readTournaments() {
        $stmt = $this->pdo->query("SELECT * FROM tournaments ORDER BY date");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTournament($id, $game_id, $name, $date, $image) {
        // Update the tournament first
        $stmt = $this->pdo->prepare("UPDATE tournaments SET game_id = ?, name = ?, date = ?, image = ? WHERE id = ?");
        $stmt->execute([$game_id, $name, $date, $image, $id]);
    
        // Now, update the status based on the new date
        $newStatus = $this->generateStatus($date);
        $statusStmt = $this->pdo->prepare("UPDATE tournaments SET status = ? WHERE id = ?");
        $statusStmt->execute([$newStatus, $id]);
    }
    

    public function deleteTournament($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tournaments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
            if ($_GET['action'] === 'save') {
                if ($_SESSION['role'] !== 'admin') {
                    header("HTTP/1.1 403 Forbidden");
                    exit();
                }

                $id = $_POST['id'] ?? null;
                $game_id = $_POST['game_id'];
                $name = $_POST['name'];
                $date = $_POST['date'];
                $status = $this->generateStatus($date);
                
                // Check if a new image is uploaded; if not, retrieve the existing image
                $image = null;
                if (!empty($_FILES['image']['name'])) {
                    $image = $this->uploadImage($_FILES['image']);
                } else {
                    // If no new image is uploaded, keep the existing one
                    $existingQuery = "SELECT image FROM tournaments WHERE id = ?";
                    $existingStmt = $this->pdo->prepare($existingQuery);
                    $existingStmt->execute([$id]);
                    $existingImage = $existingStmt->fetch(PDO::FETCH_ASSOC)['image'];
                    $image = $existingImage; // Keep the existing image
                }

                if ($id) {
                    $this->updateTournament($id, $game_id, $name, $date, $status, $image);
                } else {
                    $this->createTournament($game_id, $name, $date, $status, $image);
                }

                header("Location: ../../public/admin/tournaments.php");
                exit();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
            if ($_GET['action'] === 'get') {
                $this->fetchTournament();
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Invalid request']);
            exit();
        }
    }

    private function fetchTournament() {
        $id = $_GET['id'] ?? null;

        if (!isset($id) || !filter_var($id, FILTER_VALIDATE_INT)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Invalid ID']);
            exit();
        }

        $stmt = $this->pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
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
        exit();
    }

    private function generateStatus($date) {
        $currentDate = new DateTime();
        $tournamentDate = new DateTime($date);
        
        if ($tournamentDate > $currentDate) {
            return 'upcoming';
        } elseif ($tournamentDate < $currentDate) {
            return 'completed';
        } else {
            return 'ongoing';
        }
    }

    private function uploadImage($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null; // Handle the error accordingly
        }

        $targetDir = "../../public/uploads/";
        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType; // Generate a unique file name
        $targetFile = $targetDir . $newFileName;

        // Check if image file is a valid image type (optional)
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            die("Error uploading the file.");
        }

        return $newFileName; // Return only the file name
    }
}

$controller = new TourController($pdo);
$controller->handleRequest();
