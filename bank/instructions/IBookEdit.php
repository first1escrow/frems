<?php
include_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
include_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
include_once dirname(dirname(__DIR__)) . '/openadodb.php' ;
include_once dirname(dirname(__DIR__)) . '/session_check.php' ;
include_once dirname(dirname(__DIR__)) . '/tracelog.php' ;
include_once 'bookFunction.php';
include_once dirname(dirname(__DIR__)) . '/class/brand.class.php';
include_once dirname(dirname(__DIR__)) . '/class/getBank.php' ;
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
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName,
			(SELECT count(tId) AS count FROM tBankTrans WHERE tExport_nu = bExport_nu) AS count
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
$data['bODate'] = dateformate($data['bODate']);

//如果為空就帶入今天日期
if ($data['bDate'] == '000-00-00') {
	$data['bDate'] = (date('Y')-1911)."-".date("m-d");
}
$data['expCount'] = $tmp['totalcount'];
$data['CertifiedId_9'] = substr($data['bCertifiedId'],5);
$data['AccountNum'] = substr($data['bObank'],0,3);
$data['NewAccountNum'] = substr($data['bCbank'],0,3);
//如果有指定筆數就帶入
if($data['bSpecificCount'] == 0) {
    $data['bSpecificCount'] = $data['count'] ;
}


$brand = new Brand();
$menu_bank2 = $brand->GetBankMenuList();
$menu_branch = getBankBranch($conn,substr($data['bObank'],0,3),substr($data['bObank'],3)) ;
$menu_branch_new = getBankBranch($conn,substr($data['bCbank'],0,3),substr($data['bCbank'],3)) ;
###補通訊跟退票
//cat1:disabled
$data['show1'] = 'cat1'; //永豐的票據領回
$data['show2'] = 'cat1';//一銀補通訊
$data['show3'] = 'cat1';//共用補通訊
$data['show4'] = 'cat1';//永豐補通訊

if ($data['bBank'] == 1 || $data['bBank'] == 7) { //一銀補通訊
	if ($data['bCategory']==6) { //補通訊
		$data['show2'] = ''; 
		$data['show3'] = '';
	}elseif ($data['bCategory']==7 || $data['bCategory']==8 || $data['bCategory']==11 || $data['bCategory']==12) {
		$data['show1'] = '';
	}

}elseif ($data['bBank'] == 4 || $data['bBank'] == 6 ) {
	if ($data['bCategory']==6) { //永豐補通訊
		$data['show3'] = '';//共用補通訊
		$data['show4'] = '';//永豐補通訊

	}elseif ($data['bCategory']==7 || $data['bCategory']==8 || $data['bCategory'] == 9) {//永豐的票據領回
		$data['show1'] = ''; //永豐的代收票據領回
	}
}elseif ($data['bBank'] == 5) {
	if ($data['bCategory']==6) { //補通訊
		$data['show3'] = '';//補通訊
		$data['show4'] = '';//補通訊

	}elseif ($data['bCategory']==7 || $data['bCategory']==8 || $data['bCategory'] == 9) {//票據領回
		$data['show1'] = ''; //代收票據領回
	}elseif ($data['bCategory'] == 11 || $data['bCategory'] == 12) {
		$data['show4'] = '';
	}
}

###補通訊跟退票


$exp = 1;

//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='".$data['bId']."' AND bDel = 0";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	// $data_detail[] = $rs->fields;
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
$sql = "SELECT cId,cBankName,cBranchName FROM tContractBank WHERE cShow = 1";

$rs = $conn->Execute($sql);
$menu_bank[0] = '請選則';
while (!$rs->EOF) {
	# code...
	if ($rs->fields['cId'] == 1 || $rs->fields['cId'] == 5) { //一銀不顯示分行
		$rs->fields['cBranchName'] = '';
	}
	$menu_bank[$rs->fields['cId']] = $rs->fields['cBankName'].$rs->fields['cBranchName'];
	$rs->MoveNext();
}
###############
$smarty->assign('opStaus',array(0=>'待確認',1=>'待審核',2=>'已審核'));
$smarty->assign('stopStatus',array(0 =>'禁止',1=>'不禁止'));
$smarty->assign('data',$data);
$smarty->assign('data_detail',$data_detail);
$smarty->assign('Fax',$Fax);
$smarty->assign('exp',$exp);
$smarty->assign('Mod',1);
$smarty->assign('menu_bank',$menu_bank);
$smarty->assign('menu_bank2', $menu_bank2);
$smarty->assign('menu_branch',$menu_branch);
$smarty->assign('menu_branch_new',$menu_branch_new);
$smarty->assign('data_Error',$data_Error);
$smarty->assign('data_Correct',$data_Correct);
$smarty->assign('ErowCount',count($data_Error)+1);
$smarty->assign('CrowCount',count($data_Correct)+1);

