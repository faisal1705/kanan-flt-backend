<?php
require_once __DIR__ . '/../includes/functions.php';
//requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();
$studentsCount = $pdo->query("SELECT COUNT(*) AS c FROM students")->fetch()['c'] ?? 0;
$bookingsCount = $pdo->query("SELECT COUNT(*) AS c FROM flt_bookings")->fetch()['c'] ?? 0;
?>

<style>
/* ===== Dashboard Styling ===== */
.dashboard-card {
    border-radius: 14px;
    transition: 0.25s;
    border: none;
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.dashboard-icon {
    font-size: 42px;
    opacity: .8;
}

.btn-action {
    padding: 10px 22px;
    border-radius: 8px;
    font-weight: 600;
}

.footer-text {
    margin-top: 50px;
    color: #777;
    font-size: 13px;
    text-align: center;
}
</style>

<div class="container my-5">

    <h2 class="fw-bold mb-4">📊 Admin Dashboard</h2>

    <div class="row g-4">

        <!-- Total Students -->
        <div class="col-md-4">
            <div class="card shadow-sm dashboard-card p-3 text-center">
                <div class="dashboard-icon text-primary">👨‍🎓</div>
                <h5 class="mt-2 text-muted">Total Students</h5>
                <h1 class="fw-bold text-primary"><?php echo $studentsCount; ?></h1>
            </div>
        </div>

        <!-- Total FLT Bookings -->
        <div class="col-md-4">
            <div class="card shadow-sm dashboard-card p-3 text-center">
                <div class="dashboard-icon text-success">📘</div>
                <h5 class="mt-2 text-muted">Total FLT Bookings</h5>
                <h1 class="fw-bold text-success"><?php echo $bookingsCount; ?></h1>
            </div>
        </div>

        <!-- Reset FLT -->
        <div class="col-md-4">
            <div class="card shadow-sm dashboard-card p-3 text-center">
                <div class="dashboard-icon text-danger">♻️</div>
                <h5 class="mt-2 text-muted">Reset FLT Week</h5>

                <button class="btn btn-danger btn-action mt-2" onclick="confirmReset()">Reset Now</button>

                <p class="small text-muted mt-2">*Deletes FLT bookings from SQL only* <br> *(Google Sheet remains safe)*</p>
            </div>
        </div>

    </div>

    <!-- NAV BUTTONS -->
    <div class="text-center mt-5">
        <a href="settings.php" class="btn btn-primary btn-action me-2">⚙️ Settings</a>
        <a href="bookings.php" class="btn btn-secondary btn-action me-2">📄 View Bookings</a>
        <a href="students.php" class="btn btn-secondary btn-action me-2">🧑‍🎓 View Students</a>
        <a href="logout.php" class="btn btn-outline-danger btn-action">🚪 Logout</a>
    </div>

    <div class="footer-text">
        © 2025 Kanan FLT System
    </div>
</div>

<script>
function confirmReset() {
    if (confirm("⚠️ Are you sure you want to RESET this FLT week?\n\nThis will DELETE all FLT bookings from database ONLY.\nGoogle Sheet data will remain SAFE.")) {
        window.location.href = "reset_flt.php";
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
