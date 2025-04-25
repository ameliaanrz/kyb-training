<?php

require_once __DIR__ . '/matrix.class.php';

class MatrixContentController extends Matrix
{
  private $mtx_id = '';
  private $dpt_id = '';
  private $file = '';
  private $upload_year = '';
  private $file_type = '';
  private $dch_id = '';
  private $tch_id = '';
  private $sch_id = '';
  private $m_id = '';
  private $tcc_id = '';
  private $chapter_title = '';
  private $chapter_description = '';
  private $material_title = '';
  private $material_description = '';
  private $subchapter_title = '';
  private $subchapter_link = '';
  private $content_type = '';
  private $subchapter_paragraph = '';
  private $list_content = '';
  private $subchapter_file_name = '';
  private $subchapter_file_size = '';
  private $errors = array(
    'mtx_id' => '',
    'dpt_id' => '',
    'file_type' => '',
    'file' => '',
    'upload_year' => '',
    'dch_id' => '',
    't_id' => '',
    'm_id' => '',
    'tch_id' => '',
    'sch_id' => '',
    'tcc_id' => '',
    'chapter_title' => '',
    'subchapter_title' => '',
    'subchapter_link' => '',
    'subchapter_list' => '',
    'vrstats' => '',
    'material_title' => '',
    'chapter_description' => '',
    'material_description' => '',
    'content_type' => '',
    'subchapter_paragraph' => '',
    'subchapter_file_name' => '',
  );

  public function changePosition($mtx_id, $type)
  {
    if (strcmp($type, 'down') === 0) {
      /**
       * move position down
       * -----------------
       * 1. get next TCC_ID
       * 2. change current TCC_ID to the largest possible TCC_ID
       * 3. change next TCC_ID to current TCC_ID
       * 4. change current TCC_ID to next TCC_ID
       */
      $next_mtx_id = $this->getNextMTXID($mtx_id);
      if (!empty($next_mtx_id)) {
        // change current mtx_id to the largest possible mtx_id
        $this->changeMtxId($mtx_id, 'MTX99999');
        // change next mtx_id to curr mtx_id
        $this->changeMtxId($next_mtx_id, $mtx_id);
        // change current mtx_id to next mtx_id
        $this->changeMtxId('MTX99999', $next_mtx_id);
      }
    } else {
      /**
       * move up down
       * -----------------
       * 1. get prev mtx_id
       * 2. change current mtx_id to the largest possible mtx_id
       * 3. change prev mtx_id to current mtx_id
       * 4. change current mtx_id to prev mtx_id
       */
      $prev_mtx_id = $this->getPrevMTXID($mtx_id);
      if (!empty($prev_mtx_id)) {
        // change current mtx_id to the largest possible mtx_id
        $this->changeMtxId($mtx_id, 'MTX99999');
        // change prev mtx_id to curr mtx_id
        $this->changeMtxId($prev_mtx_id, $mtx_id);
        // change current mtx_id to prev mtx_id
        $this->changeMtxId('MTX99999', $prev_mtx_id);
      }
    }
  }

  public function getDepartmentName($dpt_id)
  {
    $result = $this->getDepartmentNameById($dpt_id);
    return empty($result) ? false : $result['DEPARTMENT'];
  }

  public function getMaterial($m_id)
  {
    $result = $this->getMaterialById($m_id);
    return empty($result) ? false : $result;
  }

  public function getDepartmentMaterials($dpt_id)
  {
    $result = $this->getAllDepartmentMaterials($dpt_id);
    return empty($result) ? false : $result;
  }

  public function getDepartmentChapters($m_id)
  {
    $result = $this->getAllDepartmentChapters($m_id);
    return empty($result) ? false : $result;
  }

  public function getDepartmentMaterial($m_id)
  {
    return $this->getDepartmentMaterialById($m_id);
  }

  public function getDepartmentChapter($dch_id)
  {
    $result = $this->getDepartmentChapterById($dch_id);
    return empty($result) ? false : $result;
  }

  public function getLatestDepartmentChapterId($dpt_id)
  {
    $result = $this->getLatestDepartmentContentChapterId($dpt_id);
    return empty($result) ? false : $result['TCH_ID'];
  }

  public function getLatestDepartmentMaterialId($dpt_id)
  {
    $result = $this->getLatestDepartmentContentMaterialId($dpt_id);
    return empty($result) ? false : $result['M_ID'];
  }

  public function getLatestSubchapterId()
  {
    $result = $this->getCurrentLatestSubchapterId();
    return empty($result) ? false : $result['DSCH_ID'];
  }

  public function getLatestCoreContentId()
  {
    $result = $this->getCurrentLatestCoreContentId();
    return empty($result) ? false : $result['MTX_ID'];
  }

