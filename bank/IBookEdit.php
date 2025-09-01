<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../../tracelog.php' ;
include_once 'bookFunction.php';
include_once 'class/brand.class.php';
include_once 'class/getBank.php' ;
// $_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;
$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], ' ', '新增指示書') ;

//if ($_SESSION["member_id"] != '1' and $_SESSION["member_id"] != '5' ) { 
if ($_SESSION["member_bankcheck"] != '1') {
    echo '
    <script>
    alert("您無此功能使用權限!!") ;
    </script>
    ' ;
    //header('Location: /bank/new/out.php') ;
    exit ;
}
$bId = $_GET['id'] ;

$sql = "SELECT 
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cTrustAccountName FROM tContractBank WHERE cId=bBank) AS cTrustAccountName,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '".$bId."'";

$rs = $conn->Execute($sql);

$data = $rs->fields;
$tmp = expMoney($rs->fields['bExport_nu']);
$data['expMoney'] = $tmp['totalMoney'];
$data['bStatusName'] = BookStatus($rs->fields['bStatus']);
$data['bDate'] = dateformate($data['bDate']);
//如果為空就帶入今天日期
if ($data['bDate'] == '000-00-00') {
	$data['bDate'] = (date('Y')-1911)."-".date("m-d");
}
$data['expCount'] = $tmp['totalcount'];
$data['CertifiedId_9'] = substr($data['bCertifiedId'],5);
$data['AccountNum'] = substr($data['bObank'],0,3);

$brand = new Brand();
$menu_bank2 = $brand->GetBankMenuList();
$menu_branch = getBankBranch($conn,substr($data['bObank'],0,3),substr($data['bObank'],3)) ;
###補通訊跟退票
//cat1:disabled
$data['show1'] = 'cat1'; //永豐的票據領回
$data['show2'] = 'cat1';//一銀補通訊
$data['show3'] = 'cat1';//共用補通訊
$data['show4'] = 'cat1';//永豐補通訊

if ($data['bBank'] == 1 && $data['bCategory']==6) { //一銀補通訊
	$data['show2'] = ''; 
	$data['show3'] = '';

	
}elseif ($data['bBank'] == 4 || $data['bBank'] == 6) {
	if ($data['bCategory']==6) { //永豐補通訊
		$data['show3'] = '';//共用補通訊
		$data['show4'] = '';//永豐補通訊

	}elseif ($data['bCategory']==7 || $data['bCategory']==8) {//永豐的票據領回
		$data['show1'] = ''; //永豐的代收票據領回
	}
}
###補通訊跟退票


$exp = 1;

//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='".$data['bId']."' AND bDel = 0";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	if ($rs->fields['bCat'] == '1') { //1:錯誤帳戶 //補通訊用
		$data_Error[] = $rs->fields;
	}elseif ($rs->fields['bCat'] == '2') { //2:正確帳戶 //補通訊用
		$data_Correct[] = $rs->fields;
	}else{
		$data_detail[] = $rs->fields;
	}

	$rs->MoveNext();
}

//人員傳真號碼
$sql = "SELECT pFaxNum FROM tPeopleInfo WHERE pId ='".$data['bCreatorId']."'";
$rs = $conn->Execute($sql);
$Fax = $rs->fields['pFaxNum'];

//銀行
$sql = "SELECT cId,cBankName,cBranchName FROM tContractBank WHERE cShow = 1 AND cId !=5";

$rs = $conn->Execute($sql);
$menu_bank[0] = '請選則';
while (!$rs->EOF) {
	# code...
	if ($rs->fields['cId'] == 1) { //一銀不顯示分行
		$rs->fields['cBranchName'] = '';
	}
	$menu_bank[$rs->fields['cId']] = $rs->fields['cBankName'].$rs->fields['cBranchName'];
	$rs->MoveNext();
}
###############
$smarty->assign('opStaus',array(0=>'待確認',1=>'待審核',2=>'已審核'));
$smarty->assign('data',$data);
$smarty->assign('data_detail',$data_detail);
$smarty->assign('Fax',$Fax);
$smarty->assign('exp',$exp);
$smarty->assign('Mod',1);
$smarty->assign('menu_bank',$menu_bank);
$smarty->assign('menu_bank2', $menu_bank2);
$smarty->assign('menu_branch',$menu_branch);
$smarty->assign('data_Error',$data_Error);
$smarty->assign('data_Correct',$data_Correct);
$smarty->assign('ErowCount',count($data_Error)+1);
$smarty->assign('CrowCount',count($data_Correct)+1);

if ($data['bBank'] == 4 || $data['bBank'] == 6) {
	
	if ($data['bCategory'] == 1) {
		$smarty->assign('pdf','sinopac01_pdf.php');
		$smarty->display('IBook01.inc.tpl', '', 'bank') ;
	}elseif ($data['bCategory'] == 2) {
		$smarty->assign('pdf','sinopac02_pdf.php');
		$smarty->display('IBook03.inc.tpl', '', 'bank') ;
	}elseif ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
		$smarty->assign('pdf','sinopac03_pdf.php');
		$smarty->display('IBook03.inc.tpl', '', 'bank') ;
	}else if ($data['bCategory'] == 6) {//6補通訊7退票領回8代收票據領回
		$smarty->assign('pdf','sinopac05_pdf.php');
		$smarty->display('IBook04.inc.tpl', '', 'bank') ;
	}elseif ($data['bCategory'] == 7 || $data['bCategory'] == 8) {
		$smarty->assign('pdf','sinopac04_pdf.php');
		$smarty->display('IBook04.inc.tpl', '', 'bank') ;
	}
	
}elseif ($data['bBank'] == 1) {
	if ($data['bCategory'] == 1) {
		$smarty->assign('pdf','firstInform2.php');
		$smarty->display('IBook01.inc.tpl', '', 'bank') ;
	}elseif ($data['bCategory'] == 6) {
		$smarty->assign('pdf','firstInform3.php');
		$smarty->display('IBook04.inc.tpl', '', 'bank') ;
	}elseif ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
		$smarty->assign('pdf','firstInform1.php');
		$smarty->display('IBook03.inc.tpl', '', 'bank') ;
	}
}elseif ($data['bBank'] == 5) {
	$smarty->display('IBook01.inc.tpl', '', 'bank') ;
}


####

// $smarty->assign('id',$id);
// $smarty->assign('code',$code);
// $smarty->assign('code2',$code2);
// $smarty->assign('tExport_nu',$tExport_nu);
// $smarty->assign('bank_vr',$bank_vr);
// $smarty->assign('bank',$bank);
// $smarty->assign('money',$money);










?>