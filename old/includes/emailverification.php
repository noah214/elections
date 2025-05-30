<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

function send_verification($fullname, $email, $otp) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'leonardpanergo@gmail.com'; // Your Gmail address
        $mail->Password = 'smkm ntiu nsqs fmou'; // Your Gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('leonardpanergo@gmail.com', 'BOTOmasino Elections');
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true);
        $mail->Subject = "Password Reset Verification";
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #004aad; margin-bottom: 20px;">Hello, ' . $fullname . '</h2>
                <p>You have requested to reset your password for your BOTOmasino Elections account.</p>
                <p>Please use the following verification code to reset your password:</p>
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; font-size: 24px; color: #004aad; font-weight: bold; margin: 20px 0;">
                    ' . $otp . '
                </div>
                <p>If you did not request this password reset, please ignore this email.</p>
                <p style="margin-top: 20px; font-size: 14px; color: #6c757d;">— BOTOmasino Elections Team</p>
            </div>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

function send_emailverification($fullname, $email, $otp) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'leonardpanergo@gmail.com'; // Your Gmail address
        $mail->Password = 'smkm ntiu nsqs fmou'; // Your Gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('leonardpanergo@gmail.com', 'BOTOmasino Elections');
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true);
        $mail->Subject = "Email Verification";
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #004aad; margin-bottom: 20px;">Hello, ' . $fullname . '</h2>
                <p>You have requested to verify your email for your BOTOmasino Elections account.</p>
                <p>Please use the following verification code to verify your email:</p>
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; font-size: 24px; color: #004aad; font-weight: bold; margin: 20px 0;">
                    ' . $otp . '
                </div>
                <p>If you did not request this email verification, please ignore this email.</p>
                <p style="margin-top: 20px; font-size: 14px; color: #6c757d;">— BOTOmasino Elections Team</p>
            </div>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
?> 