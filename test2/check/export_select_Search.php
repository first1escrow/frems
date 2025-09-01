<?php

include_once '../../session_check.php' ;
include('../../openadodb.php') ;

// if ($_SESSION['member_id'] == 6) {
	// print_r($_POST);
// }
$_POST = escapeStr($_POST) ;
$cId = array();
if (is_array($_POST['CertifiedId'])) {
	foreach ($_POST['CertifiedId'] as $k => $v) {
		$exp = explode('_', $v);

		$tmp[] = "OR tMemo = '".$exp[0]."'";
		array_push($cId, $exp[0]);
		unset($exp);
	}
	$checkedNumber = implode('', $tmp);

	unset($tmp);
}

if ($_SESSION["member_pDep"] == 5 && $_SESSION["member_id"] != 1) {
    if ($_POST['id']) {
     
    }else{
        $str = " AND (tOwner ='".$_SESSION['member_name']."' ".$checkedNumber." )";
    }
    
}

$str .= "AND (tMemo LIKE '".$_POST['id']."%' ".$checkedNumber.")";

$sql = "select * from tBankTrans WHERE tOk='2' ".$str." group by tVR_Code,tObjKind ORDER BY tVR_Code ASC";

// echo $sql;

$rs = $conn->Execute($sql);
while( !$rs->EOF ) {    
	$checked = '';
	if (is_array($_POST['CertifiedId'])) {
		if (in_array($rs->fields['tMemo'],$cId)) {
			$checked = 'checked="checked"';
		}
	}
	
   $kind = ($rs->fields['tCode2'] == '大額繳稅' || $rs->fields['tCode2'] == '臨櫃開票')? '【'.$rs->fields['tCode2'].'】':'';

    echo "<div style=\"padding: 5px; text-align: left;margin-left: 30%;\">
                <span class=\"\"><input type=\"checkbox\" name=\"CertifiedId[]\" value=\"".$rs->fields['tMemo'].'_'.$rs->fields['tObjKind']."\" id=\"CertifiedId".$rs->fields['tMemo']."\" ".$checked."><label for=\"CertifiedId".$rs->fields['tMemo']."\"><span></span>".$rs->fields['tObjKind'].$kind."_".$rs->fields['tMemo']."</label></span>
            </div>";

    $rs->moveNext();
}



?>