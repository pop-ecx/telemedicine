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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Get the invoice ID either from POST (if the form was submitted) or GET (initial page load)
$invoiceId = $_POST['invoice_id'] ?? $_GET['invoice_id'] ?? null;

if (!$invoiceId) {
    echo "Invalid Invoice ID.";
    exit;
}

// Attempt to fetch the invoice details first to ensure it's valid before any processing
$query = "SELECT * FROM invoices_list WHERE invoice_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$result = $stmt->get_result();

if ($invoice = $result->fetch_assoc()) {
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
        // Since the invoice exists, process payment and update database
        $updateSql = "UPDATE invoices_list SET status = 'Paid', payer_phone = ?, code = ? WHERE invoice_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssi", $_POST['payer_phone'], $_POST['confirmation_code'], $invoiceId);
        $stmt->execute();
        $stmt->close();

        // After updating, send confirmation email
        $mail = new PHPMailer(true); // Exception handling enabled
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'telemed@kijabehospital.org';
            $mail->Password = 'Kijabe@2024###';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('telemed@kijabehospital.org', 'Telemedicine Services');
            $mail->addAddress($_POST['payer_email'], 'User'); // Add recipient from form
            $mail->addAddress('ictmgr@kijabehospital.org', 'Correspondent'); // Additional recipient

            $mail->isHTML(true);
            $mail->Subject = 'Invoice Payment Confirmation';
            $mail->Body = 'Your payment for Invoice ID ' . $invoiceId . ' has been successfully processed.';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

        header('Location: payment_success.php');
        exit();
    }
} else {
    echo "Invoice not found.";
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
