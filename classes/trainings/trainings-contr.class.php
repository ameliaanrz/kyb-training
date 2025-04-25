<?php

require_once __DIR__ . '/trainings.class.php';

class TrainingsController extends Trainings
{
  public function getTrainings($page = 1, $lists_shown = 10, $search = '',$colomIndex,$direction)
  {
    $trainings = $this->getAllTrainings($page, $lists_shown, $search,$colomIndex,$direction);
    if (empty($trainings)) {
      
      return false;
    }

    return $trainings;
  }

  public function getTraining($t_id)
  {
    $training = $this->getCurrTraining($t_id);
    if (empty($training)) {
      return false;
    }

    return $training;
  }

  public function getCompanyPurposes($t_id)
  {
    $training = $this->getAllCompanyPurposes($t_id);
    if (empty($training)) {
      return false;
    }

    return $training;
  }

  public function getParticipantsPurposes($t_id)
  {
    $training = $this->getAllParticipantspurposes($t_id);
    if (empty($training)) {
      return false;
    }

    return $training;
  }
}
