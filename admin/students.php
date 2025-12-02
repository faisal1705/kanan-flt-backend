<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$stmt = $pdo->query("SELECT * FROM students ORDER BY updated_at DESC LIMIT 200");
$rows = $stmt->fetchAll();

// Dropdown filter values
$filterBatch = $pdo->query("SELECT DISTINCT batch FROM students ORDER BY batch")->fetchAll();
$filterFaculty = $pdo->query("SELECT DISTINCT faculty_name FROM students ORDER BY faculty_name")->fetchAll();
$filterRM = $pdo->query("SELECT DISTINCT rm FROM students ORDER BY rm")->fetchAll();
$filterStatus = $pdo->query("SELECT DISTINCT status FROM students ORDER BY status")->fetchAll();
?>

<style>
.table-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

th {
    background: #003366 !important;
    color: #fff !important;
}

.search-box {
    width: 350px;
    border-radius: 25px;
    padding: 8px 15px;
}

.filter-select {
    width: 180px;
}

.status-badge {
    white-space: nowrap;
    padding: 4px 10px;
    display: inline-block;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
}
.status-prep { background:#6c757d; color:white; }
.status-booked { background:#0d6efd; color:white; }
.status-active { background:#198754; color:white; }
.status-hold { background:#ffc107; color:black; }
.status-proc { background:#0d6efd; color:white; }

.view-btn { background:#0d6efd; }
.edit-btn { background:#0dcaf0; }
.delete-btn { background:#dc3545; }
</style>

<div class="container my-4">
    <h3 class="fw-bold mb-3">📚 Students (Synced from Consolidated Sheet)</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- 🔍 SEARCH + FILTERS -->
    <div class="row g-3 mb-3">

        <div class="col-md-4">
            <input type="text" id="search" class="form-control search-box"
                   placeholder="Search by name, phone, email, faculty, batch…">
        </div>

        <div class="col-md-2">
            <select id="filterBatch" class="form-select filter-select">
                <option value="">All Batch</option>
                <?php foreach ($filterBatch as $b): ?>
                    <option><?= htmlspecialchars($b['batch']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select id="filterFaculty" class="form-select filter-select">
                <option value="">All Faculty</option>
                <?php foreach ($filterFaculty as $f): ?>
                    <option><?= htmlspecialchars($f['faculty_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select id="filterRM" class="form-select filter-select">
                <option value="">All RM</option>
                <?php foreach ($filterRM as $r): ?>
                    <option><?= htmlspecialchars($r['rm']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select id="filterStatus" class="form-select filter-select">
                <option value="">All Status</option>
                <?php foreach ($filterStatus as $s): ?>
                    <option><?= htmlspecialchars($s['status']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <div class="table-card">
        <table class="table table-hover table-bordered align-middle">
            <thead>
                <tr>
                    <th>Student Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Batch</th>
                    <th>Faculty</th>
                    <th>RM</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="studentBody">
                <?php include 'search_students.php'; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 🔥 AJAX Live Search + Filters -->
<script>
function loadStudents() {
    let q = document.getElementById("search").value;
    let b = document.getElementById("filterBatch").value;
    let f = document.getElementById("filterFaculty").value;
    let r = document.getElementById("filterRM").value;
    let s = document.getElementById("filterStatus").value;

    fetch("search_students.php?q=" + encodeURIComponent(q)
        + "&batch=" + encodeURIComponent(b)
        + "&faculty=" + encodeURIComponent(f)
        + "&rm=" + encodeURIComponent(r)
        + "&status=" + encodeURIComponent(s)
    )
    .then(res => res.text())
    .then(html => {
        document.getElementById("studentBody").innerHTML = html;
    });
}

document.getElementById("search").addEventListener("keyup", loadStudents);
document.getElementById("filterBatch").addEventListener("change", loadStudents);
document.getElementById("filterFaculty").addEventListener("change", loadStudents);
document.getElementById("filterRM").addEventListener("change", loadStudents);
document.getElementById("filterStatus").addEventListener("change", loadStudents);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
