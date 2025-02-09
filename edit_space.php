<?php
// edit_space.php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth_check.php';
require_once 'db.php';

$errors = [];
$formMode = '';
$space = null;

// Determine mode: if a space_id is provided, use edit mode; otherwise, add mode.
if (isset($_GET['space_id']) && !empty($_GET['space_id'])) {
    $formMode = 'edit_space';
    $space_id = $_GET['space_id'];
    $stmt = $pdo->prepare("SELECT * FROM spaces WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        ':id' => $space_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    if ($stmt->rowCount() > 0) {
        $space = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $errors[] = "Grow Space not found.";
    }
} else {
    $formMode = 'add';
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_space'])) {
        // Edit mode: update existing grow space.
        $space_id = $_POST['space_id'] ?? '';
        $space_name = trim($_POST['space_name'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $environment_type = $_POST['environment_type'] ?? '';
        $size_length_feet = trim($_POST['size_length_feet'] ?? '0');
        $size_length_inches = trim($_POST['size_length_inches'] ?? '0');
        $size_width_feet = trim($_POST['size_width_feet'] ?? '0');
        $size_width_inches = trim($_POST['size_width_inches'] ?? '0');
        $size_height_feet = trim($_POST['size_height_feet'] ?? '0');
        $size_height_inches = trim($_POST['size_height_inches'] ?? '0');

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

        // Process photo upload if a new file is provided
        $photoPath = $space['photo'] ?? '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/' . $_SESSION['username'] . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = basename($_FILES['photo']['name']);
            $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
            $targetFile = $uploadDir . time() . '_' . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photoPath = $targetFile;
            } else {
                $errors[] = "Photo upload failed.";
            }
        }
        if (empty($space_name)) {
            $errors[] = "Grow Space Name is required.";
        }
        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE spaces SET 
                space_name = :space_name, 
                notes = :notes, 
                environment_type = :environment_type, 
                size_length_feet = :size_length_feet, 
                size_length_inches = :size_length_inches, 
                size_width_feet = :size_width_feet, 
                size_width_inches = :size_width_inches, 
                size_height_feet = :size_height_feet, 
                size_height_inches = :size_height_inches, 
                photo = :photo,
                lights = :lights,
                updated_at = NOW()
                WHERE id = :id AND user_id = :user_id");
            $stmt->execute([
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
                ':lights' => $lightsJSON,
                ':id' => $space_id,
                ':user_id' => $_SESSION['user_id']
            ]);
            header("Location: spaces.php?message=Grow+Space+updated");
            exit;
        }
    } else if (isset($_POST['add_space'])) {
        // Add mode: insert new grow space.
        $space_name = trim($_POST['space_name'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $environment_type = $_POST['environment_type'] ?? '';
        $size_length_feet = trim($_POST['size_length_feet'] ?? '0');
        $size_length_inches = trim($_POST['size_length_inches'] ?? '0');
        $size_width_feet = trim($_POST['size_width_feet'] ?? '0');
        $size_width_inches = trim($_POST['size_width_inches'] ?? '0');
        $size_height_feet = trim($_POST['size_height_feet'] ?? '0');
        $size_height_inches = trim($_POST['size_height_inches'] ?? '0');

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

        // Process file upload if provided
        $photoPath = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/' . $_SESSION['username'] . '/';
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
}
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" 
      dir="ltr" data-theme="theme-default" data-assets-path="./assets/" 
      data-template="vertical-menu-template-no-customizer" data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= ($formMode == 'edit_space' ? "Edit Grow Space" : "Add New Grow Space") ?> - My Application</title>
    <meta name="description" content="<?= ($formMode == 'edit_space' ? "Edit your grow space details" : "Add a new grow space") ?>" />
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="./assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="./assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="./assets/vendor/fonts/flag-icons.css" />
    <link rel="stylesheet" href="./assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="./assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="./assets/css/demo.css" />
    <link rel="stylesheet" href="./assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="./assets/vendor/libs/bs-stepper/bs-stepper.css" />
    <link rel="stylesheet" href="./assets/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="./assets/vendor/libs/select2/select2.css" />
    <style>
      .custom-option {
        border: 1px solid #e0e0e0;
        border-radius: 0.375rem;
        padding: 1rem;
        position: relative;
        transition: border-color 0.3s ease;
        cursor: pointer;
        min-height: 250px;
      }
      .custom-option:hover {
        border-color: #7367f0;
      }
      .custom-option.checked {
        border-color: #7367f0;
        background-color: #f3f2ff;
      }
      .custom-option .custom-option-body {
        display: block;
        padding: 1rem;
      }
      .custom-option .form-check-input {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        width: 1.25rem;
        height: 1.25rem;
      }
    </style>
    <script src="./assets/vendor/js/helpers.js"></script>
    <script src="./assets/js/config.js"></script>
  </head>
  <body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>
    <?php include 'navbar.php'; ?>
    <div class="content-wrapper">
      <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><?= ($formMode == 'edit_space' ? "Edit Grow Space" : "Add New Grow Space") ?></h4>
        <?php if(!empty($errors)): ?>
          <div class="alert alert-danger">
            <?php foreach($errors as $error): ?>
              <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <!-- Include the modular grow space form -->
        <?php include 'grow_space_form.php'; ?>
        <script>
          document.addEventListener("DOMContentLoaded", function(){
            <?php if($formMode == 'edit_space' && $space): ?>
              document.getElementById('space_name').value = <?= json_encode($space['space_name']) ?>;
              document.getElementById('notes_space').value = <?= json_encode($space['notes']) ?>;
              document.getElementById('environment_type').value = <?= json_encode($space['environment_type']) ?>;
              document.getElementsByName('size_length_feet')[0].value = <?= json_encode($space['size_length_feet']) ?>;
              document.getElementsByName('size_length_inches')[0].value = <?= json_encode($space['size_length_inches']) ?>;
              document.getElementsByName('size_width_feet')[0].value = <?= json_encode($space['size_width_feet']) ?>;
              document.getElementsByName('size_width_inches')[0].value = <?= json_encode($space['size_width_inches']) ?>;
              document.getElementsByName('size_height_feet')[0].value = <?= json_encode($space['size_height_feet']) ?>;
              document.getElementsByName('size_height_inches')[0].value = <?= json_encode($space['size_height_inches']) ?>;
              <?php if(!empty($space['lights'])):
                $lightsData = json_decode($space['lights'], true);
                if(is_array($lightsData)):
                  foreach($lightsData as $index => $light):
              ?>
                if(<?= $index ?> === 0){
                  document.querySelector('select[name="light_type[]"]').value = <?= json_encode($light['light_type']) ?>;
                  document.querySelector('select[name="light_location[]"]').value = <?= json_encode($light['light_location']) ?>;
                  document.querySelector('input[name="brand[]"]').value = <?= json_encode($light['brand']) ?>;
                  document.querySelector('input[name="model[]"]').value = <?= json_encode($light['model']) ?>;
                  document.querySelector('input[name="wattage[]"]').value = <?= json_encode($light['wattage']) ?>;
                } else {
                  var addButton = document.getElementById('add-light');
                  addButton.click();
                  var lights = document.querySelectorAll('select[name="light_type[]"]');
                  lights[lights.length - 1].value = <?= json_encode($light['light_type']) ?>;
                  var lightLocations = document.querySelectorAll('select[name="light_location[]"]');
                  lightLocations[lightLocations.length - 1].value = <?= json_encode($light['light_location']) ?>;
                  var brands = document.querySelectorAll('input[name="brand[]"]');
                  brands[brands.length - 1].value = <?= json_encode($light['brand']) ?>;
                  var models = document.querySelectorAll('input[name="model[]"]');
                  models[models.length - 1].value = <?= json_encode($light['model']) ?>;
                  var wattages = document.querySelectorAll('input[name="wattage[]"]');
                  wattages[wattages.length - 1].value = <?= json_encode($light['wattage']) ?>;
                }
              <?php 
                  endforeach;
                endif;
              endif; ?>
            <?php endif; ?>
          });
        </script>
      </div>
      <?php include 'footer.php'; ?>
    </div>
    <script src="./assets/vendor/libs/jquery/jquery.js"></script>
    <script src="./assets/vendor/libs/popper/popper.js"></script>
    <script src="./assets/vendor/js/bootstrap.js"></script>
    <script src="./assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="./assets/vendor/js/menu.js"></script>
    <script src="./assets/js/main.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function(){
        document.getElementById('add-light').addEventListener('click', function(){
          var template = document.getElementById('light-template');
          var clone = template.cloneNode(true);
          clone.removeAttribute('id');
          clone.style.display = 'block';
          document.getElementById('lights-container').appendChild(clone);
        });
        document.getElementById('lights-container').addEventListener('click', function(e){
          if(e.target && e.target.classList.contains('remove-light')){
            e.target.closest('.light-item').remove();
          }
        });
      });
    </script>
  </body>
</html>
