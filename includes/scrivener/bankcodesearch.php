<?php
include_once dirname(dirname(__DIR__)).'/class/getAddress.php' ;
include_once dirname(dirname(__DIR__)).'/openadodb.php' ;

/**
 * 取得地政士資訊
 */
Function GetScrivenerInfo($id) {
    global $conn;

    $sql = "SELECT * FROM `tScrivener` Where sId = '" . $id . "' Order by sId;   ";
    $rs = $conn->Execute($sql);
    return $rs->fields;
}

/**
 * 取得合約銀行保證號碼資訊
 */
Function GetBankCode($id, $bc='8') {
    global $conn;

    $bankcode = array();
    $conBank = array() ;
    
    $conBank = getBC($bc) ;
    $bc = $conBank['cBankVR'].'%' ;
    
    $sql = "SELECT * FROM `tBankCode` where bUsed = '0' AND bDel = 'n' AND bSID = '" . $id . "' AND bAccount LIKE '".$bc."' ORDER BY bVersion DESC";
    $rs = $conn->Execute($sql);

    $result = array();
    while (!$rs->EOF) {
		$result[] = $rs->fields;
		$rs->MoveNext();
	}
    
    foreach ($result as $k => $v) {
        $bankcode[$v['bAccount']]['bAccount'] = $v['bAccount'];
        $bankcode[$v['bAccount']]['bVersion'] = $v['bVersion'];

        //99986
        if (preg_match("/^99986/", $v['bAccount'])) {
            $bankcode[$v['bAccount']]['branch'] = 6; // 城中
        }
    }

    return $bankcode;
}

/**
 * 取得合約銀行資訊
 */
Function getBC($bc) {
    global $conn;
    
    $sql = 'SELECT cBankCode,cBankVR FROM tContractBank WHERE cBankCode="'.$bc.'";' ;
    $rs = $conn->Execute($sql);

    return $rs->fields;
}

$result1 = GetScrivenerInfo($_POST["id"]);
$result2 = GetBankCode($_POST["id"], $_POST["bc"]);

$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS sales FROM tScrivenerSales WHERE sScrivener = '".$_POST["id"]."'";
$rs  = $conn->Execute($sql);

$result1['sales']    = $rs->fields['sales'];
$result1['sAddress'] = filterCityAreaName($conn,$result1['sZip1'],$result1['sAddress']) ;
$result1['sCity']    = getCityName($conn,$result1['sZip1']) ;
$result1['sArea']    = getAreaName($conn,$result1['sZip1']) ;

exit(json_encode(array($result1, $result2), JSON_UNESCAPED_UNICODE));

?>
