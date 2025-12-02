<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

$search   = trim($_GET['q'] ?? "");
$fltDay   = trim($_GET['day'] ?? "");
$fltType  = trim($_GET['type'] ?? "");

$sql = "
    SELECT b.*, s.name, s.phone, s.student_code
    FROM flt_bookings b
    JOIN students s ON b.student_id = s.id
    WHERE 1
";

$params = [];

// 🔍 Dynamic case-insensitive search
if ($search !== "") {
    $sql .= " AND (
        LOWER(s.name) LIKE LOWER(?) OR
        LOWER(s.phone) LIKE LOWER(?) OR
        LOWER(b.candidate_no) LIKE LOWER(?) OR
        LOWER(s.student_code) LIKE LOWER(?)
    )";
    $w = "%" . $search . "%";
    $params = [$w, $w, $w, $w];
}

if ($fltDay !== "") {
    $sql .= " AND b.flt_day = ?";
    $params[] = $fltDay;
}

if ($fltType !== "") {
    $sql .= " AND b.flt_type = ?";
    $params[] = $fltType;
}

$sql .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<style>
.table-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
th { background:#003366!important; color:#fff!important; }
.badge-day { background:#0d6efd; }
.badge-type { background:#198754; }
.badge-id { background:#6c757d; }
.action-btn { padding:4px 8px; font-size:13px; }
</style>

<div class="container my-4">
    <h3 class="fw-bold mb-3">📘 FLT Bookings</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <!-- SEARCH & FILTER BAR -->
    <form id="searchForm" method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="q" id="searchInput" class="form-control"
                   placeholder="Search name, phone, candidate no, student code"
                   value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="col-md-2">
            <select name="day" class="form-control">
                <option value="">FLT Day</option>
                <option value="Saturday- Slot 1" <?= $fltDay=="Saturday- Slot 1"?"selected":"" ?>>Saturday Slot 1</option>
                <option value="Saturday- Slot 2" <?= $fltDay=="Saturday- Slot 2"?"selected":"" ?>>Saturday Slot 2</option>
                <option value="Sunday" <?= $fltDay=="Sunday"?"selected":"" ?>>Sunday</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="type" class="form-control">
                <option value="">Type</option>
                <option value="Pen Paper" <?= $fltType=="Pen Paper"?"selected":"" ?>>Pen Paper</option>
                <option value="CBT" <?= $fltType=="CBT"?"selected":"" ?>>CBT</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <div class="table-card">
        <table class="table table-hover table-bordered align-middle" id="bookingsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Code</th>
                    <th>Candidate No</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>FLT Day</th>
                    <th>Type</th>
                    <th>FLT Date</th>
                    <th>Created</th>
                    <th width="130">Actions</th>
                </tr>
            </thead>

            <tbody id="resultsBody">
                <?php if (empty($rows)): ?>
                    <tr><td colspan="10" class="text-center text-danger">No bookings found</td></tr>
                <?php endif; ?>

                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><span class="badge badge-id"><?= htmlspecialchars($r['student_code']) ?></span></td>
                    <td><?= htmlspecialchars($r['candidate_no']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>

                    <td><span class="badge badge-day"><?= $r['flt_day'] ?></span></td>
                    <td><span class="badge badge-type"><?= $r['flt_type'] ?></span></td>

                    <td><?= date('d-m-Y', strtotime($r['flt_date'])) ?></td>
                    <td><?= $r['created_at'] ?></td>

                    <td>
                        <a href="edit_booking.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary action-btn">Edit</a>
                        <a href="delete_booking.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger action-btn"
                           onclick="return confirm('Delete this booking?');">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- AJAX LIVE SEARCH -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    document.getElementById("searchForm").submit();
});
</script>
