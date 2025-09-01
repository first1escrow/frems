<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$type      = isset($_POST['add']) && is_string($_POST['add']) ? addslashes(trim($_POST['add'])) : '';
$cid       = trim(addslashes($_POST['cid']));
$cateogry  = trim(addslashes($_POST['cateogry']));
$new_phone = addslashes(trim($_POST['new_phone']));
$others_id = addslashes(trim($_POST['others_id']));

if ($new_phone) {
    $_others_id = empty($others_id) ? 'NULL' : '"' . $others_id . '"';
    $sql        = 'INSERT INTO tContractPhone(cCertifiedId, cIdentity, cMobileNum, cOthersId) VALUES ("' . $cid . '", "' . $cateogry . '", "' . $new_phone . '", ' . $_others_id . ');';
    $conn->Execute($sql);

    $_others_id = null;unset($_others_id);
}

if (isset($_POST['phone']) && is_array($_POST['phone']) && count($_POST['phone']) > 0) {
    for ($i = 0; $i < count($_POST['phone']); $i++) {
        $_others_id = empty($others_id) ? 'IS NULL' : '"' . $others_id . '"';
        $phone_val  = isset($_POST['phone'][$i]) ? $_POST['phone'][$i] : '';
        $id_val     = isset($_POST['id'][$i]) ? $_POST['id'][$i] : '';
        $sql        = 'UPDATE tContractPhone SET cMobileNum = "' . addslashes(trim($phone_val)) . '" WHERE cCertifiedId = "' . $cid . '" AND cId = "' . addslashes(trim($id_val)) . '" AND cOthersId ' . $_others_id . ';';
        $conn->Execute($sql);
        $_others_id = null;unset($_others_id);
    }
}

exit('ok');
