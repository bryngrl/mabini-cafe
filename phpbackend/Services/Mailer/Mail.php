<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class Mail{

function sendAutomate($email,$name){
    $mail = new PHPMailer(true);


    // === SERVER SETTINGS ===
    $mail->isSMTP();                        
    $mail->Host       = 'smtp.gmail.com';     
    $mail->SMTPAuth   = true;                 
    $mail->Username   = 'domdomkenneth23@gmail.com'; 
    $mail->Password   = 'cqiodnxiytnrsisl';       
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS encryption
    $mail->Port       = 587;                  // Port para sa TLS

    // === SENDER & RECIPIENT ===
    $mail->setFrom('domdomkenneth23@gmail.com', 'Mabini Cafe');
    $mail->addAddress($email,$name);

    $mail->isHTML(false);
    $mail->Subject = 'PHPMailer SMTP test';
    $mail->Body    = "PHPMailer is working with SMTP on localhost!";

    // === SEND EMAIL ===
       $mail->send();
      $mail->smtpClose();

}

}

