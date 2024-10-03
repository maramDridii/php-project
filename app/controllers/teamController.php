<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/database.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['message'] = 'Unauthorized access.';
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'edit':
            editTeam($pdo);
            break;
        case 'register':
            registerTeam($pdo);
            break;
        case 'apply':
            applyForTournament($pdo);
            break;
        case 'uploadLogo':
            uploadLogo($pdo);
            break;
        case 'uploadImages':
            uploadImages($pdo);
            break;
        case 'delete':
            deleteTeam($pdo);
            break;
        case 'deleteImage':
            deleteImage($pdo);
            break;
        default:
            $_SESSION['message'] = 'Invalid action.';
            header("Location: ../../public/manage_team.php");
            exit();
    }
}

function applyForTournament($pdo) {
    if (!isset($_POST['team_id']) || !isset($_POST['tournament_id'])) {
        $_SESSION['message'] = 'Team ID or Tournament ID not set.';
        header("Location: ../../public/manage_team.php");
        exit();
    }

    $team_id = intval($_POST['team_id']);
    $tournament_id = intval($_POST['tournament_id']);

    if ($team_id <= 0 || $tournament_id <= 0) {
        $_SESSION['message'] = 'Invalid team ID or tournament ID.';
        header("Location: ../../public/manage_team.php");
        exit();
    }

    $query = "SELECT tournament_id FROM teams WHERE id = :team_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['team_id' => $team_id]);
    $existingApplication = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingApplication) {
        $withdrawQuery = "UPDATE teams SET tournament_id = NULL, tournament_status = 'Withdrawn' WHERE id = :team_id";
        $withdrawStmt = $pdo->prepare($withdrawQuery);
        $withdrawStmt->execute(['team_id' => $team_id]);
    }

    $updateQuery = "UPDATE teams SET tournament_id = :tournament_id, tournament_status = 'In Progress' WHERE id = :team_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute(['tournament_id' => $tournament_id, 'team_id' => $team_id]);

    $_SESSION['message'] = 'Successfully applied for tournament.';
    header("Location: ../../public/manage_team.php");
    exit();
}

function uploadLogo($pdo) {
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $team_id = intval($_POST['team_id']);
        $logoPath = uploadFile($_FILES['logo']);

        if ($logoPath) {
            $query = "UPDATE teams SET logo = :logo WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['logo' => basename($logoPath), 'id' => $team_id]);
            $_SESSION['message'] = 'Logo uploaded successfully.';
        } else {
            $_SESSION['message'] = 'Failed to upload logo.';
        }
    } else {
        $_SESSION['message'] = 'No logo uploaded.';
    }
    header("Location: ../../public/manage_team.php");
    exit();
}

function uploadImages($pdo) {
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $team_id = intval($_POST['team_id']);
        $imagePaths = uploadMultipleFiles($_FILES['images']);

        if ($imagePaths) {
            $query = "SELECT images FROM teams WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $team_id]);
            $team = $stmt->fetch(PDO::FETCH_ASSOC);

            $currentImages = json_decode($team['images'], true) ?? [];
            $newImages = array_merge($currentImages, json_decode($imagePaths, true));
            $updatedImages = json_encode($newImages);

            $query = "UPDATE teams SET images = :images WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['images' => $updatedImages, 'id' => $team_id]);

            $_SESSION['message'] = 'Images uploaded successfully.';
        } else {
            $_SESSION['message'] = 'Failed to upload images.';
        }
    } else {
        $_SESSION['message'] = 'No images uploaded.';
    }
    header("Location: ../../public/manage_team.php");
    exit();
}

function deleteImage($pdo) {
    if (isset($_POST['team_id']) && isset($_POST['image'])) {
        $team_id = intval($_POST['team_id']);
        $image_to_delete = htmlspecialchars(trim($_POST['image']));

        if ($team_id > 0 && !empty($image_to_delete)) {
            $query = "SELECT images FROM teams WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $team_id]);
            $team = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($team) {
                $images = json_decode($team['images'], true);
                if (!is_array($images)) {
                    $_SESSION['message'] = 'Error decoding images from database.';
                    header("Location: ../../public/manage_team.php");
                    exit();
                }

                if (($key = array_search($image_to_delete, $images)) !== false) {
                    unset($images[$key]);
                    $updatedImages = json_encode(array_values($images));

                    $updateQuery = "UPDATE teams SET images = :images WHERE id = :id";
                    $updateStmt = $pdo->prepare($updateQuery);
                    $updateStmt->execute(['images' => $updatedImages, 'id' => $team_id]);

                    $imagePath = '../../public/uploads/' . $image_to_delete;
                    if (file_exists($imagePath) && unlink($imagePath)) {
                        $_SESSION['message'] = 'Image deleted successfully.';
                    } else {
                        $_SESSION['message'] = 'Error deleting image from filesystem.';
                    }
                } else {
                    $_SESSION['message'] = 'Image not found in the database.';
                }
            } else {
                $_SESSION['message'] = 'Team not found.';
            }
        } else {
            $_SESSION['message'] = 'Invalid team ID or image.';
        }
    } else {
        $_SESSION['message'] = 'Team ID or image not set.';
    }
    header("Location: ../../public/manage_team.php");
    exit();
}

function deleteTeam($pdo) {
    $team_id = intval($_POST['id']);

    if ($team_id > 0) {
        $query = "SELECT images, logo FROM teams WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $team_id]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($team) {
            if ($team['logo']) {
                $logoPath = '../../public/uploads/' . $team['logo'];
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }

            if ($team['images']) {
                $images = json_decode($team['images'], true);
                foreach ($images as $image) {
                    $imagePath = '../../public/uploads/' . $image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            $deleteQuery = "DELETE FROM teams WHERE id = :id";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->execute(['id' => $team_id]);

            if ($deleteStmt->rowCount() > 0) {
                $_SESSION['message'] = 'Team deleted successfully.';
            } else {
                $_SESSION['message'] = 'No team found with the given ID, deletion failed.';
            }
        } else {
            $_SESSION['message'] = 'Team not found.';
        }
    } else {
        $_SESSION['message'] = 'Invalid team ID.';
    }
    header("Location: ../../public/manage_team.php");
    exit();
}

function uploadFile($file) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $targetDir = '../../public/uploads/';
        $fileName = basename($file['name']);
        $targetFile = $targetDir . uniqid() . '_' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $fileName; // Return just the filename
        } else {
            return null; // Return null on failure
        }
    }
    return null;
}

function uploadMultipleFiles($files) {
    $imagesArray = [];
    if (isset($files) && !empty($files['name'][0])) {
        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $targetDir = '../../public/uploads/';
                $fileName = basename($files['name'][$key]);
                $targetFile = $targetDir . uniqid() . '_' . $fileName;

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $imagesArray[] = $fileName; // Store just the filename
                }
            }
        }
    }
    return json_encode($imagesArray);
}

?>
