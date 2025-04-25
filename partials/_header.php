<?php @session_start();
$base_dir = 'kyb-training-center';
$uri = explode('/', $_SERVER['REQUEST_URI']); // split request uri by '/'
try {
  $dir = $uri[count($uri) - 2];
} catch (Exception $e) {
  $dir = $base_dir;
}
if ($dir != $base_dir):
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <base href="..">
    </base>
  </head>
<?php endif; ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- css -->
  <link rel="stylesheet" href="public/css/styles.css">
  <!-- paginationjs css -->
  <link rel="stylesheet" href="node_modules/paginationjs/dist/pagination.css">
  <!-- bootstrap cdn -->
  <script src="node_modules/jquery/dist/jquery.min.js"></script>
  <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" type="text/css" href="node_modules/plugins/OwlCarousel2-2.2.1/owl.carousel.css"> -->
  <link rel="stylesheet" href="node_modules/lightslider/dist/css/lightslider.min.css" />
  <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
  <script type="text/javascript" src="node_modules/tinymce/js/tinymce/tinymce.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  

  <!-- jquery -->
  <!-- font awesome cdn -->
  <link rel="stylesheet" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css" referrerpolicy="no-referrer" />
  <!-- favicon -->
  <link rel="shortcut icon" href="public/imgs/kyb.png" type="image/svg">
  <link href="node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />

  <title>KYB Training Center</title>
  <?php if (isset($_SESSION['RLS_ID']) && $_SESSION['RLS_ID'] === 'RLS03'): ?>
    <link href="public/css/style.css" rel="stylesheet" />
  <?php endif; ?>

  <script type="text/javascript" src="node_modules/fullcalendar/index.global.min.js"></script>

  <link href="node_modules/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
  <script type="text/javascript" src="node_modules/tabulator-tables/dist/js/tabulator.min.js"></script>



  <style>
    /* CSS untuk ikon arah pengurutan */
    th.sorting::after {
      content: "";
      display: inline-block;
      width: 0;
      height: 0;
      margin-left: 5px;
      vertical-align: middle;
    }

    th.sorting.asc::after {
      content: "\f078";
      /* Unicode untuk FontAwesome up arrow */
    }

    th.sorting.desc::after {
      content: "\f077";
      /* Unicode untuk FontAwesome down arrow */
    }

    .event-card-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: start;
      gap: 20px;
    }

    .events-running {
      background-color: #00BFFF;
      display: inline-block;
      padding: 2px;
      border-radius: 15px;
    }

    .events-upcoming {
      background-color: #FFD700;
      display: inline-block;
      padding: 2px;
      border-radius: 15px;
    }

    .events-already {
      background-color: #D3D3D3;
      display: inline-block;
      padding: 2px;
      border-radius: 15px;
    }

    .events-top {
      display: grid;
      place-items: center;
      /* Menengahkan baik secara horizontal maupun vertikal */
    }

    .event-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background-color: #fff;
      width: calc(33.333% - 20px);
      /* Adjust based on your desired width */
      margin-bottom: 20px;
      padding: 20px;
    }

    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .event-card .card-title {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 10px;
      color: #333;
    }

    .event-card .card-text {
      font-size: 1rem;
      color: #666;
      margin-bottom: 20px;
    }

    .event-card .btn {
      background-color: #ff4d4d;
      border: none;
      color: white;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 1rem;
      margin-top: 10px;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }

    .event-card .btn:hover {
      background-color: #ff1a1a;
    }

    .status-running {
      color: yellow;
    }

    .status-done {
      color: green;
    }

    .status-upcoming {
      color: orange;
    }

    .btn-event {
      display: inline-block;
      padding: 8px 16px;
      text-align: center;
      text-decoration: none;
      border-radius: 4px;
      color: #fff;
      background-color: #dc3545;
      border: 1px solid #dc3545;
    }

    .btn-event-non {
      display: inline-block;
      padding: 8px 16px;
      text-align: center;
      text-decoration: none;
      border-radius: 4px;
      color: #fff;
      background-color: #2e292a;
      border: 1px solid #2e292a;
    }

    .btn-event-non:hover {
      background-color: #fff;
      border-color: #fff;
      color: #2e292a;
    }

    .btn-event:hover {
      background-color: #fff;
      border-color: #fff;
      color: #dc3545;
    }

    #calendar {
      max-width: 1400px;
      margin: 0 auto;
      margin: 40px 10px;
      padding: 0;
      font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
      font-size: 16px;
    }

    /* Menghilangkan garis bawah dari tautan */
    .fc a {
      text-decoration: none;
    }

    /* Mengubah warna teks */
    .fc {
      color: #FFFFFF;
      /* Ganti dengan warna yang Anda inginkan, misalnya: #333 untuk warna hitam */
    }

    .profile-card {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .profile-content {
      flex: 1;
      padding: 10px;
    }

    .profile-img {
      max-width: 18%;
      flex-shrink: 0;
      /* agar gambar tidak mengecil lebih dari yang diinginkan */
    }

    .profile-title {
      font-size: 1.5rem;
      margin-bottom: 10px;
    }

    .profile-desc {
      font-size: 1rem;
      color: #555;
    }

    .carousel-inner {
      position: relative;
      width: 100%;
      overflow: hidden;
    }

    .carousel-item {
      position: relative;
      text-align: center;
      min-height: 400px;
      /* Sesuaikan dengan kebutuhan */
    }

    .carousel-item img {
      position: absolute;
      top: 50%;
      left: 50%;
      height: 100%;
      width: auto;
      transform: translate(-50%, -50%);
      z-index: 1;
    }

    .carousel-item .blur-background {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: cover;
      filter: blur(10px);
      transform: scale(1.1);
      z-index: 0;
    }

    .upload-date {
      background-color: #D3D3D3;
  display: block; /* Agar mengikuti lebar parent */
  width: 100%; /* Lebar penuh */
  padding: 10px;
  border: 2px solid #D3D3D3;
  border-radius: 5px;
  color: black;
  font-weight: bold;
  text-align: center; /* Pusatkan teks */
  transition: all 0.3s ease-in-out;
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
  <main class="container main-container py-5">