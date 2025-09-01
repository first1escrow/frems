<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/getAddress.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
include_once '../sms/sms_function_manually.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/lib/encode.php' ;

// echo dirname(dirname(dirname(__FILE__))).'/lib/rc4/rc4.php';


$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '地政士生日禮申請') ;

$sms = new SMS_Gateway();

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$sId = empty($_GET['sId']) ? $_POST['sId'] : $_GET['sId'];
$cat = empty($_GET['cat']) ? $_POST['cat'] : $_GET['cat'];


// $sql = "SELECT * FROM tScrivenerLevel WHERE sId = '".$sId."'";
// $rs = $conn->Execute($sql);
// $data = $rs->fields;

$year = empty($_POST['year']) ? date('Y') : ($_POST['year']+1911);

// if ($data['sYear']) {
// 	$year = $data['sYear'];
// }
// unset($data);
// echo "<pre>";
// print_r($_POST);
// header("Content-Type:text/html; charset=utf-8"); 




##資料修改##
function getData($sId,$type=''){

	global $conn;
	$sql = "SELECT	
			sl.sId,
			sl.sScrivener,
			s.sName,
			CONCAT('SC', LPAD(s.sId,4,'0')) as sCode2,
			s.sBirthday,
			sl.sLevel
		FROM
			tScrivener AS s
		LEFT JOIN
			tScrivenerLevel AS sl ON sl.sScrivener=s.sId
		WHERE
		sl.sId = '".$sId."'";

	$rs = $conn->Execute($sql);

	$txt = $rs->fields['sCode2'].$rs->fields['sName'];

	if ($type =='code') {
		return $rs->fields['sScrivener'];
	}
	return $txt;
}

function getSalesMobile($pId){
	global $conn;

	$sql = "SELECT pMobile FROM  tPeopleInfo WHERE pId ='".$pId."'";
	// echo $sql;
	$rs = $conn->Execute($sql);

	return $rs->fields['pMobile'];
}
##


// echo $cat."_".$_POST['ok']."_".$id;
if ($cat == 'add' && $_POST['ok'] =='ok' && $sId != 0 ) { //&& $_SESSION['member_pDep'] == 7
	
	$scrivener = getData($sId,'code');
	$str = '';
	//店家回饋資料
	$sql = "SELECT *,(SELECT zCity FROM tZipArea WHERE zZip = fZipR) AS city FROM tFeedBackData WHERE fType = 1 AND fStatus =0 AND fStoreId = '".$scrivener."'";
	$rs = $conn->Execute($sql);
	$feedbackData = $rs->fields;

	if ($_POST['gift'] != 0) {
		$str .= "sGift = '".$_POST['gift']."',";
	}

	//負責業務
	$serviceSales = array();
	$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$scrivener."'";
	// echo $sql;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		array_push($serviceSales, $rs->fields['sSales']);

		$rs->MoveNext();
	}

	if (!empty($serviceSales)) {
		$str .= "sSales = '".@implode(',', $serviceSales)."',";
	}

	$sql = "UPDATE
				tScrivenerLevel
			SET
				sMoney = '".$_POST['money']."',
				".$str."
				sStatus = 1,
				sNote ='".$_POST['note']."',
				sApplicant = '".$_SESSION['member_id']."',
				sTime = '".date('Y-m-d H:i:s')."',
				sName = '".$feedbackData['fTitle']."',
				sIdentify = '".$feedbackData['fIdentity']."',
				sIdentifyIdNumber = '".$feedbackData['fIdentityNumber']."',
				sZip = '".$feedbackData['fZipR']."',
				sAddress = '".$feedbackData['fAddrR']."'
			WHERE
				sId ='".$sId."'";

	$conn->Execute($sql);
	// $mobile = '0930945670';//3
	// $mobile = '0937185661';//3
	

