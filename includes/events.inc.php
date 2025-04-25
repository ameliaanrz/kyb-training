<?php

session_start();

$currUrl = $_SERVER['REQUEST_URI'];

// action types consts
define("ACTION_TYPE_GET_EVENTS", "EACT01");
define("ACTION_TYPE_CREATE_EVENTS", "EACT02");
define("ACTION_TYPE_UPDATE_EVENTS", "EACT03");
define("ACTION_TYPE_DELETE_EVENTS", "EACT04");
define("ACTION_TYPE_GET_TRAINERS", "EACT05");
define("ACTION_TYPE_GET_ORGANIZER", "EACT06");
define("ACTION_TYPE_CREATE_TRAINER", "EACT07");
define("ACTION_TYPE_CREATE_ORGANIZER", "EACT08");
define("ACTION_TYPE_CREATE_LOCATION", "EACT09");
define("ACTION_TYPE_GET_ORGANIZERS", "EACT10");
define("ACTION_TYPE_GET_QUERY_DATA", "EACT11");
define("ACTION_TYPE_GET_PARTICIPANTS", "EACT12");
define("ACTION_TYPE_APPROVE_PARTICIPANTS", "EACT13");
define("ACTION_TYPE_GET_TRAINING", "EACT14");
define("ACTION_TYPE_GET_REGISTER_QUERIES", "EACT15");
define("ACTION_TYPE_GET_USERS", "EACT16");
define("ACTION_TYPE_REGISTER_USER", "EACT17");
define("ACTION_TYPE_GET_REGISTERED_USERS", "EACT18");
define("ACTION_TYPE_REGISTER_USERS", "EACT19");
define("ACTION_TYPE_GET_ROLE", "EACT20");
define("ACTION_TYPE_GET_DEPARTMENT", "EACT21");
define("ACTION_TYPE_APPROVEDEPT_PARTICIPANTS", "EACT22");
define("ACTION_TYPE_DISSAPPROVEDEPT_PARTICIPANTS", "EACT23");
define("ACTION_TYPE_GET_EVENT_STATUS", "EACT24");
define("ACTION_TYPE_GET_CONTENT", "EACT25");
define("ACTION_TYPE_GET_PARTICIPANT_EVENT", "EACT26");
define("ACTION_TYPE_UPLOAD_EVENTS", "EACT27");

// default action type
$action_type = ACTION_TYPE_GET_EVENTS;

// allowed action types
$action_types = array(ACTION_TYPE_GET_EVENTS, ACTION_TYPE_GET_EVENT_STATUS, ACTION_TYPE_DISSAPPROVEDEPT_PARTICIPANTS, ACTION_TYPE_APPROVEDEPT_PARTICIPANTS, ACTION_TYPE_CREATE_EVENTS, ACTION_TYPE_UPDATE_EVENTS, ACTION_TYPE_DELETE_EVENTS, ACTION_TYPE_GET_TRAINERS, ACTION_TYPE_GET_ORGANIZER, ACTION_TYPE_CREATE_TRAINER, ACTION_TYPE_CREATE_ORGANIZER, ACTION_TYPE_CREATE_LOCATION, ACTION_TYPE_GET_ORGANIZERS, ACTION_TYPE_GET_QUERY_DATA, ACTION_TYPE_GET_PARTICIPANTS, ACTION_TYPE_APPROVE_PARTICIPANTS, ACTION_TYPE_GET_TRAINING, ACTION_TYPE_GET_REGISTER_QUERIES, ACTION_TYPE_GET_USERS, ACTION_TYPE_REGISTER_USER, ACTION_TYPE_GET_REGISTERED_USERS, ACTION_TYPE_REGISTER_USERS, ACTION_TYPE_GET_ROLE, ACTION_TYPE_GET_DEPARTMENT, ACTION_TYPE_GET_CONTENT, ACTION_TYPE_GET_PARTICIPANT_EVENT, ACTION_TYPE_UPLOAD_EVENTS);

// set action type based on url param
if (isset($_GET['type'])) {
  $action_type = get_data('type');
}

// action type not allowed error response
if (!in_array($action_type, $action_types)) {
  header("HTTP/1.1 400 Bad Request");
  header("Content-type: application/json");
  echo json_encode(array("Error" => "Invalid type parameter"));
  exit();
}

$currPage = 1;
$lists_shown = 10;

// variables
$search = '';
$department = '';
$company = '';
$section = '';
$subsection = '';
$grade = '';
$gender = '';
$evt_id = '';
$events = '';
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
$purpose = '';
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
$events = array();
$trainings = array();
$ids = array();
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
  'events' => '',
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

