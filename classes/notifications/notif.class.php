<?php

require_once __DIR__ . '/../connection.class.php';
require_once __DIR__ . '/../connectiondbhp.class.php';


class Notifications extends Connection
{
  protected function createNewNotification($ntf_id, $src_npk, $ntf_t_id, $create_date, $description, $dst_npk, $evt_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("INSERT INTO notifications (NTF_ID, SRC_NPK, NTF_T_ID, CREATE_DATE, DESCRIPTION, DST_NPK, EVT_ID) VALUES (:ntf_id, :src_npk, :ntf_t_id, :create_date, :description, :dst_npk, :evt_id)");

    $stmt->bindValue('ntf_id', $ntf_id);
    $stmt->bindValue('src_npk', $src_npk);
    $stmt->bindValue('ntf_t_id', $ntf_t_id);
    $stmt->bindValue('create_date', $create_date);
    $stmt->bindValue('description', $description);
    $stmt->bindValue('dst_npk', $dst_npk);
    $stmt->bindValue('evt_id', $evt_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $stmt = '';
  }

  protected function createNewNotificationWA($message, $no_hp, $send_date)
  {
    $send_date = date('Y-m-d H:i:s');
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("INSERT INTO reminders (MESSAGE, NO_HP, SEND_DATE) VALUES (:message, :no_hp, :send_date)");

    $stmt->bindValue('message', $message);
    $stmt->bindValue('no_hp', $no_hp);
    $stmt->bindValue('send_date', $send_date);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $stmt = '';
  }

  public function getAllApprovedParticipants($evt_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NPK FROM event_participants WHERE EVT_ID = :evt_id AND APPROVED = 1");

    $stmt->bindValue('evt_id', $evt_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }
  protected function getNotificationTypeAll()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT * FROM notification_types");

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getCurrNoHp($npk)
  {
    $conn = new Connection();
    $stmt = $conn->pdo3->prepare("SELECT no_hp FROM hp WHERE NPK = :npk");
    $stmt->bindValue(':npk', $npk);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result : null; // Return null if no result found
  }

  protected function getMessages()
  {
    $conn = new Connection();
    $stmt = $conn->pdo3->prepare("SELECT NTF_DESC FROM notification_types WHERE NTF_T_ID = 'MEMO01'");

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result : null; // Return null if no result found
  }
  protected function getMessageByType($ntf_t_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NTF_DESC FROM notification_types WHERE NTF_T_ID = :ntf_t_id");
    $stmt->bindValue('ntf_t_id', $ntf_t_id);

    if (!$stmt->execute()) {
      return false; // Handle error appropriately
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  protected function getPhoneNumberByNPK($npk)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT no_hp FROM hp WHERE NPK = :npk");
    $stmt->bindValue('npk', $npk);

    if (!$stmt->execute()) {
      return false; // Handle error appropriately
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  protected function getCurrNoWA($npk)
  {
    $conn = new Connection();
    $stmt = $conn->pdo3->prepare("SELECT no_hp FROM hp WHERE NPK = :npk");

    $stmt->bindValue('npk', $npk);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getNotificationTypeById($ntf_t_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT * FROM notification_types WHERE NTF_T_ID = :ntf_t_id");

    $stmt->bindValue('ntf_t_id', $ntf_t_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getLatestNotificationId()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NTF_ID FROM notifications ORDER BY NTF_ID DESC LIMIT 1");

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result['NTF_ID'];
  }

  protected function getCurrEvent($evt_id)
  {
    // get latest notification id
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT trainings.TRAINING,evt.EVT_TO, evt.START_DATE, locations.LOCATION FROM events AS evt INNER JOIN trainings ON evt.T_ID = trainings.T_ID INNER JOIN locations ON locations.LOC_ID = evt.LOC_ID WHERE EVT_ID = :evt_id");

    $stmt->bindValue('evt_id', $evt_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function notifExists($dstNpk, $description)
  {
    // get latest notification id
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NTF_ID FROM notifications WHERE DST_NPK = :npk AND DESCRIPTION LIKE CONCAT('%', :description, '%')");

    $stmt->bindValue('npk', $dstNpk);
    $stmt->bindValue('description', $description);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getAllUserNotifications($npk, $limit, $offset)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT notifs.NTF_ID, notifs.EVT_ID, notifs_types.NTF_TITLE, notifs.NTF_T_ID, notifs.CREATE_DATE, notifs.DESCRIPTION, notifs.IS_READ FROM notifications AS notifs INNER JOIN notification_types AS notifs_types ON notifs_types.NTF_T_ID = notifs.NTF_T_ID WHERE notifs.DST_NPK = :npk ORDER BY notifs.NTF_ID DESC LIMIT $limit OFFSET $offset");

    $stmt->bindValue('npk', $npk);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getCurrUserNotifsCount($npk)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT COUNT(NTF_ID) FROM notifications WHERE DST_NPK = :npk");

    $stmt->bindValue('npk', $npk);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_NUM);
    $stmt = '';

    return $result;
  }

  protected function getCurrLatestEvtId()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT EVT_ID FROM events ORDER BY EVT_ID DESC LIMIT 1;");

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getCurrLatestNotifId()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NTF_ID FROM notifications ORDER BY NTF_ID DESC LIMIT 1;");

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getCurrTrainingName($t_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT TRAINING FROM trainings WHERE T_ID = :t_id");

    $stmt->bindValue('t_id', $t_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result ? $result['TRAINING'] : null;
  }

  protected function getCurrNomor($npk)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT no_hp FROM hp WHERE npk = :npk");

    $stmt->bindValue('npk', $npk);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result ? $result['TRAINING'] : null;
  }

  protected function getCurrTrainingNameFromEvt($evt_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT trainings.TRAINING FROM events AS evt INNER JOIN trainings ON trainings.T_ID = evt.T_ID WHERE evt.EVT_ID = :evt_id");

    $stmt->bindValue('evt_id', $evt_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getAllHrd()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NPK FROM users WHERE RLS_ID = 'RLS01'");

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  protected function getCurrKadep($dpt_id)
  {
    $conn = new Connection();
    $sql = "SELECT NPK FROM users WHERE RLS_ID = 'RLS02'";
    if (!is_null($dpt_id)) {
      $sql .= " AND DPT_ID = :dpt_id";
    }
    $stmt = $conn->pdo->prepare($sql);

    if (!is_null($dpt_id)) {
      $stmt->bindValue('dpt_id', $dpt_id);
    }

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }


  protected function getCurrPIC($dpt_id)
  {
    $conn = new Connection();
    $sql = "SELECT NPK FROM users WHERE RLS_ID = 'RLS03'";
    if (!is_null($dpt_id)) {
      $sql .= " AND DPT_ID = :dpt_id";
    }
    $stmt = $conn->pdo->prepare($sql);

    if (!is_null($dpt_id)) {
      $stmt->bindValue('dpt_id', $dpt_id);
    }

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }


  protected function getAllNpks($dpt_id, $rls_id)
  {
    // Handle the default value for rls_id
    if (empty($rls_id)) {
      $rls_id = 'RLS02';
    }

    // Create the connection
    $conn = new Connection();

    // Check if $dpt_id is "all"
    if ($dpt_id == "all") {
      // Fetch all department IDs from the departments table
      $dept_sql = "SELECT DPT_ID FROM departments";
      $dept_stmt = $conn->pdo->query($dept_sql);
      $dpt_id_array = $dept_stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
      // Separate the dpt_id string into an array
      $dpt_id_array = explode(",", $dpt_id);
    }

    // Separate the rls_id string into an array
    $rls_id_array = explode(",", $rls_id);

    // Create placeholders for rls_id and dpt_id
    $rls_placeholders = implode(",", array_fill(0, count($rls_id_array), '?'));
    $dpt_placeholders = implode(",", array_fill(0, count($dpt_id_array), '?'));

    // Prepare SQL statement with multiple rls_id values
    $sql = "SELECT * FROM users WHERE rls_id IN ($rls_placeholders) AND DPT_ID IN ($dpt_placeholders)";

    // Prepare the statement
    $stmt = $conn->pdo->prepare($sql);

    // Bind the rls_id values
    $param_index = 1;
    foreach ($rls_id_array as $value) {
      $stmt->bindValue($param_index, $value);
      $param_index++;
    }

    // Bind the dpt_id values
    foreach ($dpt_id_array as $value) {
      $stmt->bindValue($param_index, $value);
      $param_index++;
    }

    // Execute the statement and fetch results
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results
    return $results;
  }



  protected function clearCurrUserNotifs($npk)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("DELETE FROM notifications WHERE DST_NPK = :npk");

    $stmt->bindValue('npk', $npk);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $stmt = '';
  }

  protected function deleteCurrNotification($ntf_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("DELETE FROM notifications WHERE NTF_ID = :ntf_id");

    $stmt->bindValue('ntf_id', $ntf_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $stmt = '';
  }

  protected function updateNotificationTypes($ntf_t_id, $ntf_desc)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("UPDATE notification_types SET NTF_DESC = :ntf_desc WHERE NTF_T_ID = :ntf_t_id");

    $stmt->bindValue('ntf_desc', $ntf_desc);
    $stmt->bindValue('ntf_t_id', $ntf_t_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $stmt = '';
  }

  protected function KirimNotifWhatsApp($token, $data)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $data["target"],
        'message' => $data["message"],
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response; // Return response from Fonnte API
  }
  //}


}
