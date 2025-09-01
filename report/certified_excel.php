<?php
include_once '../configs/config.class.php' ;
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../class/intolog.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;

//預載log物件
$logs = new Intolog() ;
##

$logs->writelog('certifiedExcel') ;

$start_date = trim($_POST['start_date']) ;
$end_date = trim($_POST['end_date']) ;
$buyer = trim($_POST['buyer']) ;
$owner = trim($_POST['owner']) ;
$scrivener = trim($_POST['scrivener']) ;
$branch = trim($_POST['branch']) ;
$category = trim($_POST['category']) ;
$certifiedid = trim($_POST['certifiedid']) ;

$totalMoney = 0 ;
$query = '' ; 
$functions = '' ;

// 搜尋條件-保證號碼
if ($certifiedid) {
	//if ($query) { $query .= " AND " ; }
	$query .= ' AND tra.tMemo="'.$certifiedid.'" ' ;
}

// 搜尋條件-買方
if ($buyer) {
	$tmp = explode(')',$buyer) ;
	$buyer = trim($tmp[0]) ;
	unset($tmp) ;
	
	$tmp = explode('(',$buyer) ;
	$buyer = trim($tmp[1]) ;
	unset($tmp) ;
	
	//if ($query) { $query .= " AND " ; }
	$query .= ' AND buy.cIdentifyId="'.$buyer.'" ' ;
}

// 搜尋條件-賣方
if ($owner) {
	$tmp = explode(')',$owner) ;
	$owner = trim($tmp[0]) ;
	unset($tmp) ;
	
	$tmp = explode('(',$owner) ;
	$owner = trim($tmp[1]) ;
	unset($tmp) ;
	
	//if ($query) { $query .= " AND " ; }
	$query .= ' AND own.cIdentifyId="'.$owner.'" ' ;
}

// 搜尋條件-地政士
if ($scrivener) {
	$tmp = explode(')',$scrivener) ;
	$scrivener_id = trim($tmp[0]) ;
	$scrivener = $tmp[1] ;
	unset($tmp) ;
	
	$tmp = explode('(',$scrivener_id) ;
	$scrivener_id = trim($tmp[1]) ;
	unset($tmp) ;
	
	$scr_id = substr($scrivener_id,2) ;
	
	//if ($query) { $query .= " AND " ; }
	
	$scr_id += 1 - 1 ;
	$query .= ' AND csc.cScrivener="'.$scr_id.'" ' ;
}

// 搜尋條件-仲介類別
if ($category) {
	//if ($query) { $query .= " AND " ; }
	$query .= ' AND bra.bCategory="'.$category.'" ' ;
}

// 搜尋條件-仲介店
if ($branch) {
	$tmp = explode(')',$branch) ;
	$branch_id = trim($tmp[0]) ;
	$branch = trim($tmp[1]) ;
	unset($tmp) ;
	
	$tmp = explode('(',$branch_id) ;
	$branch_id = trim($tmp[1]) ;
	unset($tmp) ;
	
	//if ($query) { $query .= " AND " ; }
	
	$bcode = substr($branch_id,0,2) ;
	$branch_id = substr($branch_id,2,5) ;
	
	$branch_id += 1 - 1 ;
	$query .= ' AND rea.cBranchNum="'.$branch_id.'" AND rea.cBrand=(SELECT bId FROM tBrand AS bah WHERE bCode="'.$bcode.'") ' ;
}

// 搜尋條件-出款日期 起
if ($start_date) {
	$tmp = explode('-',$start_date) ;

	//if ($query) { $query .= " AND " ; }
	$query .= ' AND tra.tExport_time>="'.($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2].' 00:00:00" ' ;
	//$query .= ' cas.cApplyDate>="'.($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2].' 00:00:00" ' ;
	unset($tmp) ;
}

