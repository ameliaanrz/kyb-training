<?php

$currUrl = $_SERVER['REQUEST_URI'];

define("ACTION_TYPE_PRINT_STATISTICS", "RACT01");
define("ACTION_TYPE_PRINT_PARTICIPANTS", "RACT02");
define("ACTION_TYPE_PRINT_PARTICIPANT_TRAININGS", "RACT03");
define("ACTION_TYPE_PRINT_ATTENDANCE_FORM", "RACT04");

$action_type = ACTION_TYPE_PRINT_STATISTICS;

$action_types = array(ACTION_TYPE_PRINT_STATISTICS, ACTION_TYPE_PRINT_PARTICIPANTS, ACTION_TYPE_PRINT_PARTICIPANT_TRAININGS, ACTION_TYPE_PRINT_ATTENDANCE_FORM);

if (isset($_GET['type'])) {
  $action_type = get_data('type');
}

if (!in_array($action_type, $action_types)) {
  header("HTTP/1.1 400 Bad Request");
  header("Content-type: application/json");
  echo json_encode(array("Error" => "Invalid type parameter"));
  exit();
}
// variables
$search = '';
$department = '';
$company = '';
$section = '';
$subsection = '';
$grade = '';
$gender = '';
$evt_id = '';
$t_id = '';
$organizer = '';
$ta_id = '';
$trainer = '';
$loc_id = '';
$location = '';
$training_location = '';
$new_location = '';
$trainer = '';
$new_trainer = '';
$new_organizer = '';
$purpose='';
$description = '';
$start_date = '';
$end_date = '';
$days = '';
$start_time = '';
$end_time = '';
$duration = '';
$completion = '';
$approval = '';
$training_status = null;

// arrays
$employees = array();
$departments = array();
$trainings = array();
$event = array();
$locations = array();
$trainers = array();
$registered_users = array();
$completions = array();
$approvals = array();
$errors = array(
  't_id' => '',
  'registered_users' => '',
  'org_id' => '',
  'organizer' => '',
  'ta_id' => '',
  'trainer' => '',
  'company' => '',
  'section' => '',
  'subsection' => '',
  'grade' => '',
  'gender' => '',
  'new_organizer' => '',
  'description' => '',
  'start_date' => '',
  'end_date' => '',
  'days' => '',
  'start_time' => '',
  'end_time' => '',
  'duration' => '',
  'location' => '',
  'loc_id' => '',
  'new_location' => '',
  'new_trainer' => '',
  'completion' => '',
  'approval' => ''
);

session_start();

$npk = $_SESSION['NPK'];
$name = $_SESSION['NAME'];

