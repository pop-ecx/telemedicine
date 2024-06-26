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

require_once '../includes/config.php'; // Include your config file
$conn = getConnection(); // Get the database connection

// Collect POST data from form submission
$serviceId = $_POST['service_id'];
$doctorName = htmlspecialchars($_POST['doctor_name'], ENT_QUOTES, 'UTF-8');
$timeSlot = htmlspecialchars($_POST['time_slot'], ENT_QUOTES, 'UTF-8');
$userId = $_SESSION['user_id']; // Assume user ID is stored in session
$patientName = $_SESSION['username']; // Assume patient name is stored in session


// Validate and sanitize service ID
$serviceId = filter_var($serviceId, FILTER_SANITIZE_NUMBER_INT);

// Fetch service details including price
$serviceSql = "SELECT price FROM services_list WHERE id = ?";
$serviceStmt = $conn->prepare($serviceSql);
$serviceStmt->bind_param("i", $serviceId);
$serviceStmt->execute();
$serviceStmt->bind_result($price);
$serviceStmt->fetch();
$serviceStmt->close();

if (!$price) {
    die("Service not found or invalid service ID."); // Handle cases where service is not found
}

// Prepare and execute booking insertion
$insertBookingSql = "INSERT INTO bookings (service, doctor, time, patient_name) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertBookingSql);
$stmt->bind_param("ssss", $serviceId, $doctorName, $timeSlot, $patientName);
$stmt->execute();
$bookingId = $stmt->insert_id;

if (!$bookingId) {
    die("Failed to book the appointment."); // Handle failure to book the appointment
}

// Prepare data for invoice creation
$status = 'Pending';
$invoiceCode = uniqid();

// Insert into invoices list
$insertInvoiceSql = "INSERT INTO invoices_list (user_id, service_id, time_raised, amount, status, invoice_code) VALUES (?, ?, NOW(), ?, ?, ?)";
$stmt = $conn->prepare($insertInvoiceSql);
$stmt->bind_param("iisss", $userId, $serviceId, $price, $status, $invoiceCode);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    die("Failed to create invoice."); // Handle failure to create invoice
}

$stmt->close();
$conn->close();

// Email confirmation to patient
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'telemed@kijabehospital.org'; // Replace with your SMTP username
    $mail->Password = 'Kijabe@2024###'; // Replace with your SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('telemed@kijabehospital.org', 'Kijabe Telemedicine');
    $mail->addAddress($patientName);

    $mail->isHTML(true);
    $mail->Subject = 'Booking Confirmation';
    $mail->Body = "<p>Hello {$patientName},</p><p>Your appointment with {$doctorName} at {$timeSlot} has been successfully booked.</p>";

    $mail->send();
} catch (Exception $e) {
    error_log('Mailer Error: ' . $mail->ErrorInfo);
}

header('Location: booking_confirmation.php'); // Redirect to a confirmation page
exit();
?>
