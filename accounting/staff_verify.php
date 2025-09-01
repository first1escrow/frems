<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';

if (session_status() != 2) {
    session_start();
}

$acc = $_POST['staff_account'];
$pwd = $_POST['staff_password'];

$sql = 'SELECT * FROM tPeopleInfo WHERE pAccount = :acc AND pPassword = :pwd;';

$conn = new first1DB;
$rs = $conn->all($sql, ['acc' => $acc, 'pwd' => $pwd]);
$max = count($rs);

if ($max>0) {
	header("location:mobile.php"); 
} else {
	header("location:staff_login.php");
}
?>
