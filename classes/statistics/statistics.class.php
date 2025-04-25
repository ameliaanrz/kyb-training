<?php

require_once __DIR__ . '/../connection.class.php';

class Statistics extends Connection
{
  public function getGender()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.GENDER FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK GROUP BY users.GENDER ORDER BY users.GENDER");

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = "";
    return $result;
  }

  public function getSubSections()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT sub.SUB_SEC_ID, sub.SUBSECTION FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK INNER JOIN subsections AS sub ON sub.SUB_SEC_ID = users.SUB_SEC_ID GROUP BY sub.SUB_SEC_ID ORDER BY sub.SUBSECTION");
    // $statement = $connection->pdo->prepare("SELECT SUB_SEC_ID, SUBSECTION FROM subsections");

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = "";
    return $result;
  }

  public function getSections()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT sec.SEC_ID, sec.SECTION FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK INNER JOIN sections AS sec ON sec.SEC_ID = users.SEC_ID GROUP BY sec.SEC_ID ORDER BY sec.SECTION");
    // $statement = $connection->pdo->prepare("SELECT SEC_ID, SECTION FROM sections");

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = "";
    return $result;
  }

  public function getDepartments()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT dpt.DPT_ID, dpt.DEPARTMENT FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK INNER JOIN departments AS dpt ON dpt.DPT_ID = users.DPT_ID GROUP BY dpt.DPT_ID ORDER BY dpt.DEPARTMENT");
    // $statement = $connection->pdo->prepare("SELECT DPT_ID, DEPARTMENT FROM departments");

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = "";
    return $result;
  }

  public function getGrades()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT GRADE FROM users GROUP BY GRADE");

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = "";
    return $result;
  }

  public function getTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT COUNT(DISTINCT evt.EVT_ID) AS EVT_TOTAL 
