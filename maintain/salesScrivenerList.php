<?php
require_once dirname(__DIR__) . '/openadodb.php';

//顯示回傳資料
$sSales = '';
$sql    = '
	SELECT
		a.sId,
		a.sStage,
		(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName
	FROM
		tScrivenerSales AS a
	WHERE
		sScrivener="' . $sid . '"
	ORDER BY
		sId
	ASC;
';
$rs     = $conn->Execute($sql);
$tmp    = array();
$_stage = array();
$tIndex = 0;
$stage  = '';
while (!$rs->EOF) {
    $tmp[$tIndex] = '<span style="padding:2px;background-color:yellow;">';

    if ($rs->fields['sStage'] == '2') {
        $tmp[$tIndex] .= $rs->fields['sSalesName'];

        $tmp[$tIndex] .= '(已審核)';
        $_stage[] = '<span style="padding:2px;background-color:yellow;"><span onclick="salesConfirm(\'' . $rs->fields['sId'] . '\',\'n\')" style="cursor:pointer;display:">X</span>' . $rs->fields['sSalesName'] . '</span>';
    } else {
        // $tmp[$tIndex] .= '<span onclick="del('.$rs->fields['sId'].')" style="cursor:pointer;display:">X</span>' ;
        $tmp[$tIndex] .= $rs->fields['sSalesName'];

        $stage = '<input type="button" style="padding:5px;margin-right:10px;" value="確認" onclick="salesConfirm(\'' . $rs->fields['sId'] . '\',\'y\')">';
    }

    $tmp[$tIndex] .= '</span>';

    $tIndex++;
    $rs->MoveNext();
}
$sSales = implode(',', $tmp);
unset($tmp);

if (!$stage) {
    $stage = implode(',', $_stage);
}

unset($_stage);

echo json_encode(array($sSales, $stage));
##
