<?php

require_once __DIR__ . '/../../vendor/setasign/fpdf/fpdf.php';

class PDF extends FPDF
{
  private $pdf_title;
  private $name;
  private $curr_date;

  public function __construct($pdf_title, $name, $curr_date)
  {
    parent::__construct();
    $this->pdf_title = $pdf_title;
    $this->name = $name;
    $this->curr_date = $curr_date;
  }

  // Page header
  function Header()
  {
    $this->Image(__DIR__ . '/../../public/imgs/logo.png', 10, 10, 30);
    $this->Cell(32);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(26, 5, $this->pdf_title);
    $this->ln();
    $this->Cell(32, 5);
    $this->SetFont('Arial', '', 11);
    $this->Cell(42, 5, 'Printed by ' . $this->name . ' at ' . $this->curr_date);
    $this->Ln(10);
  }

  // Page details
  function Details($evt_id, $event)
  {
    $this->SetFont("Arial", "B", 12);
    $this->Cell(26, 5, 'Training Event Details');
    $this->Ln(7);
    $this->SetFont("Arial", "", 11);
    $this->Cell(1);
    $this->Cell(26, 5, 'Event ID: ' . $evt_id);
    $this->Ln(6);
    $this->Cell(1);
    $this->Cell(26, 5, 'Training name: ' . $event['TRAINING']);
    $this->Ln(6);
    $this->Cell(1);
    $this->Cell(26, 5, 'Organizer: ' . $event['ORGANIZER']);
    $this->Ln(6);
    $this->Cell(1);
    $this->Cell(26, 5, 'Trainer: ' . $event['NAME']);
    $this->Ln(6);
    $this->Cell(1);
    $this->Cell(26, 5, 'Location: ' . $event['LOCATION']);
    $this->Ln(6);
    $this->Cell(1);
    $this->Cell(26, 5, 'Start date: ' . $event['START_DATE']);
    $this->Ln(6);
    $this->Cell(1);
    $this->Cell(26, 5, 'End date: ' . $event['END_DATE']);
    $this->Ln(6);
    $this->Cell(1);
    $this->Cell(26, 5, 'Duration: ' . $event['DURATION_HOURS'] . ' Hours');
    $this->Ln(8);
  }

  // Page footer
  function Footer()
  {
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial', 'I', 8);
    // Page number
    $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }

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

  // Simple table
  function BasicTable($header, $data)
  {
    // Table details
    $this->SetFont(
      "Arial",
      "B",
      12
    );
    // Table title
    $this->SetFont(
      "Arial",
      "B",
      12
    );
    $this->Cell(
      26,
      5,
      'Participants List'
    );
    $this->Ln(8);
    // Header
    $this->Cell(2);
    $this->SetFont('Arial', 'B', 11);
    foreach ($header as $col)
      $this->Cell(31, 7, $col, 1, 'C');
    $this->Ln();
    // Data
    $this->SetFont('Arial', '', 12);
    foreach ($data as $row) {
      $this->Cell(2);
      $this->Cell(31, 7, substr($row['NAME'], 0, 10), 1, 'C');
      $this->Cell(31, 7, substr($row['COMPANY'], 0, 14), 1, 'C');
      $this->Cell(31, 7, substr($row['DEPARTMENT'], 0, 10), 1, 'C');
      $this->Cell(31, 7, '', 1, 'C');
      $this->Cell(31, 7, '', 1, 'C');
      $this->Cell(31, 7, '', 1, 'C');
      $this->Ln();
    }
  }
}
