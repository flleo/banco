<?php

include 'connect.php';

$connect = connect();

function saldo() {
    global $connect;
    $conn = $connect;
    $query = "SELECT sum(saldo) FROM movimientos";
    $stmt = $conn->prepare($query);    
    $stmt->execute();
    $results = $stmt->fetchAll();
    return $results;
}

function carga()
{
    global $connect;
    $conn = $connect;
    $query = "SELECT * FROM movimientos ORDER BY fecha_valor DESC";
    $stmt = $conn->prepare($query);    
    $stmt->execute();
    $results = $stmt->fetchAll();
    return $results;

}

function insert($icampos,$ivalues,$iivalues)
{
    global $connect;
    $conn = $connect;
    $query = " INSERT INTO movimientos ($icampos) VALUES 
    ($ivalues) ";                     
    $statement = $conn->prepare($query);
    try {
        $statement->execute($iivalues);
    } catch (PDOExecption $e) {
        $conn->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
    }      

}








