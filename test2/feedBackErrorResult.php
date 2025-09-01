<?php
$query = '' ; 
$functions = '' ;
$_POST = escapeStr($_POST) ;

$sEndDate = $_POST['sEndDate'] ;
$eEndDate = $_POST['eEndDate'] ;
$sales = empty($_POST["sales"]) 
        ? $_SESSION['member_id']
        : $_POST["sales"];

$tmp = explode('-',$sEndDate) ;
$sEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
unset($tmp) ;

$tmp = explode('-',$eEndDate) ;
$eEndDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
unset($tmp) ;
##
//取得合約銀行帳號
$_sql = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" GROUP BY cBankAccount ORDER BY cId ASC;' ;
$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$conBank[] = $rs->fields['cBankAccount'] ;

	$rs->MoveNext();
}

$conBank_sql = implode('","',$conBank) ;
##

$contractDate = '' ;
$bankLoansDate = array();
$_sql = '
	SELECT 
		tra.tMemo as cCertifiedId,
		tra.tBankLoansDate
	FROM
		tBankTrans AS tra
	JOIN
		tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=tra.tMemo
	WHERE
		
		tra.tAccount IN ("'.$conBank_sql.'")
		AND tra.tKind = "保證費"
		AND (tra.tBankLoansDate>="'.$sEndDate.'" AND tra.tBankLoansDate<="'.$eEndDate.'")
	GROUP BY
		tra.tMemo
	ORDER BY
		tra.tExport_time
	ASC ;
' ;

$rs = $conn->Execute($_sql);
while (!$rs->EOF) {
	$cid_arr[] = $rs->fields['cCertifiedId'] ;
	$bankLoansDate[$rs->fields['tMemo']] = $rs->fields['tBankLoansDate'];
	$rs->MoveNext();
}

//取出範圍內未收履保費但仍要回饋(有利息)的案件
if ($contractDate) {
	$_sql = 'SELECT cCertifiedId,cBankList FROM tContractCase WHERE cBankList>="'.$sEndDate.'" AND cBankList<="'.$eEndDate.'"';
	$rs = $conn->Execute($_sql);
	while (!$rs->EOF) {
		$cid_arr[] = $rs->fields['cCertifiedId'] ;
		$bankLoansDate[$rs->fields['cCertifiedId']] = $rs->fields['cBankList'];

		$rs->MoveNext();
	}
}
$cId_str= implode('","',$cid_arr) ;

##
$query = ' cc.cCertifiedId<>"" AND cc.cCaseStatus<>"8" AND cc.cCertifiedId !="005030342"' ;
$query .= 'AND cc.cCertifiedId IN("'.@implode('","', $cid_arr).'")';
$total_page = $_POST['total_page'] + 1 - 1 ;
$current_page = $_POST['current_page'] + 1 - 1 ;

$record_limit = $_POST['record_limit'] + 1 - 1 ;

if (!$record_limit) { $record_limit = 10 ; }


if ($_SESSION['member_test'] != 0) { 

	if ($sn == '') {
		if ($query) { $query .= " AND " ; }
		$sql = "SELECT
					b.bId
				FROM
					`tZipArea` AS za
				JOIN
					tBranch AS b ON b.bZip = za.zZip
				WHERE
					za.zTrainee = '".$_SESSION['member_test']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$test_tmp[] = "'".$rs->fields['bId']."'";

			$rs->MoveNext();
		}

		$sql = "SELECT
					s.sId
				FROM
					`tZipArea` AS za
				JOIN
					tScrivener AS s ON s.sCpZip1 = za.zZip
				WHERE
					za.zTrainee = '".$_SESSION['member_test']."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$test_tmp2[] = "'".$rs->fields['sId']."'";

			$rs->MoveNext();
		}

		$query .= "(cr.cBranchNum IN(".implode(',', $test_tmp).") OR cr.cBranchNum1 IN(".implode(',', $test_tmp).") OR cr.cBranchNum2 IN(".implode(',', $test_tmp).") OR cs.cScrivener IN (".implode(',', $test_tmp2)."))";
		unset($test_tmp);unset($test_tmp2);
	}


}

