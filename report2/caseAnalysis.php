<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once 'includes/maintain/feedBackData.php';
require_once '../report/getBranchType.php' ;


function setZero($val){


	$val = ($val =='')?'0':$val;

	return $val;
}


$_POST = escapeStr($_POST) ;
$check = trim($_REQUEST['ck']);

if ($check==1) {




	if ($_POST['StartDate']) {
		$tmp = explode('-', $_POST['StartDate']);
		$_POST['StartDate'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
		unset($tmp);
	}

	if ($_POST['EndDate']) {
		$tmp = explode('-', $_POST['EndDate']);
		$_POST['EndDate'] = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
		unset($tmp);
	}

	$date_start = $_POST['StartDate']." 00:00:00";
	$date_end = $_POST['EndDate']." 23:59:59";


	if ($_POST['cat'] == 1) { //進案日期
		$sql = " AND cApplyDate >='".$date_start."' AND cApplyDate <='".$date_end."'";
	}elseif ($_POST['cat'] == 2) {//簽約日期
		$sql = " AND cSignDate >='".$date_start."' AND cSignDate <='".$date_end."'";
	}elseif ($_POST['cat'] == 3) {//結案日期
		$sql = " AND cEndDate >='".$date_start."' AND cEndDate <='".$date_end."'";
	}
	##

	$sql .= ' AND cas.cCertifiedId !="005030342"' ;

	$ShowCat = $_POST['ShowCat'];
	// echo $ShowCat;
	if ($ShowCat == 'area') {
		$sql .= " AND zip.zCity IN('台北市','新北市','台中市','台南市','高雄市','桃園市')";
	}

	$sql ='
	SELECT 
		cas.cEscrowBankAccount as cEscrowBankAccount,
		cas.cCertifiedId as cCertifiedId, 
		cas.cApplyDate as cApplyDate, 
		cas.cSignDate as cSignDate, 
		cas.cFinishDate as cFinishDate,
		cas.cEndDate as cEndDate, 
		inc.cTotalMoney as cTotalMoney, 
		inc.cCertifiedMoney as cCertifiedMoney,
		cas.cCaseFeedBackMoney,
		cas.cCaseFeedBackMoney1,
		cas.cCaseFeedBackMoney2,
		cas.cSpCaseFeedBackMoney,
		cas.cCaseFeedback,
		cas.cCaseFeedback1,
		cas.cCaseFeedback2,
		rea.cBrand as brand,
		rea.cBrand1 as brand1,
		rea.cBrand2 as brand2,
		rea.cBranchNum as branch,
		rea.cBranchNum1 as branch1,
		rea.cBranchNum2 as branch2,
		zip.zCity as city
	FROM 
		tContractCase AS cas 
	LEFT JOIN 
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
	LEFT JOIN 
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
	LEFT JOIN 
		tContractProperty AS cp ON cp.cCertifiedId=cas.cCertifiedId  AND cItem =0
	LEFT JOIN
		tZipArea AS zip ON zip.zZip = cp.cZip
	WHERE
	cas.cCertifiedId<>""
	'.$sql.' AND cas.cCaseStatus<>"8"
	GROUP BY
		cas.cCertifiedId
	ORDER BY 
		cas.cApplyDate,cas.cId,cas.cSignDate ASC;
	' ;

	$rs = $conn->Execute($sql);

	$i = 0;

	while (!$rs->EOF) {
		$arr[$i] = $rs->fields;

		if ($_POST['cat'] == 1) { //進案日期
			// cApplyDate
			$arr[$i]['cApplyDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$i]['cApplyDate'])) ;
			
			$tmp = explode('-',$arr[$i]['cApplyDate']) ;
			$arr[$i]['year'] = $tmp[0]-1911;
			$arr[$i]['month'] = $tmp[1];
		
		}elseif ($_POST['cat'] == 2) {//簽約日期
			// cSignDate
			$arr[$i]['cSignDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$i]['cSignDate'])) ;

			$tmp = explode('-',$arr[$i]['cSignDate']) ;
			$arr[$i]['year'] = $tmp[0]-1911;
			$arr[$i]['month'] = $tmp[1];
		}elseif ($_POST['cat'] == 3) {//結案日期
			// cEndDate
			$arr[$i]['cEndDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$i]['cEndDate'])) ;
			$tmp = explode('-',$arr[$i]['cEndDate']) ;
			$arr[$i]['year'] = $tmp[0]-1911;
			$arr[$i]['month'] = $tmp[1];
		}
		$i++;
		unset($tmp);
		$rs->MoveNext();
	}
	##
	// 案件總筆數	買賣總價金額	合約總保證費金額	
	// 241 	2,313,199,550 	1,380,450 	

	##
	$list1 = array('2' => 0, 'T' => 0, 'U' => 0, 'F' => 0, 'O' => 0, '3' => 0) ;
	for ($i = 0 ; $i < count($arr) ; $i ++) {	
		$type = branch_type($conn,$arr[$i]) ;

		if ($type == 'F') {
			$type = 'O';
		}

		if (preg_match("/^9998[56]0/",$arr[$i]['cEscrowBankAccount'])) {		// 永豐銀行利率
			$bank = 'sinopac';
		}
		else if (preg_match("/^96988/",$arr[$i]['cEscrowBankAccount'])) {		// 台新銀行利率
			$bank = 'taishin';
		}
		else {										// 其他(一銀)銀行利率
			$bank = 'first';
		}

		if ($type != 'N') {
			//總回饋金額
			$cCaseFeedBackMoney = 0;
			$tmp = getOtherFeedMoney($arr[$i]['cCertifiedId']);
						

			if ($arr[$i]['brand'] > 0 ) {
				if ($arr[$i]['cCaseFeedback'] == 0) {
					$cCaseFeedBackMoney += $arr[$i]['cCaseFeedBackMoney'];
				}
			}

			if ($arr[$i]['brand1'] > 0) {
				if ($arr[$i]['cCaseFeedback1'] == 0) {
					$cCaseFeedBackMoney += $arr[$i]['cCaseFeedBackMoney1'];
				}
			}

			if ($arr[$i]['brand2'] > 0) {
				if ($arr[$i]['cCaseFeedback2'] == 0) {
					$cCaseFeedBackMoney += $arr[$i]['cCaseFeedBackMoney2'];
				}
			}

			if ($arr[$i]['cSpCaseFeedBackMoney'] > 0) {
				$cCaseFeedBackMoney += $arr[$i]['cSpCaseFeedBackMoney'];
			}


			if ($tmp['fMoney'] > 0) {
				$cCaseFeedBackMoney += $tmp['fMoney'];
			}
						
			unset($tmp);

			$total['totalMoney']+=$arr[$i]['cTotalMoney'];
			$total['certifiedMoney']+=$arr[$i]['cCertifiedMoney'] ;
			$total['count']++ ;
			$total['feedbackmoney'] += $cCaseFeedBackMoney;

			$year[$arr[$i]['year']]['totalMoney']+=$arr[$i]['cTotalMoney'] ;
			$year[$arr[$i]['year']]['certifiedMoney']+=$arr[$i]['cCertifiedMoney'] ;
			$year[$arr[$i]['year']]['count']++;
			$year[$arr[$i]['year']]['feedbackmoney'] += $cCaseFeedBackMoney;


			$data[$arr[$i]['year']][$arr[$i]['month']]['totalMoney']+=$arr[$i]['cTotalMoney'] ;
			$data[$arr[$i]['year']][$arr[$i]['month']]['certifiedMoney']+=$arr[$i]['cCertifiedMoney'] ;
			$data[$arr[$i]['year']][$arr[$i]['month']]['count']++;
			$data[$arr[$i]['year']][$arr[$i]['month']]['feedbackmoney'] += $cCaseFeedBackMoney;
			##

			##銀行分類##

			$year[$arr[$i]['year']]['totalMoney'.$bank]+=$arr[$i]['cTotalMoney'] ;
			$year[$arr[$i]['year']]['certifiedMoney'.$bank]+=$arr[$i]['cCertifiedMoney'] ;
			$year[$arr[$i]['year']]['count'.$bank]++;
			$year[$arr[$i]['year']]['feedbackmoney'.$bank] += $cCaseFeedBackMoney;

			$data[$arr[$i]['year']][$arr[$i]['month']]['totalMoney'.$bank]+=$arr[$i]['cTotalMoney'] ;
			$data[$arr[$i]['year']][$arr[$i]['month']]['certifiedMoney'.$bank]+=$arr[$i]['cCertifiedMoney'] ;
			$data[$arr[$i]['year']][$arr[$i]['month']]['count'.$bank]++;
			$data[$arr[$i]['year']][$arr[$i]['month']]['feedbackmoney'.$bank] += $cCaseFeedBackMoney;

			$total['totalMoney'.$bank]+=$arr[$i]['cTotalMoney'];
			$total['certifiedMoney'.$bank]+=$arr[$i]['cCertifiedMoney'] ;
			$total['count'.$bank]++ ;
			$total['feedbackmoney'.$bank] += $cCaseFeedBackMoney;

			##

			if ($ShowCat == 'br') {
				##品牌分類##		
				$year[$arr[$i]['year']]['totalMoney'.$type]+=$arr[$i]['cTotalMoney'] ;
				$year[$arr[$i]['year']]['certifiedMoney'.$type]+=$arr[$i]['cCertifiedMoney'] ;
				$year[$arr[$i]['year']]['count'.$type]++;
				$year[$arr[$i]['year']]['feedbackmoney'.$type] += $cCaseFeedBackMoney;


				$data[$arr[$i]['year']][$arr[$i]['month']]['totalMoney'.$type]+=$arr[$i]['cTotalMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['certifiedMoney'.$type]+=$arr[$i]['cCertifiedMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['count'.$type]++;
				$data[$arr[$i]['year']][$arr[$i]['month']]['feedbackmoney'.$type] += $cCaseFeedBackMoney;


				$total['totalMoney'.$type]+=$arr[$i]['cTotalMoney'];
				$total['certifiedMoney'.$type]+=$arr[$i]['cCertifiedMoney'] ;
				$total['count'.$type]++ ;
				$total['feedbackmoney'.$type] += $cCaseFeedBackMoney;


				//銀行+品牌
				$year[$arr[$i]['year']]['totalMoney'.$type.$bank]+=$arr[$i]['cTotalMoney'] ;
				$year[$arr[$i]['year']]['certifiedMoney'.$type.$bank]+=$arr[$i]['cCertifiedMoney'] ;
				$year[$arr[$i]['year']]['count'.$type.$bank]++;
				$year[$arr[$i]['year']]['feedbackmoney'.$type.$bank] += $cCaseFeedBackMoney;

				$data[$arr[$i]['year']][$arr[$i]['month']]['totalMoney'.$type.$bank]+=$arr[$i]['cTotalMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['certifiedMoney'.$type.$bank]+=$arr[$i]['cCertifiedMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['count'.$type.$bank]++;
				$data[$arr[$i]['year']][$arr[$i]['month']]['feedbackmoney'.$type.$bank] += $cCaseFeedBackMoney;

				$total['totalMoney'.$type.$bank]+=$arr[$i]['cTotalMoney'];
				$total['certifiedMoney'.$type.$bank]+=$arr[$i]['cCertifiedMoney'] ;
				$total['count'.$type.$bank]++ ;
				$total['feedbackmoney'.$type.$bank] += $cCaseFeedBackMoney;
				##
			}elseif ($ShowCat == 'area') {
				//地區

				$arr[$i]['city'] = ($arr[$i]['city']=='')? '未知':$arr[$i]['city'];
				##地區##		
				$year[$arr[$i]['year']]['totalMoney'.$arr[$i]['city']]+=$arr[$i]['cTotalMoney'] ;
				$year[$arr[$i]['year']]['certifiedMoney'.$arr[$i]['city']]+=$arr[$i]['cCertifiedMoney'] ;
				$year[$arr[$i]['year']]['count'.$arr[$i]['city']]++;
				$year[$arr[$i]['year']]['feedbackmoney'.$arr[$i]['city']] += $cCaseFeedBackMoney;


				$data[$arr[$i]['year']][$arr[$i]['month']]['totalMoney'.$arr[$i]['city']]+=$arr[$i]['cTotalMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['certifiedMoney'.$arr[$i]['city']]+=$arr[$i]['cCertifiedMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['count'.$arr[$i]['city']]++;
				$data[$arr[$i]['year']][$arr[$i]['month']]['feedbackmoney'.$arr[$i]['city']] += $cCaseFeedBackMoney;


				$total['totalMoney'.$arr[$i]['city']]+=$arr[$i]['cTotalMoney'];
				$total['certifiedMoney'.$arr[$i]['city']]+=$arr[$i]['cCertifiedMoney'] ;
				$total['count'.$arr[$i]['city']]++ ;
				$total['feedbackmoney'.$arr[$i]['city']] += $cCaseFeedBackMoney;

				//地區+品牌
				$year[$arr[$i]['year']]['totalMoney'.$arr[$i]['city'].$bank]+=$arr[$i]['cTotalMoney'] ;
				$year[$arr[$i]['year']]['certifiedMoney'.$arr[$i]['city'].$bank]+=$arr[$i]['cCertifiedMoney'] ;
				$year[$arr[$i]['year']]['count'.$arr[$i]['city'].$bank]++;
				$year[$arr[$i]['year']]['feedbackmoney'.$arr[$i]['city'].$bank] += $cCaseFeedBackMoney;

				$data[$arr[$i]['year']][$arr[$i]['month']]['totalMoney'.$arr[$i]['city'].$bank]+=$arr[$i]['cTotalMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['certifiedMoney'.$arr[$i]['city'].$bank]+=$arr[$i]['cCertifiedMoney'] ;
				$data[$arr[$i]['year']][$arr[$i]['month']]['count'.$arr[$i]['city'].$bank]++;
				$data[$arr[$i]['year']][$arr[$i]['month']]['feedbackmoney'.$arr[$i]['city'].$bank] += $cCaseFeedBackMoney;

				$total['totalMoney'.$arr[$i]['city'].$bank]+=$arr[$i]['cTotalMoney'];
				$total['certifiedMoney'.$arr[$i]['city'].$bank]+=$arr[$i]['cCertifiedMoney'] ;
				$total['count'.$arr[$i]['city'].$bank]++ ;
				$total['feedbackmoney'.$arr[$i]['city'].$bank] += $cCaseFeedBackMoney;
				##
			}
		}
	}



	if ($ShowCat == 'area') {
		include_once 'caseAnalysisExcelArea.php';
	}else{
		include_once 'caseAnalysisExcel.php';
	}
	
}




$smarty->display('caseAnalysis.inc.tpl', '', 'report2');

?>