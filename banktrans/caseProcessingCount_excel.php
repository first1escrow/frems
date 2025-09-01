<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
// include_once '../web_addr.php' ;
// include_once '../session_check.php' ;
##
$_POST = escapeStr($_POST) ;

$date_start = $_POST['s_year']."-".$_POST['s_month']."-01";
$date_end = $_POST['e_year']."-".$_POST['e_month']."-31";

// $date_start = '105-11-01';
// $date_end = '105-12-20';



if ($_POST['s_month'] == $_POST['e_month']) {
	$title = $_POST['s_year']." / ".$_POST['s_month']."月份";
	$month_count = 1;
}else{
	$title = $_POST['s_year']."年".$_POST['s_month']."月份~".$_POST['e_year']."年".$_POST['e_month']."月份";
	// 
	// $_POST['e_year'] = 106;
	// $_POST['e_month'] = 3;
	if ($_POST['e_year'] > $_POST['s_year']) {
		$month_count = (($_POST['e_year']-$_POST['s_year']) * 12) - $_POST['s_month'];
		
		$month_count = $month_count +$_POST['e_month']+1; //算到月底要加1
		// echo $month_count;
	}else{
		$month_count = ($_POST['e_month']-$_POST['s_month'])+1;//算到月底要加1
	}

}


##查詢字串
if (!empty($date_start)) {
	
	$date_start = date_change($date_start)."";

	$query_date = " bt.tBankLoansDate >='".$date_start."'";
	
	$query_date2 = "cc.cBankList >= '".$date_start."'";
	
}

if (!empty($date_end)) {

	$date_end = date_change($date_end)."";
	$query_date .= " AND bt.tBankLoansDate <= '".$date_end."'";
	$query_date2 .= " AND cc.cBankList <= '".$date_end."'";
	
}


	$sql  = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(5,6) AND pBankTrans IN(1,2) AND pId!=6 ORDER BY pId ASC ";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['pId']; //選項
		$data_People[]=$rs->fields; //被選取的

		$rs->MoveNext();
	}

	$str = implode(',',$tmp) ;
	
	$query = " AND s.sUndertaker1 IN (".$str.")";

	unset($tmp);


##

##
//以各保號的承辦人計算和媒體檔‧SP:保號000000000(永豐)000000008(台新)利息出款
//s.sUndertaker1、name:地政士的經辦 ； OwnerId、bt.tOwner:出款的人
$sql = "
		SELECT 
			bt.tMemo,
			s.sUndertaker1,
			(SELECT pName FROM tPeopleInfo WHERE pId=s.sUndertaker1) as name,
			(SELECT pCategory_stime FROM tPeopleInfo WHERE pName=bt.tOwner) as cat_stime,
			(SELECT pCategory_etime FROM tPeopleInfo WHERE pName=bt.tOwner) as cat_etime,
			(SELECT pId FROM tPeopleInfo WHERE pName=bt.tOwner) as OwnerId,
			bt.tOwner,
			bt.tBankLoansDate
		FROM 
		 	tBankTrans AS bt
		LEFT JOIN 
			tContractScrivener AS cs ON cs.cCertifiedId=bt.tMemo		
		LEFT JOIN 
			tScrivener AS s ON cs.cScrivener=s.sId
		WHERE
			bt.tExport='1' AND ".$query_date.$query."  OR (tMemo IN ('000000000','000000008') AND ".$query_date.") 
		
		ORDER BY tBankLoansDate ASC";
		
		// echo $sql;
