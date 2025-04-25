<?php

require_once __DIR__ . '/../connection.class.php';

class User extends Connection
{
  protected function getAllFilterNames($dpt_id, $t_id, $npk, $org_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT dpt.DEPARTMENT,trainings.T_ID, trainings.TRAINING, users.NAME, org.ORGANIZER FROM event_participants AS ep INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID INNER JOIN trainings ON trainings.T_ID = evt.T_ID INNER JOIN users ON users.NPK = ep.NPK INNER JOIN organizers AS org ON org.ORG_ID = evt.ORG_ID  INNER JOIN departments AS dpt ON dpt.DPT_ID = users.DPT_ID WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND  (:t_id IS NULL OR trainings.T_ID = :t_id) AND (:npk IS NULL OR ep.NPK = :npk) AND (:org_id IS NULL OR evt.ORG_ID = :org_id)");

    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('npk', $npk);
    $statement->bindValue('org_id', $org_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllEvents($npk, $dpt_id, $t_id, $org_id, $start_date, $end_date, $approved, $grade, $gender, $completed, $search, $colomIndex, $direction)
  {
    $validColumns = array(
      'NPK' => 'user.NPK',
      'NAME' => 'user.NAME',
      'DPT_ID' => 'user.DPT_ID',
      'DEPARTMENT' => 'dpt.DEPARTMENT',
      'GRADE' => 'user.GRADE',
      'EVT_ID' => 'evt.EVT_ID',
      'TRAINING' => 'tr.TRAINING',
      'ORGANIZER' => 'org.ORGANIZER',
      'START_DATE' => 'evt.START_DATE',
      'END_DATE' => 'evt.END_DATE',
      'APPROVED' => 'ep.APPROVED'
    );

    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'user.NPK';
    $defaultDirection = 'asc';

    if (!array_key_exists($colomIndex, $validColumns)) {
      $colomIndex = $defaultColumn; // default column
    } else {
      $colomIndex = $validColumns[$colomIndex];
    }
    if (!in_array(strtolower($direction), $validDirections)) {
      $direction = $defaultDirection; // default direction
    }

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT 
        user.NPK, 
        user.NAME, 
        user.DPT_ID, 
        dpt.DEPARTMENT, 
        user.GRADE, 
        evt.EVT_ID, 
        tr.TRAINING, 
        org.ORGANIZER, 
        evt.START_DATE, 
        evt.END_DATE, 
        ep.APPROVED 
    FROM 
        events AS evt 
    INNER JOIN 
        event_participants AS ep ON ep.EVT_ID = evt.EVT_ID  
    INNER JOIN 
        trainings AS tr ON tr.T_ID = evt.T_ID 
    INNER JOIN 
        organizers AS org ON org.ORG_ID = evt.ORG_ID 
    INNER JOIN 
        users AS user ON user.NPK = ep.NPK 
    INNER JOIN 
        departments AS dpt ON user.DPT_ID = dpt.DPT_ID 
    WHERE 
        (:dpt_id IS NULL OR user.DPT_ID = :dpt_id) 
        AND (:npk IS NULL OR ep.NPK = :npk)
        AND (:grade IS NULL OR user.GRADE = :grade) 
        AND (:gender IS NULL OR user.GENDER = :gender) 
        AND (:t_id IS NULL OR tr.T_ID = :t_id) 
        AND (:org_id IS NULL OR org.ORG_ID = :org_id) 
        AND (:start_date IS NULL OR evt.START_DATE >= :start_date) 
        AND (:end_date IS NULL OR evt.END_DATE <= :end_date) 
        AND (:approved IS NULL OR ep.APPROVED = :approved) 
        AND (:completed IS NULL OR 
             (CASE WHEN :completed = 1 THEN evt.END_DATE < CURDATE() 
                   ELSE evt.END_DATE >= CURDATE() END)) 
        AND (:search IS NULL OR 
             ep.NPK = :search OR 
             tr.TRAINING LIKE CONCAT('%', :search, '%') OR 
             evt.EVT_ID LIKE CONCAT('%', :search, '%'))
    ORDER BY 
        $colomIndex $direction
    ");

    $statement->bindValue('npk', $npk);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('org_id', $org_id);
    $statement->bindValue('start_date', $start_date);
    $statement->bindValue('end_date', $end_date);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('approved', $approved);
    $statement->bindValue('completed', $completed);
    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getPurposes($npk, $dpt_id, $t_id, $evt_id, $org_id, $description, $purpose, $start_date, $end_date, $approved, $approved_dept, $grade, $gender, $completed, $search, $colomIndex, $direction, $month, $year)
  {
    $validColumns = array(
      'NPK' => 'user.NPK',
      'NAME' => 'user.NAME',
      'T_ID' => 'tr.T_ID',
      'DPT_ID' => 'user.DPT_ID',
      'DEPARTMENT' => 'dpt.DEPARTMENT',
      'GRADE' => 'user.GRADE',
      'EVT_ID' => 'evt.EVT_ID',
      'TRAINING' => 'tr.TRAINING',
      'PURPOSE' => 'tr.PURPOSE',
      'DESCRIPTION' => 'tr.DESCRIPTION',
      'ORGANIZER' => 'org.ORGANIZER',
      'START_DATE' => 'evt.START_DATE',
      'END_DATE' => 'evt.END_DATE',
      'APPROVED' => 'ep.APPROVED',
      'APPROVED_DEPT' => 'ep.APPROVED_DEPT'
    );

    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'user.NPK';
    $defaultDirection = 'asc';

    if (!array_key_exists($colomIndex, $validColumns)) {
      $colomIndex = $defaultColumn; // default column
    } else {
      $colomIndex = $validColumns[$colomIndex];
    }
    if (!in_array(strtolower($direction), $validDirections)) {
      $direction = $defaultDirection; // default direction
    }

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT 
        user.NPK, 
        user.NAME, 
        user.DPT_ID, 
        dpt.DEPARTMENT, 
        user.GRADE, 
        evt.EVT_ID, 
        tr.T_ID,
        tr.TRAINING,
        tr.PURPOSE, 
        tr.DESCRIPTION, 
        org.ORGANIZER, 
        evt.START_DATE, 
        evt.END_DATE, 
        ep.APPROVED,
        ep.APPROVED_DEPT 
    FROM 
        events AS evt 
    INNER JOIN 
        event_participants AS ep ON ep.EVT_ID = evt.EVT_ID  
    INNER JOIN 
        trainings AS tr ON tr.T_ID = evt.T_ID 
    INNER JOIN 
        organizers AS org ON org.ORG_ID = evt.ORG_ID 
    INNER JOIN 
        users AS user ON user.NPK = ep.NPK 
    INNER JOIN 
        departments AS dpt ON user.DPT_ID = dpt.DPT_ID 
    WHERE 
        (:dpt_id IS NULL OR user.DPT_ID = :dpt_id) 
        AND (:npk IS NULL OR ep.NPK = :npk)
        AND (:grade IS NULL OR user.GRADE = :grade) 
        AND (:gender IS NULL OR user.GENDER = :gender) 
        AND (:purpose IS NULL OR tr.PURPOSE = :purpose) 
        AND (:description IS NULL OR tr.DESCRIPTION = :description) 
        AND (:t_id IS NULL OR tr.T_ID = :t_id) 
        AND (:evt_id IS NULL OR evt.EVT_ID = :evt_id) 
        AND (:org_id IS NULL OR org.ORG_ID = :org_id) 
        AND (:start_date IS NULL OR evt.START_DATE >= :start_date) 
        AND (:end_date IS NULL OR evt.END_DATE <= :end_date) 
        AND ep.APPROVED = 1
        AND ep.APPROVED_DEPT = 1
        AND (:completed IS NULL OR 
             (CASE WHEN :completed = 1 THEN evt.END_DATE < CURDATE() 
                   ELSE evt.END_DATE >= CURDATE() END)) 
        AND (:search IS NULL OR 
             ep.NPK = :search OR 
             tr.TRAINING LIKE CONCAT('%', :search, '%') OR 
             evt.EVT_ID LIKE CONCAT('%', :search, '%'))
             AND (:month IS NULL OR MONTH(evt.START_DATE) = :month OR MONTH(evt.END_DATE) = :month)
        AND (:year IS NULL OR YEAR(evt.START_DATE) = :year OR YEAR(evt.END_DATE) = :year)

      ORDER BY 
        $colomIndex $direction
    ");

    $statement->bindValue('npk', $npk);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('org_id', $org_id);
    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('start_date', $start_date);
    $statement->bindValue('purpose', $purpose);
    $statement->bindValue('description', $description);
    $statement->bindValue('end_date', $end_date);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('approved', $approved);
    $statement->bindValue('approved_dept', $approved_dept);
    $statement->bindValue('completed', $completed);
    $statement->bindValue('search', $search);
    $statement->bindValue('month', $month);
    $statement->bindValue('year', $year);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

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

  public function getAllYears()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT DISTINCT YEAR(START_DATE) AS year 
        FROM events 
        UNION 
        SELECT DISTINCT YEAR(END_DATE) AS year 
        FROM events 
        ORDER BY year DESC
    ");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  protected function getAllCompletions($npk) {}

  protected function getAllApprovals($npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllFilters()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, trainings.TRAINING, sections.SECTION, subsections.SUBSECTION, users.GRADE, users.GENDER FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID INNER JOIN sections ON users.SEC_ID = sections.SEC_ID INNER JOIN subsections ON users.SUB_SEC_ID = subsections.SUB_SEC_ID INNER JOIN companies ON companies.C_ID = users.C_ID GROUP BY users.NPK ORDER BY users.NPK");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllUsers($dpt_id, $sec_id, $sub_sec_id, $gender, $grade, $t_id, $search, $colomn, $direction)
  {
    $validColumns = array(
      'NPK' => 'users.NPK',
      'NAME' => 'users.NAME',
      'COMPANY' => 'companies.COMPANY',
      'DEPARTMENT' => 'departments.DEPARTMENT',
      'TRAINING' => 'trainings.TRAINING',
      'SECTION' => 'sections.SECTION',
      'SUBSECTION' => 'subsections.SUBSECTION',
      'GRADE' => 'users.GRADE',
      'GENDER' => 'users.GENDER'
    );

    $validDirections = array('asc', 'desc');

    // Set default values
    $defaultColumn = 'users.NPK';
    $defaultDirection = 'asc';

    // Validate column
    if (!array_key_exists($colomn, $validColumns)) {
      $colomn = $defaultColumn; // default column
    } else {
      $colomn = $validColumns[$colomn];
    }

    // Validate direction
    if (!in_array(strtolower($direction), $validDirections)) {
      $direction = $defaultDirection; // default direction
    } else {
      $direction = strtolower($direction);
    }

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT 
        users.NPK, 
        users.NAME, 
        companies.COMPANY, 
        departments.DEPARTMENT, 
        trainings.TRAINING, 
        sections.SECTION, 
        subsections.SUBSECTION, 
        users.GRADE, 
        users.GENDER 
    FROM 
        event_participants AS ep 
    INNER JOIN users ON ep.NPK = users.NPK 
    INNER JOIN departments ON users.DPT_ID = departments.DPT_ID 
    INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID 
    INNER JOIN trainings ON evt.T_ID = trainings.T_ID 
    LEFT JOIN sections ON users.SEC_ID = sections.SEC_ID 
    LEFT JOIN subsections ON users.SUB_SEC_ID = subsections.SUB_SEC_ID 
    INNER JOIN companies ON companies.C_ID = users.C_ID 
    WHERE 
        (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) 
        AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) 
        AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) 
        AND (:grade IS NULL OR users.GRADE = :grade) 
        AND (:gender IS NULL OR users.GENDER = :gender) 
        AND (:t_id IS NULL OR trainings.T_ID = :t_id) 
        AND (:search IS NULL OR (users.NPK LIKE CONCAT('%', :search, '%') OR users.NAME LIKE CONCAT('%', :search, '%')))
    ORDER BY 
        $colomn $direction
    ");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      header("HTTP/1.1 500 Internal Server Error");
      $statement = null;
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllSections($dpt_id, $sub_sec_id, $gender, $grade, $t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT sections.SEC_ID, sections.SECTION FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID INNER JOIN sections ON sections.SEC_ID = users.SEC_ID WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:grade IS NULL OR users.GRADE = :grade) AND (:t_id IS NULL OR evt.T_ID = :t_id) GROUP BY sections.SEC_ID ORDER BY sections.SECTION");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllSubsections($dpt_id, $sec_id, $gender, $grade, $t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT subsections.SUB_SEC_ID, subsections.SUBSECTION FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN subsections ON subsections.SUB_SEC_ID = users.SUB_SEC_ID INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID WHERE (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:grade IS NULL OR users.GRADE = :grade) AND (:t_id IS NULL OR evt.T_ID = :t_id) GROUP BY subsections.SUB_SEC_ID ORDER BY subsections.SUBSECTION");

    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAll() {}

  protected function getAllCompanies()
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT companies.C_ID, companies.COMPANY FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN companies ON companies.C_ID = users.C_ID GROUP BY companies.C_ID ORDER BY companies.COMPANY");

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    return $result;
  }

  protected function getAllGrades($dpt_id, $sec_id, $sub_sec_id, $gender, $t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.GRADE FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID  WHERE (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:t_id IS NULL OR evt.T_ID = :t_id) GROUP BY users.GRADE ORDER BY users.GRADE");

    $statement->bindValue("dpt_id", $dpt_id);
    $statement->bindValue("sec_id", $sec_id);
    $statement->bindValue("sub_sec_id", $sub_sec_id);
    $statement->bindValue("gender", $gender);
    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllGenders($dpt_id, $sec_id, $sub_sec_id, $grade, $t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.GENDER FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID WHERE (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:grade IS NULL OR users.GRADE = :grade) AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:t_id IS NULL OR evt.T_ID = :t_id) GROUP BY users.GENDER ORDER BY users.GENDER");

    $statement->bindValue("dpt_id", $dpt_id);
    $statement->bindValue("sec_id", $sec_id);
    $statement->bindValue("sub_sec_id", $sub_sec_id);
    $statement->bindValue("grade", $grade);
    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getUserById($npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, dpt.DPT_ID, dpt.DEPARTMENT, sec.SEC_ID, sec.SECTION, subsec.SUB_SEC_ID, subsec.SUBSECTION, users.GRADE, users.GENDER FROM users INNER JOIN departments AS dpt ON dpt.DPT_ID = users.DPT_ID LEFT JOIN sections AS sec ON sec.SEC_ID = users.SEC_ID LEFT JOIN  subsections AS subsec ON subsec.SUB_SEC_ID = users.SUB_SEC_ID WHERE users.NPK = :npk");

    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  public function getNameById($npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, dpt.DPT_ID, dpt.DEPARTMENT, sec.SEC_ID, sec.SECTION, subsec.SUB_SEC_ID, subsec.SUBSECTION, users.GRADE, users.GENDER FROM users INNER JOIN departments AS dpt ON dpt.DPT_ID = users.DPT_ID LEFT JOIN sections AS sec ON sec.SEC_ID = users.SEC_ID LEFT JOIN  subsections AS subsec ON subsec.SUB_SEC_ID = users.SUB_SEC_ID WHERE users.NPK = :npk");

    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  protected function getAllTrainings($npk, $dpt_id, $sec_id, $sub_sec_id, $gender, $grade, $org_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT trainings.T_ID, trainings.TRAINING FROM event_participants AS ep INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN users ON users.NPK = ep.NPK INNER JOIN trainings ON evt.T_ID = trainings.T_ID WHERE (:npk IS NULL OR ep.NPK = :npk) AND (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:grade IS NULL OR users.GRADE = :grade) AND (:dpt_id IS NULL OR users.DPT_ID = :dpt_id) AND (:org_id IS NULL OR evt.ORG_ID = :org_id) GROUP BY trainings.T_ID ORDER BY trainings.TRAINING');

    $statement->bindValue('npk', $npk);
    $statement->bindValue("dpt_id", $dpt_id);
    $statement->bindValue("sec_id", $sec_id);
    $statement->bindValue("sub_sec_id", $sub_sec_id);
    $statement->bindValue("gender", $gender);
    $statement->bindValue("grade", $grade);
    $statement->bindValue("org_id", $org_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }

  protected function getAllFilteredTrainings($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT trainings.T_ID, trainings.TRAINING FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID WHERE users.DPT_ID = :dpt_id GROUP BY trainings.T_ID ORDER BY trainings.TRAINING');
    $statement->bindValue('dpt_id', $dpt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }

  protected function getAllFilteredDepartments($training)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT departments.DPT_ID, departments.DEPARTMENT FROM event_participants AS ep INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN users ON ep.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID WHERE evt.T_ID = :t_id GROUP BY departments.DPT_ID ORDER BY departments.DEPARTMENT');
    $statement->bindValue('t_id', $training);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }

  protected function getTrainingById($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT T_ID, TRAINING FROM trainings WHERE T_ID = :t_id');
    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }

  protected function getDepartmentById($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT DPT_ID, DEPARTMENT FROM departments WHERE DPT_ID = :dpt_id');
    $statement->bindValue('dpt_id', $dpt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings.php?error=stmterror");
      exit();
    }

    $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $departments;
  }

  protected function filterAllTrainings($page, $lists_shown, $npk, $organizer, $search, $approval, $completion)
  {
    $offset = ($page - 1) * $lists_shown;
    $connection = new Connection();

    $statement = $connection->pdo->prepare("SELECT ep.EVT_ID, trainings.TRAINING, organizers.ORGANIZER, ep.APPROVED, ep.COMPLETED FROM event_participants AS ep INNER JOIN events as evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID INNER JOIN organizers ON evt.ORG_ID = organizers.ORG_ID WHERE (:organizer IS NULL OR organizers.ORG_ID = :organizer) AND (:search IS NULL OR (trainings.TRAINING LIKE CONCAT('%', :search , '%') OR trainings.T_ID LIKE CONCAT('%', :search , '%'))) AND ep.NPK = :npk AND (:approved IS NULL OR ep.APPROVED = :approved) AND (:completed IS NULL OR ep.COMPLETED = :completed) LIMIT $lists_shown OFFSET $offset");

    $statement->bindValue('npk', $npk);
    $statement->bindValue('organizer', $organizer);
    $statement->bindValue('search', $search);
    $statement->bindValue('approved', $approval);
    $statement->bindValue('completed', $completion);

    if (!$statement->execute()) {
      $statement = '';
      header("Location: ../trainings.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = '';
    return $trainings;
  }

  protected function getAllOrganizers($npk, $t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT org.ORG_ID, org.ORGANIZER FROM event_participants AS ep INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID INNER JOIN organizers AS org ON evt.ORG_ID = org.ORG_ID WHERE ep.NPK = :npk AND (:t_id IS NULL OR trainings.T_ID = :t_id) GROUP BY org.ORG_ID ORDER BY org.ORGANIZER');

    $statement->bindValue("npk", $npk);
    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $organizers = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $organizers;
  }

  protected function getTrainingEventByNpk($page, $lists_shown, $npk)
  {
    $offset = ($page - 1) * $lists_shown;

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT evt.EVT_ID, trainings.TRAINING, organizers.ORGANIZER, evt.START_DATE, evt.END_DATE, ep.APPROVED, ep.COMPLETED FROM event_participants AS ep INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID INNER JOIN organizers ON evt.ORG_ID = organizers.ORG_ID WHERE ep.NPK = :npk ORDER BY evt.EVT_ID LIMIT $lists_shown OFFSET $offset");
    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings.php?error=stmterror");
      exit();
    }

    $trainings = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $trainings;
  }

  protected function getAllDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT departments.DPT_ID, departments.DEPARTMENT FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN events AS evt ON evt.EVT_ID = ep.EVT_ID WHERE (:sec_id IS NULL OR users.SEC_ID = :sec_id) AND (:sub_sec_id IS NULL OR users.SUB_SEC_ID = :sub_sec_id) AND (:gender IS NULL OR users.GENDER = :gender) AND (:grade IS NULL OR users.GRADE = :grade) AND (:t_id IS NULL OR evt.T_ID = :t_id) GROUP BY departments.DPT_ID ORDER BY departments.DEPARTMENT");

    $statement->bindValue("sec_id", $sec_id);
    $statement->bindValue("sub_sec_id", $sub_sec_id);
    $statement->bindValue("gender", $gender);
    $statement->bindValue("grade", $grade);
    $statement->bindValue("t_id", $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../index.php?err=stmterror");
      exit();
    }

    $departments = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $departments;
  }



  protected function createManyParticipants($npk, $password, $name, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $c_id, $rls_id)
  {

    $connection = new Connection();

    $statement = $connection->pdo->prepare('INSERT INTO users (NPK, PASSWORD, NAME,DPT_ID,SEC_ID,SUB_SEC_ID,GRADE,GENDER,C_ID,RLS_ID) 
    VALUES (:npk,:password,:name,:dpt_id,:sec_id,:sub_sec_id,:grade,:gender,:c_id,:rls_id)');

    $statement->bindValue('npk', $npk);
    $statement->bindValue('password', $password);
    $statement->bindValue('name', $name);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('sub_sec_id', $sub_sec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('c_id', $c_id);
    $statement->bindValue('rls_id', $rls_id);


    if (!$statement->execute()) {
      $statement = null;
      header("HTTP/1.1 500 Internal Server Error");
      exit();
    }

    $statement = null;
  }

  protected function getAllFilteredUsers($page, $lists_shown, $c_id, $dpt_id, $sec_id, $subsec_id, $grade, $gender, $t_id, $search)
  {
    $offset = ($page - 1) * 25;

    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, companies.COMPANY, departments.DEPARTMENT, trainings.TRAINING, sections.SECTION, subsections.SUBSECTION, users.GRADE, users.GENDER FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID INNER JOIN sections ON users.SEC_ID = sections.SEC_ID INNER JOIN subsections ON users.SUB_SEC_ID = subsections.SUB_SEC_ID INNER JOIN companies ON companies.C_ID = users.C_ID WHERE (:c_id IS NULL OR companies.C_ID = :c_id) AND (:dpt_id IS NULL OR departments.DPT_ID = :dpt_id) AND (:t_id IS NULL OR trainings.T_ID = :t_id) AND (:sec_id IS NULL OR sections.SEC_ID = :sec_id) AND (:subsec_id IS NULL OR subsections.SUB_SEC_ID = :subsec_id) AND (:grade IS NULL OR users.GRADE = :grade) AND (:gender IS NULL OR users.GENDER = :gender) AND (:search IS NULL OR (users.NPK = :search OR users.NAME LIKE CONCAT('%', :search , '%'))) GROUP BY users.NPK ORDER BY users.NPK LIMIT $lists_shown OFFSET $offset");
    $statement->bindValue('c_id', $c_id);
    $statement->bindValue('dpt_id', $dpt_id);
    $statement->bindValue('t_id', $t_id);
    $statement->bindValue('sec_id', $sec_id);
    $statement->bindValue('subsec_id', $subsec_id);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
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
