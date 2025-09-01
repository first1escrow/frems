<?php
$list = [];

$tables = '<tr style="background-color:#F8ECE9;"><th>&nbsp;</th>';
for ($i = 0; $i < 12; $i++) {
    $yr = $years;
    $mn = $i + 1;

    $tables .= '<th>' . $mn . '&nbsp;月</th>';

    foreach ($staff as $k => $v) {
        $sql = '
                SELECT
                    COUNT(cId) as total
                FROM
                    tCalendar
                WHERE
                    YEAR(cStartDateTime) = "' . $yr . '"
                    AND MONTH(cStartDateTime) = "' . $mn . '"
                    AND cCreator = "' . $v['pId'] . '"
                    AND cErease = "1"
                ORDER BY
                    cStartDateTime
                ASC;
            ';
        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $list[$i][] = [
                'year'  => $yr,
                'month' => $mn,
                'pId'   => $v['pId'],
                'pName' => $v['pName'],
                'total' => $rs->fields['total'],
            ];

            $rs->MoveNext();
        }
    }
}

foreach ($list[0] as $k => $v) {
    $cindex = ($k % 2 == 0) ? '#FFF0F5' : '';
    $tables .= '<tr style="background-color:' . $cindex . '">' . "\n";
    $tables .= '<td nowrap>' . $v['pName'] . '</td>';

    foreach ($list as $ka => $va) {
        $lnk = ($list[$ka][$k]['total'] > 0) ? '<a href="Javascript:goto(\'' . $list[$ka][$k]['year'] . '\', \'' . $list[$ka][$k]['month'] . '\', \'' . $list[$ka][$k]['pId'] . '\')">' . number_format($list[$ka][$k]['total']) . '</a>' : '0';
        $tables .= '<td style="text-align: center;">' . $lnk . '</td>';
    }
    $tables .= '</tr>' . "\n";
}