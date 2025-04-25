<?php

$currUrl = $_SERVER['REQUEST_URI'];
$params = '';
$tmp = explode('?', $currUrl);
if ($tmp[1]) {
  $anotherTmp = explode("&", $tmp[1]);
  $secondTmp =
    $params = strpos($anotherTmp[0], "page=") === false ? $tmp[1] : implode("&", array_slice($anotherTmp, 1));
}

// require classes and instantiate pagination
require_once __DIR__ . '/../classes/connection.class.php';
require_once __DIR__ . '/../classes/pagination.class.php';
$paginationObj = new Pagination();

// get page counts
$pageCount = 1;
if (strpos($currUrl, '/trainings.php') !== false) {
  $pageCount = $paginationObj->getTrainingsPageCount($lists_shown, empty($search) ? NULL : $search);
} else if (strpos($currUrl, '/users.php') !== false) {
  $pageCount = $paginationObj->getUsersPageCount($lists_shown, empty($department) ? NULL : $department, empty($training) ? NULL : $training);
} else if (strpos($currUrl, '/register_participants.php') !== false) {
  $pageCount = $paginationObj->getRegisterParticipantsPageCount($lists_shown, empty($company) ? NULL : $company, empty($department) ? NULL : $department, empty($section) ? NULL : $section, empty($subsection) ? NULL : $subsection, empty($grade) ? NULL : $grade, empty($gender) ? NULL : $gender, empty($search) ? NULL : $search);
} else if (strpos($currUrl, '/events.php') !== false) {
  $pageCount = $paginationObj->getEventsPageCount($lists_shown, empty($search) ? NULL : $search, empty($organizer) ? NULL : $organizer, empty($start_date) ? NULL : $start_date, empty($end_date) ? NULL : $end_date, empty($start_time) ? NULL : $start_time, empty($end_time) ? NULL : $end_time, empty($training_status) ? NULL : $training_status, empty($training_location) ? NULL : $training_location, empty($trainer) ? NULL : $trainer);
} else if (strpos($currUrl, '/users/update.php') !== false) {
  $pageCount = $paginationObj->getUserUpdatePageCount($npk, $lists_shown, empty($search) ? NULL : $search, empty($organizer) ? NULL : $organizer, empty($approval) || $approval == -1 ? NULL : $approval, empty($completion) || $completion == -1 ? NULL : $completion);
} else if (strpos($currUrl, '/events/register.php') !== false) {
  $pageCount = $paginationObj->getRegisteredUsersPageCount($evt_id, $lists_shown, empty($company) ? NULL : $company, empty($department) ? NULL : $department, empty($section) ? NULL : $section, empty($subsection) ? NULL : $subsection, empty($grade) ? NULL : $grade, empty($gender) ? NULL : $gender, empty($search) ? NULL : $search);
} else if (strpos($currUrl, '/events/approve.php') !== false) {
  $pageCount = $paginationObj->getApprovedUsersPageCount($evt_id, $lists_shown, empty($company) ? NULL : $company, empty($department) ? NULL : $department, empty($section) ? NULL : $section, empty($subsection) ? NULL : $subsection, empty($grade) ? NULL : $grade, empty($gender) ? NULL : $gender, empty($search) ? NULL : $search);
}

if (isset($_GET['page'])) {
  $currPage = $_GET['page'];
}

$startPage = $currPage < 5 ? 1 : ($currPage % 5 == 0 ? $currPage : $currPage - ($currPage % 5) + 1);

$endPage = $currPage + 4 < $pageCount ? $startPage + 4 : $pageCount;
