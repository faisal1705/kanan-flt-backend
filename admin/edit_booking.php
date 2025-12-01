<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pdo = getPDO();

if (!isset($_GET['id'])) die("Invalid ID");

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM flt_bookings WHERE id=?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) die("Booking not found");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = $_POST['flt_day'];
    $type = $_POST['flt_type'];
    $date = $_POST['flt_date'];

    $update = $pdo->prepare("
        UPDATE flt_bookings 
        SET flt_day=?, flt_type=?, flt_date=? 
        WHERE id=?
    ");
    $update->execute([$day, $type, $date, $id]);

    header("Location: bookings.php?updated=1");
    exit;
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container my-4">
    <h3>Edit Booking</h3>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">FLT Day</label>
            <input type="text" name="flt_day" class="form-control" value="<?= $data['flt_day'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">FLT Type</label>
            <input type="text" name="flt_type" class="form-control" value="<?= $data['flt_type'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">FLT Date</label>
            <input type="date" name="flt_date" class="form-control" value="<?= $data['flt_date'] ?>">
        </div>

        <button class="btn btn-primary">Save Changes</button>
        <a href="bookings.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
