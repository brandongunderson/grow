<?php
$pageTitle = "Plant Details - My Application";
$pageDescription = "View detailed information about your plant.";
require_once 'auth_check.php';
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$plant_id = $_GET['plant_id'] ?? 0;

if (!$plant_id) {
    echo "Plant ID not specified.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM plants WHERE id = ? AND user_id = ?");
$stmt->execute([$plant_id, $user_id]);
$plant = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$plant) {
    echo "Plant not found.";
    exit;
}
?>
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><?= htmlspecialchars($plant['name']) ?> - Details</h4>
    <div class="card mb-4">
      <div class="card-body">
        <p><strong>Strain:</strong> <?= htmlspecialchars($plant['strain']) ?></p>
        <p><strong>Phenotype:</strong> <?= htmlspecialchars($plant['phenotype']) ?></p>
        <p><strong>Growth Stage:</strong> <?= htmlspecialchars($plant['growth_stage']) ?></p>
        <p><strong>Health Status:</strong> <?= htmlspecialchars($plant['health_status']) ?></p>
        <p><strong>Assigned Grow ID:</strong> <?= htmlspecialchars($plant['grow_id']) ?></p>
        <p><strong>Created:</strong> <?= htmlspecialchars($plant['created_at']) ?></p>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