// echo $sql."<br>";
$rs = $conn->Execute($sql);
// $total=$rs->RecordCount();//計算總筆數
while (!$rs->EOF) {

	##利息出款
	if ($rs->fields['tMemo']=='000000000' || $rs->fields['tMemo']=='000000008') {//如果是利息出款則取建檔者
		
		$rs->fields['sUndertaker1']=$rs->fields['OwnerId'];
		$rs->fields['name']=$rs->fields['tOwner'];
	}
	##
	
	// $list = $rs->fields;
	$list[$rs->fields['sUndertaker1']]['name'] = $rs->fields['name']; //姓名
	$list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['count'] = $list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['count']+1; //單日出款數
	$list[$rs->fields['sUndertaker1']]['total'] = $list[$rs->fields['sUndertaker1']]['total']+1; //總出款數

	##代理出款筆數(幫別人出款)

	if ($rs->fields['sUndertaker1'] != $rs->fields['OwnerId']  && $rs->fields['pTest'] != 1) {//($rs->fields['tBankLoansDate'] >= $rs->fields['cat_etime'] && $rs->fields['cat_etime'] !='0000-00-00') || $rs->fields['cat_etime']=='0000-00-00' && ($rs->fields['tOwner'] !=33 ||$rs->fields['tOwner'] !=32)

		if (strtotime($rs->fields['tBankLoansDate']) >= strtotime($rs->fields['cat_stime']) && strtotime($rs->fields['tBankLoansDate']) <= strtotime($rs->fields['cat_etime'])) {
			
		}else{
			$list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['unextra'] = $list[$rs->fields['sUndertaker1']][$rs->fields['tBankLoansDate']]['unextra']+1;//幫別人出的單日出款數

			$list[$rs->fields['OwnerId']][$rs->fields['tBankLoansDate']]['extra'] = $list[$rs->fields['OwnerId']][$rs->fields['tBankLoansDate']]['extra']+1;//被別人出的單日出款數


			$list[$rs->fields['sUndertaker1']]['unextra'] = $list[$rs->fields['sUndertaker1']]['unextra']+1;//幫別人出的總出款數

			$list[$rs->fields['OwnerId']]['extra'] = $list[$rs->fields['OwnerId']]['extra']+1;//被別人出的總出款數
		}
	}

	

	$rs->MoveNext();
}
##
$sql = "SELECT * FROM tPropleBanktransError WHERE pDate >='".$date_start."' AND pDate <='".$date_end."'";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$data[$rs->fields['pMid']]['pBasic'] += $rs->fields['pBasic'];
	$data[$rs->fields['pMid']]['pBanktran'] += $rs->fields['pBanktran'];
	$data[$rs->fields['pMid']]['pMoney'] += $rs->fields['pMoney'];
	$data[$rs->fields['pMid']]['pBankBranch'] += $rs->fields['pBankBranch'];
	$data[$rs->fields['pMid']]['pTxt'] += $rs->fields['pTxt'];
	$data[$rs->fields['pMid']]['pAccount'] += $rs->fields['pAccount'];
	$data[$rs->fields['pMid']]['pAccountName'] += $rs->fields['pAccountName'];
	$data[$rs->fields['pMid']]['pOther'] += $rs->fields['pOther'];
	$data[$rs->fields['pMid']]['pEnd'] += $rs->fields['pEnd'];
	$data[$rs->fields['pMid']]['pEndTotal']+= $rs->fields['pEndTotal'];

	if ($rs->fields['pBasicMsg'] != '') { $data[$rs->fields['pMid']]['pBasicMsg'][]= $rs->fields['pBasicMsg'];}
	
	if ($rs->fields['pBanktranMsg'] != '') { $data[$rs->fields['pMid']]['pBanktranMsg'][]= $rs->fields['pBanktranMsg'];}
	
	if ($rs->fields['pMoneyMsg'] != '') { $data[$rs->fields['pMid']]['pMoneyMsg'][]= $rs->fields['pMoneyMsg'];}

	if ($rs->fields['pBankBranchMsg'] != '') { $data[$rs->fields['pMid']]['pBankBranchMsg'][]= $rs->fields['pBankBranchMsg'];}
		
	if ($rs->fields['pTxtMsg'] != '') { $data[$rs->fields['pMid']]['pTxtMsg'][]= $rs->fields['pTxtMsg'];}
		
	if ($rs->fields['pAccountMsg'] != '') { $data[$rs->fields['pMid']]['pAccountMsg'][]= $rs->fields['pAccountMsg'];}
		
	if ($rs->fields['pAccountNameMsg'] != '') { $data[$rs->fields['pMid']]['pAccountNameMsg'][]= $rs->fields['pAccountNameMsg'];}

	if ($rs->fields['pOtherMsg'] != '') { $data[$rs->fields['pMid']]['pOtherMsg'][]= $rs->fields['pOtherMsg'];}
	
	if ($rs->fields['pEndMsg'] != '') { $data[$rs->fields['pMid']]['pEndMsg'][]= $rs->fields['pEndMsg'];}

	if ($rs->fields['pSp_msg'] != '') { $data[$rs->fields['pMid']]['pSp_msg'][]= $rs->fields['pSp_msg'];}
	
	

	$rs->MoveNext();
}
###
######結案數量統計表######
$sql = "SELECT * FROM tPropleCaseEndError WHERE pDate >='".$date_start."' AND pDate <='".$date_end."'";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$data2[$rs->fields['pMid']]['pInfo'] += $rs->fields['pInfo'];
	$data2[$rs->fields['pMid']]['pCMoney'] += $rs->fields['pCMoney'];
	$data2[$rs->fields['pMid']]['pInt'] += $rs->fields['pInt'];
	$data2[$rs->fields['pMid']]['pInv'] += $rs->fields['pInv'];

	if ($rs->fields['pInfoCertifiedId'] != '') { $data2[$rs->fields['pMid']]['pInfoCertifiedId'][]= $rs->fields['pInfoCertifiedId'];}
	if ($rs->fields['pInfoMsg'] != '') { $data2[$rs->fields['pMid']]['pInfoMsg'][]= $rs->fields['pInfoMsg'];}
	
	if ($rs->fields['pCMoneyCertifiedId'] != '') { $data2[$rs->fields['pMid']]['pCMoneyCertifiedId'][]= $rs->fields['pCMoneyCertifiedId'];}
	if ($rs->fields['pCMoneyMsg'] != '') { $data2[$rs->fields['pMid']]['pCMoneyMsg'][]= $rs->fields['pCMoneyMsg'];}

	if ($rs->fields['pIntCertifiedId'] != '') { $data2[$rs->fields['pMid']]['pIntCertifiedId'][]= $rs->fields['pIntCertifiedId'];}		
	if ($rs->fields['pIntMsg'] != '') { $data2[$rs->fields['pMid']]['pIntMsg'][]= $rs->fields['pIntMsg'];}
		
	if ($rs->fields['pInvCertifiedId'] != '') { $data2[$rs->fields['pMid']]['pInvCertifiedId'][]= $rs->fields['pInvCertifiedId'];}		
	if ($rs->fields['pInvMsg'] != '') { $data2[$rs->fields['pMid']]['pInvMsg'][]= $rs->fields['pInvMsg'];}


	$rs->MoveNext();
}
// echo "<pre>";
// print_r($data2);
// echo "</pre>";
// die;
//履保費出款日期+最後修改者
//日期條件以履保費出款日期為主
//以會計點交表為準

