<?php
#this class is for otp
require_once 'Auth.php';

class Otp extends Auth{



    public function generate_otp($email){
          $issuedAt = time();
        $expire = $issuedAt + 500;
        // Generate 6-digit OTP
       $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
       $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
       $payload = [
         'sub' =>$email,
         'otp_hash' => $otp_hash,
          'iat' => $issuedAt,
          'exp' => $expire
       ];

            return JWT::encode($payload, $this->secret_key, 'HS256');

    }
}