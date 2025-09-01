<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
include_once 'getBranchType.php';

$tlog = new TraceLog() ;

//預載log物件
$logs = new Intolog() ;
##

$xls = trim(addslashes($_POST['xls'])) ;

$bank = trim(addslashes($_POST['bank'])) ;
$sApplyDate = trim(addslashes($_POST['sApplyDate'])) ;
$eApplyDate = trim(addslashes($_POST['eApplyDate'])) ;
$sEndDate = trim(addslashes($_POST['sEndDate'])) ;
$eEndDate = trim(addslashes($_POST['eEndDate'])) ;
$sSignDate = trim(addslashes($_POST['sSignDate'])) ;
$eSignDate = trim(addslashes($_POST['eSignDate'])) ;
$branch = trim(addslashes($_POST['branch'])) ;
$scrivener = trim(addslashes($_POST['scrivener'])) ;
$zip = trim(addslashes($_POST['zip'])) ;
$citys = trim(addslashes($_POST['citys'])) ;
$brand = trim(addslashes($_POST['brand'])) ;
$sales = trim(addslashes($_POST['sales'])) ;
$status = trim(addslashes($_POST['status'])) ;
$realestate = trim(addslashes($_POST['realestate'])) ;
$cCertifiedId = trim(addslashes($_POST['cCertifiedId'])) ;
$buyer = trim(addslashes($_POST['buyer'])) ;
$owner = trim(addslashes($_POST['owner'])) ;

$show_hide = trim(addslashes($_POST['show_hide'])) ;

$total_page = trim(addslashes($_POST['total_page'])) + 1 - 1 ;
$current_page = trim(addslashes($_POST['current_page'])) + 1 - 1 ;
$record_limit = trim(addslashes($_POST['record_limit'])) + 1 - 1 ;

if (!$record_limit) { $record_limit = 10 ; }



$query = '' ; 
$functions = '' ;

$sad = $sApplyDate ;
$ead = $eApplyDate ;
$sed = $sEndDate ;
$eed = $eEndDate ;
$ssd = $sSignDate ;
$esd = $eSignDate ;
$br = $branch ;
$sc = $scrivener ;
$byr = $buyer ;
$owr = $owner ;

//取得合約銀行
$Savings = array() ;
$savingAccount = '' ;
$i = 0 ;
$sql = 'SELECT cBankAccount FROM tContractBank GROUP BY cBankAccount ORDER BY cId ASC;' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$Savings[$i++] = $rs->fields['cBankAccount'] ;
	$rs->MoveNext();
}

$savingAccount = implode('","',$Savings) ;
unset($Savings) ;
##

//取得所有出款保證費紀錄("27110351738","10401810001889","20680100135997")
$sql = '
	SELECT 
		DISTINCT tMemo, 
		tMoney 
	FROM 
		tBankTrans 
	WHERE 
		tAccount IN ("'.$savingAccount.'") 
		AND tPayOk="1" 
	ORDER BY 
		tMemo 
	ASC' ;

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$export_data[$rs->fields['tMemo']] = $rs->fields['tMoney'] ;

	$rs->MoveNext();
}

##

$query = ' cas.cCertifiedId<>"" ' ;

// 搜尋條件-銀行別
if ($bank) {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cBank="'.$bank.'" ' ;
}

// 搜尋條件-進案日期
if ($sApplyDate) {
	$tmp = explode('-',$sApplyDate) ;
	$sApplyDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate>="'.$sApplyDate.' 00:00:00" ' ;
}
if ($eApplyDate) {
	$tmp = explode('-',$eApplyDate) ;
	$eApplyDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cApplyDate<="'.$eApplyDate.' 23:59:59" ' ;
}

// 搜尋條件-結案日期
if ($sEndDate) {
	$tmp = explode('-',$sEndDate) ;
	$sEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate>="'.$sEndDate.' 00:00:00" ' ;
	//$query .= ' tra.tExport_time>="'.$sEndDate.' 00:00:00" ' ;
}
if ($eEndDate) {
	$tmp = explode('-',$eEndDate) ;
	$eEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cEndDate<="'.$eEndDate.' 23:59:59" ' ;
	//$query .= ' tra.tExport_time<="'.$eEndDate.' 23:59:59" ' ;
}
##

