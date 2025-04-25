<?php

$currUrl = $_SERVER['REQUEST_URI'];

define("ACTION_TYPE_GET", "MACT01");
define("ACTION_TYPE_DELETE", "MACT02");
define("ACTION_TYPE_CREATE", "MACT03");
define("ACTION_TYPE_UPDATE", "MACT04");
define("ACTION_TYPE_GET_MATERIALS", "MACT05");
define("ACTION_TYPE_DELETE_MATERIAL", "MACT06");
define("ACTION_TYPE_CREATE_MATERIAL", "MACT07");
define("ACTION_TYPE_UPDATE_MATERIAL", "MACT08");
define("ACTION_TYPE_GET_CHAPTERS", "MACT09");
define("ACTION_TYPE_DELETE_CHAPTER", "MACT10");
define("ACTION_TYPE_CREATE_CHAPTER", "MACT11");
define("ACTION_TYPE_UPDATE_CHAPTER", "MACT12");
define("ACTION_TYPE_GET_CONTENT", "MACT013");
define("ACTION_TYPE_DELETE_CONTENT", "MACT14");
define("ACTION_TYPE_CREATE_CONTENT", "MACT15");
define("ACTION_TYPE_UPDATE_CONTENT", "MACT16");
define("ACTION_TYPE_CREATE_SUBCHAPTER", "MACT17");
define("ACTION_TYPE_UPDATE_SUBCHAPTER", "MACT18");
define("ACTION_TYPE_DELETE_SUBCHAPTER", "MACT19");
define("ACTION_TYPE_CHANGE_POSITION", "MACT20");
define("ACTION_TYPE_CHANGE_STATUS_VR", "MACT21");

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

$mtx_id = '';
$department_name = '';
$file_type = '';
$file = '';
$dpt_id = '';
$upload_year = '';
$dsch_id = '';
$dch_id = '';
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
$department = array();

$departments = array();
$chapters = array();
$material = array();
$materials = array();
$currPage = 1;

$errors = array(
  'mtx_id' => '',
  'file_type' => '',
  'file' => '',
  'dpt_id' => '',
  'upload_year' => '',
  'department_name' => '',
  'dch_id' => '',
  'dsch_id' => '',
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
);

// training content variables
$dch_id = '';
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
define("FILE_TYPE_PARAGRAPH", 1);
define("FILE_TYPE_IMAGE", 2);
define("FILE_TYPE_VIDEO", 3);
define("FILE_TYPE_PDF", 4);
define("FILE_TYPE_LINK", 5);
define("FILE_TYPE_UNORDERED_LIST", 6);
define("FILE_TYPE_ORDERED_LIST", 7);

// arrs
$chapter = array(
  'DCH_ID' => '',
  'TITLE' => '',
  'DESCRIPTION' => ''
);
$subchapters = array();
$subchapters_contents = array();

