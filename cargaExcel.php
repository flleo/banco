
<?php

//import.php

include 'vendor/autoload.php';


try {
    if ($_FILES["import_excel"]["name"] != '') {
        $allowed_extension = array('xls', 'csv', 'xlsx');
        $file_array = explode(".", $_FILES["import_excel"]["name"]);
        $file_extension = end($file_array);

        if (in_array($file_extension, $allowed_extension)) {
            //$file_name = time() . '.' . $file_extension;
            $file_name = 'uploads/' . $_FILES["import_excel"]["name"];
            move_uploaded_file($_FILES['import_excel']['tmp_name'], $file_name);
            $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_name);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
            $spreadsheet = $reader->load($file_name);
            //Hola excel 0
            $hojaActual = $spreadsheet->getSheet(0);
            $letraMayorDeColumna = $hojaActual->getHighestColumn();
            $csize = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);
            $rsize = $hojaActual->getHighestRow();
            
            $message = '<div class="alert alert-success">Data Imported Successfully</div>';
        } else {
            $message = '<div class="alert alert-danger">Only .xls .csv or .xlsx file allowed</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Please Select File</div>';
    }
} catch (PDOExecption $e) {
    print "Error!: " . $e->getMessage() . "</br>";
}
echo $message;

?>

