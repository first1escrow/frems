<?php
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/class/traits/CheckIn.trait.php';
require_once dirname(__DIR__) . '/class/HumanResource.class.php';
require_once dirname(__DIR__) . '/class/staff.class.php';
require_once dirname(__DIR__) . '/class/leaveHourCount.class.php';
require_once dirname(__DIR__) . '/includes/staffHRBeginDate.php';

use First1\V1\HR\HR;
use First1\V1\Staff\LeaveHourCount;

$from = $_POST['applyCheckFrom'];
if (empty($from) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $from)) {
    throw new Exception('from data abnormal');
}

$to = $_POST['applyCheckTo'];
if (empty($to) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $to)) {
    throw new Exception('to data abnormal');
}

$dateRange = $from . ' ~ ' . $to;

$staff = empty($_POST['staff']) ? null : $_POST['staff'];

$hr            = HR::getInstance();
$hr->BEGINDATE = BEGINDATE;

$conn = new first1DB;

if (! empty($staff)) {
    $sql = 'SELECT pId, pName, pOnBoard FROM tPeopleInfo WHERE pId = :staff;';
    $rs  = $conn->one($sql, ['staff' => $staff]);

    $staff = null;
    if (! empty($rs)) {
        $staff[$rs['pId']] = $rs;
    }
}

if (empty($staff)) {
    $dept = in_array($_SESSION['member_id'], [2, 3, 13, 129]) ? null : $_SESSION['member_pDep'];
    $dept = ($_SESSION['member_id'] == 1) ? [5, 11] : $dept; //履保主管兼看行政部門

    $staff = $hr->dumpStaffData(null, $dept);
}

if (empty($staff)) {
    throw new Exception('no staff data');
}

$from .= ' 00:00:00';
$to .= ' 23:59:59';

$sql = '';
if (! empty($dept)) {
    $dept = is_array($dept) ? $dept : [$dept];
    $sql  = ' AND b.pDep IN (' . implode(',', $dept) . ')';
}

$sql = 'SELECT
            a.sStaffId,
            a.sApplyDate,
            a.sApplyTime,
            a.sApplyType,
            a.sReason,
            b.pDep
        FROM
            tStaffCheckInApply AS a
        JOIN
            tPeopleInfo AS b
        ON
            a.sStaffId = b.pId
        WHERE
            a.sApplyDate >= :from
            AND a.sApplyDate <= :to
            AND b.pJob = 1
            ' . $sql . '
        ORDER BY
            a.sStaffId, a.sApplyDate;';
$rs = $conn->all($sql, ['from' => $from, 'to' => $to]);

$data = [];
if (! empty($rs)) {
    foreach ($rs as $v) {
        $data[$v['sStaffId']]['staff']     = $staff[$v['sStaffId']];
        $data[$v['sStaffId']]['checkIn'][] = $v;
    }
}

$staff = null;unset($staff);

$rs         = $conn->all('SELECT dId, dDep, dTitle FROM tDepartment');
$department = [];
foreach ($rs as $v) {
    $department[$v['dId']] = $v['dDep'];
}
$conn = $rs = null;unset($conn, $rs);

$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("假勤明細");
$objPHPExcel->getProperties()->setDescription('第一建經' . $year . '年度' . $month . '月份補打卡明細');

//20250123 增加總表
$objPHPExcel->createSheet();

// Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('總表');

$summary = [];
$sheets  = 0;
foreach ($data as $k => $v) {
    $summary[] = [
        'dept'  => $v['staff']['pDep'],
        'name'  => $v['staff']['pName'],
        'count' => count($v['checkIn']),
    ];

    if ($sheets > 0) {
        $objPHPExcel->createSheet();
    }

    //指定目前工作頁
    // $objPHPExcel->setActiveSheetIndex(($k + 1));
    $objPHPExcel->setActiveSheetIndex($sheets + 1);

    //調整欄位寬度
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

    //調整欄位高度
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);

    // 設定文字置中
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    //寫入表頭資料
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '部門');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '補打卡日期');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '上班／下班');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '時間');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '備註');

    $pName = $v['staff']['pName'];
    foreach ($v['checkIn'] as $index => $d) {
        $row             = $index + 2;
        $dept            = ($row == 2) ? $department[$v['staff']['pDep']] : '';
        $pName           = ($index == 0) ? $pName : '';
        $d['sApplyType'] = ($d['sApplyType'] == 'IN') ? '上班' : '下班';

        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $dept, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $pName, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $d['sApplyDate'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $d['sApplyType'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $d['sApplyTime'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $d['sReason'], PHPExcel_Cell_DataType::TYPE_STRING);
    }

    // Rename sheet 重命名工作表標籤
    $objPHPExcel->getActiveSheet()->setTitle($v['staff']['pName']);

    $sheets++;
}

//總表內容
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);

// 設定文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('A1', '部門');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '次數');

foreach ($summary as $v) {
    $row = $objPHPExcel->getActiveSheet()->getHighestRow() + 1;

    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $department[$v['dept']], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $v['name'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $v['count'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
}

//產出報表
$dateRange = str_replace('-', '', $dateRange);
$dateRange = str_replace('~', '-', $dateRange);
$dateRange = str_replace(' ', '', $dateRange);

$_file = '補打卡報表_' . $dateRange . '.xlsx';

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=' . $_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit;