if (strcmp($action_type, ACTION_TYPE_GET) === 0) {
  // require trainings controller and create instance
  require_once __DIR__ . '/../classes/matrix/matrix-contr.class.php';
  $departmentsContr = new MatrixController();

  // get Department id
  if (isset($_GET['dpt_id']) && !empty($_GET['dpt_id'])) {
    // get params
    $dpt_id = get_data('dpt_id');

    // get single Department
    $department = $departmentsContr->getDepartment($dpt_id);

    // return responses
    if ($department === false) {
      header("HTTP/1.1 204 No Content");
      echo json_encode(array("status"=> false));
    } else {
      header("Content-type: application/json");
      echo json_encode(
        array(
          "department" => $department
        )
      );
    }
    exit();
  }

  // get Departments with total lists shown
  if (isset($_GET['lists_shown']) && !empty($_GET['lists_shown']) && filter_var($_GET['lists_shown'], FILTER_VALIDATE_INT)) {
    $lists_shown = get_data('lists_shown');
  }

  // get all Departments
  if (isset($_GET['page']) && !empty($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT)) {
    $currPage = get_data('page');
  }

  // search filter Departments
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


  // get all Departments count
  $departmentsCountTotal = $departmentsContr->getAllDepartmentsCount();

  // get Departments
  $departments = $departmentsContr->getDepartments($page, $lists_shown, $search,$colomIndex,$direction);

  if ($departments === false) {
    header("HTTP/1.1 204 No Content");
    exit();
  }

  header("Content-type: application/json");
  echo json_encode(array("departments_count" => $departmentsCountTotal, "departments" => $departments));
  exit();
} else if ($action_type == ACTION_TYPE_GET_MATERIALS) {
  // require dpt_id param
  if (!isset($_GET['dpt_id']) || !$_GET['dpt_id']) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department ID parameter is required"));
    exit();
  }

  // get url parameters
  $dpt_id = get_data('dpt_id');
  $m_id = get_data('m_id');

  // require classes and instantiate contentController
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // get Department name
  $department = $departmentsContentController->getDepartmentName($dpt_id);

  if ($department === false) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Invalid department ID"));
    exit();
  }

  // get single material
  if (isset($m_id) && !empty($m_id)) {
    // get department material
    $material = $departmentsContentController->getDepartmentMaterial($m_id);

    if ($material === false) {
      header("HTTP/1.1 204 No Content");
    } else {
      header("Content-type: application/json");
      echo json_encode(array("department_name" => $department, "department_material" => $material));
    }
    exit();
  }

  // get department materials
  $materials = $departmentsContentController->getDepartmentMaterials($dpt_id);

  if ($materials === false) {
    header("HTTP/1.1 204 No Content");
    exit();
  } else {
    header("Content-type: application/json");
    echo json_encode(array("department_name" => $department, "department_materials" => $materials));
    exit();
  }
} else if ($action_type == ACTION_TYPE_DELETE_MATERIAL) {
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // get url params
  $dpt_id = get_data('dpt_id');
  $m_id = get_data('m_id');

  if (!isset($m_id) || empty($m_id) || !isset($dpt_id) || empty($dpt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Material and department ID parameter is required"));
    exit();
  }

  // delete material
  $departmentsContentController->deleteMaterial($m_id);

  // redirect to current page
  header("Location: ../matrix/materials.php?dpt_id=$dpt_id");
} else if ($action_type == ACTION_TYPE_CREATE_MATERIAL) {
  // require dpt_id param
  if (!isset($_GET['dpt_id']) || !$_GET['dpt_id']) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department ID parameter is required"));
    exit();
  }

  // get dpt_id
  $dpt_id = get_data('dpt_id');

  // require classes and instantiate contentController
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // create new department chapter
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $raw_m_id = $departmentsContentController->getLatestDepartmentMaterialId($dpt_id);
    $m_id = empty($raw_m_id) ? "M0000" : "M" . str_pad(strval((int) substr($raw_m_id, 1) + 1), 4, "0", STR_PAD_LEFT);
    $material_title = post_data('material_title');
    $material_description = post_data('material_description');

    // create new material
    $errors = array_merge($errors, $departmentsContentController->createDepartmentMaterial($dpt_id, $m_id, $material_title, $material_description));

    // redirect to department chapters page
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
  // get dpt_id and m_id
  $dpt_id = get_data('dpt_id');
  $m_id = get_data('m_id');

  // require dpt_id param
  if (empty($dpt_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department and material ID parameter is required"));
    exit();
  }

  // get post datas
  $material_title = post_data('material_title');
  $material_description = post_data('material_description');

  // require and instantiate controller
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // update chapter
  $errors = array_merge($errors, $departmentsContentController->updateDepartmentMaterial($m_id, $material_title, $material_description));

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
  $dpt_id = get_data('dpt_id');
  $m_id = get_data('m_id');
  $dch_id = get_data('dch_id');
  
  // require url params
  if (empty($dpt_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department and material ID parameters are required"));
    exit();
  }

  // require classes and instantiate contentController
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // get department name
  $department = $departmentsContentController->getDepartmentName($dpt_id);
  $material = $departmentsContentController->getMaterial($m_id);

  if (isset($dch_id) && !empty($dch_id)) {
    // get single department chapter
    $chapter = $departmentsContentController->getDepartmentChapter($dch_id);

    if ($chapter === false || $department === false || $material === false) {
      header("HTTP/1.1 204 No Content");
    } else {
      header("Content-type: application/json");
      echo json_encode(
        array(
          "department" => $department,
          "material" => $material,
          "chapter" => $chapter
        )
      );
    }
    exit();
  }

  // get all department chapters
  $chapters = $departmentsContentController->getDepartmentChapters($m_id);

  if ($department === false || $material === false || $chapters === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode(
      array(
        "department" => $department,
        "material" => $material,
        "chapters" => $chapters
      )
    );
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_DELETE_CHAPTER) === 0) {
  // get post datats
  $dch_id = get_data('dch_id');
  $dpt_id = get_data('dpt_id');
  $m_id = get_data('m_id');

  if (empty($dch_id) || empty($m_id) || empty($dpt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Chapter, department, and material ID parameters are required"));
    exit();
  }

  // require controller and create instance
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // delete chapter
  $departmentsContentController->deleteChapter($dch_id);

  // redirect to current page on success
  header("Location: ../matrix/chapters.php?dpt_id=$dpt_id&m_id=$m_id");
} else if (strcmp($action_type, ACTION_TYPE_CREATE_CHAPTER) === 0) {
  // get url params
  $dpt_id = get_data('dpt_id');
  $m_id = get_data('m_id');

  if (empty($dpt_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department and material ID parameters are required"));
    exit();
  }

  // require and instantiate departmentsContentController
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // get post datas
  $raw_dch_id = $departmentsContentController->getLatestDepartmentChapterId($dpt_id);
  $dch_id = empty($raw_dch_id) ? "DCH0000" : "DCH" . str_pad(strval((int) substr($raw_dch_id, 3) + 1), 4, "0", STR_PAD_LEFT);
  $chapter_title = post_data('chapter_title');
  $chapter_description = post_data('chapter_description');

  // create new department chapter
  $errors = array_merge($errors, $departmentsContentController->createDepartmentChapter($dpt_id, $m_id, $dch_id, $chapter_title, $chapter_description));

  // redirect to department chapters page
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
  $dch_id = get_data('dch_id');
  $m_id = get_data('m_id');

  if (empty($dch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department chapter and material ID parameters are required"));
    exit();
  }

  // get post datas
  $chapter_title = post_data('chapter_title');
  $chapter_description = post_data('chapter_description');

  // require controller and instantiation
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // update chapter
  $errors = array_merge($errors, $departmentsContentController->updateDepartmentChapter($dch_id, $chapter_title, $chapter_description));

  // responses
  if (empty($errors['dch_id']) && empty($errors['chapter_title']) && empty($errors['chapter_description']) && empty($errors['m_id'])) {
    header("HTTP/1.1 200 OK");
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_GET_CONTENT) === 0) {
  // get dpt_id, m_id and dch_id
  $dpt_id = get_data('dpt_id');
  $year = get_data('year');
  error_log("Received year: " . $year);

  // require dpt_id param
  if (empty($dpt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department ID parameter is required"));
    exit();
  }

  // require content controller and instantiate it
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // get subchapter contents
  $fileDocs = $departmentsContentController->getDepartmentSubchaptersContents($dpt_id, $year);
  $years = $departmentsContentController->getYears();

  // JSON responses
  if ($fileDocs === false || $years === false) {
    header("HTTP/1.1 204 No Content");
  } else {
    header("Content-type: application/json");
    echo json_encode(array(
      "fileDocs" => $fileDocs,
      "years" => $years
    ));  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_CREATE_CONTENT) === 0) {
  // get url params
  $dpt_id = get_data("dpt_id");
  $year = date('Y');

  if (empty($dpt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department ID parameter is required"));
    exit();
  }

  require_once __DIR__ . '/../classes/matrix/matrix.class.php';
  $departmentsController = new Matrix();

  if ($departmentsController->contentExists($dpt_id, $year)) {
    header("HTTP/1.1 400 Bad Request");
    // header("Content-type: application/json");
    echo "<script type='text/javascript'>alert('Data for the year $year already exists.'); 
    window.location.href='../matrix/content.php?dpt_id=$dpt_id';</script>";
    exit();
  }
  
  // require and instantiate departmentsContentController
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // file upload
  if (isset($_FILES['file'])) {
    // get file datas
    $raw_mtx_id = $departmentsContentController->getLatestCoreContentId();
    $mtx_id = empty($raw_mtx_id) ? "MTX00001" : "MTX" . str_pad(strval((int) substr($raw_mtx_id, 3) + 1), 5, "0", STR_PAD_LEFT);
    $file = $_FILES['file'];
    $subchapter_file_name = $file['name'];
    $subchapter_file_size = $file['size'];

    // get post datas
    $file_type = post_data('file_type');

    // upload file
    $errors = array_merge($errors, $departmentsContentController->uploadFile($mtx_id, $dpt_id, $file_type, $subchapter_file_name, $subchapter_file_size));

    // responses
    if (empty($errors['mtx_id']) && empty($errors['dpt_id']) && empty($errors['file_type']) && empty($errors['subchapter_file_size']) && empty($errors['subchapter_file_name'])) {
      // store file
      move_uploaded_file($file['tmp_name'], __DIR__ . '/../public/imgs/uploads/' . $subchapter_file_name);

      // redirect to content page
      header("Location: ../matrix/content.php?dpt_id=$dpt_id");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array("mtx_id" => $errors['mtx_id'], "file_type" => $errors['file_type'], "file_size" => $errors['subchapter_file_size'], "file_name" => $errors['subchapter_file_name']));
    }
  } else {
    // get post datas
    $raw_mtx_id = $departmentsContentController->getLatestCoreContentId();
    $mtx_id = empty($raw_mtx_id) ? "MTX00001" : "MTX" . str_pad(strval((int) substr($raw_mtx_id, 3) + 1), 5, "0", STR_PAD_LEFT);
    $file = post_data('file');
    $file_type = post_data('file_type');

    // create new core content
    $errors = array_merge($errors, $departmentsContentController->createCoreContent($mtx_id, $dpt_id, $file_type, $file));

    // responses
    if (empty($errors['file']) && empty($errors['dpt_id']) && empty($errors['mtx_id']) && empty($errors['file_type'])) {
      header("HTTP/1.1 201 Created");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array($errors['file'], $errors['mtx_id'], $errors['file_type']));
    }
  }

  exit();

  // redirect to current page if there's no errors
  if (empty($errors['mtx_id']) && empty($errors['sch_id']) && empty($errors['file_type']) && empty($errors['subchapter_paragraph'])) {
    header("Location: ../matrix/chapters/content.php?dpt_id=$dpt_id&dch_id=$dch_id&m_id=$m_id&error=none");
  }
} else if (strcmp($action_type, ACTION_TYPE_CREATE_SUBCHAPTER) === 0) {
  // get url params
  $dpt_id = get_data("dpt_id");
  $dch_id = get_data("dch_id");
  $m_id = get_data("m_id");

  if (empty($dpt_id) || empty($dch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department, chapter, subchapter, and material ID parameters are required"));
    exit();
  }

  // get post datas
  $subchapter_title = post_data('subchapter_title');

  // require department content controller and instantiate it
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $contentsController = new MatrixContentController();

  // create subchapter id
  $raw_sch_id = $contentsController->getLatestSubchapterId();
  $sch_id = empty($raw_sch_id) ? "SCH0001" : "SCH" . str_pad(strval((int) substr($raw_sch_id, 3) + 1), 4, "0", STR_PAD_LEFT);

  // create new department subchapter
  $errors = array_merge($errors, $contentsController->createSubchapter($dch_id, $sch_id, $subchapter_title));

  // responses
  if (empty($errors['dch_id']) && empty($errors['sch_id']) && empty($errors['subchapter_title'])) {
    header("HTTP/1.1 201 Created");
    header("Content-type: application/json");
    echo json_encode(array("success" => "Department subchapter is created"));
  } else {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode($errors);
  }
  exit();
} else if (strcmp($action_type, ACTION_TYPE_DELETE_SUBCHAPTER) === 0) {
  // get url params
  $sch_id = get_data("sch_id");
  $dpt_id = get_data("dpt_id");
  $dch_id = get_data("dch_id");
  $m_id = get_data("m_id");

  if (empty($sch_id) || empty($dpt_id) || empty($dch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department, chapter, subchapter, and material ID parameters are required"));
    exit();
  }

  // require departments content controller and instantiate
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $fileController = new MatrixContentController();

  // delete subchapter
  $fileController->deleteSubchapter($sch_id);

  // redirect to content page
  header("Location: ../matrix/chapters/content.php?dpt_id=$dpt_id&dch_id=$dch_id&m_id=$m_id");
} else if (strcmp($action_type, ACTION_TYPE_UPDATE_SUBCHAPTER) === 0) {
  // get url params
  $sch_id = get_data("sch_id");
  $dpt_id = get_data("dpt_id");
  $dch_id = get_data("dch_id");
  $m_id = get_data("m_id");

  if (empty($sch_id) || empty($dpt_id) || empty($dch_id) || empty($m_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "department, chapter, subchapter, and material ID parameters are required"));
    exit();
  }

  // require departments content controller and instantiate
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $fileController = new MatrixContentController();

  // get post datas
  $subchapter_title = post_data('subchapter_title');

  // update subchapter
  $errors = array_merge($errors, $fileController->updateDepartmentSubchapter($sch_id, $subchapter_title));

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
  $mtx_id = get_data("mtx_id");

  if (empty($mtx_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "department core content ID parameter is required"));
    exit();
  }

  // require departments content controller and instantiate
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $fileController = new MatrixContentController();

  // update subchapter
  $fileController->deleteContent($mtx_id);

  // http response
  header("HTTP/1.1 204 No Content");
  exit();
} else if (strcmp($action_type, ACTION_TYPE_UPDATE_CONTENT) === 0) {
  // get url params
  $dpt_id = get_data("dpt_id");
  $mtx_id = get_data("mtx_id");

  if (empty($mtx_id) || empty($dpt_id)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department and department core content ID parameters are required"));
    exit();
  }

  // require and instantiate MatrixContentController
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $departmentsContentController = new MatrixContentController();

  // file upload
  if (isset($_FILES['file'])) {
    // get file datas
    $file = $_FILES['file'];
    $subchapter_file_name = $file['name'];
    $subchapter_file_size = $file['size'];

    // get post datas
    $file_type = post_data('file_type');

    // upload file
    $errors = array_merge($errors, $departmentsContentController->uploadFile($mtx_id, $dpt_id, $file_type, $subchapter_file_name, $subchapter_file_size));

    // responses
    if (empty($errors['mtx_id']) && empty($errors['dpt_id']) && empty($errors['file_type']) && empty($errors['subchapter_file_name']) && empty($errors['subchapter_file_size'])) {
      // store file
      move_uploaded_file($file['tmp_name'], __DIR__ . '/../public/imgs/uploads/' . $subchapter_file_name);

      // redirect to content page
      header("Location: ../matrix/content.php?dpt_id=$dpt_id");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array("file" => empty($errors['subchapter_file_size']) ? $errors['subchapter_file_name'] : $errors['subchapter_file_size'], "mtx_id" => $errors['mtx_id'], "file_type" => $errors['file_type']));
    }
  } else {
    // get post datas
    $file = post_data('file');
    $file_type = post_data('file_type');

    // create new core content
    $errors = array_merge($errors, $departmentsContentController->updateDepartmentContent($mtx_id, $file_type, $file));

    // responses
    if (empty($errors['file']) && empty($errors['mtx_id']) && empty($errors['file_type'])) {
      header("HTTP/1.1 200 OK");
    } else {
      header("HTTP/1.1 400 Bad Request");
      header("Content-type: application/json");
      echo json_encode(array("file" => $errors['file'], "mtx_id" => $errors['mtx_id'], "file_type" => $errors['file_type']));
    }
  }

  // redirect to current page if there's no errors
  if (empty($errors['mtx_id']) && empty($errors['sch_id']) && empty($errors['file_type']) && empty($errors['subchapter_paragraph'])) {
    header("Location: ../matrix/content.php?dpt_id=$dpt_id");
  }
} else if (strcmp($action_type, ACTION_TYPE_CHANGE_POSITION) === 0) {
  // get url params
  $mtx_id = post_data("mtx_id");
  $type = post_data("type");

  if (empty($mtx_id) || empty($type)) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-type: application/json");
    echo json_encode(array("error" => "Department core content ID and change type parameters are required"));
    exit();
  }

  // require departments content controller and instantiate
  require_once __DIR__ . '/../classes/matrix/content-contr.class.php';
  $fileController = new MatrixContentController();

  // update subchapter
  $fileController->changePosition($mtx_id, $type);

  // http response
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
