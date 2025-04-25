<?php

require_once __DIR__ . '/../../vendor/setasign/fpdf/fpdf.php';

class PDF extends FPDF
{
  // Load data
  function LoadData($file)
  {
    // Read file lines
    $lines = file($file);
    $data = array();
    foreach ($lines as $line)
      $data[] = explode(';', trim($line));
    return $data;
  }
   var $isTrainingTables = false;
   var $isThisAttendance = false;
  var $nameTraining = '';

  function setNameTrain($training){
     $this->nameTraining = isset($training['TRAINING']) ? $training['TRAINING'] : null;
  }

  function OutputTrainingName() {
        if (!empty($this->nameTraining)) {
            $this->Cell(0, 10, $this->nameTraining, 0, 1, 'C');
        } else {
            $this->Cell(0, 10, ' ', 0, 1, 'C');
        }
    }
  // heading
  function Header()
  {
    $this->Image(__DIR__ . '/../../public/imgs/logo.png', 11, 11, 30);
              $this->Cell(32);
              $this->SetFont('Arial', 'B', 18);
              if (!empty($this->nameTraining)) {
                  $this->Cell(0, 10, $this->nameTraining, 0, 1, 'C');
              } else {
                  $this->Cell(0, 10, $this->nameTraining, 0, 1, 'C');
              }
              echo $this->nameTraining;
              $this->Ln();
              $this->SetFont('Arial', '', 12);
              date_default_timezone_set('Asia/Jakarta');
              $this->Ln(7);
        if ($this->isTrainingTables) {
          // If trainingsTables is being used, add table header
            // Column headings for table
            $header = array('NO','NPK','Name', 'Training', 'Organizer','Department','Grade','Start Date', 'End Date');

            // Column widths
            $w = array(10,15, 47, 50, 30, 50, 12,30,30);
            
            // Set font for header
            $this->SetFont('Arial', 'B', 10);
            // Header
            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
            }
            $this->Ln();
        }
        if ($this->isThisAttendance) {
              // Heading Main Title
              
            // Column headings for table
            $header = array('NO','NPK','Name', 'Department','Signature');

             $w = array(10, 20, 70, 80, 90);
            
            $this->SetFont('Arial', '', 13);
            // Header
            for ($i = 0; $i < count($header); $i++) {
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
            }
            $this->Ln();
        }

  }

  // Better table
  function trainingsStatsTable($header, $data)
  {
    // Column widths
    $w = array(50, 50, 50, 50);
    // Header
    for ($i = 0; $i < count($header); $i++)
      $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    $this->Ln();
    // Data
    $this->Cell($w[0], 6, $data['trainings_total'], 'LR');
    $this->Cell($w[1], 6, $data['running_trainings'], 'LR');
    $this->Cell($w[2], 6, number_format($data['next_trainings']), 'LR', 0);
    $this->Cell($w[3], 6, number_format($data['completed_trainings']), 'LR', 0);
    $this->Ln();
    // Closing line
    $this->Cell(array_sum($w), 0, '', 'T');
  }

  function participantsStatsTable($header, $data)
  {
    // Column widths
    $w = array(50, 50, 50, 50, 50);
    // Header
    for ($i = 0; $i < count($header); $i++)
      $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    $this->Ln();
    // Data
    $this->Cell($w[0], 6, $data['employees_total'], 'LR');
    $this->Cell($w[0], 6, $data['female_participants'], 'LR');
    $this->Cell($w[1], 6, $data['male_participants'], 'LR');
    $this->Cell($w[2], 6, number_format($data['female_hours']), 'LR', 0);
    $this->Cell($w[3], 6, number_format($data['male_hours']), 'LR', 0);
    $this->Ln();
    // Closing line
    $this->Cell(array_sum($w), 0, '', 'T');
  }

  function participantsTable($header, $data)
  {
    // Column widths

    $w = array(10, 140, 40,30,30);
    $this->SetFont('Arial', '', 10);
    // Header
    for ($i = 0; $i < count($header); $i++)
      $this->Cell($w[$i], 7 , $header[$i], 1, 0, 'C');
    $this->Ln();
    $num =1;
    // Data
    foreach ($data as $row) {
      $this->Cell($w[0], 6, $num, 'LR', 0, 'C');
      $this->Cell($w[1], 6, substr($row['TRAINING'], 0, 50) . (strlen($row['TRAINING']) > 50 ? '...' : ''), 'LR');
      $this->Cell($w[2], 6, substr($row['ORGANIZER'],0,19). (strlen($row['ORGANIZER']) > 19 ? '...' : ''), 'LR');
      $this->Cell($w[3], 6, substr($row['START_DATE'], 0, 13), 'LR');
      $this->Cell($w[4], 6, substr($row['END_DATE'], 0, 13), 'LR');
      $this->Ln();
      $num++;
    }
    // Closing line
    $this->Cell(array_sum($w), 0, '', 'T');

  }
  
  function trainingsTables($header, $data)
  {
    $this->isTrainingTables = true;

    // Column widths
    $w = array(10,15, 47, 50, 30, 50, 12,30,30);
    $this->SetFont('Arial', '', 10);
    // Header
    for ($i = 0; $i < count($header); $i++)
      $this->Cell($w[$i], 7 , $header[$i], 1, 0, 'C');
    $this->Ln();
    $num =1;
    // Data
    foreach ($data as $row) {
      $this->Cell($w[0], 6, $num, 'LR', 0, 'C');
      $this->Cell($w[1], 6, substr($row['NPK'],0,15). (strlen($row['NPK']) > 15 ? '...' : ''), 'LR');
      $this->Cell($w[2], 6, substr($row['NAME'],0,15). (strlen($row['NAME']) > 15 ? '...' : ''), 'LR');
      $this->Cell($w[3], 6, substr($row['TRAINING'], 0, 19) . (strlen($row['TRAINING']) > 19 ? '...' : ''), 'LR');
      $this->Cell($w[4], 6, substr($row['ORGANIZER'],0,10). (strlen($row['ORGANIZER']) > 10 ? '...' : ''), 'LR');
      $this->Cell($w[5], 6,  substr($row['DEPARTMENT'],0,20). (strlen($row['DEPARTMENT']) > 20 ? '...' : ''), 'LR',0,'C');
      $this->Cell($w[6], 6, substr($row['GRADE'], 0, 25), 'LR',0,'C');
      $this->Cell($w[7], 6, substr($row['START_DATE'], 0, 13), 'LR');
      $this->Cell($w[8], 6, substr($row['END_DATE'], 0, 13), 'LR');
      $this->Ln();
      $num++;
    }
    // Closing line
    $this->Cell(array_sum($w), 0, '', 'T');
   $this->isTrainingTables = false;

  }

  function AttendanceTables($header, $users) {
    $this->isThisAttendance = true;

    // Column widths
    $w = array(10, 20, 70, 80, 90); // Adjusted column widths, including a larger column for the merged signature

    // Header
    for ($i = 0; $i < count($header); $i++) {
        $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C');
    }
    $this->Cell($w[4], 10, 'Signature', 1, 0, 'C'); // Header for merged signature column
    $this->Ln();

    // Data
    $num = 1;
    $this->SetFont('Arial', '', 10);
    foreach ($users as $user) {
        $this->Cell($w[0], 10, $num, 'LR', 0, 'C');
        $this->Cell($w[1], 10, $user['NPK'], 'LR');
        $this->Cell($w[2], 10, substr($user['NAME'], 0, 24) . (strlen($user['NAME']) > 24 ? '...' : ''), 'LR', 0, 'C');
        $this->Cell($w[3], 10, $user['DEPARTMENT'], 'LR', 0, 'C');
        
         // Determine position of the number in the signature column
        $signContent = '';
        if ($num % 2 == 1) {
            $signContent = $num . '. ' . str_repeat(' ', 80); // Number on the left with ". "
        } else {
            $signContent = str_repeat(' ', 5) . $num . '. '; // Number on the right with ". "
        }

        $this->Cell($w[4], 10, $signContent, 'LR', 0, 'C'); // Merged signature column
        $this->Ln();
        $num++;
    }

    // Closing line
    $this->Cell(array_sum($w), 0, '', 'T');
     $this->isThisAttendance = false;
}


 
  // Page footer
  function Footer()
  {
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial', 'I', 8);
    // Page number
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
  }
}
