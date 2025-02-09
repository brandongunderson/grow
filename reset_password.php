<?php
session_start();
require_once 'db.php';

$error = "";
$success = "";

// Get the token from the URL.
$token = $_GET['token'] ?? "";

if ($token == "") {
    $error = "Invalid or missing token.";
} else {
    $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $error = "Invalid token.";
    } elseif (strtotime($user['reset_expires']) < time()) {
        $error = "This reset link has expired.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($error)) {
    $new_password = $_POST['new_password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";
    
    if ($new_password == "" || $confirm_password == "") {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirmation do not match.";
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id");
        if ($stmt->execute(['password' => $new_hashed_password, 'id' => $user['id']])) {
            $success = "Your password has been updated successfully.";
        } else {
            $error = "Failed to update password. Please try again.";
        }
    }
}
?>
<!doctype html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="./assets/" data-template="vertical-menu-template-no-customizer" data-style="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Reset Password - Pages | Vuexy - Bootstrap Admin Template</title>
  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="./assets/img/favicon/favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./assets/vendor/fonts/fontawesome.css" />
  <link rel="stylesheet" href="./assets/vendor/fonts/tabler-icons.css" />
  <link rel="stylesheet" href="./assets/vendor/fonts/flag-icons.css" />
  <link rel="stylesheet" href="./assets/vendor/css/rtl/core.css" />
  <link rel="stylesheet" href="./assets/vendor/css/rtl/theme-default.css" />
  <link rel="stylesheet" href="./assets/css/demo.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/node-waves/node-waves.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/typeahead-js/typeahead.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/@form-validation/form-validation.css" />
  <link rel="stylesheet" href="./assets/vendor/css/pages/page-auth.css" />
  <script src="./assets/vendor/js/helpers.js"></script>
  <script src="./assets/js/config.js"></script>
</head>
<body>
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-6">
        <div class="card">
          <div class="card-body">
            <div class="app-brand justify-content-center mb-6">
              <a href="index.php" class="app-brand-link">
                <span class="app-brand-logo demo">
                  <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0" />
                  </svg>
                </span>
                <span class="app-brand-text demo text-heading fw-bold">Vuexy</span>
              </a>
            </div>
            <h4 class="mb-1">Reset Your Password</h4>
            <p class="mb-6">Enter your new password.</p>
            
            <?php if($error != ""): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if($success != ""): ?>
              <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (empty($error) && $token != "" && isset($user) && $user): ?>
            <form id="formResetPassword" action="" method="POST">
              <div class="mb-3 form-password-toggle">
                <label class="form-label" for="new_password">New Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password" />
                  <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                </div>
              </div>
              <div class="mb-4 form-password-toggle">
                <label class="form-label" for="confirm_password">Confirm New Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" />
                  <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100" type="submit">Reset Password</button>
            </form>
            <?php endif; ?>

            <p class="text-center mt-4">
              <a href="login.php">Back to Login</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="./assets/vendor/libs/jquery/jquery.js"></script>
  <script src="./assets/vendor/libs/popper/popper.js"></script>
  <script src="./assets/vendor/js/bootstrap.js"></script>
  <script src="./assets/vendor/libs/node-waves/node-waves.js"></script>
  <script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="./assets/vendor/libs/hammer/hammer.js"></script>
  <script src="./assets/vendor/libs/i18n/i18n.js"></script>
  <script src="./assets/vendor/libs/typeahead-js/typeahead.js"></script>
  <script src="./assets/vendor/js/menu.js"></script>
  <!-- endbuild -->
  <!-- Vendors JS -->
  <script src="./assets/vendor/libs/@form-validation/popular.js"></script>
  <script src="./assets/vendor/libs/@form-validation/bootstrap5.js"></script>
  <script src="./assets/vendor/libs/@form-validation/auto-focus.js"></script>
  <!-- Main JS -->
  <script src="./assets/js/main.js"></script>
  <!-- Page JS -->
  <script src="./assets/js/pages-auth.js"></script>
</body>
</html>
