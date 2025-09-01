<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$bId = $_POST['id'];
if (!preg_match("/^\d+$/", $bId)) {
    exit('未指定品牌');
}

$conn = new first1DB;

//查詢是否有已指定的仲介店家
$sql    = 'SELECT bId as realty FROM tBranch WHERE bBrand = :bId;';
$realty = $conn->all($sql, ['bId' => $bId]);

if (!empty($realty)) {
    exit('已有建立該品牌店家資料！無法刪除...');
}

//刪除
$sql = 'DELETE FROM tBrand WHERE bId = :bId;';
echo $conn->exeSql($sql, ['bId' => $bId]) ? '已刪除' : '刪除失敗';

exit;
