<?php
require_once dirname(dirname(__DIR__)) . '/class/leaveHourCount.class.php';

use First1\V1\Staff\LeaveHourCount;

//取得員工日期內的打卡資料
function getCheckInOutData(&$conn, $staff_id, $dates)
{
    $sql = 'SELECT sId, sStaffId, sDateTime, sInOut FROM tStaffCheckIn WHERE sStaffId = ' . $staff_id . ' AND DATE(sDateTime) IN ("' . implode('","', $dates) . '");';
    $rs  = $conn->all($sql);
    if (! $rs) {
        return [];
    }

    $data = [];
    foreach ($rs as $v) {
        $date = date('Y-m-d', strtotime($v['sDateTime']));
        $time = date('H:i:s', strtotime($v['sDateTime']));
        $type = $v['sInOut'];

        $data[$date][$type] = empty($data[$date][$type]) ? $time : $data[$date][$type];

        if ($type == 'IN') {
            $data[$date][$type] = ($data[$date][$type] > $time) ? $time : $data[$date][$type];
        }

        if ($type == 'OUT') {
            $data[$date][$type] = ($data[$date][$type] < $time) ? $time : $data[$date][$type];
        }
    }

    foreach ($data as $date => $v) {
        list($workday, $holiday) = getHoliday($conn, $date, $date);
        $weekend                 = date('N', strtotime($date)) >= 6 ? true : false;

        //假日或周末(非補班日)
        if (($weekend && ! in_array($date, $workday)) || in_array($date, $holiday)) {
            $data[$date]['hours'] = round(LeaveHourCount::getOvertimeHours($v['IN'], $v['OUT']), 1);
            continue;
        }

        $v['IN'] = '18:30:00';
        if ($v['IN'] < $v['OUT']) {
            $data[$date]['IN']    = $v['IN'];
            $data[$date]['hours'] = round(LeaveHourCount::getOvertimeHours($v['IN'], $v['OUT']), 1);
            continue;
        }
    }

    return $data;
}

//取得假日(補班日)資料
function getHoliday(&$conn, $from, $to)
{
    $sql = 'SELECT hFromDate, hName, hMakeUpWorkday FROM tHoliday WHERE hFromDate >= :from AND hFromDate <= :to AND hFromTime <= "09:00:00";';
    $rs  = $conn->all($sql, ['from' => $from, 'to' => $to]);
    if (empty($rs)) {
        return [[], []];
    }

    $holiday = [];
    $workday = [];
    foreach ($rs as $v) {
        //補班日
        if ($v['hMakeUpWorkday'] == 'Y') {
            $workday[] = $v['hFromDate'];
        }

        //假日
        if ($v['hMakeUpWorkday'] == 'N') {
            $holiday[] = $v['hFromDate'];
        }
    }

    return [$workday, $holiday];
}

//取得人員姓名資訊
function getStaffs(&$conn)
{
    $sql = 'SELECT pId, pName, pDep FROM tPeopleInfo WHERE pJob = 1;';
    $rs  = $conn->all($sql);

    if (empty($rs)) {
        return [];
    }

    $staffInfo = [];
    foreach ($rs as $v) {
        $staffInfo[$v['pId']] = $v;
    }

    return $staffInfo;
}

//取得部門資訊
function getDepartments(&$conn)
{
    $sql = 'SELECT dId, dDep, dTitle, dColor FROM tDepartment WHERE 1;';
    $rs  = $conn->all($sql);

    if (empty($rs)) {
        return [];
    }

    $department = [];
    foreach ($rs as $v) {
        $department[$v['dId']] = $v;
    }

    return $department;
}

//取得加班申請紀錄
function getOvertimeData(&$conn, $from, $to)
{
    $sql = 'SELECT
            sId,
            sApplicant,
            DATE(sOvertimeFromDateTime) as date
        FROM
            tStaffOvertimeApply AS a
        WHERE
            sOvertimeFromDateTime >= :from
            AND sOvertimeToDateTime <= :to
            AND sStatus = "Y";';

    return $conn->all($sql, ['from' => $from, 'to' => $to]);
}
