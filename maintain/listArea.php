<?php
require_once dirname(__DIR__) . '/class/getAddress.php';
require_once dirname(__DIR__) . '/openadodb.php';

$city = trim($_POST['city']);

echo getArea($conn, $city);
