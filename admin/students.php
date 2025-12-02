<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// If AJAX search request
if (isset($_GET['ajax']) && isset($_GET['q'])) {
    echo json_encode(searchStudents($pdo, $_GET['q']));
    exit;
}

// Normal page load
$rows = searchStudents($pdo, $_GET['q'] ?? '');
?>

<div class="container my-4">
  <h3>Students (from Consolidated Sheet)</h3>
  <a href="dashboard.php" class="btn btn-link mb-3">Back to Dashboard</a>

  <!-- 🔍 Dynamic Search Box -->
  <input type="text" id="searchBox" class="form-control mb-3" placeholder="Search by name, phone, email, student code, batch..." autocomplete="off">

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-sm align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Student Code</th>
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
      <tbody id="studentTable">
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['student_code']) ?></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['batch']) ?></td>
            <td><?= htmlspecialchars($r['faculty_name']) ?></td>
            <td><?= htmlspecialchars($r['rm']) ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
            <td><?= htmlspecialchars($r['updated_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 🔥 AJAX Live Search Script -->
<script>
document.getElementById("searchBox").addEventListener("keyup", function () {
    let q = this.value;

    fetch("students.php?ajax=1&q=" + encodeURIComponent(q))
        .then(res => res.json())
        .then(rows => {
            let tbody = document.getElementById("studentTable");
            tbody.innerHTML = "";

            rows.forEach(r => {
                tbody.innerHTML += `
                <tr>
                    <td>${r.id}</td>
                    <td>${r.student_code}</td>
                    <td>${r.name}</td>
                    <td>${r.phone}</td>
                    <td>${r.email}</td>
                    <td>${r.batch}</td>
                    <td>${r.faculty_name}</td>
                    <td>${r.rm}</td>
                    <td>${r.status}</td>
                    <td>${r.updated_at}</td>
                </tr>`;
            });
        });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