//先是否有資料(因105年11月前的最後修改者正確性低，所以直接拿計算好的數量用)
$sql = "SELECT * FROM tPropleCaseEnd WHERE pDate >='".$date_start."' AND pDate <='".$date_end."'";
$rs = $conn->Execute($sql);
$total=$rs->RecordCount();

// if ($total > 0) { 
	while (!$rs->EOF) {
		$list2[$rs->fields['pMid']] = $list2[$rs->fields['pMid']]+$rs->fields['pCount'];

		$rs->MoveNext();
	}
// }else{
	$sql = 'SELECT * FROM tContractBank WHERE cShow=1;' ;
	$rs = $conn->Execute($sql);


	while (!$rs->EOF) {
		//活儲帳號
		$bank_acc[] = '"'.$rs->fields['cBankAccount'].'"' ;

		//銀行代號
		$cBankCode[] = '"'.$rs->fields['cBankCode'].'"' ;
		$rs->MoveNext();
	}

	##
	
	##
		
	unset($tmp) ;

	$sql = '
		SELECT 
			bt.tMemo,
			cc.cCertifiedId,
			cc.cCaseEndLastEditor ,
			(SELECT pName FROM tPeopleInfo WHERE pId=cc.cCaseEndLastEditor) as name,
			bt.tBankLoansDate AS tDate,
			bt.tMoney,
			bt.tVR_Code as VR_Code
		FROM 
			tBankTrans AS bt 
		JOIN 
			tContractCase AS cc ON cc.cCertifiedId=bt.tMemo
		WHERE 
			bt.tExport="1"
			AND cc.cCaseEndLastEditor != 0
			AND bt.tPayOk="1"
			AND bt.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
			AND ((bt.tObjKind IN ("點交(結案)","解除契約","建經發函終止")) OR (bt.tObjKind = "其他" AND tKind="保證費"))
			AND bt.tAccount IN ('.implode(',', $bank_acc).')
			AND ('.$query_date.') 
		 
		ORDER BY
			bt.tExport_time,bt.tMemo 
		ASC ;
	' ;
	// echo $sql;
	// die;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {

		$tmpC[] = $rs->fields;
		
		$rs->MoveNext();
	}
	
	$sql = '
		SELECT
			cc.cBankList AS tDate,
			cc.cCaseEndLastEditor,
			cc.cCertifiedId,
			cc.cEscrowBankAccount as VR_Code
		FROM
			tContractCase AS cc
		
		WHERE
			'.$query_date2.'
			AND cc.cBank IN ('.implode(',', $cBankCode).')
			AND cc.cBankList<>""
			AND cc.cCaseEndLastEditor != 0
		ORDER BY
			cc.cBankList,cc.cCertifiedId
		ASC ;
	' ;
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$rs->fields['tMoney'] = 0;
		$tmpC[] = $rs->fields;
		$rs->MoveNext();
	}
	
	$tmp = explode('-', $date_start);
	$tmp2 = explode('-', $date_end);
	$d1 = ($tmp[0]-1911).$tmp[1].$tmp[2];
	$d2 = ($tmp2[0]-1911).$tmp2[1].$tmp2[2];
	unset($tmp);unset($tmp2);

	$sql = '
			SELECT 
				* 
			FROM 
				tExpense 
			WHERE 
				eTradeCode="178Y" 
				AND eExportCode="8888888"
				AND (ePayTitle LIKE "%退款回存%" OR ePayTitle LIKE "退匯存入")
				AND eTradeDate >= "'.$d1.'" AND eTradeDate <= "'.$d2.'"
			;' ;
	// echo $sql."\r\n";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$tmpC2[] = $rs->fields;

		$rs->MoveNext();
	}


	$t = count($tmpC);
	$tmpArr = array();

	for ($j=0; $j < $t; $j++) { 

		$tMoney = str_pad($tmpC[$j]['tMoney'],13,'0',STR_PAD_LEFT).'00' ;
		$VR_Code = "00".$tmpC[$j]['VR_Code'] ;
		

		if (is_array($tmpC2)) { //有退款回存
			for ($i=0; $i < count($tmpC2); $i++) { 
				if ($tmpC2[$i]['eDepAccount'] == $VR_Code && $tmpC2[$i]['eLender'] == $tMoney) {
					$get[] = $tmpC[$j];
					// print_r($tmpC);
					unset($tmpC[$j]);
				}else{
					

					if (!in_array($tmpC[$j]['cCertifiedId'], $tmpArr)) {
						if ($tmpC[$j]['cCaseEndLastEditor'] != 0) {
							$list2[$tmpC[$j]['cCaseEndLastEditor']]++;
							$list3[$tmpC[$j]['cCaseEndLastEditor']]++;
							$tmpArr[] =$tmpC[$j]['cCertifiedId']; //比對重複用
						}
						
						
						
					}
							
				}
				unset($tMoney);
			}
		}else{
			if (!in_array($tmpC[$j]['cCertifiedId'], $tmpArr)) {
				if ($tmpC[$j]['cCaseEndLastEditor'] != 0) {
					$list2[$tmpC[$j]['cCaseEndLastEditor']]++;
					$list3[$tmpC[$j]['cCaseEndLastEditor']]++;
					$tmpArr[] = $tmpC[$j]['cCertifiedId'];//比對重複用
				}
				// $list2[$tmpC[$j]['cCaseEndLastEditor']]++;
				
				
			}
		}
		

		

	}
