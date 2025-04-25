<?php

$currUrl = $_SERVER['REQUEST_URI'];

define("ACTION_TYPE_GET", "TACT01");
define("ACTION_TYPE_DELETE", "TACT02");
define("ACTION_TYPE_CREATE", "TACT03");
define("ACTION_TYPE_UPDATE", "TACT04");
define("ACTION_TYPE_GET_MATERIALS", "TACT05");
define("ACTION_TYPE_DELETE_MATERIAL", "TACT06");
define("ACTION_TYPE_CREATE_MATERIAL", "TACT07");
define("ACTION_TYPE_UPDATE_MATERIAL", "TACT08");
define("ACTION_TYPE_GET_CHAPTERS", "TACT09");
define("ACTION_TYPE_DELETE_CHAPTER", "TACT10");
define("ACTION_TYPE_CREATE_CHAPTER", "TACT11");
define("ACTION_TYPE_UPDATE_CHAPTER", "TACT12");
define("ACTION_TYPE_GET_CONTENT", "TACT013");
define("ACTION_TYPE_DELETE_CONTENT", "TACT14");
define("ACTION_TYPE_CREATE_CONTENT", "TACT15");
define("ACTION_TYPE_UPDATE_CONTENT", "TACT16");
define("ACTION_TYPE_CREATE_SUBCHAPTER", "TACT17");
define("ACTION_TYPE_UPDATE_SUBCHAPTER", "TACT18");
define("ACTION_TYPE_DELETE_SUBCHAPTER", "TACT19");
define("ACTION_TYPE_CHANGE_POSITION", "TACT20");
define("ACTION_TYPE_CHANGE_STATUS_VR", "TACT21");

$action_type = ACTION_TYPE_GET;

$action_types = array(ACTION_TYPE_GET, ACTION_TYPE_DELETE, ACTION_TYPE_CREATE, ACTION_TYPE_UPDATE, ACTION_TYPE_GET_MATERIALS, ACTION_TYPE_DELETE_MATERIAL, ACTION_TYPE_CREATE_MATERIAL, ACTION_TYPE_UPDATE_MATERIAL, ACTION_TYPE_GET_CHAPTERS, ACTION_TYPE_DELETE_CHAPTER, ACTION_TYPE_CREATE_CHAPTER, ACTION_TYPE_UPDATE_CHAPTER, ACTION_TYPE_GET_CONTENT, ACTION_TYPE_DELETE_CONTENT, ACTION_TYPE_CREATE_CONTENT, ACTION_TYPE_UPDATE_CONTENT, ACTION_TYPE_CREATE_SUBCHAPTER, ACTION_TYPE_UPDATE_SUBCHAPTER, ACTION_TYPE_DELETE_SUBCHAPTER, ACTION_TYPE_CHANGE_POSITION,ACTION_TYPE_CHANGE_STATUS_VR);

if (isset($_GET['type'])) {
  $action_type = get_data('type');
}

if (!in_array($action_type, $action_types)) {
  header("HTTP/1.1 400 Bad Request");
  header("Content-type: application/json");
  echo json_encode(array("Error" => "Invalid type parameter"));
  exit();
}

$t_id = '';
$tsch_id = '';
$training_name = '';
$organizer = '';
$description = '';
$purpose = '';
$outline = '';
$participant = '';
$search = '';
$new_organizer = '';
$start_date = '';
$end_date = '';
$days = '';
$start_time = '';
$end_time = '';
$duration = '';
$direction = '';
$page = 1;
$duration_days = '';
$duration_hours = '';
$lists_shown = 10;
$colomIndex = '';

$organizers = array();
$company_purposes = array();
$participant_purposes = array();
$materials = array();
$company_purposes_update = array();
$participant_purposes_update = array();
$materials_update = array();
$training = array();

$trainings = array();
$chapters = array();
$material = array();
$materials = array();
$currPage = 1;