  public function getDepartmentSubchaptersContents($dpt_id, $year)
  {
    error_log("Fetching contents for dpt_id: " . $dpt_id);

    $result = $this->getAllDepartmentSubchaptersContents($dpt_id, $year);
    error_log("Fetching contents for dpt_id: " . $dpt_id);

    if (empty($result)) {
        return false;
    }
    return $result;
  }

  public function getSubchapters($dch_id)
  {
    $result = $this->getCurrentSubchapters($dch_id);
    return empty($result) ? false : $result;
  }

  public function getYears(){
    $result = $this->getAllYears();
    return empty($result) ? false : $result;
  }

  public function createCoreContent($mtx_id, $dpt_id, $file_type, $file)
{
    $this->mtx_id = $mtx_id;
    $this->dpt_id = $dpt_id;
    $this->file_type = $file_type;
    $this->file = $file;
    $this->upload_year = date('Y'); // Set the upload date here
    
    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['mtx_id']) && empty($this->errors['dpt_id']) && empty($this->errors['file_type']) && empty($this->errors['file']) && empty($this->errors['upload_year'])) {
        $this->createNewCoreContent($this->mtx_id, $this->dpt_id, $this->file_type, $this->file, $this->upload_year);
    }

    // return errors
    return $this->errors;
}
  public function createList($mtx_id, $dsch_id, $file_type, $list_content)
  {
    $this->mtx_id = $mtx_id;
    $this->sch_id = $dsch_id;
    $this->file_type = $file_type;
    $this->list_content = $list_content;
    $this->upload_year = date('Y'); // Set the upload date here


    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['mtx_id']) && empty($this->errors['sch_id']) && empty($this->errors['file_type']) && empty($this->errors['subchapter_list'])) {
      $this->createNewCoreContent($this->mtx_id, $this->sch_id, $this->file_type, $this->list_content, $this->upload_year);
    }

    // return errors
    return $this->errors;
  }

  public function createLink($mtx_id, $dsch_id, $file_type, $subchapter_link)
  {
    $this->mtx_id = $mtx_id;
    $this->sch_id = $dsch_id;
    $this->file_type = $file_type;
    $this->upload_year = date('Y'); // Set the upload date here
    $this->subchapter_link = $subchapter_link;

    // error checks
    $this->invalidLink();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['mtx_id']) && empty($this->errors['sch_id']) && empty($this->errors['file_type']) && empty($this->errors['subchapter_link'])) {
      $this->createNewCoreContent($this->mtx_id, $this->sch_id, $this->file_type, $this->subchapter_link, $this->upload_year);
    }

    // return errors
    return $this->errors;
  }

  public function uploadFile($mtx_id, $dpt_id, $file_type, $subchapter_file_name, $subchapter_file_size)
  {
    $this->mtx_id = $mtx_id;
    $this->dpt_id = $dpt_id;
    $this->file_type = $file_type;
    $this->upload_year = date('Y'); // Set the upload date here
    $this->subchapter_file_name = $subchapter_file_name;
    $this->subchapter_file_size = $subchapter_file_size;

    // error checks
    $this->invalidFileType();
    $this->fileSizeTooBig();
    $this->emptyField();

    if (!empty($this->errors)) {
      error_log("Upload File Errors: " . json_encode($this->errors));
  }
    // create new Department chapter
    if (empty($this->errors['mtx_id']) && empty($this->errors['dpt_id']) && empty($this->errors['file_type']) && empty($this->errors['subchapter_file_name']) && empty($this->errors['subchapter_file_size'])) {
      if ($this->getDepartmentContent($this->mtx_id)) {
        $this->updateCurrDepartmentContent($this->mtx_id, $this->file_type, $this->subchapter_file_name, $this->upload_year);
      } else {
        $this->createNewCoreContent($this->mtx_id, $this->dpt_id, $this->file_type, $this->subchapter_file_name, $this->upload_year);
      }
    }

    // return errors
    return $this->errors;
  }

  public function createSubchapter($dch_id, $sch_id, $subchapter_title)
  {
    $this->dch_id = $dch_id;
    $this->sch_id = $sch_id;
    $this->subchapter_title = $subchapter_title;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['dch_id']) && empty($this->errors['sch_id']) && empty($this->errors['subchapter_title'])) {
      $this->createNewSubchapter($this->dch_id, $this->sch_id, $this->subchapter_title);
    }

    // return errors
    return $this->errors;
  }

  public function createDepartmentChapter($dpt_id, $m_id, $tch_id, $title, $description)
  {
    $this->dpt_id = $dpt_id;
    $this->m_id = $m_id;
    $this->tch_id = $tch_id;
    $this->chapter_title = $title;
    $this->chapter_description = $description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['dpt_id']) && empty($this->errors['m_id']) && empty($this->errors['tch_id']) && empty($this->errors['chapter_title']) && empty($this->errors['chapter_description'])) {
      $this->createNewDepartmentChapter($this->dpt_id, $this->m_id, $this->tch_id, $this->chapter_title, $this->chapter_description);
    }

    // return errors
    return $this->errors;
  }

  public function createDepartmentMaterial($dpt_id, $m_id, $title, $description)
  {
    $this->dpt_id = $dpt_id;
    $this->m_id = $m_id;
    $this->material_title = $title;
    $this->material_description = $description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['dpt_id']) && empty($this->errors['m_id']) && empty($this->errors['material_title']) && empty($this->errors['material_description'])) {
      $this->createNewDepartmentMaterial($this->dpt_id, $this->m_id, $this->material_title, $this->material_description);
    }

    // return errors
    return $this->errors;
  }

  public function updateDepartmentMaterial($m_id, $material_title, $material_description)
  {
    $this->m_id = $m_id;
    $this->material_title = $material_title;
    $this->material_description = $material_description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['m_id']) && empty($this->errors['material_title']) && empty($this->errors['material_description'])) {
      $this->updateCurrentDepartmentMaterial($this->m_id, $this->material_title, $this->material_description);
    }

    // return errors
    return $this->errors;
  }

  public function updateDepartmentChapter($dch_id, $title, $description)
  {
    $this->dch_id = $dch_id;
    $this->chapter_title = $title;
    $this->chapter_description = $description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new Department chapter
    if (empty($this->errors['tch_id']) && empty($this->errors['chapter_title']) && empty($this->errors['chapter_description'])) {
      $this->updateCurrentDepartmentChapter($this->dch_id, $this->chapter_title, $this->chapter_description);
    }

    // return errors
    return $this->errors;
  }

  public function updateDepartmentSubchapter($sch_id, $subchapter_title)
  {
    $this->sch_id = $sch_id;
    $this->subchapter_title = $subchapter_title;

    // check errors
    $this->notLongEnough();
    $this->emptyField();

    if (empty($this->errors['sch_id']) && empty($this->errors['subchapter_title'])) {
      $this->updateCurrentDepartmentSubchapter($sch_id, $subchapter_title);
    }

    return $this->errors;
  }

  public function updateDepartmentContent($mtx_id, $file_type, $file)
  {
    $this->mtx_id = $mtx_id;
    $this->file_type = $file_type;
    $this->file = $file;
    $this->upload_year = date('Y'); // Set the upload date here

    // check for errors
    $this->emptyField();

    // if no errors update Department
    if (empty($this->errors['mtx_id']) && empty($this->errors['file_type']) && empty($this->errors['file'])) {
      $this->updateCurrDepartmentContent($this->mtx_id, $this->file_type, $this->file, $this->upload_year);
    }

    // return errors
    return $this->errors;
  }

  public function reuploadFile($mtx_id, $file_type, $subchapter_content, $file_size)
  {
    $this->mtx_id = $mtx_id;
    $this->file_type = $file_type;
    //$this->upload_year = date('Y'); // Set the upload date here
    $this->subchapter_file_name = $subchapter_content;
    $this->subchapter_file_size = $file_size;

    // check for errors
    $this->fileSizeTooBig();
    $this->invalidFileType();
    $this->emptyField();

    // if no errors update Department
    if (empty($this->errors['mtx_id']) && empty($this->errors['file_type']) && empty($this->errors['subchapter_file_name']) && empty($this->errors['subchapter_file_size'])) {
      $this->updateCurrDepartmentContent($this->mtx_id, $this->file_type, $this->subchapter_file_name, $this->upload_year);
    }

    // return errors
    return $this->errors;
  }

  public function deleteContent($mtx_id)
  {
    return $this->deleteCurrContent($mtx_id);
  }

  public function deleteDepartment($dpt_id)
  {
    return $this->deleteCurrDepartment($dpt_id);
  }

  public function deleteMaterial($m_id)
  {
    return $this->deleteCurrMaterial($m_id);
  }

  public function deleteChapter($dch_id)
  {
    return $this->deleteCurrChapter($dch_id);
  }

  public function deleteSubchapter($dsch_id)
  {
    return $this->deleteCurrSubchapter($dsch_id);
  }

  private function emptyField()
  {
    $errs = array();
    define("REQUIRED_INPUT_ERROR", "*This field is required");

    if (empty($this->dpt_id)) {
      $errs['dpt_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->dch_id)) {
      $errs['dch_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->sch_id)) {
      $errs['sch_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->chapter_title)) {
      $errs['chapter_title'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->material_title)) {
      $errs['material_title'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_title)) {
      $errs['subchapter_title'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->chapter_description)) {
      $errs['chapter_description'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->material_description)) {
      $errs['material_description'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->mtx_id)) {
      $errs['mtx_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->file_type)) {
      $errs['file_type'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_paragraph)) {
      $errs['subchapter_paragraph'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_file_name)) {
      $errs['subchapter_file_name'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_file_size)) {
      $errs['subchapter_file_size'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->list_content)) {
      $errs['subchapter_list'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_link)) {
      $errs['subchapter_link'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->file)) {
      $errs['file'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->upload_year)) {
      $errs['upload_year'] = REQUIRED_INPUT_ERROR;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function invalidFileType()
  {
    $errs = array();

    define("INVALID_PDF_TYPE_ERROR", "*File must be of type pdf");
    define("INVALID_IMAGE_TYPE_ERROR", "*Image type must be jpg, jpeg, svg, png, gif, or tiff");
    define("INVALID_VIDEO_TYPE_ERROR", "*Video type must be mp4, mov, wmv, avi, flv, mkv, or webm");

    $allowedImgs = array('jpg', 'jpeg', 'svg', 'png', 'gif', 'tiff', 'webp');
    $allowedVids = array('mp4', 'mov', 'wmv', 'avi', 'flv', 'mkv', 'webm');

    // get file extension
    $file_ext = strtolower(pathinfo($this->subchapter_file_name, PATHINFO_EXTENSION));

    // checks image types
    if ($this->file_type == FILE_TYPE_IMAGE && !in_array($file_ext, $allowedImgs)) {
      $errs['file_type'] = INVALID_IMAGE_TYPE_ERROR;
    }

    // checks video types
    else if ($this->file_type == FILE_TYPE_VIDEO && !in_array($file_ext, $allowedVids)) {
      $errs['file_type'] = INVALID_VIDEO_TYPE_ERROR;
    }

    // checks pdf type
    else if ($this->file_type == FILE_TYPE_PDF && strcmp($file_ext, "pdf") !== 0) {
      $errs['file_type'] = INVALID_PDF_TYPE_ERROR;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function fileSizeTooBig()
  {
    $errs = array();

    define("IMAGE_SIZE_ERROR", "*Image size should not exceed 5 MB");
    define("VIDEO_SIZE_ERROR", "*Video size should not exceed 500 MB");

    // checks image file size
    if (($this->file_type == FILE_TYPE_IMAGE || $this->file_type == FILE_TYPE_PDF) && $this->subchapter_file_size > 5 * 1024 * 1024) {
      $errs['subchapter_file_name'] = IMAGE_SIZE_ERROR;
    }

    // checks video file size
    else if ($this->file_type == FILE_TYPE_VIDEO && $this->subchapter_file_size > 500 * 1024 * 1024) {
      $errs['subchapter_file_name'] = VIDEO_SIZE_ERROR;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function invalidLink()
  {
    $errs = array();

    if (!filter_var($this->subchapter_link, FILTER_VALIDATE_URL)) {
      $errs['subchapter_link'] = '*Invalid URL';
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function notLongEnough()
  {
    $errs = array();
    define("STRLEN_ERROR", "*Characters length must be between 4 and 100");

    if (strlen($this->chapter_title) < 4 || strlen($this->chapter_title) > 100) {
      $errs['chapter_title'] = STRLEN_ERROR;
    }
    if (strlen($this->material_title) < 4 || strlen($this->material_title) > 100) {
      $errs['material_title'] = STRLEN_ERROR;
    }
    if (strlen($this->subchapter_title) < 4 || strlen($this->subchapter_title) > 100) {
      $errs['subchapter_title'] = STRLEN_ERROR;
    }
    if (strlen($this->chapter_description) < 4 || strlen($this->chapter_description) > 1000) {
      $errs['chapter_description'] = "*Characters length must be between 4 and 1000";
    }
    if (strlen($this->material_description) < 4 || strlen($this->material_description) > 1000) {
      $errs['material_description'] = "*Characters length must be between 4 and 1000";
    }
    if (strlen($this->subchapter_paragraph) < 4 || strlen($this->subchapter_paragraph) > 1000) {
      $errs['subchapter_paragraph'] = "*Characters length must be between 4 and 1000";
    }
    foreach (explode(',', $this->list_content) as $content) {
      if (strlen($content) < 2 || strlen($content) > 50) {
        $errs['subchapter_list'] = "*Characters length must be between 2 and 50";
      }
    }
    if (strlen($this->list_content) < 4 || strlen($this->list_content) > 1000) {
      $errs['subchapter_list'] = "*Characters total length must be between 4 and 1000";
    }
    if (strlen($this->file) < 4 || strlen($this->file) > 1000) {
      $errs['file'] = "*Characters total length must be between 4 and 1000";
    }

    $this->errors = array_merge($this->errors, $errs);
  }
}
