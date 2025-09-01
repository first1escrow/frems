<?php
include_once '../class/getAddress.php' ;
include_once '../openadodb.php' ;

 $_POST = escapeStr($_POST) ;
$city = $_POST['city'] ;

echo getArea($conn,$city) ;
?>