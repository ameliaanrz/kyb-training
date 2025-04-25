<?php

session_start();
date_default_timezone_set("Asia/Jakarta");
setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian', 'ID');

$npk = $_SESSION['NPK'];
$type = '1';
$limit = 5;
$offset = 0;

// if not logged in, returns response code 403 (forbidden)
if (!isset($_SESSION['NPK'])) {
  header("HTTP/1.1 403 Forbidden");
  exit();
}

// all available action types
define('NOTIF_ACTION_TYPE_GET', '1');
define('NOTIF_ACTION_TYPE_CREATE', '2');
define('NOTIF_ACTION_TYPE_CLEAR', '3');
define('NOTIF_ACTION_TYPE_REGISTER', '4');
define('NOTIF_ACTION_TYPE_APPROVE', '5');
define('NOTIF_ACTION_TYPE_APPROVE_DEPT', '8');
define('NOTIF_ACTION_TYPE_GET_NOTIFTYPE', '6');
define('NOTIF_ACTION_TYPE_UPDATE_NOTIFTYPE', '7');
define('NOTIF_ACTION_TYPE_DELETE_NOTIF', '9');
define('NOTIF_ACTION_TYPE_GET_WHATSAPP', '10');
define('NOTIF_ACTION_TYPE_DISAPPROVE', '11');
define('NOTIF_ACTION_TYPE_GETBYID', '12');
define('NOTIF_ACTION_TYPE_SEND', '13');
define('NOTIF_ACTION_TYPE_GET_NO_HP', '14');
define('NOTIF_ACTION_TYPE_SEND_WHATSAPP', '15');

// get type parameter from url
if (isset($_GET['type'])) {
  $type = get_data('type');

  if (!in_array($type, array(NOTIF_ACTION_TYPE_SEND_WHATSAPP, NOTIF_ACTION_TYPE_GET_NO_HP, NOTIF_ACTION_TYPE_GETBYID, NOTIF_ACTION_TYPE_DISAPPROVE, NOTIF_ACTION_TYPE_GET, NOTIF_ACTION_TYPE_GET_WHATSAPP, NOTIF_ACTION_TYPE_DELETE_NOTIF, NOTIF_ACTION_TYPE_APPROVE_DEPT, NOTIF_ACTION_TYPE_CREATE, NOTIF_ACTION_TYPE_CLEAR, NOTIF_ACTION_TYPE_REGISTER, NOTIF_ACTION_TYPE_APPROVE, NOTIF_ACTION_TYPE_GET_NOTIFTYPE, NOTIF_ACTION_TYPE_UPDATE_NOTIFTYPE, NOTIF_ACTION_TYPE_SEND))) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Type parameter is invalid"));
    exit();
  }
}

if (isset($_GET['limit'])) {
  $limit = $_GET["limit"];
}

if (isset($_GET['offset'])) {
  $offset = $_GET['offset'];
}

// require notif controller
require_once __DIR__ . '/../classes/notifications/notif-contr.class.php';
require_once __DIR__ . '/../classes/notifications/otp-contr.class.php';
require_once __DIR__ . '/../classes/notifications/remind-contr.class.php';
require_once __DIR__ . '/../classes/events/events-contr.class.php';

// instantiate notif controller
$notifController = new NotificationsController();
//$otpController = new OtpController();
$remindController = new RemindController();
$eventController = new EventController();

