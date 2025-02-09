<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "My Plants - My Application";
$pageDescription = "Manage your plants.";
require_once 'auth_check.php';
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];

// Retrieve plants.
$stmt = $pdo->prepare("SELECT * FROM plants WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$plants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve grows.
$stmt = $pdo->prepare("SELECT * FROM grows WHERE user_id = ?");
$stmt->execute([$user_id]);
$grows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve grow spaces.
$stmt = $pdo->prepare("SELECT * FROM spaces WHERE user_id = ?");
$stmt->execute([$user_id]);
$spaces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">My Plants</h4>
    <?php if(empty($plants)): ?>
      <?php if(empty($grows)): ?>
        <?php if(empty($spaces)): ?>
          <div class="alert alert-warning">
            <strong>No Grow Spaces Found.</strong> Please add a grow space first.
            <br>
            <a href="edit_space.php" class="btn btn-primary mt-2">Add Grow Space</a>
          </div>
        <?php else: ?>
          <div class="alert alert-info">
            <strong>No Grows Found.</strong> You have grow spaces but haven't created any grows yet.
            <br>
            <a href="edit_grow.php" class="btn btn-primary mt-2">Add New Grow</a>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="alert alert-info">
          <strong>No Plants Found.</strong> You have created grows but no plants have been added.
          <br>
          <a href="edit_plant.php" class="btn btn-primary mt-2">Add New Plant</a>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="mb-4">
        <a href="edit_plant.php" class="btn btn-primary">Add New Plant</a>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Strain</th>
                  <th>Phenotype</th>
                  <th>Growth Stage</th>
                  <th>Health</th>
                  <th>Grow</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($plants as $plant): ?>
                  <tr>
                    <td><?= htmlspecialchars($plant['name']) ?></td>
                    <td><?= htmlspecialchars($plant['strain']) ?></td>
                    <td><?= htmlspecialchars($plant['phenotype']) ?></td>
                    <td><?= htmlspecialchars($plant['growth_stage']) ?></td>
                    <td><?= htmlspecialchars($plant['health_status']) ?></td>
                    <td>
                      <?php 
                      foreach($grows as $grow) {
                          if ($grow['id'] == $plant['grow_id']) {
                              echo htmlspecialchars($grow['name']);
                          }
                      }
                      ?>
                    </td>
                    <td>
                      <a href="plant.php?plant_id=<?= $plant['id'] ?>" class="btn btn-sm btn-info">View</a>
                      <a href="edit_plant.php?plant_id=<?= $plant['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                      <a href="delete_plant.php?plant_id=<?= $plant['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this plant?');">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
