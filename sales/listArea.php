<?php
include_once '../class/getAddress.php' ;
include_once '../openadodb.php' ;

$city = trim(addslashes($_POST['city'])) ;

echo getArea($conn,$city) ;
?>