<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* New: Modern background for the page */
    body {
        background-color: #f0f2f5; /* Light gray background */
        background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
    }

    .booking-card {
        border-radius: 18px; /* Softer rounded corners */
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Elevated, softer shadow */
        transition: transform 0.3s ease;
    }
    /* Add hover effect for a premium feel */
    .booking-card:hover {
        transform: translateY(-3px);
    }
    
    /* Enhanced Gradient Header */
    .booking-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); /* Bright, modern blue-to-cyan gradient */
        padding: 30px 25px; /* Slightly more padding */
        text-align: center;
        color: #fff;
        position: relative;
    }
    /* Subtle geometric shape in the header */
    .booking-header::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 10px;
        background: rgba(255, 255, 255, 0.1);
        clip-path: polygon(0 0, 100% 0, 70% 100%, 30% 100%);
    }

    .booking-header h4 {
        margin: 0;
        font-size: 1.8rem; /* Slightly larger, clearer font */
        font-weight: 800; /* Bolder */
        letter-spacing: 1px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .booking-body {
        padding: 40px 35px; /* More breathable space */
        background: #ffffff;
    }
    
    /* Updated Info Box */
    .info-box {
        background: #e6f7ff; /* Lighter, calming blue */
        border-left: 6px solid #1890ff; /* Thicker, punchier accent line */
        padding: 18px;
        border-radius: 8px;
        font-size: 1rem;
        margin-bottom: 30px;
        color: #333;
        line-height: 1.5;
    }
    
    .info-box strong {
        color: #004d99; /* Darker blue for emphasis */
    }

    /* Primary Button Styling */
    .btn-primary {
        background: linear-gradient(90deg, #1890ff 0%, #0050b3 100%); /* Strong gradient for the button */
        border: none;
        padding: 15px 0; /* Taller button */
        font-size: 1.15rem;
        font-weight: 700;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(24, 144, 255, 0.4);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #0050b3 0%, #1890ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(24, 144, 255, 0.6);
    }
    
    /* Input/Select Field Styling */
    .form-control-lg, .form-select-lg {
        border-radius: 8px;
        padding: 12px 15px; /* More padding */
        border: 1px solid #d9d9d9;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control-lg:focus, .form-select-lg:focus {
        border-color: #4facfe;
        box-shadow: 0 0 0 0.25rem rgba(79, 172, 254, 0.25);
    }
    
    .form-label {
        font-weight: 700; /* Bolder label */
        color: #1a1a1a;
        margin-bottom: 8px;
        display: block;
    }
    
    /* Icon alignment next to labels */
    .form-label i {
        margin-right: 8px;
        color: #1890ff; /* Blue icons */
    }

    /* Small adjustment to the container for better centering */
    .container {
        max-width: 1100px;
    }
</style>
<script>
document.querySelector("form").addEventListener("submit", function(e) {
    let phone = document.getElementById("phone").value;

    if (!/^\d{10}$/.test(phone)) {
        e.preventDefault();
        alert("Please enter a valid 10-digit phone number.");
    }
});
</script>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">

      <div class="card shadow booking-card">
        
        <div class="booking-header">
          <h4><i class="bi bi-calendar-check"></i> FLT Booking Form</h4>
        </div>

        <div class="booking-body">
          
          <div class="info-box">
            <strong>Note:</strong> Booking is open <b>Tuesday to Thursday</b>.  
            Please enter your registered phone number.
          </div>

          <form action="https://kanan-flt-backend.onrender.com/book.php" method="POST">


            <div class="mb-3">
              <label class="form-label"><i class="bi bi-phone"></i> Registered Phone Number</label>
              <input type="text" name="phone" class="form-control form-control-lg" pattern="\d{10}" maxlength="10" minlength="10" inputmode="numeric" placeholder="Enter phone number" required>
            </div>

            <div class="mb-3">
              <label class="form-label"><i class="bi bi-envelope"></i> Email (for confirmation)</label>
              <input type="email" name="email" class="form-control form-control-lg" placeholder="example@gmail.com" required>
            </div>

            <div class="mb-3">
              <label class="form-label"><i class="bi bi-calendar-event"></i> FLT Day</label>
              <select name="flt_day" class="form-select form-select-lg" required>
                <option value="">Select your day</option>
                <option value="Saturday- Slot 1">Saturday - Slot 1 (Morning)</option>
                <option value="Saturday- Slot 2">Saturday - Slot 2 (Mid-day)</option>
                <option value="Sunday">Sunday</option>
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label"><i class="bi bi-pencil-square"></i> FLT Type</label>
              <select name="flt_type" class="form-select form-select-lg" required>
                <option value="Pen Paper">Pen-Paper</option>
                <option value="CBT">CBT (Computer Based)</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-check-circle"></i> Book My FLT Slot
            </button>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
