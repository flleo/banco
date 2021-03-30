<?php

include 'mysql.php';
include 'cargaExcel.php';


$b = $_GET['b'];

switch ($b) {
    case 'bbva':        bbva();         break;
    case 'caixa':       caixa();        break;
    case 'santander':   santander();    break;
}

function santander()
{
    $entidad = array('FECHA OPERACIÓN','FECHA VALOR','CONCEPTO','IMPORTE EUR','SALDO');
    $campos = array('fecha_contable', 'fecha_valor', 'concepto', 'importe', 'saldo');
    $bvalues = array( ':fecha_contable', ':fecha_valor', ':concepto', ':importe', ':saldo');
    global $csize,$rsize,$hojaActual,$message;
    //Recuperamos campos de excel similares a la tabla mysql e insertamos en tabla
    $camposr = [];//Campos de la fila no vacios (reales)
    $bvaluesr = [];//Campos de bindeo no vacios (reales)
    $values = []; //Array de inserción
    $celda = null;
    $valorform = null; //Valor de la celda
    $m = 0; $i=9; $j=1;
    if (verificaArchivo($i-1,$j,$csize,$entidad,$hojaActual)) {   //Comprobamos si el fichero corresponde con el banco
        for ($i=9; $i <= $rsize; $i++) {
            for ($j=1; $j <= $csize; $j++) {          
                //Valor de la celda
                $celda = $hojaActual->getCellByColumnAndRow($j, $i);
                $valorform = $celda->getValue();
                //A partir de esta fila están los valores a insertar, actualizamos los campos necesarios también.
                if ($valorform != '') {
                
                    //Recuperamos valores de los movimientos de excel, los añadimos a $ivalues [clave][valor]
                    if ($j == 1) {
                        $valorform = $celda->getFormattedValue();
                        $values[':fecha_contable'] = date("Y-m-d", strtotime($valorform));
                        $camposr[$m] = $campos[$j-1];
                    } elseif ($j == 2) {
                        $valorform = $celda->getFormattedValue();
                        $values[':fecha_valor'] = date("Y-m-d", strtotime($valorform));
                        $camposr[$m] = $campos[$j-1];
                    } else {
                        $values[$bvalues[$j-1]] = $valorform;
                        $camposr[$m] = $campos[$j-1];
                    }
                    $bvaluesr[$m] = $bvalues[$j-1];
                }          
                $m++;
                if ($j == $csize) {                    // Insertamos en tabla
                $camposs = implode(", ", $camposr);    //Nos da un string
                $bvaluess = implode(", ", $bvaluesr);
                    if ($values != null) {
                        //Campo único
                        $camposs = $camposs.", referencia";
                        $bvaluess = $bvaluess.", :referencia";
                        $values[':referencia'] = 'santander';
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
                    $m = 0;
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


function caixa()
{
    $entidad = array('Movimiento','Fecha','Fecha valor', 'Más datos','Importe','Saldo');
    $campos = array('concepto', 'fecha_contable', 'fecha_valor', 'observaciones', 'importe', 'saldo');
    $bvalues = array(':concepto', ':fecha_contable', ':fecha_valor', ':observaciones', ':importe', ':saldo');
    global $csize,$rsize,$hojaActual,$message;
    //Recuperamos campos de excel similares a la tabla mysql e insertamos en tabla
    $camposr = [];//Campos de la fila no vacios (reales)
    $bvaluesr = [];//Campos de bindeo no vacios (reales)
    $values = []; //Array de inserción
    $celda = null;
    $valorform = null; //Valor de la celda
    $m = 0; $i = 4; $j = 1;
    if (verificaArchivo($i-1,$j,$csize,$entidad,$hojaActual)) {   //Comprobamos si el fichero corresponde con el banco
        for ($i=4; $i <= $rsize; $i++) {
            for ($j=1; $j <= $csize; $j++) {
                //Valor de la celda
                $celda = $hojaActual->getCellByColumnAndRow($j, $i);
                $valorform = $celda->getValue();
                //A partir de esta fila están los valores a insertar, actualizamos los campos necesarios también.
                if ($valorform != '') {
                    //Recuperamos valores de los movimientos de excel, los añadimos a $ivalues [clave][valor]
                    if ($j == 2) {
                        $valorform = $celda->getFormattedValue();
                        $values[':fecha_contable'] = date("Y-m-d", strtotime(str_replace('/', '-', $valorform)));
                        $camposr[$m] = $campos[$j-1];
                    } elseif ($j == 3) {
                        $valorform = $celda->getFormattedValue();
                        $values[':fecha_valor'] = date("Y-m-d", strtotime(str_replace('/', '-', $valorform)));
                        $camposr[$m] = $campos[$j-1];
                    } else {
                        $values[$bvalues[$j-1]] = $valorform;
                        $camposr[$m] = $campos[$j-1];
                    }
                    $bvaluesr[$m] = $bvalues[$j-1];
                }
                $m++;
                if ($j == $csize) {
                    // Insertamos en tabla
                $camposs = implode(", ", $camposr);    //Nos da un string
                $bvaluess = implode(", ", $bvaluesr);
                    if ($values != null) {
                        //Campo único
                        $camposs = $camposs.", referencia";
                        $bvaluess = $bvaluess.", :referencia";
                        $values[':referencia'] = 'lacaixa';
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
                    $m = 0;
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
           

function bbva()
{
    $entidad = array('Fecha Contable','Fecha valor','Código','Concepto','Observaciones','Importe','Saldo','Divisa','Oficina','Remesa');
    $campos = array('fecha_contable', 'fecha_valor', 'codigo', 'concepto', 'observaciones', 'importe', 'saldo', 'divisa', 'oficina', 'remesa');
    $bvalues = array(':fecha_contable', ':fecha_valor', ':codigo', ':concepto', ':observaciones', ':importe', ':saldo', ':divisa', ':oficina', ':remesa');
    global $csize,$rsize,$hojaActual,$message;
    //Recuperamos campos de excel similares a la tabla mysql e insertamos en tabla
    $camposr = [];//Campos de la fila no vacios (reales)
    $bvaluesr = [];//Campos de bindeo no vacios (reales)
    $values = []; //Array de inserción
    $celda = null;
    $valorform = null; //Valor de la celda
    $m = 0; $i = 17;   $j = 2;
    if (verificaArchivo($i-1,$j,$csize,$entidad,$hojaActual)) {   //Comprobamos si el fichero corresponde con el banco
        for ($i=17; $i <= $rsize; $i++) {
            for ($j=2; $j <= $csize; $j++) {
                //Valor de la celda
                $celda = $hojaActual->getCellByColumnAndRow($j, $i);
                $valorform = $celda->getValue();
                //A partir de esta fila están los valores a insertar, actualizamos los campos necesarios también.
                if ($valorform != '') {
                    //Recuperamos valores de los movimientos de excel, los añadimos a $ivalues [clave][valor]
                    if ($j == 2) {
                        $values[':fecha_contable'] = date("Y-m-d", strtotime(str_replace('/', '-', $valorform)));
                        $camposr[$m] = $campos[$j-2];
                    } elseif ($j == 3) {
                        $values[':fecha_valor'] = date("Y-m-d", strtotime(str_replace('/', '-', $valorform)));
                        $camposr[$m] = $campos[$j-2];
                    } else {
                        $values[$bvalues[$j-2]] = $valorform;
                        $camposr[$m] = $campos[$j-2];
                    }
                    $bvaluesr[$m] = $bvalues[$j-2];
                }
                $m++;
                if ($j == $csize) {
                    // Insertamos en tabla
                        $camposs = implode(", ", $camposr);    //Nos da un string
                        $bvaluess = implode(", ", $bvaluesr);
                    if ($values != null) {
                        //Campo único
                        $camposs = $camposs.", referencia";
                        $bvaluess = $bvaluess.", :referencia";
                        $values[':referencia'] = 'bbva';
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
                    $l = 0;
                    $m = 0;
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


function exceptions_error_handler($severity, $message, $filename, $lineno)
{
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
}

echo $message;
  
?>

