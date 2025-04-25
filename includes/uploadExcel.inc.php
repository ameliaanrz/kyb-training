<?php

// if (!isset($_FILES['excel_file'])) {
//     header("HTTP/1.1 400 Bad Request");
//     header("Content-type: application/json");
//     echo json_encode(array("error" => "No file uploaded"));
//     exit();
// }

// $excelfile = $_FILES['excel_file']['tmp_name'];
require_once '../vendor/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php'; // Memuat pustaka PHPExcel
ob_start();
date_default_timezone_set('Asia/Jakarta');

    $inputFileName = 'tes.xls';
    $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
    $sheet = $objPHPExcel->getActiveSheet();

    $seen = array();
    $duplicates = array();

    $rowNumber = 1; // Variabel untuk menyimpan nomor baris

    foreach ($sheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(FALSE); // Untuk mengambil semua sel dalam baris, bahkan jika kosong
        
        $rowData = array();
        foreach ($cellIterator as $cell) {
            $rowData[] = $cell->getValue(); // Mengambil nilai dari setiap sel
        }

        $key = implode(",", $rowData); // Membuat kunci dari semua nilai di setiap kolom
        
        // Periksa apakah kunci sudah ada sebelumnya
        if (isset($seen[$key])) {
            $duplicates[] = array(
                'row' => $rowNumber,
                'data' => $rowData
            );
        } else {
            $seen[$key] = true;
        }
        
        $rowNumber++; // Meningkatkan nomor baris
    }

    // Output duplikat
        if (!empty($duplicates)) {
            header("Content-type: application/json");
            $result=array("error" => "Duplikat ditemukan", "duplicates" => $duplicates);
            echo json_encode($result);
        } else {
            header("HTTP/1.1 201 Created");
            header("Content-type: application/json");
            echo json_encode(array("success" => "No duplicates found"));
        }
ob_end_flush();
?>
