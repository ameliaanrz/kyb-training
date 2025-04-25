<?php
session_start();
date_default_timezone_set("Asia/Krasnoyarsk");


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
define('ROLES_ACTION_TYPE_GET', '1');
define('ROLES_ACTION_TYPE_UPDATE', '2');
define('ACTION_TYPE_GET_FILTERS', '3');

// get type parameter from url
if (isset($_GET['type'])) {
  $type = get_data('type');

  if (!in_array($type, array(ROLES_ACTION_TYPE_GET,ROLES_ACTION_TYPE_UPDATE,ACTION_TYPE_GET_FILTERS))) {
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

require_once __DIR__ . '/../classes/roles/roles-controller.php';
    $rolesContr = new RolesController();


// get all roles
if ($type == ROLES_ACTION_TYPE_GET) {
    $npk = get_data('npk');
    $name = get_data('name');
    $role = get_data('role');
    $department = get_data('dpt_id');
    $section = get_data('sec_id');
    $subsection = get_data('sub_sec_id');
    $direction = get_data('direction');
    $colom = get_data('colomIndex');
    $search = get_data('search');

  $roles = $rolesContr->getAllUsers($npk,$name,$role,$department,$section,$subsection,$direction,$colom,$search);
  if($roles == null){
    header("HTTP/1.1 204 Not Found");
    exit();
  }else{
    header("Content-type: application/json");
    echo json_encode($roles);
  exit();
  }
}else if($type == ACTION_TYPE_GET_FILTERS){
    // get params
  $dpt_id = get_data('dpt_id');
  $sec_id = get_data('sec_id');
  $sub_sec_id = get_data('sub_sec_id');
  $role = get_data('rls_id');


  // get filters
  $departments = $rolesContr->getDepartments($sec_id, $sub_sec_id, $gender, $grade, $t_id);
  $sections = $rolesContr->getSections($dpt_id, $sub_sec_id, $gender, $grade, $t_id);
  $subsections = $rolesContr->getSubsections($dpt_id, $sec_id, $gender, $grade, $t_id);
  $roles = $rolesContr->getRoles();

  $fetch = array(
    'departments' => $departments,
    'sections' => $sections,
    'subsections' => $subsections,
    'roles' => $roles
  );
  header("Content-type: application/json");
    echo json_encode($fetch);
  exit();

}else if($type ==ROLES_ACTION_TYPE_UPDATE){
    $rls_id = post_data('role');
    $npk = post_data('npk');

    $rolesContr->updateRoleUsers($rls_id,$npk);
    header("HTTP/1.1 200 OK");
    exit();
    
}




function post_data($field)
{
  if (isset($_POST[$field]) && is_array($_POST[$field])) {
    $arr = $_POST[$field];
    foreach ($arr as $i => $item) {
      $item = isset($item) && $item ? $item : false;
      $item = stripslashes(htmlspecialchars($item));
      $arr[$i] = $item;
    }
    return $arr;
  }
  $_POST[$field] = isset($_POST[$field]) && $_POST[$field] ? $_POST[$field] : false;
  return stripslashes(htmlspecialchars($_POST[$field]));
}

function get_data($field)
{
  return isset($_GET[$field]) && !empty($_GET[$field]) ? stripslashes(htmlspecialchars($_GET[$field])) : null;
}
?>
