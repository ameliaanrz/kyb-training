<?php
session_start();

require_once __DIR__ . '/../classes/connection.class.php';
require_once __DIR__ . '/../classes/otp.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $npk = $_POST['npk'];

    if (empty($npk)) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("error" => "NPK is required."));
        exit();
    }

    try {
        $resendOtp = new OTP();
        $newOtp = $resendOtp->createOTP($npk); // Buat OTP baru atau update OTP yang sudah ada
        $_SESSION['otp'] = $newOtp; // Simpan OTP baru di session

        header("Content-type: application/json");
        echo json_encode(array("success" => "OTP has been resent."));
        exit();
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(array("error" => $e->getMessage()));
        exit();
    }
}