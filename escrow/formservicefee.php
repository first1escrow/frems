<?php
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$cat = $_POST['cat'];
$brand = $_POST['brand'];

//公司資訊
$company = json_decode(file_get_contents(dirname(dirname(__FILE__)).'/includes/company.json'),true) ;

$sql = "SELECT
			p.pName,
			p.pExt,
			p.pFaxNum,
			p.pGender
		FROM
			tContractScrivener AS cs
		LEFT JOIN
			tScrivener AS s ON s.sId = cs.cScrivener
		LEFT JOIN
			tPeopleInfo AS p ON p.pId=s.sUndertaker1
		WHERE 
			cs.cCertifiedId = '".$_POST['cid']."'
		";

$rs = $conn->Execute($sql);

$undertaker = $rs->fields;

if ($undertaker['pGender'] == 'M') {
  $undertaker['undertaker'] = mb_substr($undertaker['pName'],0,1,"UTF-8").'先生' ;
}else {
  $undertaker['undertaker'] = mb_substr($undertaker['pName'],0,1,"UTF-8").'小姐' ;
}


if ($cat==2) {//2直營1加盟
	
	include_once 'formservicefee_excel.php';//直營
}else{
	if ($brand == 69) {
		include_once 'formservicefee_excel3.php'; //幸福家
	}else{
		include_once 'formservicefee_excel2.php'; //加盟
	}
	
}

die;
?>