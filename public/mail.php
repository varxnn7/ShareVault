<?php
// Since this is now in the public folder, we go up one level to find vendor
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendShareEmail($recipientEmail, $fileLink, $fileName)
{
    $mail = new PHPMailer(true);

    try {
        // For Development
        // $mail->SMTPDebug = 2; // Disabled to prevent breaking the UI response
        // $mail->Debugoutput = 'html';
        // For Production
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Port       = 587;
        $mail->Username   = $_ENV['SMTP_USERNAME'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? '';
        $mail->SMTPSecure = 'PHPMailer::ENCRYPTION_STARTTLS';

        //SENDER
        $senderEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'varunkukreja017@gmail.com';
        $mail->setFrom($senderEmail, 'ShareVault'); // Your Verified Sender Email

        // RECIPIENT
        $mail->addAddress($recipientEmail);
        $mail->isHTML(true);
        $mail->Subject = 'New File Shared: ' . $fileName;
        $mail->Body    = "
            <div style='font-family: sans-serif; max-width: 600px; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #6366f1;'>ShareVault</h2>
                <p>Hello,</p>
                <p>A file has been shared with you: <strong>$fileName</strong></p>
                <div style='margin: 30px 0;'>
                    <a href='$fileLink' style='background: #6366f1; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                        Download File
                    </a>
                </div>
                <p style='font-size: 0.8rem; color: #777;'>Link expires in 7 days.</p>
            </div>";
        $mail->AltBody = "A file ($fileName) has been shared with you. Download it here: $fileLink";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error silently so it doesn't crash the JSON upload response
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}