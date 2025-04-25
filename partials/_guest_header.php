<?php
session_start();
if (isset($_SESSION['NPK'])) {
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- css -->
  <link rel="stylesheet" href="public/css/styles.css">
  <!-- paginationjs css -->
  <link rel="stylesheet" href="node_modules/paginationjs/dist/pagination.css">
  <!-- bootstrap cdn -->
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="node_modules/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
<link rel="stylesheet" href="node_modules/lightslider/lightslider.min.css" />
  <!-- jquery -->
  <script src="node_modules/jquery/dist/jquery.min.js"></script>
  <!-- font awesome cdn -->
  <link rel="stylesheet" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css" referrerpolicy="no-referrer" />
  <!-- favicon -->
  <link rel="shortcut icon" href="public/imgs/kyb.png" type="image/svg">
  <title>KYB Training Center | Dashboard</title>
   <style>
        .carousel-item img {
            width: 100%; /* Gambar akan mengisi lebar parent (carousel-item) */
            height: auto; /* Menjaga proporsi gambar */
            max-width: 1780px; /* Maksimum lebar gambar */
            max-height: 500px; /* Maksimum tinggi gambar */
        }
        .otp-input {
    width: 40px;
    height: 50px;
    font-size: 1.5rem;
    margin: 0 5px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

    </style>
</head>

<body>
  <header>
    <?php include_once __DIR__ . '/../components/navbar.php'; ?>
  </header>