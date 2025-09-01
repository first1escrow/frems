<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


$_POST = escapeStr($_POST) ;




if ($_POST) {

	$sDate = (substr($_POST['sDate'], 0,3)+1911).substr($_POST['sDate'], 3);
	$eDate = (substr($_POST['eDate'], 0,3)+1911).substr($_POST['eDate'], 3);

	$undertaker = $_POST['undertaker'];
	if ($_POST['xls']) {
		// ini_set("display_errors", "On"); 
		// error_reporting(E_ALL & ~E_NOTICE);
		include_once 'QuestionAnalysisSearchExcel.php';

	}
	// $id = $_POST['id'];

	// print_r($_POST);

	$http = (!empty($_SERVER['HTTPS']))?'https://':'http://';
	$txt = file_get_contents($http.$_SERVER['HTTP_HOST'].'/includes/report/getQuestionAnalysis.php?sDate='.urlencode($sDate).'&eDate='.urlencode($eDate).'&id='.$_POST['id'].'&undertaker='.$undertaker);
	$data =  json_decode($txt,true);

	$data['avgScore'] = round($data['score']/$data['vaild']);

	if ($_SESSION['member_id'] == 6) {
		
		// echo 'http://first.twhg.com.tw/includes/report/getQuestionAnalysis.php?sDate='.urlencode($sDate).'&eDate='.urlencode($eDate).'&id='.$_POST['id'].'&undertaker='.$undertaker;
	
		// header("Content-Type:text/html; charset=utf-8");
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		//nogood
		// echo $_POST['xls'];

		

	}
	
}
// $url = "http://www.first1.com.tw/line/question/Questionaire.php?v=848a2ba9be8f8bdca5ff9bb02c94d8eb7006e692d1621a0dc77e6811f1f89ab5a5a2bb61fa11a47053c88fd37049164143017eda65164e9220466ea2";


// $undertaker = $_POST['undertaker'];


// $sql = "SELECT qSend,qDateStart,qContent FROM tQuestionaire WHERE qId = '".$_POST['id']."'";
// $rs = $conn->Execute($sql);
// $Questionaire = $rs->fields;

// $qContent = json_decode(base64_decode($Questionaire['qContent']),true);

// //選項資料陣列組成
// for ($i=0; $i < count($qContent); $i++) { 
// 	$data['count'][$i]['title'] = $qContent[$i]['question'];
// 	foreach ($qContent[$i]['item'] as $k => $v) {
// 		$data['count'][$i]['item'][$v] = 0;
// 	}
	
// }
// ##
// ##寄送方式


//     $where = 'cEndDate >= "'.$sDate.'" AND cEndDate <= "'.$eDate.'"';
         
//     //找尋結案案件(LINE發送不見得所有代書都有LINE 所以數量會有落差)
//     $sql = "SELECT
//                 cc.cCertifiedId
//             FROM
//                 tContractCase AS cc
//             LEFT JOIN
//                 tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
//             LEFT JOIN
//                 tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
//             WHERE ".$where;
//     // echo $sql;

//     $rs = $conn->Execute($sql);

//     while (!$rs->EOF) {
//     	$case[] = $rs->fields['cCertifiedId'];

//     	$rs->MoveNext();
//     }

//     $certifiedId = '"'.implode('","', $case).'"';

//     //查詢問卷填寫狀況
// 	$sql = "SELECT qAnswer FROM tQuestionaireAnswer WHERE qCertifiedId IN(".$certifiedId.") ";
	
// 	$rs = $conn->Execute($sql);
// 	$data['total'] = $rs->RecordCount();

// 	while (!$rs->EOF) {
// 	// 	//要記錄總數量跟
		
// 		if ($rs->fields['qAnswer'] != '') {
// 			$AnsArr = json_decode(base64_decode($rs->fields['qAnswer']),true);
// 			// echo base64_decode($rs->fields['qAnswer']);
			
// 			foreach ( $AnsArr as $k => $v) {
// 				if (preg_match("/question/", $k)) {
// 					$index = str_replace('question', '', $k);
// 					$data['count'][$index]['item'][$v]++;
// 				}
				
// 			}
// 			$data['vaild']++; //有效問卷
// 		}else{
// 			$data['invalid']++;//無效問卷
// 		}
		

// 		$rs->MoveNext();
// 	}
	

// echo "<pre>";
// print_r($data);
//經辦
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN (5,6) AND pJob =1";
$rs = $conn->Execute($sql);
$menuPeople[0] = '全部';
while (!$rs->EOF) {
	$menuPeople[$rs->fields['pId']] = $rs->fields['pName'];

	$rs->MoveNext();
}

// //從問卷開始統計年份顯示
// for ($i=(substr($Questionaire['qDateStart'], 0,4)-1911); $i <= (date('Y')-1911); $i++) { 
// 	$menuYear[$i] = $i;
// }

// for ($i=1; $i <= 12 ; $i++) { 
// 	$menuMonth[$i] = $i;
// }
##

###

$smarty->assign('data',$data);
$smarty->assign('menuPeople',$menuPeople);
$smarty->assign('sDate',$_POST['sDate']);
$smarty->assign('eDate',$_POST['eDate']);
$smarty->assign('undertaker',$undertaker);
$smarty->display('QuestionAnalysisSearch.inc.tpl', '', 'report2');
?>
