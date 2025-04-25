<?php

require_once __DIR__ . '/trainings.class.php';

class TrainingsContentController extends Trainings
{
  private $t_id = '';
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
  private $vrstats = '';
  private $subchapter_paragraph = '';
  private $list_content = '';
  private $subchapter_file_name = '';
  private $subchapter_file_size = '';
  private $core_content;
  private $errors = array(
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
    'core_content' => ''
  );

  public function changePosition($tcc_id, $type)
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
      $next_tcc_id = $this->getNextTCCID($tcc_id);
      if (!empty($next_tcc_id)) {
        // change current tcc_id to the largest possible tcc_id
        $this->changeTccId($tcc_id, 'TCC99999');
        // change next tcc_id to curr tcc_id
        $this->changeTccId($next_tcc_id, $tcc_id);
        // change current TCC_ID to next TCC_ID
        $this->changeTccId('TCC99999', $next_tcc_id);
      }
    } else {
      /**
       * move up down
       * -----------------
       * 1. get prev TCC_ID
       * 2. change current TCC_ID to the largest possible TCC_ID
       * 3. change prev TCC_ID to current TCC_ID
       * 4. change current TCC_ID to prev TCC_ID
       */
      $prev_tcc_id = $this->getPrevTCCID($tcc_id);
      if (!empty($prev_tcc_id)) {
        // change current tcc_id to the largest possible tcc_id
        $this->changeTccId($tcc_id, 'TCC99999');
        // change prev tcc_id to curr tcc_id
        $this->changeTccId($prev_tcc_id, $tcc_id);
        // change current TCC_ID to prev TCC_ID
        $this->changeTccId('TCC99999', $prev_tcc_id);
      }
    }
  }

  public function getTrainingName($t_id)
  {
    $result = $this->getTrainingNameById($t_id);
    return empty($result) ? false : $result['TRAINING'];
  }

  public function getMaterial($m_id)
  {
    $result = $this->getMaterialById($m_id);
    return empty($result) ? false : $result;
  }

  public function getTrainingMaterials($t_id)
  {
    $result = $this->getAllTrainingMaterials($t_id);
    return empty($result) ? false : $result;
  }

  public function getTrainingChapters($m_id)
  {
    $result = $this->getAllTrainingChapters($m_id);
    return empty($result) ? false : $result;
  }

  public function getTrainingMaterial($m_id)
  {
    return $this->getTrainingMaterialById($m_id);
  }

  public function getTrainingChapter($tch_id)
  {
    $result = $this->getTrainingChapterById($tch_id);
    return empty($result) ? false : $result;
  }

  public function getLatestTrainingChapterId($t_id)
  {
    $result = $this->getLatestTrainingContentChapterId($t_id);
    return empty($result) ? false : $result['TCH_ID'];
  }

  public function getLatestTrainingMaterialId($t_id)
  {
    $result = $this->getLatestTrainingContentMaterialId($t_id);
    return empty($result) ? false : $result['M_ID'];
  }

  public function getLatestSubchapterId()
  {
    $result = $this->getCurrentLatestSubchapterId();
    return empty($result) ? false : $result['TSCH_ID'];
  }

  public function getLatestCoreContentId()
  {
    $result = $this->getCurrentLatestCoreContentId();
    return empty($result) ? false : $result['TCC_ID'];
  }

  public function getTrainingSubchaptersContents($t_id)
  {
    error_log("Fetching contents for t_id: " . $t_id);

    $result = $this->getAllTrainingSubchaptersContents($t_id);
    error_log("Fetching contents for t_id: " . $t_id);

    if (empty($result)) {
        return false;
    }
    return $result;
  }

  public function getSubchapters($tch_id)
  {
    $result = $this->getCurrentSubchapters($tch_id);
    return empty($result) ? false : $result;
  }

  public function updateVRStat($t_id, $vrstats){
    $this->t_id = $t_id;
    $this->vrstats = $vrstats;

    $this->updateVRStats($this->t_id, $this->vrstats);
    // return error
    return $this->errors;
  }

  public function createCoreContent($tcc_id, $t_id, $content_type, $core_content)
  {
    $this->tcc_id = $tcc_id;
    $this->t_id = $t_id;
    $this->content_type = $content_type;
    $this->core_content = $core_content;

    if (in_array($this->content_type, array(CONTENT_TYPE_ORDERED_LIST, CONTENT_TYPE_UNORDERED_LIST))) {
      $this->core_content = implode(',', $this->core_content);
    }

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['tcc_id']) && empty($this->errors['t_id']) && empty($this->errors['content_type']) && empty($this->errors['core_content'])) {
      $this->createNewCoreContent($this->tcc_id, $this->t_id, $this->content_type, $this->core_content);
    }

    // return errors
    return $this->errors;
  }

  public function createList($tcc_id, $tsch_id, $content_type, $list_content)
  {
    $this->tcc_id = $tcc_id;
    $this->sch_id = $tsch_id;
    $this->content_type = $content_type;
    $this->list_content = $list_content;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['tcc_id']) && empty($this->errors['sch_id']) && empty($this->errors['content_type']) && empty($this->errors['subchapter_list'])) {
      $this->createNewCoreContent($this->tcc_id, $this->sch_id, $this->content_type, $this->list_content);
    }

    // return errors
    return $this->errors;
  }

  public function createLink($tcc_id, $tsch_id, $content_type, $subchapter_link)
  {
    $this->tcc_id = $tcc_id;
    $this->sch_id = $tsch_id;
    $this->content_type = $content_type;
    $this->subchapter_link = $subchapter_link;

    // error checks
    $this->invalidLink();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['tcc_id']) && empty($this->errors['sch_id']) && empty($this->errors['content_type']) && empty($this->errors['subchapter_link'])) {
      $this->createNewCoreContent($this->tcc_id, $this->sch_id, $this->content_type, $this->subchapter_link);
    }

    // return errors
    return $this->errors;
  }

  public function uploadFile($tcc_id, $t_id, $content_type, $subchapter_file_name, $subchapter_file_size)
  {
    $this->t_id = $t_id;
    $this->tcc_id = $tcc_id;
    $this->content_type = $content_type;
    $this->subchapter_file_name = $subchapter_file_name;
    $this->subchapter_file_size = $subchapter_file_size;

    // error checks
    $this->invalidFileType();
    $this->fileSizeTooBig();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['tcc_id']) && empty($this->errors['t_id']) && empty($this->errors['content_type']) && empty($this->errors['subchapter_file_name']) && empty($this->errors['subchapter_file_size'])) {
      if ($this->getTrainingContent($this->tcc_id)) {
        $this->updateCurrTrainingContent($this->tcc_id, $this->content_type, $this->subchapter_file_name);
      } else {
        $this->createNewCoreContent($this->tcc_id, $this->t_id, $this->content_type, $this->subchapter_file_name);
      }
    }

    // return errors
    return $this->errors;
  }

  public function createSubchapter($tch_id, $sch_id, $subchapter_title)
  {
    $this->tch_id = $tch_id;
    $this->sch_id = $sch_id;
    $this->subchapter_title = $subchapter_title;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['tch_id']) && empty($this->errors['sch_id']) && empty($this->errors['subchapter_title'])) {
      $this->createNewSubchapter($this->tch_id, $this->sch_id, $this->subchapter_title);
    }

    // return errors
    return $this->errors;
  }

  public function createTrainingChapter($t_id, $m_id, $tch_id, $title, $description)
  {
    $this->t_id = $t_id;
    $this->m_id = $m_id;
    $this->tch_id = $tch_id;
    $this->chapter_title = $title;
    $this->chapter_description = $description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['t_id']) && empty($this->errors['m_id']) && empty($this->errors['tch_id']) && empty($this->errors['chapter_title']) && empty($this->errors['chapter_description'])) {
      $this->createNewTrainingChapter($this->t_id, $this->m_id, $this->tch_id, $this->chapter_title, $this->chapter_description);
    }

    // return errors
    return $this->errors;
  }

  public function createTrainingMaterial($t_id, $m_id, $title, $description)
  {
    $this->t_id = $t_id;
    $this->m_id = $m_id;
    $this->material_title = $title;
    $this->material_description = $description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['t_id']) && empty($this->errors['m_id']) && empty($this->errors['material_title']) && empty($this->errors['material_description'])) {
      $this->createNewTrainingMaterial($this->t_id, $this->m_id, $this->material_title, $this->material_description);
    }

    // return errors
    return $this->errors;
  }

  public function updateTrainingMaterial($m_id, $material_title, $material_description)
  {
    $this->m_id = $m_id;
    $this->material_title = $material_title;
    $this->material_description = $material_description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['m_id']) && empty($this->errors['material_title']) && empty($this->errors['material_description'])) {
      $this->updateCurrentTrainingMaterial($this->m_id, $this->material_title, $this->material_description);
    }

    // return errors
    return $this->errors;
  }

  public function updateTrainingChapter($tch_id, $title, $description)
  {
    $this->tch_id = $tch_id;
    $this->chapter_title = $title;
    $this->chapter_description = $description;

    // error checks
    $this->notLongEnough();
    $this->emptyField();

    // create new training chapter
    if (empty($this->errors['tch_id']) && empty($this->errors['chapter_title']) && empty($this->errors['chapter_description'])) {
      $this->updateCurrentTrainingChapter($this->tch_id, $this->chapter_title, $this->chapter_description);
    }

    // return errors
    return $this->errors;
  }

  public function updateTrainingSubchapter($sch_id, $subchapter_title)
  {
    $this->sch_id = $sch_id;
    $this->subchapter_title = $subchapter_title;

    // check errors
    $this->notLongEnough();
    $this->emptyField();

    if (empty($this->errors['sch_id']) && empty($this->errors['subchapter_title'])) {
      $this->updateCurrentTrainingSubchapter($sch_id, $subchapter_title);
    }

    return $this->errors;
  }

  public function updateTrainingContent($tcc_id, $content_type, $core_content)
  {
    $this->tcc_id = $tcc_id;
    $this->content_type = $content_type;
    $this->core_content = $core_content;

    if ($this->content_type == CONTENT_TYPE_PARAGRAPH) {
      $this->subchapter_paragraph = $this->core_content;
      // error check
      $this->notLongEnough();
      $this->errors['core_content'] = $this->errors['subchapter_paragraph'];
    } else if ($this->content_type == CONTENT_TYPE_LINK) {
      $this->subchapter_link = $this->core_content;
      // error check
      $this->invalidLink();
      $this->errors['core_content'] = $this->errors['subchapter_link'];
    } else if ($this->content_type == CONTENT_TYPE_UNORDERED_LIST || $this->content_type == CONTENT_TYPE_ORDERED_LIST) {
      $this->core_content = implode(',', $this->core_content);
      $this->list_content = $this->core_content;
      // error check
      $this->notLongEnough();
      $this->errors['core_content'] = $this->errors['subchapter_list'];
    }

    // check for errors
    $this->emptyField();

    // if no errors update training
    if (empty($this->errors['tcc_id']) && empty($this->errors['content_type']) && empty($this->errors['core_content'])) {
      $this->updateCurrTrainingContent($this->tcc_id, $this->content_type, $this->core_content);
    }

    // return errors
    return $this->errors;
  }

  public function reuploadFile($tcc_id, $content_type, $subchapter_content, $file_size)
  {
    $this->tcc_id = $tcc_id;
    $this->content_type = $content_type;
    $this->subchapter_file_name = $subchapter_content;
    $this->subchapter_file_size = $file_size;

    // check for errors
    $this->fileSizeTooBig();
    $this->invalidFileType();
    $this->emptyField();

    // if no errors update training
    if (empty($this->errors['tcc_id']) && empty($this->errors['content_type']) && empty($this->errors['subchapter_file_name']) && empty($this->errors['subchapter_file_size'])) {
      $this->updateCurrTrainingContent($this->tcc_id, $this->content_type, $this->subchapter_file_name);
    }

    // return errors
    return $this->errors;
  }

  public function deleteContent($tcc_id)
  {
    return $this->deleteCurrContent($tcc_id);
  }

  public function deleteTraining($t_id)
  {
    return $this->deleteCurrTraining($t_id);
  }

  public function deleteMaterial($m_id)
  {
    return $this->deleteCurrMaterial($m_id);
  }

  public function deleteChapter($tch_id)
  {
    return $this->deleteCurrChapter($tch_id);
  }

  public function deleteSubchapter($tsch_id)
  {
    return $this->deleteCurrSubchapter($tsch_id);
  }

  private function emptyField()
  {
    $errs = array();
    define("REQUIRED_INPUT_ERROR", "*This field is required");

    if (empty($this->t_id)) {
      $errs['t_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->tch_id)) {
      $errs['tch_id'] = REQUIRED_INPUT_ERROR;
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
    if (empty($this->tcc_id)) {
      $errs['tcc_id'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->content_type)) {
      $errs['content_type'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_paragraph)) {
      $errs['subchapter_paragraph'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_file_name)) {
      $errs['subchapter_file_name'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->list_content)) {
      $errs['subchapter_list'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->subchapter_link)) {
      $errs['subchapter_link'] = REQUIRED_INPUT_ERROR;
    }
    if (empty($this->core_content)) {
      $errs['core_content'] = REQUIRED_INPUT_ERROR;
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
    if ($this->content_type == CONTENT_TYPE_IMAGE && !in_array($file_ext, $allowedImgs)) {
      $errs['content_type'] = INVALID_IMAGE_TYPE_ERROR;
    }

    // checks video types
    else if ($this->content_type == CONTENT_TYPE_VIDEO && !in_array($file_ext, $allowedVids)) {
      $errs['content_type'] = INVALID_VIDEO_TYPE_ERROR;
    }

    // checks pdf type
    else if ($this->content_type == CONTENT_TYPE_PDF && strcmp($file_ext, "pdf") !== 0) {
      $errs['content_type'] = INVALID_PDF_TYPE_ERROR;
    }

    $this->errors = array_merge($this->errors, $errs);
  }

  private function fileSizeTooBig()
  {
    $errs = array();

    define("IMAGE_SIZE_ERROR", "*Image size should not exceed 5 MB");
    define("VIDEO_SIZE_ERROR", "*Video size should not exceed 500 MB");

    // checks image file size
    if (($this->content_type == CONTENT_TYPE_IMAGE || $this->content_type == CONTENT_TYPE_PDF) && $this->subchapter_file_size > 5 * 1024 * 1024) {
      $errs['subchapter_file_name'] = IMAGE_SIZE_ERROR;
    }

    // checks video file size
    else if ($this->content_type == CONTENT_TYPE_VIDEO && $this->subchapter_file_size > 500 * 1024 * 1024) {
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
    if (strlen($this->core_content) < 4 || strlen($this->core_content) > 1000) {
      $errs['core_content'] = "*Characters total length must be between 4 and 1000";
    }

    $this->errors = array_merge($this->errors, $errs);
  }
}
