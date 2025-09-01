<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/scrivener.class.php';
include_once 'class/getAddress.php' ;
include_once 'class/getBank.php' ;
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php' ;
include_once '../tracelog.php' ;
include_once '../includes/maintain/feedBackData.php' ;


$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '檢視特定地政士資料明細') ;

//預載log物件
$logs = new Intolog() ;
##

if (empty($_POST["id"])) {
    $_POST["id"] = $_GET['id'];
}

$scrivener = new Scrivener();

$data = $scrivener->GetScrivenerInfo($_POST["id"]);

$from_sales =  $_POST['from_sales'];

$list_ppl = $scrivener->GetPeopleList();
$menu_ppl = $scrivener->ConvertOption($list_ppl, 'pId', 'pName');
$list_brand = $scrivener->GetBrandList();
// $menu_brand = $scrivener->ConvertOption($list_brand, 'bId', 'bName');
$menu_brand = array(2 => '非仲介成交',1=> '台灣房屋',49=>'優美地產');

$list_guild = $scrivener->GetCategoryGuild();
$menu_guild = $scrivener->ConvertOption($list_guild, 'cId', 'cName');
$menu_invoice = $scrivener->GetCategoryInvoice();
$list_ppl = $scrivener->GetPeopleList();
$menu_ppl = $scrivener->ConvertOption($list_ppl, 'pId', 'pName');
$menu_status = $scrivener->GetCategoryScrivenerStatus();
$data['sBrand'] = explode(",", $data['sBrand']);

//修正地址縣市區域重複
$data['sAddress'] = filterCityAreaName($conn,$data['sZip1'],$data['sAddress']) ;
$data['sCpAddress'] = filterCityAreaName($conn,$data['sCpZip1'],$data['sCpAddress']) ;
##

//取得總行(1)選單
$menu_bank = $scrivener->GetBankMenuList();
##

//取得分行(1)選單
$menu_branch = getBankBranch($conn,$data['sAccountNum1'],$data['sAccountNum2']) ;
##

//取得分行(2)選單
$menu_branch21 = getBankBranch($conn,$data['sAccountNum11'],$data['sAccountNum21']) ;

$menu_branch22 = getBankBranch($conn,$data['sAccountNum12'],$data['sAccountNum22']) ;
##
$menu_categoryrecall = $scrivener->GetCategoryRecall();
$menu_categoryidentify = $scrivener->GetCategoryIdentify();
$menu_feedbank = getBankBranch($conn,$data['sAccountNum5'],$data['sAccountNum6']) ;
##

