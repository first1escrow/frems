<?php
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/class/HumanResource.class.php';

use First1\V1\HR\HR;

$conn = new first1DB;
$hr   = HR::getInstance();

$staffId = empty($_POST['staff_b4']) ? null : $_POST['staff_b4'];

if (! empty($staffId)) {
    $sql = 'SELECT pId, pName, pOnBoard, pDep FROM tPeopleInfo WHERE pId = :staff;';
    $rs  = $conn->one($sql, ['staff' => $staffId]);

    $staff = null;
    if (! empty($rs)) {
        $staff[$rs['pId']] = $rs;
    }
} else {
    $staff = $hr->dumpStaffData();
}

if (empty($staff)) {
    throw new Exception('no staff data');
}

$defaultLeaves = $hr->getLeaveType();

$objPHPExcel = new PHPExcel();
$objPHPExcel->removeSheetByIndex(0);

//Set properties
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("員工假別剩餘時數統計");
$objPHPExcel->getProperties()->setDescription("員工假別剩餘時數統計");

foreach ($staff as $key => $staffInfo) {
    $sheet     = $objPHPExcel->createSheet();
    $staffName = $staffInfo['pName'];
    $sheet->setTitle($staffName);

    // Set column widths
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(12);
    $sheet->getColumnDimension('D')->setWidth(12);
    $sheet->getColumnDimension('E')->setWidth(12);

    $sheet->setCellValue('A1', '姓名');
    $sheet->setCellValue('B1', $staffName);
    $sheet->setCellValue('A2', '到職日');
    $sheet->setCellValue('B2', substr($staffInfo['pOnBoard'], 0, 10));

    // Set headers
    $sheet->setCellValue('A4', '假別');
    $sheet->setCellValue('B4', '總時數');
    $sheet->setCellValue('C4', '已休時數');
    $sheet->setCellValue('D4', '剩餘時數');

    $staffId = $staffInfo['pId'];

    $sql            = 'SELECT sLeaveId, sLeaveDefault, sLeaveBalance FROM tStaffLeaveDefault WHERE sStaffId = :staffId';
    $staffLeaveData = $conn->all($sql, ['staffId' => $staffId]);
    $staffLeave     = [];
    foreach ($staffLeaveData as $leave) {
        $staffLeave[$leave['sLeaveId']] = $leave;
    }

    $row = 5;
    foreach ($defaultLeaves as $leave) {
        $totalHours     = 0;
        $usedHours      = 0;
        $remainingHours = 0;
        if (isset($staffLeave[$leave['sId']])) {
            $totalHours     = $staffLeave[$leave['sId']]['sLeaveDefault'];
            $usedHours      = $totalHours - $staffLeave[$leave['sId']]['sLeaveBalance'];
            $remainingHours = $staffLeave[$leave['sId']]['sLeaveBalance'];
        }

        $sheet->setCellValue('A' . $row, $leave['leaveName']);
        $sheet->setCellValue('B' . $row, $totalHours);
        $sheet->setCellValue('C' . $row, $usedHours);
        $sheet->setCellValue('D' . $row, $remainingHours);
        $row++;
    }
}

// Output Excel
$_file = '員工假別剩餘時數統計_' . date('Ymd') . '.xlsx';

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
