<?php
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/class/HumanResource.class.php';
require_once dirname(__DIR__) . '/class/leaveHourCount.class.php';
require_once dirname(__DIR__) . '/includes/HR/overtimeFunction.php';

use First1\V1\Staff\LeaveHourCount;

//拆分加班時間
function divideOvertime($data)
{
    $from = new DateTime($data['sOvertimeFromDateTime']);
    $to   = new DateTime($data['sOvertimeToDateTime']);

    $today = $from->format('Y-m-d');

    /********************/
    /* 以下為拆分加班時間 */
    /* 設定正確的起迄時間 */
    /********************/

    //開始時間位於中午休息時間、調整至 13:00:00
    if (($from->format('H:i:s') >= '12:00:00') && ($from->format('H:i:s') < '13:00:00')) {
        $from->setTime(13, 0, 0);

        $data['sOvertimeFromDateTime'] = $today . ' 13:00:00';
        $data['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data['sOvertimeFromDateTime'], $data['sOvertimeToDateTime']);
    }

    //開始時間位於下午休息時間、調整至 18:30:00
    if (($from->format('H:i:s') >= '18:00:00') && ($from->format('H:i:s') < '18:30:00')) {
        $from->setTime(18, 30, 0);

        $data['sOvertimeFromDateTime'] = $today . ' 18:30:00';
        $data['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data['sOvertimeFromDateTime'], $data['sOvertimeToDateTime']);
    }

    //結束時間位於中午休息時間、調整至 12:00:00
    if (($to->format('H:i:s') > '12:00:00') && ($to->format('H:i:s') <= '13:00:00')) {
        $to->setTime(12, 0, 0);

        $data['sOvertimeToDateTime']   = $today . ' 12:00:00';
        $data['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data['sOvertimeFromDateTime'], $data['sOvertimeToDateTime']);
    }

    //結束時間位於下午休息時間、調整至 18:00:00
    if (($to->format('H:i:s') > '18:00:00') && ($to->format('H:i:s') <= '18:30:00')) {
        $to->setTime(18, 0, 0);

        $data['sOvertimeFromDateTime'] = $today . ' 18:00:00';
        $data['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data['sOvertimeFromDateTime'], $data['sOvertimeToDateTime']);
    }

    /********************/
    /* 以下為拆分加班時間 */
    /* 休息時間          */
    /********************/

    //加班時間為中午休息時間、不顯示
    if (($from->format('H:i:s') >= '12:00:00') && ($to->format('H:i:s') <= '13:00:00')) {
        return [];
    }

    //加班時間為下午休息時間、不顯示
    if (($from->format('H:i:s') >= '18:00:00') && ($to->format('H:i:s') <= '18:30:00')) {
        return [];
    }

    /********************/
    /* 以下為拆分加班時間 */
    /* 上午             */
    /********************/
    if ($from->format('H:i:s') < '12:00:00') {
        //加班時間為上午、不拆分
        if ($to->format('H:i:s') <= '12:00:00') {
            return [$data];
        }

        //加班時間為上午 ~ 下午時間、拆分成兩筆
        if ($to->format('H:i:s') <= '18:00:00') {
            $data1                          = $data;
            $data1['sOvertimeToDateTime']   = $today . ' 12:00:00';
            $data1['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data1['sOvertimeFromDateTime'], $data1['sOvertimeToDateTime']);

            $data2                          = $data;
            $data2['sOvertimeFromDateTime'] = $today . ' 13:00:00';
            $data2['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data2['sOvertimeFromDateTime'], $data2['sOvertimeToDateTime']);

            return [$data1, $data2];
        }

        //加班時間為上午 ~ 晚上時間、拆分成三筆
        if ($to->format('H:i:s') > '18:30:00') {
            $data1                          = $data;
            $data1['sOvertimeToDateTime']   = $today . ' 12:00:00';
            $data1['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data1['sOvertimeFromDateTime'], $data1['sOvertimeToDateTime']);

            $data2                          = $data;
            $data2['sOvertimeFromDateTime'] = $today . ' 13:00:00';
            $data2['sOvertimeToDateTime']   = $today . ' 18:00:00';
            $data2['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data2['sOvertimeFromDateTime'], $data2['sOvertimeToDateTime']);

            $data3                          = $data;
            $data3['sOvertimeFromDateTime'] = $today . ' 18:30:00';
            $data3['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data3['sOvertimeFromDateTime'], $data3['sOvertimeToDateTime']);

            return [$data1, $data2, $data3];
        }
    }

    /********************/
    /* 以下為拆分加班時間 */
    /* 下午              */
    /********************/
    if ($from->format('H:i:s') >= '12:00:00') {
        //加班時間為下午時間、不拆分
        if ($to->format('H:i:s') <= '18:00:00') {
            return [$data];
        }

        //加班時間為下午到晚上、拆分兩筆
        if ($from->format('H:i:s') < '18:00:00') {
            $data1                          = $data;
            $data1['sOvertimeToDateTime']   = $today . ' 18:00:00';
            $data1['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data1['sOvertimeFromDateTime'], $data1['sOvertimeToDateTime']);

            $data2                          = $data;
            $data2['sOvertimeFromDateTime'] = $today . ' 18:30:00';
            $data2['sTotalHoursOfOvertime'] = LeaveHourCount::getFromToHours($data2['sOvertimeFromDateTime'], $data2['sOvertimeToDateTime']);

            return [$data1, $data2];
        }
    }

    /********************/
    /* 以下為拆分加班時間 */
    /* 晚上              */
    /********************/

    return [$data];
}

$from     = empty($_POST['from']) ? '' : $_POST['from'];
$to       = empty($_POST['to']) ? '' : $_POST['to'];
$staff_id = empty($_POST['staff']) ? 0 : $_POST['staff'];

if (empty($from) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $from)) {
    echo '日期格式錯誤(起)';
    exit;
}

if (empty($to) || ! preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $to)) {
    echo '日期格式錯誤(迄)';
    exit;
}

$dateRange = $from . ' ~ ' . $to;

$from .= ' 00:00:00';
$to .= ' 23:59:59';

$conn = new first1DB;

//取得人員姓名資訊
$staffInfo = getStaffs($conn);

//取得部門資訊
$departments = getDepartments($conn);

//取得加班申請紀錄
$rs = getOvertimeData($conn, $from, $to);
if (empty($rs)) {
    $rs = [];
}

//去除重複日期
$staffs = [];
foreach ($rs as $v) {
    $staffs[$v['sApplicant']][] = $v['date'];
    $staffs[$v['sApplicant']]   = array_unique($staffs[$v['sApplicant']]);
}

$overtimes = [];
foreach ($staffs as $staff_id => $dates) {
    $data = getCheckInOutData($conn, $staff_id, $dates);

    if (empty($data)) {
        continue;
    }

    foreach ($data as $date => $v) {
        if (! empty($v['hours'])) {
            $overtimes[$staff_id][] = [
                'date'       => $date,
                'staffName'  => $staffInfo[$staff_id],
                'fromTime'   => $v['IN'],
                'toTime'     => $v['OUT'],
                'totalHours' => $v['hours'],
            ];
        }
    }
}

//產出 Excel 檔案
$objPHPExcel = new PHPExcel();

//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("假勤明細");
$objPHPExcel->getProperties()->setDescription('第一建經加班申請明細');

//無加班資料
if (empty($overtimes)) {
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '無加班資料');
    $objPHPExcel->getActiveSheet()->setTitle('無加班資料');
    $objPHPExcel->setActiveSheetIndex(0);
}

//有加班資料
if (! empty($overtimes)) {
    $cnt = 0;
    foreach ($overtimes as $k => $v) {
        if ($cnt > 0) {
            $objPHPExcel->createSheet();
        }

        //指定目前工作頁
        $objPHPExcel->setActiveSheetIndex($cnt);

        //調整欄位寬度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);

        //調整欄位高度
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(24);

        // 設定文字置中
        $objPHPExcel->getActiveSheet()->getStyle('A:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //寫入表頭資料
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '部門');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '加班日期');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '時間（起）');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '時間（迄）');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '時間（小時）');

        if (! empty($v)) {
            $row = 0;
            foreach ($v as $row => $d) {
                $row += 2;

                $dept      = ($row == 2) ? $departments[$d['staffName']['pDep']]['dDep'] : '';
                $name      = ($row == 2) ? $d['staffName']['pName'] : '';
                $date      = $d['date'];
                $from_time = $d['fromTime'];
                $to_time   = $d['toTime'];
                $hours     = $d['totalHours'];

                $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $row, $dept, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $row, $name, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $row, $date, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $row, $from_time, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $row, $to_time, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $hours);

                $dept = $name = $date = $from_time = $to_time = $hours = null;
            }
        }

        // Rename sheet 重命名工作表標籤
        $objPHPExcel->getActiveSheet()->setTitle($d['staffName']['pName']);

        $cnt++;
    }
}

$objPHPExcel->setActiveSheetIndex(0);

//產出報表
$dateRange = str_replace('-', '', $dateRange);
$dateRange = str_replace('~', '-', $dateRange);
$dateRange = str_replace(' ', '', $dateRange);

$_file = '加班單申請報表_' . $dateRange . '.xlsx';

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