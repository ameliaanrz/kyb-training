<?php

// variables
$npk = '';
$adminName = '';
$approval = '';
$completion = '';
$org_id = '';
$t_id = '';
$cds_id = '';
$search = '';

// consts
define('GET_PARTICIPANTS_LIST', 'pdf001');
define('GET_TRAININGS_LIST', 'pdf002');

if (isset($_GET['type']) && $_GET['type'] == GET_PARTICIPANTS_LIST) {
  // get datas
  $adminName = get_data('name');
  $t_id = get_data('t_id');
  $cds_id = get_data('cds_id');
  $search = get_data('search');

  // require classes and init pdfcontroller
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/converts/converts.class.php';
  $conv = new Converts();

  // convert to pdf
  $conv->usersToPdf($t_id, $cds_id, $search, $adminName);

  // redirect to users page
  header("Location: ../users.php?t_id=$t_id&cds_id=$cds_id");
  exit();
} else if (isset($_GET['type']) && $_GET['type'] == GET_TRAININGS_LIST) {
  // get datas
  $npk = get_data('id');
  $adminName = get_data('name');
  $org_id = get_data('organizer');
  $approval = get_data('approval');
  $completion = get_data('completion');
  $search = get_data('search');

  // require classes and init pdfcontroller
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/converts/converts.class.php';
  $conv = new Converts();

  // convert to pdf
  $conv->trainingsToPdf($npk, $adminName, $org_id, $approval, $completion, $search);
}

function get_data($field)
{
  $result = stripslashes(htmlspecialchars($_GET[$field]));
  return (empty($result) && $result != "0") || $result == -1 ? null : $result;
}