// get notifications
if ($type == NOTIF_ACTION_TYPE_GET) {
  // get all notifications
  $notifs = $notifController->getUserNotifications($npk, $limit, $offset);

  // get notification count
  $notifsCount = $notifController->getUserNotifsCount($npk);

  // responses
  if (
    $notifs === false || $notifsCount === false
  ) {
    header("HTTP/1.1 204 No Content");
  } else {
    // else response the notifs
    header("Content-type: application/json");
    echo json_encode(array('notifs' => $notifs, 'notifs_count' => $notifsCount));
  }

  // create notification
} else if ($type == NOTIF_ACTION_TYPE_CREATE) {
  // get post datas
  $t_id = $_POST['t_id'];
  $evt_id = $_POST['evt_id'];
  if (empty($t_id) || empty($evt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => $evt_id));
    exit();
  }

  $training_name = $notifController->getTrainingName($t_id);
  if ($training_name === false) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => $training_name));
    exit();
  }
  $ntf_id = $notifController->getLatestNotifId();
  $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
  $src_npk = $npk;
  $getevent = $notifController->getEventById($evt_id);
  if ($getevent === false) {
    header("HTTP/1.1 204 No Content");
    header("Content-type: application/json");
    echo json_encode(array('message' => 'Event not Found'));
    exit();
  }
  $dpt_to = $getevent['EVT_TO'];
  $role = 'RLS02,RLS03';
  $dst_npks = $notifController->getKadepNpks($dpt_to, $role);
  if ($dst_npks === false) {
    header("HTTP/1.1 204 No Content");
    header("Content-type: application/json");
    echo json_encode(array('message' => $dst_npks));
    exit();
  }
  $ntf_t_id = 'NTFT01';
  $notifs = $notifController->getNotifTypeById($ntf_t_id);
  $create_date = date('Y-m-d');

  //OTP
  $nomor = '6281261662219';
  $desc = 'Training Akan Dilakasanakan tgl 12';
  $description = str_replace('$namaevent', $training_name, $notifs['NTF_DESC']);
  foreach ($dst_npks as $dst_npk) {
    $notifController->createNotification($ntf_id, $src_npk, $ntf_t_id, $create_date, $description, $dst_npk['NPK'], $evt_id);
    $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
    //$response=$notifController->SendWa($nomor,$desc);
  }
  echo json_encode($response);
} else if ($type == NOTIF_ACTION_TYPE_CLEAR) {
  // clear user notifications
  $notifController->clearUserNotifs($npk);

  // responses
  header("HTTP/1.1 204 No Content");
  header("Content-type: application/json");
  echo json_encode(array("success" => "User notifications are cleared"));
} else if ($type == NOTIF_ACTION_TYPE_DELETE_NOTIF) {
  $ntf_id = post_data('ntf_id');
  $notifController->deleteNotification($ntf_id);
  header("HTTP/1.1 201 Created");
  header("Content-type: application/json");
  echo json_encode(array("success" => 'Berhasil'));
} else if ($type == NOTIF_ACTION_TYPE_REGISTER || $type == NOTIF_ACTION_TYPE_APPROVE || $type == NOTIF_ACTION_TYPE_APPROVE_DEPT) {
  // get post datas
  $evt_id = post_data('evt_id');
  if (empty($evt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'EVT_ID is required'));
    exit();
  }
  $training_name = $notifController->getTrainingNameFromEvt($evt_id);
  if ($training_name === false) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'Invalid training'));
    exit();
  }
  $ntf_id = $notifController->getLatestNotifId();
  $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
  $src_npk = $npk;
  $ntf_t_id = '';
  $description = '';
  $dst_npks = array();
  if ($type == NOTIF_ACTION_TYPE_REGISTER) {
    $getevent = $notifController->getEventById($evt_id);
    $dpt_to = post_data('dpt_id');
    $role = 'RLS02';
    $dst_npks = $notifController->getKadepNpks($dpt_to, $role);
    if ($dst_npks === false) {
      header("HTTP/1.1 204 No Content");
      header("Content-type: application/json");
      echo json_encode(array('message' => 'No destination users is found'));
      exit();
    }
    $ntf_t_id = 'NTFT02';
    $notifs = $notifController->getNotifTypeById($ntf_t_id);
    $description = str_replace('$namaevent', $training_name, $notifs['NTF_DESC']);
  } else if ($type == NOTIF_ACTION_TYPE_APPROVE) {
    $getevent = $notifController->getEventById($evt_id);
    if ($getevent === false) {
      header("HTTP/1.1 204 No Content");
      header("Content-type: application/json");
      echo json_encode(array('message' => 'Event not Found'));
      exit();
    }
    $role = 'RLS02,RLS03';
    $dpt_to = $getevent['EVT_TO'];
    $dst_npks = $notifController->getKadepNpks($dpt_to, $role);
    if ($dst_npks === false) {
      header("HTTP/1.1 204 No Content");
      header("Content-type: application/json");
      echo json_encode(array('message' => $dst_npks));
      exit();
    }
    $ntf_t_id = 'NTFT03';
    $ntf_t_id_usr = 'NTFT06';
    $notifs = $notifController->getNotifTypeById($ntf_t_id);

    $description = str_replace('$namaevent', $training_name, $notifs['NTF_DESC']);
  } else if ($type == NOTIF_ACTION_TYPE_APPROVE_DEPT) {
    $getevent = $notifController->getEventById($evt_id);
    if ($getevent === false) {
      header("HTTP/1.1 204 No Content");
      header("Content-type: application/json");
      echo json_encode(array('message' => 'Event not Found'));
      exit();
    }
    $role = 'RLS03';
    $dpt_to = $getevent['EVT_TO'];
    $dst_npks = $notifController->getKadepNpks($dpt_to, $role);
    $dst_npks_hrd = $notifController->getHrdNpks();
    if ($dst_npks === false) {
      header("HTTP/1.1 204 No Content");
      header("Content-type: application/json");
      echo json_encode(array('message' => $dst_npks));
      exit();
    }
    $ntf_t_id = 'NTFT04';
    $ntf_t_id_hrd = 'NTFT02';
    $notifs = $notifController->getNotifTypeById($ntf_t_id);

    $description = str_replace('$namaevent', $training_name, $notifs['NTF_DESC']);
  }
  $create_date = date('Y-m-d');
  $nomor = '6281261662219';
  $desc = post_data('desc');
  // create notifications
  $response = "";
  // Check and process $dst_npks
  if (!empty($dst_npks)) {
    if (!is_array($dst_npks)) {
      $dst_npk = $dst_npks; // If it's not an array, treat it as a single item
      $notifController->createNotification($ntf_id, $src_npk, $ntf_t_id, $ntf_t_id_usr, $create_date, $description, $dst_npk['NPK'], $evt_id);
      $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
      //$response=$notifController->SendWa($nomor,$desc);
    } else {
      foreach ($dst_npks as $dst_npk) {
        $notifController->createNotification($ntf_id, $src_npk, $ntf_t_id, $ntf_t_id_usr, $create_date, $description, $dst_npk['NPK'], $evt_id);
        $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
        //$response=$notifController->SendWa($nomor,$desc);
      }
    }
  }

  // Check and process $dst_npks_hrd
  if (!empty($dst_npks_hrd)) {
    if (!is_array($dst_npks_hrd)) {
      $hrd = $dst_npks_hrd; // If it's not an array, treat it as a single item
      $notifController->createNotification($ntf_id, $src_npk, $ntf_t_id_hrd, $create_date, $description, $hrd['NPK'], $evt_id);
      $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
      //$response=$notifController->SendWa($nomor,$desc);
    } else {
      foreach ($dst_npks_hrd as $hrd) {
        $notifController->createNotification($ntf_id, $src_npk, $ntf_t_id_hrd, $create_date, $description, $hrd['NPK'], $evt_id);
        $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
        //$response=$notifController->SendWa($nomor,$desc);
      }
    }
  }


  // responses
  header("HTTP/1.1 201 Created");
  header("Content-type: application/json");
  echo json_encode(array("success" => "$response"));
} else if ($type == NOTIF_ACTION_TYPE_DISAPPROVE) {
  $evt_id = post_data('evt_id');
  $training_name = $notifController->getTrainingNameFromEvt($evt_id);
  if ($training_name === false) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array('error' => 'Invalid training'));
    exit();
  }
  $ntf_id = $notifController->getLatestNotifId();
  $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
  $src_npk = $npk;
  $ntf_t_id = '';
  $description = '';
  $dst_npks = array();
  $getevent = $notifController->getEventById($evt_id);
  if ($getevent === false) {
    header("HTTP/1.1 204 No Content");
    header("Content-type: application/json");
    echo json_encode(array('message' => 'Event not Found'));
    exit();
  }
  $role = 'RLS02,RLS03';
  $dpt_to = $getevent['EVT_TO'];
  $dst_npks = $notifController->getKadepNpks($dpt_to, $role);
  if ($dst_npks === false) {
    header("HTTP/1.1 204 No Content");
    header("Content-type: application/json");
    echo json_encode(array('message' => $dst_npks));
    exit();
  }
  $ntf_t_id = 'NTFT05';
  $notifs = $notifController->getNotifTypeById($ntf_t_id);
  $create_date = date('Y-m-d');
  //if role RLS_O2 == KADEPT
  //ELSE ROLE RLS_01 == ADMIN HRD
  $roles = '';
  if ($_SESSION['RLS_ID'] == 'RLS02') {
    $roles = 'KADEPT';
  } else if ($_SESSION['RLS_ID'] == 'RLS01') {
    $roles = 'ADMIN HRD';
  }
  $description = str_replace('$namaevent', $training_name, $notifs['NTF_DESC']);
  $description = str_replace('$role', $roles, $description);
  $nomor = '6281261662219';
  $desc = post_data('desc');
  $response = "";
  // Check and process $dst_npks
  if (!empty($dst_npks)) {
    if (!is_array($dst_npks)) {
      $dst_npk = $dst_npks; // If it's not 
      $notifController->createNotification($ntf_id, $src_npk, $ntf_t_id, $create_date, $description, $dst_npk['NPK'], $evt_id);
      $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
      //$response=$notifController->SendWa($nomor,$desc);
    } else {
      foreach ($dst_npks as $dst_npk) {
        $notifController->createNotification($ntf_id, $src_npk, $ntf_t_id, $create_date, $description, $dst_npk['NPK'], $evt_id);
        $ntf_id = $ntf_id !== false ? "NTF" . str_pad(strval((int)substr($ntf_id, 3) + 1), 4, "0", STR_PAD_LEFT) : "NTF0001";
        //$response=$notifController->SendWa($nomor,$desc);
      }
    }
  }
  header("HTTP/1.1 201 Created");
  header("Content-type: application/json");
  //echo json_encode(array("success" => "$response"));

} else if ($type == NOTIF_ACTION_TYPE_GETBYID) {
  $ntf_id = get_data('ntf_id');
  $notifs = $notifController->getNotifTypeById($ntf_id);
  header("HTTP/1.1 201 Created");
  header("Content-type: application/json");
  echo json_encode($notifs);
} elseif ($type == NOTIF_ACTION_TYPE_UPDATE_NOTIFTYPE) {
  $ntf_t_id = post_data('ntf_id');
  $ntf_desc = post_data('ntf_desc');
  $notifController->updateNotificationType($ntf_t_id, $ntf_desc);
  header("HTTP/1.1 201 Created");
  header("Content-type: application/json");
  echo json_encode(array("success" => 'Berhasil'));
} else if ($type == NOTIF_ACTION_TYPE_GET_NOTIFTYPE) {
  $notifType = $notifController->getAllNotifType();
  header("Content-type: application/json");
  echo json_encode($notifType);
} else if ($type == NOTIF_ACTION_TYPE_GET_WHATSAPP) {
  $evt_id = post_data('evt_id');
  $getevent = $notifController->getEventById($evt_id);

  if ($getevent === false) {
    header("HTTP/1.1 404 Not Found");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event not found"));
    exit();
  }
  $dpt_to = $getevent['EVT_TO'];
  $arry_dpt = array();

  if ($dpt_to == 'all') {
    // Set $dpt_to to null to indicate selecting all departments
    $dpt_to = null;
  } elseif (strpos($dpt_to, ',') === false) {
    // No comma, single department ID
    $arry_dpt[] = $dpt_to;
  } else {
    // Multiple department IDs
    $arry_dpt = explode(',', $dpt_to);
  }

  $dst_npks = array();

  if ($dpt_to === null) {
    // Select all departments
    $kadep_results = $notifController->getKadept(null);
    $picnpk_results = $notifController->getPICnpk(null);

    if (is_array($kadep_results)) {
      $dst_npks = array_merge($dst_npks, $kadep_results);
    }
    if (is_array($picnpk_results)) {
      $dst_npks = array_merge($dst_npks, $picnpk_results);
    }
  } else {
    // Select specific departments
    foreach ($arry_dpt as $dpt) {
      $kadep_results = $notifController->getKadept($dpt);
      $picnpk_results = $notifController->getPICnpk($dpt);

      if (is_array($kadep_results)) {
        $dst_npks = array_merge($dst_npks, $kadep_results);
      }
      if (is_array($picnpk_results)) {
        $dst_npks = array_merge($dst_npks, $picnpk_results);
      }
    }
  }

  if ($dst_npks == false) {
    header("HTTP/1.1 204 No Content");
    header("Content-type: application/json");
    echo json_encode(array("error" => "No destination users found"));
    exit();
  }
  $no = array();
  foreach ($dst_npks as $dst_npk) {
    $otp_results = $otpController->getOtps($dst_npk['NPK']);
    // Assuming getOtps returns an array of results, append them to $no
    if (is_array($otp_results)) {
      $no[] = $otp_results;
    }
  }
} else if ($type == NOTIF_ACTION_TYPE_SEND) {
  $evt_id = get_data('evt_id');
  $t_id = get_data('t_id');
  $loc_id = get_data('loc_id');
  $location = get_data('location');
  $training = get_data('training');
  $start_date = get_data('start_date');
  $end_date = get_data('end_date');
  $start_time = get_data('start_time');
  $end_time = get_data('end_time');

  $trainings = $eventController->getTrainings();
  $getevent = $eventController->getEvent($evt_id);

  if (empty($evt_id)) {
    header("HTTP/1.1 404 Not Found");
    exit();
  }

  if ($getevent === false) {
    header("HTTP/1.1 404 Not Found");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Event not found"));
    exit();
  }

  // Ambil daftar nomor HP berdasarkan evt_id
  $nomorList = $remindController->getNomorByNpk($evt_id);

  if (!$nomorList) {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["error" => "No matching phone numbers found."]);
    exit();
  }

  $getallEvent = $remindController->getEvents($evt_id, $training, $t_id, $loc_id, $location, $start_date, $end_date, $start_time, $end_time);

  if (empty($getallEvent)) {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["error" => "No event found"]);
    exit();
  }

  // Ambil data event pertama
  $eventData = $getallEvent[0];

  $eventName = $eventData['TRAINING'];
  $startDate = $eventData['START_DATE'];
  $endDate = $eventData['END_DATE'];
  $startTime = $eventData['START_TIME'];
  $endTime = $eventData['END_TIME'];
  $location = $eventData['LOCATION'];

  $currentHour = date('H');
  if ($currentHour < 12) {
    $greeting = "Selamat Pagi";
  } elseif ($currentHour < 15) {
    $greeting = "Selamat Siang";
  } elseif ($currentHour < 18) {
    $greeting = "Selamat Sore";
  } else {
    $greeting = "Selamat Malam";
  }

  $startDateFormatted = strftime('%A, %d %B %Y', strtotime($startDate));
  $endDateFormatted = strftime('%A, %d %B %Y', strtotime($endDate));

  $startTimeFormatted = date('H:i', strtotime($startTime));
  $endTimeFormatted = date('H:i', strtotime($endTime));

  if ($startDate !== $endDate) {
    $tanggal_event = "$startDateFormatted - $endDateFormatted.\nWaktu: $startTimeFormatted - $endTimeFormatted\nTempat: $location";
  } else {
    $tanggal_event = "$startDateFormatted. Pukul $startTimeFormatted - $endTimeFormatted";
  }

  // Ambil daftar nama peserta berdasarkan evt_id
  $participantNames = $remindController->getParticipantName($evt_id);

  if (!empty($participantNames)) {
    $participantList = implode("\n- ", $participantNames); // Format daftar peserta
  } else {
    $participantList = "Tidak ada peserta yang terdaftar.";
  }

  // Format pesan
  $message = "$greeting, Kami menginformasikan bahwa pelatihan *$eventName* akan segera dimulai. Mohon untuk mempersiapkan diri dengan baik. Berikut detail pelaksanaannya:\nHari/Tanggal: $tanggal_event\n\nBerikut adalah daftar peserta yang dijadwalkan mengikuti pelatihan:\n- $participantList\n\nTerima kasih atas perhatian dan kerja samanya.";

  $send_date = date('Y-m-d H:i:s');
  $errors = [];

  foreach ($nomorList as $nomor) {
    $no_hp = $nomor['no_hp'];

    // Simpan nomor HP ke dalam tabel reminders
    $result = $remindController->createReminder($message, $no_hp, $send_date);
  }

  if (!empty($errors)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(["errors" => $errors]);
    exit();
  }

  // Jika berhasil
  header("HTTP/1.1 200 OK");
  echo json_encode(["message" => "Reminders successfully sent."]);
  exit();
} else if ($type == NOTIF_ACTION_TYPE_GET_NO_HP) {
  $npks = json_decode($_POST['npks'], true);
  $no_hp_list = [];

  // Fetch no_hp for each NPK
  foreach ($npks as $npk) {
    $no_hp = $notifController->getNoHpByNpk($npk);
    if ($no_hp) {
      $no_hp_list[] = ['no_hp' => $no_hp];
    }
  }

  // Return the no_hp list as JSON
  header("Content-type: application/json");
  echo json_encode($no_hp_list);
  exit();
} else if ($type == NOTIF_ACTION_TYPE_SEND_WHATSAPP) {
}

function get_data($field)
{
  return isset($_GET[$field]) && !empty($_GET[$field]) ? stripslashes(htmlspecialchars($_GET[$field])) : null;
}

function post_data($field)
{
  return isset($_POST[$field]) && !empty($_POST[$field]) ? stripslashes(htmlspecialchars($_POST[$field])) : null;
}
