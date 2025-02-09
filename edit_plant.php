<?php
// edit_plant.php
$pageTitle = "Edit Plant - My Application";
$pageDescription = "Create or edit a plant.";
require_once 'auth_check.php';
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$plant_id = $_GET['plant_id'] ?? 0;
$name = "";
$strain = "";
$phenotype = "";
$growth_stage = "";
$health_status = "";
$grow_id = "";
$error = "";

if ($plant_id) {
    $stmt = $pdo->prepare("SELECT * FROM plants WHERE id = ? AND user_id = ?");
    $stmt->execute([$plant_id, $user_id]);
    $plant = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($plant) {
        $name = $plant['name'];
        $strain = $plant['strain'];
        $phenotype = $plant['phenotype'];
        $growth_stage = $plant['growth_stage'];
        $health_status = $plant['health_status'];
        $grow_id = $plant['grow_id'];
    } else {
        $error = "Plant not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? "");
    $strain = trim($_POST['strain'] ?? "");
    $phenotype = trim($_POST['phenotype'] ?? "");
    $growth_stage = trim($_POST['growth_stage'] ?? "");
    $health_status = trim($_POST['health_status'] ?? "");
    $grow_id = trim($_POST['grow_id'] ?? "");

    if ($name == "") {
        $error = "Plant name is required.";
    } else {
        if ($plant_id) {
            $stmt = $pdo->prepare("UPDATE plants SET name = ?, strain = ?, phenotype = ?, growth_stage = ?, health_status = ?, grow_id = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$name, $strain, $phenotype, $growth_stage, $health_status, $grow_id, $plant_id, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO plants (user_id, grow_id, name, strain, phenotype, growth_stage, health_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $grow_id, $name, $strain, $phenotype, $growth_stage, $health_status]);
            $plant_id = $pdo->lastInsertId();
        }
        header("Location: plants.php");
        exit;
    }
}
?>
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><?= $plant_id ? "Edit Plant" : "Add New Plant" ?></h4>
    <?php if($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="card">
      <div class="card-body">
        <form method="POST" action="">
          <div class="mb-3">
            <label class="form-label" for="name">Plant Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="strain">Strain</label>
            <input type="text" class="form-control" id="strain" name="strain" value="<?= htmlspecialchars($strain) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label" for="phenotype">Phenotype</label>
            <input type="text" class="form-control" id="phenotype" name="phenotype" value="<?= htmlspecialchars($phenotype) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label" for="growth_stage">Growth Stage</label>
            <input type="text" class="form-control" id="growth_stage" name="growth_stage" value="<?= htmlspecialchars($growth_stage) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label" for="health_status">Health Status</label>
            <input type="text" class="form-control" id="health_status" name="health_status" value="<?= htmlspecialchars($health_status) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label" for="grow_id">Grow ID</label>
            <input type="text" class="form-control" id="grow_id" name="grow_id" value="<?= htmlspecialchars($grow_id) ?>">
          </div>
          <button type="submit" class="btn btn-primary"><?= $plant_id ? "Update Plant" : "Add Plant" ?></button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