// }





// echo '******';
// echo "<pre>";
// print_r($list3);
// echo "</pre>";
// echo $sql;
// die;


// for ($i=0; $i < count($tmpC); $i++) { 

// 	$tmp = explode('-',$tmpC[$i]['tDate']) ;
// 	$tDate = ($tmp[0] - 1911).$tmp[1].$tmp[2] ;
// 	$tMoney = str_pad($tmpC[$i]['tMoney'],13,'0',STR_PAD_LEFT).'00' ;
	
// 	$sql = '
// 		SELECT 
// 			* 
// 		FROM 
// 			tExpense 
// 		WHERE 
// 			eTradeCode="178Y" 
// 			AND eExportCode="8888888" 
// 			AND eDepAccount="00'.$tmpC[$i]['VR_Code'].'" 
// 			AND (ePayTitle LIKE "%退款回存%" OR ePayTitle LIKE "退匯存入")
// 			AND eTradeDate="'.$tDate.'"
// 			AND eLender="'.$tMoney.'"
// 		;' ;
// 	$rs = $conn->Execute($sql);
// 	$fg = 0 ; 
// 	while (!$rs->EOF) {
// 		$tmpArr[$tmpC[$i]['tCertifiedId']] ++ ;
// 		if ($tmpArr[$tmpC[$i]['tCertifiedId']] > 1) {
// 			$detail[$k] = $tmpC[$i] ; //最後
// 			$k ++ ;
// 		}
// 		$fg ++ ;



// 		$rs->MoveNext();
// 	}

// 	if (!$fg) {
// 		$detail[$k] = $tmpC[$i] ;
// 		$k ++ ;
// 	}
	
// 	unset($tmp);unset($tmpArr);
// }


##

// echo "<pre>";
// print_r($list2);
// echo "</pre>";

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("人員出款錯誤紀錄表");
$objPHPExcel->getProperties()->setDescription("第一建經人員出款錯誤紀錄表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('人員出款錯誤紀錄表');
$objPHPExcel->getActiveSheet()->getStyle("A1:Z24")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

//寫入表頭資料
$objPHPExcel->getActiveSheet()->mergeCells("A1:Z3");

$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1',"人員出款錯誤紀錄表\r\n".$title);

$col = 65;
$row = 5;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'基本檔(0.5)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'出錯款(GG)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'金額(1)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'分行(1)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'附言(1)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'帳號(1)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'戶名(1)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'其他(0)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'結案(0.5)');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'錯誤筆數合計');
$row++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'本家出款筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'(加權1.5)代理出款筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'被代理出款筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'出款筆數小計');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'結案筆數小計');
$row++;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'加權後總筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'錯誤率');

