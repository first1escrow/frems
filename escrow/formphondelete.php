<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$id = addslashes(trim($_POST['id']));

$sql = 'DELETE FROM tContractPhone WHERE cId = "' . $id . '";';
$conn->Execute($sql);

exit('ok');
