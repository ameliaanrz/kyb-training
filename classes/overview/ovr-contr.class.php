<?php

require_once __DIR__ . '/ovr.class.php';

class OverviewController extends Overview
{
  public function getAllOverview()
  {
    $result=$this->getAllOvr();
    return empty($result) ? false : $result;
  }

  public function UpdateOverviews($id_overview,$img_slider,$img_profile1,$title_profile1,$desc_profile1,$img_profile2,$title_profile2,$desc_profile2){
    $return = $this->updateOvr($id_overview,$img_slider,$img_profile1,$title_profile1,$desc_profile1,$img_profile2,$title_profile2,$desc_profile2);
    if($return){
      return true;
    }else{
      return false;
    }
  }
}
?>