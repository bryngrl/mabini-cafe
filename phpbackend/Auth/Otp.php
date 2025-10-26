<?php
require __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Otp {
    protected $secret_key = "supersecretkey";

    public function generate_otp($email){
        $issuedAt = time();
        $expire = $issuedAt + (12 * 60); 
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        // gawin ko lang string otp domskie
        $otp = (string)$otp;
        $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
        $payload = [
            'sub' => $email,
            'otp_hash' => $otp_hash,
            'iat' => $issuedAt,
            'exp' => $expire
        ];
        return ["token" => JWT::encode($payload, $this->secret_key, 'HS256'), "otp" => $otp];
    }

    public function verify_otp($token, $otp_input){
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            $payload = (array)$decoded;

            // Check expiry
            if ($payload['exp'] < time()) {
                return ["ok" => false, "error" => "OTP expired"];
            }

            // Trim ko lang otp domkskrt 
            $otp_input = trim((string)$otp_input);

            // Verify OTP
            if (!password_verify($otp_input, $payload['otp_hash'])) {
                return ["ok" => false, "error" => "Invalid OTP"];
            }

            // Success
            return ["ok" => true, "message" => "OTP verified", "email" => $payload['sub']];

        } catch (\Exception $e) {
            return ["ok" => false, "error" => "Invalid token"];
        }
    }
}
