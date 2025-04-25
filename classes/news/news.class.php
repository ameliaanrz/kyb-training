<?php

require_once __DIR__ . '/../connection.class.php';

class News extends Connection
{
  protected function createNewTrainingNews($nws_id, $evt_id, $nws_t_id, $description, $src_npk, $dst_npk, $curr_date)
  {
    // get latest news id
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("INSERT INTO news (NWS_ID, EVT_ID, NWS_T_ID, DESCRIPTION, SRC_NPK, DST_NPK, CREATE_DATE) VALUES (:nws_id, :evt_id, :nws_t_id, :description, :src_npk, :dst_npk, :create_date)");

    $stmt->bindValue('nws_id', $nws_id);
    $stmt->bindValue('evt_id', $evt_id);
    $stmt->bindValue('nws_t_id', $nws_t_id);
    $stmt->bindValue('description', $description);
    $stmt->bindValue('src_npk', $src_npk);
    $stmt->bindValue('dst_npk', $dst_npk);
    $stmt->bindValue('create_date', $curr_date);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $stmt = '';
  }

  protected function getLatestNewsId()
  {
    // get latest news id
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NWS_ID FROM news ORDER BY NWS_ID DESC LIMIT 1");

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result['NWS_ID'];
  }

  protected function getTrainingDetails($evt_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT trainings.TRAINING, organizers.ORGANIZER, evt.START_DATE, evt.END_DATE, locations.LOCATION FROM events AS evt INNER JOIN trainings ON trainings.T_ID = evt.T_ID INNER JOIN organizers ON organizers.ORG_ID = evt.ORG_ID INNER JOIN locations ON locations.LOC_ID = evt.LOC_ID WHERE evt.EVT_ID = :evt_id");

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

  protected function getAllApprovedParticipants($evt_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NPK FROM event_participants WHERE APPROVED = 1 AND EVT_ID = :evt_id");

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

  protected function findCurrNews($nws_t_id, $description, $src_npk, $dst_npk)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT NWS_ID FROM news WHERE NWS_T_ID = :nws_t_id AND DESCRIPTION LIKE CONCAT ('%', :description, '%') AND SRC_NPK = :src_npk AND DST_NPK = :dst_npk");

    $stmt->bindValue('nws_t_id', $nws_t_id);
    $stmt->bindValue('description', $description);
    $stmt->bindValue('src_npk', $src_npk);
    $stmt->bindValue('dst_npk', $dst_npk);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }
}