$col = 66;
$row = 4;
$objPHPExcel->getActiveSheet()->setCellValue('Z4','錯誤筆數小計/種類');
// $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);

$objPHPExcel->getActiveSheet()->getStyle("B1:B24")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('E1:E24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('H1:H24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('K1:K24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('N1:N24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('Q1:Q24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('T1:T24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('W1:W24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('Z1:Z24')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle('A13:Z13')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB('F8EDEB'); 

// 
for ($i=0; $i < count($data_People); $i++) {
	$row = 4;

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB('E4BEB1'); 
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data_People[$i]['pName']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pBasic']);

	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pBanktran']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pMoney']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pBankBranch']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pTxt']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pAccount']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pAccountName']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pOther']);
	$row++;


	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pEnd']);
	$row++;
	//錯誤筆數合計
	$total = $data[$data_People[$i]['pId']]['pBasic'] + $data[$data_People[$i]['pId']]['pBanktran']+$data[$data_People[$i]['pId']]['pMoney']+$data[$data_People[$i]['pId']]['pBankBranch']+$data[$data_People[$i]['pId']]['pTxt']+$data[$data_People[$i]['pId']]['pAccount']+$data[$data_People[$i]['pId']]['pAccountName']+$data[$data_People[$i]['pId']]['pOther']+$data[$data_People[$i]['pId']]['pEnd'];
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$total);
	$row++;

	##出款筆數##
	$row = 16;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,(int)$list[$data_People[$i]['pId']]['total']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,(int)$list[$data_People[$i]['pId']]['extra']);
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,(int)$list[$data_People[$i]['pId']]['unextra']);
	$row++;
	//出款筆數小計
	$real_total = $list[$data_People[$i]['pId']]['total'] + $list[$data_People[$i]['pId']]['extra'] - $list[$data_People[$i]['pId']]['unextra'];
	
	$list[$data_People[$i]['pId']]['total_plus'] +=$real_total;//在round前做，會比較正確
	$list[$data_People[$i]['pId']]['extra_plus'] +=$list[$data_People[$i]['pId']]['extra']*1.5;//	

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$real_total);
	$row++;
	//結案筆數小計
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,(int)$list[$data_People[$i]['pId']]['pEndTotal']);
	$row++;

	
	//加權後總筆數(本家+(代理*1.5)-被代理+結案小計數)
	$row = 22;
	$real_total2 = $list[$data_People[$i]['pId']]['total'] + ($list[$data_People[$i]['pId']]['extra']*1.5) - $list[$data_People[$i]['pId']]['unextra']+$data[$data_People[$i]['pId']]['pEndTotal'];

	$real_total2 = round($real_total2);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$real_total2);

	$row++;
	//錯誤率
	$total2 = ($data[$data_People[$i]['pId']]['pBasic']*0.5) + $data[$data_People[$i]['pId']]['pMoney']+$data[$data_People[$i]['pId']]['pBankBranch']+$data[$data_People[$i]['pId']]['pTxt']+$data[$data_People[$i]['pId']]['pAccount']+$data[$data_People[$i]['pId']]['pAccountName']+($data[$data_People[$i]['pId']]['pOther']*0)+($data[$data_People[$i]['pId']]['pEnd'] *0.5);
	if ($real_total2 != 0) {
		$error = round($total2/$real_total2,4)*100;
	}else{
		$error = 0;
	}
	$error_part[$data_People[$i]['pId']]['total'] = $error;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$error."%");
	$row++;

	$col++;
	$col2 = $col+1;
	$row = 4;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'錯誤說明');
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pBasicMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pBanktranMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pMoneyMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pBankBranchMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pTxtMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pAccountMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pAccountNameMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(';',$data[$data_People[$i]['pId']]['pOtherMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data[$data_People[$i]['pId']]['pEndMsg']);
	$row++;
	//14  16-23  mergeCell
	// echo $row;
	// die;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row); //14
	$row++;$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//16
	$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//17
	$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//18
	$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//19
	$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//20
	$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//21
	$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//22
	$row++;
	$objPHPExcel->getActiveSheet()->mergeCells(chr($col).$row.":".chr($col2).$row);//23
	$col++;$col++;


	//特殊錯誤訊息
	if ($data[$data_People[$i]['pId']]['pSp_msg'] != '') {
		$msg[] = implode("\r\n", $data[$data_People[$i]['pId']]['pSp_msg']);
	}
	//錯誤比數小計/種類
	$error_count['pBasic'] += $data[$data_People[$i]['pId']]['pBasic'];
	$error_count['pBanktran'] += $data[$data_People[$i]['pId']]['pBanktran'];
	$error_count['pMoney'] += $data[$data_People[$i]['pId']]['pMoney'];
	$error_count['pBankBranch'] += $data[$data_People[$i]['pId']]['pBankBranch'];
	$error_count['pTxt'] += $data[$data_People[$i]['pId']]['pTxt'];
	$error_count['pAccount'] += $data[$data_People[$i]['pId']]['pAccount'];
	$error_count['pAccountName'] += $data[$data_People[$i]['pId']]['pAccountName']; 
	$error_count['pOther'] += $data[$data_People[$i]['pId']]['pOther'];
	$error_count['pEnd'] += $data[$data_People[$i]['pId']]['pEnd'];
	$error_count['total'] += $total; //錯誤筆數合計加總
	
	$error_count2['total'] += $list[$data_People[$i]['pId']]['total'];//本家
	$error_count2['extra'] += $list[$data_People[$i]['pId']]['extra'];//代理
	$error_count2['unextra'] += $list[$data_People[$i]['pId']]['unextra'];//被代理
	$error_count2['real_total'] += $real_total;//出款筆數小計

	$error_count2['pEndTotal'] += $data[$data_People[$i]['pId']]['pEndTotal'];

	
	unset($total);unset($real_total);unset($error);unset($total2);unset($real_total2);
	//$data
}

//特殊錯誤訊息
$col = 65;
$row = 40;
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":R".$row);
$objPHPExcel->getActiveSheet()->getStyle("A".$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
$objPHPExcel->getActiveSheet()->getStyle("A".$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(100);

$tmp_msg = @implode("\r\n", $msg);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB('F8EDEB'); 
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$tmp_msg);
$row++;


##錯誤比數小計/種類

//錯誤數量
$col = 90;//Z
$row = 5;
if (is_array($error_count)) {
	foreach ($error_count as $k => $v) {
		// if ($v== 0) {$v = "0";}
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,(int)$v);
		$row++;
	}
}
$objPHPExcel->getActiveSheet()->mergeCells("A15:Z15");
$objPHPExcel->getActiveSheet()->getStyle('A15:Z15')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A15:Z15')->getFill()->getStartColor()->setARGB('F8EDEB'); 

