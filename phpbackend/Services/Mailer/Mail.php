<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class Mail{

 public function sendAutomate($email,$name){
    $mail = new PHPMailer(true);


    // === SERVER SETTINGS ===
    $mail->isSMTP();                        
    $mail->Host       = 'smtp.gmail.com';     
    $mail->SMTPAuth   = true;                 
    $mail->Username   = 'domdomkenneth23@gmail.com'; 
    $mail->Password   = 'cqiodnxiytnrsisl';       
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS encryption
    $mail->Port       = 587;                  // Port para sa TLS


    $mail->setFrom('domdomkenneth23@gmail.com', 'Mabini Cafe');
    $mail->addAddress($email,$name);

    $mail->isHTML(true);
  
     $mail->Subject = 'Thank you for contacting us!';
    $body = "
        <p>Hi {$name},</p>
        <p>Thank you for reaching out to us! Someone from our team will respond to your inquiry within 24 hours. 
        Please refrain from sending additional emails on your current thread, as this may push your message back in the queue. 
        We apologize for any inconvenience and appreciate your patience!</p>
        <p>Thank you,<br>
        Team Mabini Cafe</p>
    ";

    $mail->Body = $body;
    // === SEND EMAIL ===
      return $mail->send();
   

}

}

