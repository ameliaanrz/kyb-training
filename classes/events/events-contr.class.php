<?php

require_once __DIR__ . '/events.class.php';

class EventController extends Event
{
  private $evt_id;
  private $t_id;
  private $org_id;
  private $activated;
  private $ids;
  private $organizer;
  private $trainer;
  private $ta_id;
  private $loc_id;
  private $location;
  private $start_date;
  private $end_date;
  private $start_time;
  private $end_time;
  private $duration_hours;
  private $duration_days;
  private $evt_to;


  protected $errors = array(
    'evt_id' => '',
    't_id' => '',
    'org_id' => '',
    'activated' => '',
    'organizer' => '',
    'ta_id' => '',
    'trainer' => '',
    'ta_id' => '',
    'loc_id' => '',
    'location' => '',
    'start_date' => '',
    'end_date' => '',
    'start_time' => '',
    'end_time' => '',
    'duration_hours' => '',
    'duration_days' => '',
    'evt_to' => ''
  );

  public function getRegisteredParticipants($evt_id, $c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $approved, $completed, $search, $colomIndex, $direction)
  {
    $result = $this->getAllRegisteredParticipants($evt_id, $c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $approved, $completed, $search, $colomIndex, $direction);
    return empty($result) ? false : $result;
  }

  public function registerUser($ep_id, $evt_id, $npk)
  {
    $this->registerCurrUser($ep_id, $evt_id, $npk);
  }

  public function unregisterUser($evt_id, $npk)
  {
    $this->unregisterCurrUser($evt_id, $npk);
  }

  public function getUsers($c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $search, $evt_id, $colomIndex, $direction)
  {
    $users = $this->getAllUsers($c_id, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $search, $evt_id, $colomIndex, $direction);
    return empty($users) ? false : $users;
  }

  public function getRegisterParticipantsQueries()
  {
    $companies = $this->getAllUserCompanies();
    $departments = $this->getAllUserDepartments();
    $sections = $this->getAllUserSections();
    $subsections = $this->getAllUserSubsections();
    $grades = $this->getAllUserGrades();
    $genders = $this->getAllUserGenders();

    return array(
      'companies' => $companies,
      'departments' => $departments,
      'sections' => $sections,
      'subsections' => $subsections,
      'grades' => $grades,
      'genders' => $genders,
    );
  }

  public function getTrainingByEvtId($evt_id)
  {
    $training = $this->getCurrTrainingByEvtId($evt_id);
    return empty($training) ? false : $training;
  }

  public function createTrainingEvent($t_id, $org_id, $ta_id, $loc_id, $start_date, $end_date, $duration_days, $start_time, $end_time, $duration_hours, $evt_to)
  {
    // set variables
    $this->t_id = $t_id;
    $this->org_id = $org_id;
    $this->ta_id = $ta_id;
    $this->loc_id = $loc_id;
    $this->start_date = $start_date;
    $this->end_date = $end_date;
    $this->start_time = $start_time;
    $this->end_time = $end_time;
    $this->duration_hours = $duration_hours;
    $this->duration_days = $duration_days;
    $this->evt_to = $evt_to;

    // check error
    $this->invalidTimes();
    $this->emptyField();

    // if no error, create new training event
    if (
      empty($this->errors['t_id']) &&
      empty($this->errors['org_id']) &&
      empty($this->errors['ta_id']) &&
      empty($this->errors['loc_id']) &&
      empty($this->errors['start_date']) &&
      empty($this->errors['end_date']) &&
      empty($this->errors['start_time']) &&
      empty($this->errors['end_time']) &&
      empty($this->errors['duration_hours']) &&
      empty($this->errors['duration_days']) &&
      empty($this->errors['evt_to'])
    ) {
      $this->createNewEvent($t_id, $org_id, $ta_id, $loc_id, $start_date, $end_date, $duration_days, $start_time, $end_time, $duration_hours, $evt_to);
    }

    // return error
    return $this->errors;
  }

