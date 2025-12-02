<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$stmt = $pdo->query("SELECT * FROM students ORDER BY updated_at DESC LIMIT 200");
$rows = $stmt->fetchAll();
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

.badge-status {
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 13px;
}

.status-active { background:#198754; color:white; }
.status-hold   { background:#ffc107; color:black; }
.status-proc   { background:#0d6efd; color:white; }
.status-other  { background:#6c757d; color:white; }

.view-btn { background:#0d6efd; }
.edit-btn { background:#0dcaf0; }
.delete-btn { background:#dc3545; }
</style>

<div class="container my-4">
    <h3 class="fw-bold mb-3">👨‍🎓 Students (Synced from Consolidated Sheet)</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- 🔍 AJAX Search -->
    <input type="text" id="search" class="form-control search-box mb-3"
           placeholder="Search by name, phone, email, batch, faculty…">

    <div class="table-card">
        <table class="table table-hover table-bordered align-middle" id="studentsTable">
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
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['student_code']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['batch']) ?></td>
                    <td><?= htmlspecialchars($r['faculty_name']) ?></td>
                    <td><?= htmlspecialchars($r['rm']) ?></td>

                    <td>
                        <span class="badge-status 
                            <?= ($r['status']=='Active'?'status-active':
                                 ($r['status']=='On Hold'?'status-hold':
                                 ($r['status']=='Process Started'?'status-proc':'status-other'))) ?>">
                            <?= htmlspecialchars($r['status']) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($r['updated_at']) ?></td>

                    <td>
                        <a href="view_student.php?id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-primary view-btn mb-1">View</a>

                        <a href="edit_student.php?id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-info edit-btn mb-1">Edit</a>

                        <a href="delete_student.php?id=<?= $r['id'] ?>" 
                           onclick="return confirm('Delete this student?');"
                           class="btn btn-sm btn-danger delete-btn">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- AJAX SEARCH SCRIPT -->
<script>
document.getElementById("search").addEventListener("keyup", function () {
    let query = this.value;

    fetch("search_students.php?q=" + encodeURIComponent(query))
        .then(res => res.text())
        .then(html => {
            document.getElementById("studentBody").innerHTML = html;
        });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