// 搜尋條件-簽約日期
if ($sSignDate) {
	$tmp = explode('-',$sSignDate) ;
	$sSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate>="'.$sSignDate.' 00:00:00" ' ;
}
if ($eSignDate) {
	$tmp = explode('-',$eSignDate) ;
	$eSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cSignDate<="'.$eSignDate.' 23:59:59" ' ;
}
##

// 搜尋條件-仲介店
if ($branch) {
	if ($query) { $query .= " AND " ; }
	//$query .= ' rea.cBranchNum="'.$branch.'" ' ;
	$query .= ' (rea.cBranchNum="'.$branch.'" OR rea.cBranchNum1="'.$branch.'" OR rea.cBranchNum2="'.$branch.'") ' ;
}

// 搜尋條件-地政士
if ($scrivener) {
	if ($query) { $query .= " AND " ; }
	$query .= ' csc.cScrivener="'.$scrivener.'" ' ;
}

// 搜尋條件-買方姓名
if ($buyer) {
	if ($query) { $query .= " AND " ; }
	$query .= ' buy.cId="'.$buyer.'" ' ;
}

// 搜尋條件-賣方姓名
if ($owner) {
	if ($query) { $query .= " AND " ; }
	$query .= ' own.cId="'.$owner.'" ' ;
}

// 搜尋條件-保證號碼
if ($cCertifiedId) {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCertifiedId="'.$cCertifiedId.'" ' ;
}

// 搜尋條件-仲介品牌
if ($brand) {
	if ($query) { $query .= " AND " ; }
	$query .= ' rea.cBrand="'.$brand.'" ' ;
}


// 搜尋條件-地區
if ($zip) {
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip="'.$zip.'" ' ;
}
else if ($citys) {
	$zipArr = array() ;
	$zipStr = '' ;
	$sql = 'SELECT zZip FROM tZipArea WHERE zCity="'.$citys.'" ORDER BY zCity,zZip ASC;' ;
	// echo $sql;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$zipArr[] = $rs->fields['zZip'] ;
		$rs->MoveNext();
	}

	
	$zipStr = implode('","',$zipArr) ;
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip IN ("'.$zipStr.'") ' ;
	unset($zipArr) ;
	unset($zipStr) ;
}
##

// 搜尋條件-案件狀態
if ($status) {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus="'.$status.'" ' ;
}
else {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus<>"8" ' ;
}

if ($status=='3') {
	$t_day = '結案日期' ;
}
else {
	$t_day = '簽約日期' ;
}



if ($query) { $query = ' WHERE '.$query ; }
//LEFT JOIN
//	tBankTrans AS tra ON tra.tMemo=cas.cCertifiedId AND tra.tAccount="27110351738"
//	tra.tMoney tMoney,

$query ='
SELECT 
	cas.cCertifiedId as cCertifiedId, 
	cas.cApplyDate as cApplyDate, 
	cas.cSignDate as cSignDate, 
	cas.cFinishDate as cFinishDate,
	cas.cEndDate as cEndDate, 
	buy.cName as buyer, 
	own.cName as owner, 
	inc.cTotalMoney as cTotalMoney, 
	inc.cCertifiedMoney as cCertifiedMoney, 
	csc.cScrivener as cScrivener, 
	(SELECT b.sName FROM tScrivener AS b WHERE b.sId=csc.cScrivener) as scrivener, 
	pro.cAddr as cAddr, 
	pro.cZip as cZip, 
	zip.zCity as zCity, 
	zip.zArea as zArea, 
	(SELECT c.sName FROM tStatusCase AS c WHERE c.sId=cas.cCaseStatus) as status,
	rea.cBrand as brand,
	rea.cBrand1 as brand1,
	rea.cBrand2 as brand2,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand1 ),LPAD(rea.cBranchNum1,5,"0")) as bCode1,
	CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand2 ),LPAD(rea.cBranchNum2,5,"0")) as bCode2,
	rea.cBranchNum as branch,
	rea.cBranchNum1 as branch1,
	rea.cBranchNum2 as branch2
FROM 
	tContractCase AS cas 
LEFT JOIN 
	tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
LEFT JOIN 
	tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId 
LEFT JOIN 
	tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