// events actions based on action type
if (strcmp($action_type, ACTION_TYPE_GET_EVENTS) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();
  $evt_id = get_data('evt_id');
  $search = get_data('search');
  $month = get_data('month');
  $year = get_data('year');
  $colomn = get_data('colomn');
  $direction = get_data('direction');

  // get all trainings
  $trainings = $eventController->getTrainings();

  // get all organizers
  $organizers = $eventController->getTrainerOrganizers();

  // get all locations
  $locations = $eventController->getLocations();

  // get all trainers
  $trainers = $eventController->getTrainers();

  // get all months
  $months = $eventController->getMonths();

  $years = $eventController->getYears();


  if (!empty($evt_id)) {
    $event = $eventController->getEvent($evt_id);

    if ($event === false) {
      header("HTTP/1.1 204 No Content");
    } else {
      header("Content-type: application/json");
      echo json_encode(array(
        "event" => $event,
        "trainings" => $trainings,
        "organizers" => $organizers,
        "locations" => $locations,
        "trainers" => $trainers,
      ));
    }
    exit();
  }

  // get all training events
  $events = $eventController->getEvents($search, $month, $year, $colomn, $direction);

  // return response
  header("Content-type: application/json");
  echo json_encode(array(
    "events" => $events,
    "trainings" => $trainings,
    "purpose" => $purpose,
    "organizers" => $organizers,
    "locations" => $locations,
    "trainers" => $trainers,
    "months" => $months,
    "years" => $years
  ));
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_ORGANIZER) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  $trainer = get_data('trainer');

  // get all organizers
  $organizer = $eventController->getOrganizerByTrainer($trainer);

  // return response
  header("Content-type: application/json");
  echo json_encode(array(
    "organizer" => $organizer,
  ));
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_TRAINERS) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  $organizer = get_data('organizer');

  // get all trainers
  $trainers = $eventController->getTrainers($organizer);

  // return response
  header("Content-type: application/json");
  echo json_encode(array(
    "trainers" => $trainers,
  ));
  exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE_EVENTS) === 0) {
  // get post datas
  $t_id = post_data('t_id');
  $org_id = post_data('org_id');
  $ta_id = post_data('ta_id');
  $loc_id = post_data('loc_id');
  $start_date = post_data('start_date');
  $end_date = post_data('end_date');
  $start_time = post_data('start_time');
  $end_time = post_data('end_time');
  $duration_days = post_data('duration_days');
  $duration_hours = post_data('duration_hours');
  $evt_to = $_POST['evt_to'];

  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventsContr = new EventController();

  // create new event
  $errors = $eventsContr->createTrainingEvent($t_id, $org_id, $ta_id, $loc_id, $start_date, $end_date, $duration_days, $start_time, $end_time, $duration_hours, $evt_to);

  // json response
  if (
    empty($errors['t_id']) &&
    empty($errors['org_id']) &&
    empty($errors['ta_id']) &&
    empty($errors['loc_id']) &&
    empty($errors['start_date']) &&
    empty($errors['end_date']) &&
    empty($errors['start_time']) &&
    empty($errors['end_time']) &&
    empty($errors['duration_hours']) &&
    empty($errors['duration_days']) &&
    empty($error['evt_to'])
  ) {
    header("HTTP/1.1 201 Created");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE_ORGANIZER) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventsContr = new EventController();

  // get post datas
  $org_id = $eventsContr->getLatestOrgId();
  $org_id = empty($org_id) ? "ORG001" : "ORG" . str_pad(strval((int)substr($org_id, 3) + 1), 3, "0", STR_PAD_LEFT);
  $organizer = post_data('organizer');

  // create new event
  $errors = $eventsContr->createOrganizer($org_id, $organizer);

  // json response
  if (
    empty($errors['org_id']) &&
    empty($errors['organizer'])
  ) {
    header("HTTP/1.1 201 Created");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('org_id' => $errors['org_id'], 'organizer' => $errors['organizer']));
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_ORGANIZERS) === 0) {
  // require and instantiate events controller
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventsContr = new EventController();

  // get all organizers
  $organizers = $eventsContr->getAllOrganizers();

  // json responses
  if (empty($organizers)) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($organizers);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE_TRAINER) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventsContr = new EventController();

  // get post datas
  $ta_id = $eventsContr->getLatestTrainerId();
  $ta_id = empty($ta_id) ? "TA0001" : "TA" . str_pad(strval((int)substr($ta_id, 2) + 1), 4, "0", STR_PAD_LEFT);
  $org_id = post_data('org_id');
  $trainer = post_data('trainer');

  // create new event
  $errors = $eventsContr->createTrainer($org_id, $ta_id, $trainer);

  // json response
  if (
    empty($errors['org_id']) &&
    empty($errors['trainer']) &&
    empty($errors['ta_id'])
  ) {
    header("HTTP/1.1 201 Created");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('org_id' => $errors['org_id'], 'trainer' => $errors['trainer'], 'ta_id' => $errors['ta_id']));
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE_LOCATION) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventsContr = new EventController();

  // get post datas
  $loc_id = $eventsContr->getLatestLocationId();
  $loc_id = empty($loc_id) ? "LOC0001" : "LOC" . str_pad(strval((int)substr($loc_id, 3) + 1), 4, "0", STR_PAD_LEFT);
  $location = post_data('location');

  // create new location
  $errors = $eventsContr->createLocation($loc_id, $location);

  // json response
  if (
    empty($errors['loc_id']) &&
    empty($errors['location'])
  ) {
    header("HTTP/1.1 201 Created");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('loc_id' => $errors['loc_id'], 'location' => $errors['location']));
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_UPDATE_EVENTS) === 0) {
  // get url param
  $evt_id = get_data('evt_id');

  if (empty($evt_id)) {
    header("HTTP/1.1 404 Not Found");
    exit();
  }

  // get post datas
  $t_id = post_data('t_id');
  $org_id = post_data('org_id');
  $evt_to = $_POST['evt_to'];
  $ta_id = post_data('ta_id');
  $loc_id = post_data('loc_id');
  $start_date = post_data('start_date');
  $end_date = post_data('end_date');
  $start_time = post_data('start_time');
  $end_time = post_data('end_time');
  $activated = $_POST['activated'];

  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventsContr = new EventController();

  // create new event
  $errors = array_merge($errors, $eventsContr->updateTrainingEvent($evt_id, $evt_to, $activated, $t_id, $org_id, $ta_id, $loc_id, $start_date, $end_date, $start_time, $end_time));

  // json response
  if (
    empty($errors['evt_id']) &&
    empty($errors['t_id']) &&
    empty($errors['org_id']) &&
    empty($errors['ta_id']) &&
    empty($errors['loc_id']) &&
    empty($errors['start_date']) &&
    empty($errors['end_date']) &&
    empty($errors['start_time']) &&
    empty($errors['end_time']) &&
    empty($errors['evt_to'])
  ) {
    header("HTTP/1.1 200 OK");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_DELETE_EVENTS) === 0) {
  // get url param
  $evt_id = get_data('evt_id');

  if (empty($evt_id)) {
    header("HTTP/1.1 404 Not Found");
    exit();
  }

  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventsContr = new EventController();

  // delete event
  $eventsContr->deleteEvent($evt_id);

  // json response
  header("HTTP/1.1 204 No Content");
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_QUERY_DATA) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  $evt_id = get_data('evt_id');

  if (empty($evt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event ID parameter is required"));
    exit();
  }

  // get all event datas
  $results = $eventController->getEventData($evt_id);

  // return response
  header("Content-type: application/json");
  echo json_encode($results);
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_PARTICIPANTS) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  $evt_id = get_data('evt_id');

  // search params
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $approval = isset($_GET['approval']) && in_array($_GET['approval'], array('0', '1', '2')) ? stripslashes(htmlspecialchars($_GET['approval'])) : null;
  $grade = get_data('grade');
  $gender = get_data('gender');
  $search = get_data('search');
  $colomIndex = get_data('colomIndex');
  $direction = get_data('direction');

  if (empty($evt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event ID parameter is required"));
    exit();
  }

  // get all event datas
  $participants = $eventController->getParticipants($evt_id, $dpt_id, $approval, $sec_id, $sub_sec_id, $grade, $gender, $search, $colomIndex, $direction);

  // return response
  if ($participants === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($participants);
  }

  exit();
} else if (strcmp($action_type, ACTION_TYPE_APPROVE_PARTICIPANTS) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  if (!isset($_POST['ids'])) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event IDs parameter is required"));
    exit();
  }

  $ids = $_POST['ids'];
  $approval = post_data('approval');
  $reason = post_data('reason');
  $tmp_ids = array();
   if (is_array($ids)) {
    foreach ($ids as $id) {
      if (!empty($id)) {
        array_push($tmp_ids, stripslashes(htmlspecialchars($id)));
      }
    }
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event IDs must be an array"));
    exit();
  }
  $ids = $tmp_ids;

  if (empty($ids) || !in_array($approval, array('0', '1', '2'))) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event ID parameter is required"));
    exit();
  }

  // approve participants
  $eventController->approveParticipants($ids, $approval);
  $participants = $eventController->getParticipants($evt_id, $dpt_id, $approval, $sec_id, $sub_sec_id, $grade, $gender, $search, $colomIndex, $direction);

  // return response
  if ($participants === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($participants);
  }

  exit();
} else if (strcmp($action_type, ACTION_TYPE_APPROVEDEPT_PARTICIPANTS) === 0) {
  // require events controller and instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  $ids = $_POST['ids'];
  $approval = post_data('approval');
  $tmp_ids = array();
  foreach ($ids as $id) {
    if (!empty($id)) {
      array_push($tmp_ids, stripslashes(htmlspecialchars($id)));
    }
  }
  $ids = $tmp_ids;

  if (empty($ids) || !in_array($approval, array('0', '1', '2'))) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event ID parameter is required"));
    exit();
  }

  // approve participants
  $eventController->approvedDeptParticipants($ids, $approval);
  $participants = $eventController->getParticipants($evt_id, $dpt_id, $approval, $sec_id, $sub_sec_id, $grade, $gender, $search, $colomIndex, $direction);

  // return response
  if ($participants === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($participants);
  }

  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_TRAINING) === 0) {
  // require events controller and init
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $evtContr = new EventController();
  $t_id = get_data('t_id');
  // get training id param
  $evt_id = get_data('evt_id');

  if (empty($evt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'Event ID parameter is required'));
    exit();
  }

  // get training
  $training = $evtContr->getTrainingByEvtId($evt_id);
  //$training = $evtContr->getTraining($t_id);

  // response
  if ($training === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($training);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_REGISTER_QUERIES) === 0) {
  // require events controller and init
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $evtContr = new EventController();

  // get evt_id parameter
  $evt_id = get_data('evt_id');

  // get queries
  $queries = $evtContr->getRegisterParticipantsQueries();

  // response
  header("Content-type: application/json");
  echo json_encode($queries);
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_USERS) === 0) {
  // require events controller and init
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $evtContr = new EventController();

  // get parameters
  $c_id = get_data('c_id');
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $grade = get_data('grade');
  $gender = get_data('gender');
  $search = get_data('search');
  $evt_id = get_data('evt_id');
  $colomIndex = get_data('colomIndex');
  $direction = get_data('direction');

  // get users
  $users = $evtContr->getUsers($c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $search, $evt_id, $colomIndex, $direction);

  // response
  if ($users == false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($users);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_REGISTER_USER) === 0) {
  // require events controller and init
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $evtContr = new EventController();

  // get parameters
  $npk = post_data('npk');
  $evt_id = get_data('evt_id');
  $check_type = get_data('check_type');
  $tmp_npk = array();
  foreach ($npk as $tmp_npk) {
    if (!empty($npk)) {
      array_push($tmp_npk, stripslashes(htmlspecialchars($npk)));
    }
  }
  $npk = $tmp_npk;


  // unexisting parameter check
  if (empty($evt_id) || empty($check_type)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'NPK and EVT_ID parameter is required'));
    exit();
  }

  if (strcmp($check_type, "true") === 0) {
    // create new event participants id
    $ep_id = $evtContr->getLatestEpId() ? "EP" . str_pad(strval((int)substr($evtContr->getLatestEpId(), 2) + 1), 4, "0", STR_PAD_LEFT) : 'EP0001';

    foreach ($npk as $users) {
      $evtContr->registerUser($ep_id, $evt_id, $users);
    }

    header("HTTP/1.1 201 Created");
  } else {
    $evtContr->unregisterUser($evt_id, $npk);
  }

  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_REGISTERED_USERS) === 0) {
  // instantiate events controller
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $evtContr = new EventController();

  // get parameters
  $evt_id = get_data('evt_id');
  $c_id = get_data('c_id');
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $grade = get_data('grade');
  $gender = get_data('gender');
  $approved = isset($_GET['approved']) && !empty($_GET['approved']) ? stripslashes(htmlspecialchars($_GET['approved'] - 1)) : null;
  $completed = isset($_GET['completed']) && !empty($_GET['completed']) ? stripslashes(htmlspecialchars($_GET['completed'] - 1)) : null;
  $search = get_data('search');
  $colomIndex = get_data('colomIndex');
  $direction = get_data('direction');

  // unexisting parameter check
  if (empty($evt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'NPK and EVT_ID parameter is required'));
    exit();
  }

  // get registered users
  $users = $evtContr->getRegisteredParticipants($evt_id, $c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $approved, $completed, $search, $colomIndex, $direction);

  // response
  if ($users === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($users);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_REGISTER_USERS) === 0) {
  // require events controller and init
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $evtContr = new EventController();

  // get parameters
  $users = post_datas('users');
  $evt_id = get_data('evt_id');
  $check_type = get_data('check_type');

  // unexisting parameter check
  if (empty($users) || empty($evt_id) || empty($check_type)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'check_type, users, and EVT_ID is required'));
    exit();
  }

  // register event participant
  if (strcmp($check_type, "true") === 0) {
    foreach ($users as $npk) {
      // create new event participants id
      $ep_id = $evtContr->getLatestEpId() ? "EP" . str_pad(strval((int)substr($evtContr->getLatestEpId(), 2) + 1), 4, "0", STR_PAD_LEFT) : 'EP0001';

      $evtContr->registerUser($ep_id, $evt_id, $npk);
    }

    header("HTTP/1.1 201 Created");
  } else {
    foreach ($users as $npk) {
      $evtContr->unregisterUser($evt_id, $npk);
    }
  }

  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_ROLE) === 0) {
  $role = $_SESSION['RLS_ID'];
  $npk = $_SESSION['NPK'];

  if (empty($role)) {
    header("HTTP/1.1 404 Not Found");
  } else {
    header('Content-type: application/json');
    echo json_encode($role);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_DEPARTMENT) === 0) {
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $evtContr = new EventController();
  $departments = $evtContr->getDepartmentTO();

  if (empty($departments)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => $departments));
    exit();
  } else {
    header('Content-type: application/json');
    echo json_encode($departments);
  }
  exit();
} elseif (strcmp($action_type, ACTION_TYPE_GET_EVENT_STATUS) === 0) {
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  # code...
  $evtContr = new EventController();
  $evt_id = get_data('evt_id');
  $status = $evtContr->getEventStatus($evt_id);
  if (empty($status)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => $status));
    exit();
  } else {
    header('Content-type: application/json');
    echo json_encode($status);
  }
}

