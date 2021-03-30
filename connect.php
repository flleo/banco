<?php

function connect() 
{
    $connect = new PDO("mysql:host=localhost;dbname=timado1p_banco", "root", "");
    //$connect = new PDO("mysql:host=localhost;dbname=timado1p_banco", "timado1practicas", "Practicas2021");
    //$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $connect;
}
