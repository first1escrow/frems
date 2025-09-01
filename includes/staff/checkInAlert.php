<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$conn = new first1DB;

$sql = 'SELECT sId, sStaffId, sDate, sReason FROM tStaffCheckInAlert AS a WHERE sStaffId = :staff AND sRead IS NULL;';
$rs  = $conn->all($sql, ['staff' => $_SESSION['member_id']]);

if (! empty($rs)) {
    $sql = 'UPDATE tStaffCheckInAlert SET sRead = :date WHERE sStaffId = :staff AND sRead IS NULL;';
    $conn->exeSql($sql, ['date' => date('Y-m-d H:i:s'), 'staff' => $_SESSION['member_id']]);

    $html = '
    <div style="margin: 0px auto; width: 99%; height: 300px; overflow: auto;">
        <table border="1" width="100%">
            <tr>
                <th style="padding:5px;">日期</th>
                <th style="padding:5px;">原因</th>
            </tr>
    ';

    foreach ($rs as $v) {
        $html .= '
            <tr>
                <td style="padding:5px;" nowrap>' . $v['sDate'] . '</td>
                <td style="padding:5px;" nowrap>' . $v['sReason'] . '</td>
            </tr>
        ';
    }

    $html .= '
        </table>
    </div>
    ';

    exit($html);
}
