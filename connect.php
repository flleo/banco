<?php

function connect() 
{
    $connect = new PDO("mysql:host=localhost;dbname=banco", "root", "");
    //$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $connect;
}
