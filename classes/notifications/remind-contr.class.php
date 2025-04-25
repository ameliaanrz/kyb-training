<?php

require_once __DIR__ . '/remind.class.php';

class RemindController extends Remind
{

  public function getNomorHp($evt_id)
  {
    return $this->getNomorByNpk($evt_id);
  }

  public function createReminder($message, $no_hp, $send_date)
  {
    // add constraints and error checkings here
    $this->createNewReminder($message, $no_hp, $send_date);
  }

  public function getEvents($evt_id, $t_id, $loc_id, $location, $training, $start_date, $end_date, $start_time, $end_time)
  {
    $result = $this->getAllEvents($evt_id, $t_id, $loc_id, $location, $training, $start_date, $end_date, $start_time, $end_time);
    return empty($result) ? false : $result;
  }

  public function getParticipantName($evt_id)
  {
    return $this->getAllParticipantsNames($evt_id);
  }
}
