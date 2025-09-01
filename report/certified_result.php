<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
// include_once '../opendb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
require_once dirname(__DIR__).'/first1DB.php';

$tlog = new TraceLog() ;

//預載log物件
$logs = new Intolog() ;
##

$start_date = trim($_POST['start_date']) ;
$end_date = trim($_POST['end_date']) ;
$buyer = trim($_POST['buyer']) ;
$owner = trim($_POST['owner']) ;
$scrivener = trim($_POST['scrivener']) ;
$branch = trim($_POST['branch']) ;
$category = trim($_POST['category']) ;
$certifiedid = trim($_POST['certifiedid']) ;
$case_status = trim($_POST['case_status']) ;
$show_hide = trim($_POST['show_hide']) ;

$total_page = trim($_POST['total_page']) + 1 - 1 ;
$current_page = trim($_POST['current_page']) + 1 - 1 ;
$record_limit = trim($_POST['record_limit']) + 1 - 1 ;
$next_page = trim($_POST['next_page']) ;

if (!$record_limit) { $record_limit = 10 ; }

$query = '' ; 
$functions = '' ;
$total = 0 ;

$buyer1 = $buyer ;
$owner1 = $owner ;
$scrivener1 = $scrivener ;
$branch1 = $branch ;

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

// 搜尋條件-狀態
if ($case_status) {
	//if ($query) { $query .= " AND " ; }
	$query .= ' AND cas.cCaseStatus="'.$case_status.'" ' ;
}

// 限制有媒體匯出檔才加入計算
//if ($query) { $query .= " AND " ; }
$query .= ' AND tra.tAccount IN ("27110351738","10401810001889","20680100135997") AND tra.tPayOk="1" ' ;
///////////////////////////////////////////////////////////////////

//if ($query) { $query = ' WHERE '.$query ; }

$sql = '
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
$tlog->selectWrite($_SESSION['member_id'], $sql, '保證費統計表搜尋') ;
$logs->writelog('certifiedWeb') ;

// 取得所有資料
$conn = new first1DB;
$arr1 = $conn->all($sql);

$arr = array() ;
$j = 0 ; 
$tbl = '' ;
$max = count($arr1) ;
for ($i = 0 ; $i < $max ; $i ++) {

	foreach ($arr1[$i] as $key => $value) {
		$arr[$j][$key] = $value ;
	}
	$j ++ ;
	
	if ($arr1[$i]['cCertifiedId']==$arr1[$i+1]['cCertifiedId']) {
		$i ++ ;
	}
}
unset($arr1) ;

$max = count($arr) ;
for ($i = 0 ; $i < $max ; $i ++) {
	$total += $arr[$i]['money'] ;
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

if ($max > 0) {
	$tb1 .= '
		<tr style="text-align:center;background-color:#F8ECE9;">
			<td>'.number_format($max).'&nbsp;</td>
			<td>'.number_format($total).'&nbsp;</td>
			<td id="showhide"><a href="#" onclick="detail()">查看明細</a></td>
		</tr>
		<tr>
			<td colspan="3" style="height:40px;">
				<input type="button" class="bt4" value="回上一頁" onclick=go_back()>
				<input type="button" class="bt4" value="匯出 excel 檔" onclick=xls("certified_excel.php")>
			</td>
		</tr>
		' ;
	
	for ($i = $i_begin ; $i < $i_end ; $i ++) {
		if ($i % 2 == 0) { $color_index = "#FFFFFF" ; }
		else { $color_index = "#F8ECE9" ; }
		
		if ($arr[$i]['e_time']) {
			$arr[$i]['e_time'] = substr($arr[$i]['e_time'], 0,10) ;
			$tmp = explode('-',$arr[$i]['e_time']) ;
			$arr[$i]['e_time'] = ($tmp[0]-1911).'-'.$tmp[1].'-'.$tmp[2] ;
			unset($tmp) ;
		}

		$tb2 .= '
			<tr style="background-color:'.$color_index.';">
				<td>'.($i+1).'&nbsp;</td>
				<td>'.$arr[$i]['cCertifiedId'].'&nbsp;</td>
				<td>'.$arr[$i]['e_time'].'&nbsp;</td>
				<td>'.$arr[$i]['buyer'].'&nbsp;</td>
				<td>'.$arr[$i]['owner'].'&nbsp;</td>
				<td>'.$arr[$i]['scrivener'].'&nbsp;</td>
				<td>'.@number_format($arr[$i]['money']).'&nbsp;</td>
				<td>'.$arr[$i]['status'].'&nbsp;</td>
			</tr>
		' ;
	}
	
}
else {
	$tb1 .= '	
		<tr style="text-align:center;background-color:#FFFFFF">
			<td colspan="3" style="height:20px;text-align:left;border:1px solid #ccc;"><span style="font-size:9pt;color:red;">目前尚無任何資料！</span></td>
		</tr>
		<tr>
			<td colspan="3" style="height:40px;">
				<input type="button" class="bt4" value="回上一頁" onclick=go_back()>
			</td>
		</tr>
	' ;

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

$functions = "

" ;


if ($max==0) {
	$i_begin = 0 ;
	$i_end = 0 ;
}
else {
	$i_begin += 1 ;
}

# 頁面資料
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',$max) ;
if ($next_page) {
	$smarty->assign('display','') ;
}
else {
	$smarty->assign('display','none') ;
}

# 搜尋資訊
$smarty->assign('start_date',$start_date) ;
$smarty->assign('end_date',$end_date) ;
$smarty->assign('certifiedid',$certifiedid) ;
$smarty->assign('buyer',$buyer1) ;
$smarty->assign('owner',$owner1) ;
$smarty->assign('scrivener',$scrivener1) ;
$smarty->assign('branch',$branch1) ;
$smarty->assign('category',$category) ;

# 搜尋結果
$smarty->assign('tb1',$tb1) ;
$smarty->assign('tb2',$tb2) ;

# 其他
//$smarty->assign('functions',$functions) ;
$smarty->assign('show_hide',$show_hide) ;

$smarty->display('certified_result.inc.tpl', '', 'report');
?>