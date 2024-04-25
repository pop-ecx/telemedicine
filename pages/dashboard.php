<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session at the beginning of the script

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect them to the login page
    header('Location: ../index.php');
    exit();
}

// Database connection
require_once '../includes/config.php';
$conn = getConnection(); // This is where you call the function to get the database connection

$userId = $_SESSION['user_id'];

// Prepare and execute the query
$stmt = $conn->prepare("SELECT name, username, lastlogin FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $username, $lastlogin);
$stmt->fetch();
$stmt->close();

// Prepare and execute the query to get booking details
$query = "SELECT user_id, doctor, service, platform, link, username, passcode FROM booking_details WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to hold all booking records
$bookings = [];

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}


// SQL to fetch services data
$services_query = "SELECT * FROM services_list";
$services_result = $conn->query($services_query);

$services = [];
if ($services_result->num_rows > 0) {
    while ($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
} else {
    echo "0 results in services";
}


// Query to fetch invoice data along with service name
$query = "SELECT i.invoice_id, i.service_id, s.name as service_name, i.amount, i.status, i.time_raised 
FROM invoices_list i
JOIN services_list s ON i.service_id = s.id
WHERE i.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$invoices = [];
while ($row = $result->fetch_assoc()) {
    $invoices[] = $row;
}

// SQL to fetch schedule data
$schedule_query = "SELECT sch.week_day, sch.time_slot, sch.is_booked, d.name AS doctor_name FROM schedules sch JOIN doctors d ON sch.doctor_id = d.doctor_id;";
$schedule_result = $conn->query($schedule_query);

$schedule_data = [];
if ($schedule_result->num_rows > 0) {
    while ($row = $schedule_result->fetch_assoc()) {
        $schedule_data[] = $row;
    }
} else {
    echo "No schedule data found";
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
    .link-wrap {
        max-width: 200px; /* Adjust based on your layout's needs */
        overflow-wrap: break-word; /* Wraps the text to the next line */
        word-wrap: break-word; /* Older browsers support */
        word-break: break-all; /* Ensures break at any character */
        white-space: normal; /* Overrides the default nowrap of table cells */
    }

     /* Reduce the spacing between table rows and adjust padding within cells */
    .compact-table tbody tr th,
    .compact-table tbody tr td {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    /* Minimize margins and padding within the card for a tighter layout */
    .compact-card-body {
        padding: 8px; /* Reduced padding */
    }

    /* Ensure the link wraps properly and doesn't overflow */
    .link-wrap {
        max-width: 150px; /* Adjust based on your layout's needs */
        overflow-wrap: break-word;
        word-break: break-all;
        white-space: normal;
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
<!-- Doctor Schedules Section -->
<div class="row">
    <div class="col-lg-8 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body">
                <!-- Card header with title and dropdown menu for settings -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title fw-semibold">Doctor Schedules</h5>
                    <div class="dropdown">
                        <button id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-sm shadow-none" aria-label="More Options">
                            <i class="ti ti-dots-vertical fs-7"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Schedule Display Table -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>Day</th>
                            <th>Time Slot</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedule_data as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['doctor_name']); ?></td>
                            <td><?= htmlspecialchars($entry['week_day']); ?></td>
                            <td><?= htmlspecialchars($entry['time_slot']); ?></td>
                            <td>
                                <span class="badge <?= $entry['is_booked'] ? 'bg-danger' : 'bg-success' ?>">
                                    <?= $entry['is_booked'] ? 'Booked' : 'Available' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>






         <div class="col-lg-4">
    <div class="row">
        <div class="col-lg-12 col-sm-6">

            <div class="card w-100">
                <?php foreach ($bookings as $booking): ?>
                    <div class="card-body compact-card-body">
                      <h5 class="card-title fw-semibold">Meeting Details</h5>
                        <h5 class="card-title"><?= htmlspecialchars($booking['doctor']); ?> - <?= htmlspecialchars($booking['service']); ?></h5>
                        <table class="table table-striped compact-table">
                            <tbody>
                                <tr>
                                    <th scope="row">Doctor</th>
                                    <td><?= htmlspecialchars($booking['doctor']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Service</th>
                                    <td><?= htmlspecialchars($booking['service']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Platform</th>
                                    <td><?= htmlspecialchars($booking['platform']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Meeting Link</th>
                                    <td>
                                        <div class="link-wrap">
                                            <a href="<?= htmlspecialchars($booking['link']); ?>" target="_blank"><?= htmlspecialchars($booking['link']); ?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Credentials</th>
                                    <td>Username: <?= htmlspecialchars($booking['username']); ?><br>Passcode: <?= htmlspecialchars($booking['passcode']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
        
<div class="table-responsive" data-simplebar>
    <table class="table table-borderless align-middle text-nowrap">
        <thead>
            <tr>
                <!-- Spanning the title across all columns with increased font size -->
                <th colspan="3" class="text-center py-3" style="font-size: 24px;">
                    Telemedicine Services <br>
                    <small style="font-size: 14px;">Click on the service name to book</small>
                </th>
            </tr>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Price</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
            <tr>
                <td>
                    <!-- Link to book the service, assuming booking.php and passing service ID -->
                    <a href="bookings.php?service_id=<?= urlencode($service['id']); ?>" title="Book <?= htmlspecialchars($service['name']); ?>" id="serviceLink">
                        <?= htmlspecialchars($service['name']); ?> 
                    </a>
                </td>
                <td>KE <?= number_format($service['price'], 2); ?></td>
                <td>
                    <span class="badge <?= $service['status'] == 1 ? 'bg-light-success text-success' : 'bg-light-danger text-danger'; ?>">
                        <?= $service['status'] == 1 ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



       <div class="container">
    
<table class="table">
    <thead>
        <tr>
            <th colspan="8" class="text-center py-3" style="font-size: 24px;">My Invoices</th>
        </tr>
        <tr>
            <th>Invoice ID</th>
            <th>Service Name</th> <!-- Changed from Service ID -->
            <th>Amount</th>
            <th>Status</th>
            <th>Time Raised</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($invoices as $invoice): ?>
            <tr>
                <td><?= isset($invoice['invoice_id']) ? htmlspecialchars($invoice['invoice_id']) : ''; ?></td>
                <td><?= isset($invoice['service_name']) ? htmlspecialchars($invoice['service_name']) : ''; ?></td>
                <td>KE <?= isset($invoice['amount']) ? number_format($invoice['amount'], 2) : ''; ?></td>
                <td><?= isset($invoice['status']) ? htmlspecialchars($invoice['status']) : ''; ?></td>
                <td><?= isset($invoice['time_raised']) ? htmlspecialchars($invoice['time_raised']) : ''; ?></td>
                <td>
                    <?php if (strtolower($invoice['status']) != 'paid'): ?>
                        <a href="pay_invoice.php?invoice_id=<?= urlencode($invoice['invoice_id']); ?>" class="btn btn-success">Pay</a>
                    <?php else: ?>
                        <!-- Replace the view link with a PDF download button -->
                        <button class="btn btn-primary download-pdf"
                                data-invoice-id="<?= isset($invoice['invoice_id']) ? htmlspecialchars($invoice['invoice_id']) : ''; ?>"
                                data-user-name="<?= isset($invoice['user_name']) ? htmlspecialchars($invoice['user_name']) : ''; ?>"
                                data-service-name="<?= isset($invoice['service_name']) ? htmlspecialchars($invoice['service_name']) : ''; ?>"
                                data-amount="<?= isset($invoice['amount']) ? htmlspecialchars($invoice['amount']) : ''; ?>"
                                data-status="<?= isset($invoice['status']) ? htmlspecialchars($invoice['status']) : ''; ?>"
                                data-time-raised="<?= isset($invoice['time_raised']) ? htmlspecialchars($invoice['time_raised']) : ''; ?>">
                            View
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    document.querySelectorAll('.download-pdf').forEach(button => {
        button.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            const userName = this.getAttribute('data-user-name');
            const serviceName = this.getAttribute('data-service-name');
            const amount = this.getAttribute('data-amount');
            const status = this.getAttribute('data-status');
            const timeRaised = this.getAttribute('data-time-raised');

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.text('Invoice Receipt', 10, 10);
            doc.text(`Invoice ID: ${invoiceId}`, 10, 20);
            doc.text(`User Name: ${userName}`, 10, 30);
            doc.text(`Service Name: ${serviceName}`, 10, 40);
            doc.text(`Amount: KE ${amount}`, 10, 50);
            doc.text(`Status: ${status}`, 10, 60);
            doc.text(`Time Raised: ${timeRaised}`, 10, 70);

            doc.save(`invoice_${invoiceId}_receipt.pdf`);
        });
    });
</script>



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