if (is_array($sp)) {
	//先查出各簽約日
	//品牌
	$sql = "SELECT bId,bSignDate,bRecall,bName FROM tBrand WHERE bRecall != '' AND bRecall != 0";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$arrayCategory2['b'.$rs->fields['bId']] = $rs->fields;
		$rs->MoveNext();
	}
	//群組
	$sql = "SELECT bId,bSignDate,bRecall,bName FROM tBranchGroup WHERE bRecall != '' AND bRecall != 0";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$arrayCategory2['g'.$rs->fields['bId']] = $rs->fields;
		$rs->MoveNext();
	}
}

if ($query) { $query = ' WHERE '.$query ; }

$query ='
SELECT 
	cc.cCertifiedId AS cCertifiedId,
	cc.cSignDate,
	cc.cEndDate,
	inc.cCertifiedMoney as cCertifiedMoney,
	inc.cFirstMoney as cFirstMoney,
	(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS BranchName,
	(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS BranchName1,
	(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS BranchName2,	
	(SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum) AS BranchGroup,
	(SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum1) AS BranchGroup1,
	(SELECT bGroup FROM tBranch WHERE bId = cr.cBranchNum2) AS BranchGroup2,	
	(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS BrandName,
	(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS BrandName1,
	(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS BrandName2,
	(SELECT bCode FROM tBrand WHERE bId = cr.cBrand) AS bCode,
	(SELECT bCode FROM tBrand WHERE bId = cr.cBrand1) AS bCode1,
	(SELECT bCode FROM tBrand WHERE bId = cr.cBrand2) AS bCode2,	
	cr.cBrand,
	cr.cBrand1,
	cr.cBrand2,
	cr.cBranchNum,
	cr.cBranchNum1,
	cr.cBranchNum2,
	(SELECT bFeedDateCat FROM tBranch WHERE bId=cr.cBranchNum)  AS bFeedDateCat,
    (SELECT bFeedDateCat FROM tBranch WHERE bId=cr.cBranchNum1)  AS bFeedDateCat1,
    (SELECT bFeedDateCat FROM tBranch WHERE bId=cr.cBranchNum2)  AS bFeedDateCat2,
    (SELECT bCategory FROM tBranch WHERE bId=cr.cBranchNum) category,
	(SELECT bCategory FROM tBranch WHERE bId=cr.cBranchNum1) category1,
	(SELECT bCategory FROM tBranch WHERE bId=cr.cBranchNum2) category2,	
	cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
	cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
	cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
	cc.cSpCaseFeedBackMoney AS ScrivenerSPFeedMoney,
	cc.cSpCaseFeedBackMoneyMark AS cSpCaseFeedBackMoneyMark,
	cc.cCaseFeedback AS cCaseFeedback,
	cc.cCaseFeedback1 AS cCaseFeedback1,
	cc.cCaseFeedback2 AS cCaseFeedback2,
	cc.cFeedbackTarget AS cFeedbackTarget,
	cc.cFeedbackTarget1 AS cFeedbackTarget1,
	cc.cFeedbackTarget2 AS cFeedbackTarget2,
	cc.cBranchScrRecall,
	cc.cBranchScrRecall1,
	cc.cBranchScrRecall2,
	cc.cBrandScrRecall,
	cc.cBrandScrRecall1,
	cc.cBrandScrRecall2,
	cc.cScrivenerSpRecall,	
	cs.cScrivener,
	(SELECT sName FROM tScrivener WHERE sId = cs.cScrivener) AS sName,
	(SELECT sOffice FROM tScrivener WHERE sId = cs.cScrivener) AS sOffice,
	(SELECT sFeedDateCat FROM tScrivener WHERE sId = cs.cScrivener) AS sFeedDateCat,
	(SELECT sCategory FROM tScrivener WHERE sId=cs.cScrivener) as scrivenerCategory,
	cc.cCaseFeedBackModifier,
	buy.cName AS buyer,
	own.cName AS owner,
	inc.cTotalMoney,
	cc.cCaseStatus,
	(SELECT sName FROM tStatusCase AS sc WHERE sc.sId=cc.cCaseStatus) AS status
FROM 
	tContractCase AS cc 
LEFT JOIN 
	tContractBuyer AS buy ON buy.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractOwner AS own ON own.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId 
LEFT JOIN 
	tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractProperty AS pro ON pro.cCertifiedId=cc.cCertifiedId 
LEFT JOIN 
	tContractIncome AS inc ON inc.cCertifiedId=cc.cCertifiedId 
LEFT JOIN
	tZipArea AS zip ON zip.zZip=pro.cZip
LEFT JOIN 
	tScrivener AS scr ON scr.sId = cs.cScrivener
'.$query.' 
GROUP BY
	cc.cCertifiedId
ORDER BY 
	cc.cApplyDate,cc.cId,cc.cSignDate ASC;
' ;



$rs = $conn->Execute($query);
$CaseFeedTotal = array();
$i = 0; $j= 0;
while (!$rs->EOF) {
	if (checkSales($rs->fields,$sales,$conn)) {
		$arr[$i] = $rs->fields;

		//特殊回饋
		##特殊回饋金
		$arr[$i]['sSpRecall'] = '';
		$check=0;


		if($arr[$i]['cBrand']!=1 ){
			if ($arr[$i]['cBrand']!=49) {

				if ($arr[$i]['cBrand'] !=2) {
					$check=1;
				}
				
			}
			
		}elseif ($arr[$i]['cBrand1']!=1&&$arr[$i]['cBrand1']!='0') {
			if ($arr[$i]['cBrand1']!=49) {

				if ($arr[$i]['cBrand1'] !=2) {
					$check=1;
				}
			}
		}elseif ($arr[$i]['cBrand2']!=1&&$arr[$i]['cBrand2']!='0') {
			if ($arr[$i]['cBrand2']!=49) {
				if ($arr[$i]['cBrand2'] !=2) {
					$check=1;
				}
			}
		}
		##
		// 案件總回饋金計算
		if ($rs->fields['cCaseFeedback'] == 0) {
			$CaseFeedTotal[$rs->fields['cCertifiedId']] += $rs->fields['cCaseFeedBackMoney'];
		}
		
		if ($rs->fields['cBranchNum1'] > 0) {
			if ($rs->fields['cCaseFeedback1'] == 0) {
				$CaseFeedTotal[$rs->fields['cCertifiedId']] += $rs->fields['cCaseFeedBackMoney1'];
			}
			
		}

		if ($rs->fields['cBranchNum2'] > 0) {
			if ($rs->fields['cCaseFeedback2'] == 0) {
				$CaseFeedTotal[$rs->fields['cCertifiedId']] += $rs->fields['cCaseFeedBackMoney2'];
			}
			
		}

		if ($rs->fields['ScrivenerSPFeedMoney'] > 0) {
			$CaseFeedTotal[$rs->fields['cCertifiedId']]+=$rs->fields['ScrivenerSPFeedMoney'];
		}

		##

		//如果有仲介代書回饋比率，就顯示

		//cBrandRecall
		if ($arr[$i]['cBranchScrRecall'] == 0 && $arr[$i]['cBranchScrRecall1'] == 0 && $arr[$i]['cBranchScrRecall2'] == 0 && 
			$arr[$i]['cBrandScrRecall'] == 0 && $arr[$i]['cBrandScrRecall1'] == 0 && $arr[$i]['cBrandScrRecall2'] == 0 && 
			$arr[$i]['cBrandRecall'] == 0 && $arr[$i]['cBrandRecall1'] == 0 && $arr[$i]['cBrandRecall2'] == 0) {
			if($check==0 || ($arr[$i]['cScrivenerSpRecall'] == 0 && $arr[$i]['cScrivenerSpRecall2'] == ''))
			{
				$arr[$i]['sSpRecall'] = 'none';
			}else{
				$arr[$i]['sSpRecall'] = '';
			}
		}else{
			$arr[$i]['sSpRecall'] = '';
		}

		unset($check);

		//其他回饋
		$tmp = getOtherFeed3($arr[$i]['cCertifiedId']);

		if ($tmp) {
			$arr[$i]['otherFeedCount'] = count($tmp);
			$arr[$i]['otherFeed'] = $tmp;
			foreach ($tmp as $k => $v) {

				$CaseFeedTotal[$rs->fields['cCertifiedId']]+=$v['fMoney'];
			}
		}
		
		unset($tmp);
		
		if ($cat == 2) {
			$tmp = round(($arr[$i]['cTotalMoney']-$arr[$i]['cFirstMoney'])*0.0006); //萬分之六
			$tmp2 = round(($arr[$i]['cTotalMoney']-$arr[$i]['cFirstMoney'])*0.0006)*0.1;
			if (($tmp-$tmp2) > $arr[$i]['cCertifiedMoney']) {
				
				$arr2[$j] = $arr[$i];
				
				$j++;
			}


			
			unset($tmp); unset($tmp2);
		}elseif ($cat == 3) {
			$check = false;
			
			if ($arr[$i]['cBranchNum1'] > 0 || $arr[$i]['cBranchNum2'] > 0) {
				if ($arr[$i]['cFeedbackTarget'] == 1) { // 回饋仲介
					if ($arr[$i]['bFeedDateCat'] != $arr[$i]['bFeedDateCat1'] ) {
						$check = true;
					}
					//配件回饋地政士
					if (($arr[$i]['bFeedDateCat'] != $arr[$i]['sFeedDateCat']) && $arr[$i]['cFeedbackTarget1'] == 2 && $arr[$i]['cBranchNum1'] > 0) {
						$check = true;
					}


					if (($arr[$i]['bFeedDateCat'] != $arr[$i]['sFeedDateCat']) && $arr[$i]['cFeedbackTarget2'] == 2 && $arr[$i]['cBranchNum2'] > 0) {
						$check = true;
					}

				}elseif ($arr[$i]['cFeedbackTarget'] == 2){
					if (($arr[$i]['sFeedDateCat'] != $arr[$i]['bFeedDateCat1']) && $arr[$i]['cBranchNum1'] > 0) {
						$check = true;
					}
					
					if (($arr[$i]['sFeedDateCat'] != $arr[$i]['bFeedDateCat2']) && $arr[$i]['cBranchNum2'] > 0) {
						$check = true;
					}
				}
			}

			if ($check) {
				$arr2[$j] = $arr[$i];
				$j++;
			}
			unset($check);
		}

		##額外顯示用##
		if (is_array($sp)) {
			foreach ($sp as $k => $v) {
				$FeedBackType = mb_substr($v, 0,1);
				$FeedBackId = (int)mb_substr($v, 1);
				// echo $FeedBackType."_".$FeedBackId;
				if ($FeedBackType == 'b') {
					if ($FeedBackId == 72 ) {
						if ($arr[$i]['cSignDate'] >= '2019-05-01') {
							$sql = "SELECT fCertifiedId FROM tFeedBackMoney WHERE fDelete = 0 AND (fType = 1 OR fType = 2) AND  fCertifiedId = '".$arr[$i]['cCertifiedId']."'";

							$rs2 = $conn->Execute($sql);

							if ($rs2->fields['fCertifiedId']) {
								if ($arr[$i]['cBrand'] == $FeedBackId || $arr[$i]['cBrand1'] == $FeedBackId || $arr[$i]['cBrand2'] == $FeedBackId) { //比對是否有品牌回饋
									$dataSp[$FeedBackId][] = $arr[$i];
								}
							}
							
						}
						
					}else{

						// if ($_SESSION['member_id'] == 6) {
							$sql = "SELECT fCertifiedId FROM tFeedBackMoney WHERE fDelete = 0 AND (fType = 1 OR fType = 2) AND fCertifiedId = '".$arr[$i]['cCertifiedId']."'";

							$rs2 = $conn->Execute($sql);

							if ($rs2->fields['fCertifiedId']) {

								if ($arr[$i]['cBrand'] == $FeedBackId || $arr[$i]['cBrand1'] == $FeedBackId || $arr[$i]['cBrand2'] == $FeedBackId) { //比對是否有品牌回饋
										$dataSp[$FeedBackId][] = $arr[$i];
								}
							}
						// }else{
						// 	if ($arr[$i]['cSignDate'] > $arrayCategory2[$v]['bSignDate']) {
						// 		if ($arr[$i]['cBrand'] == $FeedBackId || $arr[$i]['cBrand1'] == $FeedBackId || $arr[$i]['cBrand2'] == $FeedBackId) { //比對是否有品牌回饋
						// 				$dataSp[$FeedBackId][] = $arr[$i];
						// 		}
						// 	}
						// }
						
						// if ($arr[$i]['cSignDate'] > $arrayCategory2[$v]['bSignDate']) {
							
						// }
						
					}
						
				}else{
					$sql = "SELECT fCertifiedId FROM tFeedBackMoney WHERE fDelete = 0 AND (fType = 1 OR fType = 2) AND fCertifiedId = '".$arr[$i]['cCertifiedId']."'";

					$rs2 = $conn->Execute($sql);

					if ($rs2->fields['fCertifiedId']) {
						if ($arr[$i]['BranchGroup'] == $FeedBackId || $arr[$i]['BranchGroup1'] == $FeedBackId || $arr[$i]['BranchGroup2'] == $FeedBackId) { //比對是否有品牌回饋
							$dataSp[$FeedBackId][] = $arr[$i];
						}
					}
					
				}
					
			}
			unset($FeedBackType);unset($FeedBackId);
				
			
			
		} 
		
		// $Group = 
		
		$i++;

	

	}
	

	$rs->MoveNext();
}

// echo "<pre>";
// print_r($CaseFeedTotal);
// die;

if ($_SESSION['member_id'] == 6) {
	// echo 'AAAA';
	// print_r($_POST);

	// echo $sp;
	// echo "<pre>";
	// print_r($dataSp);
	// die;
				// echo $query;
	// for ($i=0; $i < count( $arr); $i++) { 
	// 	$tmp = round(($arr[$i]['cTotalMoney']-$arr[$i]['cFirstMoney'])*0.0006); //萬分之六
	// 		$tmp2 = round(($arr[$i]['cTotalMoney']-$arr[$i]['cFirstMoney'])*0.0006)+10;


	// 				echo $arr[$i]['cCertifiedId']."_".$tmp."_".$tmp2."_".($tmp-$tmp2)."_".$arr[$i]['cCertifiedMoney']."<br>";
	// }
				
	// 			// echo "<pre>";
	// 			// print_r($arr2);
	// 			// echo "</pre>";
	// die;
}

if ($cat == 2 || $cat == 3) {
	unset($arr);

	
		$arr = $arr2;


	
}elseif (is_array($sp)) {
	unset($arr);
	$arr = array();
	foreach ($sp as $k => $v) {
		$FeedBackType = mb_substr($v, 0,1);
		$FeedBackId = (int)mb_substr($v, 1);
		if (is_array($dataSp)) {
			foreach ($dataSp as $key => $value) {
				// print_r($value);
				$arr = array_merge($arr,$value);
			}
		}
		
		
	}
	// if (in_array(16, $sp)) { //飛鷹
	// 	$arr = $dataSp16;
			
	// }
	// if(in_array(72, $sp)) {//群義
	// 	$arr = $dataSp72;
	// }
}

if ($_POST['xls'] == 'ok') {
	include_once 'feedBackErrorExcel.php';
	die;
}

$max = count($arr);

# 計算總頁數
if (($max % $record_limit) == 0) {
	$total_page = $max / $record_limit ;
}
else {
	$total_page = floor($max / $record_limit) + 1 ;
}
##

# 設定目前頁數顯示範圍
if ($current_page) {
	if ($current_page >= ($max / $record_limit)) {
		if ($max % $record_limit == 0) {
			$current_page = floor($max / $record_limit) ;
		}
		else {
			$current_page = floor($max / $record_limit) + 1 ;
		}
	}
	$i_end = $current_page * $record_limit ;
	$i_begin = $i_end - $record_limit ;
	if ($i_end > $max) {
		$i_end = $max ;
	}
	if($i_end > $max) { $i_end = $max ; }
}
else {
	$i_end = $record_limit ;
	if($i_end > $max) { $i_end = $max ; }
	$i_begin = 0 ;
	$current_page = 1 ;
}

$j = 1 ; 
for ($i = $i_begin ; $i < $i_end ; $i ++) {

	$list[] = $arr[$i];
}
	
unset($arr);

// echo "<pre>";
// print_r($arr);
// echo "</pre>";

######################################################
function checkSales($arr,$pId,$conn)
{

	global $conn;
	global $_POST;

  
    if ($_SESSION['member_pDep'] != 7 && $_POST['sales'] == '') {return true;}
    $twhgCount = 0;//業務不能看直營的案件
    $branch[] = $arr['cBranchNum'];
    if ($arr['cBrand'] == 1 && $arr['category'] == 2) {//仲介台屋直營
        $twhgCount++;
    }

    if ($arr['cBranchNum1'] > 0) {
        $branch[] = $arr['cBranchNum1'];
        if ($arr['cBrand1'] == 1 && $arr['category1'] == 2) {//仲介台屋直營
            $twhgCount++;
        }
    }
    if ($arr['cBranchNum2'] > 0){
    	$branch[] = $arr['cBranchNum2'];
        if ($arr['cBrand2'] == 1 && $arr['category2'] == 2) {//仲介台屋直營
            $twhgCount++;
        }
    }   

   

    if ($twhgCount == count($branch)) { //直營不可以給業務看
        return false;
    }

    if ($_SESSION['member_test'] != 0) {
  		return true;
  	}
    // if ($arr['scrivenerCategory'] == 2 || $arr['cScrivener'] == 1182 || $arr['cScrivener'] == 632) { //直營代書不可以給業務看
    //     return false;
    // }
    
   
    ##
    $salesCount = 0;
    $sql = "SELECT bSales FROM tBranchSales WHERE bBranch IN(".@implode(',', $branch).") AND bSales = '".$pId."'";
   	// echo $sql;
    $rs = $conn->Execute($sql);
    $salesCount +=$rs->RecordCount();


    $sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener =".$arr['cScrivener']." AND sSales='".$pId."'";
    $rs = $conn->Execute($sql);
    $salesCount +=$rs->RecordCount();

    if ($salesCount > 0) {
        return true;
    }else{
        return false;
    }

	// $sql = "SELECT * FROM tPeopleInfo WHERE PDep !=7 AND pId ='".$pId."'";

	// $rs = $conn->Execute($sql);

	// $max = $rs->RecordCount();

	// if ($max > 0) {return true;}
	
	// if ($_SESSION['member_test'] == 1 || $_SESSION['member_test'] == 2 || $_SESSION['member_test'] == 3) {
	// 	return true;
	// }
	
	
	// 	$sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$arr['cCertifiedId']."' AND cSalesId = '".$pId."'";
	// 	// echo $sql;
	// 	$rs2 = $conn->Execute($sql);
			
	// 	if ($rs2->fields['cSalesId']) {
	// 		$max = 1;
	// 	}	

	// $tmp = getOtherFeed3($arr['cCertifiedId']);

	// for ($i=0; $i < count($tmp); $i++) { 
	// 	$sales[] = $tmp['salesId'];
	// }
	
	

	// if ($max > 0) { // 
	// 	return true;
	// }
	// else{
	// 	if (($arr['zCity'] == '台北市' || $arr['zCity'] == '新北市') && $_SESSION['member_id'] == 25) {
	// 	// echo $arr['city'];
	// 		return true;
	// }else{

	// 	if (is_array($sales)) {
	// 		if (in_array($pId, $sales)) {
	// 			return true;
	// 		}
	// 	}
	// 	return false;
	// }
		
	// }

}

?>