//負責業務
$sSales = '' ;
$sql = '
	SELECT
		a.sId,
		a.sStage,
		(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName
	FROM
		tScrivenerSales AS a
	WHERE
		sScrivener="'.trim(addslashes($_POST['id'])).'"
	ORDER BY
		sId
	ASC;
' ;
$rs = $conn->Execute($sql) ;
$tmp = array() ;
$_stage = array() ;
$tIndex = 0 ;
$stage = '' ;
while (!$rs->EOF) {
	if($_SESSION['pBusinessEdit'] == 1) {
		$color = 'yellow' ;
		$display = '' ;
	}
	else {
		$color = 'orange' ;
		$display = 'none' ;
	}
	
	// $tmp[$tIndex] = '<span style="padding:2px;background-color:'.$color.';">' ;
	
	//判斷是否結束合作(1使用2停用) 如果是的話就不要有刪除的
	// if (($data['sStatus'] == 1) && ($rs->fields['sStage'] != '2')) $tmp[$tIndex] .= '<span onclick="del('.$rs->fields['sId'].')" style="cursor:pointer;display:'.$display.'">X</span>' ;
	##
	
	$tmp[$tIndex] .= $rs->fields['sSalesName'] ;
	
	// if ($rs->fields['sStage'] == '2') {
	// 	$tmp[$tIndex] .= '(已審核)' ;
	// 	$_stage[] = '<span style="padding:2px;background-color:'.$color.';"><span onclick="salesConfirm(\''.$rs->fields['sId'].'\',\'n\')" style="cursor:pointer;display:'.$display.'">X</span>'.$rs->fields['sSalesName'].'</span>' ;
	// }
	// else $stage = '<input type="button" style="padding:5px;margin-right:10px;" value="確認" onclick="salesConfirm(\''.$rs->fields['sId'].'\',\'y\')">' ;
	
	// $tmp[$tIndex] .= '</span>' ;
	
	$tIndex ++ ;
	$rs->MoveNext() ;
}
$sSales = implode(',',$tmp) ;
unset($tmp) ;

if (!$stage) $stage = implode(',',$_stage) ;
unset($_stage) ;
##

//是否可調整回饋金權限
$_disabled = ' disabled="disabled"' ;

if ($_SESSION['member_pFeedBackModify']=='1') {
	$_disabled = '' ;

}

##

//解鎖服務
$locker = '<img id="lockerPNG" src="../images/unlock.png">' ;

$idiotPWD = array('12345','123456','1234567','12345678') ;
$validate = date("Y-m-d H:i:s",strtotime("-3 months")) ;

$pwd = $data['sPassword'] ;
$logTime = $data['sLoginTime'] ;

if ($logTime < $validate) $locker = '<img id="lockerPNG" style="cursor:pointer;" src="../images/locked.png" onclick="unlocker()" title="解鎖">' ;
//else if (in_array($pwd,$idiotPWD)) $locker = '<img id="lockerPNG" style="cursor:pointer;" src="../images/locked.png" onclick="unlocker()" title="解鎖">' ;
##

//埋log紀錄
$logs->writelog('formScrivener','查詢地政士('.$data['sName'].' SC'.str_pad($data['sId'],4,'0',STR_PAD_LEFT).')') ;
##

$data['sAppointDate'] = $scrivener->ConvertDateToRoc($data['sAppointDate'], base::DATE_FORMAT_NUM_DATE);
$data['sOpenDate'] = $scrivener->ConvertDateToRoc($data['sOpenDate'], base::DATE_FORMAT_NUM_DATE);
$data['sSaveDate'] = $scrivener->ConvertDateToRoc($data['sSaveDate'], base::DATE_FORMAT_NUM_DATE);
##
//設定回饋年度範圍
for ($i = 2012 ; $i <= date("Y") ; $i ++) {
	$arr = array() ;
	$tmp = $rs->fields['cEndDate'] ;
	$arr = explode('-',$tmp) ;
	$FBYear[$i] = ($i - 1911).'&nbsp;' ;
	unset($tmp) ; unset($arr) ;
	$rs->MoveNext() ;
}
// $data['sContractStatusTime'] = DateChange($data['sContractStatusTime']);
$today = DateChange(date('Y-m-d'));
function DateChange($date)
{
	$date = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$date)) ;
	$tmp = explode('-',$date) ;
	
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
	
	$date = $tmp[0].'-'.$tmp[1].'-'.$tmp[2] ;
	unset($tmp) ;
	return $date;
}
##

$sql = "SELECT * FROM tSalesSign WHERE sType = 1 AND sStore ='".$_POST["id"]."'";

$rs = $conn->Execute($sql);



if ($rs->fields['sStore']) {
	$rs->fields['sSignDate'] = DateChange($rs->fields['sSignDate']);
	$salessign = $rs->fields;
	// $salessign['sContractStatus'] = '1';
	if ($rs->fields['sSignDate'] != '000-00-00') {
		$salessign['sContractStatus'] = '1';
	}
}


if ($data['sbStatusDateStart'] == '0000-00-00') {
	$data['sStatusDateStart'] = '000-00-00';
}else{
	$data['sStatusDateStart'] = DateChange($data['sStatusDateStart']);
}

if ($data['sStatusDateEnd'] == '0000-00-00') {
	$data['sStatusDateEnd'] = '000-00-00';
}else{
	$data['sStatusDateEnd'] = DateChange($data['sStatusDateEnd']);
}
##
//回饋簡訊
$sql = '
	SELECT 
		a.sId as sn,
		a.sNID as id, 
		a.sName as sName, 
		a.sMobile as sMobile, 
		b.tTitle as tTitle 
	FROM 
		tScrivenerFeedSms AS a 
	JOIN 
		tTitle_SMS AS b ON a.sNID=b.id
	WHERE
		a.sScrivener="'.trim(addslashes($_POST['id'])).'"
		
	ORDER BY
		a.sNID,b.tTitle
	ASC
