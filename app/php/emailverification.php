<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

function send_verification($email, $code) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'noreply.botomasino@gmail.com';                 // SMTP username
        $mail->Password = 'qrbk cklx anjx ffyh';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, ssl also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        
        //Recipients
        $mail->setFrom('noreply.botomasino@gmail.com','BOTOmasino Elections'); 
        $mail->addAddress($email);     // Add a recipient
        //Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = 'Password Reset Verification - BOTOmasino Elections';
        
        // Email template
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #ffffff;
                    background-color: #000000;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #1a1a1a;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                }
                .header {
                    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
                    padding: 30px 20px;
                    text-align: center;
                    border-bottom: 2px solid #ffd700;
                }
                .header h1 {
                    color: #ffd700;
                    font-size: 28px;
                    margin: 0;
                    font-weight: 600;
                }
                .content {
                    padding: 40px 30px;
                }
                .title {
                    color: #ffd700;
                    font-size: 24px;
                    margin-bottom: 25px;
                    text-align: center;
                }
                .message {
                    color: #ffffff;
                    font-size: 16px;
                    margin-bottom: 30px;
                    text-align: center;
                }
                .code-container {
                    text-align: center;
                    margin: 30px 0;
                }
                .code {
                    background: #000000;
                    color: #ffd700;
                    font-size: 36px;
                    font-weight: bold;
                    padding: 20px 40px;
                    border-radius: 8px;
                    display: inline-block;
                    letter-spacing: 5px;
                    border: 2px solid #ffd700;
                }
                .button {
                    display: inline-block;
                    background: #ffd700;
                    color: #000000;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: 600;
                    margin: 20px 0;
                    transition: all 0.3s ease;
                }
                .button:hover {
                    background: #ffed4a;
                    transform: translateY(-2px);
                }
                .footer {
                    background: #000000;
                    padding: 20px;
                    text-align: center;
                    font-size: 14px;
                    color: #ffd700;
                    border-top: 2px solid #ffd700;
                }
                @media only screen and (max-width: 600px) {
                    .container {
                        margin: 10px;
                    }
                    .content {
                        padding: 20px;
                    }
                    .code {
                        font-size: 28px;
                        padding: 15px 30px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>BOTOmasino Elections</h1>
                </div>
                <div class="content">
                    <h2 class="title">Password Reset Verification</h2>
                    <p class="message">We received a request to reset your password. To proceed, please use the verification code below:</p>
                    
                    <div class="code-container">
                        <div class="code">' . $code . '</div>
                    </div>

                    <div style="text-align: center;">
                        <a href="http://localhost/elections/app/public/forgot_password.php" class="button">Reset Password</a>
                    </div>

                    <p class="message">If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
                </div>
                <div class="footer">
                    <p>This is an automated message, please do not reply to this email.</p>
                    <p>© 2024 UST Supreme Student Council. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function send_emailverification($email, $code) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'noreply.botomasino@gmail.com';                 // SMTP username
        $mail->Password = 'qrbk cklx anjx ffyh';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, ssl also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        
        //Recipients
        $mail->setFrom('noreply.botomasino@gmail.com', 'BOTOmasino Elections'); 
        $mail->addAddress($email);     // Add a recipient
        //Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = 'Email Verification - BOTOmasino Elections';
        
        // Email template
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #ffffff;
                    background-color: #000000;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: #1a1a1a;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                }
                .header {
                    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
                    padding: 30px 20px;
                    text-align: center;
                    border-bottom: 2px solid #ffd700;
                }
                .header h1 {
                    color: #ffd700;
                    font-size: 28px;
                    margin: 0;
                    font-weight: 600;
                }
                .content {
                    padding: 40px 30px;
                }
                .title {
                    color: #ffd700;
                    font-size: 24px;
                    margin-bottom: 25px;
                    text-align: center;
                }
                .message {
                    color: #ffffff;
                    font-size: 16px;
                    margin-bottom: 30px;
                    text-align: center;
                }
                .code-container {
                    text-align: center;
                    margin: 30px 0;
                }
                .code {
                    background: #000000;
                    color: #ffd700;
                    font-size: 36px;
                    font-weight: bold;
                    padding: 20px 40px;
                    border-radius: 8px;
                    display: inline-block;
                    letter-spacing: 5px;
                    border: 2px solid #ffd700;
                }
                .button {
                    display: inline-block;
                    background: #ffd700;
                    color: #000000;
                    padding: 15px 30px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: 600;
                    margin: 20px 0;
                    transition: all 0.3s ease;
                }
                .button:hover {
                    background: #ffed4a;
                    transform: translateY(-2px);
                }
                .footer {
                    background: #000000;
                    padding: 20px;
                    text-align: center;
                    font-size: 14px;
                    color: #ffd700;
                    border-top: 2px solid #ffd700;
                }
                @media only screen and (max-width: 600px) {
                    .container {
                        margin: 10px;
                    }
                    .content {
                        padding: 20px;
                    }
                    .code {
                        font-size: 28px;
                        padding: 15px 30px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>BOTOmasino Elections</h1>
                </div>
                <div class="content">
                    <h2 class="title">Email Verification</h2>
                    <p class="message">Welcome to BOTOmasino Elections! To complete your registration, please verify your email address using the code below:</p>
                    
                    <div class="code-container">
                        <div class="code">' . $code . '</div>
                    </div>

                    <p class="message">Please enter this verification code in the form to complete your registration.</p>

                    <p class="message">If you did not create an account with BOTOmasino Elections, please ignore this email.</p>
                </div>
                <div class="footer">
                    <p>This is an automated message, please do not reply to this email.</p>
                    <p>© 2024 UST Supreme Student Council. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
        error_log("Email sent successfully to: " . $email);
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email to " . $email . ". Error: " . $e->getMessage());
        return false;
    }
}
?> 