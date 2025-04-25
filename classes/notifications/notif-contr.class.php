<?php

require_once __DIR__ . '/notif.class.php';

class NotificationsController extends Notifications
{
  public function createNotification($ntf_id, $src_npk, $ntf_t_id, $create_date, $description, $dst_npk, $evt_id)
  {
    // add constraints and error checkings here
    $this->createNewNotification($ntf_id, $src_npk, $ntf_t_id, $create_date, $description, $dst_npk, $evt_id);
  }

  public function createNotificationWA($message, $no_hp, $send_date)
  {
    // add constraints and error checkings here
    $this->createNewNotificationWA($message, $no_hp, $send_date);
  }

  public function clearUserNotifs($npk)
  {
    $this->clearCurrUserNotifs($npk);
  }

  public function deleteNotification($ntf_id)
  {
    $this->deleteCurrNotification($ntf_id);
  }

  public function SendNotifWhatsApp($nomor, $desc)
  {
    $response = $this->KirimNotifWhatsApp($nomor, $desc);
    return $response;
  }

  public function getAllNotifType()
  {
    return $this->getNotificationTypeAll();
  }
  public function getNotifTypeById($ntf_t_id)
  {
    return $this->getNotificationTypeById($ntf_t_id);
  }

  public function getUserNotifications($npk, $limit, $offset)
  {
    $notifs = $this->getAllUserNotifications($npk, $limit, $offset);
    if (empty($notifs)) {
      return false;
    }

    return $notifs;
  }

  public function getNoWA($npk)
  {
    $result = $this->getCurrNoWA($npk);
    if (empty($result)) {
      return false;
    }
    return $result;
  }

  public function getPhone($npk)
  {
    $result = $this->getPhoneNumberByNPK($npk);
    if (empty($result)) {
      return false;
    }
    return $result;
  }

  public function getMessage($ntf_t_id)
  {
    $result = $this->getMessageByType($ntf_t_id);
    if (empty($result)) {
      return false;
    }
    return $result;
  }

    /*public function getNoHpByNpk($npk)
  {
    $result = $this->getCurrNoHp($npk);
    if (empty($result)) {
      return false;
    }
    return $result;
  }*/
  
  
  /*public function getNomor($npk)
  {
    $result = $this->getCurrNomor($npk);
    if (empty($result)) {
      return false;
    }
    return $result;
  }*/

  public function getUserNotifsCount($npk)
  {
    $count = $this->getCurrUserNotifsCount($npk);
    if (empty($count)) {
      return false;
    }

    return $count[0];
  }

  public function getLatestEvtId()
  {
    $result = $this->getCurrLatestEvtId();
    if (empty($result)) {
      return false;
    }

    return $result['EVT_ID'];
  }

  public function getTrainingName($t_id)
  {
    $result = $this->getCurrTrainingName($t_id);
    if (empty($result)) {
      return false;
    }
    return $result;
  }

  public function getTrainingNameFromEvt($evt_id)
  {
    $result = $this->getCurrTrainingNameFromEvt($evt_id);
    if (empty($result)) {
      return false;
    }

    return $result['TRAINING'];
  }

  public function getKadepNpks($dpt_id, $rls_id)
  {
    $result = $this->getAllNpks($dpt_id, $rls_id);
    if (empty($result)) {
      return false;
    }

    return $result;
  }

  public function getKadept($dpt_id)
  {
    $result = $this->getCurrKadep($dpt_id);
    if (empty($result)) {
      return false;
    }

    return $result;
  }

  public function getPICnpk($dpk_id)
  {
    $result = $this->getCurrPIC($dpk_id);
    if (empty($result)) {
      return false;
    }

    return $result;
  }

  public function updateNotificationType($ntf_t_id, $ntf_desc)
  {
    $this->updateNotificationTypes($ntf_t_id, $ntf_desc);
  }

  public function getEventById($evt_id)
  {
    $result = $this->getCurrEvent($evt_id);
    if (empty($result)) {
      return false;
    }
    return $result;
  }

  public function getHrdNpks()
  {
    $result = $this->getAllHrd();
    if (empty($result)) {
      return false;
    }

    return $result;
  }

  public function getLatestNotifId()
  {
    $result = $this->getCurrLatestNotifId();
    if (empty($result)) {
      return false;
    }

    return $result['NTF_ID'];
  }
}
