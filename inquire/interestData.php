<?php
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';

$cid      = empty($_REQUEST['cid']) ? '' : $_REQUEST['cid'];
$lastDate = empty($_REQUEST['lastDate']) ? '' : $_REQUEST['lastDate'];
$lastDate = str_replace('/', '-', $lastDate);

if (! preg_match("/^[0-9]{9}$/", $cid)) {
    exit('保證號碼錯誤!');
}

if (empty($lastDate)) {
    $lastDate = date("Y-m-d");
} else {
    $arr      = [];
    $arr      = explode('-', $lastDate);
    $lastDate = ($arr[0] + 1911) . '-' . $arr[1] . '-' . $arr[2];
    $arr      = null;unset($arr);
}

$sql = 'SELECT * FROM tBankCode WHERE bAccount LIKE "%' . $cid . '";';
$rs  = $conn->Execute($sql);

$account = $rs->fields['bAccount'];
if (! preg_match("/^[0-9]{14}$/", $account)) {
    exit('保證號碼錯誤!!');
}

//取結案日
$sql = "SELECT cEndDate FROM tContractCase WHERE cEscrowBankAccount = '" . $account . "'";
$rs  = $conn->Execute($sql);

$tmp      = explode(' ', $rs->fields['cEndDate']);
$cEndDate = $tmp[0];
if ($cEndDate != '0000-00-00') {
    $lastDate = $cEndDate;
}

$list = [];
$sql  = 'SELECT * FROM tBankInterest WHERE tAccount = "' . $account . '" AND tTime <= "' . $lastDate . '" ORDER BY tTime ASC;';
$rs   = $conn->Execute($sql);

while (! $rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}

//20250331 增加預估未來利息計算列表
$last = $list[count($list) - 1];
if (! empty($last)) {
    $day = new DateTime($last['tTime']);
    $day->modify('+1 day');

    while ($day->format('Y-m-d') <= $lastDate) {
        $list[] = [
            'tTime'     => $day->format('Y-m-d'),
            'tMoney'    => $last['tMoney'],
            'tRate'     => $last['tRate'],
            'tInterest' => $last['tInterest'],
            'tRemark'   => '(以' . $last['tTime'] . '的帳戶金額與年利率預估計算)',
        ];

        $day->modify('+1 day');
    }
}

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件利息表");
$objPHPExcel->getProperties()->setDescription("第一建經案件利息明細表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('利息明細');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(36);

//保證號碼
$row = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '保證號碼');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $cid, PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->mergeCells('B' . $row . ':C' . $row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':C' . $row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '履保帳號');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $account, PHPExcel_Cell_DataType::TYPE_STRING);
$objPHPExcel->getActiveSheet()->mergeCells('B' . $row . ':C' . $row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':C' . $row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '＊利息計算採單利方式');
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->getColor()->setARGB('FF0000');

$row++;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '＊利息公式：利息(四捨五入) = 帳戶金額(當日) * 年利率(%) / 365 天');
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getFont()->getColor()->setARGB('FF0000');

$row++;

//標題
$row = 6;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, '日期');
$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, '帳戶金額');
$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, '年利率(%)');
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, '利息(四捨五入)');
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '累計利息');
$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '備註');

$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$row++;

//內容
foreach ($list as $k => $v) {
    $arr   = [];
    $arr   = explode('-', $v['tTime']);
    $tTime = ($arr[0] - 1911) . '-' . $arr[1] . '-' . $arr[2];
    $arr   = null;unset($arr);

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $tTime);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $v['tMoney']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, ($v['tRate'] * 100));
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $v['tInterest']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '=SUM(D7:D' . $row . ')');

    if (! empty($v['tRemark'])) {
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $v['tRemark']);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(9);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getFont()->getColor()->setARGB('FF0000');
    }

    $row++;
}

//小計
$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, '利息金額合計');
$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, '=SUM(D7:D' . ($row - 1) . ')');

$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

$row++;

//產出 Excel
$_file = iconv('UTF-8', 'BIG5', '利息明細');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file . '.xlsx');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;
