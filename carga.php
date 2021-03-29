<?php

include 'mysql.php';

$res = carga();
$saldo = 0.0;
echo "      <table id='datos' class='table'>
                <thead>
                    <tr>
                        <th>Fecha Contable</th>
                        <th>Fecha Valor</th>
                        <th>Código</th>
                        <th>Concepto</th>
                        <th>Observaciones</th>
                        <th>Importe</th>
                        <th>Saldo</th>
                        <th>Divisa</th>
                        <th>Oficina</th>
                        <th>Remesa</th>
                    </tr>
                </thead>
                <tbody>";
                        foreach ($res as $key) {
                            echo "<tr>";
                            for ($i=2; $i<=11; $i++) {
                                echo "<td>$key[$i]</td>";                                
                            }                        
echo                            "</tr>"; 
                            $saldo += $key[8];
                        }
echo "                                     
                </tbody>
            </table>";
echo"<script>$('#saldo').html($saldo);</script>";