// 搜尋條件-出款日期 迄
if ($end_date) {
	$tmp = explode('-',$end_date) ;

	//if ($query) { $query .= " AND " ; }
	$query .= ' AND tra.tExport_time<="'.($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2].' 23:59:59" ' ;
	//$query .= ' cas.cApplyDate<="'.($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2].' 23:59:59" ' ;
	unset($tmp) ;
}

// 限制有媒體匯出檔才加入計算
//if ($query) { $query .= " AND " ; }
$query .= ' AND tra.tAccount IN ("27110351738","10401810001889","20680100135997") AND tra.tPayOk="1" ' ;
///////////////////////////////////////////////////////////////////

//if ($query) { $query = ' WHERE '.$query ; }

$sql ='
SELECT 
	DISTINCT tra.tMemo cCertifiedId, 
	buy.cName buyer, 
	own.cName owner, 
	scr.sName scrivener, 
	tra.tMoney money, 
	tra.tExport_time e_time,
	(SELECT sName FROM tStatusCase WHERE cas.cCaseStatus=sId) status 
	
FROM 
	tBankTrans AS tra 
LEFT JOIN
	tContractCase AS cas ON cas.cCertifiedId=tra.tMemo 
LEFT JOIN
	tContractBuyer AS buy ON buy.cCertifiedId=tra.tMemo 
LEFT JOIN 
	tContractOwner AS own ON own.cCertifiedId=tra.tMemo 
LEFT JOIN 
	tContractScrivener AS csc ON csc.cCertifiedId=tra.tMemo 
LEFT JOIN 
	tScrivener AS scr ON scr.sId=csc.cScrivener 
LEFT JOIN 
	tContractRealestate AS rea ON rea.cCertifyId=tra.tMemo 
LEFT JOIN 
	tBranch AS bra ON bra.bId=rea.cBranchNum 
WHERE
	tra.tMemo <> "000000000"
'.$query.'
ORDER BY tra.tExport_time,tra.tMemo ASC
' ;

//echo "Q=".$sql ;
$tlog->exportWrite($_SESSION['member_id'], $sql, '匯出保證費統計表Excel') ;

$rs = $conn->Execute($sql) ;
$totalMoney = 0 ;

# 取得所有資料
$i = 0 ;
while (!$rs->EOF) {
	$arr[$i] = $rs->fields ;
		
	//調整日期顯示
	if ($arr[$i]['e_time']) {
		$arr[$i]['e_time'] = preg_replace("/ [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$arr[$i]['e_time']) ;
		$tmp = explode('-',$arr[$i]['e_time']) ;
		$tmp[0] -= 1911 ;
		$arr[$i]['e_time'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
	}
	
	$totalMoney += $arr[$i]['money'] + 1 - 1 ;
	
	$i ++ ;
	$rs->MoveNext() ;
}
$imax = count($arr) ;

//header拋檔
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=certifiedMoney.csv");
header("Pragma: no-cache");
header("Expires: 0");
##

//CSV版
$fh = fopen("php://output","w") ;

fwrite($fh,iconv("UTF-8","big5","總案件筆數,保證費總金額 \n")) ;
fwrite($fh,$imax.','.$totalMoney."\n\n") ;
fwrite($fh,iconv("UTF-8","big5","序號,保證號碼,匯款日期,買方,賣方,地政士姓名,保證費,案件狀態 \n")) ;

for ($i = 0 ; $i < $imax ; $i ++) {
	fwrite($fh,($i+1).','.iconv('UTF-8','big5',$arr[$i]['cCertifiedId']).'_ ,'.iconv('UTF-8','big5',$arr[$i]['e_time']).','.iconv('UTF-8','big5',$arr[$i]['buyer']).' ,'.iconv('UTF-8','big5',$arr[$i]['owner']).' ,'.iconv('UTF-8','big5',$arr[$i]['scrivener']).' ,'.iconv('UTF-8','big5',$arr[$i]['money']).' ,'.iconv('UTF-8','big5',$arr[$i]['status'])." \n") ;
}
fclose($fh) ;

##

?>