<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php'; // Include your config file
require_once 'vendor/autoload.php'; // Include Composer autoloader


ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$conn = getConnection(); // Get the database connection

// Retrieve invoice ID from URL
$invoiceId = $_GET['invoice_id'] ?? null;

if (!$invoiceId) {
    echo "Invalid Invoice ID.";
    exit;
}

// Query to fetch invoice details with associated user and service names
$query = "SELECT i.invoice_id, u.name as user_name, s.name as service_name, i.amount, i.status, i.time_raised 
          FROM invoices_list i
          JOIN users u ON i.user_id = u.id
          JOIN services_list s ON i.service_id = s.id
          WHERE i.invoice_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Invoice not found.";
    exit;
}

$invoice = $result->fetch_assoc();

// Check if the invoice is paid
if ($invoice['status'] !== 'paid') {
    echo "Invoice is not paid.";
    exit;
}

// Generate PDF receipt
$pdfContent = "
    <h1>Invoice Receipt</h1>
    <p><strong>Invoice ID:</strong> {$invoice['invoice_id']}</p>
    <p><strong>User Name:</strong> {$invoice['user_name']}</p>
    <p><strong>Service Name:</strong> {$invoice['service_name']}</p>
    <p><strong>Amount:</strong> {$invoice['amount']}</p>
    <p><strong>Status:</strong> {$invoice['status']}</p>
    <p><strong>Time Raised:</strong> {$invoice['time_raised']}</p>
";



// Output the PDF as attachment for download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="invoice_receipt.pdf"');
echo $pdfOutput;

// Close statement and database connection
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
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



    <h1>Invoice Details</h1>
    <p>Invoice ID: <?= htmlspecialchars($invoice['invoice_id']) ?></p>
    <p>User Name: <?= htmlspecialchars($invoice['user_name']) ?></p>
    <p>Service Name: <?= htmlspecialchars($invoice['service_name']) ?></p>
    <p>Amount: <?= htmlspecialchars($invoice['amount']) ?></p>
    <p>Status: <?= htmlspecialchars($invoice['status']) ?></p>
    <p>Time Raised: <?= htmlspecialchars($invoice['time_raised']) ?></p>

    <!-- Download PDF button -->
            <button id="download-pdf">Download PDF</button>
            <script>
                document.getElementById('download-pdf').addEventListener('click', function() {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();

                    doc.text('Invoice Receipt', 10, 10);
                    doc.text(`Invoice ID: ${<?= json_encode($invoice['invoice_id']) ?>}`, 10, 20);
                    doc.text(`User Name: ${<?= json_encode($invoice['user_name']) ?>}`, 10, 30);
                    doc.text(`Service Name: ${<?= json_encode($invoice['service_name']) ?>}`, 10, 40);
                    doc.text(`Amount: ${<?= json_encode($invoice['amount']) ?>}`, 10, 50);
                    doc.text(`Status: ${<?= json_encode($invoice['status']) ?>}`, 10, 60);
                    doc.text(`Time Raised: ${<?= json_encode($invoice['time_raised']) ?>}`, 10, 70);

                    doc.save('invoice_receipt.pdf');
                });
            </script>
        </div>
        <?php include '../includes/foot.php'; ?>



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
