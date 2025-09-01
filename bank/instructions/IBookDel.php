<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$id = empty($_POST['id']) ? null : $_POST['id'];
if (!preg_match('/^\d+$/', $id)) {
    http_response_code(400);
    exit('異常操作');
}

$conn = new first1DB;

$sql  = 'SELECT bCategory, bExport_nu, bStatus, bDel FROM tBankTrankBook WHERE bId = :id';
$book = $conn->one($sql, ['id' => $id]);

if (empty($book)) {
    http_response_code(404);
    exit('查無指示書');
}

if ($book['bStatus'] == 2) {
    http_response_code(400);
    exit('指示書已審核，禁止刪除');
}

//案件為一般出款指示書
if ($book['bCategory'] == 1) {
    if (empty($book['bExport_nu'])) {
        http_response_code(400);
        exit('媒體檔出款紀錄不存在');
    }

    $sql = 'SELECT tExport_nu, tBankLoansDate, tPayOk FROM tBankTrans WHERE tExport_nu = :export_nu;';
    $rs  = $conn->all($sql, ['export_nu' => $book['bExport_nu']]);
    if (!empty($rs)) {
        http_response_code(400);
        exit('媒體檔已出款，禁止刪除');
    }
}

$sql = "UPDATE
			tBankTrankBook
		SET
			bDel = '1',
			bModifyName = '" . $_SESSION['member_name'] . "',
			bModifyDate = '" . date('Y-m-d H:i:s') . "'
		WHERE
			bId ='" . $_POST['id'] . "'
		";

if ($conn->exeSql($sql)) {
    exit('刪除成功');
}

http_response_code(400);
exit('刪除失敗');