$template = '';
$pdf = '';

if ($data['bBank'] == 4 || $data['bBank'] == 6) {
	
	if ($data['bCategory'] == 1) {
		$template = 'IBook01.inc.tpl';
		$pdf = 'sinopac01_pdf.php';

	}elseif ($data['bCategory'] == 2) {
		$template = 'IBook03.inc.tpl';
		$pdf = 'sinopac02_pdf.php';
	}elseif ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
		$template = 'IBook03.inc.tpl';
		$pdf = 'sinopac03_pdf.php';

	}else if ($data['bCategory'] == 6) {//6補通訊7退票領回8代收票據領回
		$template = 'IBook04.inc.tpl';
		$pdf = 'sinopac05_pdf.php';

	}elseif ($data['bCategory'] == 7 || $data['bCategory'] == 8 || $data['bCategory'] == 9) {
		$template = 'IBook04.inc.tpl';
		$pdf = 'sinopac04_pdf.php';
	}
	
}elseif ($data['bBank'] == 1 || $data['bBank'] == 7) {
	if ($data['bCategory'] == 1) {
		$template = 'IBook01.inc.tpl';
		$pdf = 'firstInform2.php';

	}elseif ($data['bCategory'] == 6) {
		$template = 'IBook04.inc.tpl';
		$pdf = 'firstInform3.php';

	}elseif ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
		$template = 'IBook03.inc.tpl';
		$pdf = 'firstInform1.php';

	}elseif ($data['bCategory'] == 7 || $data['bCategory'] == 8) {
		$template = 'IBook04.inc.tpl';
		$pdf = 'firstInform4.php';
	}elseif ($data['bCategory'] == 11 || $data['bCategory'] == 12) {
		$template = 'IBook04.inc.tpl';
		$pdf = 'firstInform'.$data['bCategory'].'.php';

	}elseif ($data['bCategory'] == 13) {
		// $smarty->assign('pdf','firstInform2.php');
		// $smarty->display('IBook01.inc.tpl', '', 'bank') ;
		$template = 'IBook01.inc.tpl';
		$pdf = 'firstInform13.php';
	}elseif ($data['bCategory'] == 14) {
        $template = 'IBook01_2.inc.tpl';
        $pdf = 'firstInform14.php';
    }
}elseif ($data['bBank'] == 5) {
	if ($data['bCategory'] == 1) {
		$template = 'IBook01.inc.tpl';
		$pdf = 'taishin01_pdf.php';


	}elseif ($data['bCategory'] == 2) {
		$template = 'IBook03.inc.tpl';
		$pdf = 'taishin06_pdf.php';

	}elseif ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
		$template = 'IBook03.inc.tpl';
		$pdf = 'taishin03_pdf.php';

	}else if ($data['bCategory'] == 6) {//6補通訊7退票領回8代收票據領回
		$template = 'IBook04.inc.tpl';
		$pdf = 'taishin06_pdf.php';
	}elseif ($data['bCategory'] == 7 || $data['bCategory'] == 8 ) {
		$template = 'IBook04.inc.tpl';
		$pdf = 'taishin07_pdf.php';

	}elseif($data['bCategory'] == 10){
		$template = 'IBook01.inc.tpl';
		$pdf = 'taishin10_pdf.php';

	}elseif ($data['bCategory'] == 11) {
		
		$template = 'IBook04.inc.tpl';
		$pdf = 'taishin11_pdf.php';
	}elseif ($data['bCategory'] == 12) {
		
		$template = 'IBook04.inc.tpl';
		$pdf = 'taishin12_pdf.php';
	}
}

$smarty->assign('pdf',$pdf);
$smarty->display($template, '', 'bank') ;
####

// $smarty->assign('id',$id);
// $smarty->assign('code',$code);
// $smarty->assign('code2',$code2);
// $smarty->assign('tExport_nu',$tExport_nu);
// $smarty->assign('bank_vr',$bank_vr);
// $smarty->assign('bank',$bank);
// $smarty->assign('money',$money);










?>