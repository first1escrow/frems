<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;
include_once '../class/lineMessage.php';

$cId = '100080917';//100080917	

$cId = '101141521';
$sql = "SELECT
			cc.cCertifiedId,
			cs.cScrivener,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cr.cBranchNum3
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
		WHERE
			cc.cCertifiedId = '".$cId."'";
				
$rs = $conn->Execute($sql);
$caseData = $rs->fields;


$sales = array();
		$salesId = array();

		$salesId = array_merge($salesId,getScrivenerSales($caseData['cScrivener']));

		if ($caseData['cBranchNum'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum']));
		}
		print_r($salesId);
		if ($caseData['cBranchNum1'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum1']));
		}

		if ($caseData['cBranchNum2'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum2']));
		}

		if ($caseData['cBranchNum3'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum3']));
		}

		foreach ($salesId as $v) {
			$sales[$v] = $v;
		}

// print_r($salesId);
// die;
function getScrivenerSales($id){
	global $conn;
	//地政士業務
	$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$id."'";
	$rs = $conn->Execute($sql);
	$sales = array();
	while (!$rs->EOF) {
		array_push($sales, $rs->fields['sSales']);

		$rs->MoveNext();
	}

	return $sales;

}
function getBranchSales($id){
	global $conn;
	//仲介業務
	$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$id."'";
	$rs = $conn->Execute($sql);
	$sales = array();
	while (!$rs->EOF) {
		array_push($sales, $rs->fields['bSales']);

		$rs->MoveNext();
	}

	return $sales;
}

$v = enCrypt('lineId=Ufd21fe9f27bbc139abe18b1287e1480a&s=SC0224&c=O&cId=101141521');
				$data['lineId'] = 'Ufd21fe9f27bbc139abe18b1287e1480a';
				$data['btn_url'] = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v='.$v;
				$data['title'] = '履保費未收足通知';
				$data['text'] = '保證號碼:'.$cId.'，請填寫原因並審核';
				$data['btn_label'] = '點我填寫';
				echo $data['btn_url'] ;
				// $line->sendFlexTemplateMsg($data);
die;

$str = " AND (cc.cCaseStatus = 2 OR cc.cEndDate >= '2021-07-01 00:00:00' AND cc.cEndDate <= '2021-07-31 23:59:59')"; //

// $str = " AND cc.cCaseStatus = 2";
// $str = " AND cc.cCertifiedId IN('100685155','090478071','100892679','100154776','090153971','090365071')";
	$sql ="SELECT 
            cc.cCertifiedId AS cCertifiedId,
            ci.cTotalMoney AS cTotalMoney,
            ci.cCertifiedMoney as cerifiedmoney,
            ci.cFirstMoney as cFirstMoney,
            cr.cBranchNum AS branch,
            cr.cBranchNum1 AS branch1,
            cr.cBranchNum2 AS branch2,
            cr.cBranchNum3 AS branch3,
            cr.cBrand AS brand,
            cr.cBrand1 AS brand1,
            cr.cBrand2 AS brand2, 
            cr.cBrand3 AS brand3,
            cr.cServiceTarget AS cServiceTarget,
            cr.cServiceTarget1 AS cServiceTarget1,
            cr.cServiceTarget2 AS cServiceTarget2,
            cr.cServiceTarget3 AS cServiceTarget3,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS bRecall,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS bRecall1,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS bRecall2,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS bRecall3,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS scrRecall,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS scrRecall1,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS scrRecall2,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS scrRecall3,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum)  AS bFeedbackMoney,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum1)  AS bFeedbackMoney1,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum2)  AS bFeedbackMoney2,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum3)  AS bFeedbackMoney3,
            (SELECT sRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall,
            (SELECT sSpRecall2 FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall2,
            (SELECT sFeedbackMoney FROM tScrivener WHERE sId=cs.cScrivener) AS sFeedbackMoney,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandScrRecall,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandScrRecall1,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandScrRecall2,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandRecall,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandRecall1,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandRecall2,                   
            cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
            cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
            cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
            cc.cCaseFeedBackMoney3 AS cCaseFeedBackMoney3,
            cc.cCaseFeedback AS cCaseFeedback,
            cc.cCaseFeedback1 AS cCaseFeedback1,
            cc.cCaseFeedback2 AS cCaseFeedback2,
            cc.cCaseFeedback3 AS cCaseFeedback3,
            cc.cFeedbackTarget AS cFeedbackTarget,
            cc.cFeedbackTarget1 AS cFeedbackTarget1,
            cc.cFeedbackTarget2 AS cFeedbackTarget2,
            cc.cFeedbackTarget3 AS cFeedbackTarget3,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum)  AS branchbook,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum1)  AS branchbook1,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum2)  AS branchbook2,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum3)  AS branchbook3,
            cr.cAffixBranch,
            cr.cAffixBranch1,
            cr.cAffixBranch2,
            cr.cAffixBranch3
        FROM 
            tContractCase AS cc
        JOIN tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
        JOIN tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
        JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
        WHERE 
             ci.cTotalMoney !=0 AND cc.cCaseFeedBackModifier ='' AND ci.cCertifiedMoney !=0  ".$str."
        ORDER BY cc.cEndDate ASC"; //AND cc.cFeedBackClose = 0
$rs = $conn->Execute($sql);
while (!$rs->EOF) {

	$cerifiedMoney = ($rs->fields['cTotalMoney']-$rs->fields['cFirstMoney']) * 0.0006; //應收保證費
	// if (($rs->fields['cerifiedmoney'] + 10) < $cerifiedMoney) {
		$check = 0;

		if (($rs->fields['branchbook'] == 0 && $rs->fields['branch'] != 505 && $rs->fields['brand'] != 1 ) ) {
			$check = 1;
		}

		if ($rs->fields['branch1'] > 0 && $rs->fields['branchbook1'] == 0 && $rs->fields['brand1'] != 1) {
			$check = 1;
		}

		if ($rs->fields['branch2'] > 0 && $rs->fields['branchbook2'] == 0 && $rs->fields['brand2'] != 1) {
			$check = 1;
		}

		if ($rs->fields['branch3'] > 0 && $rs->fields['branchbook3'] == 0 && $rs->fields['brand3'] != 1) {
			$check = 1;
		}

		// if (($rs->fields['cerifiedmoney'] + 10) < $cerifiedMoney) {
		// 	$check = 0;
		// }


		// if ($rs->fields['branch1'] > 0 ) {
		// 	$check = 0;
		// }

		getFeedMoney('c',$rs->fields['cCertifiedId']);
		if ($check == 1) {
			
		}
		// $str = array();
		// if ($rs->fields['branchbook'] == 0 && $rs->fields['branch'] != 505) {
		// 	$str[]= " cCaseFeedback = 1";
		// }

		// if ($rs->fields['branchbook1'] == 0 && $rs->fields['branch1'] > 0) {
		// 	$str[]= " cCaseFeedback1 = 1";
		// }

		// if ($rs->fields['branchbook2'] == 0 && $rs->fields['branch2'] > 0) {
		// 	$str[]= " cCaseFeedback2 = 1";
		// }
		
		// if ($rs->fields['branchbook3'] == 0 && $rs->fields['branch3'] > 0) {
		// 	$str[]= " cCaseFeedback3 = 1";
		// }
		// echo $rs->fields['cCertifiedId']."<br>";
	// }

	


	$rs->MoveNext();
}




