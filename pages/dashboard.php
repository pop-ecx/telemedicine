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
                <!-- User Profile Card -->
<div class="card overflow-hidden">
    <div class="card-body p-4">
        <h5 class="card-title mb-10 fw-semibold">My Profile</h5>
        <div class="row align-items-center">
            <!-- User details column -->
            <div class="col-md-9">
                <h4 class="fw-semibold mb-3"><?= htmlspecialchars($name); ?></h4>
                <div>
                    <strong>Username:</strong> <?= htmlspecialchars($username); ?><br>
                    <strong>Last Login:</strong> <?= htmlspecialchars($lastlogin); ?>
                </div>
                <div class="mt-3">
                    <a href="update-profile.php" class="btn btn-primary">Update</a>
                </div>
            </div>
            <!-- Avatar column -->
            <div class="col-md-3 d-flex justify-content-center">
                <img src="assets/images/profile/user1.jpg" alt="User Avatar" style="width: 70px; height: 70px; border-radius: 50%;">
            </div>
        </div>
    </div>
</div>

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
            <td><?= htmlspecialchars($invoice['invoice_id']); ?></td>
            <td><?= htmlspecialchars($invoice['service_name']); ?></td> <!-- Display the service name -->
            <td>KE <?= number_format($invoice['amount'], 2); ?></td>
            <td><?= htmlspecialchars($invoice['status']); ?></td>
            <td><?= htmlspecialchars($invoice['time_raised']); ?></td>
            <td>
                <?php if (strtolower($invoice['status']) != 'paid'): ?>
                    <a href="pay_invoice.php?invoice_id=<?= urlencode($invoice['invoice_id']); ?>" class="btn btn-success">Pay</a>
                <?php else: ?>
                    <a href="view_invoice.php?invoice_id=<?= urlencode($invoice['invoice_id']); ?>" class="btn btn-primary">View</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>



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