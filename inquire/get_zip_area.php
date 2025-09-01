<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';

$_city = $_REQUEST['city'];

$conn = new first1DB;

$sql = 'SELECT * FROM tZipArea WHERE zCity = :city;';
$rs = $conn->all($sql, ['city' => $_city]);

$_area = "<option>請選擇</option>\n";
foreach ($rs as $v) {
	$_area .= "<option value='".$v['zArea']."'>".$v['zArea']."</option>\n";
}

exit($_area);
?>