<?php
// add_grow_space.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth_check.php';
require_once 'db.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $space_name = trim($_POST['space_name'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $environment_type = $_POST['environment_type'] ?? '';
    $size_length_feet = $_POST['size_length_feet'] ?? '0';
    $size_length_inches = $_POST['size_length_inches'] ?? '0';
    $size_width_feet = $_POST['size_width_feet'] ?? '0';
    $size_width_inches = $_POST['size_width_inches'] ?? '0';
    $size_height_feet = $_POST['size_height_feet'] ?? '0';
    $size_height_inches = $_POST['size_height_inches'] ?? '0';
    
    // Process lights
    $lightTypes = $_POST['light_type'] ?? [];
    $lightLocations = $_POST['light_location'] ?? [];
    $brands = $_POST['brand'] ?? [];
    $models = $_POST['model'] ?? [];
    $wattages = $_POST['wattage'] ?? [];
    $lights = [];
    for ($i = 0; $i < count($lightTypes); $i++) {
        if (trim($lightTypes[$i]) !== '' || trim($lightLocations[$i]) !== '' ||
            trim($brands[$i]) !== '' || trim($models[$i]) !== '' || trim($wattages[$i]) !== '') {
            $lights[] = [
                'light_type' => trim($lightTypes[$i]),
                'light_location' => trim($lightLocations[$i]),
                'brand' => trim($brands[$i]),
                'model' => trim($models[$i]),
                'wattage' => trim($wattages[$i])
            ];
        }
    }
    $lightsJSON = json_encode($lights);
    
    // Handle file upload if provided
    $photoPath = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $filename  = basename($_FILES['photo']['name']);
        $filename  = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
        $uploadDir = 'uploads/' . $_SESSION['username'] . '/grow_spaces/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $targetFile = $uploadDir . time() . '_' . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = $targetFile;
        }
    }
    
    if (empty($space_name)) {
        $response['error'] = "Grow Space Name is required.";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $stmt = $pdo->prepare("INSERT INTO spaces 
        (user_id, space_name, notes, environment_type, size_length_feet, size_length_inches, size_width_feet, size_width_inches, size_height_feet, size_height_inches, photo, lights, created_at)
        VALUES 
        (:user_id, :space_name, :notes, :environment_type, :size_length_feet, :size_length_inches, :size_width_feet, :size_width_inches, :size_height_feet, :size_height_inches, :photo, :lights, NOW())");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':space_name' => $space_name,
        ':notes' => $notes,
        ':environment_type' => $environment_type,
        ':size_length_feet' => $size_length_feet,
        ':size_length_inches' => $size_length_inches,
        ':size_width_feet' => $size_width_feet,
        ':size_width_inches' => $size_width_inches,
        ':size_height_feet' => $size_height_feet,
        ':size_height_inches' => $size_height_inches,
        ':photo' => $photoPath,
        ':lights' => $lightsJSON
    ]);
    $newSpaceId = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("SELECT * FROM spaces WHERE id = ? AND user_id = ?");
    $stmt->execute([$newSpaceId, $_SESSION['user_id']]);
    $newSpace = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($newSpace) {
        $response['success'] = true;
        $response['data'] = $newSpace;
    } else {
        $response['error'] = "Failed to add grow space.";
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    header("Location: spaces.php");
    exit;
}
