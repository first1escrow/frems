<?php
include_once '../../openadodb.php';
include_once '../../session_check.php' ;

$_POST = escapeStr($_POST) ;
$feed = $_POST['feed'];
$feed1 = $_POST['feed1'];
$feed2 = $_POST['feed2'];
$feedsp = $_POST['feedsp'];

$msg = 1;

if ($_POST) {

	if ($_POST['cCertifiedId']) {
		$str .= " AND cc.cCertifiedId = '".$_POST['cCertifiedId']."'" ;
	}

	$sql ="SELECT 
			cc.cCertifiedId AS cCertifiedId,
			cc.cCaseStatus AS cCaseStatus,
			cc.cApplyDate AS cApplyDate,
			cc.cBranchRecall AS cBranchRecall,
			cc.cBranchRecall1 AS cBranchRecall1,
			cc.cBranchRecall2 AS cBranchRecall2,
			cc.cBranchScrRecall AS cBranchScrRecall,
			cc.cBranchScrRecall1 AS cBranchScrRecall1,
			cc.cBranchScrRecall2 AS cBranchScrRecall2,
			cc.cScrivenerRecall AS cScrivenerRecall,
			cc.cScrivenerSpRecall AS cScrivenerSpRecall,
			cc.cBrandScrRecall AS cBrandScrRecall,
			cc.cBrandScrRecall1 AS cBrandScrRecall1,
			cc.cBrandScrRecall2 AS cBrandScrRecall2,
			cc.cBrandRecall AS cBrandRecall,
			cc.cBrandRecall1 AS cBrandRecall1,
			cc.cBrandRecall2 AS cBrandRecall2,
			cc.cEndDate AS cEndDate,
			ci.cTotalMoney AS cTotalMoney,
			ci.cCertifiedMoney as cerifiedmoney,
			cr.cBranchNum AS branch,
			cr.cBranchNum1 AS branch1,
			cr.cBranchNum2 AS branch2,
			cr.cBrand AS brand,
			cr.cBrand1 AS brand1,
			cr.cBrand2 AS brand2,		
			cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
			cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
			cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
			cc.cSpCaseFeedBackMoney AS cSpCaseFeedBackMoney,
			cc.cCaseFeedback AS cCaseFeedback,
			cc.cCaseFeedback1 AS cCaseFeedback1,
			cc.cCaseFeedback2 AS cCaseFeedback2,
			cc.cFeedbackTarget AS cFeedbackTarget,
			cc.cFeedbackTarget AS cFeedbackTarget1,
			cc.cFeedbackTarget AS cFeedbackTarget2			
	    FROM 
	   		tContractCase AS cc
	   	JOIN 
	   		tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
	   	JOIN 
	   		tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
	   	JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
	   
	   	WHERE 
	   		1=1 ".$str."
	   		ORDER BY  cc.cEndDate ASC";
	   			
	$rs = $conn->Execute($sql);

	$list = $rs->fields;

	$btmp = $btmp1 = $btmp2 = 0;

	$bcount = 0;
	//確認店家數及地政回饋比率
	if ($list['branch'] > 0) {

		if ($list['cFeedbackTarget'] == 2) {//scrivener
			$bpart[0] = $list['cScrivenerRecall'];
		}else{
			$bpart[0] = $list['cBranchRecall'];
		}

        if ($list['scrRecall1'] != '' || $list['scrRecall1'] != '0') {
        	$scrRePart = $list['scrRecall1'];//仲介回饋地政士
        }

        //品牌回饋代書 
		if ($list['cBrandRecall'] != '') {
			$bpart[0] = $list['cBrandRecall'];
		}

		if ($list['cBrandScrRecall'] != '') {
			$scrRePartsp = $list['cBrandScrRecall'];
		}
                             
        $bcount++;
    }

   

    if ($list['branch1'] > 0) {
    	if ($list['cFeedbackTarget1'] == 2) {//scrivener
			$bpart[1] = $list['cScrivenerRecall'];
		}else{
			$bpart[1] = $list['cBranchRecall1'];
		}



        if ($list['scrRecall2'] != '' || $list['scrRecall2'] != '0') {
        	$scrRePart = $list['scrRecall2'];//仲介回饋地政士
        }

        //品牌回饋代書 
		if ($list['cBrandRecall1'] != '' && $list['cBrandRecall1'] != '0') {
			$bpart[1] = $list['cBrandRecall1'];
		}

		if ($list['cBrandScrRecall1'] != '' && $list['cBrandScrRecall1'] != '0') {
			$scrRePartsp = $list['cBrandScrRecall1'];
		}
                	             
        $bcount++;
    }

    if ($list['branch2'] > 0) {
    	if ($list['cFeedbackTarget2'] == 2) {//scrivener
			$bpart[2] = $list['cScrivenerRecall'];
		}else{
			$bpart[2] = $list['cBranchRecall2'];
		}

        if ($list['scrRecall3'] != '' || $list['scrRecall3'] != '0') {
        	$scrRePart = $list['scrRecall3'];//仲介回饋地政士
        }

        //品牌回饋代書 
		if ($list['cBrandRecall2'] != '' && $list['cBrandRecall2'] != '0') {
			$bpart[2] = $list['cBrandRecall2'];
		}

		if ($list['cBrandScrRecall2'] != '' && $list['cBrandScrRecall2'] != '0') {
			$scrRePartsp = $list['cBrandScrRecall2'];
		}
                             
        $bcount++;
    }


    if ($bcount == 1) { //只有一間店
    	$btmp = round(($feed*100)/$list['cerifiedmoney'],2);//反推仲介1比率
    }else{
    	$btmp = round(($feed*$bcount*100)/$list['cerifiedmoney'],2);//反推仲介1比率

    	$btmp1 = round(($feed1*$bcount*100)/$list['cerifiedmoney'],2);//反推仲介2比率
    	if ($bcount == 3) {
    		$btmp2 = round(($feed2*$bcount*100)/$list['cerifiedmoney'],2);//反推仲介3比率
    	}
    	
    }

    if ($scrRePartsp != '') {
    	$scrRePart = $scrRePartsp;
    }

    $spart = $scrRePart;

    if ($spart != '') {
    	
    	$stmp = round(($feedsp*100)/$list['cerifiedmoney'],2);
    	
    }
   
    if (($btmp != $bpart[0]) || ($btmp1 != $bpart[1]) ||($btmp2 != $bpart[2]) ||($stmp != $spart) ) {
    	
    	// $msg = "原本比率:店一".$bpart[0]."_店二".$bpart[1]."_店三".$bpart[2]."_仲介回饋給地政或特殊回饋比率".$spart."\r\n";
    	
    	// $msg .= '反推的比率:店一'.$btmp."_店二".$btmp1."_店三".$btmp2."_仲介回饋給地政或特殊回饋比率".$stmp."\r\n";

    	$msg .= '注意比率不同';
    }
    
    echo $msg;
    ##old##
	
	
}



?>



