<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once dirname(dirname(dirname(__DIR__))) . '/first1DB.php';
require_once dirname(dirname(dirname(__DIR__))) . '/class/payByCase/payByCase.class.php';

use First1\V1\PayByCase\PayByCase;

//取得地政士正在進行中的案件
function getWorkingCases(&$conn, $sId)
{
    $sql = 'SELECT a.cCertifiedId FROM tContractCase AS a JOIN tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId WHERE b.cScrivener = ' . $sId . ' AND a.cCaseStatus = 2;';
    $rs  = $conn->all($sql);

    return empty($rs) ? [] : array_column($rs, 'cCertifiedId');
}

//設定地政士
function setScrivener(&$conn, $sId)
{
    $sql = 'UPDATE tScrivener SET sFeedDateCat = 2 WHERE sId = :sId;';
    return $conn->exeSql($sql, ['sId' => $sId]);
}

$fh   = __DIR__ . '/data/s.csv';
$list = explode("\r\n", file_get_contents($fh));

$conn = new first1DB;

$paybycase = new PayByCase($conn);
foreach ($list as $v) {
    if (!preg_match("/^\w+$/iu", $v)) {
        continue;
    }

    $v = trim($v);

    $fh = __DIR__ . '/log';
    if (!is_dir($fh)) {
        mkdir($fh, 0777, true);
    }
    $fh .= '/' . $v . '.log';

    if (is_file($fh)) {
        unlink($fh);
    }

    $id = (int) substr($v, 2);

    if (empty(setScrivener($conn, $id))) { //設定地政士
        throw new Exception('設定失敗(' . $v . ')');
    }

    $cases = getWorkingCases($conn, $id); //取得進行中案件

    if (!empty($cases)) {
        foreach ($cases as $cId) {
            $paybycase->salesConfirmList($cId);
            file_put_contents($fh, date("Y-m-d H:i:s") . ' 地政士ID: ' . $v . ' 案件: ' . $cId . "... 設定完成\n", FILE_APPEND);
        }
    }

    echo date("Y-m-d H:i:s") . ' 地政士ID: ' . $v . ' 進行中案件: ' . count($cases) . "\n";
    file_put_contents($fh, print_r($cases, true), FILE_APPEND);
}

exit;
