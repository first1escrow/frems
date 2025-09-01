<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

//新增前台紀錄
function insertPayByCase(&$conn, $uids)
{
    if (empty($uids)) {
        throw new Exception('Empty BankTrans Id');
    }

    $sql = 'SELECT
                a.bCertifiedId,
                a.bMoney,
                a.bDate,
                DATE(a.bExport_time) as date,
                b.fTargetId,
                b.fTax,
                b.fNHI
            FROM
                tBankTransRelay AS a
            JOIN
                tFeedBackMoneyPayByCase AS b ON a.bCertifiedId = b.fCertifiedId AND b.fTarget = "S"
            WHERE
                a.bUid IN ("' . $uids . '")
                AND a.bKind LIKE "地政士回饋金";';
    $rs = $conn->all($sql);

    if (empty($rs)) {
        return true;
    }

    $values = [];
    foreach ($rs as $v) {
        $values[] = '(
            1,
            "SC",
            "' . $v['fTargetId'] . '",
            "地政士回饋金",
            "' . $v['date'] . '",
            ' . ($v['bMoney'] + $v['fTax'] + $v['fNHI']) . ',
            ' . $v['fNHI'] . ',
            ' . $v['fTax'] . ',
            ' . $v['bMoney'] . ',
            ' . $v['bCertifiedId'] . ',
            0,
            NOW()
        )';
    }

    $sql = 'INSERT INTO
                tStoreFeedBackMoneyFrom_Record
            (
                sType,
                sStoreCode,
                sStoreId,
                sSeason,
                sDate,
                sFeedBackMoney,
                sNHITax,
                sTax,
                sAmountReceived,
                sMemo,
                sDel,
                sCreated_at
            ) VALUES ' . implode(',', $values) . ';';
    return $conn->exeSql($sql);
}

//鎖定案件合約書回饋金資訊
function lockCaseFeedbackInfo(&$conn, $uids)
{
    if (empty($uids)) {
        throw new Exception('Empty BankTrans Id');
    }

    $sql = 'SELECT bCertifiedId FROM tBankTransRelay WHERE bUid IN ("' . $uids . '") AND bKind = "地政士回饋金";';
    $rs  = $conn->all($sql);

    if (!empty($rs)) {
        $cIds = array_column($rs, 'bCertifiedId');
        $sql  = 'UPDATE tContractCase SET cFeedBackClose = 1, cFeedbackDate = "' . date('Y-m-d') . '" WHERE cCertifiedId IN ("' . implode('","', $cIds) . '") AND cFeedbackDate IS NULL;';
        $conn->exeSql($sql);

        $sql  = 'UPDATE tContractCase SET cBankList = "' . date('Y-m-d') . '" WHERE cBankRelay = "Y" AND cBankList = "" AND cCertifiedId IN ("' . implode('","', $cIds) . '");';
        $conn->exeSql($sql);
    }

    return true;
}

$conn = new first1DB;

$post = $_POST;
$uids = implode('","', $post['uid']);

//設定批號、檔名
$export_nu = (date("Y") - 1911) . date("md");

$sql = 'SELECT bExport_nu FROM tBankTransRelay WHERE bExport_nu IS NOT NULL ORDER BY bExport_nu DESC LIMIT 1;';
$rs  = $conn->one($sql);

$order = 1; //今日批次
if (!empty($rs)) {
    $tExport_nu    = explode('-', $rs['bExport_nu']);
    $tExport_nu[1] = (int) $tExport_nu[1];

    if (((date("Y") - 1911) . date("md")) != ($tExport_nu[0])) {
        $tExport_nu[1] = 0;
    }

    $order = $tExport_nu[1] + 1;

    $tExport_nu = null;unset($tExport_nu);
}
$order = str_pad($order, 3, '0', STR_PAD_LEFT);
$export_nu .= '-' . $order;

$order = null;unset($order);

//取得欲匯出紀錄
$sql = 'SELECT
            a.*,
            (SELECT bBank3_name FROM tBank WHERE bBank3 = SUBSTRING(a.bBankCode, 1, 3) AND bBank4 = SUBSTRING(a.bBankCode, 4, 4)) AS bankAlias
        FROM
            tBankTransRelay AS a
        WHERE
            a.bUid IN ("' . $uids . '")
        ORDER BY
            a.bOrderTime, a.bCertifiedId, a.bCreated_at ASC,  a.bKind DESC;';
$data = $conn->all($sql);

//更新中繼銀行出款紀錄
$sql = 'UPDATE tBankTransRelay SET bExport = 1, bExport_time = NOW(), bExport_nu = :export_nu, bPayOk = 1 WHERE bUid IN ("' . $uids . '");';
$conn->exeSql($sql, ['export_nu' => $export_nu]);

//新增前台紀錄
insertPayByCase($conn, $uids);

//鎖定案件合約書回饋金資訊
lockCaseFeedbackInfo($conn, $uids);

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("中繼帳戶整批匯款");
$objPHPExcel->getProperties()->setDescription("中繼帳戶回饋金與保證費整批匯款");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(36);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(36);

//設定字體
$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

//設定標題
$objPHPExcel->getActiveSheet()->setCellValue('A1', '收款銀行代號');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '收款銀行名稱');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '匯款金額');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '手續費');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '郵電費');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '收款帳號');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '收款戶名');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '備註');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '通知方式代號');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '通知方式');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '匯款申請人');

//內容
if (!empty($data)) {
    foreach ($data as $k => $v) {
        $row = $k + 2;

        $remark = ($v['bKind'] == '地政士回饋金') ? '第一建經' . $v['bCertifiedId'] . '回饋金' : $v['bCertifiedId'] . '保證費';

        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $v['bBankCode'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $v['bankAlias'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $v['bMoney'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, 0, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, 0, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $v['bAccount'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $v['bAccountName'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $remark, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $row, '第一建築經理股份有限公司', PHPExcel_Cell_DataType::TYPE_STRING);
    }
}

//產出
$_file = $export_nu . '.xlsx';

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");
