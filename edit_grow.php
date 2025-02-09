<?php
// edit_grow.php

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

// Process grow submission (wizard Step 2/3); ignore AJAX space additions.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grow_name']) && !isset($_POST['ajax_space'])) {
    $grow_space_id    = $_POST['grow_space_id'] ?? '';
    $grow_name        = trim($_POST['grow_name'] ?? '');
    $start_date_input = trim($_POST['start_date'] ?? '');
    $notes            = trim($_POST['notes'] ?? '');
    $selected_plants  = $_POST['selected_plants'] ?? []; // Array of plant IDs

    if (empty($grow_space_id)) {
        $errors[] = "Please select a Grow Space.";
    }
    if (empty($grow_name)) {
        $errors[] = "Grow Name is required.";
    }
    
    if (empty($start_date_input)) {
        $start_date = date('Y-m-d H:i:s');
    } else {
        $start_date = date('Y-m-d H:i:s', strtotime($start_date_input));
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO grows 
            (user_id, grow_space_id, grow_name, start_date, notes, plants, created_at)
            VALUES (:user_id, :grow_space_id, :grow_name, :start_date, :notes, :plants, NOW())");
        $plantsJSON = json_encode($selected_plants);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':grow_space_id' => $grow_space_id,
            ':grow_name' => $grow_name,
            ':start_date' => $start_date,
            ':notes' => $notes,
            ':plants' => $plantsJSON
        ]);
        $newGrowId = $pdo->lastInsertId();
        header("Location: view_grow.php?id=" . $newGrowId);
        exit;
    }
}