if ($_SESSION['member_id'] != 6) {
	// $cc = $sms->manual_send($mobile,$msg,'y','系統發送');

	// $lineId = 'U4b14569b842b0d5d4613b77b94af02b6';
	//提醒政耀審核
	$lineId = 'Ue3a988aae4cc2d611cd4b4ed56420d85';

	sendMsg($lineId,$sId);

	// $lineId = 'U4b14569b842b0d5d4613b77b94af02b6';

	// sendMsg($lineId,$sId);

	unset($lineId);
}
	
	unset($feedbackData);
	##

	
	$cat = 'edit';
}elseif ($cat == 'edit' && $_POST['ok'] =='ok' && ($_SESSION['member_ScrivenerLevel'] == 1 )) {//|| $_SESSION['member_ScrivenerLevel'] == 4
	if ($_POST['gift'] != 0) {
		$str = "sGift = '".$_POST['gift']."',";
	}
	$sql = "UPDATE
				tScrivenerLevel
			SET
				sMoney = '".$_POST['money']."',
				".$str."
				sApplicant = '".$_SESSION['member_id']."',
				sNote ='".$_POST['note']."',
				sTime = '".date('Y-m-d H:i:s')."'
			WHERE
				sId ='".$sId."'";
	// echo $sql;
	$conn->Execute($sql);
	
}elseif ($cat == 'edit' && $_POST['ok'] =='ok' && ($_SESSION['member_ScrivenerLevel'] == 2 )) {//|| $_SESSION['member_ScrivenerLevel'] == 4
	
	$sql = "SELECT sApplicant FROM tScrivenerLevel WHERE sId ='".$sId."'";
	$rs = $conn->Execute($sql);
	$sApplicant = $rs->fields['sApplicant'];
	// echo $rs->fields['sApplicant'].'.'.$_SESSION['member_id'];
	if ($rs->fields['sApplicant'] == $_SESSION['member_id']) {


		$str = "sMoney = '".$_POST['money']."',
				";
		if ($_POST['gift'] != 0) {
			$str .= "sGift = '".$_POST['gift']."',";
		}
	}

	$sql = "UPDATE
				tScrivenerLevel
			SET
				".$str."
				sStatus = '".$_POST['status']."',
				sNote2 ='".$_POST['note2']."',
				sInspetor = '".$_SESSION['member_id']."',
				sTime2 = '".date('Y-m-d H:i:s')."'
			WHERE
				sId ='".$sId."'";
	// echo $sql;
	$conn->Execute($sql);

	if ($_POST['status'] == 2) {
		$mobile = getSalesMobile($sApplicant);//3
		// $mobile = '0937185661';//3
		$msg = getData($sId)."地政士生日禮審核通過";
		// echo $msg."-".$mobile;
		// die;
		if ($_SESSION['member_id'] != 6) {
			$cc = $sms->manual_send($mobile,$msg,'y','系統發送');
		}
		
	}
	
	
}elseif ($cat == 'edit' && $_POST['ok'] =='ok' && ($_SESSION['member_ScrivenerLevel'] == 3 || $_SESSION['member_ScrivenerLevel'] == 4 || $_SESSION['member_ScrivenerLevel'] == 5)) {
	if ($_POST['gift'] != 0) {
		$str = "sGift = '".$_POST['gift']."',";
	}
	$sql = "UPDATE
				tScrivenerLevel
			SET
				sMoney = '".$_POST['money']."',
				".$str."
				sStatus = '".$_POST['status']."',
				sNote3 ='".$_POST['note3']."',
				sInspetor2 = '".$_SESSION['member_id']."',
				sTime3 = '".date('Y-m-d H:i:s')."',
				sReceipt = '".$_POST['receipt']."',
				sName = '".$_POST['Name']."',
				sIdentify = '".$_POST['Identify']."',
				sIdentifyIdNumber = '".$_POST['IdentifyId']."',
				sTicket = '".$_POST['Ticket']."',
				sZip = '".$_POST['fZipC']."',
				sAddress = '".$_POST['fAddrC']."'
			WHERE
				sId ='".$sId."'";
	
	$conn->Execute($sql);
	
}
########
##選單##
// $dd = date('m-d', strtotime('-30 day', strtotime(date('Y-m-d'))));
// $dd = date('m-d', strtotime('-30 day', strtotime('2019-12-15')));





