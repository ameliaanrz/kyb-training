<?php
ob_start();
$currUrl = $_SERVER['REQUEST_URI'];

// action types
define("ACTION_TYPE_GET_PARTICIPANTS", "TACT01");
define("ACTION_TYPE_GET_FILTERS", "TACT02");
define("ACTION_TYPE_GET_PARTICIPANT", "TACT03");
define("ACTION_TYPE_GET_PROFILE_FILTERS", "TACT04");
define("ACTION_TYPE_GET_EVENTS", "TACT05");
define("ACTION_TYPE_CREATE_USER", "TACT06");
define("ACTION_TYPE_GET_YEARS", "TACT07");

// default action type
$action_type = ACTION_TYPE_GET_PARTICIPANTS;

// enabled action types
$action_types = array(ACTION_TYPE_GET_PARTICIPANTS, ACTION_TYPE_GET_FILTERS, ACTION_TYPE_GET_PARTICIPANT, ACTION_TYPE_GET_PROFILE_FILTERS, ACTION_TYPE_GET_EVENTS, ACTION_TYPE_CREATE_USER,ACTION_TYPE_GET_YEARS);

// set action type
if (isset($_GET['type'])) {
  $action_type = get_data('type');
}

// unavailable action type
if (!in_array($action_type, $action_types)) {
  header("HTTP/1.1 400 Bad Request");
  header("Content-type: application/json");
  echo json_encode(array("Error" => "Invalid type parameter"));
  exit();
}

// arrays
$errors = array();
$departments = array();
$companies = array();
$sections = array();
$subsections = array();
$grades = array();
$genders = array();
$trainings = array();
$organizers = array();
$organizer = '';
$approval = -1;
$completion = -1;
$users = array();
$user = array(
  'npk' => '',
  'first_name' => '',
  'last_name' => '',
  'CDS_ID' => '',
  'new_department' => '',
  'new_company_department_section' => ''
);
$errors = array(
  'npk' => '',
  'first_name' => '',
  'last_name' => '',
  'company_department_section' => '',
  'new_company_department_section' => ''
);

// variables
$currPage = 1;
$lists_shown = 10;
$editing = false;
$department = '';
$training = '';
$t_id = '';
$section = '';
$subsection = '';
$company = '';
$grade = '';
$gender = '';
$search = '';
$npk = '';
$first_name = '';
$last_name = '';