//出款數量
$row = 16;
if (is_array($error_count2)) {
	foreach ($error_count2 as $k => $v) {
		// if ($v== 0) {$v = "0";}
		
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,(int)$v);
		$row++;
	}
}

$objPHPExcel->getActiveSheet()->getStyle('A21:Z21')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A21:Z21')->getFill()->getStartColor()->setARGB('F8EDEB'); 
//加權後總筆數
$row = 22;
$real_total2 = $error_count2['total'] + ($error_count2['extra']*1.5) - $error_count2['unextra']+$error_count2['pEnd'];
$real_total2 = round($real_total2);
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$real_total2);
$row++;
//錯誤率

$error_total = ($error_count['pBasic']*0.5) + $error_count['pMoney']+$error_count['pBankBranch']+$error_count['pTxt']+$error_count['pAccount']+$error_count['pAccountName']+($error_count['pOther']*0)+($error_count['pEnd'] *0.5);



$error_total = round($error_total);
if ($real_total2 != 0) {
	$error = round(($error_total/$real_total2),4)*100;
}else{
	$error = 0;
}


$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$error."%");	

$objPHPExcel->getActiveSheet()->mergeCells("A24:Z24");
$objPHPExcel->getActiveSheet()->getStyle('A24:Z24')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A24:Z24')->getFill()->getStartColor()->setARGB('F8EDEB'); 
unset($error_count);unset($error_count2);unset($error);
########################################結案錯誤率##########################################################3
//寫入表頭資料
$objPHPExcel->getActiveSheet()->mergeCells("A26:Z28");
$objPHPExcel->getActiveSheet()->getStyle("A26:Z37")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


$objPHPExcel->getActiveSheet()->getStyle('A26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A26')->getFont()->setSize(15);
$objPHPExcel->getActiveSheet()->getStyle('A26')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A26')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A26',"結案錯誤紀錄表\r\n".$title);

$objPHPExcel->getActiveSheet()->getStyle('B37:Z37')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


