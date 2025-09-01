<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';

$_city = $_REQUEST['city'];
$_area = $_REQUEST['area'];

$sql = 'SELECT * FROM tZipArea WHERE zCity="'.$_city.'" AND zArea="'.$_area.'";';

$conn = new first1DB();
$tmp = $conn->one($sql);

exit($tmp['zArea'].'_'.$tmp['zZip'].'_');

?>