// echo (date('Y')-1911)."_".$year;
//20210315 業務只能申請下個月的生日禮(EX:現在3月只能申請4月的)
if ($year < 2021) {
	if ($year != date('Y')) { //要顯示明年度的
	
		$str = " sl.sYear ='".$year."'";

	}else{
		if ($cat == 'add') {
			$str = "  sl.sYear ='".$year."'";
		}else if($cat == 'edit'){
			$str = "  sl.sId ='".$sId."'";
		}
		
		//地政士下拉選單
		if ($year == date('Y') && $cat == 'add') {
			

			$dd = date('m')."-01";
			$str .= " sl.sStatus = 0  AND (SUBSTR(s.sBirthday, 6) >= '".$dd."')";

		}
	}
}else{

	if ($year != date('Y')) { //要顯示明年度的
		
		$str = " sl.sYear ='".$year."'";
		$checkMonth = date('Ym');
            
            $month = date('m', strtotime('+1 month', strtotime(substr($checkMonth, 0,4)."-".substr($checkMonth, 4,2)."-01")));

            $str .= " AND (MONTH(s.sBirthday) <= ".$month.")";
		// if (date('m') == 12 && $year == (date('Y')+1) ) { //今年12月顯示明年一月
		// 	$str .= "AND sl.sStatus = 0 AND (SUBSTR(s.sBirthday, 6) = '01') ";
		// }else{
		// 	$str .= "AND sl.sStatus = 0 AND (SUBSTR(s.sBirthday, 6) >= 'xx') "; //不顯示
		// }
		

	}else{
		


		if ($cat == 'add') {	
			$str = "1=1";
			$str .= "  AND sl.sYear ='".$year."'";
			$str .= " AND sl.sStatus = 0";	
			$checkMonth = date('Ym');
			
			$month = date('m', strtotime('+1 month', strtotime(substr($checkMonth, 0,4)."-".substr($checkMonth, 4,2)."-01")));

			$str .= " AND (MONTH(s.sBirthday) <= ".$month.")";
			
			
		}else if($cat == 'edit'){
			$str = " sl.sId ='".$sId."'";
		}
	}
	
	

}



// $str = "  AND sl.sYear ='".$year."'";
// //地政士下拉選單
// if ($year == date('Y') && $cat == 'add') {
// 	// $dd = date('m-d', strtotime('-30 day', strtotime(date('Y-m-d'))));
// 	$dd = date('m')."-01";
// 	$str .= " AND sl.sStatus = 0  AND (SUBSTR(s.sBirthday, 6) >= '".$dd."' OR s.sId = 493)";

// }

if($_SESSION['member_test'] != 0){
      $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
      $rs = $conn->Execute($sql);
      while (!$rs->EOF) {
        $tmpZip[] = "'".$rs->fields['zZip']."'";

        $rs->MoveNext();
      }
      $str .= " AND s.sCpZip1 IN (".@implode(',', $tmpZip).")";
      unset($tmpZip);

 }elseif ($_SESSION['member_pDep'] == 7) {
	$str .= " AND ss.sSales = '".$_SESSION['member_id']."'";
}

$sql = "SELECT	
			sl.sId,
			sl.sScrivener,
			s.sName,
			CONCAT('SC', LPAD(s.sId,4,'0')) as sCode2,
			s.sBirthday,
			sl.sLevel,
			sl.sGift,
			sl.sLock 

		FROM
			tScrivener AS s
		LEFT JOIN
			tScrivenerLevel AS sl ON sl.sScrivener=s.sId
		LEFT JOIN
		    tScrivenerSales AS ss ON ss.sScrivener=s.sId
		WHERE
			".$str."	 
		GROUP BY s.sId
		ORDER BY MONTH(s.sBirthday),DAY(s.sBirthday)";

// echo $sql;
// die($sql);
$rs = $conn->Execute($sql);
// $menuScrivener[0] = '請選擇';
$optionScrivener = '<option value="">請選擇</option>';
while (!$rs->EOF) {
	

	// echo $sql.";<br>";
	if (checkLock($cat,$rs->fields['sLock'],$year,substr($rs->fields['sBirthday'], 5,2)) ) {
		$rs->fields['sBirthday'] = substr($rs->fields['sBirthday'], 5);
		// $menuScrivener[$rs->fields['sId']] = $rs->fields['sCode2'].$rs->fields['sName']."(".$rs->fields['sBirthday']." 等級:".$rs->fields['sLevel'].")";

		$color = ($rs->fields['sLevel'] == 0)? '#FFF':'#FFC9C9';

		$selected = ($sId == $rs->fields['sId'])? 'selected=selected':'';
		
		
		$optionScrivener .= '<option style="background-color:'.$color.' " value="'.$rs->fields['sId'].'" '.$selected.'>'.$rs->fields['sCode2'].$rs->fields['sName'].'('.$rs->fields['sBirthday'].' 等級:'.$rs->fields['sLevel'].')</option>';
	
	}

	

	unset($gift);
	$rs->MoveNext();
}
// die;
##
//年份選單
function checkLock($cat,$lock,$year,$month){
	global $conn;

	if ($lock == 2 || $cat == 'edit') {
		return true;
	}

	$sql = "SELECT sDate FROM tScrivenerBirthdayLock WHERE sDate BETWEEN '".$year."-".$month."-01' AND '".$year."-".$month."-31' AND sLock = 1 ";
	$rs = $conn->Execute($sql);
	
	if ($rs->EOF) {
		return true;
	}else{
		return false;
	}


	
}

