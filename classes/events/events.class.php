<?php

require_once __DIR__ . '/../connection.class.php';

class Event extends Connection
{
  protected function getAllRegisteredParticipants($evt_id, $c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $approved, $completed, $search, $colomIndex = null, $direction = null)
  {
    // Valid columns with their respective table prefixes
    $validColumns = array(
      'NPK' => 'users.NPK',
      'NAME' => 'users.NAME',
      'COMPANY' => 'companies.COMPANY',
      'DEPARTMENT' => 'dpt.DEPARTMENT',
      'GRADE' => 'users.GRADE',
      'GENDER' => 'users.GENDER',
      'APPROVED' => 'ep.APPROVED',
      'APPROVED_DEPT' => 'ep.APPROVED_DEPT',
      'COMPLETED' => 'COMPLETED'
    );

    // Valid directions
    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'users.DPT_ID';
    $defaultDirection = 'asc';

    // Validate column and direction
    if (!array_key_exists($colomIndex, $validColumns)) {
      $colomIndex = $defaultColumn;
    } else {
      $colomIndex = $validColumns[$colomIndex];
    }

    if (!in_array(strtolower($direction), $validDirections)) {
      $direction = $defaultDirection;
    }

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT 
          users.NPK, 
          users.NAME, 
          companies.COMPANY, 
          dpt.DEPARTMENT, 
          users.GRADE, 
          users.GENDER, 
          ep.APPROVED,
          ep.APPROVED_DEPT, 
          IF(evt.END_DATE < CURRENT_DATE(), 1, 0) AS COMPLETED 
      FROM 
          event_participants AS ep 
          INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID 
          INNER JOIN users ON users.NPK = ep.NPK 
          INNER JOIN companies ON companies.C_ID = users.C_ID 
          INNER JOIN departments AS dpt ON dpt.DPT_ID = users.DPT_ID 
      WHERE 
          evt.EVT_ID = :evt_id
          AND (:c_id IS NULL OR users.C_ID = :c_id) 
          AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) 
          AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) 
          AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) 
          AND (:grade IS NULL OR users.GRADE = :grade) 
          AND (:gender IS NULL OR users.GENDER = :gender) 
          AND (:completed IS NULL OR IF(:completed = 1, evt.END_DATE < CURRENT_DATE(), evt.END_DATE >= CURRENT_DATE())) 
          AND (:approved IS NULL OR ep.APPROVED = :approved) 
          AND (:search IS NULL OR (users.NAME LIKE CONCAT('%', :search, '%') OR users.NPK LIKE CONCAT('%', :search, '%'))) 
      ORDER BY 
          $colomIndex $direction
      ");

    // Bind values to the statement
    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('c_id', $c_id);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('approved', $approved);
    $statement->bindValue('completed', $completed);
    $statement->bindValue('search', $search);

    // Execute the statement and handle errors
    if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      error_log("SQL Error: " . print_r($errorInfo, true));
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    return $result;
  }



  protected function unregisterCurrUser($evt_id, $npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("DELETE FROM event_participants WHERE EVT_ID = :evt_id AND NPK = :npk");

    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function registerCurrUser($ep_id, $evt_id, $npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("INSERT INTO event_participants (EP_ID, APPROVED, COMPLETED, EVT_ID, NPK) VALUES (:ep_id, 0, 0, :evt_id, :npk)");

    $statement->bindValue('ep_id', $ep_id);
    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function getAllUsers($c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $search, $evt_id, $colomIndex = null, $direction = null)
  {
    // Valid columns with their respective table prefixes
    $validColumns = array(
      'NPK' => 'users.NPK',
      'NAME' => 'users.NAME',
      'COMPANY' => 'companies.COMPANY',
      'DEPARTMENT' => 'departments.DEPARTMENT',
      'GENDER' => 'users.GENDER',
      'GRADE' => 'users.GRADE',
      'STATUS' => 'status'
    );

    // Valid directions
    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'users.NPK';
    $defaultDirection = 'asc';

    // Validate column and direction
    if (!array_key_exists($colomIndex, $validColumns)) {
      $colomIndex = $defaultColumn;
    } else {
      $colomIndex = $validColumns[$colomIndex];
    }

    if (!in_array(strtolower($direction), $validDirections)) {
      $direction = $defaultDirection;
    }

    $connection = new Connection();

    // Prepare the SQL statement with dynamic ORDER BY clause
    $statement = $connection->pdo->prepare("SELECT 
        users.NPK, 
        users.NAME, 
        companies.COMPANY, 
        departments.DEPARTMENT, 
        users.GENDER, 
        users.GRADE, 
        CASE 
            WHEN ep.NPK IS NOT NULL THEN 1 
            ELSE 0 
        END AS status
    FROM 
        users 
    INNER JOIN 
        companies ON companies.C_ID = users.C_ID 
    INNER JOIN 
        departments ON departments.DPT_ID = users.DPT_ID 
    LEFT JOIN 
        (SELECT NPK FROM event_participants WHERE EVT_ID = :evt_id) AS ep ON ep.NPK = users.NPK
    WHERE 
        (:c_id IS NULL OR users.C_ID = :c_id) 
        AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) 
        AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) 
        AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) 
        AND (:grade IS NULL OR users.GRADE = :grade) 
        AND (:gender IS NULL OR users.GENDER = :gender) 
        AND (:search IS NULL OR (users.NAME LIKE CONCAT('%', :search ,'%') OR users.NPK LIKE CONCAT('%', :search, '%')))
    ORDER BY 
        $colomIndex $direction");

    // Bind values to the statement
    $statement->bindValue('c_id', $c_id);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('search', $search);
    $statement->bindValue('evt_id', $evt_id);

    // Execute the statement and handle errors
    if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      error_log("SQL Error: " . print_r($errorInfo, true));
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getEventStatusById($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT 
    events.EVT_ID,
    events.EVT_TO, 
    trainings.T_ID, 
    events.START_DATE, 
    events.END_DATE, 
    events.START_TIME, 
    events.END_TIME, 
    events.ACTIVATED,
    CASE 
        WHEN events.START_DATE > CURRENT_DATE THEN 'Upcoming'
        WHEN events.START_DATE <= CURRENT_DATE AND events.END_DATE >= CURRENT_DATE THEN 'Running'
        WHEN events.END_DATE < CURRENT_DATE THEN 'Complete'
    END AS EVENT_STATUS
FROM 
    events 
INNER JOIN 
    trainings ON events.T_ID = trainings.T_ID 
INNER JOIN 
    organizers ON events.ORG_ID = organizers.ORG_ID
    where evt_id=:evt_id");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $status = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $status;
  }

  protected function getAllUserGenders()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT GENDER FROM users GROUP BY GENDER ORDER BY GENDER');

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllUserGrades()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT GRADE FROM users GROUP BY GRADE ORDER BY GRADE');

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllUserSubsections()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT subsections.SUB_SEC_ID, subsections.SUBSECTION FROM users INNER JOIN subsections ON subsections.SUB_SEC_ID = users.SUB_SEC_ID GROUP BY subsections.SUB_SEC_ID ORDER BY subsections.SUBSECTION');

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllUserSections()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT sections.SEC_ID, sections.SECTION FROM users INNER JOIN sections ON sections.SEC_ID = users.SEC_ID GROUP BY sections.SEC_ID ORDER BY sections.SECTION');

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllUserDepartments()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT departments.DPT_ID, departments.DEPARTMENT FROM users INNER JOIN departments ON departments.DPT_ID = users.DPT_ID GROUP BY departments.DPT_ID ORDER BY departments.DEPARTMENT');

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllUserCompanies()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT companies.C_ID, companies.COMPANY FROM users INNER JOIN companies ON companies.C_ID = users.C_ID GROUP BY companies.C_ID ORDER BY companies.COMPANY');

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getCurrTrainingByEvtId($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT trainings.T_ID, trainings.TRAINING, trainings.DESCRIPTION, loc.LOCATION, events.START_DATE,events.END_DATE,events.START_TIME,events.END_TIME FROM events INNER JOIN trainings ON trainings.T_ID = events.T_ID inner join locations as loc on loc.LOC_ID = events.LOC_ID 
 WHERE events.EVT_ID = :evt_id');

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $training = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $training;
  }

  protected function checkCurrEvtActivated($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT ACTIVATED FROM events WHERE EVT_ID = :evt_id");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $activated = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $activated;
  }

  protected function getEventById($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM events WHERE EVT_ID = :evt_id");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $trainings = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }

  protected function getCurrEventData($evt_id)
  {
    $connection = new Connection();
    $results = array();

    $statement = $connection->pdo->prepare("SELECT dpt.DPT_ID, dpt.DEPARTMENT FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK INNER JOIN departments AS dpt ON dpt.DPT_ID = users.DPT_ID WHERE ep.EVT_ID = :evt_id GROUP BY dpt.DPT_ID");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $results['departments'] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = $connection->pdo->prepare("SELECT sec.SEC_ID, sec.SECTION FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK INNER JOIN sections AS sec ON sec.SEC_ID = users.SEC_ID WHERE ep.EVT_ID = :evt_id GROUP BY sec.SEC_ID;");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $results['sections'] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = $connection->pdo->prepare("SELECT sub.SUB_SEC_ID, sub.SUBSECTION FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK INNER JOIN subsections AS sub ON sub.SUB_SEC_ID = users.SUB_SEC_ID WHERE ep.EVT_ID = :evt_id GROUP BY sub.SUB_SEC_ID;");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $results['subsections'] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = $connection->pdo->prepare("SELECT users.GRADE FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE ep.EVT_ID = :evt_id GROUP BY users.GRADE;");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $results['grades'] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = $connection->pdo->prepare("SELECT users.GENDER FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE ep.EVT_ID = :evt_id GROUP BY users.GENDER;");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $results['genders'] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = $connection->pdo->prepare("SELECT APPROVED FROM event_participants WHERE EVT_ID = :evt_id GROUP BY APPROVED;");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $results['approvals'] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = $connection->pdo->prepare("SELECT COMPLETED FROM event_participants WHERE EVT_ID = :evt_id GROUP BY COMPLETED;");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $results['completions'] = $statement->fetchAll(PDO::FETCH_ASSOC);

    $statement = null;

    return $results;
  }

  protected function getAllEvents($search, $month, $year, $colomn, $direction)
  {
    $validColumns = array(
      'EVT_ID' => 'events.EVT_ID',
      'T_ID' => 'trainings.T_ID',
      'TRAINING' => 'trainings.TRAINING',
      'ORGANIZER' => 'organizers.ORGANIZER',
      'START_DATE' => 'events.START_DATE',
      'END_DATE' => 'events.END_DATE',
      'START_TIME' => 'events.START_TIME',
      'END_TIME' => 'events.END_TIME',
      'ACTIVATED' => 'events.ACTIVATED'
    );

    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'EVT_ID';
    $defaultDirection = 'desc';

    if (!array_key_exists($colomn, $validColumns)) {
      $colomn = $defaultColumn; // default column
    } else {
      $colomn = $validColumns[$colomn];
    }
    if (!in_array(strtolower($direction), $validDirections)) {
      $direction = $defaultDirection; // default direction
    }

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT 
          events.EVT_ID,
          events.EVT_TO, 
          trainings.T_ID, 
          trainings.TRAINING,
          trainings.PURPOSE, 
          organizers.ORGANIZER, 
          events.START_DATE, 
          events.END_DATE, 
          events.DURATION_HOURS, 
          events.START_TIME, 
          events.END_TIME, 
          events.DURATION_DAYS, 
          events.ACTIVATED 
      FROM 
          events 
      INNER JOIN 
          trainings ON events.T_ID = trainings.T_ID 
      INNER JOIN 
          organizers ON events.ORG_ID = organizers.ORG_ID 
      WHERE 
          (:search IS NULL 
          OR (events.EVT_ID LIKE CONCAT('%', :search, '%') 
          OR trainings.TRAINING LIKE CONCAT('%', :search, '%'))) 
          AND (:month IS NULL 
          OR (MONTH(events.START_DATE) <= :month 
          AND MONTH(events.END_DATE) >= :month))
          AND (:year IS NULL 
          OR (YEAR(events.START_DATE) = :year 
          AND YEAR(events.END_DATE) = :year))
      ORDER BY 
          $colomn $direction
      ");

    $statement->bindValue('search', $search);
    $statement->bindValue('month', $month);
    $statement->bindValue('year', $year);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }


  protected function getAllTrainingParticipants($evt_id, $dpt_id, $approval, $sec_id, $sub_sec_id, $grade, $gender, $search, $colomIndex = null, $direction = null)
  {
    // Valid columns with their respective table prefixes
    $validColumns = array(
      'EP_ID' => 'ep.EP_ID',
      'NPK' => 'users.NPK',
      'GRADE' => 'users.GRADE',
      'GENDER' => 'users.GENDER',
      'NAME' => 'users.NAME',
      'DEPARTMENT' => 'dept.DEPARTMENT',
      'APPROVED' => 'ep.APPROVED',
      'APPROVED_DEPT' => 'ep.APPROVED_DEPT',
      'COMPLETED' => 'ep.COMPLETED'
    );

    // Valid directions
    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'users.NPK';
    $defaultDirection = 'asc';

    // Validate column and direction
    if (!array_key_exists($colomIndex, $validColumns)) {
      $colomIndex = $defaultColumn;
    } else {
      $colomIndex = $validColumns[$colomIndex];
    }

    if (!in_array(strtolower($direction), $validDirections)) {
      $direction = $defaultDirection;
    }

    $connection = new Connection();

    // Prepare the SQL statement with dynamic ORDER BY clause
    $statement = $connection->pdo->prepare("SELECT 
        ep.EP_ID, 
        users.NPK, 
        users.GRADE, 
        users.GENDER, 
        users.NAME, 
        dept.DEPARTMENT, 
        ep.APPROVED,
        ep.APPROVED_DEPT, 
        ep.COMPLETED 
    FROM 
        event_participants AS ep 
    INNER JOIN 
        users ON users.NPK = ep.NPK 
    INNER JOIN 
        departments AS dept ON dept.DPT_ID = users.DPT_ID 
    WHERE 
        ep.EVT_ID = :evt_id 
        AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) 
        AND (:approval IS NULL OR ep.APPROVED = :approval) 
        AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) 
        AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) 
        AND (:grade IS NULL OR users.GRADE = :grade) 
        AND (:gender IS NULL OR users.GENDER = :gender) 
        AND (:search IS NULL OR (users.NPK = :search OR users.NAME LIKE CONCAT('%', :search, '%'))) 
    GROUP BY 
        users.NPK 
    ORDER BY 
        $colomIndex $direction");

    // Bind values to the statement
    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('approval', $approval);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('search', $search);

    // Execute the statement and handle errors
    if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      error_log("SQL Error: " . print_r($errorInfo, true));
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $participants = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $participants;
  }

  protected function approveDept_AllParticipants($ids, $approval)
  {
    $connection = new Connection();

    foreach ($ids as $id) {
      $statement = $connection->pdo->prepare("UPDATE event_participants SET APPROVED_DEPT = :approved WHERE EP_ID = :id");

      $statement->bindValue('approved', $approval);
      $statement->bindValue('id', $id);

      if (!$statement->execute()) {
        $statement = null;
        header("HTTP/1.1 500 Internal Server Error");
        exit();
      }
    }

    $statement = null;
  }

  protected function approveAllParticipants($ids, $approval)
  {
    $connection = new Connection();

    foreach ($ids as $id) {
      $statement = $connection->pdo->prepare("UPDATE event_participants SET APPROVED = :approved WHERE EP_ID = :id");

      $statement->bindValue('approved', $approval);
      $statement->bindValue('id', $id);

      if (!$statement->execute()) {
        $statement = null;
        header("HTTP/1.1 500 Internal Server Error");
        exit();
      }
    }

    $statement = null;
  }

  protected function updateExpiredEvents()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("UPDATE events SET ACTIVATED = 0 WHERE END_DATE < CURDATE() AND ACTIVATED != 0");

    if (!$stmt->execute()) {
      $stmt = null;
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $stmt = null;
  }

  protected function getAllEmployees($page, $lists_shown)
  {
    $connection = new Connection();
    if ($page == -1) {
      $statement = $connection->pdo->prepare('SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, users.GENDER, users.GRADE FROM users INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN companies ON users.C_ID = companies.C_ID ORDER BY users.NPK');
    } else {
      $offset = ($page - 1) * $lists_shown;
      $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, users.GENDER, users.GRADE FROM users INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN companies ON users.C_ID = companies.C_ID ORDER BY users.NPK LIMIT $lists_shown OFFSET $offset");
    }

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $employees = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $employees;
  }

  protected function getDepartment()
  {
    try {
      $connection = new Connection();
      $pdo = $connection->pdo;

      $statement = $pdo->prepare('SELECT DPT_ID, DEPARTMENT FROM departments');

      if (!$statement->execute()) {
        throw new Exception('Statement execution failed');
      }

      $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
      $statement = null;
      $pdo = null; // Close the connection

      return $departments;
    } catch (PDOException $e) {
      error_log("Database error: " . $e->getMessage());
      return $e->getMessage();
      exit();
    } catch (Exception $e) {
      error_log("Database error: " . $e->getMessage());
      return $e->getMessage();
      exit();
    }
  }


  protected function getAllDepartments($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT dpt.DPT_ID, dpt.DEPARTMENT FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN departments AS dpt ON users.DPT_ID = dpt.DPT_ID WHERE (:evt_id IS NULL OR ep.EVT_ID = :evt_id) GROUP BY dpt.DPT_ID ORDER BY dpt.DEPARTMENT");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $departments;
  }

  protected function getAllCompanies($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT companies.C_ID, companies.COMPANY FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN companies ON users.C_ID = companies.C_ID WHERE (:evt_id IS NULL OR ep.EVT_ID = :evt_id) GROUP BY companies.C_ID ORDER BY companies.COMPANY");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $companies = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $companies;
  }

  protected function getAllUsersCompanies()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM companies");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $companies = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $companies;
  }

  protected function getAllSections($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT sections.SEC_ID, sections.SECTION FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN sections ON users.SEC_ID = sections.SEC_ID WHERE (:evt_id IS NULL OR ep.EVT_ID = :evt_id) GROUP BY sections.SEC_ID ORDER BY sections.SECTION");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $sections = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $sections;
  }

  protected function getAllUsersSections()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM sections");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $sections = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $sections;
  }

  protected function getAllSubsections($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT subsections.SUB_SEC_ID, subsections.SUBSECTION FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK INNER JOIN subsections ON users.SUB_SEC_ID = subsections.SUB_SEC_ID WHERE ep.EVT_ID = :evt_id GROUP BY users.SUB_SEC_ID ORDER BY subsections.SUBSECTION");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $subsections = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $subsections;
  }

  protected function getAllUsersSubsections()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM subsections");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $subsections = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $subsections;
  }

  protected function getAllUsersDepartment()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DPT_ID, DEPARTMENT FROM departments ORDER BY DEPARTMENT");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $departments;
  }

  protected function getAllGrades($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.GRADE FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE (:evt_id IS NULL OR ep.EVT_ID = :evt_id) GROUP BY users.GRADE ORDER BY users.GRADE");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $grades = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $grades;
  }

  protected function getAllUsersGrades()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT GRADE FROM users GROUP BY GRADE");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $grades = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $grades;
  }

  protected function getAllGenders($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT GENDER FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE (:evt_id IS NULL OR ep.EVT_ID = :evt_id) GROUP BY GENDER ORDER BY GENDER");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $genders = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $genders;
  }

  protected function getAllUsersGenders()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT GENDER FROM users GROUP BY GENDER");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $genders = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $genders;
  }

  protected function getAllCompletions($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT COMPLETED FROM event_participants AS ep WHERE (:evt_id IS NULL OR ep.EVT_ID = :evt_id) GROUP BY COMPLETED ORDER BY COMPLETED");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $completions = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $completions;
  }

  protected function getAllApprovals($evt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT APPROVED FROM event_participants AS ep WHERE (:evt_id IS NULL OR ep.EVT_ID = :evt_id) GROUP BY APPROVED ORDER BY APPROVED");

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?err=stmterror");
      exit();
    }

    $approvals = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $approvals;
  }

  protected function getAllTrainings()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT trainings.T_ID, trainings.TRAINING, trainings.DESCRIPTION FROM trainings ORDER BY trainings.TRAINING');

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }

  protected function getTrainingById($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT trainings.T_ID, trainings.TRAINING, trainings.DESCRIPTION FROM trainings WHERE T_ID=:t_id');
    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $training = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $training;
  }

  protected function getAllTrainerOrganizers($ta_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT org.ORG_ID, org.ORGANIZER FROM trainers INNER JOIN organizers AS org ON org.ORG_ID = trainers.ORG_ID WHERE (:ta_id IS NULL OR trainers.TA_ID = :ta_id) GROUP BY org.ORG_ID");

    $statement->bindValue('ta_id', $ta_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $organizers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $organizers;
  }

  public function getAllOrganizers()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM organizers");

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $organizers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $organizers;
  }

  protected function getCurrOrganizerByTrainer($ta_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT org.ORG_ID, org.ORGANIZER FROM trainers INNER JOIN organizers AS org ON org.ORG_ID = trainers.ORG_ID WHERE trainers.TA_ID = :ta_id");

    $statement->bindValue('ta_id', $ta_id);

    if (!$statement->execute()) {
      $statement = '';
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $organizers = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = '';
    return $organizers;
  }

  protected function getAllLocations()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT * FROM locations ORDER BY LOCATION');

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $organizers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $organizers;
  }

  protected function getAllTrainers($org_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT trainers.TA_ID, trainers.NAME FROM trainers INNER JOIN organizers AS org ON org.ORG_ID = trainers.ORG_ID WHERE (:org_id IS NULL OR org.ORG_ID = :org_id)');

    $statement->bindValue('org_id', $org_id);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $trainers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainers;
  }

  protected function filterAllEvents($page, $lists_shown, $search, $organizer, $start_date, $end_date, $start_time, $end_time, $training_status, $training_location, $trainer)
  {
    $offset = ($page - 1) * $lists_shown;

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT events.EVT_ID, trainings.T_ID, trainings.TRAINING, organizers.ORGANIZER, events.START_DATE, events.END_DATE, events.DURATION_DAYS, events.START_TIME, events.END_TIME, events.DURATION_HOURS, events.ACTIVATED FROM events INNER JOIN trainings ON events.T_ID = trainings.T_ID INNER JOIN organizers ON events.ORG_ID = organizers.ORG_ID WHERE (:search IS NULL OR (events.EVT_ID LIKE CONCAT('%', :search , '%')) OR (trainings.T_ID LIKE CONCAT('%', :search, '%') OR trainings.TRAINING LIKE CONCAT('%', :search, '%'))) AND (:organizer IS NULL OR events.ORG_ID=:organizer) AND ((:start_date IS NULL OR :start_date <= events.START_DATE) AND (:end_date IS NULL OR :end_date >= events.END_DATE)) AND ((:start_time IS NULL OR :start_time <= events.START_TIME) AND (:end_time IS NULL OR :end_time >= events.END_TIME)) AND (:training_status IS NULL OR events.ACTIVATED = :training_status) AND (:training_location IS NULL OR events.LOC_ID = :training_location) AND (:trainer IS NULL OR events.TA_ID = :trainer) ORDER BY trainings.T_ID LIMIT $lists_shown OFFSET $offset");
    // $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, departments.COMPANY_DEPARTMENT_SECTION, trainings.TRAINING, trainings.ORG_ID FROM users INNER JOIN departments ON users.CDS_ID = departments.CDS_ID INNER JOIN trainings ON users.T_ID = trainings.T_ID WHERE (:training IS NULL OR users.T_ID=:training) AND (:department IS NULL OR users.CDS_ID=:department) AND (:search IS NULL OR (users.NPK LIKE CONCAT('%', :search , '%') OR users.NAME LIKE CONCAT('%', :search , '%'))) ORDER BY users.NPK LIMIT 25 OFFSET $offset");
    $statement->bindValue('search', $search);
    $statement->bindValue('organizer', $organizer);
    $statement->bindValue('start_date', $start_date);
    $statement->bindValue('end_date', $end_date);
    $statement->bindValue('start_time', $start_time);
    $statement->bindValue('end_time', $end_time);
    $statement->bindValue('training_status', $training_status);
    $statement->bindValue('training_location', $training_location);
    $statement->bindValue('trainer', $trainer);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  private function getLatestOrganizerId()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT ORG_ID FROM organizers ORDER BY ORG_ID DESC LIMIT 1');

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result['ORG_ID'];
  }

  public function getLatestLocationId()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT LOC_ID FROM locations ORDER BY LOC_ID DESC LIMIT 1');

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result['LOC_ID'];
  }

  public function getLatestTrainerId()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT TA_ID FROM trainers ORDER BY TA_ID DESC LIMIT 1');

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /events.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result['TA_ID'];
  }


  protected function createNewEvent($t_id, $organizer, $trainer, $location, $start_date, $end_date, $days, $start_time, $end_time, $duration, $evt_to)
  {
    $connection = new Connection();

    $evt_to_str = implode(',', $evt_to);

    $statement = $connection->pdo->prepare('INSERT INTO events (EVT_ID, T_ID,EVT_TO, START_DATE, END_DATE, DURATION_DAYS, START_TIME, END_TIME, DURATION_HOURS, ORG_ID, TA_ID, LOC_ID) VALUES (:evt_id, :t_id,:evt_to, :start_date, :end_date, :days, :start_time, :end_time, :duration, :org_id, :ta_id, :loc_id)');

    $evt_id = $connection->getLatestEvtId() ? "EVT" . str_pad(strval((int)substr($connection->getLatestEvtId(), 3) + 1), 4, "0", STR_PAD_LEFT) : 'EVT0001';
    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('start_date', $start_date);
    $statement->bindValue('end_date', $end_date);
    $statement->bindValue('days', $days);
    $statement->bindValue('start_time', $start_time);
    $statement->bindValue('end_time', $end_time);
    $statement->bindValue('duration', $duration);
    $statement->bindValue('org_id', $organizer);
    $statement->bindValue('ta_id', $trainer);
    $statement->bindValue('loc_id', $location);
    $statement->bindValue('evt_to', $evt_to_str);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $statement = null;
  }


  protected function uploadOldEvent($evt_id, $t_id, $start_date, $end_date, $duration_days, $duration_hours, $activated,  $start_time, $end_time, $loc_id, $org_id, $ta_id, $evt_to)
  {
    $connection = new Connection();

    // Check if a record with the same EVT_ID already exists
    $stmt = $connection->pdo->prepare("SELECT COUNT(*) FROM events WHERE EVT_ID = :evt_id");
    $stmt->execute(['evt_id' => $evt_id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
      // If a record already exists, return an error message
      return ['error' => 'EVT_ID already exists: ' . $evt_id];
    }

    // If no record exists, proceed with the insert
    $statement = $connection->pdo->prepare('INSERT INTO events (EVT_ID, T_ID, START_DATE, END_DATE, DURATION_DAYS,  DURATION_HOURS, ACTIVATED, START_TIME, END_TIME, LOC_ID, ORG_ID, TA_ID, EVT_TO) VALUES (:evt_id, :t_id, :start_date, :end_date, :duration_days, :duration_hours, :activated, :start_time, :end_time, :loc_id, :org_id, :ta_id, :evt_to)');

    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('start_date', $start_date);
    $statement->bindValue('end_date', $end_date);
    $statement->bindValue('duration_days', $duration_days);
    $statement->bindValue('duration_hours', $duration_hours);
    $statement->bindValue('activated', $activated);
    $statement->bindValue('start_time', $start_time);
    $statement->bindValue('end_time', $end_time);
    $statement->bindValue('loc_id', $loc_id);
    $statement->bindValue('org_id', $org_id);
    $statement->bindValue('ta_id', $ta_id);
    $statement->bindValue('evt_to', $evt_to);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $statement = null;
  }

  protected function createOldOrganizer($org_id)
  {
    $connection = new Connection();

    $statement = $connection->pdo->prepare('INSERT INTO organizers (ORG_ID) VALUES (:org_id)');

    $statement->bindValue('org_id', $org_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }


  protected function createNewOrganizer($org_id, $organizer)
  {
    $connection = new Connection();

    $statement = $connection->pdo->prepare('INSERT INTO organizers (ORG_ID, ORGANIZER) VALUES (:org_id, :organizer)');

    $statement->bindValue('org_id', $org_id);
    $statement->bindValue('organizer', $organizer);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function createOldTrainer($ta_id)
  {
    $connection = new Connection();

    $statement = $connection->pdo->prepare('INSERT INTO trainers (TA_ID) VALUES (:ta_id)');

    $statement->bindValue('ta_id', $ta_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function createNewTrainer($org_id, $ta_id, $trainer)
  {
    $connection = new Connection();

    $statement = $connection->pdo->prepare('INSERT INTO trainers (TA_ID, NAME, ORG_ID) VALUES (:ta_id, :trainer, :org_id)');

    $statement->bindValue('ta_id', $ta_id);
    $statement->bindValue('trainer', $trainer);
    $statement->bindValue('org_id', $org_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function createOldLocation($loc_id)
  {
    $connection = new Connection();

    $statement = $connection->pdo->prepare('INSERT INTO locations (LOC_ID) VALUES (:loc_id)');

    $statement->bindValue('loc_id', $loc_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function createNewLocation($loc_id, $location)
  {
    $connection = new Connection();

    $statement = $connection->pdo->prepare('INSERT INTO locations (LOC_ID, LOCATION) VALUES (:loc_id, :location)');

    $statement->bindValue('loc_id', $loc_id);
    $statement->bindValue('location', $location);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function updateCurrEvent($evt_id, $evt_to, $activated, $t_id, $org_id, $ta_id, $loc_id, $start_date, $end_date, $start_time, $end_time)
  {
    $connection = new Connection();
    $evt_to_str = implode(',', $evt_to);
    $statement = $connection->pdo->prepare("UPDATE events SET T_ID =:t_id, EVT_TO=:evt_to, ACTIVATED = $activated, ORG_ID = :org_id, TA_ID = :ta_id, LOC_ID = :loc_id, START_DATE = :start_date, END_DATE = :end_date, START_TIME = :start_time, END_TIME = :end_time WHERE EVT_ID = :evt_id");

    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('evt_to', $evt_to_str);
    $statement->bindValue('org_id', $org_id);
    $statement->bindValue('ta_id', $ta_id);
    $statement->bindValue('loc_id', $loc_id);
    $statement->bindValue('start_date', $start_date);
    $statement->bindValue('end_date', $end_date);
    $statement->bindValue('start_time', $start_time);
    $statement->bindValue('end_time', $end_time);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function deleteCurrEvent($evt_id)
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("DELETE FROM events WHERE EVT_ID = :evt_id");

    $stmt->bindValue('evt_id', $evt_id);

    if (!$stmt->execute()) {
      $stmt = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $stmt = '';
  }

  protected function regisNewParticipants($evt_id, $registered_users)
  {
    $connection = new Connection();

    foreach ($registered_users as $npk) {
      $statement = $connection->pdo->prepare('INSERT INTO event_participants (EP_ID, NPK, EVT_ID) VALUES (:ep_id, :npk, :evt_id)');

      $ep_id = $connection->getLatestEpId() ? "EP" . str_pad(strval((int)substr($connection->getLatestEpId(), 2) + 1), 4, "0", STR_PAD_LEFT) : 'EP0001';
      $statement->bindValue('ep_id', $ep_id);
      $statement->bindValue('evt_id', $evt_id);
      $statement->bindValue('npk', $npk);

      if (!$statement->execute()) {
        $statement = null;
        header("Location: ../../users.php?error=stmterror");
        exit();
      }

      $statement = null;
    }
  }

  protected function unRegisterSomeParticipants($evt_id, $registered_users)
  {
    $connection = new Connection();

    foreach ($registered_users as $npk) {
      $statement = $connection->pdo->prepare('DELETE FROM event_participants WHERE NPK=:npk AND EVT_ID=:evt_id');

      $statement->bindValue('evt_id', $evt_id);
      $statement->bindValue('npk', $npk);

      if (!$statement->execute()) {
        $statement = null;
        header("Location: ../../users.php?error=stmterror");
        exit();
      }

      $statement = null;
    }
  }

  protected function getAllRegisteredUsers($page, $lists_shown, $evt_id)
  {
    $connection = new Connection();
    if ($page == -1) {
      $statement = $connection->pdo->prepare('SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, users.GRADE, users.GENDER, event_participants.APPROVED, event_participants.COMPLETED FROM event_participants INNER JOIN users ON event_participants.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN companies ON users.C_ID = companies.C_ID WHERE event_participants.EVT_ID = :evt_id ORDER BY users.DPT_ID');
    } else {
      $offset = ($page - 1) * $lists_shown;
      $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, users.GRADE, users.GENDER, event_participants.APPROVED, event_participants.COMPLETED FROM event_participants INNER JOIN users ON event_participants.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN companies ON users.C_ID = companies.C_ID WHERE event_participants.EVT_ID = :evt_id ORDER BY users.DPT_ID LIMIT $lists_shown OFFSET $offset");
    }

    $statement->bindValue('evt_id', $evt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $participants = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $participants;
  }

  protected function approveSomeParticipants($evt_id, $registered_users)
  {
    $connection = new Connection();

    foreach ($registered_users as $npk) {
      $statement = $connection->pdo->prepare('UPDATE event_participants SET APPROVED = 1 WHERE NPK=:npk AND EVT_ID=:evt_id');

      $statement->bindValue('evt_id', $evt_id);
      $statement->bindValue('npk', $npk);

      if (!$statement->execute()) {
        $statement = null;
        header("Location: ../../users.php?error=stmterror");
        exit();
      }

      $statement = null;
    }
  }

  protected function disapproveSomeParticipants($evt_id, $registered_users)
  {
    $connection = new Connection();

    foreach ($registered_users as $npk) {
      $statement = $connection->pdo->prepare('UPDATE event_participants SET APPROVED = 2 WHERE NPK=:npk AND EVT_ID=:evt_id');

      $statement->bindValue('evt_id', $evt_id);
      $statement->bindValue('npk', $npk);

      if (!$statement->execute()) {
        $statement = null;
        header("Location: ../../users.php?error=stmterror");
        exit();
      }

      $statement = null;
    }
  }

  protected function getAllFilteredUsers($currPage, $lists_shown, $evt_id, $company, $department, $section, $subsection, $grade, $gender, $search)
  {
    $offset = ($currPage - 1) * $lists_shown;

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, event_participants.APPROVED, event_participants.COMPLETED, users.GRADE, users.GENDER FROM event_participants INNER JOIN users ON event_participants.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN companies ON users.C_ID = companies.C_ID WHERE (:company IS NULL OR users.C_ID = :company) AND (:department IS NULL OR departments.DPT_ID = :department) AND (:section IS NULL OR users.SEC_ID = :section) AND (:subsection IS NULL OR users.SUB_SEC_ID = :subsection) AND (:grade IS NULL OR users.GRADE = :grade) AND (:gender IS NULL OR users.GENDER = :gender) AND (:search IS NULL OR (users.NPK LIKE CONCAT('%', :search, '%') OR (users.NAME LIKE CONCAT('%', :search, '%')))) AND event_participants.EVT_ID = :evt_id ORDER BY users.NPK LIMIT $lists_shown OFFSET $offset");

    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('search', $search);
    $statement->bindValue('company', $company);
    $statement->bindValue('department', $department);
    $statement->bindValue('section', $section);
    $statement->bindValue('subsection', $subsection);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllFilteredEmployees($currPage, $lists_shown, $company, $department, $section, $subsection, $grade, $gender, $search)
  {
    $offset = ($currPage - 1) * $lists_shown;

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, users.GRADE, users.GENDER FROM users INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN companies ON users.C_ID = companies.C_ID WHERE (:company IS NULL OR users.C_ID = :company) AND (:department IS NULL OR departments.DPT_ID = :department) AND (:section IS NULL OR users.SEC_ID = :section) AND (:subsection IS NULL OR users.SUB_SEC_ID = :subsection) AND (:grade IS NULL OR users.GRADE = :grade) AND (:gender IS NULL OR users.GENDER = :gender) AND (:search IS NULL OR (users.NPK LIKE CONCAT('%', :search, '%') OR (users.NAME LIKE CONCAT('%', :search, '%')))) ORDER BY users.NPK LIMIT $lists_shown OFFSET $offset");

    $statement->bindValue('search', $search);
    $statement->bindValue('company', $company);
    $statement->bindValue('department', $department);
    $statement->bindValue('section', $section);
    $statement->bindValue('subsection', $subsection);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getAllYears()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT DISTINCT YEAR(START_DATE) AS YEAR
FROM events
UNION
SELECT DISTINCT YEAR(END_DATE) AS YEAR
FROM events
ORDER BY YEAR;
");

    if (!$stmt->execute()) {
      $stmt = '';
      header('HTTP/1.1 500 Internal Server Error');
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  public function getAllMonths()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT MONTH(START_DATE) AS MONTH FROM events WHERE YEAR(START_DATE) <= YEAR(CURRENT_DATE()) AND YEAR(END_DATE) >= YEAR(CURRENT_DATE()) UNION SELECT MONTH(END_DATE) AS MONTH FROM events WHERE YEAR(START_DATE) <= YEAR(CURRENT_DATE()) AND YEAR(END_DATE) >= YEAR(CURRENT_DATE()) ORDER BY MONTH");

    if (!$stmt->execute()) {
      $stmt = '';
      header('HTTP/1.1 500 Internal Server Error');
      exit();
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = '';

    return $result;
  }

  public function getLatestOrgId()
  {
    $conn = new Connection();
    $stmt = $conn->pdo->prepare("SELECT ORG_ID FROM organizers ORDER BY ORG_ID DESC LIMIT 1");

    if (!$stmt->execute()) {
      $stmt = '';
      header('HTTP/1.1 500 Internal Server Error');
      exit();
    }

    $result = $stmt->fetch(PDO::FETCH_NUM);
    $stmt = '';

    return $result[0];
  }

  public function getAllParticipantEvents($npk)
  {
    $connection = new Connection();

    $sql = "SELECT e.EVT_ID, e.T_ID, e.START_DATE, e.END_DATE, e.ACTIVATED 
            FROM events e
            INNER JOIN event_participants ep ON e.EVT_ID = ep.EVT_ID
            WHERE ep.NPK = :npk";

    $statement = $connection->pdo->prepare($sql);
    $statement->bindValue(":npk", $npk);

    if (!$statement->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  public function getAllParticipants($evt_id)
  {
    $connection = new Connection();
    $sql = "SELECT NPK FROM event_participants WHERE EVT_ID = :evt_id";
    $statement = $connection->pdo->prepare($sql);
    $statement->bindValue(":evt_id", $evt_id);

    if (!$statement->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }
}
