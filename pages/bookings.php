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

$serviceId = $_GET['service_id'] ?? null;

// Query to fetch invoice data
$query = "SELECT invoice_id, service_id, amount, status, time_raised FROM invoices_list WHERE user_id = ?";
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
                      <h5 class="card-title mb-10 fw-semibold">Book an Appointment</h5>
                      <form action="submit_booking.php" method="post">
                        <div class="mb-3">
                          <label for="serviceSelect" class="form-label">Service</label>
                          <select class="form-select" id="serviceSelect" name="service_id" required>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>" <?= ($service['id'] == $serviceId) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        </div>
                        <div class="mb-3">
                  <label for="doctorInput" class="form-label">Doctor</label>
                  <input type="text" class="form-control" id="doctorInput" name="doctor_name" required placeholder="Doctor's name - you can say Any ">
                </div>
                <div class="mb-3">
                  <label for="timeInput" class="form-label">Time</label>
                  <input type="text" class="form-control" id="timeInput" name="time_slot" required placeholder="Enter preferred time">
                </div>

                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                      </form>
                    </div>
                  </div>
                    </div>
                </div>

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
