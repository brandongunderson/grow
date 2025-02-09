<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;

function returnJson($success, $message) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

$displayName = trim($_POST['full_name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$company     = trim($_POST['company'] ?? '');
$state       = trim($_POST['state'] ?? '');
$country     = trim($_POST['country'] ?? '');
$timeZones   = trim($_POST['timeZones'] ?? '');
$profilePictureUrl = trim($_POST['profile_picture_url'] ?? '');

if ($displayName == "" || $email == "") {
    returnJson(false, "Display Name and E-mail are required.");
}

try {
    $sql = "UPDATE users SET full_name = :fullName, email = :email, company = :company, state = :state, country = :country, timeZones = :timeZones";
    if ($profilePictureUrl != "") {
        $sql .= ", profile_picture = :profile_picture";
    }
    $sql .= " WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':fullName', $displayName);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':company', $company);
    $stmt->bindValue(':state', $state);
    $stmt->bindValue(':country', $country);
    $stmt->bindValue(':timeZones', $timeZones);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    if ($profilePictureUrl != "") {
        $stmt->bindValue(':profile_picture', $profilePictureUrl);
    }
    $stmt->execute();
    returnJson(true, "Account updated successfully!");
} catch (Exception $e) {
    returnJson(false, "Database error: " . $e->getMessage());
}
?>
