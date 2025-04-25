<?php
session_start();

$npk = '';
$password = '';
$errors = array(
  'npk' => '',
  'password' => '',
  'captcha' => ''
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Hapus session OTP dan NPK sementara sebelum proses login baru
  unset($_SESSION['otp']);
  unset($_SESSION['pending_npk']);
  unset($_SESSION['otp_verified']);

  // Ambil data
  $npk = post_data('npk');
  $password = post_data('password');
  $captcha = post_data('captcha');
  $captcha_key = $_SESSION['captcha'] ?? '';

  // Instantiate class
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/login.class.php';
  require_once __DIR__ . '/../classes/login-contr.class.php';
  require_once __DIR__ . '/../classes/otp-contr.class.php';
  require_once __DIR__ . '/../classes/otp.class.php';

  $login = new LoginController($npk, $password, $captcha, $captcha_key);
  $errors = array_merge($errors, $login->loginUser());

  if (!$errors['npk'] && !$errors['password'] && !$errors['captcha']) {
    // Hapus session lama
    session_regenerate_id(true);
    
    // Generate OTP baru
    $createOTP = new OTP();
    $otp = $createOTP->createOTP($npk);
    
    // Simpan di session
    $_SESSION['otp'] = $otp;
    $_SESSION['pending_npk'] = $npk; // Hanya NPK sementara
    
    // Pastikan session utama dihapus
    unset($_SESSION['NPK']);
    unset($_SESSION['otp_verified']);

    header("Content-type: application/json");
    echo json_encode(array("success" => "User authorized", "otp" => $otp));
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