<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teksavyagency";
$port = 3307;

// Create connection 
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to send email via PHPMailer
function sendEmailNotification($toEmail, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'teksavy12@gmail.com';
        $mail->Password = '231124@tek';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Details
        $mail->setFrom('teksavy12@gmail.com', 'Teksavy Agency');
        $mail->addAddress($toEmail); // Recipient
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;

        $mail->send();
        echo 'Notification email has been sent';
    } catch (Exception $e) {
        echo "Notification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $service = $conn->real_escape_string($_POST['service']);
    $date = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO teksavy (name, email, phone, service, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $service, $date);

    if ($stmt->execute()) {
        echo "New record created successfully";
        $adminEmail = "teksavy12@gmail.com"; // Replace with your email address
        $subject = "New Data Inserted";
        $message = "A new record has been inserted into the database.<br><br>
                    <b>Name:</b> $name<br>
                    <b>Email:</b> $email<br>
                    <b>Phone:</b> $phone<br>
                    <b>Service:</b> $service<br>
                    <b>Date:</b> $date";
        sendEmailNotification($adminEmail, $subject, $message);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
