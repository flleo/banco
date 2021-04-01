
<?php

//import.php

include 'vendor/autoload.php';
$message='';

try {
    if ($_FILES["import_excel"]["name"] != '') {
        $file = $_FILES["import_excel"]["name"];
        $allowed_extension = array('xls', 'csv', 'xlsx');
        $file_array = explode(".", $file);       
        $file_extension = end($file_array);
        $file_precuel = prev($file_array);
        if (in_array($file_extension, $allowed_extension)) {            
            $file_name = 'uploads/' . $file;          
            move_uploaded_file($_FILES['import_excel']['tmp_name'], $file_name);         
            $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_name);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
            $spreadsheet = $reader->load($file_name);                      
            if ($spreadsheet != null) {
                //Hola excel 0
                $hojaActual = $spreadsheet->getSheet(0);
                $letraMayorDeColumna = $hojaActual->getHighestColumn();
                $csize = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);
                $rsize = $hojaActual->getHighestRow();
                $message = '<div class="alert alert-success">Data Imported Successfully</div>';
            } else {
                $message = '<div class="alert alert-danger">No se pudo importar el archivo</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Only .xls .csv or .xlsx file allowed</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Please Select File</div>';
    }
} catch (PDOExecption $e) {
    print "Error!: " . $e->getMessage() . "</br>";
}



?>
