<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../includes/config.php';
$conn = getConnection();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username']; // Assuming the username is stored in session

// Fetch the most recent booking_id for this user
$recentBookingQuery = "SELECT * FROM bookings WHERE patient_name = ? ORDER BY booking_id DESC LIMIT 1";
$stmt = $conn->prepare($recentBookingQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$bookingResult = $stmt->get_result();
$booking = $bookingResult->fetch_assoc();

if ($booking) {
    $bookingId = $booking['booking_id'];

    // Fetch the invoice details associated with this booking
    $invoiceQuery = "SELECT * FROM invoices_list WHERE invoice_id = (SELECT invoice_id FROM invoices_list WHERE user_id = ? ORDER BY invoice_id DESC LIMIT 1)";
    $stmt = $conn->prepare($invoiceQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $invoiceResult = $stmt->get_result();
    $invoice = $invoiceResult->fetch_assoc();
} else {
    // Handle no booking found scenario
    $booking = null;
    $invoice = null;
}

$stmt->close();
$conn->close();
?>




<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AIC Kijabe Hospital Telemedicine</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <?php include '../includes/sidebar.php';?>
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
<?php include '../includes/head.php';?>
      <div class="container-fluid">
<!-- Doctor Schedules Section -->
<div class="row">
    <div class="col-lg-8 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body">
                <div class="container">
        <h1>Booking Confirmation</h1>
        <div class="booking-details">
            <h2>Booking Details</h2>
            <p><strong>Service:</strong> <?= htmlspecialchars($booking['service']) ?></p>
            <p><strong>Doctor:</strong> <?= htmlspecialchars($booking['doctor']) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($booking['time']) ?></p>
            <p><strong>Patient:</strong> <?= htmlspecialchars($booking['patient_name']) ?></p>
        </div>
        <div class="invoice-details">
            <h2>Invoice Details</h2>
            <p><strong>Invoice ID:</strong> <?= htmlspecialchars($invoice['invoice_id']) ?></p>
            <p><strong>Amount:</strong> KE <?= htmlspecialchars($invoice['amount']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($invoice['status']) ?></p>
            <p><strong>Time Raised:</strong> <?= htmlspecialchars($invoice['time_raised']) ?></p>
        </div>
        <div class="pay-now">
            <a href="pay_invoice.php?invoice_id=<?= urlencode($invoice['invoice_id']) ?>" class="btn btn-primary">Pay Now</a>
        </div>
    </div>
              
          </div>
        </div>
        







</div>

        </div>
        <?php include '../includes/foot.php';?>
      </div>
    </div>
  </div>
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
  <script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="assets/js/dashboard.js"></script>
</body>

</html>
