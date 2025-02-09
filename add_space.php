<?php
// add_space.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth_check.php';
require_once 'db.php';

$errors = [];
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
        if (trim($lightTypes[$i]) !== '' || trim($lightLocations[$i]) !== '' || trim($brands[$i]) !== '' || trim($models[$i]) !== '' || trim($wattages[$i]) !== '') {
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

    // Process file upload
    $photoPath = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/' . $_SESSION['username'] . '/grow_spaces/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = basename($_FILES['photo']['name']);
        $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
        $targetFile = $uploadDir . time() . '_' . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = $targetFile;
        }
    }
    
    if (empty($space_name)) {
        $errors[] = "Grow Space Name is required.";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO spaces 
            (user_id, space_name, notes, environment_type, size_length_feet, size_length_inches, size_width_feet, size_width_inches, size_height_feet, size_height_inches, photo, lights, created_at)
            VALUES (:user_id, :space_name, :notes, :environment_type, :size_length_feet, :size_length_inches, :size_width_feet, :size_width_inches, :size_height_feet, :size_height_inches, :photo, :lights, NOW())");
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
        header("Location: spaces.php?message=Grow+Space+added");
        exit;
    }
}
$pageTitle = "Add Grow Space - My Application";
$pageDescription = "Add a new grow space.";
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
?>
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Add New Grow Space</h4>
    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
          <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php $formMode = 'default'; include 'grow_space_form.php'; ?>
  </div>
</div>
<?php include 'footer.php'; ?>
<script src="./assets/vendor/libs/jquery/jquery.js"></script>
<script src="./assets/vendor/libs/popper/popper.js"></script>
<script src="./assets/vendor/js/bootstrap.js"></script>
<script src="./assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="./assets/vendor/js/menu.js"></script>
<script src="./assets/js/main.js"></script>
