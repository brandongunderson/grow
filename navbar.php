<?php
// navbar.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$avatar = './assets/img/avatars/1.png';
$fullName = "John Doe";
if (isset($_SESSION['user_id'])) {
    require_once 'db.php';
    $stmt = $pdo->prepare("SELECT profile_picture, full_name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userData) {
        if (!empty($userData['profile_picture'])) {
            $avatar = $userData['profile_picture'];
        }
        if (!empty($userData['full_name'])) {
            $fullName = $userData['full_name'];
        }
    }
}
?>
<div class="layout-page">
  <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="ti ti-menu-2 ti-md"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
      <!-- Search Bar -->
      <div class="navbar-nav align-items-center">
        <div class="nav-item navbar-search-wrapper mb-0">
          <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
            <i class="ti ti-search ti-md me-2 me-lg-4 ti-lg"></i>
            <span class="d-none d-md-inline-block text-muted fw-normal">Search (Ctrl+/)</span>
          </a>
        </div>
      </div>
      <!-- /Search -->

      <!-- Right-side Items -->
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- Quick Links Dropdown (same as before) -->
        <!-- ... (code unchanged) ... -->

        <!-- Notifications Dropdown (same as before) -->
        <!-- ... (code unchanged) ... -->

        <!-- User Dropdown -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar">
              <img src="<?= htmlspecialchars($avatar) ?>" alt="User Avatar" class="rounded-circle" />
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item mt-0" href="edit_account.php">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-2">
                    <div class="avatar">
                      <img src="<?= htmlspecialchars($avatar) ?>" alt="User Avatar" class="rounded-circle" />
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0"><?= htmlspecialchars($fullName) ?></h6>
                    <small class="text-muted">Admin</small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider my-1 mx-n2"></div>
            </li>
            <li>
              <a class="dropdown-item" href="pages-profile-user.html">
                <i class="ti ti-user me-3 ti-md"></i><span class="align-middle">My Profile</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="edit_account.php">
                <i class="ti ti-settings me-3 ti-md"></i><span class="align-middle">Account Settings</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="pages-account-settings-billing.html">
                <span class="d-flex align-items-center align-middle">
                  <i class="flex-shrink-0 ti ti-file-dollar me-3 ti-md"></i>
                  <span class="flex-grow-1 align-middle">Billing</span>
                  <span class="flex-shrink-0 badge bg-danger d-flex align-items-center justify-content-center">4</span>
                </span>
              </a>
            </li>
            <li>
              <div class="dropdown-divider my-1 mx-n2"></div>
            </li>
            <li>
              <a class="dropdown-item" href="pages-account-settings-notifications.html">
                <i class="ti ti-bell me-3 ti-md"></i><span class="align-middle">Notifications</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="pages-account-settings-connections.html">
                <i class="ti ti-link me-3 ti-md"></i><span class="align-middle">Connections</span>
              </a>
            </li>
            <li>
              <div class="dropdown-divider my-1 mx-n2"></div>
            </li>
            <li>
              <a class="dropdown-item" href="logout.php">
                <i class="ti ti-logout me-3 ti-md"></i><span class="align-middle">Logout</span>
              </a>
            </li>
          </ul>
        </li>
        <!-- End User Dropdown -->
      </ul>
    </div>
    
    <!-- Search Input for Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper d-none">
      <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..." aria-label="Search..." />
      <i class="ti ti-x search-toggler cursor-pointer"></i>
    </div>
  </nav>
