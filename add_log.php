<?php
// add_log.php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$grow_id = $_POST['grow_id'] ?? 0;
$content = trim($_POST['content'] ?? "");

if ($grow_id && $content != "") {
    $stmt = $pdo->prepare("INSERT INTO timeline_logs (grow_id, user_id, log_date, content, created_at) VALUES (?, ?, NOW(), ?, NOW())");
    $stmt->execute([$grow_id, $user_id, $content]);
}
header("Location: grow.php?grow_id=" . urlencode($grow_id));
exit;
?>