$row = 30;
$col = 65 ;
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'買賣方資訊不完整');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'履保費分配有異');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'利息扣憑分配有異');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'紙本發票應勾選而未勾選');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'錯誤筆數合計');
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":Z".$row);
$objPHPExcel->getActiveSheet()->getStyle("A".$row.":Z".$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A".$row.":Z".$row)->getFill()->getStartColor()->setARGB('F8EDEB'); 
$row++;//34
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'結案筆數');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col).($row++),'錯誤率');
$objPHPExcel->getActiveSheet()->mergeCells("A".$row.":Z".$row);
$objPHPExcel->getActiveSheet()->getStyle("A".$row.":Z".$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A".$row.":Z".$row)->getFill()->getStartColor()->setARGB('F8EDEB'); 
$row++;


$col = 66 ;
for ($i=0; $i < count($data_People); $i++) { 
	$row = 29;
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB('E4BEB1'); 
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data_People[$i]['pName']);
	$row++;
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data2[$data_People[$i]['pId']]['pInfo']);
	$row++;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data2[$data_People[$i]['pId']]['pCMoney']);
	$row++;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data2[$data_People[$i]['pId']]['pInt']);
	$row++;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$data2[$data_People[$i]['pId']]['pInv']);
	$row++;
	//錯誤筆數
	$total = $data2[$data_People[$i]['pId']]['pInfo']+$data2[$data_People[$i]['pId']]['pCMoney']+$data2[$data_People[$i]['pId']]['pInt']+$data2[$data_People[$i]['pId']]['pInv'];
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$total);
	$row++;$row++;
	//結案筆數
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$list2[$data_People[$i]['pId']]);
	$row++;
	//錯誤率(錯誤筆數/結案筆數)
	if ($list2[$data_People[$i]['pId']] != '') {
		$error = round($total/$list2[$data_People[$i]['pId']],4)*100;
	}else{
		$error = 0;
	}
	$error_total2[$data_People[$i]['pId']]['total'] = $error;
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$error."%");
	
	$row = 29;
	$col++;
	//履保號碼
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'履保號碼');
	$row++;
	
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode("\r\n", $data2[$data_People[$i]['pId']]['pInfoCertifiedId']));
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col).$row, @implode(";", $data2[$data_People[$i]['pId']]['pInfoCertifiedId']),PHPExcel_Cell_DataType::TYPE_STRING); 
	
	$row++;

	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode("\r\n", $data2[$data_People[$i]['pId']]['pCMoneyCertifiedId']));
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col).$row, @implode(";", $data2[$data_People[$i]['pId']]['pCMoneyCertifiedId']),PHPExcel_Cell_DataType::TYPE_STRING); 
	
	$row++;

	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode("\r\n", $data2[$data_People[$i]['pId']]['pIntCertifiedId']));
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col).$row, @implode(";", $data2[$data_People[$i]['pId']]['pIntCertifiedId']),PHPExcel_Cell_DataType::TYPE_STRING); 
	
	$row++;
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col).$row, @implode(";", $data2[$data_People[$i]['pId']]['pInvCertifiedId']),PHPExcel_Cell_DataType::TYPE_STRING); 
	
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode("\r\n", $data2[$data_People[$i]['pId']]['pInvCertifiedId']));
	$row++;

	$row = 29;
	$col++;
	//錯誤說明
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,'錯誤說明');
	$row++;
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(";", $data2[$data_People[$i]['pId']]['pInfoMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(";", $data2[$data_People[$i]['pId']]['pCMoneyMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(";", $data2[$data_People[$i]['pId']]['pIntMsg']));
	$row++;

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,@implode(";", $data2[$data_People[$i]['pId']]['pInvMsg']));
	$row = 29;
	$col++;
	
	//錯誤比數小計/種類
	$error_count['pInfo'] += $data2[$data_People[$i]['pId']]['pInfo'];
	$error_count['pCMoney'] += $data2[$data_People[$i]['pId']]['pCMoney'];
	$error_count['pInt'] += $data2[$data_People[$i]['pId']]['pInt'];
	$error_count['pInv'] += $data2[$data_People[$i]['pId']]['pInv'];
	$error_count['total'] += $total;

	
	$error_count2['total'] += $list2[$data_People[$i]['pId']];//結案筆數

	
	unset($total);unset($error);
}
$row = 29;

//錯誤筆數小計/種類
$objPHPExcel->getActiveSheet()->setCellValue('Z'.$row,'錯誤筆數小計/種類');
$row++;
$col = 90;//

if (is_array($error_count)) {
	foreach ($error_count as $k => $v) {
		// if ($v== 0) {$v = "0";}
		$objPHPExcel->getActiveSheet()->setCellValue('Z'.$row,$v);
		$row++;
	}
}
$row++;

if (is_array($error_count2)) {
	foreach ($error_count2 as $k => $v) {
		// if ($v== 0) {$v = "0";}
		$objPHPExcel->getActiveSheet()->setCellValue('Z'.$row,$v);
		$row++;
	}
}
//錯誤率
if ($error_count2['total']) {
	$tmp = round(($error_count['total']/$error_count2['total']),4)*100 ;
	
}

