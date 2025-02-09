<?php
// delete_space.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth_check.php';
require_once 'db.php';

if (isset($_GET['grow_space_id']) && !empty($_GET['grow_space_id'])) {
    $space_id = $_GET['grow_space_id'];

    $stmt = $pdo->prepare("SELECT * FROM spaces WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id' => $space_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("DELETE FROM spaces WHERE id = :id");
        $stmt->execute([':id' => $space_id]);
        header("Location: spaces.php?message=Grow+Space+deleted");
        exit;
    } else {
        header("Location: spaces.php?error=Grow+Space+not+found");
        exit;
    }
} else {
    header("Location: spaces.php?error=Grow+Space+ID+is+missing");
    exit;
}
?>
