<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "My Grows - My Application";
$pageDescription = "Manage your grows.";
require_once 'auth_check.php';
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM grows WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$grows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold">My Grows</h4>
      <a href="edit_grow.php" class="btn btn-primary">Add New Grow</a>
    </div>

    <?php if (empty($grows)): ?>
      <div class="alert alert-info">
        <strong>No Grows Found.</strong> You have not created any grows yet.
      </div>
    <?php else: ?>
      <div class="row gy-6">
        <?php foreach ($grows as $grow): ?>
          <div class="col-sm-6 col-lg-4">
            <div class="card p-2 h-100 shadow-none border">
              <!-- Grow Photo -->
              <div class="rounded-2 text-center mb-4">
                <a href="grow.php?grow_id=<?= $grow['id'] ?>">
                  <img src="<?= !empty($grow['photo']) ? htmlspecialchars($grow['photo']) : 'assets/img/placeholder.png' ?>" 
                       alt="Grow Photo" class="img-fluid" style="max-height: 150px; object-fit: cover;">
                </a>
              </div>
              <!-- Card Body -->
              <div class="card-body p-4 pt-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-label-primary">Grow</span>
                  <small class="text-muted"><?= date("M d, Y", strtotime($grow['created_at'])) ?></small>
                </div>
                <a href="grow.php?grow_id=<?= $grow['id'] ?>" class="h5 d-block mb-2"><?= htmlspecialchars($grow['name']) ?></a>
                <p class="mb-2">
                  <?= htmlspecialchars(substr($grow['description'], 0, 100)) ?><?= strlen($grow['description']) > 100 ? "..." : "" ?>
                </p>
                <p class="mb-2">Grow Space: <?= htmlspecialchars($grow['grow_space_id']) ?></p>
                <div class="d-flex flex-column flex-md-row gap-2">
                  <a href="grow.php?grow_id=<?= $grow['id'] ?>" class="btn btn-sm btn-info">View</a>
                  <a href="edit_grow.php?grow_id=<?= $grow['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                  <a href="delete_grow.php?grow_id=<?= $grow['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this grow?');">Delete</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
