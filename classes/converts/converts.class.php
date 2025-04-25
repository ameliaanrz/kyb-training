<?php

// require dependencies
require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';

class Converts extends Connection
{
  public function usersToPdf($t_id, $cds_id, $search, $adminName)
  {
    $participants = $this->getAllUsers($t_id, $cds_id, $search);
    $trainingName = $this->getTrainingName($t_id);
    $trainingName = empty($trainingName) ? "All" : $trainingName;
    $department = $this->getDepartmentName($cds_id);
    $department = empty($department) ? "All" : $department;

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($adminName);
    if (!empty($t_id)) {
      $pdf->SetTitle($trainingName . ' Training Participants List');
      $pdf->SetSubject('All participant list of ' . $trainingName . ' training');
    } else {
      $pdf->SetTitle('Training Participants List');
      $pdf->SetSubject('All PT. Kayaba Indonesia training participant list');
    }
    $pdf->SetKeywords('TCPDF, training, participants, PT. Kayaba Indonesia');

    // set default header data
    $pdf->SetHeaderData('../../../../../public/imgs/logo.png', PDF_HEADER_LOGO_WIDTH, "PT. Kayaba Indonesia Training Participants", "Printed by " . $adminName . " at " . date('d-m-Y H:i:s'), array(0, 64, 255), array(0, 64, 128));
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128), 'Copyright © ' . date('Y') . ' PT. Kayaba Indonesia');

    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
      require_once(dirname(__FILE__) . '/lang/eng.php');
      $pdf->setLanguageArray($l);
    }

    // set default font subsetting mode
    $pdf->setFontSubsetting(true);

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 14, '', true);

    // Add a page
    $pdf->AddPage();

    // set text shadow effect
    $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

    // Set some content to print
    $html = "<h1>Participants list</h1>";
    $html .= "<p>Training name: ";
    $html .= $trainingName;
    $html .= "</p>";
    $html .= "<p>Department: ";
    $html .= $department;
    $html .= "</p>";
    $html .= "<p>Total participants: " .  count($participants) . "</p>";
    $html .= "<table>
            <thead>
            <tr>
              <th><b>NPK</b></th>
              <th><b>Name</b></th>
              <th><b>Department</b></th>
            </tr>
            </thead>
            <tbody>";

    foreach ($participants as $person) {
      $html .= "<tr>
                  <td>" . $person['NPK'] . "</td>
                  <td>" . $person['NAME'] . "</td>
                  <td>" . $person['DEPARTMENT'] . "</td>
                </tr>";
    }

    $html .= "
            </tbody>
          </table>
    ";

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output($t_id . '_participant_list.pdf', 'I');
  }

  public function trainingsToPdf($npk, $adminName, $org_id, $approval, $completion, $search)
  {
    $trainings = $this->getAllTrainings($npk, $org_id, $approval, $completion, $search);
    $participantName = $this->getParticipantName($npk);
    $organizer = $this->getOrganizerName($org_id);
    $organizer = empty($organizer) ? "All" : $organizer;
    $approvalStatus = $approval == 1 ? "<span style=\"color: green\">Approved</span>" : ($approval == NULL ? "All" : ($approval == 2 ? "<span style=\"color: #dc3545\">Not approved</span>" : "<span style=\"color: #ffc107\">Waiting for approval</span>"));
    $completionStatus = $completion == 1 ? "<span style=\"color: green\">Completed</span>" : ($completion == NULL ? "All" : "<span style=\"color: red\">Not completed</span>");

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($adminName);
    if (!empty($npk)) {
      $pdf->SetTitle($participantName . ' Training List');
      $pdf->SetSubject('All training list of ' . $participantName);
    } else {
      $pdf->SetTitle('Training Participant List');
      $pdf->SetSubject('All PT. Kayaba Indonesia training list');
    }
    $pdf->SetKeywords('TCPDF, training list, PT. Kayaba Indonesia');

    // set default header data
    $pdf->SetHeaderData('../../../../../public/imgs/logo.png', PDF_HEADER_LOGO_WIDTH, "PT. Kayaba Indonesia Participant Training List", "Printed by " . $adminName . " at " . date('d-m-Y H:i:s'), array(0, 64, 255), array(0, 64, 128));
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128), 'Copyright © ' . date('Y') . ' PT. Kayaba Indonesia');

    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
      require_once(dirname(__FILE__) . '/lang/eng.php');
      $pdf->setLanguageArray($l);
    }

    // set default font subsetting mode
    $pdf->setFontSubsetting(true);

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 14, '', true);

    // Add a page
    $pdf->AddPage();

    // set text shadow effect
    $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

    // Set some content to print
    $html = "<h1>Training List</h1>";
    $html .= "<p>Participant name: ";
    $html .= $participantName;
    $html .= "</p>";
    $html .= "<p>Organizer: ";
    $html .= $organizer;
    $html .= "</p>";
    $html .= "<p>Approval status: ";
    $html .= $approvalStatus;
    $html .= "</p>";
    $html .= "<p>Completion status: ";
    $html .= $completionStatus;
    $html .= "</p>";
    $html .= "<p>Total trainings: " .  count($trainings) . "</p>";
    $html .= "<table>
            <thead>
            <tr>
              <th><b>EVT_ID</b></th>
              <th><b>Training</b></th>
              <th><b>Organizer</b></th>
              <th><b>Approval Status</b></th>
              <th><b>Completion Status</b></th>
            </tr>
            </thead>
            <tbody>";

    foreach ($trainings as $training) {
      $t_approved = $training['APPROVED'] == 1 ? "<span style=\"color: green\">Approved</span>" : ($training['APPROVED'] == 2 ? "<span style=\"color: #dc3545\">Not approved</span>" : "<span style=\"color: #ffc107\">Waiting for approval</span>");
      $t_completed = $training['COMPLETED'] == 1 ? "<span style=\"color: green\">Completed</span>" : "<span style=\"color: red\">Not completed</span>";
      $html .= "<tr>
                  <td>" . $training['EVT_ID'] . "</td>
                  <td>" . $training['TRAINING'] . "</td>
                  <td>" . $training['ORGANIZER'] . "</td>
                  <td>" . $t_approved . "</td>
                  <td>" . $t_completed . "</td>
                </tr>";
    }

    $html .= "
            </tbody>
          </table>
    ";

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output($npk . '_trainings_list.pdf', 'I');
  }

  private function getAllUsers($t_id, $cds_id, $search)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT users.NPK, users.NAME, departments.DEPARTMENT FROM event_participants AS ep INNER JOIN users ON ep.NPK = users.NPK INNER JOIN departments ON users.DPT_ID = departments.DPT_ID INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID WHERE (:training IS NULL OR evt.T_ID=:training) AND (:department IS NULL OR users.DPT_ID=:department) AND (:search IS NULL OR (users.NPK LIKE CONCAT('%', :search , '%') OR users.NAME LIKE CONCAT('%', :search , '%'))) GROUP BY users.NPK ORDER BY departments.DPT_ID, users.NPK");

    $statement->bindValue("training", $t_id);
    $statement->bindValue("department", $cds_id);
    $statement->bindValue("search", $search);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  private function getAllTrainings($npk, $org_id, $approval, $completion, $search)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare("SELECT evt.EVT_ID, trainings.TRAINING, organizers.ORGANIZER, ep.APPROVED, ep.COMPLETED FROM event_participants AS ep INNER JOIN events AS evt ON ep.EVT_ID = evt.EVT_ID INNER JOIN trainings ON evt.T_ID = trainings.T_ID INNER JOIN organizers ON evt.ORG_ID = organizers.ORG_ID WHERE ep.NPK = :npk AND (:org_id IS NULL OR evt.ORG_ID = :org_id) AND (:approval IS NULL OR ep.APPROVED = :approval) AND (:completion IS NULL OR ep.COMPLETED = :completion) AND (:search IS NULL OR (ep.EVT_ID = :search OR trainings.TRAINING LIKE CONCAT('%', :search, '%'))) ORDER BY evt.EVT_ID");

    $statement->bindValue('npk', $npk);
    $statement->bindValue('org_id', $org_id);
    $statement->bindValue('approval', $approval);
    $statement->bindValue('completion', $completion);
    $statement->bindValue('search', $search);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    return $result;
  }

  private function getTrainingName($t_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT TRAINING FROM trainings WHERE T_ID = :t_id');

    $statement->bindValue('t_id', $t_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /users.php?error=stmterror");
      return;
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result['TRAINING'];
  }

  private function getDepartmentName($dpt_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT DEPARTMENT FROM departments WHERE DPT_ID = :dpt_id');

    $statement->bindValue('dpt_id', $dpt_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /users.php?error=stmterror");
      return;
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result['DEPARTMENT'];
  }

  private function getParticipantName($npk)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT NAME FROM users WHERE NPK = :npk');

    $statement->bindValue('npk', $npk);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result['NAME'];
  }

  private function getOrganizerName($org_id)
  {
    $connection = new Connection();
    $statement = $connection->pdo->prepare('SELECT ORGANIZER FROM organizers WHERE ORG_ID = :org_id');

    $statement->bindValue('org_id', $org_id);

    if (!$statement->execute()) {
      $statement = null;
      header("Location: /users.php?error=stmterror");
      exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;

    return $result['ORGANIZER'];
  }
}
