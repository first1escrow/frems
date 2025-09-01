<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once 'class/scrivener.class.php';

include_once '../openadodb.php' ;

// $contract = new Contract();


$cid=trim($_POST['cid']);//保號
$page = trim($_POST['page']); //目前頁數



if (!$page) {
	$page=1;
	
}

$row=10;
$limit_s= ($page-1)*$row;
$limit_e = $limit_s+$row; //結束筆數
$sql_search=" bc.bFrom = 2 ";

if ( $_SESSION['member_tEcontract']==2) {
	// $sql_search .=" AND s.sUndertaker1='".$_SESSION['member_id']."'";
	$sql_search .=" AND s.sUndertaker1='".$_SESSION['member_id']."'";
}
##搜尋

if ($cid) {
	// $sql_search .= " AND cas.cCertifiedId = '".$cid."'";
	$sql_search .= " AND bc.bAccount LIKE '%".$cid."'";
}

##
## 列表

// $sql="
// 	SELECT
// 		cas.cCertifiedId AS CertifiedId,
// 		cas.cSignCategory AS SignCategory,
//  		(SELECT sName FROM  tScrivener AS a WHERE a.sId=scr.cScrivener ) AS  ScrivenerName,
//  		code.bCreateDate AS CreatDate,
//  		s.sUndertaker1
//  	FROM 
//  		tContractCase AS cas	
// 	JOIN
// 		tContractScrivener AS scr ON cas.cCertifiedId =scr.cCertifiedId
// 	JOIN  
// 		tBankCode AS code ON  code.bAccount=cas.cEscrowBankAccount
// 	JOIN 
// 		tScrivener AS s ON s.sId=scr.cScrivener
// 	WHERE
// 		".$sql_search." 
// 	ORDER BY CreatDate DESC
// 		LIMIT ".$limit_s." , ".$row."
// 		";
// echo $sql;

$sql2="SELECT
 		s.sName AS  ScrivenerName,
 		SUBSTR(bc.bAccount,-9) AS CertifiedId,
 		bc.bCreateDate AS CreatDate,
 		s.sUndertaker1
 	FROM 
 	
		tBankCode AS bc

	LEFT JOIN 
		tScrivener AS s ON s.sId=bc.bSID
	WHERE ".$sql_search."" ;

$rs2 = $conn->Execute($sql2);

$total=$rs2->RecordCount();//計算總筆數


$sql="SELECT
 		s.sName AS  ScrivenerName,
 		SUBSTR(bc.bAccount,-9) AS CertifiedId,
 		bc.bCreateDate AS CreatDate,
 		s.sUndertaker1,
 		cc.cSignDate
 	FROM 
		tBankCode AS bc
	LEFT JOIN 
		tScrivener AS s ON s.sId=bc.bSID
	LEFT JOIN 
		tContractCase AS cc ON cc.cCertifiedId=SUBSTR(bc.bAccount,-9)
	WHERE ".$sql_search."ORDER BY CreatDate DESC LIMIT ".$limit_s." , ".$row."" ;



$rs=$conn->Execute($sql);


$i=0;

while (!$rs->EOF) {
	
	
		$data[$i]=$rs->fields;

		if ($i % 2 == 0) { $data[$i]['color'] = "#FFFFFF" ; }
		else{ $data[$i]['color'] = "#F8ECE9" ; }

		if ($data[$i]['SignCategory']==1) {

			$data[$i]['disabled'] ='disabled=disabled';
		}else{

			$data[$i]['disabled'] ='';
		}

		$data[$i]['check'] = (empty($data[$i]['cSignDate']))?0:1;


	$i++;
	$rs->MoveNext();
}


##
$total_page= ceil($total/$row);//總頁數
##

##頁數下拉

for ($i=1; $i <=$total_page ; $i++) { 
	$page_menu[$i]=$i;
}
##

$smarty->assign('data',$data);
$smarty->assign('limit_s',$limit_s+1);//起始紀錄
$smarty->assign('limit_e',$limit_e);//結束紀錄
$smarty->assign('total',$total);//總筆數
$smarty->assign('page',$page);//目前頁數
$smarty->assign('total_page',$total_page);//目前頁數
$smarty->assign('page_menu',$page_menu);//目前頁數

$smarty->display('formCategoryList.inc.tpl', '', 'others');
?>