<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$year = ($_POST['year'])? $_POST['year']:(date('Y')-1911);
$month = ($_POST['month'])? $_POST['month']:(int)date('m');
$sDate = ($year+1911)."-".$month."-01";
$eDate = ($year+1911)."-".$month."-31";
// $id = $_POST['id'];

$txt = file_get_contents('http://first3.twhg.com.tw/includes/report/getQuestionAnalysis.php?sDate='.urlencode($sDate).'&eDate='.urlencode($eDate).'&id='.$_POST['id']);
$data =  json_decode($txt,true);


// echo "<pre>";
// print_r($data);
// echo "</pre>";

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
// if ($Questionaire['qSend'] == 1) {

//     // $sDate = '2017-06-19 00:00:00';
//     // $eDate = '2017-06-19 23:59:59';

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
	
// }
// echo "<pre>";
// print_r($data);

//從問卷開始統計年份顯示
for ($i=(substr($Questionaire['qDateStart'], 0,4)-1911); $i <= (date('Y')-1911); $i++) { 
	$menuYear[$i] = $i;
}

for ($i=1; $i <= 12 ; $i++) { 
	$menuMonth[$i] = $i;
}
##

###
$smarty->assign('id',$_POST['id']);
$smarty->assign('year',$year);
$smarty->assign('month',$month);
$smarty->assign('data',$data);
$smarty->assign('menuYear',$menuYear);
$smarty->assign('menuMonth',$menuMonth);
$smarty->display('QuestionAnalysis.inc.tpl', '', 'line');
?>
