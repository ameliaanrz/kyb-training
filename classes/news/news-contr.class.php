<?php

require_once __DIR__ . '/news.class.php';

define('NEWS_TYPE_AVAILABLE', 'NT01');

class NewsController extends News
{
  public function createTrainingNews($evt_id, $src_npk, $participants = array())
  {
    // get curr date
    $currDate = date('Y-m-d');

    // get training details
    $training = $this->getTrainingDetails($evt_id);

    // create news type
    $nws_t_id = NEWS_TYPE_AVAILABLE;

    // create news description
    $description = $training['TRAINING'] . ' by ' . $training['ORGANIZER'] . ' is now available and is going to start on ' . $training['START_DATE'] . ' until ' . $training['END_DATE'] . ', ' . $training['LOCATION'] . '.';

    // get all training event participants if participants is not specified
    if (empty($participants)) {
      $participants = $this->getAllApprovedParticipants($evt_id);
    }

    // for each participants create new training news
    foreach ($participants as $participant) {
      // get curr participant's NPK for dst_npk
      $dst_npk = is_array($participant) ? $participant['NPK'] : $participant;

      // create news id
      $nws_id = $this->getLatestNewsId();
      $nws_id = !empty($nws_id) ? "NWS" . str_pad(strval((int)substr($nws_id, 3) + 1), 4, "0", STR_PAD_LEFT) : 'NWS0001';

      // check if news is already available
      if (!$this->findCurrNews($nws_t_id, $description, $src_npk, $dst_npk)) {
        // if news is not available yet, create news
        $this->createNewTrainingNews($nws_id, $evt_id, $nws_t_id, $description, $src_npk, $dst_npk, $currDate);
      }
    }
  }
}
