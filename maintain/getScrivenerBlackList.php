<?php
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$list = array();

$list["data"] = array();

$sql = 'SELECT
			sId AS id,
			sName AS name,
			sIdentifyId AS sIdentifyId,
			sOffice AS office,
			(SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip=sZip) AS cityArea,
			sAddress AS address
		FROM
			tScrivenerBlackList
		WHERE
			sDelete = 0
		ORDER BY sId ASC;';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $rs->fields['address'] = $rs->fields['cityArea'] . $rs->fields['address'];

    array_push($list['data'], $rs->fields);

    $rs->MoveNext();
}

if (is_array($list)) {
    echo json_encode($list);

}
$conn->close();
exit;