function getFeedMoney($type,$id,$id2='',$FeedDateCat=''){
	global $conn;

	// echo 'BBBBBBBBBBBBBBB';
	$cCertifiedId = array();

	$nowMonth = date('m');

    if ($FeedDateCat == 1) { //FeedDateCat 0:季1:月
        $sDate = date('Y-m')."-01";
        $eDate = date('Y-m')."-31";
    }else{
        if ($nowMonth >= 1 && $nowMonth <= 3) {
            $sDate = date('Y')."-01-01";
            $eDate = date('Y')."-03-31";
        }elseif ($nowMonth >= 4 && $nowMonth <= 6) {
            $sDate = date('Y')."-04-01";
            $eDate = date('Y')."-06-30";
        }elseif ($nowMonth >= 7 && $nowMonth <= 9) {
            $sDate = date('Y')."-07-01";
            $eDate = date('Y')."-09-30";
        }else {
            $sDate = date('Y')."-10-01";
            $eDate = date('Y')."-12-31";
        }

    }

	if ($type == 's') {
		$str = "AND cs.cScrivener='".$id."'";
	}elseif ($type == 'b') {
		$str = "AND (cr.cBranchNum = '".$id."' OR cr.cBranchNum1 = '".$id."' OR cr.cBranchNum2 = '".$id."')";

	}elseif ($type == 'c') {
		$str = "AND cc.cCertifiedId ='".$id."'";
	}elseif($type == 'bs'){ //品牌回饋代書
		$str = " AND (cr.cBrand = '".$id."' OR cr.cBrand1 = '".$id."' OR cr.cBrand2 = '".$id."') AND cs.cScrivener = '".$id2."'";
	}else{
		// return false;
	}

	$str .= " AND (cc.cCaseStatus = 2 OR cc.cEndDate >= '".$sDate."' AND cc.cEndDate <= '".$eDate."')";

	$sql ="SELECT 
            cc.cCertifiedId AS cCertifiedId,
            ci.cTotalMoney AS cTotalMoney,
            ci.cCertifiedMoney as cerifiedmoney,
            ci.cFirstMoney as cFirstMoney,
            cr.cBranchNum AS branch,
            cr.cBranchNum1 AS branch1,
            cr.cBranchNum2 AS branch2,
            cr.cBranchNum3 AS branch3,
            cr.cBrand AS brand,
            cr.cBrand1 AS brand1,
            cr.cBrand2 AS brand2, 
            cr.cBrand3 AS brand3,
            cr.cServiceTarget AS cServiceTarget,
            cr.cServiceTarget1 AS cServiceTarget1,
            cr.cServiceTarget2 AS cServiceTarget2,
            cr.cServiceTarget3 AS cServiceTarget3,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS bRecall,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS bRecall1,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS bRecall2,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS bRecall3,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS scrRecall,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS scrRecall1,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS scrRecall2,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS scrRecall3,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum)  AS bFeedbackMoney,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum1)  AS bFeedbackMoney1,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum2)  AS bFeedbackMoney2,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum3)  AS bFeedbackMoney3,
            (SELECT sRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall,
            (SELECT sSpRecall2 FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall2,
            (SELECT sFeedbackMoney FROM tScrivener WHERE sId=cs.cScrivener) AS sFeedbackMoney,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandScrRecall,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandScrRecall1,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandScrRecall2,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandRecall,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandRecall1,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandRecall2,                   
            cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
            cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
            cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
            cc.cCaseFeedBackMoney3 AS cCaseFeedBackMoney3,
            cc.cCaseFeedback AS cCaseFeedback,
            cc.cCaseFeedback1 AS cCaseFeedback1,
            cc.cCaseFeedback2 AS cCaseFeedback2,
            cc.cCaseFeedback3 AS cCaseFeedback3,
            cc.cFeedbackTarget AS cFeedbackTarget,
            cc.cFeedbackTarget1 AS cFeedbackTarget1,
            cc.cFeedbackTarget2 AS cFeedbackTarget2,
            cc.cFeedbackTarget3 AS cFeedbackTarget3,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum)  AS branchbook,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum1)  AS branchbook1,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum2)  AS branchbook2,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum3)  AS branchbook3,
            cr.cAffixBranch,
            cr.cAffixBranch1,
            cr.cAffixBranch2,
            cr.cAffixBranch3
        FROM 
            tContractCase AS cc
        JOIN tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
        JOIN tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
        JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
        WHERE 
             ci.cTotalMoney !=0 AND cc.cCaseFeedBackModifier ='' AND ci.cCertifiedMoney !=0  ".$str."
        ORDER BY cc.cEndDate ASC"; //AND cc.cFeedBackClose = 0

 
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
      	$list[] = $rs->fields;
      	$rs->MoveNext();
    }
   
	if (is_array($list)) {

		for ($i=0; $i < count($list); $i++) { 
			$cerifiedMoney = ($list[$i]['cTotalMoney']-$list[$i]['cFirstMoney']) * 0.0006; //應收保證費
		   	$uSql = array(
				'cBranchRecall'=>'',
				'cBranchScrRecall'=>'',
			    'cScrivenerRecall'=>'',
			    'cScrivenerSpRecall'=>'',
			    'cBranchRecall1'=> '',
			    'cCaseFeedback'=>0,
			    'cCaseFeedback1'=>0,
			    'cCaseFeedback2'=>0,
			    'cCaseFeedback3'=>0,
			    'cCaseFeedBackMoney'=>0,
			    'cCaseFeedBackMoney1'=>0,
			    'cCaseFeedBackMoney2'=>0,
			    'cCaseFeedBackMoney3'=>0,
			    'cFeedbackTarget'=>1,
			    'cFeedbackTarget1'=>1,
			    'cFeedbackTarget2'=>1,
			    'cFeedbackTarget3'=>1, 
			    'cBranchRecall2'=>'',
			    'cBranchRecall3'=>'',
			    'cBrandRecall'=>'',
			    'cBrandRecall1'=>'',
			    'cBrandRecall2'=>'',
				'cBrandRecall3'=>'',
				'cSpCaseFeedBackMoney' => 0);
		   	$brecall = array();
		   	$scrrecall = array();
		   	$scrpartsp = array();
		   	$bcount = 0;
		    $scrpart = '';

		   	//確認店家數及地政回饋比率casecheck
			if ($list[$i]['branch'] > 0) {
				if ($list[$i]['cFeedbackTarget'] == 2) {//scrivener
						$brecall[0] = $list[$i]['sRecall']/100; //計算用
				}else{
						$brecall[0] = $list[$i]['bRecall']/100;//計算用
				}
				$uSql['cBranchRecall'] = $list[$i]['bRecall'];
				if ($list[$i]['scrRecall'] != '' && $list[$i]['scrRecall'] != '0') {
				    $scrrecall[0] = $list[$i]['scrRecall']/100;//仲介回饋地政士(仲)
					$uSql['cBranchScrRecall'] = $list[$i]['scrRecall'];
				}

				//品牌回饋代書 
				if ($list[$i]['brandRecall'] != '' ) {
				    $brecall[0] = $list[$i]['brandRecall']/100;
				    $scrpartsp[0] = $list[$i]['brandScrRecall']/100;//地政士部

				    $uSql['cBrandRecall'] = $list[$i]['brandRecall'];

				}

				   
				$bcount++;
			}

			if ($list[$i]['branch1'] > 0) {

				if ($list[$i]['cFeedbackTarget1'] == 2) {//scrivener
					$brecall[1] = $list[$i]['sRecall']/100; //計算用
				}else{
					$brecall[1] = $list[$i]['bRecall1']/100;//計算用
				}

				// array_push($uSql, "cBranchRecall1 = '".$list[$i]['bRecall1']."'");
				$uSql['cBranchRecall1'] = $list[$i]['bRecall1'];
					
				if ($list[$i]['scrRecall1'] != '' && $list[$i]['scrRecall1'] != '0') {
				    $scrrecall[1] = $list[$i]['scrRecall1']/100;//仲介回饋地政士(仲)
				    // array_push($uSql, "cBranchScrRecall = '".$list[$i]['scrRecall1']."'");
				    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall1'];
				}

				//品牌回饋代書 
				if ($list[$i]['brandRecall1'] != '' ) {
				    $brecall[1] = $list[$i]['brandRecall1']/100;
				    $scrpartsp[1] = $list[$i]['brandScrRecall1']/100;//地政士部
				    // array_push($uSql, "cBrandRecall1 = '".$list[$i]['brandRecall1']."'");
				    $uSql['cBrandRecall1'] = $list[$i]['brandRecall1'];
				}

				   
				$bcount++;
			}

			if ($list[$i]['branch2'] > 0) {

				if ($list[$i]['cFeedbackTarget2'] == 2) {//scrivener
					$brecall[2] = $list[$i]['sRecall']/100; //計算用
				}else{
					$brecall[2] = $list[$i]['bRecall2']/100;//計算用
				}

				$uSql['cBranchRecall2'] = $list[$i]['bRecall2'];
				
				if ($list[$i]['scrRecall2'] != '' && $list[$i]['scrRecall2'] != '0') {
				    $scrrecall[2] = $list[$i]['scrRecall2']/100;//仲介回饋地政士(仲)
				    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall2'];
				    // array_push($uSql, "cBranchScrRecall = '".$list[$i]['scrRecall2']."'");
				    
				}

				//品牌回饋代書 
				if ($list[$i]['brandRecall2'] != '' ) {
				   	$brecall[2] = $list[$i]['brandRecall2']/100;
				   	$scrpartsp[2] = $list[$i]['brandScrRecall2']/100;//地政士部
				   	// array_push($uSql, "cBrandRecall2 = '".$list[$i]['brandRecall2']."'");
				   	$uSql['cBrandRecall2'] = $list[$i]['brandRecall2'];
				   
				}

				   
				$bcount++;
			}

			if ($list[$i]['branch3'] > 0) {

				if ($list[$i]['cFeedbackTarget3'] == 2) {//scrivener
					$brecall[3] = $list[$i]['sRecall']/100; //計算用
				}else{
					$brecall[3] = $list[$i]['bRecall3']/100;//計算用
				}

				// array_push($uSql, "cBranchRecall3 = '".$list[$i]['bRecall3']."'");
				$uSql['cBranchRecall3'] = $list[$i]['bRecall3'];

				if ($list[$i]['scrRecall2'] != '' && $list[$i]['scrRecall3'] != '0') {
				    $scrrecall[3] = $list[$i]['scrRecall3']/100;//仲介回饋地政士(仲)
				    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall3'];
				    // array_push($uSql, "cBranchScrRecall = '".$list[$i]['scrRecall3']."'");
				    
				}

				//品牌回饋代書 
				if ($list[$i]['brandRecall3'] != '' ) {
				   	$brecall[3] = $list[$i]['brandRecall3']/100;
				   	$scrpartsp[3] = $list[$i]['brandScrRecall3']/100;//地政士部
				   	$uSql['cBrandRecall3'] = $list[$i]['scrRecall3'];
				   	// array_push($uSql, "cBrandRecall3 = '".$list[$i]['scrRecall3']."'");
				   
				}

				   
				$bcount++;
			}

			//地政士特殊回饋
			if (count($scrrecall) > 0) {
		            rsort($scrrecall); //取一個就好
		            $scrpart = $scrrecall[0];
		    }

		    if (count($scrpartsp) > 0) {
		                    
		        rsort($scrpartsp); //取一個就好
		        $scrpart = $scrpartsp[0];
		    }
		    unset($scrrecall);unset($scrpartsp);

		    $uSql['cScrivenerRecall'] = $list[$i]['sRecall'];
		    $uSql['cScrivenerSpRecall'] = $list[$i]['sSpRecall'];

		    if (($list[$i]['cerifiedmoney'] + 10) < $cerifiedMoney) {
		    	//((地政士有勾未收足回饋且不是回饋代書) 或 (地政士有未勾未收足 )) 且 仲介1、2、3 都不回饋
		    	if ((($list[$i]['sFeedbackMoney'] == 1 && ($list[$i]['cFeedbackTarget'] == 1 || $list[$i]['cFeedbackTarget1'] == 1 || $list[$i]['cFeedbackTarget2'] == 1 || $list[$i]['cFeedbackTarget3'] == 1)) || $list[$i]['sFeedbackMoney'] == 0) && $list[$i]['bFeedbackMoney'] == 0 && $list[$i]['bFeedbackMoney1'] == 0 && $list[$i]['bFeedbackMoney2'] == 0) {
		    		//不回饋
		    		$uSql['cCaseFeedback'] = 0;
		    		$uSql['cCaseFeedback1'] = 0;
		    		$uSql['cCaseFeedback2'] = 0;
		    		$uSql['cCaseFeedback3'] = 0;

		    		$uSql['cCaseFeedBackMoney'] = 0;
		    		$uSql['cCaseFeedBackMoney1'] = 0;
		    		$uSql['cCaseFeedBackMoney2'] = 0;
		    		$uSql['cCaseFeedBackMoney3'] = 0;

		    		$uSql['cSpCaseFeedBackMoney'] = 0;

		    		$bcount = 0;

		    		if ($list[$i]['branch'] > 0) {
		    			$bcount++;
		    		}

		    		if ($list[$i]['branch1'] > 0) {
		    			$bcount++;
		    		}

		    		if ($list[$i]['branch2'] > 0) {
		    			$bcount++;
		    		}

		    		if ($list[$i]['branch3'] > 0) {
		    			$bcount++;
		    		}

		    		
		    		if ($bcount == 1) {
		    			if ($list[$i]['branchbook'] == 0) {
		    				$uSql['cFeedbackTarget'] = 2;
		    			}
		    		}

		    		unset($bcount);
					// $str = implode(',', $uSql);
		    		$str = array();
				    foreach ($uSql as $key => $value) {
				    	$str[]= $key."='".$value."'";
				    }
		    		
		    	 	$sql = "UPDATE tContractCase SET ".@implode(',', $str)." WHERE cCertifiedId ='".$list[$i]['cCertifiedId']."'";
		    	 	echo $sql.";<br>";
		    	 	// $conn->Execute($sql);
		    	 	unset($uSql);
			    	continue;
			    }else{
			    	$feed = 1; //有未收足回饋
			    }
		    }


		    //幸福家
		    //bcount > 1 && ((brand >0 && brand == 69) && (brand1 > 0 && brand1 == 69) && (brand2 > 0 && brand2 == 69) && (brand3 > 0 && brand3 ==69))
			if ($bcount > 1 && (($list[$i]['brand'] > 0 && $list[$i]['brand'] ==69) && ($list[$i]['brand1'] > 0 && $list[$i]['brand1'] ==69) && ($list[$i]['brand2'] > 0 && $list[$i]['brand2'] ==69) && ($list[$i]['brand3'] > 0 && $list[$i]['brand3'] ==69))){
			    //配件只有幸福家
			    	$o =0 ;
			    	if ($list[$i]['cAffixBranch'] == 1) {
			            $ownerbrand = $list[$i]['brand'];
			            $ownercol = 'cCaseFeedBackMoney';
			            $ownerRecall = $brecall[0];
			            $ownercheck = $list[$i]['branchbook'];
		                if ($feed  == 1) {
		                    if (($list[$i]['bFeedbackMoney'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                         $ownerfeed = 'cCaseFeedback';   //1不回饋  
		                    }
		                }
		                $o++;
		            }else{
		                $buyerbrand = $list[$i]['brand'];
		                $buyercol = 'cCaseFeedBackMoney';
		                $buyerRecall = $brecall[0];
		                $buyercheck = $list[$i]['branchbook'];
		                if ($feed  == 1) {
		                    if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                        $buyerfeed = 'cCaseFeedback';   //1不回饋  
		                    }
		                }
		            }        
		                      
		            if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
		                if ($list[$i]['cAffixBranch1'] == 1) {
		                    $ownerbrand = $list[$i]['brand1'];
		                    $ownercol = 'cCaseFeedBackMoney1';
		                    $ownerRecall = $brecall[1];
		                    $ownercheck = $list[$i]['branchbook1'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $ownerfeed = 'cCaseFeedback1';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand1'];
		                    $buyercol = 'cCaseFeedBackMoney1';
		                    $buyerRecall = $brecall[1];
		                    $buyercheck = $list[$i]['branchbook1'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyerfeed = 'cCaseFeedback1';   //1不回饋  
		                        }
		                    }
		                }
		            }

		            if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
		                if ($list[$i]['cAffixBranch2'] == 1) {
		                    $ownerbrand = $list[$i]['brand2'];
		                    $ownercol = 'cCaseFeedBackMoney2';
		                    $ownerRecall = $brecall[2];
		                    $ownercheck = $list[$i]['branchbook2'];
		                                //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $ownerfeed = 'cCaseFeedback2';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand2'];
		                    $buyercol = 'cCaseFeedBackMoney2';
		                    $buyerRecall = $brecall[2];
		                    $buyercheck = $list[$i]['branchbook2'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyerfeed = 'cCaseFeedback2';   //1不回饋  
		                        }
		                    }
		                }
		            }

		            if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
		                if ($list[$i]['cAffixBranch3'] == 1) {
		                    $ownerbrand = $list[$i]['brand3'];
		                    $ownercol = 'cCaseFeedBackMoney3';
		                    $ownerRecall = $brecall[3];
		                    $ownercheck = $list[$i]['branchbook3'];
		                                //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $ownerfeed = 'cCaseFeedback3';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand3'];
		                    $buyercol = 'cCaseFeedBackMoney3';
		                    $buyerRecall = $brecall[3];
		                    $buyercheck = $list[$i]['branchbook3'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyerfeed = 'cCaseFeedback3';   //1不回饋  
		                        }
		                    }
		                }
		            }

		            //以防沒選到契約書用印店(用舊的方法 只回饋給賣方)
		            if ($o == 0) {
		                if ($list[$i]['cFeedbackTarget'] == 2) {
		                    $ownerbrand = $list[$i]['brand'];
		                    $ownercol = 'cCaseFeedBackMoney';
		                    $ownerRecall = $brecall[0];
		                    $ownercheck = $list[$i]['branchbook'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                             $ownerfeed = 'cCaseFeedback';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand'];
		                    $buyercol = 'cCaseFeedBackMoney';
		                    $buyerRecall = $brecall[0];
		                    $buyercheck = $list[$i]['branchbook'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyrfeed = 'cCaseFeedback';   //1不回饋  
		                        }
		                    }
		                }

		                if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
		                    if ($list[$i]['cFeedbackTarget1'] == 2) {
		                        $ownerbrand = $list[$i]['brand1'];
		                        $ownercol = 'cCaseFeedBackMoney1'; 
		                        $ownerRecall = $brecall[1];
		                        $ownercheck = $list[$i]['branchbook1'];
		                        //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                 $ownerfeed = 'cCaseFeedback1';   //1不回饋  
		                            }
		                        }
		                        $o++;
		                    }else{
		                        $buyerbrand = $list[$i]['brand1'];
		                        $buyercol = 'cCaseFeedBackMoney1';
		                        $buyerRecall = $brecall[1];
		                        $buyercheck = $list[$i]['branchbook1'];
		                        //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $buyerfeed = 'cCaseFeedback1';   //1不回饋  
		                            }
		                        }
		                    }
		                }
		                                        
		                if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
		                    if ($list[$i]['cFeedbackTarget2'] == 2) {
		                        $ownerbrand = $list[$i]['brand2'];
		                        $ownercol = 'cCaseFeedBackMoney2';  
		                        $ownerRecall = $brecall[2]; 
		                        $ownercheck = $list[$i]['branchbook2'];
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback2';   //1不回饋  
		                            }
		                        }
		                        $o++; 
		                    }else{
		                        $buyerbrand = $list[$i]['brand2'];
		                        $buyercol = 'cCaseFeedBackMoney2';
		                        $buyerRecall = $brecall[2];
		                        $buyercheck = $list[$i]['branchbook2'];  
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                 $buyerfeed = 'cCaseFeedback2';   //1不回饋  
		                            }
		                        }
		                    }
		                }

		                if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
		                    if ($list[$i]['cFeedbackTarget3'] == 2) {
		                        $ownerbrand = $list[$i]['brand3'];
		                        $ownercol = 'cCaseFeedBackMoney3';  
		                        $ownerRecall = $brecall[3]; 
		                        $ownercheck = $list[$i]['branchbook3'];
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback3';   //1不回饋  
		                            }
		                        }
		                        $o++; 
		                    }else{
		                        $buyerbrand = $list[$i]['brand3'];
		                        $buyercol = 'cCaseFeedBackMoney3';
		                        $buyerRecall = $brecall[3];
		                        $buyercheck = $list[$i]['branchbook3'];  
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                 $buyerfeed = 'cCaseFeedback3';   //1不回饋  
		                            }
		                        }
		                    }
		                }

		                if ($o == 0) {//沒有選定賣方則從買賣方選一個
			                if ($list[$i]['cFeedbackTarget'] == 1) {

			                    $ownerbrand = $list[$i]['brand'];
			                    $ownercol = 'cCaseFeedBackMoney';
			                    $ownerRecall = $brecall[0];
			                    $ownercheck = $list[$i]['branchbook'];  
			                     //未收足不回饋
			                    if ($feed  == 1) {
			                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
			                             $ownerfeed = 'cCaseFeedback';   //1不回饋  
			                        }
			                    }

			                }else if($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['brand1'] > 0) {

		                        $ownerbrand = $list[$i]['brand1'];
		                        $ownercol = 'cCaseFeedBackMoney1';
		                        $ownerRecall = $brecall[1];
		                        $ownercheck = $list[$i]['branchbook1'];
		                       	  //未收足不回饋
		                       	if ($feed  == 1) {
		                           	if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['branchbook1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 &&  $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback1';   //1不回饋  
		                           	}
		                       	}

		                    }else if($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['brand2'] > 0) {

		                        $ownerbrand = $list[$i]['brand2'];
		                        $ownercol = 'cCaseFeedBackMoney2';
		                        $ownerRecall = $brecall[2];
		                        $ownercheck = $list[$i]['branchbook2'];
		                          //未收足回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['branchbook2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback2';   //1不回饋  
		                            }
		                        }
		                    }else if($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['brand3'] > 0) {

		                        $ownerbrand = $list[$i]['brand3'];
		                        $ownercol = 'cCaseFeedBackMoney3';
		                        $ownerRecall = $brecall[3];
		                        $ownercheck = $list[$i]['branchbook3'];
		                          //未收足回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['branchbook3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback3';   //1不回饋  
		                            }
		                        }
		                    }
		                }

		            }

		            if ($ownerbrand == 69) {
		                           
		                if ($ownerfeed == '') {
		                    $_feedbackMoney = round($ownerRecall*$list[$i]['cerifiedmoney']);
		                    $uSql[$ownercol] = $_feedbackMoney;
		                    $uSql[$buyercol] = 0;

		                    // $uSql[] = $ownercol.' = "'.$_feedbackMoney.'"';
		                    // $uSql[] = $buyercol.' = "0"';
		                   
		                }else{
		                	$uSql[$ownercol] = 0;
		                    $uSql[$buyercol] = 0;
		                    $uSql[$ownercol] = 1;
		                    $uSql[$buyercol] = 1;
		                }
		                            


		            }else if ($ownerbrand != 69) {

		             	if ($ownercheck > 0) {//他牌是契約用印店且有合作契約書 各店:保證費*回饋趴/回饋數
		                  
		                    if ($feed  == 1) { //  只有一間有勾選未收足，只算給那一間店
		                        $bcount = 0;
		                        if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
		                            $bcount++;
		                        }

		                        if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
		                        	//是契約書用印店才回饋 
		                            $bcount++;
		                        }

		                        if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch2'] == 1 || $list[$i]['brand2'] == 69)) {
		                            $bcount++;
		                        }
		                                   
		                        // echo $list[$i]['cFeedbackTarget']."_".$list[$i]['bFeedbackMoney']."_".$list[$i]['cAffixBranch']."_".$list[$i]['brand']."<br>";
		                        if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 &&($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) { 
		                        	//是契約書用印店才回饋 && 
		                        	// echo 'A';
		                            $_feedbackMoney = round(($brecall[0]*$list[$i]['cerifiedmoney'])/$bcount);
		                            // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';
		                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
		                                        // $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;    
		                        }else{
		                        	// $uSql[] = 'cCaseFeedBackMoney = "0"';
		                        	// $uSql[] = 'cCaseFeedback = "1"';

		                        	$uSql['cCaseFeedBackMoney'] = 0;
		                        	$uSql['cCaseFeedback'] = 1;
		                        }
		                       

		                        if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
		                        	//是契約書用印店才回饋 
		                        	// echo 'B';
		                             $_feedbackMoney = round(($brecall[1]*$list[$i]['cerifiedmoney'])/$bcount);
		                             // $uSql[] = 'cCaseFeedBackMoney1 = "'.$_feedbackMoney.'"';
		                             $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;
		                                        // $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;
		                        }else{
		                        	$uSql['cCaseFeedBackMoney1'] = 0;
		                        	$uSql['cCaseFeedback1'] = 1;
		                            // $uSql[] = 'cCaseFeedBackMoney1 = "0"';
		                            // $uSql[] = 'cCaseFeedback1 = "1"';
		                        }

		                        if ($bcount == 3) {
		                            if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand2'] == 69)) {//是契約書用印店才回饋 && $("[name='cAffixBranch']:checked").val() == 'b2'
		                                $_feedbackMoney = round(($brecall[2]*$list[$i]['cerifiedmoney'])/$bcount);
		                                // $uSql[] = 'cCaseFeedBackMoney2 = "'.$_feedbackMoney.'"';
		                                $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney;
		                            }else{
		                            	$uSql['cCaseFeedBackMoney2'] = 0;
		                            	$uSql['cCaseFeedback2'] = 1;
		                            }
		                                        
		                        }
		                                    // console.log('A');
		                    }else{
		                        $_feedbackMoney = round(($brecall[0]*$list[$i]['cerifiedmoney'])/$bcount);
		                        // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';
		                        $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;

		                        $_feedbackMoney = round(($brecall[1]*$list[$i]['cerifiedmoney'])/$bcount);
		                        // $uSql[] = 'cCaseFeedBackMoney1 = "'.$_feedbackMoney.'"';
		                        $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;

		                        if ($bcount == 3) {
		                            $_feedbackMoney = round(($brecall[2]*$list[$i]['cerifiedmoney'])/$bcount);
		                            // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';
		                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
		                        }

		                                    // console.log('B');
		                    }

		                }else{
		                    //沒合作契約書回饋給幸福家(買)
		                    if ($buyerfeed == '') {
		                        $_feedbackMoney =round($ownerRecall*$list[$i]['cerifiedmoney']);
		                        // $uSql[] = $buyercol.' = "'.$_feedbackMoney.'"';
		                  		// $uSql[] = $ownercol.' = "0"';
		                  		$uSql[$buyercol] = $_feedbackMoney;
		                  		$uSql[$ownercol] = 0;

		                                  
		                    }else{
		               //          $uSql[] = $ownercol.' = "0"';
					            // $uSql[] = $buyercol.' = "0"';
					            // $uSql[] = $ownerfeed.' = "1"';
					            // $uSql[] = $buyerfeed.' = "1"';

					            $uSql[$ownercol] = 0;
					            $uSql[$buyercol] = 0;
					            $uSql[$ownerfeed] = 1;
					            $uSql[$buyerfeed] = 1;

		                    }
		                               

		                }
		            }


		            if ($list[$i]['branchbook'] == 0) {
		            	$uSql['cCaseFeedBackMoney'] = 0;
		                $uSql['cCaseFeedback'] = 1;
		            }

		            if ($list[$i]['branchbook1'] == 0) {
		            	$uSql['cCaseFeedBackMoney1'] = 0;
		                $uSql['cCaseFeedback1'] = 1;
		            }

		            if ($list[$i]['branchbook2'] == 0) {
		            	$uSql['cCaseFeedBackMoney2'] = 0;
		                $uSql['cCaseFeedback2'] = 1;
		            }

		            if ($list[$i]['branchbook3'] == 0) {
		            	$uSql['cCaseFeedBackMoney3'] = 0;
		                $uSql['cCaseFeedback3'] = 1;
		            }

			}else if ($bcount > 1 && ($list[$i]['brand'] == 69 || $list[$i]['brand1'] == 69 || $list[$i]['brand2'] ==69) ) {
			    	//幸福他排配(含台屋)
			    	$o =0 ;
			    	if ($list[$i]['cServiceTarget'] == 2) {
			            $ownerbrand = $list[$i]['brand'];
			            $ownercol = 'cCaseFeedBackMoney';
			            $ownerRecall = $brecall[0];
			            $ownercheck = $list[$i]['branchbook'];
		                if ($feed  == 1) {
		                    if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                         $ownerfeed = 'cCaseFeedback';   //1不回饋  
		                    }
		                }
		                $o++;
		            }else{
		                $buyerbrand = $list[$i]['brand'];
		                $buyercol = 'cCaseFeedBackMoney';
		                $buyerRecall = $brecall[0];
		                $buyercheck = $list[$i]['branchbook'];
		                if ($feed  == 1) {
		                    if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                        $buyerfeed = 'cCaseFeedback';   //1不回饋  
		                    }
		                }
		            }        
		                      
		            if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
		                if ($list[$i]['cServiceTarget1'] == 2) {
		                    $ownerbrand = $list[$i]['brand1'];
		                    $ownercol = 'cCaseFeedBackMoney1';
		                    $ownerRecall = $brecall[1];
		                    $ownercheck = $list[$i]['branchbook1'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $ownerfeed = 'cCaseFeedback1';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand1'];
		                    $buyercol = 'cCaseFeedBackMoney1';
		                    $buyerRecall = $brecall[1];
		                    $buyercheck = $list[$i]['branchbook1'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyerfeed = 'cCaseFeedback1';   //1不回饋  
		                        }
		                    }
		                }
		            }

		            if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
		                if ($list[$i]['cServiceTarget2'] == 2) {
		                    $ownerbrand = $list[$i]['brand2'];
		                    $ownercol = 'cCaseFeedBackMoney2';
		                    $ownerRecall = $brecall[2];
		                    $ownercheck = $list[$i]['branchbook2'];
		                                //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $ownerfeed = 'cCaseFeedback2';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand2'];
		                    $buyercol = 'cCaseFeedBackMoney2';
		                    $buyerRecall = $brecall[2];
		                    $buyercheck = $list[$i]['branchbook2'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyerfeed = 'cCaseFeedback2';   //1不回饋  
		                        }
		                    }
		                }
		            }

		            if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
		                if ($list[$i]['cServiceTarget3'] == 2) {
		                    $ownerbrand = $list[$i]['brand3'];
		                    $ownercol = 'cCaseFeedBackMoney3';
		                    $ownerRecall = $brecall[3];
		                    $ownercheck = $list[$i]['branchbook3'];
		                                //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $ownerfeed = 'cCaseFeedback3';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand3'];
		                    $buyercol = 'cCaseFeedBackMoney3';
		                    $buyerRecall = $brecall[3];
		                    $buyercheck = $list[$i]['branchbook3'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyerfeed = 'cCaseFeedback3';   //1不回饋  
		                        }
		                    }
		                }
		            }

		            //以防沒選到契約書用印店(用舊的方法 只回饋給賣方)
		            if ($o == 0) {
		                if ($list[$i]['cFeedbackTarget'] == 2) {
		                    $ownerbrand = $list[$i]['brand'];
		                    $ownercol = 'cCaseFeedBackMoney';
		                    $ownerRecall = $brecall[0];
		                    $ownercheck = $list[$i]['branchbook'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                             $ownerfeed = 'cCaseFeedback';   //1不回饋  
		                        }
		                    }
		                    $o++;
		                }else{
		                    $buyerbrand = $list[$i]['brand'];
		                    $buyercol = 'cCaseFeedBackMoney';
		                    $buyerRecall = $brecall[0];
		                    $buyercheck = $list[$i]['branchbook'];
		                    //未收足不回饋
		                    if ($feed  == 1) {
		                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                            $buyrfeed = 'cCaseFeedback';   //1不回饋  
		                        }
		                    }
		                }

		                if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
		                    if ($list[$i]['cFeedbackTarget1'] == 2) {
		                        $ownerbrand = $list[$i]['brand1'];
		                        $ownercol = 'cCaseFeedBackMoney1'; 
		                        $ownerRecall = $brecall[1];
		                        $ownercheck = $list[$i]['branchbook1'];
		                        //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                 $ownerfeed = 'cCaseFeedback1';   //1不回饋  
		                            }
		                        }
		                        $o++;
		                    }else{
		                        $buyerbrand = $list[$i]['brand1'];
		                        $buyercol = 'cCaseFeedBackMoney1';
		                        $buyerRecall = $brecall[1];
		                        $buyercheck = $list[$i]['branchbook1'];
		                        //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $buyerfeed = 'cCaseFeedback1';   //1不回饋  
		                            }
		                        }
		                    }
		                }
		                                        
		                if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
		                    if ($list[$i]['cFeedbackTarget2'] == 2) {
		                        $ownerbrand = $list[$i]['brand2'];
		                        $ownercol = 'cCaseFeedBackMoney2';  
		                        $ownerRecall = $brecall[2]; 
		                        $ownercheck = $list[$i]['branchbook2'];
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback2';   //1不回饋  
		                            }
		                        }
		                        $o++; 
		                    }else{
		                        $buyerbrand = $list[$i]['brand2'];
		                        $buyercol = 'cCaseFeedBackMoney2';
		                        $buyerRecall = $brecall[2];
		                        $buyercheck = $list[$i]['branchbook2'];  
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                 $buyerfeed = 'cCaseFeedback2';   //1不回饋  
		                            }
		                        }
		                    }
		                }

		                if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
		                    if ($list[$i]['cFeedbackTarget3'] == 2) {
		                        $ownerbrand = $list[$i]['brand3'];
		                        $ownercol = 'cCaseFeedBackMoney3';  
		                        $ownerRecall = $brecall[3]; 
		                        $ownercheck = $list[$i]['branchbook3'];
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback3';   //1不回饋  
		                            }
		                        }
		                        $o++; 
		                    }else{
		                        $buyerbrand = $list[$i]['brand3'];
		                        $buyercol = 'cCaseFeedBackMoney3';
		                        $buyerRecall = $brecall[3];
		                        $buyercheck = $list[$i]['branchbook3'];  
		                         //未收足不回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                 $buyerfeed = 'cCaseFeedback3';   //1不回饋  
		                            }
		                        }
		                    }
		                }

		                if ($o == 0) {//沒有選定賣方則從買賣方選一個
			                if ($list[$i]['cFeedbackTarget'] == 1) {

			                    $ownerbrand = $list[$i]['brand'];
			                    $ownercol = 'cCaseFeedBackMoney';
			                    $ownerRecall = $brecall[0];
			                    $ownercheck = $list[$i]['branchbook'];  
			                     //未收足不回饋
			                    if ($feed  == 1) {
			                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
			                             $ownerfeed = 'cCaseFeedback';   //1不回饋  
			                        }
			                    }

			                }else if($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['brand1'] > 0) {

		                        $ownerbrand = $list[$i]['brand1'];
		                        $ownercol = 'cCaseFeedBackMoney1';
		                        $ownerRecall = $brecall[1];
		                        $ownercheck = $list[$i]['branchbook1'];
		                       	  //未收足不回饋
		                       	if ($feed  == 1) {
		                           	if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['branchbook1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 &&  $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback1';   //1不回饋  
		                           	}
		                       	}

		                    }else if($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['brand2'] > 0) {

		                        $ownerbrand = $list[$i]['brand2'];
		                        $ownercol = 'cCaseFeedBackMoney2';
		                        $ownerRecall = $brecall[2];
		                        $ownercheck = $list[$i]['branchbook2'];
		                          //未收足回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['branchbook2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback2';   //1不回饋  
		                            }
		                        }
		                    }else if($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['brand3'] > 0) {

		                        $ownerbrand = $list[$i]['brand3'];
		                        $ownercol = 'cCaseFeedBackMoney3';
		                        $ownerRecall = $brecall[3];
		                        $ownercheck = $list[$i]['branchbook3'];
		                          //未收足回饋
		                        if ($feed  == 1) {
		                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['branchbook3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) { 
		                                $ownerfeed = 'cCaseFeedback3';   //1不回饋  
		                            }
		                        }
		                    }
		                }

		            }

		            if ($ownerbrand == 69) {
		                           
		                if ($ownerfeed == '') {
		                    $_feedbackMoney = round($ownerRecall*$list[$i]['cerifiedmoney']);
		                    // $uSql[] = $ownercol.' = "'.$_feedbackMoney.'"';
		                    // $uSql[] = $buyercol.' = "0"';
		                    $uSql[$ownercol] = $_feedbackMoney;
		                    $uSql[$buyercol] = 0;
		                   
		                }else{

		                	$uSql[$ownercol] = 0;
		                    $uSql[$buyercol] = 0;
		                    $uSql[$ownerfeed] = 1;
		                    $uSql[$buyerfeed] = 1;

		                  	// $uSql[] = $ownercol.' = "0"';
		                    // $uSql[] = $buyercol.' = "0"';
		                    // $uSql[] = $ownerfeed.' = "1"';
		                    // $uSql[] = $buyerfeed.' = "1"';
		                }
		                            


		            }else if ($ownerbrand != 69) {

		             	if ($ownercheck > 0) {//他牌是契約用印店且有合作契約書 各店:保證費*回饋趴/回饋數
		                  
		                    if ($feed  == 1) { //  只有一間有勾選未收足，只算給那一間店
		                        $bcount = 0;
		                        if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
		                            $bcount++;
		                        }

		                        if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
		                        	//是契約書用印店才回饋 
		                            $bcount++;
		                        }

		                        if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch2'] == 1 || $list[$i]['brand2'] == 69)) {
		                            $bcount++;
		                        }
		                                   
		                        // echo $list[$i]['cFeedbackTarget']."_".$list[$i]['bFeedbackMoney']."_".$list[$i]['cAffixBranch']."_".$list[$i]['brand']."<br>";
		                        if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 &&($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) { 
		                        	//是契約書用印店才回饋 && 
		                        	// echo 'A';
		                            $_feedbackMoney = round(($brecall[0]*$list[$i]['cerifiedmoney'])/$bcount);
		                            // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';
		                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
		                                        // $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;    
		                        }else{
		                        	// $uSql[] = 'cCaseFeedBackMoney = "0"';
		                        	// $uSql[] = 'cCaseFeedback = "1"';
		                        	$uSql['cCaseFeedBackMoney'] = 0;
		                        	$uSql['cCaseFeedback'] = 1;
		                        }
		                       

		                        if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
		                        	//是契約書用印店才回饋 
		                        	// echo 'B';
		                             $_feedbackMoney = round(($brecall[1]*$list[$i]['cerifiedmoney'])/$bcount);
		                             // $uSql[] = 'cCaseFeedBackMoney1 = "'.$_feedbackMoney.'"';

		                            $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney; 
		                                        // $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;
		                        }else{
		                        	$uSql['cCaseFeedBackMoney1'] = 0; 
		                        	$uSql['cCaseFeedback1'] = 1; 

		                            // $uSql[] = 'cCaseFeedBackMoney1 = "0"';
		                            // $uSql[] = 'cCaseFeedback1 = "1"';
		                        }

		                        if ($bcount == 3) {
		                            if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand2'] == 69)) {//是契約書用印店才回饋 && $("[name='cAffixBranch']:checked").val() == 'b2'
		                                $_feedbackMoney = round(($brecall[2]*$list[$i]['cerifiedmoney'])/$bcount);
		                                // $uSql[] = 'cCaseFeedBackMoney2 = "'.$_feedbackMoney.'"';
		                                $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney;
		                            }else{
		                            	$uSql['cCaseFeedBackMoney2'] = 0;
		                            	$uSql['cCaseFeedback2'] = 1;
		                            }
		                                        
		                        }
		                                    // console.log('A');
		                    }else{
		                        $_feedbackMoney = round(($brecall[0]*$list[$i]['cerifiedmoney'])/$bcount);
		                        $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;

		                        // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';

		                        $_feedbackMoney = round(($brecall[1]*$list[$i]['cerifiedmoney'])/$bcount);
		                        $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;
		                        // $uSql[] = 'cCaseFeedBackMoney1 = "'.$_feedbackMoney.'"';

		                        if ($bcount == 3) {
		                            $_feedbackMoney = round(($brecall[2]*$list[$i]['cerifiedmoney'])/$bcount);
		                            // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';
		                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
		                        }

		                                    // console.log('B');
		                    }

		                }else{
		                    //沒合作契約書回饋給幸福家(買)
		                    if ($buyerfeed == '') {
		                        $_feedbackMoney =round($buyerRecall*$list[$i]['cerifiedmoney']);
		                        // $uSql[] = $buyercol.' = "'.$_feedbackMoney.'"';
		                  		// $uSql[] = $ownercol.' = "0"';
		                  		$uSql[$buyercol] = $_feedbackMoney;
		                  		$uSql[$ownercol] = 0;

		                                  
		                    }else{
		                        $uSql[$ownercol] = 0;
		                        $uSql[$buyercol] = 0;
		                        $uSql[$ownerfeed] = 0;
		                        $uSql[$buyerfeed] = 0;

					            
		                    }
		                               

		                }
		            }


		            if ($list[$i]['branchbook'] == 0) {
		            	$uSql['cCaseFeedBackMoney'] = 0;
		                $uSql['cCaseFeedback'] = 1;
		            }

		            if ($list[$i]['branchbook1'] == 0) {
		            	$uSql['cCaseFeedBackMoney1'] = 0;
		                $uSql['cCaseFeedback1'] = 1;
		            }

		            if ($list[$i]['branchbook2'] == 0) {
		            	$uSql['cCaseFeedBackMoney2'] = 0;
		                $uSql['cCaseFeedback2'] = 1;
		            }

		            if ($list[$i]['branchbook3'] == 0) {
		            	$uSql['cCaseFeedBackMoney3'] = 0;
		                $uSql['cCaseFeedback3'] = 1;
		            }
			}else{
			    	if ($bcount == 1) { //只有一間店
		                            
		                $_feedbackMoney = round($brecall[0]*$list[$i]['cerifiedmoney']);
		                // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';
		                // $uSql[] = 'cCaseFeedBackMoney1 = "0"';
		                // $uSql[] = 'cCaseFeedBackMoney2 = "0"';
		                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
		                $uSql['cCaseFeedBackMoney1'] = 0;
		                $uSql['cCaseFeedBackMoney2'] = 0;
		                $uSql['cCaseFeedBackMoney3'] = 0;


		                //無合作契約書給代書
	                    if ($list[$i]['branchbook'] != 1 && $list[$i]['branch'] > 0 && $list[$i]['brand'] != 1 && $list[$i]['brand'] != 69) {
	                        $uSql['cFeedbackTarget'] = 2;
	                    }     
	                    

		                                       
		                //如有回饋給地政士另有地政士特殊回饋
		                if (($list[$i]['cFeedbackTarget'] == 2 || $list[$i]['cFeedbackTarget1'] == 2 || $list[$i]['cFeedbackTarget2'] == 2) && ($list[$i]['brand'] != 69 || $list[$i]['brand'] != 1 || $list[$i]['brand'] != 49) && ($list[$i]['sSpRecall'] != '' || $list[$i]['sSpRecall'] != 0)) {
		                	$list[$i]['sSpRecall'] = $list[$i]['sSpRecall']/100;
		                    // echo $brecall[0]."_".$list[$i]['sSpRecall']."_";
		                    if ($list[$i]['sSpRecall'] > $brecall[0]) {
		                    	
		                         $_feedbackMoney = round($list[$i]['sSpRecall']*$list[$i]['cerifiedmoney']);
		                    }else{
		                         $_feedbackMoney = round($brecall[0]*$list[$i]['cerifiedmoney']);
		                    }

		                    $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
		                    $uSql['cCaseFeedBackMoney1'] = 0;
			                $uSql['cCaseFeedBackMoney2'] = 0;
			                $uSql['cCaseFeedBackMoney3'] = 0;
		                    // $uSql[] = 'cCaseFeedBackMoney = "'.$_feedbackMoney.'"';
			                // $uSql[] = 'cCaseFeedBackMoney1 = "0"';
			                // $uSql[] = 'cCaseFeedBackMoney2 = "0"';
		                               
		                    
		                }

		            }else if($bcount > 1){
		                $tmp_c = 0;
		                
	                    
	                    
	                    

	                    //計算回饋
	                    if ($list[$i]['branch'] > 0) {
	                    	$_feedbackMoney = round($brecall[0]*$list[$i]['cerifiedmoney']/$bcount);
	                        $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
	                    }

	                    if ($list[$i]['branch1'] > 0) {
	                    	$_feedbackMoney1 = round($brecall[1]*$list[$i]['cerifiedmoney']/$bcount);
	                        $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney1;
	                    }

	                    if ($list[$i]['branch2'] > 0) {
	                    	$_feedbackMoney2 = round($brecall[2]*$list[$i]['cerifiedmoney']/$bcount);
	                        $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney2;
	                    }

	                    if ($list[$i]['branch3'] > 0) {
	                    	$_feedbackMoney3 = round($brecall[3]*$list[$i]['cerifiedmoney']/$bcount);
	                        $uSql['cCaseFeedBackMoney3'] = $_feedbackMoney3;
	                    }
	                    
	                    //是否為台屋優美或有合作契約書
	                    if (($list[$i]['brand'] == 1 || $list[$i]['brand'] == 49 || $list[$i]['branchbook'] > 0)) {  
	                        $tmp_c++;
	                                
	                    }else{
	                        //無合契
	                        $uSql['cCaseFeedback'] = 1;
	                        $uSql['cCaseFeedBackMoney'] = 0;
	                       
	                    }

	                    if (($list[$i]['brand1'] == 1 || $list[$i]['brand1'] == 49 || $list[$i]['branchbook1'] > 0) && $list[$i]['branch1'] > 0) {  
	                        $tmp_c++;
	                                
	                    }else{
	                        //無合契
	                        $uSql['cCaseFeedback1'] = 1;
	                        $uSql['cCaseFeedBackMoney1'] = 0;
	                       
	                    }

	                    if (($list[$i]['brand2'] == 1 || $list[$i]['brand2'] == 49 || $list[$i]['branchbook2'] > 0) && $list[$i]['branch2'] > 0) {  
	                        $tmp_c++;
	                                
	                    }else{
	                        //無合契
	                        $uSql['cCaseFeedback2'] = 1;
	                        $uSql['cCaseFeedBackMoney2'] = 0;
	                       
	                    }

						if (($list[$i]['brand3'] == 1 || $list[$i]['brand3'] ==49 || $list[$i]['branchbook3'] > 0) && $list[$i]['branch3'] > 0) {
	                        $tmp_c++;

	                    }else{
	                    	//無合契
	                        $uSql['cCaseFeedback3'] = 1;
	                        $uSql['cCaseFeedBackMoney3'] = 0;
	                    }
	                    //配件都沒有合作契約書，回饋給代書
	                    if ($tmp_c == 0) {
	                               
	                        if ($list[$i]['branch'] > 0) {
	                        	$uSql['cCaseFeedback'] = 0;
	                        	$uSql['cFeedbackTarget'] = 2;
	                        	$uSql['cCaseFeedBackMoney'] = $_feedbackMoney;

	                        }

	                        if ($list[$i]['branch1'] > 0) {
	                        	$uSql['cCaseFeedback1'] = 0;
	                        	$uSql['cFeedbackTarget1'] = 2;
	                        	$uSql['cCaseFeedBackMoney1'] = $_feedbackMoney1;
	                        }

	                        if ($list[$i]['branch2'] > 0) {
	                        	$uSql['cCaseFeedback2'] = 0;
	                        	$uSql['cFeedbackTarget2'] = 2;
	                        	$uSql['cCaseFeedBackMoney2'] = $_feedbackMoney2;

	                        }

	                        if ($list[$i]['branch3'] > 0) {
	                        	$uSql['cCaseFeedback3'] = 0;
	                        	$uSql['cFeedbackTarget3'] = 2;
	                        	$uSql['cCaseFeedBackMoney3'] = $_feedbackMoney3;

	                        }
	                    }
		            }
			}



			if ($scrpart != 0 && $scrpart != '') { 
		        $scrFeedMoney = round($scrpart*$list[$i]['cerifiedmoney']) ;
		        // $uSql[] = 'cSpCaseFeedBackMoney = "'.$scrFeedMoney.'"';
		        $uSql['cSpCaseFeedBackMoney'] = $scrFeedMoney;

		    }else{
		        // $('[name="cSpCaseFeedBackMoney"]').val(0) ;
		       $uSql['cSpCaseFeedBackMoney'] = 0;
		    }

		    $str = array();
		    foreach ($uSql as $key => $value) {
		    	$str[]= $key."='".$value."'";
		    }

		   

		   	// die;
		    $sql = "UPDATE tContractCase SET ".@implode(',', $str)." WHERE cCertifiedId ='".$list[$i]['cCertifiedId']."'";
		    
		    echo $sql.";<br>";
		    // $conn->Execute($sql);

		    unset($uSql);

		    //如果有回饋給地政士 特殊回饋不回饋  
		    // echo $scrpart."_".$list[$i]['cFeedbackTarget']."_".$list[$i]['cFeedbackTarget1']."_".$list[$i]['cFeedbackTarget2']."_".$list[$i]['cFeedbackTarget3'].";<br>";

	        if (($scrpart == 0 || $scrpart == '')  && ($uSql['cFeedbackTarget'] != 2 && $uSql['cFeedbackTarget1'] != 2 && $uSql['cFeedbackTarget2'] != 2 && $uSql['cFeedbackTarget3'] != 2)) { //如果仲介品牌有回饋給地政士 特殊回饋不回饋
	        

	            if ($feed == 1) {
	                          
	                if ($list[$i]['sFeedbackMoney'] == 1) {
	                               
	                    SpRecall($list[$i]);
	                }

	            }else{
	                SpRecall($list[$i]);
	            }
	                        
	        }

	        echo "<br>";
	        // write_log($id.":".$sql.";<br>",'checkFeedPart');
    	 	//cBranchRecall cBranchScrRecall cScrivenerRecall cScrivenerSpRecall 回饋比率寫入
    	
    		$cCertifiedId[] = $list[$i]['cCertifiedId'];

		}


	   	

	}
    

    

    return $cCertifiedId;
}