// for reference only
if (strpos($currUrl, '/events.php') !== false) {
  // require classes and init eventsController
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/events/events.class.php';
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  // get current page
  if (isset($_GET['page']) && !empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) {
    $currPage = $_GET['page'];
  }

  // get shown lists
  if (isset($_GET['lists_shown']) && !empty($_GET['lists_shown']) && filter_var($_GET['lists_shown'], FILTER_VALIDATE_INT)) {
    $lists_shown = $_GET['lists_shown'];
  }

  // get all trainings
  $trainings = array_merge($eventController->getEvents($search, $month, $year, $colomn, $direction), $trainings);

  // get all organizers
  $organizers = $eventController->getOrganizers();

  // get all locations
  $locations = $eventController->getLocations();

  // get all trainers
  $trainers = $eventController->getTrainers();

  // post query form
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = post_data("action");
    $evt_id = post_data("evt_id");

    // delete event if action is delete
    if (strcmp($action, "DELETE") == 0) {
      $eventController->deleteEvent($evt_id);

      // redirect to events page
      header("Location: ../events.php?error=none");
      exit();
    }
  }

  // get query form
  if ($_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_GET['organizer']) || isset($_GET['search']) || isset($_GET['start_date']) || isset($_GET['end_date']) || isset($_GET['start_time']) || isset($_GET['end_time']) || isset($_GET['training_status']))) {
    $search = !empty($_GET['search']) ? $_GET['search'] : null;
    $organizer = !empty($_GET['organizer']) ? $_GET['organizer'] : null;
    $start_date = !empty($_GET['start_date']) ? implode(explode('-', $_GET['start_date'])) : null;
    $end_date = !empty($_GET['end_date']) ? implode(explode('-', $_GET['end_date'])) : null;
    $start_time = !empty($_GET['start_time']) ? $_GET['start_time'] : null;
    $end_time = !empty($_GET['end_time']) ? $_GET['end_time'] : null;
    $training_status = isset($_GET['training_status']) && ($_GET['training_status'] || $_GET['training_status'] === '0') ? $_GET['training_status'] : null;
    $training_location = isset($_GET['training_location']) && !empty($_GET['training_location']) ? $_GET['training_location'] : null;
    $trainer = isset($_GET['trainer']) && !empty($_GET['trainer']) ? $_GET['trainer'] : null;
    $trainings = $eventController->filterEvents($currPage, $lists_shown, $search, $organizer, $start_date, $end_date, $start_time, $end_time, $training_status, $training_location, $trainer);
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
  }
} else if (strpos($currUrl, '/events/create.php') !== false) {
  // require classes and init eventsController
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/events/events.class.php';
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  // get all trainings
  $trainings = $eventController->getTrainings();

  // get all organizers
  $organizers = $eventController->getOrganizers();

  // get all locations
  $locations = $eventController->getLocations();

  // get all trainers
  $trainers = $eventController->getTrainers();

  if (isset($_GET['t_id']) && !empty($_GET['t_id'])) {
    // get training
    $training = $eventController->getTraining($_GET['t_id']);
    $t_id = $training['T_ID'];
    $description = $training['DESCRIPTION'];
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get post datas of other than dates and times
    $organizer = post_data('organizer');
    $new_organizer = post_data('new_organizer');
    $trainer = post_data('trainer');
    $new_trainer = post_data('new_trainer');
    $location = post_data('location');
    $new_location = post_data('new_location');

    // get post datas of dates and times
    $start_date = post_data('start_date');
    $end_date = post_data('end_date');

    $tmp_start_date = new DateTime($start_date);
    $tmp_end_date = new DateTime($end_date);
    $tmp = $tmp_start_date->diff($tmp_end_date);
    $days = $tmp->days + 1;

    $start_time = post_data('start_time');
    $end_time = post_data('end_time');

    $tmp_start_time = strtotime($start_time);
    $tmp_end_time = strtotime($end_time);

    $duration = abs($tmp_start_time - $tmp_end_time) / 3600;

    // require and init createEventsController
    require __DIR__ . '/../classes/events/create-contr.class.php';
    $createEventController = new CreateEventController($t_id, $organizer, $new_organizer, $trainer, $new_trainer, $location, $new_location, $start_date, $end_date, $days, $start_time, $end_time, $duration);

    // create new event
    $errors = array_merge($errors, $createEventController->createEvent());

    // redirect to events page
    if (!$errors['t_id'] && !$errors['organizer'] && !$errors['new_organizer'] && !$errors['trainer'] && !$errors['new_trainer'] && !$errors['location'] && !$errors['new_location'] && !$errors['description'] && !$errors['start_date'] && !$errors['end_date'] && !$errors['days'] && !$errors['start_time'] && !$errors['end_time'] && !$errors['duration']) {
      header("Location: ../events.php?error=none");
    }
  }
} else if (strpos($currUrl, '/events/update.php') !== false || strpos($currUrl, '/events/view.php') !== false) {
  // check if event id parameter exists
  if (!isset($_GET['evt_id']) || !$_GET['evt_id']) {
    echo "<h1 class='mt-5 fw-bold'>404 - Training event not found</h1><p class='fs-5'>Go back to <a href='../events.php'>Events</a> page</p>";
    exit();
  }
  $evt_id = $_GET['evt_id'];

  // require classes and init eventController
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/events/events.class.php';
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  // get current event
  $event = $eventController->getEvent($evt_id);

  // check if event exists
  if (
    !isset($event['EVT_ID']) || !$event['EVT_ID']
  ) {
    echo "<h1 class='mt-5 fw-bold'>404 - Training event not found</h1><p class='fs-5'>Go back to <a href='../events.php'>Events</a> page</p>";
    exit();
  }

  // get all organizers
  $organizers = $eventController->getOrganizers();

  // get all trainings
  $trainings = $eventController->getTrainings();

  // get all trainers
  $trainers = $eventController->getTrainers();

  // get all locations
  $locations = $eventController->getLocations();

  // update event
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_event'])) {
      // get post data
      $evt_id = post_data('evt_id');

      // delete event
      $eventController->deleteEvent($evt_id);

      // redirect to events page
      header("Location: ../events.php?error=none");
    }

    // get post datas
    $evt_id = post_data('evt_id');
    $training_name = post_data('training_name');
    $organizer = post_data('organizer');
    $new_organizer = post_data('new_organizer');
    $trainer = post_data('trainer');
    $new_trainer = post_data('new_trainer');
    $location = post_data('location');
    $new_location = post_data('new_location');

    $start_date = post_data('start_date');
    $end_date = post_data('end_date');

    $tmp_start_date = new DateTime($start_date);
    $tmp_end_date = new DateTime($end_date);
    $tmp = $tmp_start_date->diff($tmp_end_date);
    $days = $tmp->days + 1;

    $start_time = post_data('start_time');
    $end_time = post_data('end_time');

    $tmp_start_time = strtotime($start_time);
    $tmp_end_time = strtotime($end_time);

    $duration = abs($tmp_start_time - $tmp_end_time) / 3600;

    $activation = post_data('training_status');

    // require classes and init updateEventController
    require_once __DIR__ . '/../classes/events/update-contr.class.php';
    $updateEventController = new UpdateEventController($evt_id, $training_name, $organizer, $new_organizer, $trainer, $new_trainer, $location, $new_location, $start_date, $end_date, $days, $start_time, $end_time, $duration, $activation);

    // create news for participants when activation == 1
    if ($activation == 1) {
      // require and init news controller
      require_once __DIR__ . '/../classes/news/news-contr.class.php';
      $newsController = new NewsController();

      // get source npk
      $srcNpk = $_SESSION['NPK'];

      // create notifications
      $newsController->createTrainingNews($evt_id, $srcNpk);
    }

    // update event
    $errors = array_merge($errors, $updateEventController->updateEvent());

    // redirect to current page
    if (empty($errors['evt_id']) && empty($errors['training_name']) && empty($errors['organizer']) && empty($errors['new_organizer']) && empty($errors['trainer']) && empty($errors['new_trainer']) && empty($errors['location']) && empty($errors['new_location']) && empty($errors['start_date']) && empty($errors['end_date']) && empty($errors['days']) && empty($errors['start_time']) && empty($errors['end_time']) && empty($errors['duration']) && empty($errors['activation'])) {
      header("Location: ../events/update.php?evt_id=" . $evt_id . "&error=none");
    }
  }
} else if (strpos($currUrl, '/events/register.php') !== false || strpos($currUrl, '/events/register_participants.php') !== false) {
  // check if event id parameter exists
  if (!isset($_GET['evt_id']) || !$_GET['evt_id']) {
    echo "<h1 class='mt-5 fw-bold'>404 - Training event not found</h1><p class='fs-5'>Go back to <a href='../events.php'>Events</a> page</p>";
    exit();
  }
  $evt_id = $_GET['evt_id'];

  if (isset($_GET['page']) && !empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) {
    $currPage = get_data('page');
  }
  if (isset($_GET['lists_shown']) && !empty($_GET['lists_shown']) && filter_var($_GET['lists_shown'], FILTER_VALIDATE_INT)) {
    $lists_shown = get_data('lists_shown');
  }

  // require classes and init eventController
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/events/events.class.php';
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  // get all departments
  $departments = $eventController->getDepartments($evt_id);

  // get all companies
  $companies = $eventController->getCompanies($evt_id);

  // get all sections
  $sections = $eventController->getSections($evt_id);

  // get all subsections
  $subsections = $eventController->getSubsections($evt_id);

  // get all grades
  $grades = $eventController->getGrades($evt_id);

  // get all genders
  $genders = $eventController->getGenders($evt_id);

  // get current event
  $event = $eventController->getEvent($evt_id);

  // check if event exists
  if (
    !isset($event['EVT_ID']) || !$event['EVT_ID']
  ) {
    echo "<h1 class='mt-5 fw-bold'>404 - Training event not found</h1><p class='fs-5'>Go back to <a href='../events.php'>Events</a> page</p>";
    exit();
  }

  // get registered users
  $registered_users = $eventController->getRegisteredUsers($currPage, $lists_shown, $evt_id);

  // get all organizers
  // $organizers = $eventController->getOrganizers();

  // query form
  if ($_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_GET['company']) || isset($_GET['department']) || isset($_GET['section']) || isset($_GET['subsection']) || isset($_GET['grade']) || isset($_GET['gender']) || isset($_GET['search']))) {
    $company = get_data('company');
    $department = get_data('department');
    $section = get_data('section');
    $subsection = get_data('subsection');
    $grade = get_data('grade');
    $gender = get_data('gender');
    $search = get_data('search');
    $registered_users = $eventController->getFilteredUsers($currPage, $lists_shown, $evt_id, $company, $department, $section, $subsection, $grade, $gender, $search);
  }

  // unregister participants
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get user datas
    $registered_users_npk = $_POST['registered_users'];
    $evt_id = $_GET['evt_id'];

    // require classes init registerParticipantController
    require_once __DIR__ . '/../classes/events/unregis-contr.class.php';
    $unRegisController = new UnregisterParticipantController($evt_id, $registered_users_npk);

    // register participants
    $errors = array_merge($errors, $unRegisController->unRegisterParticipants());

    // redirect to events/register.php
    if (empty($errors['evt_id'])) {
      header('Location: ../events/register.php?evt_id=' . $evt_id . '&error=none');
    }
  }

  // register new participants
  if (strpos($currUrl, '/events/register_participants.php') !== false) {
    // get parameters
    if (isset($_GET['page']) && !empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) {
      $currPage = get_data('page');
    }
    if (isset($_GET['lists_shown']) && !empty($_GET['lists_shown']) && filter_var($_GET['lists_shown'], FILTER_VALIDATE_INT)) {
      $lists_shown = get_data('lists_shown');
    }

    // get all employees
    $employees = $eventController->getEmployees($currPage, $lists_shown);

    // get all registered users NPK
    $registered_users_npk = array();

    foreach ($registered_users as $user) {
      array_push($registered_users_npk, $user['NPK']);
    }

    // get all departments
    $departments = $eventController->getUsersDepartments();

    // get all companies
    $companies = $eventController->getUsersCompanies();

    // get all sections
    $sections = $eventController->getUsersSections();

    // get all subsections
    $subsections = $eventController->getUsersSubsections();

    // get all grades
    $grades = $eventController->getUsersGrades();

    // get all genders
    $genders = $eventController->getUsersGenders();

    // query logics
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_GET['company']) || isset($_GET['department']) || isset($_GET['section']) || isset($_GET['subsection']) || isset($_GET['grade']) || isset($_GET['gender']) || isset($_GET['search']))) {
      $company = get_data('company');
      $department = get_data('department');
      $section = get_data('section');
      $subsection = get_data('subsection');
      $grade = get_data('grade');
      $gender = get_data('gender');
      $search = get_data('search');
      $employees = $eventController->getFilteredEmployees($currPage, $lists_shown, $company, $department, $section, $subsection, $grade, $gender, $search);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // get user datas
      $registered_users = $_POST['registered_users'];
      $evt_id = $_GET['evt_id'];

      // require classes init registerParticipantController
      require_once __DIR__ . '/../classes/events/regis-contr.class.php';
      $regisController = new RegisterParticipantController($evt_id, $registered_users);

      // register participants
      $errors = array_merge($errors, $regisController->regisParticipants());

      // redirect to events/register.php
      if (empty($errors['evt_id'])) {
        header('Location: ../events/register_participants.php?evt_id=' . $evt_id . '&error=none');
      }
    }
  }
} else if (strpos($currUrl, '/events/approve.php') !== false) {
  // check if event id parameter exists
  if (!isset($_GET['evt_id']) || !$_GET['evt_id']) {
    echo "<h1 class='mt-5 fw-bold'>404 - Training event not found</h1><p class='fs-5'>Go back to <a href='../events.php'>Events</a> page</p>";
    exit();
  }
  $evt_id = $_GET['evt_id'];
  if (isset($_GET['page']) && !empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) {
    $currPage = get_data('page');
  }
  if (isset($_GET['lists_shown']) && !empty($_GET['lists_shown']) && filter_var($_GET['lists_shown'], FILTER_VALIDATE_INT)) {
    $lists_shown = get_data('lists_shown');
  }

  // require classes and init eventController
  require_once __DIR__ . '/../classes/connection.class.php';
  require_once __DIR__ . '/../classes/events/events.class.php';
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  /// get all departments
  $departments = $eventController->getDepartments($evt_id);

  // get all companies
  $companies = $eventController->getCompanies($evt_id);

  // get all sections
  $sections = $eventController->getSections($evt_id);

  // get all subsections
  $subsections = $eventController->getSubsections($evt_id);

  // get all grades
  $grades = $eventController->getGrades($evt_id);

  // get all genders
  $genders = $eventController->getGenders($evt_id);

  // get all approvals
  $approvals = $eventController->getApprovals($evt_id);

  // get all completions
  $completions = $eventController->getCompletions($evt_id);

  // get current event
  $event = $eventController->getEvent($evt_id);

  // get registered users
  $registered_users = $eventController->getRegisteredUsers($currPage, $lists_shown, $evt_id);

  // check if event exists
  if (
    !isset($event['EVT_ID']) || !$event['EVT_ID']
  ) {
    echo "<h1 class='mt-5 fw-bold'>404 - Training event not found</h1><p class='fs-5'>Go back to <a href='../events.php'>Events</a> page</p>";
    exit();
  }

  // get all organizers
  // $organizers = $eventController->getOrganizers();

  // query form
  if (
    $_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_GET['company']) || isset($_GET['department']) || isset($_GET['section']) || isset($_GET['subsection']) || isset($_GET['grade']) || isset($_GET['gender']) || isset($_GET['search']) || isset($_GET['approval']) || isset($_GET['completion']))
  ) {
    $company = get_data('company');
    $department = get_data('department');
    $section = get_data('section');
    $subsection = get_data('subsection');
    $grade = get_data('grade');
    $gender = get_data('gender');
    $search = get_data('search');
    $approval = get_data('approval');
    $completion = get_data('completion');
    $registered_users = $eventController->getFilteredUsers($currPage, $lists_shown, $evt_id, $company, $department, $section, $subsection, $grade, $gender, $search);
  }

  // approve / disapprove participants
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registered_users'])) {
    // get user datas
    $registered_users_npk = $_POST['registered_users'];
    $evt_id = $_GET['evt_id'];

    // require classes init registerParticipantController
    require_once __DIR__ . '/../classes/events/approval-contr.class.php';
    $approvalController = new EventApprovalController($evt_id, $registered_users_npk);

    if (isset($_POST['approval_type']) && $_POST['approval_type'] == 1) {
      // approve
      $errors = array_merge($errors, $approvalController->approveParticipants());

      // create news if event is activated
      $tmp = $approvalController->checkEvtActivated($evt_id);
      if ($tmp['ACTIVATED']) {
        // require and init news controller
        require_once __DIR__ . '/../classes/news/news-contr.class.php';
        $newsController = new NewsController();

        // get source npk
        $srcNpk = $_SESSION['NPK'];

        // create notifications
        $newsController->createTrainingNews($evt_id, $srcNpk, $registered_users_npk);
      }
    } else if (isset($_POST['approval_type']) && $_POST['approval_type'] == 2) {
      // disapprove
      $errors = array_merge($errors, $approvalController->disapproveParticipants());
    }

    // redirect to events/register.php
    if (empty($errors['evt_id'])) {
      header('Location: ../events/approve.php?evt_id=' . $evt_id . '&error=none');
    }
  }
} else if (strcmp($action_type, ACTION_TYPE_GET_CONTENT) === 0) {
  // get t_id, m_id and tch_id
  $t_id = get_data('t_id');

  // require t_id param
  if (empty($t_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training ID parameter is required"));
    exit();
  }

  // require content controller and instantiate it
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // get subchapter contents
  $contents = $trainingsContentController->getTrainingSubchaptersContents($t_id);

  // JSON responses
  if ($contents === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($contents);
  }
  exit();
} else if ($action_type == ACTION_TYPE_GET_TRAINING) {
  $t_id = get_data('t_id');
  $training = $eventController->getTraining($t_id);

  if ($training) {
    echo json_encode([
      'duration_days' => $training['DURATION_DAYS'],
      'duration_hours' => $training['DURATION_HOURS']
    ]);
  } else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Training not found']);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_PARTICIPANT_EVENT) === 0) {
  // get dpt_id, m_id and dch_id
  $evt_id = get_data('evt_id');
  $npk = get_data('npk');

  if (empty($npk)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["error" => "NPK parameter is required"]);
    exit();
  }

  // Require event controller dan instantiate
  require_once __DIR__ . '/../classes/events/events-contr.class.php';
  $eventController = new EventController();

  // Ambil events berdasarkan NPK
  $events = $eventController->getParticipantEvents($npk);

  // JSON response
  if ($events === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode(["events" => $events]);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_UPLOAD_EVENTS) === 0) {
  // Check if 'events' is set in POST and is an array
  if (isset($_POST['events']) && is_array($_POST['events'])) {
    $events = $_POST['events']; // Expecting an array of events

    // require events controller and instantiate
    require_once __DIR__ . '/../classes/events/events-contr.class.php';
    $eventsContr = new EventController();

    $allErrors = [];
    foreach ($events as $event) {
      // Log the current event being processed
      error_log(print_r($event, true)); // Log the event data

      // Ensure each event has the required keys before accessing them
      if (isset(
        $event['evt_id'],
        $event['t_id'],
        $event['start_date'],
        $event['end_date'],
        $event['duration_days'],
        $event['duration_hours'],
        $event['activated'],
        $event['start_time'],
        $event['end_time'],
        $event['loc_id'],
        $event['org_id'],
        $event['ta_id'],
        $event['evt_to']
      )) {

        $errors = $eventsContr->createOldTrainingEvent(
          $event['evt_id'],
          $event['t_id'],
          $event['start_date'],
          $event['end_date'],
          $event['duration_days'],
          $event['duration_hours'],
          $event['activated'],
          $event['start_time'],
          $event['end_time'],
          $event['loc_id'],
          $event['org_id'],
          $event['ta_id'],
          $event['evt_to']
        );

        if (!empty($errors)) {
          $allErrors[] = $errors;
        }
      } else {
        // If any required key is missing, log which keys are missing
        $missingKeys = [];
        if (!isset($event['evt_id'])) $missingKeys[] = 'evt_id';
        if (!isset($event['t_id'])) $missingKeys[] = 't_id';
        if (!isset($event['start_date'])) $missingKeys[] = 'start_date';
        if (!isset($event['end_date'])) $missingKeys[] = 'end_date';
        if (!isset($event['duration_days'])) $missingKeys[] = 'duration_days';
        if (!isset($event['duration_hours'])) $missingKeys[] = 'duration_hours';
        if (!isset($event['activated'])) $missingKeys[] = 'activated';
        if (!isset($event['start_time'])) $missingKeys[] = 'start_time';
        if (!isset($event['end_time'])) $missingKeys[] = 'end_time';
        if (!isset($event['loc_id'])) $missingKeys[] = 'loc_id';
        if (!isset($event['org_id'])) $missingKeys[] = 'org_id';
        if (!isset($event['ta_id'])) $missingKeys[] = 'ta_id';
        if (!isset($event['evt_to'])) $missingKeys[] = 'evt_to';

        $allErrors[] = ['Error' => 'Missing required event data: ' . implode(', ', $missingKeys)];
      }
    }

    // json response
    if (empty($allErrors)) {
      header("HTTP/1.1 201 Created");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode($allErrors);
    }
  } else {
    // Handle the case where 'events' is not set or not an array
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(['Error' => 'No events data provided or data is not in the correct format.']);
  }
  exit();
}

function post_datas($field)
{
  $items = $_POST[$field];
  $filteredItems = array();
  foreach ($items as $item) {
    if (empty($item)) {
      return null;
    } else {
      array_push($filteredItems, stripslashes(htmlspecialchars($item)));
    }
  }
  return $filteredItems;
}

function post_data($field)
{
  $_POST[$field] = isset($_POST[$field]) && $_POST[$field] ? $_POST[$field] : false;
  return stripslashes(htmlspecialchars($_POST[$field]));
}

function get_data($field)
{
  return isset($_GET[$field]) && !empty($_GET[$field]) && $_GET[$field] != "null" ? stripslashes(htmlspecialchars($_GET[$field])) : null;
}
