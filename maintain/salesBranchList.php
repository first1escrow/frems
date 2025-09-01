<?php
require_once dirname(__DIR__) . '/openadodb.php';

//顯示回傳資料
$bSales = '';
$sql    = '
	SELECT
		a.bId,
		a.bStage,
		(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName
	FROM
		tBranchSales AS a
	WHERE
		bBranch="' . $brid . '"
	ORDER BY
		bId
	ASC;
';
$rs     = $conn->Execute($sql);
$tmp    = array();
$_stage = array();
$tIndex = 0;
$stage  = '';
while (!$rs->EOF) {
    $tmp[$tIndex] = '<span style="padding:2px;background-color:yellow;">';

    if ($rs->fields['bStage'] == '2') {
        $tmp[$tIndex] .= $rs->fields['bSalesName'];

        $tmp[$tIndex] .= '(已審核)';
        $_stage[] = '<span style="padding:2px;background-color:yellow;"><span onclick="salesConfirm(\'' . $rs->fields['bId'] . '\',\'n\')" style="cursor:pointer;display:">X</span>' . $rs->fields['bSalesName'] . '</span>';
    } else {
        // $tmp[$tIndex] .= '<span onclick="del('.$rs->fields['bId'].')" style="cursor:pointer;display:">X</span>' ;
        $tmp[$tIndex] .= $rs->fields['bSalesName'];

        $stage = '<input type="button" style="padding:5px;margin-right:10px;" value="確認" onclick="salesConfirm(\'' . $rs->fields['bId'] . '\',\'y\')">';
    }

    $tmp[$tIndex] .= '</span>';

    $tIndex++;
    $rs->MoveNext();
}
$bSales = implode(',', $tmp);
unset($tmp);

if (!$stage) {
    $stage = implode(',', $_stage);
}

unset($_stage);

echo json_encode(array($bSales, $stage));
##
