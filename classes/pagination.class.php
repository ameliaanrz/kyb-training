<?php

class Pagination extends Connection
{
  public function getUsersPageCount($lists_shown, $department, $training)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT ep.NPK FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID WHERE (:department IS NULL OR users.DPT_ID = :department) AND (:training IS NULL OR evt.T_ID = :training) GROUP BY NPK");

    $statement->bindValue('department', $department);
    $statement->bindValue('training', $training);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../users.php?error=stmterror");
      exit();
    }

    $result = $statement->rowCount();
    $statement = null;

    return ceil($result / $lists_shown);
  }

  public function getTrainingsPageCount($lists_shown, $search)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT * FROM trainings WHERE (:search IS NULL OR (TRAINING LIKE CONCAT('%', :search, '%') OR T_ID LIKE CONCAT('%', :search, '%')))");

    $statement->bindValue(':search', $search);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../trainings.php?error=stmterror");
      exit();
    }

    $result = $statement->rowCount();
    $statement = null;

    return ceil($result / $lists_shown);
  }

  public function getRegisterParticipantsPageCount($lists_shown, $company, $department, $section, $subsection, $grade, $gender, $search)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT NPK FROM users WHERE (:company IS NULL OR C_ID = :company) AND (:department IS NULL OR DPT_ID = :department) AND (:section IS NULL OR SEC_ID = :section) AND (:subsection IS NULL OR SUB_SEC_ID = :subsection) AND (:grade IS NULL OR GRADE = :grade) AND (:gender IS NULL OR GENDER = :gender) AND (:search IS NULL OR (NPK LIKE CONCAT('%', :search, '%') OR NAME LIKE CONCAT('%', :search, '%')))");

    $statement->bindValue('company', $company);
    $statement->bindValue('department', $department);
    $statement->bindValue('section', $section);
    $statement->bindValue('subsection', $subsection);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $result = $statement->rowCount();
    $statement = null;

    return ceil($result / $lists_shown);
  }

  public function getEventsPageCount($lists_shown, $search, $organizer, $start_date, $end_date, $start_time, $end_time, $training_status, $training_location, $trainer)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT evt.EVT_ID FROM events AS evt INNER JOIN trainings ON evt.T_ID = trainings.T_ID WHERE (:search IS NULL OR (trainings.TRAINING LIKE CONCAT('%', :search, '%') OR evt.EVT_ID LIKE CONCAT('%', :search, '%'))) AND (:organizer IS NULL OR evt.ORG_ID = :organizer) AND (:start_date IS NULL OR evt.START_DATE = :start_date) AND (:end_date IS NULL OR evt.END_DATE = :end_date) AND (:start_time IS NULL OR evt.START_TIME = :start_time) AND (:end_time IS NULL OR evt.END_TIME = :end_time) AND (:training_status IS NULL OR evt.ACTIVATED = :training_status) AND (:training_location IS NULL OR evt.LOC_ID = :training_location) AND (:trainer IS NULL OR evt.TA_ID = :trainer)");

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
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $result = $statement->rowCount();
    $statement = null;

    return ceil($result / $lists_shown);
  }

  public function getUserUpdatePageCount($npk, $lists_shown, $search, $organizer, $approval, $completion)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT ep.NPK FROM event_participants AS ep INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID WHERE ep.NPK = :npk AND (:search IS NULL OR (trainings.TRAINING LIKE CONCAT('%', :search, '%') OR evt.EVT_ID LIKE CONCAT('%', :search, '%') OR trainings.T_ID LIKE CONCAT('%', :search, '%'))) AND (:organizer IS NULL OR evt.ORG_ID = :organizer) AND (:approval IS NULL OR ep.APPROVED = :approval) AND (:completion IS NULL OR ep.COMPLETED = :completion)");

    $statement->bindValue('npk', $npk);
    $statement->bindValue('search', $search);
    $statement->bindValue('organizer', $organizer);
    $statement->bindValue('approval', $approval);
    $statement->bindValue('completion', $completion);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $result = $statement->rowCount();
    $statement = null;

    return ceil($result / $lists_shown);
  }

  public function getRegisteredUsersPageCount($evt_id, $lists_shown, $company, $department, $section, $subsection, $grade, $gender, $search)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT ep.EP_ID FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE ep.EVT_ID = :evt_id AND (:company IS NULL OR C_ID = :company) AND (:department IS NULL OR DPT_ID = :department) AND (:section IS NULL OR SEC_ID = :section) AND (:subsection IS NULL OR SUB_SEC_ID = :subsection) AND (:grade IS NULL OR GRADE = :grade) AND (:gender IS NULL OR GENDER = :gender) AND (:search IS NULL OR (users.NPK LIKE CONCAT('%', :search, '%') OR NAME LIKE CONCAT('%', :search, '%')))");

    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('company', $company);
    $statement->bindValue('department', $department);
    $statement->bindValue('section', $section);
    $statement->bindValue('subsection', $subsection);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $result = $statement->rowCount();
    $statement = null;

    return ceil($result / $lists_shown);
  }

  public function getApprovedUsersPageCount($evt_id, $lists_shown, $company, $department, $section, $subsection, $grade, $gender, $search)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT ep.EP_ID FROM event_participants AS ep INNER JOIN users ON users.NPK = ep.NPK WHERE ep.EVT_ID = :evt_id AND (:company IS NULL OR C_ID = :company) AND (:department IS NULL OR DPT_ID = :department) AND (:section IS NULL OR SEC_ID = :section) AND (:subsection IS NULL OR SUB_SEC_ID = :subsection) AND (:grade IS NULL OR GRADE = :grade) AND (:gender IS NULL OR GENDER = :gender) AND (:search IS NULL OR (users.NPK LIKE CONCAT('%', :search, '%') OR NAME LIKE CONCAT('%', :search, '%')))");

    $statement->bindValue('evt_id', $evt_id);
    $statement->bindValue('company', $company);
    $statement->bindValue('department', $department);
    $statement->bindValue('section', $section);
    $statement->bindValue('subsection', $subsection);
    $statement->bindValue('grade', $grade);
    $statement->bindValue('gender', $gender);
    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: ../../events.php?error=stmterror");
      exit();
    }

    $result = $statement->rowCount();
    $statement = null;

    return ceil($result / $lists_shown);
  }
}
