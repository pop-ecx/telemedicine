<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../includes/config.php';
$conn = getConnection();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileName = basename($_FILES['document']['name']);
    $uploadFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadFilePath)) {
        $insertSql = "INSERT INTO uploads (user_id, file_path) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        if ($stmt) {
            $stmt->bind_param("is", $user_id, $uploadFilePath);
            $stmt->execute();
            $stmt->close();
            echo "<p>File uploaded successfully.</p>";
        } else {
            echo "<p>Failed to prepare database statement.</p>";
        }
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

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
        <form action="payment_success.php" method="POST" class="dropzone" id="file-upload" enctype="multipart/form-data">
            <div class="fallback">
                <input name="document" type="file" multiple />
            </div>
            <!-- Button to manually submit the form -->
            <button type="button" id="submit-all" class="btn btn-primary">Upload Files</button>
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
  <script>
Dropzone.options.fileUpload = { 
    autoProcessQueue: false, // Disable automatic processing
    paramName: "document", // The name used to transfer the file
    maxFilesize: 2, // MB
    dictDefaultMessage: "Drop files here to upload, or click to select files",
    init: function() {
        var myDropzone = this;

        // Update the selector to match your button's ID
        document.getElementById("submit-all").addEventListener("click", function() {
            myDropzone.processQueue(); // Tell Dropzone to process all queued files
        });

        this.on("success", function(file, response) {
            console.log("File uploaded successfully: ", response);
        });

        this.on("error", function(file, response) {
            console.log("Error uploading file: ", response);
        });

        this.on("queuecomplete", function() {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                alert('All files have been uploaded successfully.');
            }
        });
    }
};
</script>

</body>

</html>
