<?php

include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
// include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;

$_GET = escapeStr($_GET) ;

$sDate =$_GET['sDate']." 00:00:00";
$eDate =$_GET['eDate']." 23:59:59";

$sql = "SELECT qSend,qDateStart,qContent FROM tQuestionaire WHERE qId = '".$_GET['id']."'";
$rs = $conn->Execute($sql);
$Questionaire = $rs->fields;

$qContent = json_decode(base64_decode($Questionaire['qContent']),true);

// echo "<pre>";
// print_r($qContent);

// die;

//選項資料陣列組成
for ($i=0; $i < count($qContent); $i++) { 
	$data['count'][$i]['title'] = $qContent[$i]['question'];
	foreach ($qContent[$i]['item'] as $k => $v) {
		$index = $k+1;
		$data['count'][$i]['item'][$index]['value'] = 0;
		$data['count'][$i]['item'][$index]['item'] = $v;
		$data['count'][$i]['item'][$index]['itemScore'] = $qContent[$i]['itemScore'][$k]; //項目配分
		$data['count'][$i]['item'][$index]['score'] = 0;
	}
	
}
unset($index);
// ##
// ##寄送方式
if ($Questionaire['qSend'] == 1) {

    $where = 'cc.cEndDate >= "'.$sDate.'" AND cc.cEndDate <= "'.$eDate.'"';

    if ($_GET['undertaker'] > 0) {
    	$sql = "SELECT sId FROM tScrivener WHERE sUndertaker1 = '".$_GET['undertaker']."'";
    	
    	$rs = $conn->Execute($sql);
    	
    	while (!$rs->EOF) {
    		$scrivenerArr[] = $rs->fields['sId'];

    		$rs->MoveNext();
    	}
    	$where .=" AND cs.cScrivener IN (".@implode(',', $scrivenerArr).")";

	}
         
    //找尋結案案件(LINE發送不見得所有代書都有LINE 所以數量會有落差)
    $sql = "SELECT
                cc.cCertifiedId
            FROM
                tContractCase AS cc
            LEFT JOIN
                tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
            LEFT JOIN
                tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
            ".$col."
            WHERE ".$where;
           

    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
    	$case[] = $rs->fields['cCertifiedId'];

    	$rs->MoveNext();
    }

    $certifiedId = '"'.implode('","', $case).'"';

    //查詢問卷填寫狀況
	$sql = "SELECT qAnswer,qCertifiedId,qAnswer2 FROM tQuestionaireAnswer WHERE qCertifiedId IN(".$certifiedId.") GROUP BY qCertifiedId ORDER BY qId DESC";

	    
	$rs = $conn->Execute($sql);
	$data['total'] = $rs->RecordCount();



	while (!$rs->EOF) {
	// 	//要記錄總數量跟
		
		
		if ($rs->fields['qAnswer2'] != '') { //因為調整問券項目順序 所以部分問答案有調整過 調整過的答案存在2

			$AnsArr = json_decode(base64_decode($rs->fields['qAnswer2']),true);
			foreach ( $AnsArr as $k => $v) {
			
				if (preg_match("/question/", $k)) {
					$index = str_replace('question', '', $k);
					$data['count'][$index]['item'][$v]['value']++;
					$data['count'][$index]['value']++;


					
					$data['count'][$index]['item'][$v]['score']+=(int)$data['count'][$index]['item'][$v]['itemScore'];
					$data['count'][$index]['score']+=$data['count'][$index]['item'][$v]['itemScore'];//項目分數合計
					$data['score'] +=$data['count'][$index]['item'][$v]['itemScore'];//分數總合計
						
					if (preg_match("/不/", $data['count'][$index]['item'][$v]['item'])) {
						$data['count'][$index]['item'][$v]['nogood'][] = $rs->fields['qCertifiedId'];
						
					}


				}
				if (preg_match("/text/", $k) && $v != '') {
					$data['count'][$index]['text'][] = $rs->fields['qCertifiedId'].":".$v;
				}

			}

			
		
			$data['vaild']++; //有效問卷
		}elseif($rs->fields['qAnswer'] != ''){
			$AnsArr = json_decode(base64_decode($rs->fields['qAnswer']),true);
			foreach ( $AnsArr as $k => $v) {
			
				if (preg_match("/question/", $k)) {
					$index = str_replace('question', '', $k);
					$data['count'][$index]['item'][$v]['value']++;
					$data['count'][$index]['value']++;


					
					$data['count'][$index]['item'][$v]['score']+=(int)$data['count'][$index]['item'][$v]['itemScore'];
					$data['count'][$index]['score']+=$data['count'][$index]['item'][$v]['itemScore'];//項目分數合計
					$data['score'] +=$data['count'][$index]['item'][$v]['itemScore'];//分數總合計
						
					if (preg_match("/不/", $data['count'][$index]['item'][$v]['item'])) {
						$data['count'][$index]['item'][$v]['nogood'][] = $rs->fields['qCertifiedId'];
						
					}


				}
				if (preg_match("/text/", $k) && $v != '') {
					$data['count'][$index]['text'][] = $rs->fields['qCertifiedId'].":".$v;
				}

			}

			
		
			$data['vaild']++; //有效問卷
		}else{
			$data['invalid']++;//無效問卷
		}
		

		$rs->MoveNext();
	}
	
}

echo json_encode($data);
?>