LEFT JOIN
	tZipArea AS zip ON zip.zZip=pro.cZip

'.$query.' 
GROUP BY
	cas.cCertifiedId
ORDER BY 
	cas.cApplyDate,cas.cId,cas.cSignDate ASC;
' ;
// echo $query;

// $logs->writelog('applycaseWeb') ;
$tlog->selectWrite($_SESSION['member_id'], $query, '業務案件統計表搜尋') ;

$rs = $conn->Execute($query);

$tbl = '' ;

# 取得所有資料
$totalMoney = 0 ;
$certifiedMoney = 0 ;
$transMoney = 0 ;


while (!$rs->EOF) {
	
	$arr[] = $rs->fields;

	$rs->MoveNext();
}



$k=0;
// echo count($arr)."<br>";
//撈取業務

for ($i=0; $i < count($arr); $i++) { 

	if ($arr[$i]['branch'] == 505) {
		$case_sales[] = ScrivenerSales($conn,$arr[$i]['cScrivener']);

		$bStore[] ='非仲介成交';
		$bCode[] = $arr[$i]['bCode'];


	}else if ($arr[$i]['branch']!=0)
	{
		$case_sales[] = BranchSales($conn,$arr[$i]['branch']);
		$bStore[] = getRealtyName($conn,$arr[$i]['branch']); 
		$bCode[] = $arr[$i]['bCode'];
		// echo $arr[$i]['bCode']."<br>";
		

	}
	
	if ($arr[$i]['branch1'] == 505) {
		$case_sales[] = ScrivenerSales($conn,$arr[$i]['cScrivener']);
		$bStore[] ='非仲介成交';
	}else if ($arr[$i]['branch1']!=0) 
	{
		$case_sales[] = BranchSales($conn,$arr[$i]['branch1']);
		$bStore[] = getRealtyName($conn,$arr[$i]['branch1']); 
		$bCode[] = $arr[$i]['bCode1'];
	}

	if ($arr[$i]['branch2'] == 505) {
		$case_sales[] = ScrivenerSales($conn,$arr[$i]['cScrivener']);
		$bStore[] ='非仲介成交';
	}else if ($arr[$i]['branch2']!=0)
	{
		$case_sales[] = BranchSales($conn,$arr[$i]['branch2']);
		$bStore[] = getRealtyName($conn,$arr[$i]['branch2']); 
		$bCode[] = $arr[$i]['bCode2'];
	}

// echo count($case_sales);
// die;
	$total = count($case_sales);



	for ($j=0; $j < $total; $j++) { 

		if ($sales==$case_sales[$j]['pId']||$sales=='0') { //判斷是否是查詢的業務;$sales=='0'預設全部

			if (checkCaseTW($conn,$arr[$i])) { //台屋優美不顯示，配件只要有出現其他品牌就要顯示

				
					$list[$k]=$arr[$i];
					$list[$k]['sales'] = $case_sales[$j]['name'];




					if ($total > 1 && $xls != 'ok') {
						$list[$k]['bStore'] = '<span style="font-size:9pt;color:blue;font-weight:bold;">*</span>';
					}


					if ($ck == 1) {
						$list[$k]['bStore'] .= implode(' ', $bStore);
						$list[$k]['bCode'] =implode(' ', $bCode);
					}else
					{
						$list[$k]['bStore'] .= $bStore[$j];
						$list[$k]['bCode'] = $bCode[$j];
					}
					//如果下一個也是回饋同個業務，則顯示1比
					if ($case_sales[$j]['cCertifiedId']==$case_sales[$j+1]['cCertifiedId'] && $case_sales[$j]['name']==$case_sales[$j+1]['name'] ) {

						
						$ck = 1;
						$list[$k]['show'] = 1;
						
					}else
					{
						if (mb_substr($bCode[$j], 0,2) != 'TH' && mb_substr($bCode[$j], 0,2) != 'UM') { //拆開顯示的，如果是其他品牌才顯示
							$list[$k]['show'] = 1;
						}
						$k++;
					}
			}
				
			
			


			
			
		}
		
		

		
	}
	
	unset($case_sales);
	unset($bStore);unset($bCode);
	unset($ck);
	

}



$arr = $list;


