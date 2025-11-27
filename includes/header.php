<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config.php';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Kanan FLT System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
       .custom-navbar {
    /* Richer, darker gradient for a premium look */
    background: linear-gradient(90deg, #0f4c81 0%, #1f6cb1 100%); 
    padding-top: 15px;
    padding-bottom: 15px;
}

.custom-navbar .navbar-brand {
    color: #ffffff; /* White text for brand */
    font-size: 1.5rem;
    letter-spacing: 0.5px;
    transition: color 0.3s;
}

.custom-navbar .navbar-brand:hover {
    color: #e0f7fa; /* Light hover effect */
}

.custom-navbar .navbar-brand i {
    color: #ffd700; /* Gold/Yellow for the icon accent */
}

.custom-navbar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    margin-left: 10px;
    transition: color 0.3s;
}

.custom-navbar .nav-link:hover {
    color: #ffffff;
}
    </style>
</head>

<body>

<!-- ⭐ Modern Gradient Navbar ⭐ -->
<nav class="navbar navbar-expand-lg custom-navbar shadow-sm ">
  <div class="container-fluid px-4">

    <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/index.php">
      <i class="bi bi-check2-square me-2"></i>
      Kanan FLT System
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
    

      </ul>
    </div>

  </div>
</nav>