  public function createOldTrainingEvent($evt_id, $t_id, $start_date, $end_date, $duration_days, $duration_hours, $activated,  $start_time, $end_time, $loc_id, $org_id, $ta_id, $evt_to)
  {
    // set variables
    $this->evt_id = $evt_id;
    $this->t_id = $t_id;
    $this->start_date = $start_date;
    $this->end_date = $end_date;
    $this->duration_hours = $duration_hours;
    $this->duration_days = $duration_days;
    $this->activated = $activated;
    $this->start_time = $start_time;
    $this->end_time = $end_time;
    $this->loc_id = $loc_id;
    $this->org_id = $org_id;
    $this->ta_id = $ta_id;
    $this->evt_to = $evt_to;

    // check error
    //$this->invalidTimesOld();
    //$this->emptyFieldOld();

    // if no error, create new training event
    if (
      empty($this->errors['evt_id']) &&
      empty($this->errors['t_id']) &&
      empty($this->errors['start_date']) &&
      empty($this->errors['end_date']) &&
      empty($this->errors['duration_hours']) &&
      empty($this->errors['duration_days']) &&
      empty($this->errors['activated']) &&
      empty($this->errors['start_time']) &&
      empty($this->errors['end_time']) &&
      empty($this->errors['loc_id']) &&
      empty($this->errors['org_id']) &&
      empty($this->errors['ta_id']) &&
      empty($this->errors['evt_to'])
    ) {
      $this->uploadOldEvent($evt_id, $t_id, $start_date, $end_date, $duration_days, $duration_hours, $activated,  $start_time, $end_time, $loc_id, $org_id, $ta_id, $evt_to);
    }

    // return error
    return $this->errors;
  }

  public function updateTrainingEvent($evt_id, $evt_to, $activated, $t_id, $org_id, $ta_id, $loc_id, $start_date, $end_date, $start_time, $end_time)
  {
    // set variables
    $this->evt_id = $evt_id;
    $this->evt_to = $evt_to;
    $this->activated = $activated;
    $this->t_id = $t_id;
    $this->org_id = $org_id;
    $this->ta_id = $ta_id;
    $this->loc_id = $loc_id;
    $this->start_date = $start_date;
    $this->end_date = $end_date;
    $this->start_time = $start_time;
    $this->end_time = $end_time;

    // check error
    $this->emptyField();

    // if no error, create new training event
    if (
      empty($this->errors['t_id']) &&
      empty($this->errors['evt_id']) &&
      empty($this->errors['org_id']) &&
      empty($this->errors['ta_id']) &&
      empty($this->errors['loc_id']) &&
      empty($this->errors['start_date']) &&
      empty($this->errors['end_date']) &&
      empty($this->errors['start_time']) &&
      empty($this->errors['end_time']) &&
      empty($this->errors['evt_to'])
    ) {
      $this->updateCurrEvent($evt_id, $evt_to, $activated, $t_id, $org_id, $ta_id, $loc_id, $start_date, $end_date, $start_time, $end_time);
    }

    // return error
    return $this->errors;
  }

  public function createOrganizer($org_id, $organizer)
  {
    $this->organizer = $organizer;
    $this->org_id = $org_id;

    $this->tooLong();
    $this->emptyField();

    if (empty($this->errors['org_id']) && empty($this->errors['organizer'])) {
      $this->createNewOrganizer($org_id, $organizer);
    }

    return $this->errors;
  }

  public function createTrainer($org_id, $ta_id, $trainer)
  {
    $this->ta_id = $ta_id;
    $this->trainer = $trainer;
    $this->org_id = $org_id;

    $this->tooLong();
    $this->emptyField();

    if (empty($this->errors['org_id']) && empty($this->errors['ta_id']) && empty($this->errors['trainer'])) {
      $this->createNewTrainer($org_id, $ta_id, $trainer);
    }

    return $this->errors;
  }

  public function createLocation($loc_id, $location)
  {
    $this->loc_id = $loc_id;
    $this->location = $location;

    $this->tooLong();
    $this->emptyField();

    if (empty($this->errors['loc_id']) && empty($this->errors['location'])) {
      $this->createNewLocation($loc_id, $location);
    }

    return $this->errors;
  }

