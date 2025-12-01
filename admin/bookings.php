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
.badge-day { background:#0d6efd; }
.badge-type { background:#198754; }
.action-btn { padding: 4px 8px; font-size: 13px; }
</style>

<div class="container my-4">
    <h3 class="mb-3 fw-bold">📘 FLT Bookings</h3>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <div class="table-card">
        <table class="table table-hover table-bordered align-middle">
            <thead>
                <tr>
                    <th>ID</th>
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
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['candidate_no']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['phone']) ?></td>

                    <td>
                        <span class="badge badge-day">
                            <?= htmlspecialchars($r['flt_day']) ?>
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-type">
                            <?= htmlspecialchars($r['flt_type']) ?>
                        </span>
                    </td>

                    <td><?= date('d-m-Y', strtotime($r['flt_date'])) ?></td>
                    <td><?= $r['created_at'] ?></td>

                    <td>
                        <a href="edit_booking.php?id=<?= $r['id'] ?>" 
                           class="btn btn-sm btn-primary action-btn">Edit</a>

                        <a href="delete_booking.php?id=<?= $r['id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this booking?');"
                           class="btn btn-sm btn-danger action-btn">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
