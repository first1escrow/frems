<?php
include_once dirname(dirname(__DIR__)).'/configs/config.class.php';
include_once dirname(dirname(__DIR__)).'/class/SmartyMain.class.php';
include_once dirname(dirname(__DIR__)).'/class/contract.class.php';
include_once dirname(dirname(__DIR__)).'/web_addr.php' ;
include_once dirname(dirname(__DIR__)).'/openadodb.php' ;
include_once dirname(dirname(__DIR__)).'/session_check.php' ;
##
 
$link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_ENCODED);
$link2 = filter_input(INPUT_POST, 'link2', FILTER_SANITIZE_ENCODED);




$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$_POST['DateStart'] = (substr($_POST['DateStart'], 0,4)+1911)."-".substr($_POST['DateStart'], 4);
$_POST['DateEnd'] = (substr($_POST['DateEnd'], 0,4)+1911)."-".substr($_POST['DateEnd'], 4);

// $contract = new Contract();
// $menu_bank = $contract->GetBankMenuList();
##銀行區域##
$sql = "SELECT * FROM tBankBannerArea ORDER BY bId ASC";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$menu_bank[$rs->fields['bBank']] = $rs->fields['bBankName'];
	$menu_bank2[$rs->fields['bBank']] = $rs->fields['bBankName2'];
	$menu_area[$rs->fields['bBank']] = $rs->fields['bArea'];
	$host = $rs->fields['bUrl'];
	$rs->MoveNext();
}
##
$menu_publish = array(0=>'未上架',1=>'已上架');
##
$cat = empty($_POST["cat"]) 
        ? $_GET["cat"]
        : $_POST["cat"];

