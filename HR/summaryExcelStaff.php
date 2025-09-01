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

$from = $_POST['from'];
// $from = '2025-05-01'; //測試用
if (empty($from) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $from)) {
    throw new Exception('from data abnormal');
}

$to = $_POST['to'];
// $to = '2025-05-01'; //測試用
if (empty($to) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $to)) {
    throw new Exception('to data abnormal');
}

$dateRange = $from . ' ~ ' . $to;

$staff = empty($_POST['staff']) ? null : $_POST['staff'];
// $staff = 122;//測試用

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

$leaveApplyData    = $hr->getLeaves($from, $to);
$overtimeApplyData = $hr->getOvertimes(substr($from, 0, 4) . '-' . substr($from, 5, 2) . '-01 00:00:00', substr($to, 0, 4) . '-' . substr($to, 5, 2) . '-31 23:59:59');
$workingHolidays   = $hr->getHolidays($from, $to, true);
$defaultLeaves     = $hr->getLeaveType();

$data = [];
foreach ($staff as $v) {
    $data[] = $hr->getCheckInOutList($from, $to, $v['pId'], false, false);
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
$objPHPExcel->getProperties()->setDescription('第一建經' . $year . '年度' . $month . '月份假勤明細');

//20250123 增加總表
$objPHPExcel->createSheet();

// Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('總表');

foreach ($data as $k => $v) {
    if ($k > 0) {
        $objPHPExcel->createSheet();
    }

    //指定目前工作頁
    $objPHPExcel->setActiveSheetIndex(($k + 1));

    //調整欄位寬度
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(30);

    //調整欄位高度
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);

    // 設定文字置中
    $objPHPExcel->getActiveSheet()->getStyle('A1:AD1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    //寫入表頭資料
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '部門');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '日期');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '上班時間');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '下班時間');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '備註');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '遲到時數(分鐘)');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '早退時數(分鐘)');

    $char  = ord('I');
    $char1 = '';
    foreach ($defaultLeaves as $_leave) {
        if ($char > 90) {
            $char  = ord('A');
            $char1 = 'A';
        }

        $objPHPExcel->getActiveSheet()->setCellValueExplicit($char1 . chr($char++) . '1', $_leave['leaveName'] . '(小時)', PHPExcel_Cell_DataType::TYPE_STRING);
    }
    $objPHPExcel->getActiveSheet()->setCellValueExplicit($char1 . chr($char) . '1', '加班(小時)', PHPExcel_Cell_DataType::TYPE_STRING);

    // 寫入資料
    $records = $v['periodData']['data'];
    usort($records, function ($a, $b) {
        return $a['sDate'] > $b['sDate'];
    });

    $pName = $v['member']['pName'];

    $total = [];
    foreach ($records as $index => $d) {
        $row   = $index + 2;
        $dept  = ($row == 2) ? $department[$v['member']['pDep']] : '';
        $pName = ($index == 0) ? $pName : '';

        //記錄個人員計算總時數
        $summary[$d['sStaffId']] = [
            'pId'   => $d['sStaffId'],
            'pName' => $v['member']['pName'],
            'pDep'  => $department[$v['member']['pDep']],
            'total' => [],
        ];

        $d['sIn'] = strip_tags($d['sIn']);
        $d['sIn'] = preg_replace("/申請補打卡/iu", '', $d['sIn']);
        $d['sIn'] = preg_replace("/請假/iu", '', $d['sIn']);
        $d['sIn'] = preg_replace("/\(假\)/iu", '', $d['sIn']);
        $d['sIn'] = preg_replace("/\*+/iu", '*', $d['sIn']);

        $d['sOut'] = strip_tags($d['sOut']);
        $d['sOut'] = preg_replace("/申請補打卡/iu", '', $d['sOut']);
        $d['sOut'] = preg_replace("/請假/iu", '', $d['sOut']);
        $d['sOut'] = preg_replace("/\(假\)/iu", '', $d['sOut']);
        $d['sOut'] = preg_replace("/\*+/iu", '*', $d['sOut']);

        $d['remark'] = strip_tags($d['remark']);

        $checkIn  = empty($d['checkIn']) ? '09:00:00' : $d['checkIn'];
        $checkOut = empty($d['checkOut']) ? '17:30:00' : $d['checkOut'];

        //20250424 遲到早退時間計算方式調整(以分計算)
        $_sIn      = substr($d['sIn'], 0, 6) . '00';
        $_sOut     = substr($d['sOut'], 0, 6) . '00';
        $_checkIn  = substr($checkIn, 0, 6) . '00';
        $_checkOut = substr($checkOut, 0, 6) . '00';
        $late      = preg_match("/遲到/u", $d['remark']) ? floor(abs(strtotime($_sIn) - strtotime($_checkIn)) / 60) : '';
        $early     = preg_match("/早退/u", $d['remark']) ? floor(abs(strtotime($_sOut) - strtotime($_checkOut)) / 60) : '';

        $_sIn = $_sOut = $_checkIn = $_checkOut = null;
        unset($_sIn, $_sOut, $_checkIn, $_checkOut);

        $total['G'] = empty($total['G']) ? 0 : $total['G'];
        $total['G'] += empty($late) ? 0 : $late;

        $total['H'] = empty($total['H']) ? 0 : $total['H'];
        $total['H'] += empty($early) ? 0 : $early;

        //記錄個人員計算總時數
        $summary[$d['sStaffId']]['total']['G'] = $total['G'];
        $summary[$d['sStaffId']]['total']['H'] = $total['H'];

        $d['sDate'] = $d['sDate'] . ' (' . $d['weekName'] . ')';

        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $dept, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $pName, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $d['sDate'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $d['sIn'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $d['sOut'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, $d['remark'], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $row, $late, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $row, $early, PHPExcel_Cell_DataType::TYPE_NUMERIC);

        //2025-01-07 遲到欄位紅字顯示
        if (! empty($late)) {
            $objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        }

        //2025-01-07 早退欄位紅字顯示
        if (! empty($early)) {
            $objPHPExcel->getActiveSheet()->getStyle('H' . $row)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        }

        //假日或休假
        $_workingHolidays = array_column($workingHolidays, 'hFromDate');

        if ((in_array($d['week'], [6, 7]) || ! empty($d['holiday'])) && ! in_array(substr($d['sDate'], 0, 10), $_workingHolidays)) {
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AD' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AD' . $row)->getFill()->getStartColor()->setARGB('00DBDCF2');
        }

        $char  = ord('I');
        $char1 = '';

        //計算個別假別總數
        $leaveApplyDataCount = [];
        if (! empty($leaveApplyData[$d['sStaffId']])) {
            foreach ($leaveApplyData[$d['sStaffId']] as $leave) {
                $leaveApplyDataCount[$leave['sLeaveId']] = empty($leaveApplyDataCount[$leave['sLeaveId']]) ? 0 : $leaveApplyDataCount[$leave['sLeaveId']];

                $sDate = substr($d['sDate'], 0, 10);
                if ((($sDate >= date('Y-m-d', $leave['sLeaveFromTmestamp'])) && ($sDate <= date('Y-m-d', $leave['sLeaveToTimestamp']))) && (! in_array($d['week'], [6, 7]) || in_array($sDate, $_workingHolidays)) && empty($d['holiday'])) {
                    $from = new DateTime(date('Y-m-d H:i:s', $leave['sLeaveFromTmestamp']));
                    $to   = new DateTime(date('Y-m-d H:i:s', $leave['sLeaveToTimestamp']));

                    $begin = $from->format('Y-m-d H:i:s');
                    if ($from->format('Y-m-d') != $sDate) { // 開始時間不是當天
                        $begin = $sDate . ' 09:00:00';
                    }

                    $end = $to->format('Y-m-d H:i:s');
                    if ($to->format('Y-m-d') != $sDate) { // 結束時間不是當天
                        $end = $sDate . ' 18:00:00';
                    }

                    $leaveApplyDataCount[$leave['sLeaveId']] += LeaveHourCount::getOvertimeHours($begin, $end);
                }
            }
        }
        $_workingHolidays = null;unset($_workingHolidays);

        foreach ($defaultLeaves as $defaultLeave) {
            if ($char > 90) {
                $char  = ord('A');
                $char1 = 'A';
            }

            $text = empty($leaveApplyDataCount[$defaultLeave['sId']]) ? '' : $leaveApplyDataCount[$defaultLeave['sId']];

            $total[$char1 . chr($char)] = empty($total[$char1 . chr($char)]) ? 0 : $total[$char1 . chr($char)];
            $total[$char1 . chr($char)] += empty($text) ? 0 : $text;

            //記錄個人員計算總時數
            $summary[$d['sStaffId']]['total'][$char1 . chr($char)] = $total[$char1 . chr($char)];

            $objPHPExcel->getActiveSheet()->setCellValueExplicit($char1 . chr($char++) . $row, $text, PHPExcel_Cell_DataType::TYPE_NUMERIC);

            //2025-01-07 特定假別欄位紅字顯示
            if (in_array($defaultLeave['sId'], [3, 4, 5, 16, 17, 18, 19, 20]) && ! empty($text)) {
                $objPHPExcel->getActiveSheet()->getStyle($char1 . chr($char - 1) . $row)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
            }

        }

        //加班時數
        $_date         = substr($d['sDate'], 0, 10);
        $overtimeHours = 0;
        if (! empty($overtimeApplyData[$d['sStaffId']])) {
            foreach ($overtimeApplyData[$d['sStaffId']] as $sOvertimeFromDateTime) {
                if (($_date == date('Y-m-d', strtotime($sOvertimeFromDateTime['sOvertimeFromDateTime']))) && (! empty($d['sIn']) || ! empty($d['sOut']))) {
                    $_workingHolidays = array_column($workingHolidays, 'hFromDate');

                    //非週六日或補班日且非假日，加班開始時間以18:30為主
                    if ((! in_array(date('N', strtotime($_date)), [6, 7]) || in_array($_date, $_workingHolidays)) && empty($d['holiday'])) {
                        if (date('H:i:s', strtotime($sOvertimeFromDateTime['sOvertimeFromDateTime'])) < '18:30:00') {
                            $sOvertimeFromDateTime['sOvertimeFromDateTime'] = $_date . ' 18:30:00';
                        }
                    } else {
                        if (! empty($d['sIn'])) {
                            $sOvertimeFromDateTime['sOvertimeFromDateTime'] = (date('H:i:s', strtotime($sOvertimeFromDateTime['sOvertimeFromDateTime'])) < '09:00:00') ? $_date . ' 09:00:00' : $_date . ' ' . $d['sIn'];
                        }
                    }

                    //20250204 加班結束時間以最晚打卡時間為主
                    if (! empty($d['sOut'])) {
                        $sOvertimeFromDateTime['sOvertimeToDateTime'] = $_date . ' ' . $d['sOut'];
                    }

                    if ((strtotime($sOvertimeFromDateTime['sOvertimeFromDateTime']) > strtotime($sOvertimeFromDateTime['sOvertimeToDateTime'])) || empty($d['sIn']) || empty($d['sOut'])) {
                        // throw new Exception('begin time must be less than end time');
                        $overtimeHours = 0;
                    } else {
                        $overtimeHours = LeaveHourCount::getOvertimeHours($sOvertimeFromDateTime['sOvertimeFromDateTime'], $sOvertimeFromDateTime['sOvertimeToDateTime']);
                        $overtimeHours = round($overtimeHours, 1);
                    }

                    break;
                }
            }
        }

        $objPHPExcel->getActiveSheet()->setCellValueExplicit('AD' . $row, $overtimeHours, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $total['AD'] = empty($total['AD']) ? 0 : $total['AD'];
        $total['AD'] += empty($overtimeHours) ? 0 : $overtimeHours;

        //記錄個人員計算總時數
        $summary[$d['sStaffId']]['total']['AD'] = $total['AD'];
    }

    // 計算總時數
    $row = count($records) + 2;

    $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, '總計');
    foreach ($total as $key => $value) {
        $va = empty($value) ? '' : $value;
        $objPHPExcel->getActiveSheet()->setCellValueExplicit($key . $row, $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);

        //2025-01-07 特定假別欄位紅字顯示
        if (in_array($key, ['G', 'H', 'K', 'L', 'M', 'X', 'Y', 'Z', 'AA', 'AB']) && ! empty($value)) {
            $objPHPExcel->getActiveSheet()->getStyle($key . $row)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        }
    }

    // Rename sheet 重命名工作表標籤
    $objPHPExcel->getActiveSheet()->setTitle($v['member']['pName']);
}

