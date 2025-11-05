<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class Mail {

    // ✅ AUTO RESPONSE TO CUSTOMER
    public function sendAutomate($email, $name) {
        $mail = new PHPMailer(true);

        // === SERVER SETTINGS ===
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'contact.mabinicafe@gmail.com';
        $mail->Password   = 'gckqlnqbzzwvbxgd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('contact.mabinicafe@gmail.com', 'Mabini Cafe');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Thank you for contacting us!';

        // === HTML DESIGN ===
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #000; color: #fff; padding: 30px; border-radius: 10px;'>
            <div style='text-align:center;'>
                <h1 style='color:#FFD700;'>Mabini Cafe</h1>
                <hr style='border:1px solid #FFD700; width:60%;'>
            </div>
            <p style='font-size:16px;'>Hi <b>{$name}</b>,</p>
            <p style='font-size:15px; line-height:1.6;'>
                Thank you for reaching out to us! Someone from our team will respond to your inquiry within 24 hours.
                Please refrain from sending additional emails on this thread to keep your message in queue.
            </p>
            <p style='margin-top:20px;'>With appreciation,<br>
            <b style='color:#FFD700;'>Team Mabini Cafe</b></p>
        </div>";

        return $mail->send();
    }

    // ✅ OTP EMAIL
    public function sendOtp($email, $otp) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'contact.mabinicafe@gmail.com';
            $mail->Password   = 'gckqlnqbzzwvbxgd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

             $mail->setFrom('contact.mabinicafe@gmail.com', 'Mabini Cafe');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';

            // === HTML DESIGN ===
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #111; color: #fff; padding: 30px; border-radius: 10px; text-align:center;'>
                <h2 style='color:#FFD700;'>Your OTP Code</h2>
                <p style='font-size:16px;'>Use this code to verify your account:</p>
                <div style='background-color:#FFD700; color:#000; font-size:24px; font-weight:bold; display:inline-block; padding:10px 20px; border-radius:8px; margin:10px 0;'>
                    $otp
                </div>
                <p style='font-size:14px; color:#ccc;'>This code will expire in <b>5 minutes</b>.</p>
                <hr style='border:1px solid #333; margin:20px 0;'>
                <p style='font-size:13px;'>Mabini Cafe Security Team</p>
            </div>";

            return $mail->send();
        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            echo json_encode(["mailer_debug_error" => $mail->ErrorInfo]);
            return ["mailer_debug_error" => $mail->ErrorInfo];
        }
    }

    // ✅ CONTACT FORM MESSAGE TO ADMIN
    public function sendContactEmail($userName, $userEmail, $topic,$reason,$orderid, $message, $attachmentPath = null) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'contact.mabinicafe@gmail.com';
             $mail->Password   = 'gckqlnqbzzwvbxgd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($mail->Username, 'Mabini Cafe');
            $mail->addAddress($mail->Username, 'Business Inbox');
            $mail->addReplyTo($userEmail, $userName);

            if ($attachmentPath) {
                $mail->addAttachment($attachmentPath);
            }

            $mail->isHTML(true);
            $mail->Subject = "New Contact Form Message: $topic";

            // === HTML DESIGN ===
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color:#fff; color:#000; padding:25px; border:3px solid #FFD700; border-radius:10px;'>
                <h2 style='color:#000; text-align:center; background-color:#FFD700; padding:10px; border-radius:5px;'>📩 New Contact Message</h2>
                <p><b style='color:#000;'>Name:</b> $userName</p>
                <p><b style='color:#000;'>Email:</b> $userEmail</p>
                <p><b style='color:#000;'>Topic:</b> $topic</p>
                <p><b style='color:#000;'>Reason:</b> $reason</p>
                <p><b style='color:#000;'>Order id:</b> $orderid</p>
                <p><b style='color:#000;'>Message:</b><br><i>$message</i></p>
                <hr style='border:1px solid #FFD700; margin-top:20px;'>
                <p style='font-size:13px; text-align:center; color:#555;'>Sent via Mabini Cafe Contact Form</p>
            </div>";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Mailer error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
