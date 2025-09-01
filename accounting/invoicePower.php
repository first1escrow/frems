<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


$cid = trim($_POST['cid']);//保號


if ($cid) {
	$sql_search .= " AND cCertifiedId = '".$cid."'";

	$sql = "SELECT cCertifiedId AS CertifiedId,cInvoiceClose FROM tContractCase WHERE cInvoiceClose !='N'".$sql_search;

	$rs = $conn->Execute($sql);

	$i = 0;
	while (!$rs->EOF) {
		
		
			$list[$i]=$rs->fields;
			##列顏色
			if ($i % 2 == 0) { $list[$i]['color'] = "#FFFFFF" ; }
			else 
				{ $list[$i]['color'] = "#F8ECE9" ; }

			##按鈕
			if ($list[$i]['cInvoiceClose']=='Y') {

				$list[$i]['cInvoiceText'] = '關閉'; 
			}else{

				$list[$i]['cInvoiceText'] = '開啟';
			}

		


		$i++;
		$rs->MoveNext();
	}
}

##搜尋





// echo "<pre>";
// print_r($data);
// echo "</pre>";



##

$smarty->assign('data',$list);
$smarty->assign('cid',$cid);

$smarty->display('invoicePower.inc.tpl', '', 'accounting');

?>