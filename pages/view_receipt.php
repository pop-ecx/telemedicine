<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php'; // Include your config file
require_once 'vendor/autoload.php'; // Include Composer autoloader




session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$conn = getConnection(); // Get the database connection

// Check if the download request is made
if (isset($_GET['download_invoice'])) {
    $invoice_id = intval($_GET['download_invoice']);
    $query = "SELECT * FROM invoices_list WHERE invoice_id = ? AND status = 'paid'";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($invoice = $result->fetch_assoc()) {
            // Here you need to generate the HTML for your invoice
            $htmlContent = "<h1>Invoice Details</h1>
                            <p>Invoice ID: {$invoice['invoice_id']}</p>
                            <p>Amount: \${$invoice['amount']}</p>
                            <p>Date: {$invoice['time_raised']}</p>";

            $dompdf = new Dompdf();
            $dompdf->loadHtml($htmlContent); // Load your HTML into dompdf
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $stmt->close();
            $conn->close();

            // Output the generated PDF to Browser
            $dompdf->stream("invoice-$invoice_id.pdf", array("Attachment" => true));
            exit();
        }
    }
    echo "Unable to fetch invoice details.";
    $conn->close();
    exit();
}

// Get the user ID from session
$user_id = $_SESSION['user_id'];

// Prepare the query to select only the paid invoices for the logged-in user
$query = "SELECT * FROM invoices_list WHERE user_id = ? AND status = 'paid'";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("i", $user_id); // Bind the user ID to ensure the query is specific to the logged-in user
    $stmt->execute();
    $result = $stmt->get_result();
    $invoices = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
} else {
    echo "Query preparation failed: " . htmlspecialchars($conn->error);
    exit();
}

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
  <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f2f2f2;
    }
  </style>
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
        <div class="container">

    <h1>Paid Invoices</h1>
    <?php if (!empty($invoices)): ?>
        <table>
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Amount</th>
                    <th>Time Raised</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?= htmlspecialchars($invoice['invoice_id']); ?></td>
                            <td>KE <?= number_format($invoice['amount'], 2); ?></td>
                            <td><?= htmlspecialchars($invoice['time_raised']); ?></td>
                            <td>
                                <button onclick="downloadPDF('<?= $invoice['invoice_id']; ?>', '<?= number_format($invoice['amount'], 2); ?>', '<?= $invoice['time_raised']; ?>')" class="btn btn-primary">Receipt</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
        </table>
    <?php else: ?>
        <p>No paid invoices available.</p>
    <?php endif; ?>

</div>

        </div>
        <?php include '../includes/foot.php';?>
      </div>
    </div>
  </div>
  <script>
    function downloadPDF(invoiceId, amount, timeRaised) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.text('Invoice Receipt', 10, 10);
        doc.text(`Invoice ID: ${invoiceId}`, 10, 20);
        doc.text(`Amount: KE ${amount}`, 10, 30);
        doc.text(`Time Raised: ${timeRaised}`, 10, 40);

        doc.save(`invoice-${invoiceId}-receipt.pdf`);
    }
  </script>
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
  <script src="assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="assets/js/dashboard.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
</body>
</html>
