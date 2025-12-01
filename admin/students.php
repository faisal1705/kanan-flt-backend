<?php
require_once __DIR__ . '/../includes/functions.php';

require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ? OR phone LIKE ? ORDER BY updated_at DESC LIMIT 200");
    $like = '%' . $search . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY updated_at DESC LIMIT 200");
}
$rows = $stmt->fetchAll();
?>
<div class="container my-4">
  <h3>Students (from Consolidated Sheet)</h3>
  <a href="dashboard.php" class="btn btn-link mb-3">Back to Dashboard</a>

  <form class="row g-2 mb-3" method="GET">
    <div class="col-auto">
      <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Search by name or phone">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">Search</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-sm align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Batch</th>
          <th>Faculty</th>
          <th>RM</th>
          <th>Status</th>
          <th>Updated</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td><?php echo htmlspecialchars($r['phone']); ?></td>
            <td><?php echo htmlspecialchars($r['email']); ?></td>
            <td><?php echo htmlspecialchars($r['batch']); ?></td>
            <td><?php echo htmlspecialchars($r['faculty_name']); ?></td>
            <td><?php echo htmlspecialchars($r['rm']); ?></td>
            <td><?php echo htmlspecialchars($r['status']); ?></td>
            <td><?php echo htmlspecialchars($r['updated_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