if ($cat == 'add') {
	for ($i=date('Y')-1911; $i <= (date('Y')-1911+1) ; $i++) { 
		$menuYear[$i] = $i;
	}
}else{
	for ($i=date('Y')-1910; $i >= 107; $i--) { 
		$menuYear[$i] = $i;
	}
}


##



// if ($_POST['name'] && $_POST['mobile']) {
// 	// $sql = "INSERT INTO tCustomerSales (cName,cMobile,cCreator,cCreatTime) VALUES('".$_POST['name']."','".$_POST['mobile']."','".$_SESSION['member_id']."','".date('Y-m-d H:i:s')."')";
// 	// $conn->Execute($sql);
// }


##地政士列表##
$disabled = '';
$disabled2 = '';
$disabled3 = '';
$disabledTicket = '';
$disabledStatus = '';
$sql = "SELECT * FROM tScrivenerLevel WHERE sId = '".$sId."'";
$rs = $conn->Execute($sql);
$data = $rs->fields;
$giftArr = getGift($data['sLevel'],$data['sGift']);
$data['giftName'] = $giftArr['gCode'].$giftArr['gName'];


#######

if ($cat == 'add') {
	
	$disabled2 = 1;
	$disabledTicket = 1;
	$data['sStatus'] = 1;
	

	
	$data['sGift'] = $giftArr['gId'];
	$data['sMoney'] = $giftArr['gMoney'];
	// $disabled3 = 1;
	// echo $data['sGift']."_____";
	
	unset($giftArr);
}
if ($cat == 'edit') {

	
	$sql = "SELECT * FROM tGift WHERE gDel = 0 AND (sLevel = ".$data['sLevel']." OR sTop = 1)";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$menuGift[$rs->fields['gId']] = $rs->fields;

		$rs->MoveNext();
	}
	
	//member_ScrivenerLevel 0:無權限、 1:限申請、 2:審核(政耀)、3:審核(會計)
	//sStatus 0:未申請 1:申請中 2:審核通過 (政耀)3:不通過(政耀)4:核准(會計)5:不核准(會計

	if ($data['sStatus'] == 1 ) {
		if ($_SESSION['member_ScrivenerLevel'] == 1) {
			// $disabled = 1;
			$disabled2 = 1;
			$disabled3 = 1;
			$disabledTicket = 1;
			// $disabledStatus = 1;
		}else if($_SESSION['member_ScrivenerLevel'] >= 2){
			if ($_SESSION['member_id'] != 3) {
				$disabled = 1;

			}

			if ($_SESSION['member_ScrivenerLevel'] == 2) {
				$disabledTicket = 1;
			}
			
			$disabled3 = 1;
		}
	}elseif ($data['sStatus'] == 2) {
		if ($_SESSION['member_ScrivenerLevel'] == 1) {
			$disabled = 1;
			$disabled3 = 1;
			$disabledStatus = 1;
			$disabled2 = 1;
			$disabledTicket = 1;
		}else if($_SESSION['member_ScrivenerLevel'] == 2){
			$disabled = 1;
			// $disabled2 = 1;
			$disabled3 = 1;
			$disabledTicket = 1;
		}elseif($_SESSION['member_ScrivenerLevel'] >= 3){
			$disabled = 1;
			$disabled2 = 1;
		}
	}elseif ($data['sStatus'] == 4) {
		$disabled = 1;
		$disabled2 = 1;
		$disabled3 = 1;

		if($_SESSION['member_ScrivenerLevel'] < 3) {
			$disabledStatus = 1;
			$disabledTicket = 1;
		}
		
	}else{
		$disabled = 1;
		$disabled2 = 1;
		$disabled3 = 1;
		$disabledTicket = 1;
	}

	$data['sReceipt'] = ($data['sReceipt'] == 1)? 'checked=checked':'';
	$year = $data['sYear'];
	
}