$errors = array(
  't_id' => '',
  'training_name' => '',
  'organizer' => '',
  'new_organizer' => '',
  'description' => '',
  'purpose' => '',
  'company_purposes' => '',
  'participant_purposes' => '',
  'outline' => '',
  'participant' => '',
  'materials' => '',
  'start_date' => '',
  'end_date' => '',
  'page' => '',
  'direction' => '',
  'days' => '',
  'start_time' => '',
  'colomIndex' => '',
  'end_time' => '',
  'duration' => '',
  'tsch_id' => '',
  'tch_id' => '',
  'sch_id' => '',
  'tcc_id' => '',
  'chapter_title' => '',
  'material_title' => '',
  'chapter_description' => '',
  'material_description' => '',
  'subchapter_title' => '',
  'content_type' => '',
  'subchapter_paragraph' => '',
  'subchapter_file_name' => '',
  'subchapter_link' => '',
  'subchapter_list' => '',
  'duration_days' => '',
  'duration_hours' => '',
  'core_content' => ''
);

// training content variables
$tch_id = '';
$tsch_id = '';
$tcc_id = '';
$chapter_title = '';
$chapter_description = '';
$material_title = '';
$material_description = '';
$content_type = '';
$subchapter_title = '';
$subchapter_paragraph = '';
$subchapter_file_name = '';
$subchapter_file_size = '';
$subchapter_content = '';
$subchapter_link = '';
$subchapter_list = '';

// content types definition
define("CONTENT_TYPE_PARAGRAPH", 1);
define("CONTENT_TYPE_IMAGE", 2);
define("CONTENT_TYPE_VIDEO", 3);
define("CONTENT_TYPE_PDF", 4);
define("CONTENT_TYPE_LINK", 5);
define("CONTENT_TYPE_UNORDERED_LIST", 6);
define("CONTENT_TYPE_ORDERED_LIST", 7);

// arrs
$chapter = array(
  'TCH_ID' => '',
  'TITLE' => '',
  'DESCRIPTION' => ''
);
$subchapters = array();
$subchapters_contents = array();