$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$tmp."%");
unset($tmp);
#############################################################################################################
//季
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(1) ;
$objPHPExcel->getActiveSheet()->setTitle('出款筆數統計');

//寫入表頭資料
$objPHPExcel->getActiveSheet()->getStyle("A1:E10")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

$objPHPExcel->getActiveSheet()->mergeCells("A1:E1");
$objPHPExcel->getActiveSheet()->getStyle("B1:B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle("D1:D10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1',$title);
#FFD382
$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFill()->getStartColor()->setARGB('FFFF3B'); 
$objPHPExcel->getActiveSheet()->setCellValue('B2','月平均錯誤率');
$objPHPExcel->getActiveSheet()->setCellValue('C2','出款總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('D2','代理總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('E2','總錯誤率');

$row = 3;
for ($i=0; $i < count($data_People); $i++) {
	$col = 65;

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB('E4BEB1'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data_People[$i]['pName']);

	$real_total2 = $list[$data_People[$i]['pId']]['total'] + ($list[$data_People[$i]['pId']]['extra']*1.5) - $list[$data_People[$i]['pId']]['unextra']+$data[$data_People[$i]['pId']]['pEndTotal'];
	$real_total2 = round($real_total2);
	$total2 = ($data[$data_People[$i]['pId']]['pBasic']*0.5) + $data[$data_People[$i]['pId']]['pMoney']+$data[$data_People[$i]['pId']]['pBankBranch']+$data[$data_People[$i]['pId']]['pTxt']+$data[$data_People[$i]['pId']]['pAccount']+$data[$data_People[$i]['pId']]['pAccountName']+($data[$data_People[$i]['pId']]['pOther']*0)+($data[$data_People[$i]['pId']]['pEnd'] *0.5);
	$total2 = round($total2);
	if ($real_total2 != 0) {
		$error = round((($total2/$real_total2)/$month_count),4)*100;
	}else{
		$error = 0;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$error."%");
	$list[$data_People[$i]['pId']]['extra_plus'] = round($list[$data_People[$i]['pId']]['extra_plus']);
	$list[$data_People[$i]['pId']]['total_plus'] = round($list[$data_People[$i]['pId']]['total_plus']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,(int)$list[$data_People[$i]['pId']]['total_plus']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,(int)$list[$data_People[$i]['pId']]['extra_plus']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$error_part[$data_People[$i]['pId']]['total'].'%');
	//$error_part
	$row++;
}

###################################################################################################3
//結案錯誤率
$objPHPExcel->createSheet() ;
$objPHPExcel->setActiveSheetIndex(2) ;
$objPHPExcel->getActiveSheet()->setTitle('結案錯誤率統計');
//寫入表頭資料
$objPHPExcel->getActiveSheet()->getStyle("A1:D10")->getBorders()->getAllborders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

$objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
$objPHPExcel->getActiveSheet()->getStyle("B1:B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle("D1:D10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('A1',$title);
#FFD382
$objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getFill()->getStartColor()->setARGB('FFFF3B'); 
$objPHPExcel->getActiveSheet()->setCellValue('B2','月平均錯誤率');
$objPHPExcel->getActiveSheet()->setCellValue('C2','結案總筆數');
$objPHPExcel->getActiveSheet()->setCellValue('D2','總錯誤率');

$row = 3;
for ($i=0; $i < count($data_People); $i++) {
	$col = 65;

	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFill()->getStartColor()->setARGB('E4BEB1'); 
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data_People[$i]['pName']);

	$total = $data2[$data_People[$i]['pId']]['pInfo']+$data2[$data_People[$i]['pId']]['pCMoney']+$data2[$data_People[$i]['pId']]['pInt']+$data2[$data_People[$i]['pId']]['pInv'];
	// $list2[$data_People[$i]['pId']]
	// echo $total."/".$list2[$data_People[$i]['pId']]."/".$month_count."<bR>";
	if ($list2[$data_People[$i]['pId']] != '') {
		$error = round((($total/$list2[$data_People[$i]['pId']])/$month_count),4)*100;
	}
	
	// echo $error."<bR>";

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$error."%");
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$list2[$data_People[$i]['pId']]);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$error_total2[$data_People[$i]['pId']]['total']."%");

	//$error_total2
	$row++;
}

// die;
######################################################################################3
$objPHPExcel->setActiveSheetIndex(0) ;
// die();

$_file = 'banktrans.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit ;

function date_change($str)
{
	$tmp = explode('-', $str);

	$tmp[0] = 1911+$tmp[0];

	$str = $tmp[0].'-'.$tmp[1].'-'.$tmp[2];

	return $str;
}
?>