if ($_SESSION['member_ScrivenerLevel'] == 2) { //0:無權限、 1:限申請、 2:審核(政耀)、3:審核(會計)、4:全部
	$menuStatus = array(1 => '申請中',2 =>'主管審核通過',3=>'主管審核不通過' );

	if ($data['sStatus'] == 4) {
		$menuStatus = array(1 => '申請中',2 =>'主管審核通過',3=>'主管審核不通過' ,4=>'已處理');
	}
	# code...
}elseif ($_SESSION['member_ScrivenerLevel'] == 3) {
	if ($_SESSION['member_id'] == 36) {
		$menuStatus = array(1 => '申請中',2 =>'主管審核通過',4=>'已處理',5=>'取消申請');
	}else{
		$menuStatus = array(1 => '申請中',2 =>'主管審核通過',4=>'已處理');
	}
	 
}else{
	// 1 => '申請中',
	// 
	if ($_SESSION['member_id'] == 6) {
		$menuStatus = array(1 => '申請中',2 =>'主管審核通過',4=>'已處理',5=>'取消申請');
	}else{
		$menuStatus = array(1 => '申請中',2 =>'主管審核通過',3=>'主管審核不通過',4=>'已處理' );
	}
	
}

function getGift($level,$gift){
	global $conn;

	if ($gift == 7) {
		$str = " gId = '".$gift."'";
	}else{
		$str = "sLevel = '".$level."'";
	}

	$sql = "SELECT * FROM tGift WHERE ".$str;
	// echo $sql;

	$rs = $conn->Execute($sql);

	return $rs->fields;
}

function sendMsg($lineId,$sId){

	$lineStr = enCrypt('lineId='.$lineId.'&s=SC0224&c=O&cat=edit&id='.$sId);
	$msg = getData($sId)."請審核地政士生日禮";
	$url = "https://www.first1.com.tw/line/firstSales/ApplyBirthdayGift.php?v=".$lineStr;
   
    $data['lineId'] = $lineId;
    $data['url'] = $url;
    $data['title'] ='地政士生日禮申請';
    $data['text'] = $msg;
    $data['label'] = '查看';
   
   
    $url = "https://firstbotnew.azurewebsites.net/bot/api/linePushBubble.php?v=".enCrypt(json_encode($data));
    
    // echo $url;
    file_get_contents($url);
}
##

// echo $data['sStatus']."_";
// echo $disabled."_".$disabled2."_".$disabled3."_";

// $month = date('m');
// $day = date('d');
// $today = date('Y-m-d');
// echo $year."_".date('Y');


// echo $year;
// echo $_SESSION['member_ScrivenerLevel'];
// echo $year;
##
$smarty->assign('listCity', listCity($conn,$data['sZip'])) ;	//聯絡地址-縣市
$smarty->assign('listArea', listArea($conn,$data['sZip'])) ;	//聯絡地址-區域

$smarty->assign('menuScrivener',$menuScrivener);
$smarty->assign('optionScrivener',$optionScrivener);
$smarty->assign('cat',$cat);
$smarty->assign('data',$data);
$smarty->assign('menuStatus',$menuStatus);
$smarty->assign('menuYear',$menuYear);
$smarty->assign('menuGift',$menuGift);
$smarty->assign('sId',$sId);
$smarty->assign('year',($year-1911));
$smarty->assign('disabled',$disabled);
$smarty->assign('disabled2',$disabled2);
$smarty->assign('disabled3',$disabled3);
$smarty->assign('disabledStatus',$disabledStatus);
$smarty->assign('disabledTicket',$disabledTicket);
$smarty->assign('menuIden',array('1' => '------','2' => '身份證編號','3' => '統一編號','4' => '護照號碼' ));
$smarty->display('ScrivenerPresentApply.inc.tpl', '', 'scrivener');
?>