//20250123 總表內容
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(30);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);

// 設定文字置中
$objPHPExcel->getActiveSheet()->getStyle('A1:AD1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('A1', '部門');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '期間');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '上班時間');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '下班時間');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '備註');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '遲到時數(分鐘)');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '早退時數(分鐘)');

$char  = ord('I');
$char1 = '';
foreach ($defaultLeaves as $_leave) {
    if ($char > 90) {
        $char  = ord('A');
        $char1 = 'A';
    }

    $objPHPExcel->getActiveSheet()->setCellValueExplicit($char1 . chr($char++) . '1', $_leave['leaveName'] . '(小時)', PHPExcel_Cell_DataType::TYPE_STRING);
}
$objPHPExcel->getActiveSheet()->setCellValueExplicit($char1 . chr($char) . '1', '加班(小時)', PHPExcel_Cell_DataType::TYPE_STRING);

if (! empty($summary)) {
    $row = 2;
    foreach ($summary as $d) {
        if ($row % 2 == 0) {
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AD' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':AD' . $row)->getFill()->getStartColor()->setARGB('00FFF7FF');
        }

        $dept  = $d['pDep'];
        $pName = $d['pName'];
        $total = $d['total'];

        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $dept, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $pName, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $dateRange, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, '', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $row, '總計', PHPExcel_Cell_DataType::TYPE_STRING);

        foreach ($total as $key => $value) {
            $objPHPExcel->getActiveSheet()->setCellValueExplicit($key . $row, $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        }

        //2025-01-07 特定假別欄位紅字顯示
        if (in_array($key, ['G', 'H', 'K', 'L', 'M', 'X', 'Y', 'Z', 'AA', 'AB']) && ! empty($value)) {
            $objPHPExcel->getActiveSheet()->getStyle($key . $row)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        }

        $row++;
    }
}

//產出報表
$dateRange = str_replace('-', '', $dateRange);
$dateRange = str_replace('~', '-', $dateRange);
$dateRange = str_replace(' ', '', $dateRange);

$_file = '假勤報表_' . $dateRange . '.xlsx';

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
