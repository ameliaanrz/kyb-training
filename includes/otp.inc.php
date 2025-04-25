<?php
session_start();

$otp = '';
$errors = array('otp' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Hapus data session lama
  unset($_SESSION['NPK']);
  unset($_SESSION['otp_verified']);

  $otp = post_data('otp');
  $npk = $_SESSION['pending_npk'] ?? '';

  if (!isset($_SESSION['otp']) || !$npk) {
    $errors['otp'] = "*Session expired. Please login again.";
  } else {
    $otp_key = $_SESSION['otp'];
    
    require_once __DIR__ . '/../classes/connection.class.php';
    require_once __DIR__ . '/../classes/otp.class.php';
    require_once __DIR__ . '/../classes/otp-contr.class.php';

    $otpController = new ReminderController($otp, $otp_key);
    $errors = array_merge($errors, $otpController->loginOTP());
  }

  if (!$errors['otp']) {
    // Set session akhir
    $_SESSION['NPK'] = $npk;
    $_SESSION['otp_verified'] = true; // ✅ Flag verifikasi OTP
    
    // Hapus data OTP
    unset($_SESSION['otp']);
    unset($_SESSION['pending_npk']);

    header("Content-type: application/json");
    echo json_encode(array("success" => "User authorized"));
    exit();
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
    exit();
  }
}

function post_data($field) {
  $_POST[$field] = $_POST[$field] ?? '';
  return stripslashes(htmlspecialchars($_POST[$field]));
}