if (strcmp($action_type, ACTION_TYPE_PRINT_STATISTICS) === 0) {
  // require report controller and create instance
  require_once __DIR__ . '/../classes/reports/report-contr.class.php';
  require_once __DIR__ . '/../classes/statistics/statistics.class.php';
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $reportContr = new ReportController();
  $stats = new Statistics();
  $usersContr = new UsersController();

  // get params
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $grade = get_data('grade');
  $gender = get_data('gender');

  $trainings_total = 0;
  $employees_total = 0;
  $running_trainings = 0;
  $female_participants = 0;
  $male_participants = 0;
  $male_hours = 0;
  $female_hours = 0;

  // get statistics
  $trainings_total = $stats->getTrainingsCount(
    $dpt_id,
    $sec_id,
    $sub_sec_id,
    $grade,
    $gender
  );
  $employees_total = $stats->getEmployeesCount(
    $dpt_id,
    $sec_id,
    $sub_sec_id,
    $grade,
    $gender
  );
  $male_participants = $stats->getMaleParticipantsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $female_participants = $stats->getFemaleParticipantsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $running_trainings = $stats->getRunningTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $next_trainings = $stats->getNextTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $completed_trainings = $stats->getCompletedTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $male_hours = $stats->getMaleTotalTrainingHours($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $femaleHours = $stats->getFemaleTotaltrainingHours($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);

  $filterNames = $usersContr->getFilterNames(
    $dpt_id,
    $sec_id,
    $sub_sec_id,
    null
  );
  $filters = array(
    'department' =>
    isset($dpt_id) && !empty($dpt_id) && isset($filterNames['DEPARTMENT']) && !empty($filterNames['DEPARTMENT']) ? substr($filterNames['DEPARTMENT'], 0, 25) : "All",
    'section' =>
    isset($sec_id) && !empty($sec_id) && isset($filterNames['SECTION']) && !empty($filterNames['SECTION']) ? substr($filterNames['SECTION'], 0, 25) : "All",
    'subsection' =>
    isset($sub_sec_id) && !empty($sub_sec_id) && isset($filterNames['SUBSECTION']) && !empty($filterNames['SUBSECTION']) ? substr($filterNames['SUBSECTION'], 0, 25) : "All",
    'grade' =>
    isset($grade) && !empty($grade) ? $grade : "All",
    'gender' => isset($gender) && !empty($gender) ? $gender : "All"
  );
  $stats = array(
    'trainings_total' => $trainings_total,
    'running_trainings' => $running_trainings,
    'next_trainings' => $next_trainings,
    'completed_trainings' => $completed_trainings,
    'employees_total' => $employees_total,
    'male_participants' => $male_participants,
    'female_participants' => $female_participants,
    'male_hours' => $male_hours,
    'female_hours' => $female_hours,
  );

  $reportContr->printStatistics($stats, $filters);
} else if (strcmp($action_type, ACTION_TYPE_PRINT_PARTICIPANTS) === 0) {
  // require report controller and create instance
  require_once __DIR__ . '/../classes/reports/report-contr.class.php';
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $reportContr = new ReportController();
  $usersContr = new UsersController();

  // get params
  $npk = get_data('npk');
  $dpt_id = get_data('dpt_id');
  $t_id = get_data('t_id');
  $grade =get_data('grade');
  $gender =get_data('gender');
  $search =get_data('search');
  $colomIndex =get_data('colomIndex');
  $direction =get_data('direction');
  $org_id = get_data('org_id');
  $start_date = get_data('start_date');
  $end_date = get_data('end_date');
  $approved = isset($_GET['approved']) && !empty($_GET['approved']) ? stripslashes(htmlspecialchars($_GET['approved'] - 1)) : null;
  $completed = isset($_GET['completed']) && !empty($_GET['completed']) ? stripslashes(htmlspecialchars($_GET['completed'] - 1)) : null;
  $org_id = get_data('org_id');

  // get all participants
  $participants = $usersContr->getEvents($npk,$dpt_id, $t_id, $org_id, $start_date, $end_date, $approved,$grade,$gender, $completed, $search, $colomIndex, $direction);
  $filterNames = $usersContr->getFilterNames($dpt_id,$t_id, $npk, $org_id);
  $filters = array(
    'department' =>
    isset($dpt_id) && !empty($dpt_id) && isset($filterNames['DEPARTMENT']) && !empty($filterNames['DEPARTMENT']) ? substr($filterNames['DEPARTMENT'], 0, 25) : "All",
    'username' => isset($npk) && !empty($npk) && isset($filterNames['NAME']) && !empty($filterNames['NAME']) ? substr($filterNames['NAME'], 0, 25) : "All",
    'organizer' => isset($org_id) && !empty($org_id) && isset($filterNames['ORGANIZER']) && !empty($filterNames['ORGANIZER']) ? substr($filterNames['ORGANIZER'], 0, 25) : "All",
    'training' => isset($t_id) && !empty($t_id) && isset($filterNames['TRAINING']) && !empty($filterNames['TRAINING']) ? substr($filterNames['TRAINING'], 0, 25) : "All",
    'start_date' => isset($start_date) && !empty($start_date) ? $start_date : "All",
    'grade' =>
    isset($grade) && !empty($grade) ? $grade : "All",
    'gender' => isset($gender) && !empty($gender) ? $gender : "All",
    'end_date' => isset($end_date) && !empty($end_date) ? $end_date : "All",
    'approved' => isset($approved) ? $approved : "All",
    'completed' => isset($completed) ? $completed : "All"
  );

  // print participants report
  $reportContr->printParticipants($filters, $participants);
} else if (strcmp($action_type, ACTION_TYPE_PRINT_PARTICIPANT_TRAININGS) === 0) {
  // require report controller and create instance
  require_once __DIR__ . '/../classes/reports/report-contr.class.php';
  require_once __DIR__ . '/../classes/users/users-contr.class.php';
  $reportContr = new ReportController();
  $usersContr = new UsersController();

  // get params
  $npk = get_data('npk');
  $dpt_id = get_data('dpt_id');
  $t_id = get_data('t_id');
  $grade =get_data('grade');
  $gender =get_data('gender');
  $search =get_data('search');
  $colomIndex =get_data('colomIndex');
  $direction =get_data('direction');
  $org_id = get_data('org_id');
  $start_date = get_data('start_date');
  $end_date = get_data('end_date');
  $approved = isset($_GET['approved']) && !empty($_GET['approved']) ? stripslashes(htmlspecialchars($_GET['approved'] - 1)) : null;
  $completed = isset($_GET['completed']) && !empty($_GET['completed']) ? stripslashes(htmlspecialchars($_GET['completed'] - 1)) : null;
  $org_id = get_data('org_id');

  // get all participants
  $events = $usersContr->getEvents($npk,$dpt_id, $t_id, $org_id, $start_date, $end_date, $approved,$grade,$gender, $completed, $search, $colomIndex, $direction);
  $filterNames = $usersContr->getFilterNames($dpt_id,$t_id, $npk, $org_id);
  $filters = array(
    'npk' => 
    $npk,
    'department' =>
    isset($dpt_id) && !empty($dpt_id) && isset($filterNames['DEPARTMENT']) && !empty($filterNames['DEPARTMENT']) ? substr($filterNames['DEPARTMENT'], 0, 25) : "All",
    'username' => isset($npk) && !empty($npk) && isset($filterNames['NAME']) && !empty($filterNames['NAME']) ? substr($filterNames['NAME'], 0, 25) : "All",
    'organizer' => isset($org_id) && !empty($org_id) && isset($filterNames['ORGANIZER']) && !empty($filterNames['ORGANIZER']) ? substr($filterNames['ORGANIZER'], 0, 25) : "All",
    'training' => isset($t_id) && !empty($t_id) && isset($filterNames['TRAINING']) && !empty($filterNames['TRAINING']) ? substr($filterNames['TRAINING'], 0, 25) : "All",
    'start_date' => isset($start_date) && !empty($start_date) ? $start_date : "All",
    'grade' =>
    isset($grade) && !empty($grade) ? $grade : "All",
    'gender' => isset($gender) && !empty($gender) ? $gender : "All",
    'end_date' => isset($end_date) && !empty($end_date) ? $end_date : "All",
    'approved' => isset($approved) ? $approved : "All",
    'completed' => isset($completed) ? $completed : "All"
  );


  if ($approved == 1) {
    $filters['approved'] = "Waiting for approval";
  } else if ($approved == 2) {
    $filters['approved'] = "Approved";
  } else if ($approved == 3) {
    $filters['approved'] = "Not Approved";
  } else {
    $filters['approved'] = "All";
  }

  if ($completed === '0') {
    $filters['completed'] = "Not completed";
  } else if ($completed === '1') {
    $filters['completed'] = "Completed";
  } else {
    $filters['completed'] = "All";
  }

  // print participants report
  $reportContr->printParticipantTrainings($filters, $events);
} else if(strcmp($action_type, ACTION_TYPE_PRINT_ATTENDANCE_FORM) === 0){
  // require report controller and create instance
  require_once __DIR__ . '/../classes/reports/report-contr.class.php';
  require_once __DIR__ . '/../classes/events/events-contr.class.php';

  $reportContr = new ReportController();
  $evtContr = new EventController();

  // get parameters
  $evt_id = get_data('evt_id');
  $for =get_data('for');

  // search params
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $approval = isset($_GET['approval']) && in_array($_GET['approval'], array('0', '1', '2')) ? stripslashes(htmlspecialchars($_GET['approval'])) : null;
  $grade = get_data('grade');
  $gender = get_data('gender');
  $search = get_data('search');
  $colomIndex =get_data('colomIndex');
  $direction =get_data('direction');
  // unexisting parameter check
  if (empty($evt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'NPK and EVT_ID parameter is required'));
    exit();
  }

  // get registered users
  $userss = $evtContr->getParticipants($evt_id, $dpt_id, $approval, $sec_id, $sub_sec_id, $grade, $gender, $search, $colomIndex, $direction);
  $training = $evtContr->getTrainingByEvtId($evt_id);

  // response

    if ($userss==false) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'null'));
    exit();
  }
  if($for == 'print'){
  $reportContr->printAttendanceForm($userss,$training);
  }else{
       function getIndonesianMonth($date) {
      $monthNames = array(
          '01' => 'Januari',
          '02' => 'Februari',
          '03' => 'Maret',
          '04' => 'April',
          '05' => 'Mei',
          '06' => 'Juni',
          '07' => 'Juli',
          '08' => 'Agustus',
          '09' => 'September',
          '10' => 'Oktober',
          '11' => 'November',
          '12' => 'Desember'
      );
      
      $month = date('m', strtotime($date));
      $year = date('Y', strtotime($date));
      return $monthNames[$month].' '.$year;
    }
    
    function getIndonesianDay($date) {
      $dayNames = array(
          'Sunday' => 'Minggu',
          'Monday' => 'Senin',
          'Tuesday' => 'Selasa',
          'Wednesday' => 'Rabu',
          'Thursday' => 'Kamis',
          'Friday' => 'Jumat',
          'Saturday' => 'Sabtu'
      );
      $day = date('l', strtotime($date));
      return $dayNames[$day];
    }

    // Get the current date in the desired format
    $tanggal_hari_ini = strftime('%d %B %Y');

    // Capitalize the first letter of the day name
    $tglnow = date('d',strtotime($tanggal_hari_ini));

    $tanggal_hari_ini = getIndonesianMonth($tanggal_hari_ini);
    
    $tglfrom=date('d',strtotime($training['START_DATE']));
    $tglto=date('d',strtotime($training['END_DATE']));
    $nameDayFrom=getIndonesianDay($training['START_DATE']);
    $nameDayTO=getIndonesianDay($training['END_DATE']);
    $bulan=getIndonesianMonth($training['START_DATE']);
    $waktufrom=date('H.i',strtotime($training['START_TIME']));
    $waktuto=date('H.i',strtotime($training['END_TIME']));
    $haritgl ='';

    if ($tglfrom == $tglto) {
      // Jika hanya satu hari
      $haritgl =  $nameDayFrom.', '.$tglfrom.' '.$bulan;
    } else {
        $haritgl = $nameDayFrom.' - '.$nameDayTO.', '.$tglfrom.' - '.$tglto.' '.$bulan;
    }

    $array = array(
      'haritgl' => $haritgl,
      'namaevent' => $training['TRAINING'],
      'participant' => $userss,
    );
    header("Content-type: application/json");
    echo json_encode($array);
  }
}

function get_data($field)
{
  return isset($_GET[$field]) && !empty($_GET[$field]) ? stripslashes(htmlspecialchars($_GET[$field])) : null;
}
