<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();
require_once __DIR__ . '/../includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setSetting('Saturday', $_POST['Saturday'] ?? 'ON');
    setSetting('Sunday', $_POST['Sunday'] ?? 'ON');
    setSetting('AdminEmail', $_POST['AdminEmail'] ?? '');
    setSetting('TrainerList', $_POST['TrainerList'] ?? '');
    $message = 'Settings updated.';
}

$saturday = getSetting('Saturday', 'ON');
$sunday   = getSetting('Sunday', 'ON');
$adminEmail = getSetting('AdminEmail', '');
$trainerList = getSetting('TrainerList', '');
?>
<div class="container my-4">
  <h3>FLT Settings</h3>
  <?php if ($message): ?>
    <div class="alert alert-success py-2 mt-2"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <form method="POST" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Saturday</label>
      <select name="Saturday" class="form-select">
        <option value="ON"  <?php if ($saturday === 'ON') echo 'selected'; ?>>ON</option>
        <option value="OFF" <?php if ($saturday === 'OFF') echo 'selected'; ?>>OFF</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Sunday</label>
      <select name="Sunday" class="form-select">
        <option value="ON"  <?php if ($sunday === 'ON') echo 'selected'; ?>>ON</option>
        <option value="OFF" <?php if ($sunday === 'OFF') echo 'selected'; ?>>OFF</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Admin Email</label>
      <input type="email" name="AdminEmail" value="<?php echo htmlspecialchars($adminEmail); ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Trainer List (comma separated)</label>
      <textarea name="TrainerList" rows="3" class="form-control"><?php echo htmlspecialchars($trainerList); ?></textarea>
    </div>
    <button class="btn btn-primary">Save Settings</button>
    <a href="dashboard.php" class="btn btn-link">Back</a>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
