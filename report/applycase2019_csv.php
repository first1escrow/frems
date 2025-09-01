<?php

include_once '../web_addr.php' ;
include_once '../session_check.php' ;

// echo 'AA';
##
// $fh = fopen('r'.date("Ymd").'.csv','w') ;
$str = "案件總筆數,買賣總價金額,合約保證費金額,回饋金額,收入\r\n";
// fwrite($fh, $str);



$str .= str_replace(',', '',$caseAna[$realKey]['count']).",".str_replace(',', '', $caseAna[$realKey]['total']).",".str_replace(',', '', $caseAna[$realKey]['certifiedMoney']).",".str_replace(',', '', $caseAna[$realKey]['feedbackmoney']).",".str_replace(',', '', $caseAna[$realKey]['money'])."\r\n\r\n\r\n";
// fwrite($fh, $str);
$str .= '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,總回饋金,回饋金[符合查詢條件],案件狀態日期,進案日期,實際點交日期,銀行出款日期,地政士姓名,標的物座落,狀態,仲介業務,地政士業務,數量占比,回饋數量占比'."\r\n";
$str = iconv('utf-8','big5', $str);

for ($i = 0 ; $i < count($caseAna[$realKey]['data']) ; $i ++) {
	//取得實際出款日
		if ($caseAna[$realKey]['data'][$i]['tBankLoansDate'] != '') {
			$caseAna[$realKey]['data'][$i]['tBankLoansDate'] = dateCg($caseAna[$realKey]['data'][$i]['tBankLoansDate']) ;
		}else{
			if ($caseAna[$realKey]['data'][$i]['cBankList'] != '') {
				
				$caseAna[$realKey]['data'][$i]['tBankLoansDate'] = dateCg($caseAna[$realKey]['data'][$i]['cBankList']) ;
				unset($tmp_d) ;
			}
		}
	##
	
		//取得各仲介店姓名與編號
		$bStore = getRealtyName($caseAna[$realKey]['data'][$i]['branch']) ;
		$bNo = $caseAna[$realKey]['data'][$i]['bCode'];
		
		if ($caseAna[$realKey]['data'][$i]['branch1'] > 0) {
			$bStore .= ' '.getRealtyName($caseAna[$realKey]['data'][$i]['branch1']) ;
			$bNo .= ' '.$caseAna[$realKey]['data'][$i]['bCode1'] ;
		}
		
		if ($caseAna[$realKey]['data'][$i]['branch2'] > 0) {
			$bStore .= ' '.getRealtyName($caseAna[$realKey]['data'][$i]['branch2']) ;
			$bNo .= ' '.$caseAna[$realKey]['data'][$i]['bCode2'] ;
		}
		$caseAna[$realKey]['data'][$i]['bStore'] = $bStore ;
		$caseAna[$realKey]['data'][$i]['bId'] = $bNo ;

	unset($tmp);

	if ($caseAna[$realKey]['data'][$i]['cCaseStatus'] =='3') { //
		$date= $caseAna[$realKey]['data'][$i]['cEndDate'] ;
	}
	else {
		$date= $caseAna[$realKey]['data'][$i]['cSignDate'] ;
	}


	$zc = $caseAna[$realKey]['data'][$i]['zCity'] ;
	$caseAna[$realKey]['data'][$i]['cAddr'] = preg_replace("/$zc/","",$caseAna[$realKey]['data'][$i]['cAddr']) ;
	$zc = $caseAna[$realKey]['data'][$i]['zArea'] ;
	$caseAna[$realKey]['data'][$i]['cAddr'] = preg_replace("/$zc/","",$caseAna[$realKey]['data'][$i]['cAddr']) ;

	$caseAna[$realKey]['data'][$i]['cAddr'] = $caseAna[$realKey]['data'][$i]['zCity'].$caseAna[$realKey]['data'][$i]['zArea'].$caseAna[$realKey]['data'][$i]['cAddr'] ;

	$str .= ($i+1).',';
	$str .= $caseAna[$realKey]['data'][$i]['cCertifiedId'].'_,';
	$str .= $caseAna[$realKey]['data'][$i]['bId'].',';
	$str .= iconv('utf-8','big5',$caseAna[$realKey]['data'][$i]['bStore']).',';
	$str .= iconv('utf-8','big5',$caseAna[$realKey]['data'][$i]['owner']).',';
	$str .= iconv('utf-8','big5',$caseAna[$realKey]['data'][$i]['buyer']).',';
	$str .= $caseAna[$realKey]['data'][$i]['cTotalMoney'].',';
	$str .= $caseAna[$realKey]['data'][$i]['cCertifiedMoney'].',';
	$str .= $caseAna[$realKey]['data'][$i]['tBankLoansMoney'].',';
	$str .= $caseAna[$realKey]['data'][$i]['showcCaseFeedBackMoney'].',';
	$str .= $certifiedData[$caseAna[$realKey]['data'][$i]['cCertifiedId']][$realKey]['CaseFeedBackMoneyPart'].',';
	$str .= $date.',';
	$str .= $caseAna[$realKey]['data'][$i]['cApplyDate'].',';
	$str .= $caseAna[$realKey]['data'][$i]['cEndDate'].",";
	$str .= $caseAna[$realKey]['data'][$i]['tBankLoansDate'].",";
	$str .= iconv('utf-8','big5', $caseAna[$realKey]['data'][$i]['scrivener']).",";
	$str .= iconv('utf-8','big5',str_replace(',', '_', $caseAna[$realKey]['data'][$i]['cAddr'])).",";
	$str .= iconv('utf-8','big5', $caseAna[$realKey]['data'][$i]['status']).",";
	$str .= iconv('utf-8','big5', str_replace(',', '_', $caseAna[$realKey]['data'][$i]['salesName'])).",";
	$str .= iconv('utf-8','big5', str_replace(',', '_', $caseAna[$realKey]['data'][$i]['Scrsales'])).",";
	$str .= $certifiedData[$caseAna[$realKey]['data'][$i]['cCertifiedId']][$realKey]['part'].",";
	$str .= $certifiedData[$caseAna[$realKey]['data'][$i]['cCertifiedId']][$realKey]['part2']."\r\n";

// $str = iconv('utf-8','big5', $str);

	// fwrite($fh,$str) ;
}
// fclose($fh) ;



//CSV版	
header("Content-type: text/csv") ;
header("Content-Disposition: attachment; filename=ContractCase.csv") ;
header("Pragma: no-cache") ;
header("Expires: 0") ;

$fh = fopen("php://output","w") ;

fwrite($fh,$str) ;
fclose($fh) ;
exit ;

?>
