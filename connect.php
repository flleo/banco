<?php

function connect() 
{
    $connect = new PDO("mysql:host=localhost;dbname=timado1p_banco", "timado1", "timado1p");
    //$connect = new PDO("mysql:host=localhost;dbname=timado1p_banco", "timado1p_usuario", "Practicas2021");
    //$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $connect;
}
