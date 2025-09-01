<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$bId = $_POST['bId'];

if (!preg_match("/^\d+$/", $bId)) {
    exit('NG');
}

$sql = 'DELETE FROM tBranchStamp WHERE bBranchId = ' . $bId;
echo empty($conn->Execute($sql)) ? 'NG' : 'OK';

exit;