FROM events AS evt 
WHERE ((MONTH(evt.START_DATE) = MONTH(CURRENT_DATE()) AND YEAR(evt.START_DATE) = YEAR(CURRENT_DATE()))
   OR (MONTH(evt.END_DATE) = MONTH(CURRENT_DATE()) AND YEAR(evt.END_DATE) = YEAR(CURRENT_DATE())))
   AND (:dpt_id IS NULL OR 
        (FIND_IN_SET(SUBSTRING_INDEX(evt.EVT_TO, ',', 1), :dpt_id) > 0 OR
         FIND_IN_SET(SUBSTRING_INDEX(evt.evt_to, ',', -1), :dpt_id) > 0));");

    $statement->bindValue('dpt_id', $dpt_id);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getRunningTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    // SELECT COUNT(EVT_ID) FROM events WHERE START_DATE <= CURRENT_DATE() AND END_DATE >= CURRENT_DATE() AND ACTIVATED = 1
      $statement = $connection->pdo->prepare("SELECT COUNT(evs.EVT_TOTAL)
  FROM (
      SELECT COUNT(evt.EVT_ID) AS EVT_TOTAL
      FROM events AS evt
      left   JOIN event_participants AS ep ON ep.EVT_ID = evt.EVT_ID
      left   JOIN users ON users.NPK = ep.NPK
      WHERE (
              CURRENT_DATE() <= evt.END_DATE AND YEAR(END_DATE) = YEAR(CURRENT_DATE()) 
            )
        AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) 
        AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) 
        AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) 
        AND (:grade IS NULL OR users.GRADE = :grade) 
        AND (:gender IS NULL OR users.GENDER = :gender) 
      GROUP BY evt.EVT_ID
  ) AS evs;
  ");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getNextTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    // SELECT COUNT(EVT_ID) FROM events WHERE START_DATE > CURRENT_DATE() AND ACTIVATED = 1
    $statement = $connection->pdo->prepare("SELECT COUNT(*) AS total_events
      FROM events AS evt WHERE YEAR(evt.START_DATE) = YEAR(CURRENT_DATE()) 
        AND evt.START_DATE > CURRENT_DATE() 
        AND evt.ACTIVATED = 0 
        AND (:dpt_id IS NULL OR 
          (FIND_IN_SET(SUBSTRING_INDEX(evt.EVT_TO, ',', 1), :dpt_id) > 0 OR 
          FIND_IN_SET(SUBSTRING_INDEX(evt.EVT_TO, ',', -1), :dpt_id) > 0));");

    $statement->bindValue('dpt_id', $dpt_id);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getCompletedTrainingsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
{
    $connection = new Connection();
    
    $statement = $connection->pdo->prepare("SELECT COUNT(evs.EVT_TOTAL) 
        FROM (
            SELECT COUNT(DISTINCT evt.EVT_ID) AS EVT_TOTAL 
            FROM events AS evt 
            LEFT JOIN event_participants AS ep ON ep.EVT_ID = evt.EVT_ID 
            LEFT JOIN users ON users.NPK = ep.NPK 
            WHERE END_DATE < CURRENT_DATE() 
              AND YEAR(END_DATE) = YEAR(CURRENT_DATE())
              AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) 
              AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) 
              AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) 
              AND (:grade IS NULL OR users.GRADE = :grade) 
              AND (:gender IS NULL OR users.GENDER = :gender)
     GROUP BY evt.EVT_ID
        ) AS evs
    ");

    $statement->bindValue(':dpt_id', $dpt_id);
    $statement->bindValue(':sec_id', $sec_id);
    $statement->bindValue(':sub_sec_id', $sub_sec_id);
    $statement->bindValue(':grade', $grade);
    $statement->bindValue(':gender', $gender);

    if (!$statement->execute()) {
        header("HTTP/1.1 500 Internal Server Error");
        exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = null; // Closing statement explicitly (although PDO closes them automatically)
    return $result[0];
}


  public function getEmployeesCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT COUNT(NPK) FROM (SELECT users.NPK FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:grade IS NULL OR users.GRADE = :grade) AND (:gender IS NULL OR users.GENDER = :gender) GROUP BY NPK) AS unique_event_participants");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getMaleParticipantsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT COUNT(NPK) FROM (SELECT ep.NPK FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE users.GENDER = 'PRIA' AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:grade IS NULL OR users.GRADE = :grade) AND (:gender IS NULL OR users.GENDER = :gender) GROUP BY ep.NPK) AS unique_event_participants");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getFemaleParticipantsCount($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT COUNT(NPK) FROM (SELECT ep.NPK FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE users.GENDER = 'WANITA' AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:grade IS NULL OR users.GRADE = :grade) AND (:gender IS NULL OR users.GENDER = :gender) GROUP BY ep.NPK) AS unique_event_participants");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getTrainingsRunningTotal()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT COUNT(EVT_ID) FROM events WHERE ACTIVATED = 1 AND CURRENT_DATE =< END_DATE;");

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getEmployeesCompletedCount()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT COUNT(EP_ID) FROM event_participants WHERE COMPLETED = 1 AND CURRENT_DATE > evt.END_DATE;");

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getMaleTotalTrainingHours($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT SUM(evt.DURATION_HOURS * evt.DURATION_DAYS) AS HOURS
  FROM event_participants AS ep
INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID
INNER JOIN users ON users.NPK = ep.NPK
WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id)
  AND (:sec_id IS NULL OR users.SEC_ID = :sec_id)
  AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id)
  AND (:grade IS NULL OR users.GRADE = :grade)
  AND (:gender IS NULL OR users.GENDER = 'PRIA')
  AND evt.ACTIVATED = 1
  AND CURRENT_DATE > evt.END_DATE");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }

  public function getFemaleTotalTrainingHours($dpt_id, $sec_id, $sub_sec_id, $grade, $gender)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT SUM(evt.DURATION_HOURS * evt.DURATION_DAYS) AS HOURS
FROM event_participants AS ep
INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID
INNER JOIN users ON users.NPK = ep.NPK
WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id)
  AND (:sec_id IS NULL OR users.SEC_ID = :sec_id)
  AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id)
  AND (:grade IS NULL OR users.GRADE = :grade)
  AND (:gender IS NULL OR users.GENDER = 'WANITA')
  AND evt.ACTIVATED = 1
  AND CURRENT_DATE > evt.END_DATE");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = "";
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_NUM);
    $statement = "";
    return $result[0];
  }
}
