<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}
require_once 'db.php';
$user_id = $_SESSION['user_id'];

// Retrieve the username.
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$userData || empty($userData['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Sanitize the username for safe folder naming.
$username = $userData['username'];
$safeUsername = preg_replace('/[^a-zA-Z0-9_-]/', '_', $username);

// Create a user-specific folder: uploads/[username]/
$userFolder = __DIR__ . '/uploads/' . $safeUsername . '/';
if (!is_dir($userFolder)) {
    if (!mkdir($userFolder, 0777, true)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to create user folder.']);
        exit;
    }
}

function returnJson($success, $message, $newImagePath = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'newImagePath' => $newImagePath
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    returnJson(false, "Invalid request method. Use POST.");
}

if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    returnJson(false, "No file uploaded or an upload error occurred.");
}

$tmpName = $_FILES['profile_picture']['tmp_name'];
$extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
$uniqueName = uniqid("img_", true) . '.' . $extension;
$target = $userFolder . $uniqueName;

if (!move_uploaded_file($tmpName, $target)) {
    returnJson(false, "Failed to move uploaded file.");
}

$newImagePath = 'uploads/' . $safeUsername . '/' . $uniqueName;
returnJson(true, "Photo uploaded successfully.", $newImagePath);
?>
