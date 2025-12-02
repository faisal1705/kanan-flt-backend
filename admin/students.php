<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$search = trim($_GET['q'] ?? '');

$query = "
    SELECT *
    FROM students
";

$params = [];

if ($search !== '') {
    $query .= " WHERE 
        LOWER(name) LIKE ? OR 
        LOWER(phone) LIKE ? OR
        LOWER(email) LIKE ? OR
        LOWER(student_code) LIKE ? OR
        LOWER(batch) LIKE ?
    ";
    $like = '%' . strtolower($search) . '%';
    $params = [$like, $like, $like, $like, $like];
}

$query .= " ORDER BY updated_at DESC LIMIT 300";
$stmt = $pdo->prepare($query);
$stmt->execute($params);

$rows = $stmt->fetchAll();
?>
<head>
    <script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    let q = this.value;

    fetch("search_students.php?q=" + encodeURIComponent(q))
        .then(res => res.json())
        .then(rows => {
            let html = "";
            rows.forEach(r => {
                html += `
                <tr>
                    <td>${r.id}</td>
                    <td>${r.student_code}</td>
                    <td>${r.name}</td>
                    <td>${r.phone}</td>
                    <td>${r.email}</td>
                    <td>${r.batch}</td>
                    <td>${r.faculty_name}</td>
                    <td>${r.rm}</td>
                    <td><span class="badge-status">${r.status}</span></td>
                    <td>${r.updated_at}</td>
                    <td>
                        <a href="view_student.php?id=${r.id}" class="btn btn-sm btn-info">View</a>
                        <a href="edit_student.php?id=${r.id}" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_student.php?id=${r.id}" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this student?');">Delete</a>
                    </td>
                </tr>`;
            });
            document.getElementById("studentsBody").innerHTML = html;
        });
});
</script>

<style>
.table-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.badge-status {
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
}
.badge-active { background:#198754; color:white; }
.badge-inactive { background:#dc3545; color:white; }
.badge-followup { background:#0d6efd; color:white; }
</style>
</head>
<div class="container my-4">

    <h3 class="fw-bold mb-3">👨‍🎓 Students</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- SEARCH BOX -->
    <form id="searchForm" class="row g-2 mb-3" method="GET">
        <div class="col-md-4">
            <input 
                type="text" 
                name="q" 
                id="searchInput"
                class="form-control"
                placeholder="Search name, phone, email, student code, batch..."
                value="<?php echo htmlspecialchars($search); ?>"
            >
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Search</button>
        </div>
        <div class="col-auto">
            <a href="students.php" class="btn btn-outline-danger">Reset</a>
        </div>
        <div class="col-auto">
            <a href="export_students.php" class="btn btn-success">⬇ Export CSV</a>
        </div>
    </form>

    <div id="tableContainer" class="table-card">
        <table class="table table-hover table-bordered align-middle">
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
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody id="studentsBody">
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['student_code']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['batch']) ?></td>
                    <td><?= htmlspecialchars($r['faculty_name']) ?></td>
                    <td><?= htmlspecialchars($r['rm']) ?></td>

                    <td>
                        <?php
                            $status = strtolower($r['status']);
                            $badgeClass = $status === "active" ? "badge-active"
                                        : ($status === "need follow up" ? "badge-followup" : "badge-inactive");
                        ?>
                        <span class="badge-status <?= $badgeClass ?>">
                            <?= htmlspecialchars($r['status']) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($r['updated_at']) ?></td>

                    <td>
                        <a href="view_student.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info">View</a>
                        <a href="edit_student.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_student.php?id=<?= $r['id'] ?>" 
                           onclick="return confirm('Delete this student?');" 
                           class="btn btn-sm btn-danger">Delete</a>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
