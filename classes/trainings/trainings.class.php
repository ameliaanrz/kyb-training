<?php

require_once __DIR__ . '/../connection.class.php';

class Trainings extends Connection
{
  protected function changeTccId($tcc_id, $new_tcc_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("UPDATE training_core_contents SET TCC_ID = :new_tcc_id WHERE TCC_ID = :tcc_id");

    $stmt->bindValue('tcc_id', $tcc_id);
    $stmt->bindValue('new_tcc_id', $new_tcc_id);

    if (!$stmt->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      $stmt = null;
      exit();
    }

    $stmt = null;
  }

  protected function getPrevTCCID($tcc_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT TCC_ID FROM training_core_contents WHERE TCC_ID < :tcc_id ORDER BY TCC_ID DESC LIMIT 1");

    $stmt->bindValue('tcc_id', $tcc_id);

    if (!$stmt->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      $stmt = null;
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    return $result['TCC_ID'];
  }

  protected function getNextTCCID($tcc_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT TCC_ID FROM training_core_contents WHERE TCC_ID > :tcc_id ORDER BY TCC_ID ASC LIMIT 1");

    $stmt->bindValue('tcc_id', $tcc_id);

    if (!$stmt->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      $stmt = null;
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;
    return $result['TCC_ID'];
  }

  protected function getTrainingContent($tcc_id)
  {
    $connection = new Connection();

    $stmt = $connection->pdo->prepare("SELECT * FROM training_core_contents WHERE TCC_ID = :tcc_id");

    $stmt->bindValue('tcc_id', $tcc_id);

    if (!$stmt->execute()) {
      $stmt = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

    return $result;
  }

  protected function getAllTrainings($page = 1, $lists_shown = 10, $search = '', $colomIndex = null, $direction = null)
{
    $connection = new Connection();
    $offset = ($page - 1) * $lists_shown;

    // Query base statement
    $sql = "SELECT trainings.T_ID, trainings.STATUS, trainings.TRAINING 
            FROM trainings 
            WHERE (:search IS NULL OR trainings.TRAINING LIKE CONCAT('%', :search, '%') OR trainings.T_ID LIKE CONCAT('%', :search, '%'))";

    // Add sorting if colomIndex and direction are provided
    if ($colomIndex !== null && $direction !== null) {
        // Validate and sanitize column index
        $validColumns = array('T_ID', 'TRAINING', 'STATUS'); // List of valid columns for sorting
        $colomIndex = in_array($colomIndex, $validColumns) ? $colomIndex : 'T_ID'; // Default to 'T_ID' if invalid
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC'; // Sanitize direction

        // Append sorting to SQL query with trainings. prefix
        $sql .= " ORDER BY trainings.$colomIndex $direction";
    } else {
        // Default sorting if not provided
        $sql .= " ORDER BY trainings.T_ID";
    }

    // Prepare SQL statement
    $stmt = $connection->pdo->prepare($sql);
    $stmt->bindValue('search', $search);

    // Execute SQL statement
    if (!$stmt->execute()) {
        $stmt = null;
        header("HTTP/1.1 500 Internal Server Error");
        exit();
    }

    // Fetch results
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    return $trainings;
}


  protected function getCurrTraining($t_id)
  {
    $connection = new Connection();

    $stmt = $connection->pdo->prepare("SELECT * FROM trainings WHERE T_ID = :t_id");

    $stmt->bindValue('t_id', $t_id);

    if (!$stmt->execute()) {
      $stmt = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $training = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = null;

    return $training;
  }

  public function getAllTrainingsCount()
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("SELECT COUNT(T_ID) FROM trainings");

    if (!$stmt->execute()) {
      $stmt = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $trainingsCount = $stmt->fetch(PDO::FETCH_NUM);
    $stmt = null;

    return $trainingsCount[0];
  }

  public function getTrainingNameById($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TRAINING from TRAININGS WHERE T_ID = :t_id");

    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getTrainingById($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * from TRAININGS WHERE T_ID = :t_id");

    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getMaterialById($m_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID, TITLE, DESCRIPTION from materials WHERE M_ID = :m_id");

    $statement->bindValue("m_id", $m_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllTrainingMaterials($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID, TITLE, DESCRIPTION FROM materials WHERE T_ID = :t_id");

    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getAllTrainingChapters($m_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TCH_ID, TITLE, DESCRIPTION FROM training_chapters WHERE M_ID = :m_id");

    $statement->bindValue("m_id", $m_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getTrainingChapterById($tch_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TCH_ID, TITLE, DESCRIPTION FROM training_chapters WHERE TCH_ID = :tch_id");

    $statement->bindValue("tch_id", $tch_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getTrainingMaterialById($m_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID, TITLE, DESCRIPTION FROM materials WHERE M_ID = :m_id");

    $statement->bindValue("m_id", $m_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../../trainings.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getAllTrainingSubchaptersContents($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TCC_ID, CONTENT_TYPE, CONTENT , VR_STATUS FROM training_core_contents WHERE t_id = :t_id");

    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getAllOrganizers()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT * FROM organizers');

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings/create.php?error=stmterror");
      exit();
    }

    $organizers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $organizers;
  }

  public function filterTrainings($page, $lists_shown, $search)
  {
    $offset = ((int)$page - 1) * $lists_shown;

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT trainings.T_ID, trainings.TRAINING FROM trainings WHERE (:search IS NULL OR (trainings.TRAINING LIKE CONCAT('%', :search, '%') OR trainings.T_ID LIKE CONCAT('%', :search, '%'))) LIMIT $lists_shown OFFSET $offset");

    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainings;
  }

  protected function createNewTraining($t_id, $training_name, $description, $purpose, $company_purposes, $participant_purposes, $outline, $duration_days, $duration_hours, $participant)
{
    $connection = new Connection();

    // Insert into trainings table
    $statement_trainings = $connection->pdo->prepare("INSERT INTO trainings (T_ID, TRAINING, DESCRIPTION, PURPOSE, OUTLINE, DURATION_DAYS, DURATION_HOURS, PARTICIPANT) VALUES (:t_id, :training_name, :description, :purpose, :outline, :duration_days, :duration_hours, :participant)");
    $statement_trainings->bindValue(':t_id', $t_id);
    $statement_trainings->bindValue(':training_name', $training_name);
    $statement_trainings->bindValue(':description', $description);
    $statement_trainings->bindValue(':purpose', $purpose);
    $statement_trainings->bindValue(':outline', $outline);
    $statement_trainings->bindValue(':duration_days', $duration_days);
    $statement_trainings->bindValue(':duration_hours', $duration_hours);
    $statement_trainings->bindValue(':participant', $participant);

    if (!$statement_trainings->execute()) {
        header("HTTP/1.1 500 Internal Server Error");
        exit();
    }

    // Insert into company_benefits table
    foreach ($company_purposes as $benefit) {
        $raw_cb_id = $connection->getLatestCompanyBenefitsId();
        $raw_cb_id = empty($raw_cb_id) ? "CB0000" : $raw_cb_id;
        $cb_id = "CB" . str_pad(strval((int)substr($raw_cb_id, 2) + 1), 4, "0", STR_PAD_LEFT);

        $statement_cb = $connection->pdo->prepare('INSERT INTO company_benefits (CB_ID, BENEFIT, T_ID) VALUES (:cb_id, :benefit, :t_id)');
        $statement_cb->bindValue(':cb_id', $cb_id);
        $statement_cb->bindValue(':benefit', $benefit);
        $statement_cb->bindValue(':t_id', $t_id);

        if (!$statement_cb->execute()) {
            header("HTTP/1.1 500 Internal Server Error");
            exit();
        }
    }

    // Insert into participant_benefits table
    foreach ($participant_purposes as $benefit) {
        $raw_pb_id = $connection->getLatestParticipantBenefitsId();
        $raw_pb_id = empty($raw_pb_id) ? "PB0000" : $raw_pb_id;
        $pb_id = "PB" . str_pad(strval((int)substr($raw_pb_id, 2) + 1), 4, "0", STR_PAD_LEFT);

        $statement_pb = $connection->pdo->prepare('INSERT INTO participant_benefits (PB_ID, BENEFIT, T_ID) VALUES (:pb_id, :benefit, :t_id)');
        $statement_pb->bindValue(':pb_id', $pb_id);
        $statement_pb->bindValue(':benefit', $benefit);
        $statement_pb->bindValue(':t_id', $t_id);

        if (!$statement_pb->execute()) {
            header("HTTP/1.1 500 Internal Server Error");
            exit();
        }
    }
}


  protected function createNewSubchapter($tch_id, $sch_id, $subchapter_title)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO training_subchapters (TSCH_ID, TCH_ID, TITLE) VALUES (:sch_id, :tch_id, :subchapter_title)');

    $statement->bindValue("sch_id", $sch_id);
    $statement->bindValue("tch_id", $tch_id);
    $statement->bindValue("subchapter_title", $subchapter_title);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /trainings.php?error=stmterror");
      exit();
    }

    $statement = null;
  }

  protected function createNewCoreContent($tcc_id, $t_id, $content_type, $core_content)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO training_core_contents (TCC_ID, T_ID, CONTENT_TYPE, CONTENT) VALUES (:tcc_id, :t_id, :content_type, :core_content)');

    $statement->bindValue("tcc_id", $tcc_id);
    $statement->bindValue("t_id", $t_id);
    $statement->bindValue("content_type", $content_type);
    $statement->bindValue("core_content", $core_content);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrentTraining($t_id, $training_name, $description, $purpose, $outline, $duration_days, $duration_hours, $participant)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("UPDATE trainings SET TRAINING = :training_name, DESCRIPTION = :description, PURPOSE = :purpose, OUTLINE = :outline, DURATION_DAYS = :duration_days, DURATION_HOURS = :duration_hours, PARTICIPANT = :participant, STATUS = 1 WHERE T_ID = :t_id");

    $statement->bindValue('training_name', $training_name);
    $statement->bindValue('description', $description);
    $statement->bindValue('purpose', $purpose);
    $statement->bindValue('outline', $outline);
    $statement->bindValue('duration_days', $duration_days);
    $statement->bindValue('duration_hours', $duration_hours);
    $statement->bindValue('participant', $participant);
    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = '';
  }

  protected function removeAllPurposes($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("DELETE FROM participant_benefits WHERE T_ID = :t_id");

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Bad Request");
      exit();
    }

    $statement = $connection->pdo->prepare("DELETE FROM company_benefits WHERE T_ID = :t_id");

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Bad Request");
      exit();
    }

    $statement = '';
  }

  protected function createParticipantBenefits($t_id, $participant_purposes)
  {
    $connection = new Connection();

    foreach ($participant_purposes as $purpose) {
      $raw_pb_id = $connection->getLatestParticipantBenefitsId();
      $pb_id = empty($raw_pb_id) ? "PB0001" : "PB" . str_pad(strval((int)substr($raw_pb_id, 2) + 1), 4, "0", STR_PAD_LEFT);

      $statement = $connection->pdo->prepare("INSERT INTO participant_benefits (PB_ID, BENEFIT, T_ID) VALUES (:pb_id, :benefit, :t_id)");

      $statement->bindValue('pb_id', $pb_id);
      $statement->bindValue('benefit', $purpose);
      $statement->bindValue('t_id', $t_id);

      if (!$statement->execute()) {
        $statement = '';
        header("HTTP/1.1 500 Bad Request");
        exit();
      }
    }

    $statement = '';
  }

  protected function createCompanyBenefits($t_id, $company_purposes)
  {
    $connection = new Connection();

    foreach ($company_purposes as $purpose) {
      $raw_cb_id = $connection->getLatestCompanyBenefitsId();
      $cb_id = empty($raw_cb_id) ? "CB0001" : "CB" . str_pad(strval((int)substr($raw_cb_id, 2) + 1), 4, "0", STR_PAD_LEFT);

      $statement = $connection->pdo->prepare("INSERT INTO company_benefits (CB_ID, BENEFIT, T_ID) VALUES (:cb_id, :benefit, :t_id)");

      $statement->bindValue('cb_id', $cb_id);
      $statement->bindValue('benefit', $purpose);
      $statement->bindValue('t_id', $t_id);

      if (!$statement->execute()) {
        $statement = '';
        header("HTTP/1.1 500 Bad Request");
        exit();
      }
    }

    $statement = '';
  }

  protected function updateCompanyPurposes($company_purposes_update)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($company_purposes_update as $purpose) {
      $cb_id = explode(';', $purpose);
      $cb_id = $cb_id[0];
      $benefit = explode(';', $purpose);
      $benefit = $benefit[1];

      $statement = $connection->pdo->prepare("UPDATE company_benefits SET BENEFIT = :company_benefit WHERE CB_ID = :cb_id");

      $statement->bindValue('cb_id', $cb_id);
      $statement->bindValue('company_benefit', $benefit);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /trainings.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function updateParticipantPurposes($participant_purposes_update)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($participant_purposes_update as $purpose) {
      $pb_id = explode(';', $purpose);
      $pb_id = $pb_id[0];
      $benefit = explode(';', $purpose);
      $benefit = $benefit[1];

      $statement = $connection->pdo->prepare("UPDATE participant_benefits SET BENEFIT = :participant_benefit WHERE PB_ID = :pb_id");

      $statement->bindValue('pb_id', $pb_id);
      $statement->bindValue('participant_benefit', $benefit);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /trainings.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function updateMaterials($materials_update)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($materials_update as $material) {
      $m_id = explode(';', $material);
      $m_id = $m_id[0];
      $title = explode(';', $material);
      $title = $title[1];

      $statement = $connection->pdo->prepare("UPDATE materials SET TITLE = :title WHERE M_ID = :m_id");

      $statement->bindValue('m_id', $m_id);
      $statement->bindValue('title', $title);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /trainings.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function updateVRStats($t_id,$vrstats){
    $connection = new Connection();
    $statement = $connection->pdo->prepare("UPDATE training_core_contents SET VR_STATUS = :vrstats WHERE T_ID = :t_id");

    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('vrstats', $vrstats);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /trainings.php?error=stmterror");
      exit();
    }

    $statement = '';
  }

  protected function getPreviousCompanyPurposes($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT CB_ID FROM company_benefits WHERE T_ID = :t_id');

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /trainings.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getPreviousParticipantPurposes($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT PB_ID FROM participant_benefits WHERE T_ID = :t_id');

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /trainings.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function removeCompanyPurposes($prevPurposes, $purposes)
  {
    $connection = new Connection();
    $statement = '';
    $purposesId = array();

    foreach ($purposes as $purpose) {
      $cb_id = explode(';', $purpose);
      $cb_id = $cb_id[0];
      array_push($purposesId, $cb_id);
    }

    foreach ($prevPurposes as $prevPurpose) {
      $prev_cb_id = $prevPurpose['CB_ID'];

      if (!in_array($prev_cb_id, $purposesId)) {
        $statement = $connection->pdo->prepare("DELETE FROM company_benefits WHERE CB_ID = :cb_id");

        $statement->bindValue('cb_id', $prev_cb_id);

        if (!$statement->execute()) {
          $statement = '';
          header("Location: /trainings.php?error=stmterror");
          exit();
        }
      }
    }
    $statement = '';
  }

  protected function removeParticipantPurposes($prevPurposes, $purposes)
  {
    $connection = new Connection();
    $statement = '';
    $purposesId = array();

    foreach ($purposes as $purpose) {
      $pb_id = explode(';', $purpose);
      $pb_id = $pb_id[0];
      array_push($purposesId, $pb_id);
    }

    foreach ($prevPurposes as $prevPurpose) {
      $prev_pb_id = $prevPurpose['PB_ID'];

      if (!in_array($prev_pb_id, $purposesId)) {
        $statement = $connection->pdo->prepare("DELETE FROM participant_benefits WHERE PB_ID = :pb_id");

        $statement->bindValue('pb_id', $prev_pb_id);

        if (!$statement->execute()) {
          $statement = '';
          header("Location: /trainings.php?error=stmterror");
          exit();
        }
      }
    }
    $statement = '';
  }

  protected function addNewCompanyPurposes($t_id, $company_purposes)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($company_purposes as $purpose) {
      $raw_cb_id = $connection->getLatestCompanyBenefitsId();
      $cb_id = empty($raw_cb_id) ? "CB0000" : "CB" . str_pad(strval((int)substr($raw_cb_id, 2) + 1), 4, "0", STR_PAD_LEFT);

      $statement = $connection->pdo->prepare("INSERT INTO company_benefits (CB_ID, BENEFIT, T_ID) VALUES (:cb_id, :benefit, :t_id)");

      $statement->bindValue('cb_id', $cb_id);
      $statement->bindValue('benefit', $purpose);
      $statement->bindValue('t_id', $t_id);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /trainings.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function addNewParticipantPurposes($t_id, $participant_purposes)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($participant_purposes as $purpose) {
      $raw_pb_id = $connection->getLatestParticipantBenefitsId();
      $pb_id = empty($raw_pb_id) ? "PB0000" : "PB" . str_pad(strval((int)substr($raw_pb_id, 2) + 1), 4, "0", STR_PAD_LEFT);

      $statement = $connection->pdo->prepare("INSERT INTO participant_benefits (PB_ID, BENEFIT, T_ID) VALUES (:pb_id, :benefit, :t_id)");

      $statement->bindValue('pb_id', $pb_id);
      $statement->bindValue('benefit', $purpose);
      $statement->bindValue('t_id', $t_id);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /trainings.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function addNewMaterials($t_id, $materials)
  {
    $connection = new Connection();
    $statement = '';
    foreach ($materials as $title) {
      $raw_m_id = $connection->getLatestMaterialsId();
      $m_id = empty($raw_m_id) ? "M0000" : "M" . str_pad(strval((int)substr($raw_m_id, 1) + 1), 4, "0", STR_PAD_LEFT);

      $statement = $connection->pdo->prepare("INSERT INTO materials (M_ID, TITLE, T_ID) VALUES (:m_id, :title, :t_id)");

      $statement->bindValue('m_id', $m_id);
      $statement->bindValue('title', $title);
      $statement->bindValue('t_id', $t_id);

      if (!$statement->execute()) {
        $statement = '';
        header("Location: /trainings.php?error=stmterror");
        exit();
      }
    }
    $statement = '';
  }

  protected function createNewTrainingChapter($t_id, $m_id, $tch_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO training_chapters (TCH_ID, TITLE, DESCRIPTION, M_ID) VALUES (:tch_id, :title, :description, :m_id)');

    $statement->bindValue("tch_id", $tch_id);
    $statement->bindValue("m_id", $m_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /trainings/chapters.php?t_id=" . $t_id . "&m_id=" . $m_id . "&error=stmterror");
      exit();
    }

    $statement = null;
  }

  protected function createNewTrainingMaterial($t_id, $m_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('INSERT INTO materials (M_ID, T_ID, TITLE, DESCRIPTION) VALUES (:m_id, :t_id, :title, :description)');

    $statement->bindValue("m_id", $m_id);
    $statement->bindValue("t_id", $t_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrentTrainingChapter($tch_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE training_chapters SET TITLE = :title, DESCRIPTION = :description WHERE TCH_ID = :tch_id');

    $statement->bindValue("tch_id", $tch_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrentTrainingMaterial($m_id, $title, $description)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE materials SET TITLE = :title, DESCRIPTION = :description WHERE M_ID = :m_id');

    $statement->bindValue("m_id", $m_id);
    $statement->bindValue("title", $title);
    $statement->bindValue("description", $description);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrentTrainingSubchapter($sch_id, $subchapter_title)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE training_subchapters SET TITLE = :subchapter_title WHERE TSCH_ID = :sch_id');

    $statement->bindValue("sch_id", $sch_id);
    $statement->bindValue("subchapter_title", $subchapter_title);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrTrainingContent($tcc_id, $content_type, $core_content)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('UPDATE training_core_contents SET CONTENT = :core_content, CONTENT_TYPE = :content_type WHERE TCC_ID = :tcc_id');

    $statement->bindValue("tcc_id", $tcc_id);
    $statement->bindValue("content_type", $content_type);
    $statement->bindValue("core_content", $core_content);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function getLatestTrainingContentChapterId($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TCH_ID FROM training_chapters ORDER BY TCH_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getLatestTrainingContentMaterialId($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT M_ID FROM materials ORDER BY M_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Bad Request");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getCurrentLatestSubchapterId()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TSCH_ID FROM training_subchapters ORDER BY TSCH_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getCurrentLatestCoreContentId()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TCC_ID FROM training_core_contents ORDER BY TCC_ID DESC LIMIT 1");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getCurrentSubchapters($tch_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT TSCH_ID, TCH_ID, TITLE FROM training_subchapters WHERE TCH_ID = :tch_id ORDER BY TSCH_ID");
    $statement->bindValue('tch_id', $tch_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function deleteCurrContent($tcc_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("DELETE FROM training_core_contents WHERE TCC_ID = :tcc_id");

    $statement->bindValue('tcc_id', $tcc_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function deleteCurrTraining($t_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("UPDATE trainings SET STATUS = 0 WHERE T_ID = :t_id");
    $stmt->bindValue('t_id', $t_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function deleteCurrMaterial($m_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("DELETE FROM materials WHERE M_ID = :m_id");

    $stmt->bindValue('m_id', $m_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function deleteCurrChapter($tch_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("DELETE FROM training_chapters WHERE TCH_ID = :tch_id");

    $stmt->bindValue('tch_id', $tch_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function deleteCurrSubchapter($tsch_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("DELETE FROM training_subchapters WHERE TSCH_ID = :tsch_id");

    $stmt->bindValue('tsch_id', $tsch_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function getAllParticipantsPurposes($t_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("SELECT * FROM participant_benefits WHERE T_ID = :t_id");

    $stmt->bindValue('t_id', $t_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /trainings.php?error=stmterror");
      exit();
    }

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $results;
  }

  protected function getAllCompanyPurposes($t_id)
  {
    $connection = new Connection();
    $stmt = $connection->pdo->prepare("SELECT * FROM company_benefits WHERE T_ID = :t_id");

    $stmt->bindValue('t_id', $t_id);

    if (!$stmt->execute()) {
      $stmt = '';
      header("Location: /trainings.php?error=stmterror");
      exit();
    }

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $results;
  }
}
