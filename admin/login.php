<?php
require_once __DIR__ . '/../includes/config.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    // Check credentials from config
    if ($user === ADMIN_USERNAME && $pass === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login - Kanan FLT System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
        /* Modern Gradient Background */
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        background: #fff;
        transition: transform 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        /* Matches your Index page gradient */
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-bottom: none;
        padding: 30px 20px;
        text-align: center;
        color: white;
    }

    .card-header h4 {
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }

    .card-header p {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: #4facfe;
        box-shadow: 0 0 0 0.25rem rgba(79, 172, 254, 0.25);
    }

    .btn-login {
        background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background: linear-gradient(90deg, #0b5ed7 0%, #0aa2c0 100%);
        box-shadow: 0 5px 15px rgba(13, 202, 240, 0.4);
    }

    .input-group-text {
        border-radius: 10px 0 0 10px;
        border: 1px solid #e9ecef;
        background-color: #fff;
        color: #4facfe;
    }
    
    .form-control {
        border-radius: 0 10px 10px 0;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
      
      <div class="card login-card">
        
        <div class="card-header">
          <div class="mb-2">
            <i class="bi bi-shield-lock-fill" style="font-size: 3rem;"></i>
          </div>
          <h4>Admin Portal</h4>
          <p>Kanan FLT System</p>
        </div>

        <div class="card-body p-4">
          
          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
              <i class="bi bi-exclamation-circle-fill me-2"></i>
              <div><?php echo htmlspecialchars($error); ?></div>
            </div>
          <?php endif; ?>

          <form method="POST">
            
            <div class="mb-4">
              <label class="form-label text-muted fw-bold small">USERNAME</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input name="username" class="form-control" placeholder="Enter username" required autofocus>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label text-muted fw-bold small">PASSWORD</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key"></i></span>
                <input name="password" type="password" class="form-control" placeholder="Enter password" required>
              </div>
            </div>

            <button class="btn btn-primary btn-login w-100 text-white mt-2">
              Sign In <i class="bi bi-arrow-right-short"></i>
            </button>

          </form>
        </div>
        
        <div class="card-footer bg-white text-center py-3 border-0">
          <small class="text-muted">© 2025 Kanan FLT System</small>
        </div>

      </div>

    </div>
  </div>
</div>

</body>
</html>