if (strcmp($action_type, ACTION_TYPE_GET) === 0) {
  // require trainings controller and create instance
  require_once __DIR__ . '/../classes/trainings/trainings-contr.class.php';
  $trainingsContr = new TrainingsController();

  // get training id
  if (isset($_GET['t_id']) && !empty($_GET['t_id'])) {
    // get params
    $t_id = get_data('t_id');

    // get single training
    $training = $trainingsContr->getTraining($t_id);

    // get training company purposes
    $companyPurposes = $trainingsContr->getCompanyPurposes($t_id);

    // get training participants purposes
    $participantsPurposes = $trainingsContr->getParticipantsPurposes($t_id);

    // return responses
    if ($training === false) {
      header("HTTP/1.1 204 No Content");
      echo json_encode(array("status"=> false));
    } else {
      header("Content-type: application/json");
      echo json_encode(
        array(
          "training" => $training,
          "company_purposes" => $companyPurposes,
          "participants_purposes" => $participantsPurposes
        )
      );
    }
    exit();
  }

  // get trainings with total lists shown
  if (isset($_GET['lists_shown']) && !empty($_GET['lists_shown']) && filter_var($_GET['lists_shown'], FILTER_VALIDATE_INT)) {
    $lists_shown = get_data('lists_shown');
  }

  // get all trainings
  if (isset($_GET['page']) && !empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) {
    $currPage = get_data('page');
  }

  // search filter trainings
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = get_data('search');
  }

  // get colomIndex
  if (isset($_GET['colomIndex']) && !empty($_GET['colomIndex'])) {
    $colomIndex = get_data('colomIndex');
  }

  // get direction
  if (isset($_GET['direction']) && !empty($_GET['direction'])) {
    $direction = get_data('direction');
  } 


  // get all trainings count
  $trainingsCountTotal = $trainingsContr->getAllTrainingsCount();

  // get trainings
  $trainings = $trainingsContr->getTrainings($page, $lists_shown, $search,$colomIndex,$direction);

  if ($trainings === false) {
    header("HTTP/1.1 204 No Content");
    exit();
  }

  header("Content-type: application/json");
  echo json_encode(array("trainings_count" => $trainingsCountTotal, "trainings" => $trainings));
  exit();
} else if (strcmp($action_type, ACTION_TYPE_DELETE) === 0) {
  // get datas
  $t_id = get_data("t_id");

  if (empty($t_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training ID parameter not specified"));
    exit();
  }

  // require and instantiate training content controller
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsController = new TrainingsContentController();

  // delete training
  $trainingsController->deleteTraining($t_id);

  // redirect to curr page
  header("Location: ../trainings.php");
  // header("HTTP/1.1 200 OK");
  // exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE) === 0) {
  require_once __DIR__ . '/../classes/connection.class.php';
  $connection = new Connection();

  // get post data
  // $raw_t_id = $connection->getLatestTrainingId();
  // $raw_t_id = $raw_t_id[0];
  // $t_id = "T" . str_pad(strval((int)substr($raw_t_id, 1) + 1), 4, "0", STR_PAD_LEFT);
  $t_id = post_data('training_id');
  $training_name = post_data('training_name');
  $description = post_data('training_description');
  $purpose = post_data('training_purpose');
  $company_purposes = post_data('company_purposes');
  $participant_purposes = post_data('participant_purposes');
  $outline = post_data('outline');
  $duration_days = post_data('durationDays');
  $duration_hours = post_data('durationHours');
  $participant = post_data('participant');

  // require classes and instantiate trainingsController
  require_once __DIR__ . '/../classes/trainings/create-contr.class.php';
  require_once __DIR__ . '/../classes/trainings/update-contr.class.php';
  require_once __DIR__ . '/../classes/trainings/trainings-contr.class.php';

  $getTraining = new TrainingsController();
  $getTraining = $getTraining->getTraining($t_id);

  if (isset($getTraining['status']) && $getTraining['status'] == 0) {
      // Training exists or is inactive, perform update
      $trainingsController = new TrainingsUpdateController($t_id, $training_name, $description, $purpose, $company_purposes, $participant_purposes, $outline, $duration_days, $duration_hours, $participant);
      $errors = $trainingsController->updateTraining();
  } else {
      // Training does not exist or is active, create new training
      $trainingsController = new CreateTrainingController($t_id, $training_name, $description, $purpose, $company_purposes, $participant_purposes, $outline, $duration_days, $duration_hours, $participant);
      $errors = $trainingsController->createTraining();
  }

  // redirect to trainings dashboard
  if (empty($errors['t_id']) && empty($errors['training_name']) && empty($errors['description']) && empty($errors['purpose']) && empty($errors['company_purposes']) && empty($errors['participant_purposes']) && empty($errors['outline']) && empty($errors['duration_days']) && empty($errors['duration_hours']) && empty($errors['participant'])) {
    exit();
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(
      array(
        "training_name" => $errors['training_name'],
        "training_id" => $errors['t_id'],
        "description" => $errors['description'],
        "purpose" => $errors['purpose'],
        "company_purposes" => $errors['company_purposes'],
        "participant_purposes" => $errors['participant_purposes'],
        "outline" => $errors['outline'],
        "durationDays" => $errors['duration_days'],
        "durationHours" => $errors['duration_hours'],
        "participant" => $errors['participant']
      )
    );
    exit();
  }
} else if ($action_type == ACTION_TYPE_UPDATE) {
  if (!isset($_GET['t_id']) || !$_GET['t_id']) {
    echo "<h1 class='mt-5 fw-bold'>404 - Training not found</h1><p class='fs-5'>Go back to <a href='../trainings.php'>Trainings</a> page</p>";
    exit();
  } else {
    // require classes and initialize trainingsController
    require __DIR__ . '/../classes/trainings/update-contr.class.php';

    // get datas
    $t_id = get_data('t_id');

    // get post datas
    $training_name = post_data('training_name');
    $description = post_data('training_description');
    $purpose = post_data('training_purpose');
    $company_purposes = post_data('company_purposes');
    $participant_purposes = post_data('participant_purposes');
    $outline = post_data('outline');
    $duration_days = post_data('durationDays');
    $duration_hours = post_data('durationHours');
    $participant = post_data('participant');

    // instantiate trainings update controller
    $trainingsUpdateController = new TrainingsUpdateController($t_id, $training_name, $description, $purpose, $company_purposes, $participant_purposes, $outline, $duration_days, $duration_hours, $participant);

    // update training
    $errors = array_merge($errors, $trainingsUpdateController->updateTraining());

    // redirect to trainings dashboard
    if (empty($errors['t_id']) && empty($errors['training_name']) && empty($errors['description']) && empty($errors['purpose']) && empty($errors['company_purposes']) && empty($errors['participant_purposes']) && empty($errors['company_purposes_update']) && empty($errors['participant_purposes_update']) && empty($errors['outline']) && empty($errors['duration_days']) && empty($errors['duration_hours']) && empty($errors['participant'])) {
      header("HTTP/1.1 200 OK");
      header("Content-type: application/json");
      echo json_encode(array("success" => "Training is updated successfully"));
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode($errors);
    }
    exit();
  }
} else if ($action_type == ACTION_TYPE_GET_MATERIALS) {
  // require t_id param
  if (!isset($_GET['t_id']) || !$_GET['t_id']) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training ID parameter is required"));
    exit();
  }

  // get url parameters
  $t_id = get_data('t_id');
  $m_id = get_data('m_id');

  // require classes and instantiate contentController
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // get training name
  $training = $trainingsContentController->getTrainingName($t_id);

  if ($training === false) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Invalid training ID"));
    exit();
  }

  // get single material
  if (isset($m_id) && !empty($m_id)) {
    // get training material
    $material = $trainingsContentController->getTrainingMaterial($m_id);

    if ($material === false) {
      header("HTTP/1.1 204 No Content");
    } else {
      header("Content-type: application/json");
      echo json_encode(array("training_name" => $training, "training_material" => $material));
    }
    exit();
  }

  // get training materials
  $materials = $trainingsContentController->getTrainingMaterials($t_id);

  if ($materials === false) {
    header("HTTP/1.1 204 No Content");
    exit();
  } else {
    header("Content-type: application/json");
    echo json_encode(array("training_name" => $training, "training_materials" => $materials));
    exit();
  }
} else if ($action_type == ACTION_TYPE_DELETE_MATERIAL) {
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // get url params
  $t_id = get_data('t_id');
  $m_id = get_data('m_id');

  if (!isset($m_id) || empty($m_id) || !isset($t_id) || empty($t_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Material and training ID parameter is required"));
    exit();
  }

  // delete material
  $trainingsContentController->deleteMaterial($m_id);

  // redirect to current page
  header("Location: ../trainings/materials.php?t_id=$t_id");
} else if ($action_type == ACTION_TYPE_CREATE_MATERIAL) {
  // require t_id param
  if (!isset($_GET['t_id']) || !$_GET['t_id']) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training ID parameter is required"));
    exit();
  }

  // get t_id
  $t_id = get_data('t_id');

  // require classes and instantiate contentController
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // create new training chapter
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $raw_m_id = $trainingsContentController->getLatestTrainingMaterialId($t_id);
    $m_id = empty($raw_m_id) ? "M0000" : "M" . str_pad(strval((int) substr($raw_m_id, 1) + 1), 4, "0", STR_PAD_LEFT);
    $material_title = post_data('material_title');
    $material_description = post_data('material_description');

    // create new material
    $errors = array_merge($errors, $trainingsContentController->createTrainingMaterial($t_id, $m_id, $material_title, $material_description));

    // redirect to training chapters page
    if (empty($errors['material_title']) && empty($errors['material_description'])) {
      header("HTTP/1.1 201 Created");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode($errors);
    }
    exit();
  }
} else if (strcmp($action_type, ACTION_TYPE_UPDATE_MATERIAL) === 0) {
  // get t_id and m_id
  $t_id = get_data('t_id');
  $m_id = get_data('m_id');

  // require t_id param
  if (empty($t_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training and material ID parameter is required"));
    exit();
  }

  // get post datas
  $material_title = post_data('material_title');
  $material_description = post_data('material_description');

  // require and instantiate controller
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // update chapter
  $errors = array_merge($errors, $trainingsContentController->updateTrainingMaterial($m_id, $material_title, $material_description));

  // redirect to current page
  if (empty($errors['m_id']) && empty($errors['material_title']) && empty($errors['material_description'])) {
    header("HTTP/1.1 200 OK");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_CHAPTERS) === 0) {
  // get url params
  $t_id = get_data('t_id');
  $m_id = get_data('m_id');
  $tch_id = get_data('tch_id');

  // require url params
  if (empty($t_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training and material ID parameters are required"));
    exit();
  }

  // require classes and instantiate contentController
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // get training name
  $training = $trainingsContentController->getTrainingName($t_id);
  $material = $trainingsContentController->getMaterial($m_id);

  if (isset($tch_id) && !empty($tch_id)) {
    // get single training chapter
    $chapter = $trainingsContentController->getTrainingChapter($tch_id);

    if ($chapter === false || $training === false || $material === false) {
      header("HTTP/1.1 204 No Content");
    } else {
      header("Content-type: application/json");
      echo json_encode(
        array(
          "training" => $training,
          "material" => $material,
          "chapter" => $chapter
        )
      );
    }
    exit();
  }

  // get all training chapters
  $chapters = $trainingsContentController->getTrainingChapters($m_id);

  if ($training === false || $material === false || $chapters === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode(
      array(
        "training" => $training,
        "material" => $material,
        "chapters" => $chapters
      )
    );
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_DELETE_CHAPTER) === 0) {
  // get post datats
  $tch_id = get_data('tch_id');
  $t_id = get_data('t_id');
  $m_id = get_data('m_id');

  if (empty($tch_id) || empty($m_id) || empty($t_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Chapter, training, and material ID parameters are required"));
    exit();
  }

  // require controller and create instance
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // delete chapter
  $trainingsContentController->deleteChapter($tch_id);

  // redirect to current page on success
  header("Location: ../trainings/chapters.php?t_id=$t_id&m_id=$m_id");
} else if (strcmp($action_type, ACTION_TYPE_CREATE_CHAPTER) === 0) {
  // get url params
  $t_id = get_data('t_id');
  $m_id = get_data('m_id');

  if (empty($t_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training and material ID parameters are required"));
    exit();
  }

  // require and instantiate trainingsContentController
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // get post datas
  $raw_tch_id = $trainingsContentController->getLatestTrainingChapterId($t_id);
  $tch_id = empty($raw_tch_id) ? "TCH0000" : "TCH" . str_pad(strval((int) substr($raw_tch_id, 3) + 1), 4, "0", STR_PAD_LEFT);
  $chapter_title = post_data('chapter_title');
  $chapter_description = post_data('chapter_description');

  // create new training chapter
  $errors = array_merge($errors, $trainingsContentController->createTrainingChapter($t_id, $m_id, $tch_id, $chapter_title, $chapter_description));

  // redirect to training chapters page
  if (empty($errors['chapter_title']) && empty($errors['chapter_description'])) {
    header("HTTP/1.1 201 Created");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_UPDATE_CHAPTER) === 0) {
  // get url params
  $tch_id = get_data('tch_id');
  $m_id = get_data('m_id');

  if (empty($tch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training chapter and material ID parameters are required"));
    exit();
  }

  // get post datas
  $chapter_title = post_data('chapter_title');
  $chapter_description = post_data('chapter_description');

  // require controller and instantiation
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // update chapter
  $errors = array_merge($errors, $trainingsContentController->updateTrainingChapter($tch_id, $chapter_title, $chapter_description));

  // responses
  if (empty($errors['tch_id']) && empty($errors['chapter_title']) && empty($errors['chapter_description']) && empty($errors['m_id'])) {
    header("HTTP/1.1 200 OK");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_CONTENT) === 0) {
  // get t_id, m_id and tch_id
  $t_id = get_data('t_id');

  // require t_id param
  if (empty($t_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training ID parameter is required"));
    exit();
  }

  // require content controller and instantiate it
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // get subchapter contents
  $contents = $trainingsContentController->getTrainingSubchaptersContents($t_id);

  // JSON responses
  if ($contents === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode($contents);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE_CONTENT) === 0) {
  // get url params
  $t_id = get_data("t_id");

  if (empty($t_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training ID parameter is required"));
    exit();
  }

  // require and instantiate trainingsContentController
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // file upload
  if (isset($_FILES['core_content'])) {
    // get file datas
    $raw_tcc_id = $trainingsContentController->getLatestCoreContentId();
    $tcc_id = empty($raw_tcc_id) ? "TCC00001" : "TCC" . str_pad(strval((int) substr($raw_tcc_id, 3) + 1), 5, "0", STR_PAD_LEFT);
    $file = $_FILES['core_content'];
    $subchapter_file_name = $file['name'];
    $subchapter_file_size = $file['size'];

    // get post datas
    $content_type = post_data('content_type');

    // upload file
    $errors = array_merge($errors, $trainingsContentController->uploadFile($tcc_id, $t_id, $content_type, $subchapter_file_name, $subchapter_file_size));

    // responses
    if (empty($errors['tcc_id']) && empty($errors['t_id']) && empty($errors['content_type']) && empty($errors['subchapter_file_size']) && empty($errors['subchapter_file_name'])) {
      // store file
      move_uploaded_file($file['tmp_name'], __DIR__ . '/../public/imgs/uploads/' . $subchapter_file_name);

      // redirect to content page
      header("Location: ../trainings/content.php?t_id=$t_id");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array("tcc_id" => $errors['tcc_id'], "content_type" => $errors['content_type'], "file_size" => $errors['subchapter_file_size'], "file_name" => $errors['subchapter_file_name']));
    }
  } else {
    // get post datas
    $raw_tcc_id = $trainingsContentController->getLatestCoreContentId();
    $tcc_id = empty($raw_tcc_id) ? "TCC00001" : "TCC" . str_pad(strval((int) substr($raw_tcc_id, 3) + 1), 5, "0", STR_PAD_LEFT);
    $core_content = post_data('core_content');
    $content_type = post_data('content_type');

    // create new core content
    $errors = array_merge($errors, $trainingsContentController->createCoreContent($tcc_id, $t_id, $content_type, $core_content));

    // responses
    if (empty($errors['core_content']) && empty($errors['t_id']) && empty($errors['tcc_id']) && empty($errors['content_type'])) {
      header("HTTP/1.1 201 Created");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array($errors['core_content'], $errors['tcc_id'], $errors['content_type']));
    }
  }

  exit();

  // redirect to current page if there's no errors
  if (empty($errors['tcc_id']) && empty($errors['sch_id']) && empty($errors['content_type']) && empty($errors['subchapter_paragraph'])) {
    header("Location: ../trainings/chapters/content.php?t_id=$t_id&tch_id=$tch_id&m_id=$m_id&error=none");
  }
} else if (strcmp($action_type, ACTION_TYPE_CREATE_SUBCHAPTER) === 0) {
  // get url params
  $t_id = get_data("t_id");
  $tch_id = get_data("tch_id");
  $m_id = get_data("m_id");

  if (empty($t_id) || empty($tch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training, chapter, subchapter, and material ID parameters are required"));
    exit();
  }

  // get post datas
  $subchapter_title = post_data('subchapter_title');

  // require training content controller and instantiate it
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $contentsController = new TrainingsContentController();

  // create subchapter id
  $raw_sch_id = $contentsController->getLatestSubchapterId();
  $sch_id = empty($raw_sch_id) ? "SCH0001" : "SCH" . str_pad(strval((int) substr($raw_sch_id, 3) + 1), 4, "0", STR_PAD_LEFT);

  // create new training subchapter
  $errors = array_merge($errors, $contentsController->createSubchapter($tch_id, $sch_id, $subchapter_title));

  // responses
  if (empty($errors['tch_id']) && empty($errors['sch_id']) && empty($errors['subchapter_title'])) {
    header("HTTP/1.1 201 Created");
    header("Content-type: application/json");
    echo json_encode(array("success" => "Training subchapter is created"));
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_DELETE_SUBCHAPTER) === 0) {
  // get url params
  $sch_id = get_data("sch_id");
  $t_id = get_data("t_id");
  $tch_id = get_data("tch_id");
  $m_id = get_data("m_id");

  if (empty($sch_id) || empty($t_id) || empty($tch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training, chapter, subchapter, and material ID parameters are required"));
    exit();
  }

  // require trainings content controller and instantiate
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $contentController = new TrainingsContentController();

  // delete subchapter
  $contentController->deleteSubchapter($sch_id);

  // redirect to content page
  header("Location: ../trainings/chapters/content.php?t_id=$t_id&tch_id=$tch_id&m_id=$m_id");
} else if (strcmp($action_type, ACTION_TYPE_UPDATE_SUBCHAPTER) === 0) {
  // get url params
  $sch_id = get_data("sch_id");
  $t_id = get_data("t_id");
  $tch_id = get_data("tch_id");
  $m_id = get_data("m_id");

  if (empty($sch_id) || empty($t_id) || empty($tch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training, chapter, subchapter, and material ID parameters are required"));
    exit();
  }

  // require trainings content controller and instantiate
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $contentController = new TrainingsContentController();

  // get post datas
  $subchapter_title = post_data('subchapter_title');

  // update subchapter
  $errors = array_merge($errors, $contentController->updateTrainingSubchapter($sch_id, $subchapter_title));

  // redirect to content page
  if (empty($errors['sch_id']) && empty($errors['subchapter_title'])) {
    header("HTTP/1.1 200 OK");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_DELETE_CONTENT) === 0) {
  // get url params
  $tcc_id = get_data("tcc_id");

  if (empty($tcc_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training core content ID parameter is required"));
    exit();
  }

  // require trainings content controller and instantiate
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $contentController = new TrainingsContentController();

  // update subchapter
  $contentController->deleteContent($tcc_id);

  // http response
  header("HTTP/1.1 204 No Content");
  exit();
} else if (strcmp($action_type, ACTION_TYPE_UPDATE_CONTENT) === 0) {
  // get url params
  $t_id = get_data("t_id");
  $tcc_id = get_data("tcc_id");

  if (empty($tcc_id) || empty($t_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training and training core content ID parameters are required"));
    exit();
  }

  // require and instantiate trainingsContentController
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $trainingsContentController = new TrainingsContentController();

  // file upload
  if (isset($_FILES['core_content'])) {
    // get file datas
    $file = $_FILES['core_content'];
    $subchapter_file_name = $file['name'];
    $subchapter_file_size = $file['size'];

    // get post datas
    $content_type = post_data('content_type');

    // upload file
    $errors = array_merge($errors, $trainingsContentController->uploadFile($tcc_id, $t_id, $content_type, $subchapter_file_name, $subchapter_file_size));

    // responses
    if (empty($errors['tcc_id']) && empty($errors['t_id']) && empty($errors['content_type']) && empty($errors['subchapter_file_name']) && empty($errors['subchapter_file_size'])) {
      // store file
      move_uploaded_file($file['tmp_name'], __DIR__ . '/../public/imgs/uploads/' . $subchapter_file_name);

      // redirect to content page
      header("Location: ../trainings/content.php?t_id=$t_id");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array("core_content" => empty($errors['subchapter_file_size']) ? $errors['subchapter_file_name'] : $errors['subchapter_file_size'], "tcc_id" => $errors['tcc_id'], "content_type" => $errors['content_type']));
    }
  } else {
    // get post datas
    $core_content = post_data('core_content');
    $content_type = post_data('content_type');

    // create new core content
    $errors = array_merge($errors, $trainingsContentController->updateTrainingContent($tcc_id, $content_type, $core_content));

    // responses
    if (empty($errors['core_content']) && empty($errors['tcc_id']) && empty($errors['content_type'])) {
      header("HTTP/1.1 200 OK");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array("core_content" => $errors['core_content'], "tcc_id" => $errors['tcc_id'], "content_type" => $errors['content_type']));
    }
  }

  // redirect to current page if there's no errors
  if (empty($errors['tcc_id']) && empty($errors['sch_id']) && empty($errors['content_type']) && empty($errors['subchapter_paragraph'])) {
    header("Location: ../trainings/content.php?t_id=$t_id");
  }
} else if (strcmp($action_type, ACTION_TYPE_CHANGE_POSITION) === 0) {
  // get url params
  $tcc_id = post_data("tcc_id");
  $type = post_data("type");

  if (empty($tcc_id) || empty($type)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training core content ID and change type parameters are required"));
    exit();
  }

  // require trainings content controller and instantiate
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $contentController = new TrainingsContentController();

  // update subchapter
  $contentController->changePosition($tcc_id, $type);

  // http response
  header("HTTP/1.1 204 No Content");
  exit();
} else if (strcmp($action_type,ACTION_TYPE_CHANGE_STATUS_VR) === 0){
  $t_id = get_data("t_id");
  $vrstats = $_POST['vrstats'];
  if (empty($t_id) || empty($vrstats)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Training core contents ID  parameters are required"));
    exit();
  }

  // require trainings content controller and instantiate
  require_once __DIR__ . '/../classes/trainings/content-contr.class.php';
  $contentController = new TrainingsContentController();
  $contentController->updateVRStat($t_id, $vrstats);
  header("HTTP/1.1 204 No Content");
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

function removeItem($items, $thing)
{
  $arr_tmp = array();
  foreach ($items as $data) {
    if (strcmp($data, $thing) !== 0) {
      array_push($arr_tmp, $data);
    }
  }

  return $arr_tmp;
}

function removeItemById($items, $id, $fieldname)
{
  $arr_tmp = array();
  foreach ($items as $data) {
    if (strcmp($data[$fieldname], $id) !== 0) {
      array_push($arr_tmp, $data);
    }
  }

  return $arr_tmp;
}