function SpRecall($data){ //特殊回饋金

	global $conn;

	$branchCount = 0;
	//有台屋、非仲一律不回饋
    if ($data['branch'] > 0) {
        $branchCount++;
        if ($data['brand'] != 1 && $data['brand'] != 49 && $data['brand'] != 2) {
            $check++;
        }
    }

    if ($data['branch1'] > 0) {
        $branchCount++;
        if ($data['brand1'] != 1 && $data['brand1'] != 49 && $data['brand1'] != 2) {
            $check ++;
        }
    }

    if ($data['branch2'] > 0) {
        $branchCount++;
        if ($data['brand2'] != 1 && $data['brand2'] != 49 && $data['brand2'] != 2) {
            $check ++;
        }
    }

    if ($data['branch3'] > 0) {
        $branchCount++;
        if ($data['brand3'] != 1 && $data['brand3'] != 49 && $data['brand3'] != 2) {
            $check ++;
        }
    }


   	if ( ($check==$branchCount) && $data['sSpRecall']!=0){
             
        $sSpRecall = round($data['sSpRecall']/ 100,2);  
        $spMoney= round($data['cerifiedmoney'] * $sSpRecall); 

                            
        $str = 'cSpCaseFeedBackMoney = "'.$spMoney.'"';

    }else{
       	$str = 'cSpCaseFeedBackMoney = "0"';
                    
    }

    $sql = "UPDATE tContractCase SET ".$str." WHERE cCertifiedId ='".$data['cCertifiedId']."'";
   	// $conn->Execute($sql);
   	echo $sql.";<br>";
                   
}