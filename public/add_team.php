<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$query = "SELECT id FROM teams WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$existingTeam = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingTeam) {
    echo "<p>You already have a team. Please delete it if you want to add another.</p>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $members = json_encode(array_map('trim', explode(',', $_POST['members'])));

    // Initialize file paths
    $logoPath = null;
    $imagesPaths = [];

    // Handle logo upload
    $logoPath = null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $logoName = basename($_FILES['logo']['name']);
    $logoPath = 'uploads/' . $logoName; // Store the path to move the file
    move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath); // Move the uploaded logo
}

    // Handle multiple image uploads
$imagesArray = [];
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
            $imageName = basename($_FILES['images']['name'][$key]);
            $imagePath = 'uploads/' . $imageName; // Store the path to move the file
            move_uploaded_file($tmpName, $imagePath); // Move the uploaded image
            $imagesArray[] = $imageName; // Store only the file name
        }
    }
}

    // Convert images array to JSON
$imagesJson = json_encode($imagesArray);

    // Insert the team into the teams table
$stmt = $pdo->prepare("INSERT INTO teams (user_id, name, members, logo, images) VALUES (:user_id, :name, :members, :logo, :images)");
$stmt->execute([
    'user_id' => $_SESSION['user_id'],
    'name' => $name,
    'members' => $members,
    'logo' => $logoName, 
    'images' => $imagesJson, 
]);

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Team</title>
    <link rel="stylesheet" href="css/addTeam.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <main class="add-team-page">
        <h1>Add Your Team</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Team Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="members">Members (comma-separated):</label>
            <input type="text" id="members" name="members" required>
            
            <label for="logo">Team Logo:</label>
            <input type="file" id="logo" name="logo" accept="image/*" required>
            
            <label for="images">Team Images:</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple required>
            
            <input type="submit" value="Add Team">
        </form>
    </main>
</body>
</html>