for ($i = 0 ; $i < count($arr) ; $i ++) {


	
	// 簽約日期
	$arr[$i]['cSignDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$i]['cSignDate'])) ;
	$tmp = explode('-',$arr[$i]['cSignDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
	
	$arr[$i]['cSignDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	##
	
	// 取得匯款金額
	$mm = 0 ;
	$mm += $export_data[$arr[$i]['cCertifiedId']] ;
	$arr[$i]['tMoney'] = $mm ;
	unset($mm) ;
	##
	
	// 進案日期
	$arr[$i]['cApplyDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$i]['cApplyDate'])) ;
	$tmp = explode('-',$arr[$i]['cApplyDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
	
	$arr[$i]['cApplyDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	##
	
	// 結案日期
	$arr[$i]['cEndDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$i]['cEndDate'])) ;
	$tmp = explode('-',$arr[$i]['cEndDate']) ;
	
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
	
	$arr[$i]['cEndDate'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	##

	if ($arr[$i]['show'] == 1) {
		$arr2[]=$arr[$i];
	}
	
}
unset($arr);
$arr = $arr2;
##

// echo "<pre>";
// print_r($arr);
// echo "</pre>";
// die;

//決定是否剔除過濾仲介類型
$max = count($arr) ;
if ($realestate) {	


	$list = array() ;
	$j = 0 ;
	for ($i = 0 ; $i < $max ; $i ++) {
		
		$type = branch_type($conn,$arr[$i]);
		
		if ($realestate == '11' && $type == 'O') {
			//$cat = '加盟其他品牌' ;
			$list[$j++] = $arr[$i] ;
		}
		else if ($realestate == '12' && $type == 'T') {
			//$cat = '加盟台灣房屋' ;
				$list[$j++] = $arr[$i] ;
	
		}
		else if ($realestate == '13' && $type == 'U') {
			//$cat = '加盟優美地產' ;
			$list[$j++] = $arr[$i] ;
		}
		else if ($realestate == '1' && ($type == 'O' || $type == 'T' || $type == 'U')) {
			//$cat = '所有加盟(其他品牌、台灣房屋、優美地產)' ;
			
			$list[$j++] = $arr[$i] ;
		}
		else if ($realestate == '2' && $type == '2') {
			//$cat = '直營' ;
			//$list[$j++] = $arr[$i] ;
			$list[$j++] = $arr[$i] ;
		}
		else if ($realestate == '3' && $type == '3') {
			//$cat = '非仲介成交' ;
			$list[$j++] = $arr[$i] ;
		}
		else if ($realestate == '4' && $type == 'N' ) {
			$list[$j++] = $arr[$i] ;
		}
	}
	unset($arr) ;
	$arr = array() ;
	
	$arr = array_merge($list) ;

	unset($list);
}
##
// echo "<pre>";
// print_r($arr);
// echo "</pre>";
// die;
//計算總額
$max = count($arr) ;

//產出excel檔
if ($xls == 'ok') {
	$tlog->exportWrite($_SESSION['member_id'], json_encode($_POST), '業務案件統計表excel匯出') ;
	
	include_once 'salescase_excel.php' ;
}
##

# 計算總頁數
if (($max % $record_limit) == 0) {
	$total_page = $max / $record_limit ;
}
else {
	$total_page = floor($max / $record_limit) + 1 ;
}
##

# 設定目前頁數顯示範圍
if ($current_page) {
	if ($current_page >= ($max / $record_limit)) {
		if ($max % $record_limit == 0) {
			$current_page = floor($max / $record_limit) ;
		}
		else {
			$current_page = floor($max / $record_limit) + 1 ;
		}
	}
	$i_end = $current_page * $record_limit ;
	$i_begin = $i_end - $record_limit ;
	if ($i_end > $max) {
		$i_end = $max ;
	}
	if($i_end > $max) { $i_end = $max ; }
}
else {
	$i_end = $record_limit ;
	if($i_end > $max) { $i_end = $max ; }
	$i_begin = 0 ;
	$current_page = 1 ;
}

$j = 1 ; 
for ($i = $i_begin ; $i < $i_end ; $i ++) {

	if ($i % 2 == 0) { $color_index = "#FFFFFF" ; }
	//else { $color_index = "#E4BeB1" ; }
	else { $color_index = "#F8ECE9" ; }
	$zc = $arr[$i]['zCity'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/","",$arr[$i]['cAddr']) ;
	$zc = $arr[$i]['zArea'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/",'',$arr[$i]['cAddr']) ;
	$arr[$i]['cAddr'] = $arr[$i]['zCity'].$arr[$i]['zArea'].$arr[$i]['cAddr'] ;

##
	$arr[$i]['cCertifiedMoney']=$arr[$i]['cCertifiedMoney'];
	$tmp = round($arr[$i]['cTotalMoney']*0.0006); //萬分之六
	$tmp2 = round($arr[$i]['cTotalMoney']*0.0006)*0.1;
  	


	if(($tmp-$tmp2)>$arr[$i]['cCertifiedMoney']) //合約保證費 如果未達6/10000的合約保證費  在合約保證費的金額位置 加註星星 
	{
		$arr[$i]['cCertifiedMoney']= '*'.$arr[$i]['cCertifiedMoney'] ;
	}else
	{
		$arr[$i]['cCertifiedMoney']= $arr[$i]['cCertifiedMoney'] ;
	}
##	
	$tbl .= '
	<tr style="text-align:center;background-color:'.$color_index.'">
		<td>'.($j++).'</td>
		<td>'.$arr[$i]['cCertifiedId'].'&nbsp;</td>
		<td>'.$arr[$i]['bStore'].'&nbsp;</td>
		<td>'.$arr[$i]['owner'].'&nbsp;</td>
		<td>'.$arr[$i]['buyer'].'&nbsp;</td>
		<td style="text-align:right;">'.number_format($arr[$i]['cTotalMoney']).'&nbsp;</td>
		<td style="text-align:right;">'.$arr[$i]['cCertifiedMoney'].'&nbsp;</td>
		' ;

		if ($status=='3') { 
			$tbl .= '<td>'.$arr[$i]['cEndDate'].'&nbsp;</td>' ; 
		}
		else {
			$tbl .= '<td>'.$arr[$i]['cSignDate'].'&nbsp;</td>' ; 
		}
	
	$tbl .= '
		<td>'.$arr[$i]['cApplyDate'].'&nbsp;</td>
		<td>'.$arr[$i]['cEndDate'].'&nbsp;</td>
		<td>'.$arr[$i]['scrivener'].'&nbsp;</td>
		<td>'.$arr[$i]['status'].'&nbsp;</td>
		<td>'.$arr[$i]['sales'].'&nbsp;</td>
	</tr>
	' ;
	/*
	if (!preg_match("/^0000-00-00 00:00:00$/",$arr[$i]['cFinishDate'])) {
		$cFinishDate = preg_replace("/ \d+:\d+:\d+$/","",$arr[$i]['cFinishDate']) ;
		$tmp = explode('-',$cFinishDate) ;
		$cFinishDate = ($tmp[0] - 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
		
		$tbl .= '
			<td>'.$cFinishDate.'&nbsp;</td>
			<td>'.$arr[$i]['scrivener'].'&nbsp;</td>
			<td>'.$arr[$i]['status'].'&nbsp;</td>
		</tr>
		' ;
	}
	*/
}
	

if ($record_limit==10) { $records_limit .= '<option value="10" selected="selected">10</option>'."\n" ; }
else { $records_limit .= '<option value="10">10</option>'."\n" ; }
if ($record_limit==50) { $records_limit .= '<option value="50" selected="selected">50</option>'."\n" ; }
else { $records_limit .= '<option value="50">50</option>'."\n" ; }
if ($record_limit==100) { $records_limit .= '<option value="100" selected="selected">100</option>'."\n" ; }
else { $records_limit .= '<option value="100">100</option>'."\n" ; }
if ($record_limit==150) { $records_limit .= '<option value="150" selected="selected">150</option>'."\n" ; }
else { $records_limit .= '<option value="150">150</option>'."\n" ; }
if ($record_limit==200) { $records_limit .= '<option value="200" selected="selected">200</option>'."\n" ; }
else { $records_limit .= '<option value="200">200</option>'."\n" ; }

// include('closedb.php') ;

if($max > 0) {
	$functions = '<span id="a_tag"><a href=# onclick="list()">檢視明細</a></span>' ;
}
else {
	$functions = '－' ;
}


if ($max==0) {
	$i_begin = 0 ;
	$i_end = 0 ;
}
else {
	$i_begin += 1 ;
}


//取得仲介店名
Function getRealtyName($conn,$no=0) {
	unset($tmp) ;
	if ($no > 0) {
		$sql = 'SELECT bStore FROM tBranch WHERE bId="'.$no.'";' ;
		$rs = $conn->Execute($sql);
		return $rs->fields['bStore'] ;
	}
	else {
		return false ;
	}
}
##

//取得仲介店編號
Function getRealtyNo($conn,$no=0) {
	unset($tmp) ;
	if ($no > 0) {
		$sql = 'SELECT bId, (SELECT bCode FROM tBrand AS b WHERE b.bId=a.bBrand) as bCode FROM tBranch AS a WHERE a.bId="'.$no.'";' ;

		$rs = $conn->Execute($sql);

		return strtoupper($rs->fields['bCode']).str_pad($rs->fields['bId'],5,'0',STR_PAD_LEFT) ;
	}
	else {
		return false ;
	}
}
##
function ScrivenerSales($conn,$sId)
{
		$sql = "
			SELECT
				(SELECT pName FROM tPeopleInfo WHERE pId=ss.sSales) AS name,
				(SELECT pId FROM tPeopleInfo WHERE pId=ss.sSales) AS pId
			FROM 
				tScrivenerSales as ss
			WHERE 
				ss.sScrivener =".$sId."
			";
			// echo $sql;
	$rs = $conn->Execute($sql);

	$tmp['name'] =$rs->fields['name'].'(地政士)';
	$tmp['pId'] = $rs->fields['pId'];
	return $tmp;
}

function BranchSales($conn,$bId)
{
	$sql = "
			SELECT
				(SELECT pName FROM tPeopleInfo WHERE pId=bs.bSales) AS name,
				(SELECT pId FROM tPeopleInfo WHERE pId=bs.bSales) AS pId
			FROM 
				tBranchSales as bs
			WHERE 
				bs.bBranch=".$bId."
			";
	$rs = $conn->Execute($sql);

	$tmp['name'] =$rs->fields['name'];
	$tmp['pId'] = $rs->fields['pId'];
	return $tmp;
}
//判斷是否為台屋系列
function checkCaseTW($conn,$arr)
{
	$type = branch_type($conn,$arr); //原本規則其他品牌>台屋>優美，所以配件有其他品牌，一定是其他

	if ($type != 'T' && $type != 'U' && $type != '2') { //如果不是台屋&&優美
			
		return true;
	}else{
		return false;
	}
		
}
##

# 頁面資料
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',number_format($max)) ;

# 搜尋資訊
$smarty->assign('bank',$bank) ;
$smarty->assign('sApplyDate',$sad) ;
$smarty->assign('eApplyDate',$ead) ;
$smarty->assign('sEndDate',$sed) ;
$smarty->assign('eEndDate',$eed) ;
$smarty->assign('sSignDate',$ssd) ;
$smarty->assign('eSignDate',$esd) ;
$smarty->assign('branch',$br) ;
$smarty->assign('scrivener',$sc) ;
$smarty->assign('zip',$zip) ;
$smarty->assign('citys',$citys) ;
$smarty->assign('brand',$brand) ;
$smarty->assign('undertaker',$undertaker) ;
$smarty->assign('status',$status) ;
$smarty->assign('cCertifiedId',$cCertifiedId) ;
$smarty->assign('buyer',$byr) ;
$smarty->assign('owner',$owr) ;
$smarty->assign('sales',$sales) ;
# 搜尋結果
$smarty->assign('tbl',$tbl) ;
$smarty->assign('totalMoney',number_format($totalMoney)) ;
$smarty->assign('certifiedMoney',number_format($certifiedMoney)) ;
$smarty->assign('transMoney',number_format($transMoney)) ;
$smarty->assign('show_hide',$show_hide) ;
$smarty->assign('realestate',$realestate) ;

# 其他
$smarty->assign('functions',$functions) ;
$smarty->assign('t_day',$t_day) ;

$smarty->display('salescase_result.inc.tpl', '', 'report');
?>