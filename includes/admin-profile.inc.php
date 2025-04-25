<?php

$adminProfile = array();

if (isset($_SESSION['NPK'])) {
  // get all datas
  $npk = $_SESSION['NPK'];

  // require classes and init profileController
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/profile/profile.class.php';
  require_once __DIR__ . '/../classes/profile/profile-contr.class.php';
  $profile = new ProfileController($npk);

  // get all profile datas
  $adminProfile = $profile->getProfileData();
}