// Load grow spaces for the current user
$stmt = $pdo->prepare("SELECT * FROM spaces WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$growSpaces = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load plants for the current user (for Step 3)
$stmt = $pdo->prepare("SELECT * FROM plants WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$plants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" 
      dir="ltr" data-theme="theme-default" data-assets-path="./assets/" 
      data-template="vertical-menu-template-no-customizer" data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Edit Grow - My Application</title>
    <meta name="description" content="Add a new grow using the vertical wizard" />
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
        <h4 class="fw-bold py-3 mb-4">Add New Grow</h4>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <p><?= htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <!-- Hidden inline form for "Add New Grow Space" (using the modular form in add mode) -->
        <div id="addGrowSpaceFormContainer" style="display:none; margin-bottom:1.5rem;">
          <?php $formMode = 'add'; include 'grow_space_form.php'; ?>
        </div>
        <!-- Vertical Icons Wizard for adding a new Grow -->
        <div class="bs-stepper wizard-vertical-icons-example">
          <div class="bs-stepper-header d-flex align-items-center">
            <div class="d-flex">
              <!-- Step 1: Grow Space -->
              <div class="step" data-target="#step-1">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle" style="display:flex; align-items:center; justify-content:center;">
                    <i class="tf-icons ti ti-layout-grid"></i>
                  </span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Grow Space</span>
                    <span class="bs-stepper-subtitle">Select or add space</span>
                  </span>
                </button>
              </div>
              <div class="line"><i class="ti ti-chevron-right"></i></div>
              <!-- Step 2: Grow Details -->
              <div class="step" data-target="#step-2">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle" style="display:flex; align-items:center; justify-content:center;">
                    <i class="tf-icons ti ti-plant"></i>
                  </span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Grows</span>
                    <span class="bs-stepper-subtitle">Enter grow info</span>
                  </span>
                </button>
              </div>
              <div class="line"><i class="ti ti-chevron-right"></i></div>
              <!-- Step 3: Plants -->
              <div class="step" data-target="#step-3">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle" style="display:flex; align-items:center; justify-content:center;">
                    <i class="tf-icons ti ti-leaf"></i>
                  </span>
                  <span class="bs-stepper-label">
                    <span class="bs-stepper-title">Plants</span>
                    <span class="bs-stepper-subtitle">Select plants to add</span>
                  </span>
                </button>
              </div>
            </div>
            <!-- Plain text "Add New Grow Space" link in the top‑right -->
            <div class="ms-auto">
              <a href="#" id="addGrowSpaceLink" class="fw-bold" style="color:#7367F0; text-decoration:none;">Add New Grow Space</a>
            </div>
          </div>
          <form method="post" action="edit_grow.php" id="growWizardForm">
            <div class="bs-stepper-content">
              <!-- Step 1: Select Grow Space -->
              <div id="step-1" class="content">
                <h5>Select Grow Space</h5>
                <div class="row">
                  <?php if (count($growSpaces) > 0): ?>
                    <?php foreach ($growSpaces as $space): ?>
                      <div class="col-md-4 mb-3">
                        <div class="custom-option form-check">
                          <label class="form-check-label custom-option-content" for="growSpace<?= $space['id']; ?>">
                            <span class="custom-option-body card-body">
                              <?php if (!empty($space['photo'])): ?>
                                <img src="<?= htmlspecialchars($space['photo']) ?>" class="img-fluid mb-2" alt="Grow Space Photo">
                              <?php endif; ?>
                              <h6><?= htmlspecialchars($space['space_name']) ?></h6>
                              <?php if (!empty($space['environment_type'])): ?>
                                <p><strong>Environment:</strong> <?= htmlspecialchars($space['environment_type']) ?></p>
                              <?php endif; ?>
                              <?php 
                                $lengthFeet   = !empty($space['size_length_feet']) ? $space['size_length_feet'] : "0";
                                $lengthInches = !empty($space['size_length_inches']) ? $space['size_length_inches'] : "0";
                                $widthFeet    = !empty($space['size_width_feet']) ? $space['size_width_feet'] : "0";
                                $widthInches  = !empty($space['size_width_inches']) ? $space['size_width_inches'] : "0";
                                $heightFeet   = !empty($space['size_height_feet']) ? $space['size_height_feet'] : "0";
                                $heightInches = !empty($space['size_height_inches']) ? $space['size_height_inches'] : "0";
                                if ($lengthFeet === "0" && $lengthInches === "0" &&
                                    $widthFeet === "0" && $widthInches === "0" &&
                                    $heightFeet === "0" && $heightInches === "0") {
                                    $sizeText = "Size: Not set";
                                } else {
                                    $sizeText = "L: " . $lengthFeet . "' " . $lengthInches . "\" x W: " . $widthFeet . "' " . $widthInches . "\" x H: " . $heightFeet . "' " . $heightInches . "\"";
                                }
                              ?>
                              <p><strong>Size:</strong> <?= $sizeText; ?></p>
                              <p><strong>Notes:</strong> <?= !empty($space['notes']) ? nl2br(htmlspecialchars($space['notes'])) : "No notes" ?></p>
                              <?php 
                                $lights = [];
                                if (!empty($space['lights'])) {
                                    $lights = json_decode($space['lights'], true);
                                }
                              ?>
                              <?php if(!empty($lights) && is_array($lights) && count($lights) > 0): ?>
                                <p><strong>Lights:</strong><br>
                                  <?php foreach($lights as $light): ?>
                                    <?= htmlspecialchars($light['light_type']); ?> (<?= htmlspecialchars($light['light_location']); ?>) – <?= htmlspecialchars($light['brand']); ?> <?= htmlspecialchars($light['model']); ?>, <?= htmlspecialchars($light['wattage']); ?>W<br>
                                  <?php endforeach; ?>
                                </p>
                              <?php else: ?>
                                <p><strong>Lights:</strong> No lights configured</p>
                              <?php endif; ?>
                            </span>
                            <input class="form-check-input" type="radio" name="grow_space_id" id="growSpace<?= $space['id']; ?>" value="<?= $space['id']; ?>">
                          </label>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="col-12">
                      <div class="alert alert-warning">
                        No Grow Spaces found. <a href="add_grow_space.php">Add a Grow Space</a>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-primary btn-next" id="step1NextBtn" disabled>Next</button>
                </div>
              </div>
              <!-- Step 2: Grow Details -->
              <div id="step-2" class="content">
                <h5>Grow Details</h5>
                <div class="mb-3">
                  <label for="grow_name" class="form-label">Grow Name</label>
                  <input type="text" id="grow_name" name="grow_name" class="form-control" placeholder="Enter grow name" required>
                </div>
                <div class="mb-3">
                  <label for="start_date" class="form-label">Start Date</label>
                  <input type="datetime-local" id="start_date" name="start_date" class="form-control">
                </div>
                <div class="mb-3">
                  <label for="notes" class="form-label">Notes</label>
                  <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Enter any notes"></textarea>
                </div>
                <div class="d-flex justify-content-between">
                  <button type="button" class="btn btn-label-secondary btn-prev">Previous</button>
                  <button type="button" class="btn btn-primary btn-next">Next</button>
                </div>
              </div>
              <!-- Step 3: Select Plants -->
              <div id="step-3" class="content">
                <h5>Select Plants to Add</h5>
                <div class="mb-3">
                  <label for="selected_plants" class="form-label">Select Plants (use Ctrl/Cmd to select multiple)</label>
                  <select id="selected_plants" name="selected_plants[]" class="form-select" multiple>
                    <?php if (count($plants) > 0): ?>
                      <?php foreach ($plants as $plant): ?>
                        <option value="<?= $plant['id']; ?>">
                          <?= htmlspecialchars(isset($plant['plant_name']) ? $plant['plant_name'] : $plant['name']); ?>
                        </option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="">No plants found. <a href="add_plant.php">Add a plant</a></option>
                    <?php endif; ?>
                  </select>
                </div>
                <div class="d-flex justify-content-between">
                  <button type="button" class="btn btn-label-secondary btn-prev">Previous</button>
                  <button type="submit" class="btn btn-success btn-submit">Submit</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php include 'footer.php'; ?>
    </div>
    <script src="./assets/vendor/libs/jquery/jquery.js"></script>
    <script src="./assets/vendor/libs/popper/popper.js"></script>
    <script src="./assets/vendor/js/bootstrap.js"></script>
    <script src="./assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="./assets/vendor/js/menu.js"></script>
    <script src="./assets/vendor/libs/bs-stepper/bs-stepper.js"></script>
    <script src="./assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="./assets/vendor/libs/select2/select2.js"></script>
    <script src="./assets/js/main.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function(){
        window.stepper = new Stepper(document.querySelector('.bs-stepper'));
        
        // When a radio input is selected, add "checked" class and enable Next button.
        $(document).on('change', '.custom-option .form-check-input', function(){
          $('.custom-option').removeClass('checked');
          if ($(this).is(':checked')) {
            $(this).closest('.custom-option').addClass('checked');
            $('#step1NextBtn').prop('disabled', false);
          }
        });
        
        // Toggle the inline Add New Grow Space form.
        $('#addGrowSpaceLink').on('click', function(e){
          e.preventDefault();
          $('#addGrowSpaceFormContainer').toggle();
        });
        
        // Handle AJAX submission of the add grow space form.
        $(document).on('submit', '#addGrowSpaceForm', function(e){
          e.preventDefault();
          var formData = new FormData(this);
          $.ajax({
            url: 'add_grow_space.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response){
              if(response.success){
                var space = response.data;
                var sizeText = "";
                if(space.size_length_feet=="0" && space.size_length_inches=="0" &&
                   space.size_width_feet=="0" && space.size_width_inches=="0" &&
                   space.size_height_feet=="0" && space.size_height_inches=="0") {
                  sizeText = "Size: Not set";
                } else {
                  sizeText = "L: " + space.size_length_feet + "' " + space.size_length_inches + "\" x W: " +
                             space.size_width_feet + "' " + space.size_width_inches + "\" x H: " +
                             space.size_height_feet + "' " + space.size_height_inches + "\"";
                }
                var notesText = (space.notes && typeof space.notes === 'string') ? space.notes.replace(/\n/g, "<br>") : "No notes";
                var lightsText = "";
                if(space.lights && space.lights !== ""){
                  try {
                    var lights = JSON.parse(space.lights);
                    if(Array.isArray(lights) && lights.length > 0){
                      lightsText = "";
                      lights.forEach(function(light){
                        lightsText += light.light_type + " (" + light.light_location + ") - " + light.brand + " " + light.model + ", " + light.wattage + "W<br>";
                      });
                    } else {
                      lightsText = "No lights configured";
                    }
                  } catch(e){
                    lightsText = "No lights configured";
                  }
                } else {
                  lightsText = "No lights configured";
                }
                var newOptionHtml = '<div class="col-md-4 mb-3">' +
                  '<div class="custom-option form-check">' +
                  '<label class="form-check-label custom-option-content" for="growSpace' + space.id + '">' +
                  '<span class="custom-option-body card-body">';
                if(space.photo){
                  newOptionHtml += '<img src="' + space.photo + '" class="img-fluid mb-2" alt="Grow Space Photo">';
                }
                newOptionHtml += '<h6>' + space.space_name + '</h6>';
                if(space.environment_type){
                  newOptionHtml += '<p><strong>Environment:</strong> ' + space.environment_type + '</p>';
                }
                newOptionHtml += '<p><strong>Size:</strong> ' + sizeText + '</p>' +
                  '<p><strong>Notes:</strong> ' + notesText + '</p>';
                if(lightsText){
                  newOptionHtml += '<p><strong>Lights:</strong><br>' + lightsText + '</p>';
                } else {
                  newOptionHtml += '<p><strong>Lights:</strong> No lights configured</p>';
                }
                newOptionHtml += '</span>' +
                  '<input class="form-check-input" type="radio" name="grow_space_id" id="growSpace' + space.id + '" value="' + space.id + '">' +
                  '</label></div></div>';
                
                $("#spacesContainer").prepend(newOptionHtml);
                $('#growSpace' + space.id).prop('checked', true).trigger('change');
                $('#addGrowSpaceFormContainer').hide();
                $('#addGrowSpaceForm')[0].reset();
                $('#addGrowSpaceError').hide().empty();
              } else {
                $('#addGrowSpaceError').text(response.error).show();
              }
            },
            error: function(xhr, status, error){
              $('#addGrowSpaceError').text("Error: " + error).show();
            }
          });
        });
        
        $('#cancelAddGrowSpace').on('click', function(){
          $('#addGrowSpaceFormContainer').hide();
        });
        
        // Add light functionality using the template.
        document.getElementById('add-light').addEventListener('click', function(){
          var template = document.getElementById('light-template');
          var clone = template.cloneNode(true);
          clone.removeAttribute('id');
          clone.style.display = 'block';
          document.getElementById('lights-container').appendChild(clone);
        });
        
        // Remove light functionality.
        document.getElementById('lights-container').addEventListener('click', function(e){
          if(e.target && e.target.classList.contains('remove-light')){
            e.target.closest('.light-item').remove();
          }
        });
      });
    </script>
  </body>
</html>
