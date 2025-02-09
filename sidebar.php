<?php
// sidebar.php
$currentFile = basename($_SERVER['PHP_SELF']);
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <!-- App Brand -->
  <div class="app-brand demo">
    <a href="index.php" class="app-brand-link">
      <span class="app-brand-logo demo">
        <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M0.0017 0V6.85398C0.0017 6.85398 -0.1332 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.238 0H0.0017Z" fill="#7367F0"/>
          <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"/>
          <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616"/>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M7.773 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.773 16.3566Z" fill="#7367F0"/>
        </svg>
      </span>
      <span class="app-brand-text demo menu-text fw-bold">Vuexy</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
    </a>
  </div>
  <!-- /App Brand -->

  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
    <!-- Default Items -->
    <li class="menu-item <?= ($currentFile == 'index.php') ? 'active' : '' ?>">
      <a href="index.php" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div>Dashboard</div>
      </a>
    </li>
    <li class="menu-item <?= ($currentFile == 'edit_account.php') ? 'active' : '' ?>">
      <a href="edit_account.php" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user"></i>
        <div>Account</div>
      </a>
    </li>

    <!-- Cultivation Section -->
    <li class="menu-header small">
      <span class="menu-header-text">Cultivation</span>
    </li>

    <!-- Grows -->
    <li class="menu-item <?= (in_array($currentFile, ['grows.php', 'edit_grow.php'])) ? 'active open' : '' ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-plant"></i>
        <div>Grows</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= ($currentFile == 'grows.php') ? 'active' : '' ?>">
          <a href="grows.php" class="menu-link">
            <div>All Grows</div>
          </a>
        </li>
        <li class="menu-item <?= ($currentFile == 'edit_grow.php') ? 'active' : '' ?>">
          <a href="edit_grow.php" class="menu-link">
            <div>Add New Grow</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Plants -->
    <li class="menu-item <?= (in_array($currentFile, ['plants.php', 'edit_plant.php', 'plant.php'])) ? 'active open' : '' ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-leaf"></i>
        <div>Plants</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= ($currentFile == 'plants.php') ? 'active' : '' ?>">
          <a href="plants.php" class="menu-link">
            <div>All Plants</div>
          </a>
        </li>
        <li class="menu-item <?= ($currentFile == 'edit_plant.php') ? 'active' : '' ?>">
          <a href="edit_plant.php" class="menu-link">
            <div>Add New Plant</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Grow Spaces -->
    <li class="menu-item <?= (in_array($currentFile, ['spaces.php', 'edit_space.php'])) ? 'active open' : '' ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-layout-grid"></i>
        <div>Grow Spaces</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= ($currentFile == 'spaces.php') ? 'active' : '' ?>">
          <a href="spaces.php" class="menu-link">
            <div>All Grow Spaces</div>
          </a>
        </li>
        <li class="menu-item <?= ($currentFile == 'edit_space.php') ? 'active' : '' ?>">
          <a href="edit_space.php" class="menu-link">
            <div>Add Grow Space</div>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</aside>