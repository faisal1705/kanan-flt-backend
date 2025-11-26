<?php
require_once __DIR__ . '/includes/header.php';
$candidate = isset($_GET['candidate']) ? htmlspecialchars($_GET['candidate']) : null;
?>
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h3 class="text-success mb-3">Booking Successful!</h3>
          <?php if ($candidate): ?>
            <p>Your Candidate Number is:</p>
            <h2 class="display-5 fw-bold"><?php echo $candidate; ?></h2>
          <?php else: ?>
            <p>Your FLT booking has been recorded.</p>
          <?php endif; ?>
          <p class="mt-3 text-muted">A confirmation email has been sent to your registered email address.</p>
          <a href="index.php" class="btn btn-primary mt-3">Book Another FLT</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
