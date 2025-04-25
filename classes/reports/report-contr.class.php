<?php

require_once __DIR__ . '/report.class.php';
date_default_timezone_set('Asia/Jakarta');
class ReportController extends PDF
{
  public function printStatistics($data, $filters)
  {
    $pdf = new PDF('L');
    // Data loading
    $pdf->AddPage();
    // Add Filters
    $pdf->SetFont('Arial', 'B', 25);
    $pdf->Cell(0,8,'STATISTICS',0,0,'C');
    $pdf->Ln(7);
    $pdf->Ln(20);

    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(110, 5, "Department: " . $filters['department']);
    $pdf->Cell(0, 5, "Gender: " . $filters['gender']);
    $pdf->Ln(7);
    $pdf->Cell(110, 5, "Section: " . $filters['section']);
    $pdf->Cell(0, 5, "Grade: " . $filters['grade']);
    $pdf->Ln(7);
    $pdf->Cell(0, 5, "Subsection: " . $filters['subsection']);
    $pdf->Ln(10);
    // Add Table
    $pdf->SetFont('Arial', '', 14);
    $header = array('Total Trainings', 'Running Trainings', 'Next Trainings', 'Completed Trainings');
    $pdf->trainingsStatsTable($header, $data);
    $pdf->Ln(10);
    $header = array('Employees Total', 'Female Participants', 'Male Participants', 'Female Hours', 'Male Hours');
    $pdf->participantsStatsTable($header, $data);
    $pdf->Output();
  }

  public function printParticipantTrainings($filters, $events)
  {
    $pdf = new PDF('L');
    // Column headings
    $header = array('NO', 'Training', 'Organizer','Start Date', 'End Date');
    // Data loading
    $pdf->AddPage();
    // Add Filters
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(110, 5, "Participant: " . $filters['username']);
    $pdf->Cell(110, 5, "Organizer: " . $filters['organizer']);
    $pdf->Cell(0, 5, "End Date: " . $filters['end_date']);
    $pdf->Ln(7);
    $pdf->Cell(110, 5, "NPK: " . $filters['npk']);
    $pdf->Cell(110, 5, "Start date: " . $filters['start_date']);
    $pdf->Cell(0, 5, "Completion: " . $filters['completed']);
    $pdf->Ln(7);
    $pdf->Cell(110, 5, "Training: " . $filters['training']);
    $pdf->Ln(7);
    // Add Table
    $pdf->SetFont('Arial', '', 14);
    $pdf->participantsTable($header, $events);
    $pdf->Output();
  }
  

  public function printParticipants($filters, $events)
  {
    $pdf = new PDF('L');
    // Column headings
    $header = array('NO','NPK','Name', 'Training', 'Organizer','Department','Grade','Start Date', 'End Date');
    // Data loading
    $pdf->AddPage();
    

    // Add Filters
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(110, 5, "Participant: " . $filters['username']);
    $pdf->Cell(110, 5, "Start date: " . $filters['start_date']);
    $pdf->Cell(0, 5, "Grade: " . $filters['grade']);
    $pdf->Ln(7);
    $pdf->Cell(110, 5, "Department: " . $filters['department']);
    $pdf->Cell(0, 5, "End date: " . $filters['end_date']);
    $pdf->Ln(7);
    $pdf->Cell(110, 5, "Training: " . $filters['training']);
    $pdf->Cell(0, 5, "Gender: " . $filters['gender']);
    $pdf->Ln(7);
    // Add Table
    $pdf->SetFont('Arial', '', 14);
    $pdf->trainingsTables($header, $events);
    $pdf->Output();
  }





  public function printAttendanceForm($users,$training){
    function getIndonesianMonth($date) {
      $monthNames = array(
          '01' => 'Januari',
          '02' => 'Februari',
          '03' => 'Maret',
          '04' => 'April',
          '05' => 'Mei',
          '06' => 'Juni',
          '07' => 'Juli',
          '08' => 'Agustus',
          '09' => 'September',
          '10' => 'Oktober',
          '11' => 'November',
          '12' => 'Desember'
      );
      
      $month = date('m', strtotime($date));
      $year = date('Y', strtotime($date));
      return $monthNames[$month].' '.$year;
    }
    
    function getIndonesianDay($date) {
      $dayNames = array(
          'Sunday' => 'Minggu',
          'Monday' => 'Senin',
          'Tuesday' => 'Selasa',
          'Wednesday' => 'Rabu',
          'Thursday' => 'Kamis',
          'Friday' => 'Jumat',
          'Saturday' => 'Sabtu'
      );
      $day = date('l', strtotime($date));
      return $dayNames[$day];
    }

    // Get the current date in the desired format
    $tanggal_hari_ini = strftime('%d %B %Y');

    // Capitalize the first letter of the day name
    $tglnow = date('d',strtotime($tanggal_hari_ini));

    $tanggal_hari_ini = getIndonesianMonth($tanggal_hari_ini);
    
    $tglfrom=date('d',strtotime($training['START_DATE']));
    $tglto=date('d',strtotime($training['END_DATE']));
    $nameDayFrom=getIndonesianDay($training['START_DATE']);
    $nameDayTO=getIndonesianDay($training['END_DATE']);
    $bulan=getIndonesianMonth($training['START_DATE']);
    $waktufrom=date('H.i',strtotime($training['START_TIME']));
    $waktuto=date('H.i',strtotime($training['END_TIME']));
    $pdf = new PDF('L');

    $pdf->AliasNbPages();

    // Column headings
    $header = array('NO','NPK','Name', 'Department');
    // Data loading

    $pdf->AddPage();
    $pdf->setNameTrain($training['TRAINING']);
    $pdf->SetFont('Arial', 'B', 19);
    $pdf->Cell(0,8,'DAFTAR NAMA TRAINING'.' '.strtoupper($training['TRAINING']),0,0,'C');
    $pdf->Cell(50,8,'',0,1);
    $pdf->Cell(40,8,'',0,0);
    $pdf->Cell(50,8,'',0,1);
    $pdf->Cell(0,10,'',0,1);
    $pdf->SetFont('Arial', '', 13);
    if ($tglfrom == $tglto) {
      // Jika hanya satu hari
      $pdf->Cell(0, 5, 'Hari/Tanggal  :  '. $nameDayFrom.', '.$tglfrom.' '.$bulan, 0, 1);
    } else {
        // Jika rentang tanggal lebih dari satu hari
        $pdf->Cell(0, 5, 'Hari/Tanggal  :  '. $nameDayFrom.' - '.$nameDayTO.', '.$tglfrom.' - '.$tglto.' '.$bulan, 0, 1);
    }

    $pdf->Cell(0,5,'Waktu            :  '. $waktufrom.' - '.$waktuto.' WIB',0,1);
    $pdf->Cell(0,5,'Tempat          :  '.$training['LOCATION'],0,1);
    $pdf->Cell(0,10,'',0,1);
    $pdf->AttendanceTables($header, $users);
    $pdf->Output();
  }
}
