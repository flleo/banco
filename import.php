<?php

include 'mysql.php';
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




$b = $_GET['b'];

$entidad = $campos = $bvalues = $i = $j = $k = $n = $o = null;
$camposr = $bvaluesr = $values = [];

switch ($b) {
    case 'bbva':  
        $entidad = array('Fecha Contable','Fecha valor','Código','Concepto','Observaciones','Importe','Saldo','Divisa','Oficina','Remesa');
        $campos = array('fecha_contable', 'fecha_valor', 'codigo', 'concepto', 'observaciones', 'importe', 'saldo', 'divisa', 'oficina', 'remesa');
        $bvalues = array(':fecha_contable', ':fecha_valor', ':codigo', ':concepto', ':observaciones', ':importe', ':saldo', ':divisa', ':oficina', ':remesa');
        $l=17; $m=2;    //fila y columna donde empiezan los datos en la hoja
        $o = 2;         //restarle a $j para fecha_contable en $campos
        $k = 2;         //columna dondo fecha_contable
        importa();         
        break;
    case 'caixa':       
        $entidad = array('Movimiento','Fecha','Fecha valor', 'Más datos','Importe','Saldo');
        $campos = array('concepto', 'fecha_contable', 'fecha_valor', 'observaciones', 'importe', 'saldo');
        $bvalues = array(':concepto', ':fecha_contable', ':fecha_valor', ':observaciones', ':importe', ':saldo');     
        $l=4; $m=1;    //fila y columna donde empiezan los datos en la hoja
        $o = 1;         //dondo fecha_contable en $campos
        $k = 2;        //columna dondo fecha_contable
        importa();         
        break;
    case 'santander':   
        $entidad = array('FECHA OPERACIÓN','FECHA VALOR','CONCEPTO','IMPORTE EUR','SALDO');
        $campos = array('fecha_contable', 'fecha_valor', 'concepto', 'importe', 'saldo');
        $bvalues = array( ':fecha_contable', ':fecha_valor', ':concepto', ':importe', ':saldo');      
        $l=9; $m=1;    //fila y columna donde empiezan los datos en la hoja
        $o = 1;         //dondo fecha_contable en $campos
        $k = 1;        //columna dondo fecha_contable
        importa();         
        break;
}

function importa() {   
    global $entidad,$campos,$bvalues,$l,$m,$k,$n,$o,$camposr,$bvaluesr,$values,$b,$csize,$rsize,$hojaActual,$message;
    //Recuperamos campos de excel similares a la tabla mysql e insertamos en tabla   
    $archivo_correcto =  verificaArchivo($l-1,$m,$csize,$entidad,$hojaActual);//Comprobamos si el fichero corresponde con el banco
    if ($archivo_correcto) {   
        for ($i=$l; $i <= $rsize; $i++) {
            for ($j=$m; $j <= $csize; $j++) {          
                //Valor de la celda
                $celda = $hojaActual->getCellByColumnAndRow($j, $i);
                $valorform = $celda->getValue();
                //A partir de esta fila están los valores a insertar, actualizamos los campos necesarios también.
                if ($valorform != '') {             
                    //Recuperamos valores de los movimientos de excel, los añadimos a $ivalues [clave][valor]
                    if ($j == $k) {
                        if($b != 'bbva'){
                            $valorform = $celda->getFormattedValue();
                            $values[':fecha_contable'] = date("Y-m-d", strtotime($valorform));
                        } else {
                            $values[':fecha_contable'] = date("Y-m-d", strtotime(str_replace('/', '-', $valorform)));
                        }
                        $camposr[$n] = $campos[$j-$o];
                    } elseif ($j == ($k+1)) {
                        $valorform = $celda->getFormattedValue();
                        $values[':fecha_valor'] = date("Y-m-d", strtotime($valorform));
                        $camposr[$n] = $campos[$j-$o];
                    } else {
                        $values[$bvalues[$j-$o]] = $valorform;
                        $camposr[$n] = $campos[$j-$o];
                    }
                    $bvaluesr[$n] = $bvalues[$j-$o];
                }          
                $n++;
                if ($j == $csize) {                    // Insertamos en tabla
                $camposs = implode(", ", $camposr);    //Nos da un string
                $bvaluess = implode(", ", $bvaluesr);
                    if ($values != null) {
                        //Campo único
                        $camposs = $camposs.", referencia";
                        $bvaluess = $bvaluess.", :referencia";
                        $values[':referencia'] = $b;
                        foreach ($values as $val) {
                            $values[':referencia'] = $values[':referencia'].$val;
                        }
                        //Insertamos
                        $res = insert($camposs, $bvaluess, $values);
                        if (!$res) {
                            $message = '<div class="alert alert-danger">El archivo ya existe</div>';
                            $i = $rsize;
                        }
                    }
                    $n = 0;
                    $camposr = [];
                    $bvaluesr = [];
                    $values = [];
                }
            }
        }
    } else {
        $message = '<div class="alert alert-danger">El archivo no se corresponde con la entidad bancaria</div>';
    }
}

function verificaArchivo($i,$j,$csize,$caixa,$hojaActual) {
    $ver = true; $l=0;
    for($j,$l; $j<$csize; $j++,$l++) {
        $celda = $hojaActual->getCellByColumnAndRow($j, $i);
        $valorform = $celda->getValue();
        if ($caixa[$l] != $valorform) {
            $ver = false;
            break;
        }
    }  
    return $ver;
}  

echo $message;
  
?>
