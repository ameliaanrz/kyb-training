<?php
require_once __DIR__ . '/users.class.php';

class CreateUsersController extends User
{

    private $npk,$password, $name, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $c_id, $rls_id, $excelFile;
    private $errors = array();

    public function __construct($npk,$password, $name, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $c_id, $rls_id, $excelFile)
    {
        $this->npk = $npk;
        $this->password = $password;
        $this->name = $name;
        $this->dpt_id = $dpt_id;
        $this->sec_id = $sec_id;
        $this->sub_sec_id = $sub_sec_id;
        $this->grade = $grade;
        $this->gender = $gender;
        $this->c_id = $c_id;
        $this->rls_id = $rls_id;
        $this->excelFile = $excelFile;
    }

    public function createTraining($npk,$password, $name, $dpt_id, $sec_id, $sub_sec_id, $grade, $gender, $c_id, $rls_id, $excelFile)
    {
        $this->npk = $npk;
        $this->password = $password;
        $this->name = $name;
        $this->dpt_id = $dpt_id;
        $this->sec_id = $sec_id;
        $this->sub_sec_id = $sub_sec_id;
        $this->grade = $grade;
        $this->gender = $gender;
        $this->c_id = $c_id;
        $this->rls_id = $rls_id;
        $this->excelFile = $excelFile;
        $this->emptyField();

        if (empty($this->errors)) {
            // create new training
            $this->createManyParticipants($this->npk,$this->password,$this->name,$this->dpt_id,$this->sec_id,$this->sub_sec_id,$this->grade,$this->gender,$this->c_id,$this->rls_id,$this->excelFile);
        }

        return $this->errors;
    }


    private function emptyField()
    {
        $errs = array();
        define("REQUIRED_INPUT_ERROR", "*This field is required");


        if (empty($this->excelFile)) {
            $errs['excelFile'] = REQUIRED_INPUT_ERROR;
        }

        $this->errors = array_merge($this->errors, $errs);
    }

    public function validateExcelFile($file)
    {
        $errors = array();

        // Check if file is uploaded
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = "No file uploaded";
        } else {
            // Check file extension
            $allowedExtensions = array('xls', 'xlsx');
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $errors[] = "Only .xls and .xlsx file formats are allowed";
            }

            // Check file size
            $maxFileSize = 10 * 1024 * 1024; // 10 MB
            if ($file['size'] > $maxFileSize) {
                $errors[] = "File size exceeds the maximum limit of 10MB";
            }

            
        }

        return $errors;
    }
}

