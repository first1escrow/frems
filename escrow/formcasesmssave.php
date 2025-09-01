<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$arr = array() ;
$cCertifiedId = $_POST['certified_id'] ;
$scid = $_POST['sScrivener'] ;
$send=$_POST['sSend2'];

$isManage=$_POST['isManage'];
$isManage2=$_POST['isManage2'];

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '案件地政士簡訊修改') ;

//額外新增
$str = '';
for ($i=0; $i < count($_POST['newMobile']); $i++) { 

	if ($_POST['newMobile'][$i]  =='') {
		continue;
	}

	$sql = "INSERT INTO
				tScrivenerSms
			SET
				sScrivener = '".$scid."',
				sNID = '".$_POST['newTitle'][$i]."',
				sName = '".$_POST['newName'][$i]."',
				sMobile = '".$_POST['newMobile'][$i]."',
				sCheck_id = '".$cCertifiedId."'
			";
	$conn->Execute($sql);
	$id = $conn->Insert_ID(); 
	// $id = '21926';
	
	//承辦代書
	if (preg_match('/new/', $isManage)) {
		$tmp = explode('_', $isManage);
		if ($i == $tmp[1]) {
			$isManage = $id;

		}
	}
	unset($tmp);
	//承辦助理
	if (preg_match('/new/', $isManage2)) {
		echo 'BBB';
		$tmp = explode('_', $isManage2);
		// print_r($tmp);
		echo $i."_".$tmp[1]."<br>";
		if ($i == $tmp[1]) {
			$isManage2 = $id;
		}
	}
	unset($tmp);
}


foreach ($_POST['isSelect'] as $k => $v) {
	$tmp = explode('_', $v);

	$name[] = $tmp[0];
	

	$arr[] = trim(addslashes($tmp[1])) ;
	unset($tmp);
}

$isSelect = implode(',',$arr) ;
$isName = implode(',',$name) ;
unset($arr) ;unset($tmp_name);

foreach ($_POST['sSend2'] as $k => $v) {

	$tmp = explode('_', $v);

	$name2[] = $tmp[0];

	$arr[] = trim(addslashes($tmp[1])) ;

	unset($tmp);
}

$send = implode(',',$arr) ;
$isName2 = implode(',', $name2);
unset($arr) ;unset($tmp_name);

//更新合約書代書簡訊發送對象清單
if ($cCertifiedId) {
	if ($isSelect) {
		$sql = 'UPDATE tContractScrivener SET cSmsTarget="'.$isSelect.'",cSmsTargetName="'.$isName.'", cSend2="'.$send.'",cSendName2="'.$isName2.'" WHERE cCertifiedId="'.$cCertifiedId.'" AND cScrivener="'.$scid.'" ;' ;
		$conn->Execute($sql);
	}

	$sql="UPDATE tContractScrivener SET cManage ='".$isManage."',cManage2 ='".$isManage2."' WHERE cCertifiedId ='".$cCertifiedId."' AND cScrivener ='".$scid."'";
	// echo $sql;
	$conn->Execute($sql);
}
##




 header('location: formcasesms.php?scid='.$scid.'&certified_id='.$cCertifiedId.'&ok=1') ;
?>
