<?php

//取得所有合約銀行資訊
Function getContractBank()
{
    global $conn;

    $sql = 'SELECT cId, cBankCode, cBankVR, CONCAT(cBankName, cBranchName) as cBankName FROM `tContractBank` WHERE cShow = 1;';
    $rs = $conn->Execute($sql);

    $list = array();
    while (!$rs->EOF) {
        $list[] = $rs->fields;
		$rs->MoveNext() ;
    }

    return $list;
}
##

?>