// api responses
if (strcmp($action_type, ACTION_TYPE_GET_PARTICIPANTS) === 0) {
  // require users controller and init
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $usersContr = new UsersController();

  // get params
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $gender = get_data('gender');
  $grade = get_data('grade');
  $t_id = get_data('t_id');
  $search = get_data('search');
  $colomIndex = get_data('colomIndex');
  $direction = get_data('direction');

  // get users
  $users = $usersContr->getUsers($dpt_id, $sec_id, $sub_sec_id, $gender, $grade, $t_id, $search,$colomIndex,$direction);

  // responses
  if (empty($users)) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($users);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_FILTERS) === 0) {
  // require users controller and init
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $usersContr = new UsersController();

  // get params
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $gender = get_data('gender');
  $grade = get_data('grade');
  $t_id = get_data('t_id');

  // get filters
  $departments = $usersContr->getDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id);
  $sections = $usersContr->getSections($dpt_id, $sub_sec_id, $gender, $grade, $t_id);
  $subsections = $usersContr->getSubsections($dpt_id, $sec_id, $gender, $grade, $t_id);
  $grades = $usersContr->getGrades($dpt_id, $sec_id, $sub_sec_id, $gender, $t_id);
  $genders = $usersContr->getGenders($dpt_id, $sec_id, $sub_sec_id, $grade, $t_id);
  $trainings = $usersContr->getTrainings(null, $dpt_id, $sec_id, $sub_sec_id, $gender, $grade);

  // responses
  header("Content-type: application/json");
  echo json_encode(
    array(
      'departments' => $departments,
      'sections' => $sections,
      'subsections' => $subsections,
      'grades' => $grades,
      'genders' => $genders,
      'trainings' => $trainings,
    )
  );
  error_log(print_r($departments, true)); // Debug log
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_PARTICIPANT) === 0) {
  // require users controller and init
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $usersContr = new UsersController();


  // get url param
  $npk = get_data('npk');

  if (empty($npk)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "NPK parameter is required"));
    exit();
  }

  // get participant details
  $user = $usersContr->getUser($npk);

  // responses
  header("Content-type: application/json");
  echo json_encode($user);
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_PROFILE_FILTERS) === 0) {
  // require users controller and init
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $usersContr = new UsersController();

  // get url param
  $npk = get_data('npk');
  $org_id = get_data('org_id');
  $t_id = get_data('t_id');

  if (empty($npk)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "NPK parameter is required"));
    exit();
  }

  // get profile training filters
  $trainings = $usersContr->getTrainings($npk, null, null, null, null, null, $org_id);
  $organizers = $usersContr->getUserOrganizers($npk, $t_id);
  // $approvals = $usersContr->getApprovals($npk);
  // $completion = $usersContr->getUserOrganizers($npk);

  // responses
  header("Content-type: application/json");
  echo json_encode(
    array(
      'trainings' => $trainings,
      'organizers' => $organizers,
      // 'approvals' => $approvals,
      // 'completions' => $completions
    )
  );
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_EVENTS) === 0) {
  // require users controller and init
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $usersContr = new UsersController();

  // get url param
  $npk = get_data('npk');
  $dpt_id = get_data('dpt_id');
  $t_id = get_data('t_id');
  $evt_id = get_data('evt_id');
  $org_id = get_data('org_id');
  $description = get_data('description');
  $purpose = get_data('purpose');
  $start_date = get_data('start_date');
  $end_date = get_data('end_date');
  $grade = get_data('grade');
  $gender =get_data('gender');
  $month = get_data('filterMonth');
  $year = get_data('filterYear');
  $approved = isset($_GET['approved']) && !empty($_GET['approved']) ? stripslashes(htmlspecialchars($_GET['approved'] - 1)) : null;
  $approved_dept = isset($_GET['approved_dept']) && !empty($_GET['approved_dept']) ? stripslashes(htmlspecialchars($_GET['approved_dept'] - 1)) : null;
  $completed = isset($_GET['completed']) && !empty($_GET['completed']) ? stripslashes(htmlspecialchars($_GET['completed'] - 1)) : null;
  $search = get_data('search');
  $colomIndex = get_data('colomIndex');
  $direction = get_data('direction');

  if (empty($npk)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "NPK parameter is required"));
    exit();
  }

  // get profile training filters
  $events = $usersContr->getPurpose($npk,$dpt_id, $t_id, $evt_id, $org_id, $description, $purpose, $approved_dept, $start_date, $end_date, $approved,$grade,$gender, $completed, $search, $colomIndex,$direction, $month, $year);

  if (empty($events)) {
    header("HTTP/1.1 204 No Content");
    exit();
  }

  // responses
  header("Content-type: application/json");
  echo json_encode($events);
  exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE_USER) === 0) {
  // Pastikan file Excel sudah diunggah
  require_once __DIR__ . '/../classes/users/create-contr.class.php';
  if (!isset($_FILES['excel_file'])) {
    //http_response_code(400);
    header("Content-type: application/json");
    echo json_encode(array("error" => "No file uploaded"));
    exit();
  }

  $excelfile = $_FILES['excel_file']['tmp_name'];
  require_once '../vendor/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php'; // Memuat pustaka PHPExcel

  try {
    // Mendeteksi tipe file dan memuatnya menggunakan IOFactory
    $inputFileType = PHPExcel_IOFactory::identify($excelfile);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($excelfile);

    // Mendapatkan sheet pertama (indeks 0)
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestDataRow(); // Mendapatkan jumlah baris yang diisi

    // Mengambil data dari setiap baris dan menyimpannya ke database
    for ($row = 2; $row <= $highestRow; $row++) { // Mulai dari baris kedua, karena baris pertama mungkin judul kolom
      $npk = $sheet->getCellByColumnAndRow(0, $row)->getValue(); // Asumsikan NPK berada di kolom A
      $npk = str_replace('.', '', $npk); // Menghilangkan titik dari NPK// Asumsikan NPK berada di kolom A
      $password = $sheet->getCellByColumnAndRow(1, $row)->getValue(); // Asumsikan Password berada di kolom B
      $name = $sheet->getCellByColumnAndRow(2, $row)->getValue(); // Asumsikan Nama berada di kolom C
      $dpt_id = $sheet->getCellByColumnAndRow(3, $row)->getValue();
      $sec_id = $sheet->getCellByColumnAndRow(4, $row)->getValue();
      $sub_sec_id = $sheet->getCellByColumnAndRow(5, $row)->getValue();
      $grade = $sheet->getCellByColumnAndRow(6, $row)->getValue();
      $gender = $sheet->getCellByColumnAndRow(7, $row)->getValue();
      $c_id = $sheet->getCellByColumnAndRow(8, $row)->getValue();
      $rls_id = $sheet->getCellByColumnAndRow(9, $row)->getValue();

      // Buat objek CreateUsersController dan panggil createTraining
      $usersContr = new CreateUsersController($npk, $password, $name, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $c_id, $rls_id, $excelfile);
      $usersContr->createTraining($npk, $password, $name, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $c_id, $rls_id, $excelfile);
    }

    // Tanggapan sukses
    header("HTTP/1.1 201 Created");
    header("Content-type: application/json");
    echo json_encode(array("success" => "Users created successfully"));
    ob_end_flush();
    exit();
  } catch (Exception $e) {
    // Tanggapan jika terjadi kesalahan
    //http_response_code(500);
    header("Content-type: application/json");
    echo json_encode(array("error" => "Internal Server Error: " . $e->getMessage()));
    exit();
  }
}else if (strcmp($action_type, ACTION_TYPE_GET_YEARS) === 0) {
  // require users controller and init
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $usersContr = new UsersController();

  // get available years
  $years = $usersContr->getYears();

  if (empty($years)) {
    header("HTTP/1.1 204 No Content");
    exit();
  }

  // responses
  header("Content-type: application/json");
  echo json_encode($years);
  exit();
}



function post_data($field)
{
  return isset($_POST[$field]) && !empty($_POST[$field]) ? stripslashes(htmlspecialchars($_POST[$field])) : null;
}

function get_data($field)
{
  return isset($_GET[$field]) && !empty($_GET[$field]) ? stripslashes(htmlspecialchars($_GET[$field])) : null;
}
