<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$stmt = $pdo->query("
    SELECT b.*, s.name, s.phone
    FROM flt_bookings b
    JOIN students s ON b.student_id = s.id
    ORDER BY b.created_at DESC
");
$rows = $stmt->fetchAll();
?>
<div class="container my-4">
  <h3>FLT Bookings</h3>
  <a href="dashboard.php" class="btn btn-link mb-3">Back to Dashboard</a>
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-sm align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Candidate No</th>
          <th>Name</th>
          <th>Phone</th>
          <th>FLT Day</th>
          <th>Type</th>
          <th>FLT Date</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['candidate_no']); ?></td>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td><?php echo htmlspecialchars($r['phone']); ?></td>
            <td><?php echo htmlspecialchars($r['flt_day']); ?></td>
            <td><?php echo htmlspecialchars($r['flt_type']); ?></td>
            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($r['flt_date']))); ?></td>
            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
