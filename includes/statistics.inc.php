<?php

define("ACTION_TYPE_GET_STATISTICS", "SACT01");
define("ACTION_TYPE_GET_FILTERS", "SACT02");

// set default type
$action_type = ACTION_TYPE_GET_STATISTICS;

// available types
$action_types = array(ACTION_TYPE_GET_STATISTICS, ACTION_TYPE_GET_FILTERS);

// set action type based on param
if (isset($_GET['type'])) {
  $action_type = get_data('type');
}

// if type is not in available type
if (!in_array($action_type, $action_types)) {
  header("HTTP/1.1 400 Bad Request");
  header("Content-type: application/json");
  echo json_encode(array("Error" => "Invalid type parameter"));
  exit();
}

// responses
if (strcmp($action_type, ACTION_TYPE_GET_STATISTICS) === 0) {
  // require classes and init statisticsController
  require_once __DIR__ . '/../classes/statistics/statistics.class.php';
  $stats = new Statistics();

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
  $trainings_total = $stats->getTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $employees_total = $stats->getEmployeesCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $male_participants = $stats->getMaleParticipantsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $female_participants = $stats->getFemaleParticipantsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $running_trainings = $stats->getRunningTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $next_trainings = $stats->getNextTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $completed_trainings = $stats->getCompletedTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $male_hours = $stats->getMaleTotalTrainingHours($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);
  $femaleHours = $stats->getFemaleTotaltrainingHours($dpt_id, $sec_id, $sub_sec_id, $grade, $gender);

  header("Content-type: application/json");
  echo json_encode(array(
    'trainings_total' => $trainings_total,
    'running_trainings' => $running_trainings,
    'next_trainings' => $next_trainings,
    'completed_trainings' => $completed_trainings,
    'employees_total' => $employees_total,
    'male_participants' => $male_participants,
    'female_participants' => $female_participants,
    'male_hours' => $male_hours,
    'female_hours' => $female_hours,
  ));
} else if (strcmp($action_type, ACTION_TYPE_GET_FILTERS) === 0) {
  // require classes and init statisticsController
  require_once __DIR__ . '/../classes/statistics/statistics.class.php';
  $stats = new Statistics();

  // get queries
  $grades = $stats->getGrades();
  $departments = $stats->getDepartments();
  $sections = $stats->getSections();
  $subsections = $stats->getSubSections();
  $genders = $stats->getGender();

  $statsw=array(
    'grades' => $grades,
    'departments' => $departments,
    'sections' => $sections,
    'subsections' => $subsections,
    'genders' => $genders,
  );
  // json response
  header("Content-type: application/json");
  echo json_encode($statsw);
}

function get_data($field)
{
  return isset($_GET[$field]) && !empty($_GET[$field]) ? stripslashes(htmlspecialchars($_GET[$field])) : null;
}