  private function emptyField()
  {
    $errs = array();
    define("REQUIRED_FIELD_ERROR", "*This field is required");

    if (empty($this->evt_id)) {
      $this->errors['evt_id'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->t_id)) {
      $this->errors['t_id'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->ta_id)) {
      $this->errors['ta_id'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->trainer)) {
      $this->errors['trainer'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->org_id)) {
      $this->errors['org_id'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->organizer)) {
      $this->errors['organizer'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->ta_id)) {
      $this->errors['ta_id'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->loc_id)) {
      $this->errors['loc_id'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->location)) {
      $this->errors['location'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->start_date)) {
      $this->errors['start_date'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->end_date)) {
      $this->errors['end_date'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->start_time)) {
      $this->errors['start_time'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->end_time)) {
      $this->errors['end_time'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->duration_days)) {
      $this->errors['duration_days'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->duration_hours)) {
      $this->errors['duration_hours'] = REQUIRED_FIELD_ERROR;
    }
    if (empty($this->evt_to)) {
      $this->errors['evt_to'] = REQUIRED_FIELD_ERROR;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function emptyFieldOld()
  {
    $errs = array();
    define("REQUIRED_FIELD", "*This field is required");

    if (empty($this->evt_id)) {
      $this->errors['evt_id'] = REQUIRED_FIELD;
    }
    if (empty($this->t_id)) {
      $this->errors['t_id'] = REQUIRED_FIELD;
    }
    if (empty($this->org_id)) {
      $this->errors['org_id'] = REQUIRED_FIELD;
    }
    if (empty($this->ta_id)) {
      $this->errors['ta_id'] = REQUIRED_FIELD;
    }
    if (empty($this->loc_id)) {
      $this->errors['loc_id'] = REQUIRED_FIELD;
    }
    if (empty($this->start_date)) {
      $this->errors['start_date'] = REQUIRED_FIELD;
    }
    if (empty($this->end_date)) {
      $this->errors['end_date'] = REQUIRED_FIELD;
    }
    if (empty($this->start_time)) {
      $this->errors['start_time'] = REQUIRED_FIELD;
    }
    if (empty($this->end_time)) {
      $this->errors['end_time'] = REQUIRED_FIELD;
    }
    if (empty($this->duration_days)) {
      $this->errors['duration_days'] = REQUIRED_FIELD;
    }
    if (empty($this->duration_hours)) {
      $this->errors['duration_hours'] = REQUIRED_FIELD;
    }
    if (empty($this->evt_to)) {
      $this->errors['evt_to'] = REQUIRED_FIELD;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  public function getYears()
  {
    $result = $this->getAllYears();
    return empty($result) ? false : $result;
  }

  public function getMonths()
  {
    $result = $this->getAllMonths();
    return empty($result) ? false : $result;
  }

  public function deleteEvent($evt_id)
  {
    return $this->deleteCurrEvent($evt_id);
  }

  public function getEvent($evt_id)
  {
    $result = $this->getEventById($evt_id);
    return empty($result) ? false : $result;
  }

  public function getEventStatus($evt_id)
  {
    $result = $this->getEventStatusById($evt_id);
    return empty($result) ? false : $result;
  }

  public function getEventData($evt_id)
  {
    $result = $this->getCurrEventData($evt_id);
    return empty($result) ? false : $result;
  }

  public function getEvents($search, $month, $year, $colomn, $direction)
  {
    $result = $this->getAllEvents($search, $month, $year, $colomn, $direction);
    return empty($result) ? false : $result;
  }

  public function getParticipants($evt_id, $dpt_id, $approval, $sec_id, $sub_sec_id, $grade, $gender, $search, $colomIndex, $direction)
  {
    $result = $this->getAllTrainingParticipants($evt_id, $dpt_id, $approval, $sec_id, $sub_sec_id, $grade, $gender, $search, $colomIndex, $direction);
    return empty($result) ? false : $result;
  }

  public function approveParticipants($ids, $approval)
  {
    $this->ids = $ids;

    $this->validIds();

    if (empty($errors['ids'])) {
      $this->approveAllParticipants($ids, $approval);
    }

    return $this->errors;
  }
  public function approvedDeptParticipants($ids, $approval)
  {
    $this->ids = $ids;

    $this->validIds();

    if (empty($errors['ids'])) {
      $this->approveDept_AllParticipants($ids, $approval);
    }

    return $this->errors;
  }

  private function validIds()
  {
    foreach ($this->ids as $id) {
      if (strlen($id) !== 6) {
        $errors['ids'] = "*ID must be exactly 6 characters long";
      }
    }
  }

  public function getFilteredUsers($currPage, $lists_shown, $evt_id, $company, $department, $section, $subsection, $grade, $gender, $search)
  {
    return $this->getAllFilteredUsers($currPage, $lists_shown, $evt_id, $company, $department, $section, $subsection, $grade, $gender, $search);
  }

  public function getFilteredEmployees($currPage, $lists_shown, $company, $department, $section, $subsection, $grade, $gender, $search)
  {
    return $this->getAllFilteredEmployees($currPage, $lists_shown, $company, $department, $section, $subsection, $grade, $gender, $search);
  }

  public function getRegisteredUsers($page, $lists_shown, $evt_id)
  {
    return $this->getAllRegisteredUsers($page, $lists_shown, $evt_id);
  }

  public function getEmployees($page, $lists_shown)
  {
    return $this->getAllEmployees((int)$page, (int)$lists_shown);
  }

  public function getDepartments($evt_id)
  {
    return $this->getAllDepartments($evt_id);
  }

  public function getDepartmentTO()
  {
    return $this->getDepartment();
  }

  public function getUsersDepartments()
  {
    return $this->getAllUsersDepartment();
  }

  public function getCompanies($evt_id)
  {
    return $this->getAllCompanies($evt_id);
  }

  public function getUsersCompanies()
  {
    return $this->getAllUsersCompanies();
  }

  public function getSections($evt_id)
  {
    return $this->getAllSections($evt_id);
  }

  public function getUsersSections()
  {
    return $this->getAllUsersSections();
  }

  public function getEventParticipants($evt_id)
  {
    return $this->getAllParticipants($evt_id);
  }

  public function getSubsections($evt_id)
  {
    return $this->getAllSubsections($evt_id);
  }

  public function getUsersSubsections()
  {
    return $this->getAllUsersSubsections();
  }

  public function getGrades($evt_id)
  {
    return $this->getAllGrades($evt_id);
  }

  public function getUsersGrades()
  {
    return $this->getAllUsersGrades();
  }

  public function getGenders($evt_id)
  {
    return $this->getAllGenders($evt_id);
  }

  public function getUsersGenders()
  {
    return $this->getAllUsersGenders();
  }

  public function getCompletions($evt_id)
  {
    return $this->getAllCompletions($evt_id);
  }

  public function getApprovals($evt_id)
  {
    return $this->getAllApprovals($evt_id);
  }

  public function getTrainings()
  {
    return $this->getAllTrainings();
  }

  public function getTraining($t_id)
  {
    $training = $this->getTrainingById($t_id);
    return empty($training) ? false : $training;
  }

  public function getTrainerOrganizers($ta_id = null)
  {
    return $this->getAllTrainerOrganizers($ta_id);
  }

  public function getOrganizerByTrainer($ta_id)
  {
    $result = $this->getCurrOrganizerByTrainer($ta_id);
    return empty($result) ? false : $result;
  }

  public function getLocations()
  {
    return $this->getAllLocations();
  }

  public function getTrainers($org_id = null)
  {
    return $this->getAllTrainers($org_id);
  }

  public function getParticipantEvents($npk)
  {
    error_log("Fetching events for NPK: " . $npk);

    $result = $this->getAllParticipantEvents($npk);

    if (empty($result)) {
      return false;
    }
    return $result;
  }

  public function filterEvents($page, $lists_shown, $search, $organizer, $start_date, $end_date, $start_time, $end_time, $training_status, $training_location, $trainer)
  {
    $this->start_time = $start_time;
    $this->end_time = $end_time;
    $this->start_date = $start_date;
    $this->end_date = $end_date;
    $this->loc_id = $training_location;
    $this->ta_id = $trainer;

    $this->invalidTimes();

    // if (empty($this->errors['start_time']) && empty($this->errors['end_time']) && empty($this->errors['start_date']) && empty($this->errors['end_date'])) {
    return $this->filterAllEvents($page, $lists_shown, $search, $organizer, $start_date, $end_date, $start_time, $end_time, $training_status, $training_location, $trainer);
    // }
  }

  private function invalidTimes()
  {
    $errs = array();
    if ($this->end_date < $this->start_date) {
      $errs['start_date'] = '*End date couldn\'t be smaller than or equals to start date';
    }
    if ($this->end_time <= $this->start_time) {
      $errs['start_time'] = '*Finish time couldn\'t be smaller than or equals to start time';
    }
    $this->errors = array_merge($this->errors, $errs);
  }

  private function tooLong()
  {
    if (strlen($this->organizer) < 4 || strlen($this->organizer) > 50) {
      $this->errors['organizer'] = "*Characters length must be between 4 and 50";
    }
    if (strlen($this->trainer) < 4 || strlen($this->trainer) > 50) {
      $this->errors['trainer'] = "*Characters length must be between 4 and 50";
    }
    if (strlen($this->location) < 4 || strlen($this->location) > 50) {
      $this->errors['location'] = "*Characters length must be between 4 and 50";
    }
  }
}
