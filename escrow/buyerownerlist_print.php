<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/includes/lib.php';
require_once dirname(__DIR__) . '/session_check.php';
// require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/first1DB.php';

$vr_code = trim($_REQUEST['cid']);
$_iden   = trim($_REQUEST['iden']);

$cCertifiedId = $vr_code;

if (!in_array($_iden, ['o', 'b'])) {
    exit('資料錯誤!!');
}

$cIdentity = ($_iden == 'o') ? 2 : 1; // 賣：2、買：1

$conn = new first1DB;

$sql = 'SELECT cId, cName, cIdentifyId, cMobileNum FROM tContractOthers WHERE cCertifiedId = "' . $cCertifiedId . '" AND cIdentity = "' . $cIdentity . '" ORDER BY cId ASC;';
$rs  = $conn->all($sql);

$list = [];
//主要買方的資料
    if($cIdentity == 1) {
        $sql = "SELECT 
                    cIdentifyId, cName, cMobileNum 
                FROM 
                    tContractBuyer
                WHERE 
                    cCertifiedId = '".$cCertifiedId."'
                ";
        $main = $conn->one($sql);
    }

//主要賣方的資料
    if($cIdentity == 2) {
        $sql = "SELECT 
                    cIdentifyId, cName, cMobileNum 
                FROM 
                    tContractOwner
                WHERE 
                    cCertifiedId = '".$cCertifiedId."'
                ";
        $main = $conn->one($sql);
    }
if (!empty($rs)) {
    $list = $rs;
    $cIds = array_column($list, 'cId');

    $phone = [];
    $sql   = 'SELECT cMobileNum, cOthersId FROM tContractPhone WHERE cCertifiedId = "' . $cCertifiedId . '" AND cIdentity IN (1, 2) AND cOthersId IN (' . implode(', ', $cIds) . ');';
    $rs    = $conn->all($sql);

    foreach ($rs as $k => $v) {
        $rs[$k]                = null;unset($rs[$k]);
        $rs[$v['cOthersId']][] = $v['cMobileNum'];
    }

    foreach ($list as $k => $v) {
        $_phone                 = array_merge((array)[$v['cMobileNum']], (array)$rs[$v['cId']]); //合併主、副手機號碼
        $_phone                 = array_filter($_phone); //去除空白的手機號碼
        $list[$k]['cMobileNum'] = implode(',', $_phone);

        $_phone = null;unset($_phone);
    }

    $rs = $cIds = null;
    unset($rs, $cIds);
}
array_unshift($list, ['cIdentifyId' => $main['cIdentifyId'], 'cName' => $main['cName'], 'cMobileNum' => $main['cMobileNum']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

	<title>列印</title>
</head>
<body style="margin: 10px;">
    <div style="padding-bottom:20px;">
        <input type="button" name="button" id="button" value="確認列印" onclick="window.print();" />
    </div>
    <table class="table  table-striped">
        <tr align="center">
            <th width="50px;">編號</th>
            <th width="20%">姓名</th>
            <th width="20%">身分證</th>
            <th width="20%">電話</th>
        </tr>
        <?php
foreach ($list as $k => $v) {
    echo '
        <tr  align="center" >
            <td>' . ($k + 1) . '</td>
            <td>' . $v['cName'] . '</td>
            <td>' . $v['cIdentifyId'] . '</td>
            <td>' . $v['cMobileNum'] . '</td>
        </tr>
    ';
}
?>
</table>
</body>
</html>

