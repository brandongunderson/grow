<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth_check.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$grow_id = isset($_GET['grow_id']) ? intval($_GET['grow_id']) : 0;

if (!$grow_id) {
    echo "Grow ID is missing.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM grows WHERE id = ? AND user_id = ?");
$stmt->execute([$grow_id, $user_id]);
$grow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$grow) {
    echo "Grow not found or you do not have permission to delete this grow.";
    exit;
}

$stmt = $pdo->prepare("DELETE FROM grows WHERE id = ? AND user_id = ?");
$stmt->execute([$grow_id, $user_id]);

header("Location: grows.php");
exit;
?>
