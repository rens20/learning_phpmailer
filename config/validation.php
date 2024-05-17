<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Add this line to autoload PHPMailer

function ValidateLogin($email, $password) {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); 
    $sql = "SELECT * FROM users_admin WHERE email = '$email' && password = '$password'";
    $result = $conn->query($sql);
    $row = mysqli_fetch_assoc($result);

    return $row;
}

function Register($email, $password, $name) {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // security sql injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    $name = mysqli_real_escape_string($conn, $name);

    // user data into database
    $insert = "INSERT INTO users_admin (email, password, name, type) VALUES ('$email', '$password', '$name', 'user')";
    if (mysqli_query($conn, $insert)) {
        $report = 'Registered Complete!';

        // Send confirmation email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // Your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'wasieacuna@gmail.com';  // Your email address
            $mail->Password   = 'qipc vais smfq rwim';  
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('wasieacuna@gmail.com', 'Ylagan\'s Bakery Shop');
            $mail->addAddress($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Registration Successful';
            $mail->Body    = "Hi $name,<br><br>You have successfully registered.<br><br>Regards,<br>Ylagan's Bakery Shop";

            $mail->send();
            $report .= ' Email sent!';
        } catch (Exception $e) {
            $report .= " Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    
    } else {
        $report = 'Error: ' . $insert . '<br>' . mysqli_error($conn);
    }

    // Close connection
    mysqli_close($conn);
    return $report;
}
?>
