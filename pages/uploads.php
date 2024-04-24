<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once '../includes/config.php'; // Ensure this file contains the getConnection function
$conn = getConnection(); // Establish the database connection

// Get the user ID from session
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Uploads</title>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AIC Kijabe Hospital Telemedicine</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px; /* Increase font size for better readability */
    }
    th, td {
        border: 1px solid #ccc;
        padding: 10px 15px; /* Increase padding for better spacing */
        text-align: left;
    }
    th {
        background-color: #f4f4f4; /* Subtle header color */
        font-weight: bold;
    }
    a {
        color: #007bff; /* Bootstrap primary blue for consistency */
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    .container {
        padding: 20px;
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
    <h2>Uploaded Files</h2>
    <table>
        <thead>
            <tr>
                
                <th>File Name</th>
                <th>Uploaded At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT id, user_id, file_path, uploaded_at FROM uploads WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
               
                    echo "<td>" . basename($row["file_path"]) . "</td>";
                    echo "<td>" . $row["uploaded_at"] . "</td>";
                    echo "<td><a href='" . htmlspecialchars($row["file_path"]) . "' target='_blank'>View</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No files uploaded</td></tr>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </tbody>
    </table>
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
