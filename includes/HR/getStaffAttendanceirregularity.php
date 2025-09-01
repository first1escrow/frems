<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';
require_once dirname(dirname(__DIR__)) . '/class/traits/CheckIn.trait.php';

class StaffAttendanceIrregularity
{
    use CheckIn;

    private $conn = null;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
}

header('Content-Type: application/json');

//是否已處理遲到早退(請假)
function isLateOrEarlyOK(&$conn, $staff_id, $date, $type)
{
    $attendance = new StaffAttendanceIrregularity($conn);
    $rs         = $attendance->getCheckInOutList($date . ' 00:00:00', $date . ' 23:59:59', $staff_id, false, false);

    $checkInTime  = '';
    $checkOutTime = '';
    $sIn          = '';
    $sOut         = '';
    if (! empty($rs['periodData']['data'][0])) {
        $checkInTime  = $rs['periodData']['data'][0]['checkIn'];
        $checkOutTime = $rs['periodData']['data'][0]['checkOut'];
        $sIn          = $rs['periodData']['data'][0]['sIn'];
        $sOut         = $rs['periodData']['data'][0]['sOut'];
    }

    if (empty($checkInTime) || empty($checkOutTime)) {
        throw new Exception('No check time found');
    }

    if ($type == 'IN') {
        if (empty($sIn)) {
            return false;
        }

        if ($sIn > $checkInTime) {
            return false;
        }
    }

    if ($type == 'OUT') {
        if (empty($sOut)) {
            return false;
        }

        if ($sOut < $checkOutTime) {
            return false;
        }
    }

    return true;
}

//是否已處理未打卡(補打卡或請假)
function isCheckOK(&$conn, $staff_id, $date)
{
    $sql = 'SELECT sId FROM tStaffCheckInApply WHERE sStaffId = :staff_id AND sApplyDate = :date AND sApplyType IN ("IN", "OUT") AND sStatus = "Y"
            UNION
            SELECT sId FROM tStaffLeaveApply WHERE sApplicant = :staff_id AND DATE(sLeaveFromDateTime) <= :date AND DATE(sLeaveToDateTime) >= :date AND sStatus = "Y";';
    $rs = $conn->one($sql, [
        'staff_id' => $staff_id,
        'date'     => $date,
    ]);

    return ! empty($rs) ? true : false;
}

//是否已處理未簽到未簽退(補打卡)
function isCheckInOutOK(&$conn, $staff_id, $date, $type)
{
    if (empty($type) || ! in_array($type, ['IN', 'OUT'])) {
        throw new Exception('Invalid type');
    }

    $sql = 'SELECT sId FROM tStaffCheckInApply WHERE sStaffId = :staff_id AND sApplyDate = :date AND sApplyType = :type AND sStatus = "Y";';
    $rs  = $conn->one($sql, [
        'staff_id' => $staff_id,
        'date'     => $date,
        'type'     => $type,
    ]);

    return ! empty($rs) ? true : false;
}

$conn = new first1DB;

//取得出勤異常統計資訊
$sql = 'SELECT
            a.sStaffId,
            a.sDate,
            a.sRead,
            a.sReason,
            b.pName,
            b.pDep,
            (SELECT dDep FROM tDepartment WHERE dId = b.pDep) as department
        FROM
            tStaffCheckInAlert AS a
        JOIN
            tPeopleInfo AS b ON a.sStaffId = b.pId
        WHERE 1;';
$rs = $conn->all($sql);
if (empty($rs)) {
    exit(json_encode(['data' => []]));
}

//比對是否已經處理
$data = [];
foreach ($rs as $key => $row) {
    if (preg_match("/遲到/iu", $row['sReason']) && ! isLateOrEarlyOK($conn, $row['sStaffId'], $row['sDate'], 'IN')) {
        $data[] = $row;
    }

    if (preg_match("/早退/iu", $row['sReason']) && ! isLateOrEarlyOK($conn, $row['sStaffId'], $row['sDate'], 'OUT')) {
        $data[] = $row;
    }

    if (preg_match("/未打卡/iu", $row['sReason']) && ! isCheckOK($conn, $row['sStaffId'], $row['sDate'])) {
        $data[] = $row;
    }

    if (preg_match("/未簽到/iu", $row['sReason']) && ! isCheckInOutOK($conn, $row['sStaffId'], $row['sDate'], 'IN')) {
        $data[] = $row;
    }

    if (preg_match("/未簽退/iu", $row['sReason']) && ! isCheckInOutOK($conn, $row['sStaffId'], $row['sDate'], 'OUT')) {
        $data[] = $row;
    }
}

$output = [];
foreach ($data as $row) {
    $output[] = [
        'staffId'    => $row['sStaffId'],
        'date'       => $row['sDate'],
        'read'       => $row['sRead'],
        'reason'     => $row['sReason'],
        'name'       => $row['pName'],
        'dep'        => $row['pDep'],
        'department' => $row['department'],
    ];
}

exit(json_encode(['data' => $output]));
