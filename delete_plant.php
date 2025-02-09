<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth_check.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$plant_id = isset($_GET['plant_id']) ? intval($_GET['plant_id']) : 0;

if (!$plant_id) {
    echo "Plant ID is missing.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM plants WHERE id = ? AND user_id = ?");
$stmt->execute([$plant_id, $user_id]);
$plant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plant) {
    echo "Plant not found or you do not have permission to delete this plant.";
    exit;
}

$stmt = $pdo->prepare("DELETE FROM plants WHERE id = ? AND user_id = ?");
$stmt->execute([$plant_id, $user_id]);

header("Location: plants.php");
exit;
?>