$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];
##
if ($_POST['bank']) {
	$uploaddir = $GLOBALS['web_upload'].'/bank/upload/';

	$https_addir = $GLOBALS['webssl_upload'].'/images/ads/upload/';



	//BANNER
	if ($_FILES["upload_file"]["name"]) {
		$ext = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION) ;
		$filename = $_POST['bank']."_".date('Ymd').'.'.$ext ;
		$uploadfile = $uploaddir.$filename;
		if (!move_uploaded_file($_FILES['upload_file']['tmp_name'],$uploadfile)) {
			$error = "2" ;
			echo '上傳失敗';
		}
		$bPic = $GLOBALS['WEB_STAGE']."bank/upload/".$filename.""; //banner 
		
		unset($uploadfile);unset($filename);
	}

	//文件
	if ($_FILES["upload_file2"]["name"]) {
		$ext2 = pathinfo($_FILES["upload_file2"]["name"], PATHINFO_EXTENSION) ;
		$filename= $_POST['bank']."_".date('Ymd').'doc.'.$ext2 ;
		$uploadfile = $uploaddir.$filename ;
		$uploadfileHttp = $https_addir.$filename;
		if (!move_uploaded_file($_FILES['upload_file2']['tmp_name'],$uploadfile)) {
			$error = "2" ;
			echo '上傳失敗2';
		}
		
		if (!copy($uploadfile,$uploadfileHttp)) {
			$error = "2" ;
			
			echo '上傳失敗2HTTPS';
			
		}
		$bUrl = $GLOBALS['WEB_STAGE']."bank/upload/".$filename."";
		$bUrl2 = $GLOBALS['WEB_STAGE_SSL']."images/ads/upload/".$filename."";
		unset($uploadfile);unset($filename);
	}
	##
	//彈跳視窗圖片
	if ($_FILES["upload_file3"]["name"]) {
		$ext2 = pathinfo($_FILES["upload_file3"]["name"], PATHINFO_EXTENSION) ;
		$filename3= $_POST['bank']."_".date('Ymd').'_window.'.$ext2 ;
		$uploadfile = $uploaddir.$filename3 ;
		$uploadfileHttp = $https_addir.$filename3;
		
		if (!move_uploaded_file($_FILES['upload_file3']['tmp_name'],$uploadfile)) {
			$error = "2" ;
			echo '上傳失敗3';
			
		}
		if (!copy($uploadfile,$uploadfileHttp)) {
			$error = "2" ;
			
			echo '上傳失敗3HTTPS';
			
		}

		unset($uploadfile);unset($filename);
		
	}


	##
		//區域設定
		// 滙豐只做~六都+新竹
		// 星展六都+竹苗
		// 渣打雙北+桃竹
		 
		// 台新不做南投縣、台東縣、屏東縣,澎湖及金門
		// 中信也是
	$area = $menu_area[$_POST['bank']];


	if ($cat == 'add' && $error != 2) {
		//bLink2 = '".$_POST['link2']."',

		$sql = "INSERT INTO
					tBankBanner
					(
						bBank,
						bBankName,
						bBankName2,
						bPic,
						bUrl,
						bUrl2,
						bPicWindow,
						bSort,
						bStart,
						bEnd,
						bLink,
						bArea,
						bOk,
						bOk2,
						bLink2
					)
				VALUES
					(
						'".$_POST['bank']."',
						'".$menu_bank[$_POST['bank']]."',
						'".$menu_bank2[$_POST['bank']]."',
						'".$bPic."',
						'".$bUrl."',
						'".$bUrl2."',
						'".$filename3."',
						'".$_POST['sort']."',
						'".$_POST['DateStart']."',
						'".$_POST['DateEnd']."',
						'".$link."',
						'".$area."',
						'".$_POST['ok']."',
						'".$_POST['ok2']."',
						'".$link2."'
					)";
			// echo $sql;
		$conn->Execute($sql);
		$cat = 'mod';
		$id = $conn->Insert_ID(); 

		// if ($filename3 != '') {
		// 	# code...//
		// 	$bUrl2 = 'https://escrow.first1.com.tw/images/ads/article.php?id='.$id;
		// 	$sql = "UPDATE tBankBanner SET bUrl2 ='".$bUrl2."' WHERE bId ='".$id."'";
		// 	$conn->Execute($sql);
		// }
		if ($ext2 == 'jpg' || $ext2 == 'png') {
			$bUrl = $GLOBALS['WEB_STAGE']."article.php?id=".$id;
			$bUrl2 = $GLOBALS['WEB_STAGE_SSL']."images/ads/article.php?id=".$id;

			$sql = "UPDATE tBankBanner SET bUrl2 ='".$bUrl2."',bUrl = '".$bUrl."' WHERE bId ='".$id."'";
			$conn->Execute($sql);
		}
		
		
	}elseif ($cat == 'mod' && $error != 2) {
		// if ($link3 != '') {
		// 	$link2 = $link3;
		// }
		$sqlStr = '';
		if ($bPic != '') {
			$sqlStr .= "bPic ='".$bPic."',";
		
		}elseif ($_POST['delbPic'] == 1) {
			$sqlStr .= "bPic ='',";
		}
		if ($bUrl) {
			$sqlStr .= "bUrl ='".$bUrl."',";
			$sqlStr .= "bUrl2 ='".$bUrl2."',";
		}elseif ($_POST['delbUrl'] == 1) {
			$sqlStr .= "bUrl ='',bUrl2 ='',";
		}

		if ($filename3) {
			$sqlStr .= "bPicWindow = '".$filename3."',";
		}elseif ($_POST['delbPicWindow'] == 1) {
			$sqlStr .= "bPicWindow = '',";
		}

		

		$sql = "UPDATE
					tBankBanner
				SET
					bBank = '".$_POST['bank']."',
					bSort = '".$_POST['sort']."',
					bBankName = '".$menu_bank[$_POST['bank']]."',
					bBankName2 = '".$menu_bank2[$_POST['bank']]."',
					".$sqlStr."
					bStart = '".$_POST['DateStart']."',
					bEnd = '".$_POST['DateEnd']."',
					bLink = '".$link."',
					bLink2 = '".$link2."',
					bArea = '".$area."',
					bOk = '".$_POST['ok']."',
					bOk2 = '".$_POST['ok2']."'
				WHERE
					bId ='".$id."'
				";

		$conn->Execute($sql);
		if ($ext2 == 'jpg' || $ext2 == 'png') {
			$bUrl = $GLOBALS['WEB_STAGE']."article.php?id=".$id;
			$bUrl2 = $GLOBALS['WEB_STAGE_SSL']."images/ads/article.php?id=".$id;

			$sql = "UPDATE tBankBanner SET bUrl2 ='".$bUrl2."',bUrl = '".$bUrl."' WHERE bId ='".$id."'";
			$conn->Execute($sql);
		}
		
	}


	unset($uploadfile);unset($filename);unset($uploadfileHttp);
}

##
$sql = "SELECT * FROM tBankBanner WHERE bId = '".$id."'";
$rs = $conn->Execute($sql);
$data = $rs->fields;

$data['bLink'] = urldecode($data['bLink']);
$data['bLink2'] = urldecode($data['bLink2']);

if ($data['bStart'] && $data['bStart'] != '0000-00-00') {
	$tmp = explode('-', $data['bStart']);
	$data['bStart'] = ($tmp[0]-1911)."-".$tmp[1]."-".$tmp[2];
	unset($tmp);
}

if ($data['bEnd'] && $data['bEnd'] != '0000-00-00') {
	$tmp = explode('-', $data['bEnd']);
	$data['bEnd'] = ($tmp[0]-1911)."-".$tmp[1]."-".$tmp[2];
	unset($tmp);
}

if ($data['bStart'] == '0000-00-00' && $data['bEnd'] == '0000-00-00') {
	$data['check'] = 'checked=checked';
}

for ($i=1; $i <= 12; $i++) { 
	$menu_sort[$i] = $i;
}
##

##
$smarty->assign('cat',$cat);
$smarty->assign('data',$data);
$smarty->assign('id',$id);
$smarty->assign('menu_bank', $menu_bank);
$smarty->assign('menu_sort',$menu_sort);
$smarty->assign('menu_publish',$menu_publish);
$smarty->display('BannerEdit.inc.tpl', '', 'www');
?>