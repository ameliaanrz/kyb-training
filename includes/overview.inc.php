<?php

$currUrl = $_SERVER['REQUEST_URI'];
define("OVERVIEW_TYPE_GETALL", "OVR01");
define("OVERVIEW_TYPE_UPDATE", "OVR02");
$action_type = OVERVIEW_TYPE_GETALL;
$action_types = array(OVERVIEW_TYPE_GETALL,OVERVIEW_TYPE_UPDATE);

if (isset($_GET['type'])) {
  $action_type = get_data('type');
}

if (!in_array($action_type, $action_types)) {
  header("HTTP/1.1 400 Bad Request");
  header("Content-type: application/json");
  echo json_encode(array("Error" => "Invalid type parameter"));
  exit();
}

if(strcmp($action_type,OVERVIEW_TYPE_GETALL)===0){
    require_once __DIR__ . '/../classes/overview/ovr-contr.class.php';

    $ovrContr= new OverviewController();

    $overview = $ovrContr->getAllOverview();

    if($overview === false){
        header("HTTP/1.1 400 Bad Request");
        header("Content-type: application/json");
        echo json_encode(array("Error" => "Cant Get"));
        exit();
    }else{
        header("Content-type: application/json");
        echo json_encode($overview);
    }
exit();
}else if(strcmp($action_type,OVERVIEW_TYPE_UPDATE)===0){
require_once __DIR__ . '/../classes/overview/ovr-contr.class.php';

$ovrContr = new OverviewController();

$id_overview = post_data('id_overview');
$img_slider = post_data('img_slider');
$img_profile1 = post_data('img_profile1');
$title_profile1 = post_data('title_profile1');
$desc_profile1 = post_data('desc_profile1');
$img_profile2 = post_data('img_profile2');
$title_profile2 = post_data('title_profile2');
$desc_profile2 = post_data('desc_profile2');

$sliderUpload = isset($_FILES['sliderUpload']) ? $_FILES['sliderUpload'] : null;
$profile1Upload = isset($_FILES['imgprofile1Upload']) ? $_FILES['imgprofile1Upload'] : null;
$profile2Upload = isset($_FILES['imgprofile2Upload']) ? $_FILES['imgprofile2Upload'] : null;

define('ERROR', '*This field is required!');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

// Perform validation
$errors = array();

if (!$id_overview) {
    $errors['id_overview'] = ERROR;
}
if (!$img_slider) {
    $errors['img_slider'] = ERROR;
}
if (!$img_profile1) {
    $errors['img_profile1'] = ERROR;
}
if (!$title_profile1) {
    $errors['title_profile1'] = ERROR;
}
if (!$desc_profile1) {
    $errors['desc_profile1'] = ERROR;
}
if (!$img_profile2) {
    $errors['img_profile2'] = ERROR;
}
if (!$title_profile2) {
    $errors['title_profile2'] = ERROR;
}
if (!$desc_profile2) {
    $errors['desc_profile2'] = ERROR;
}

// Check if files are uploaded and their sizes
if ($sliderUpload && $sliderUpload['size'] > MAX_FILE_SIZE) {
    $errors['sliderUpload'] = 'Slider image must be less than 2MB.';
}
if ($profile1Upload && $profile1Upload['size'] > MAX_FILE_SIZE) {
    $errors['imgprofile1Upload'] = 'Profile1 image must be less than 2MB.';
}
if ($profile2Upload && $profile2Upload['size'] > MAX_FILE_SIZE) {
    $errors['imgprofile2Upload'] = 'Profile2 image must be less than 2MB.';
}

// If there are any errors, return them in the response
if (!empty($errors)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
    exit();
}

// Proceed with the rest of the code if no errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

$result = $ovrContr->UpdateOverviews($id_overview, $img_slider, $img_profile1, $title_profile1, $desc_profile1, $img_profile2, $title_profile2, $desc_profile2);
if ($result !== false) {
    $uploadedFiles = array();
    $uploadErrors = array(); // Initialize the uploadErrors array

    if ($sliderUpload) {
        $fileName = $sliderUpload['name'];
        $fileDestination = __DIR__ . '/../public/imgs/uploads/' . $fileName;
        if (move_uploaded_file($sliderUpload['tmp_name'], $fileDestination) === false) {
            $uploadErrors['sliderUpload'] = 'Failed to upload slider image: ' . $fileName;
            $lastError = error_get_last();
            if ($lastError) {
                $uploadErrors['sliderUploadSystemError'] = $lastError['message'];
            }
        }
    }

    if ($profile1Upload) {
        $fileName = $profile1Upload['name'];
        $fileDestination = __DIR__ . '/../public/imgs/uploads/' . $fileName;
        if (move_uploaded_file($profile1Upload['tmp_name'], $fileDestination) === false) {
            $uploadErrors['imgprofile1Upload'] = 'Failed to upload Profile1 image: ' . $fileName;
            $lastError = error_get_last();
            if ($lastError) {
                $uploadErrors['imgprofile1UploadSystemError'] = $lastError['message'];
            }
        }
    }

    if ($profile2Upload) {
        $fileName = $profile2Upload['name'];
        $fileDestination = __DIR__ . '/../public/imgs/uploads/' . $fileName;
        if (move_uploaded_file($profile2Upload['tmp_name'], $fileDestination) === false) {
            $uploadErrors['imgprofile2Upload'] = 'Failed to upload Profile2 image: ' . $fileName;
            $lastError = error_get_last();
            if ($lastError) {
                $uploadErrors['imgprofile2UploadSystemError'] = $lastError['message'];
            }
        }
    }

    if (!empty($uploadErrors)) {
        header("HTTP/1.1 400 Bad Request");
        header("Content-type: application/json");
        echo json_encode($uploadErrors);
        exit();
    }

    // Return success response if all uploads succeeded
    header("HTTP/1.1 200 OK");
    header("Content-type: application/json");
    echo json_encode(array("Success" => "Data Updated and files uploaded successfully."));
    exit();

} else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("Error" => "Data Not Updated"));
    exit();
}

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