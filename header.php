<?php
// header.php
if (!isset($pageTitle)) {
    $pageTitle = "My Application";
}
if (!isset($pageDescription)) {
    $pageDescription = "Welcome to My Application";
}
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="./assets/" data-template="vertical-menu-template-no-customizer" data-style="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="./assets/img/favicon/favicon.ico" />
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet" />
  <!-- Icons -->
  <link rel="stylesheet" href="./assets/vendor/fonts/fontawesome.css" />
  <link rel="stylesheet" href="./assets/vendor/fonts/tabler-icons.css" />
  <link rel="stylesheet" href="./assets/vendor/fonts/flag-icons.css" />
  <!-- Core CSS -->
  <link rel="stylesheet" href="./assets/vendor/css/rtl/core.css" />
  <link rel="stylesheet" href="./assets/vendor/css/rtl/theme-default.css" />
  <link rel="stylesheet" href="./assets/css/demo.css" />
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="./assets/vendor/libs/node-waves/node-waves.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/typeahead-js/typeahead.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/select2/select2.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/@form-validation/form-validation.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/animate-css/animate.css" />
  <link rel="stylesheet" href="./assets/vendor/libs/sweetalert2/sweetalert2.css" />

  <!-- Helpers -->
  <script src="./assets/vendor/js/helpers.js"></script>
  <script src="./assets/js/config.js"></script>
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
