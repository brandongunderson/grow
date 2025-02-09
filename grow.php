<?php
// grow.php
$pageTitle = "Grow Details - My Application";
$pageDescription = "View your grow timeline and progress logs.";
require_once 'auth_check.php';
include 'header.php';
include 'sidebar.php';
include 'navbar.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$grow_id = $_GET['grow_id'] ?? 0;

if (!$grow_id) {
    echo "Grow ID not specified.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM grows WHERE id = ? AND user_id = ?");
$stmt->execute([$grow_id, $user_id]);
$grow = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$grow) {
    echo "Grow not found.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM timeline_logs WHERE grow_id = ? AND user_id = ? ORDER BY log_date DESC");
$stmt->execute([$grow_id, $user_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><?= htmlspecialchars($grow['name']) ?> - Details</h4>
    <div class="card mb-4">
      <div class="card-body">
        <h5>Description:</h5>
        <p><?= nl2br(htmlspecialchars($grow['description'])) ?></p>
        <p>Grow Space: <?= htmlspecialchars($grow['grow_space_id']) ?></p>
        <p>Created on: <?= htmlspecialchars($grow['created_at']) ?></p>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-header">
        <h5>Timeline & Progress Logs</h5>
      </div>
      <div class="card-body">
        <?php if(empty($logs)): ?>
          <p>No logs recorded for this grow yet.</p>
        <?php else: ?>
          <?php foreach($logs as $log): ?>
            <div class="mb-3">
              <small class="text-muted"><?= htmlspecialchars($log['log_date']) ?></small>
              <p><?= nl2br(htmlspecialchars($log['content'])) ?></p>
            </div>
            <hr>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5>Add New Log</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="add_log.php">
          <input type="hidden" name="grow_id" value="<?= $grow_id ?>">
          <div class="mb-3">
            <label class="form-label" for="content">Log Content</label>
            <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Add Log</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
