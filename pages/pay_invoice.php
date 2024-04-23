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

$invoiceId = $_GET['invoice_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    $invoiceId = $_POST['invoice_id'];
    $amountPaid = $_POST['amount'];  // This would come from a payment gateway in a real scenario

    
    // Update the invoice status to 'Paid', store payer_phone, and update the code
    $updateSql = "UPDATE invoices_list SET status = 'Paid', payer_phone = ?, code = ? WHERE invoice_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssi", $_POST['payer_phone'], $_POST['confirmation_code'], $invoiceId);
    $stmt->execute();
    $stmt->close();

    // Redirect to a confirmation page or back to invoices list
    header('Location: payment_success.php');
    exit();
}

// Fetch the invoice details to confirm the right one is being paid
if ($invoiceId) {
    $query = "SELECT * FROM invoices_list WHERE invoice_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $invoiceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Invalid Invoice ID.";
    exit;
}
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
        <h1>Pay Invoice</h1>
        <?php if (isset($invoice)): ?>
            <div class="invoice-details">
                <p><strong>Invoice ID:</strong> <?= htmlspecialchars($invoice['invoice_id']) ?></p>
                <p><strong>Amount Due:</strong>KE <?= htmlspecialchars($invoice['amount']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($invoice['status']) ?></p>
            </div>

            <?php if ($invoice['status'] !== 'Paid'): ?>
                <form action="pay_invoice.php" method="post">
                    <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($invoiceId) ?>">
                    <input type="hidden" name="amount" value="<?= htmlspecialchars($invoice['amount']) ?>">
                    <!-- Payer Phone Input -->
                    <div class="mb-3">
                        <label for="payerPhone" class="form-label">Payer Phone:</label>
                        <input type="text" class="form-control" id="payerPhone" name="payer_phone" required placeholder="Enter your phone number">
                    </div>

                    <!-- Confirmation Code Input -->
                    <div class="mb-3">
                        <label for="confirmationCode" class="form-label">Confirmation Code:</label>
                        <input type="text" class="form-control" id="confirmationCode" name="confirmation_code" required placeholder="Enter confirmation code">
                </div>
                    <button type="submit" name="pay_now" class="btn btn-primary">Pay Now</button>
                </form>
            <?php else: ?>
                <p>This invoice has already been paid.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Invoice not found or has been removed.</p>
        <?php endif; ?>
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
