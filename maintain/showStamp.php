<?php
//從資料庫取得圖片
require_once dirname(__DIR__) . '/openadodb.php';

$bId = $_REQUEST['bId'];

if (!empty($bId)) {
    $sql = 'SELECT * FROM tBranchStamp WHERE 1 AND bBranchId = "' . $bId . '" ORDER BY bId DESC LIMIT 1;';
    $rs  = $conn->Execute($sql);

    //顯示圖片
    if (!$rs->EOF) {
        header("Content-type: image/jpeg");
        header('Content-Disposition: inline; filename="stamp.jpg"');

        echo base64_decode($rs->fields['bStamp']);
    }
}
