<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php';

$_REQUEST = escapeStr($_REQUEST) ;
$cId = $_REQUEST['cId'];

$sql = "SELECT
			cId
		FROM
			tContractOthers
		WHERE
			cCertifiedId = '".$cId."'
          AND (cIdentity = 1 OR cIdentity = 2)
          AND (cIdentifyId='' OR cRegistZip = '' OR cRegistAddr = '' OR cBaseZip = '' OR cBaseAddr = '')
			";

$rs = $conn->Execute($sql);
if($rs->RecordCount() > 0) {
    exit(json_encode(['status' => 400, 'message' => 'error']));
}

exit(json_encode(['status' => 200, 'message' => 'success']));