;' ;

// echo $sql."\r\n";
$rs = $conn->Execute($sql) ;
$data_feedsms = array() ;
$i = 0 ;
while (!$rs->EOF) {
	$data_feedsms[$i] = $rs->fields ;
	
	$i ++ ;
	$rs->MoveNext() ;
}
##
//回饋對象資料
$data_feedData = FeedBackData($_POST['id'],1);

##
//品牌回饋地政士
$sql = "SELECT *,(SELECT bName FROM tBrand WHERE bId = sBrand) AS BrandName FROM tScrivenerFeedSp WHERE sScrivener ='".$_POST['id']."' AND sDel =0";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	# code...
	$FeedSp[] = $rs->fields;
	$rs->MoveNext();
}

##
$smarty->assign('FeedSp',$FeedSp);
$smarty->assign('data_feedData_count',count($data_feedData));
$smarty->assign('menu_note',array(''=>'請選擇','INC'=>'INC','REC'=>'REC'));
$smarty->assign('data_feedData',$data_feedData);
$smarty->assign('data_feedsms',$data_feedsms);
$smarty->assign('salessign',$salessign);
$smarty->assign('locker',$locker) ;
$smarty->assign('today',$today);
$smarty->assign('menu_cstatus',array('1'=>'是'));
$smarty->assign('_disabled',$_disabled) ;
$smarty->assign('FBYear', $FBYear);
$smarty->assign('FBYearSelect', Date("Y"));
$smarty->assign('stage',$stage) ;
$smarty->assign('from_sales',$from_sales);//判斷是否為業務責任區審核來的
$smarty->assign('sOptions', array(1 => '加盟', 2 => '直營')) ;
$smarty->assign('is_edit', 1);
$smarty->assign('menu_guild', $menu_guild);
$smarty->assign('menu_bank', $menu_bank) ;
$smarty->assign('menu_branch', $menu_branch) ;			//分行(1)
$smarty->assign('menu_branch21', $menu_branch21) ;		//分行(2)
$smarty->assign('menu_branch22',$menu_branch22);
$smarty->assign('menu_status', $menu_status);
$smarty->assign('menu_invoice', $menu_invoice);
$smarty->assign('menu_brand', $menu_brand);
$smarty->assign('menu_ppl', $menu_ppl);
$smarty->assign('menu_sales', $menu_sales) ;
$smarty->assign('sSales', $sSales) ;
$smarty->assign('listCity', listCity($conn,$data['sZip1'])) ;		//聯絡地址-縣市
$smarty->assign('listArea', listArea($conn,$data['sZip1'])) ;		//聯絡地址-區域
$smarty->assign('listCity2', listCity($conn,$data['sCpZip1'])) ;	//公司地址-縣市
$smarty->assign('listArea2', listArea($conn,$data['sCpZip1'])) ;	//公司地址-區域
$smarty->assign('data', $data);
$smarty->assign('menu_categoryidentify', $menu_categoryidentify);
$smarty->assign('menu_categoryrecall', $menu_categoryrecall);
// $smarty->assign('listCity3', listCity($conn,$data['sZip3'])) ;	//回饋金聯絡地址-縣市
// $smarty->assign('listArea3', listArea($conn,$data['sZip3'])) ;	//回饋金聯絡地址-區域
// $smarty->assign('listCity2f', listCity($conn,$data['sZip2f'])) ;	//回饋金戶籍地址-縣市
// $smarty->assign('listArea2f', listArea($conn,$data['sZip2f'])) ;	//回饋金戶籍地址-區域
$smarty->assign('FeedCity', listCity($conn)) ;	//回饋金-縣市
// $smarty->assign('menu_feedbank', $menu_feedbank) ;		//回饋金分行

$smarty->display('formscrivener.inc.tpl', '', 'maintain');
?>
