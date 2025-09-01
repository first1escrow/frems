<?php
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$list         = [];
$list["data"] = [];

$str = ($_SESSION['member_id'] == 6) ? '1=1' : ' pShow="1"';
$sql = 'SELECT
			pId AS id,
			pName AS name,
			(SELECT pTitle FROM tPowerList WHERE pId= pDep) AS dep,
			CASE pGender WHEN "M" THEN "男" WHEN "F" THEN "女" END gender,
		    pAccount AS account,
		    CASE pJob WHEN 1 THEN "使用中" WHEN 2 THEN "已停用" END job,
		    pExt AS ext,
		    pFaxNum AS faxNum,
		    pHiFaxAccount AS hiFaxAccount,
		    pMobile AS mobile,
            pAuthority AS authority
		FROM tPeopleInfo WHERE ' . $str . ' ORDER BY pId ASC;';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    if (!empty($rs->fields['authority'])) {
        $rs->fields['name'] .= ' *';
    }

    array_push($list['data'], $rs->fields);

    $rs->MoveNext();
}

if (is_array($list)) {
    echo json_encode($list);
}

exit;
