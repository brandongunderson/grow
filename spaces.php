<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "Grow Spaces - My Application";
$pageDescription = "Manage your grow spaces.";
require_once 'auth_check.php';
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM spaces WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$spaces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">My Grow Spaces</h4>
    <!-- Button to toggle add new grow space form -->
    <div class="mb-3">
      <button id="toggleAddGrowSpaceForm" class="btn btn-primary">Add New Grow Space</button>
    </div>
    <!-- Hidden Add Grow Space Form Container (appears above the cards) -->
    <div id="addGrowSpaceFormContainer" style="display: none; margin-bottom: 1.5rem;">
      <?php include 'grow_space_form.php'; ?>
    </div>
    <?php if(empty($spaces)): ?>
      <div class="alert alert-warning">
        <strong>No Grow Spaces Found.</strong> Please add a grow space.
      </div>
    <?php else: ?>
      <div class="card">
        <div class="card-body">
          <div class="row gy-4" id="spacesContainer">
            <?php foreach($spaces as $space): ?>
              <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                  <?php if($space['photo']): ?>
                    <img src="<?= htmlspecialchars($space['photo']) ?>" alt="Space Photo" class="card-img-top" style="height:200px; object-fit:cover;">
                  <?php else: ?>
                    <img src="assets/img/placeholder.png" alt="No Photo" class="card-img-top" style="height:200px; object-fit:cover;">
                  <?php endif; ?>
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($space['space_name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars(substr($space['notes'], 0, 100)) ?><?= (strlen($space['notes']) > 100 ? "..." : "") ?></p>
                    <p class="mb-0">Environment: <?= htmlspecialchars($space['environment_type']) ?></p>
                    <p class="mb-0">
                      Size: 
                      L: <?= htmlspecialchars($space['size_length_feet']) ?>' <?= htmlspecialchars($space['size_length_inches']) ?>" x 
                      W: <?= htmlspecialchars($space['size_width_feet']) ?>' <?= htmlspecialchars($space['size_width_inches']) ?>" x 
                      H: <?= htmlspecialchars($space['size_height_feet']) ?>' <?= htmlspecialchars($space['size_height_inches']) ?>"
                    </p>
                  </div>
                  <div class="card-footer d-flex justify-content-between">
                    <a href="edit_space.php?space_id=<?= $space['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_space.php?grow_space_id=<?= $space['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this grow space?');">Delete</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div><!-- end row -->
        </div><!-- end card-body -->
      </div><!-- end card -->
    <?php endif; ?>
  </div><!-- end container -->
</div><!-- end content-wrapper -->
<?php include 'footer.php'; ?>
<script src="assets/vendor/libs/jquery/jquery.js"></script>
<script src="assets/vendor/libs/popper/popper.js"></script>
<script src="assets/vendor/js/bootstrap.js"></script>
<script src="assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/vendor/js/menu.js"></script>
<script src="assets/js/main.js"></script>
<script>
$(document).ready(function(){
  $("#toggleAddGrowSpaceForm").click(function(e){
    e.preventDefault();
    $("#addGrowSpaceFormContainer").slideToggle();
  });
});
</script>
