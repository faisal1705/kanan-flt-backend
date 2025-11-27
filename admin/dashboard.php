<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$studentsCount = $pdo->query("SELECT COUNT(*) AS c FROM students")->fetch()['c'] ?? 0;
$bookingsCount = $pdo->query("SELECT COUNT(*) AS c FROM flt_bookings")->fetch()['c'] ?? 0;
?>
<div class="container my-4">
  <h3 class="mb-4">Admin Dashboard</h3>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Total Students</h5>
          <h3><?php echo (int)$studentsCount; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Total FLT Bookings</h5>
          <h3><?php echo (int)$bookingsCount; ?></h3>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-4">
    <a href="settings.php" class="btn btn-primary me-2">Settings</a>
    <a href="bookings.php" class="btn btn-secondary me-2">View Bookings</a>
    <a href="students.php" class="btn btn-secondary me-2">View Students</a>
    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
