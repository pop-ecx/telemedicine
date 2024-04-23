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

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    // Handling file upload
    $uploadDir = 'uploads/'; // Ensure this directory exists and is writable
    $fileName = basename($_FILES['document']['name']);
    $uploadFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadFilePath)) {
        // Insert file info into the database
        $insertSql = "INSERT INTO patient_uploads (user_id, file_path) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("is", $user_id, $uploadFilePath);
        $stmt->execute();
        $stmt->close();

        echo "<p>File uploaded successfully.</p>";
    } else {
        echo "<p>Sorry, there was an error uploading your file.</p>";
    }
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
        <div class="container">
        <h1>Consultation Booking Successful</h1>
        <p>Your payment has been processed successfully, and your consult is booked. Someone will contact you shortly.</p>

        <!-- Upload Form -->
        <h2>Upload Documents</h2>
        <form action="payment_success.php" method="post" enctype="multipart/form-data">
            <input type="file" name="document" required>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
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
