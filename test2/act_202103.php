<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
 header("Content-Type:text/html; charset=utf-8"); 
$_POST = escapeStr($_POST) ;
if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}

$sDate = ($_POST['sDate'])?$_POST['sDate']:'110-03-01';
$eDate = ($_POST['eDate'])?$_POST['eDate']:'110-12-31';
$cat = ($_POST['category'])?$_POST['category']:1;
$target = ($_POST['target'])?$_POST['target']:1;
$sales = $_POST['sales'];


$menuSales = array();
$menuSales[0] = '全部';
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep = 7 AND pJob =1";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuSales[$rs->fields['pId']] = $rs->fields['pName'];
	$rs->MoveNext();
}

if ($_POST['sDate']) {

	$storeData = array();
	$storeId = array();
	$storeCaseData = array();
	
	//

	if ($cat == 3) {
		$str = '';
	}else{
		$str = ' AND s.sCategory = '.$cat;
	}

	if ($sales != 0) {
		$col = "ss.sSales,";
		$tb = 'LEFT JOIN tScrivenerSales AS ss ON ss.sScrivener= s.sId';
		$str .= " AND ss.sSales = '".$sales."'";
	}


	$sql = "SELECT
				s.sId,
				s.sName AS name,
				s.sOffice AS store,
				s.sCategory as category,
				s.sAccountNum1 AS bank1,
				(SELECT bBank3_name FROM tBank WHERE bBank3=s.sAccountNum1 AND bBank4 = '') AS bankName1,
				s.sAccountNum11 AS bank2,
				(SELECT bBank3_name FROM tBank WHERE bBank3=s.sAccountNum11 AND bBank4 = '') AS bankName2,
				s.sAccountNum12 AS bank3,
				(SELECT bBank3_name FROM tBank WHERE bBank3=s.sAccountNum12 AND bBank4 = '') AS bankName3,
				s.sAccountNum2 AS bankBranch1,
				(SELECT bBank3_name FROM tBank WHERE bBank3=s.sAccountNum1 AND bBank4 = s.sAccountNum2) AS bankBranchName1,
				s.sAccountNum21 AS bankBranch2,
				(SELECT bBank3_name FROM tBank WHERE bBank3=s.sAccountNum1 AND bBank4 = s.sAccountNum21) AS bankBranchName2,
				s.sAccountNum22 AS bankBranch3,
				(SELECT bBank3_name FROM tBank WHERE bBank3=s.sAccountNum1 AND bBank4 = s.sAccountNum22) AS bankBranchName3,
				s.sAccount3 AS account1,
				s.sAccount31 AS account2,
				s.sAccount32 AS account3,
				s.sAccount4 AS accountName1,
				s.sAccount41 AS accountName2,
				s.sAccount42 AS accountName3,
				s.sAccountUnused AS accountUnused1,
				s.sAccountUnused1 AS accountUnused2,
				s.sAccountUnused2 AS accountUnused3
			FROM
				tScrivener AS s
			".$tb."
			WHERE
				s.sId NOT IN(1084,170,224) AND s.sName NOT LIKE '%業務專用%' ".$str." GROUP BY s.sId ORDER BY s.sId ASC";
	 $rs = $conn->Execute($sql);
	

	// $i = 0;
	while (!$rs->EOF) {
		$storeData[$rs->fields['sId']] = $rs->fields;
		$storeData[$rs->fields['sId']]['storeName'] = $storeData[$rs->fields['sId']]['name'];
		$storeData[$rs->fields['sId']]['storeOffice'] = $storeData[$rs->fields['sId']]['store'];

		$storeData[$rs->fields['sId']]['sales'] = @implode('_',getScrivenerSales($rs->fields['sId']));
		
		$storeData[$rs->fields['sId']]['storeCode'] = 'SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT);
		$storeData[$rs->fields['sId']]['count'] = 0;

		$storeCode .= '"'.$storeData[$rs->fields['sId']]['storeCode'].'",';

		//bank  要回饋金的帳號 QQ
		//
		$storeData[$rs->fields['sId']]['bank'] = getFeedBackData($rs->fields['sId'],1);

		


		array_push($storeId, $rs->fields['sId']);	
		$rs->MoveNext();
	}



	function getScrivenerBank($sId){
		global $conn;
		$conn->Execute("use www_first1;");
		$bank = array();
		$sql = "SELECT
					*,
					(SELECT bBank3_name FROM tBank WHERE bBank3=sBankMain AND bBank4='') AS cc,
						(SELECT bBank4_name FROM tBank WHERE bBank3=sBankMain AND bBank4=sBankBranch) AS dd
				FROM
					tScrivenerBank WHERE sScrivener = ".$sId." AND sUnUsed = 0";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			array_push($bank, $rs->fields);
			$rs->MoveNext();
		}

		return $bank;

	}

	// echo "<pre>";
	// print_r($storeData);
	// die;

	unset($col);unset($tb);unset($str);
	// $conn->Execute("use www_first1_report;");
	##
	//
	$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342" AND cas.cCaseStatus<>"8"' ; //005030342 電子合約書測試用沒有刪的樣子

	if ($storeId) {
		$query .= " AND cas.cScrivenerCode IN(".substr($storeCode, 0,-1).")"	;
		// unset($storeId);
		
	}

	// 搜尋條件-簽約日期
	if ($sDate) {
		$tmp = explode('-',$sDate) ;
		$sSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;
		
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate>="'.$sSignDate.'" ' ;
	}
	if ($eDate) {
		$tmp = explode('-',$eDate) ;
		$eSignDate = ($tmp[0] + 1911).'-'.$tmp[1].'-'.$tmp[2] ;
		unset($tmp) ;

		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate<="'.$eSignDate.'" ' ;
	}

	$sql ='
		SELECT 
			*
		FROM 
			tContractCaseReport AS cas
		WHERE
		'.$query.' 
		
		ORDER BY 
			cas.cApplyDate,cas.cSignDate ASC;
		' ;

	$conn->Execute("use www_first1_report;");
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {

		$sId = (int)substr($rs->fields['cScrivenerCode'], 2);

		if (in_array($sId, $storeId)) {

			if ($storeData[$sId]['category'] == 1) { //加盟
				$storeData[$sId]['count']++;
				if (empty($storeData[$sId]['caseData'])) {
					$storeData[$sId]['caseData'] = array();
				}
				$storeData[$sId]['caseData'][] = $rs->fields;
			}elseif ($storeData[$sId]['category'] == 2) { //直營

				$temp_storeCode = array();
				$temp_storeBrand = array();
				$temp_storeBranch = array();
				$temp_storeCompany = array();
				$temp_storeCategory = array();

						$temp_storeCode = explode(',', $rs->fields['cStoreCode']);
						$temp_storeBrand = explode(',', $rs->fields['cStoreBrand']);
						$temp_storeBranch = explode(',', $rs->fields['cStoreBranch']);
						$temp_storeCompany = explode(',', $rs->fields['cStoreCompany']);
						$temp_storeCategory = explode(',', $rs->fields['cStoreCategory']);

				
				if (!(($temp_storeBrand[0] == '台灣房屋' && $temp_storeCategory[0] == '直營') || ($temp_storeBrand[1] == '台灣房屋' && $temp_storeCategory[1] == '直營') || ($temp_storeBrand[2] == '台灣房屋' && $temp_storeCategory[2] == '直營') || ($temp_storeBrand[3] == '台灣房屋' && $temp_storeCategory[3] == '直營'))) { //直營案件
					$storeData[$sId]['count']++;
					if (empty($storeData[$sId]['caseData'])) {
						$storeData[$sId]['caseData'] = array();
					}
					$storeData[$sId]['caseData'][] = $rs->fields;
				}

				unset($temp_storeCode);
				unset($temp_storeBrand);
				unset($temp_storeBranch);
				unset($temp_storeCompany);

				
			}

			
		}
		

		$rs->MoveNext();
	}
	##

	// echo "<pre>";
	// print_r($storeData);

	// die;

	
	// $objPHPExcel = new PHPExcel();
	// //Set properties 設置文件屬性
	// $objPHPExcel->getProperties()->setCreator("第一建經");
	// $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
	// $objPHPExcel->getProperties()->setTitle("第一建經");
	// $objPHPExcel->getProperties()->setSubject("第一建經 2021活動");
	// $objPHPExcel->getProperties()->setDescription("第一建經 2021活動");

	// //指定目前工作頁
	// $objPHPExcel->setActiveSheetIndex(0);

	//寫入表頭資料
	
	$row = 1;
	// $objPHPExcel->getActiveSheet()->mergeCells("A1:D1");
	$csv_txt = ($cat == 2)?"直營":"加盟";
	$csv_txt .= "特約地政士贈獎活動\n";

	$level = array();

	// echo "<pre>";
	// print_r($storeData);

	// die;
	foreach ($storeData as $k => $v) {
		$money = 0;

		// print_r($v);
	
		if ($v['count'] >= 20 && $v['count'] < 35) {
			$money = 3000;
		}elseif ($v['count'] >= 35 && $v['count'] < 50) {
			$money = 6000;
		}elseif ($v['count'] >= 50 && $v['count'] < 70) {
			$money = 10000;
		}elseif ($v['count'] >= 70 && $v['count'] < 100) {
			$money = 15000;
		}elseif ($v['count'] >= 100 && $v['count'] < 150) {
			$money = 25000;
		}elseif($v['count'] >= 150 && $v['count'] < 250){
			$money = 50000;
		}elseif($v['count'] >= 250){
			$money = 100000;
		}


		if ($target == 1) {
			
			if ($money == 0) {
				continue;
			}
		}elseif ($target == 2) {
			if ($money > 0) {
				continue;
			}
		}


		if (empty($level[$money]['count'])) {
			$level[$money]['count'] = 0;
		}
		$level[$money]['count']++;//計算級距數量


		if (empty($level[$money]['data'])) {
			$level[$money]['data'] = array();
		}

		//$csv_txt_report2 .= "地政士編號,地政士,事務所名稱,數量,金額,代扣稅款10%,實付金額,業務人員,代號(總行+分行),總行代號,分行代號,指定帳號,戶名\n";
				
		$report2_txt = '';
		$tax = ($money > 20000)?$money*0.1:0;
		$report2_txt = $v['storeCode'].",".$v['storeName'].",".$v['storeOffice'].",".$v['count'].",".$money.",".$tax.",".($money-$tax).",".$v['sales'].',';

		foreach ($v['bank'] as $key => $value) {
			$report2_txt .= '="'.$value['bank'].$value['bankBranch'].'",="'.$value['bank'].'",="'.$value['bankBranch'].'",="'.$value['account'].'",'.$value['accountName'].',';
			
		}

		$report2_txt .= "\n";
		
		array_push($level[$money]['data'], $report2_txt);
		

	
			$csv_txt .= "名稱,數量,金額\n";
			$csv_txt .= $v['storeName'].",".$v['count'].",".$money."\n\n";

			

			if ($_POST['report'] == 1) {
				$csv_txt .= "序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,進案日期,簽約日期,實際點交日期,地政士姓名,標的物座落,狀態,仲介業務,地政士業務\n";
				if (is_array($v['caseData'])) {
					foreach ($v['caseData'] as $key => $value) {
						$branchCode = array();
						$branchName = array();
						$branchSales = array();
						$scrivenerSales = array();	
						$applyDate = (substr($value['cApplyDate'], 0,10)!='0000-00-00')?(substr($value['cApplyDate'], 0,4)-1911).substr($value['cApplyDate'], 4,6):'000-00-00';
						$signDate = (substr($value['cSignDate'], 0,10)!='0000-00-00')?(substr($value['cSignDate'], 0,4)-1911).substr($value['cSignDate'], 4,6):'000-00-00';
						
						$endDate = (substr($value['cEndDate'], 0,10)!='0000-00-00')? (substr($value['cEndDate'], 0,4)-1911).substr($value['cEndDate'], 4,6):'000-00-00';

						$temp_storeCode = array();
						$temp_storeBrand = array();
						$temp_storeBranch = array();
						$temp_storeCompany = array();

						$temp_storeCode = explode(',', $value['cStoreCode']);
						$temp_storeBrand = explode(',', $value['cStoreBrand']);
						$temp_storeBranch = explode(',', $value['cStoreBranch']);
						$temp_storeCompany = explode(',', $value['cStoreCompany']);

						for ($i=0; $i < count($temp_storeCode); $i++) { 
							

							array_push($branchCode, $temp_storeCode[$i]);
							array_push($branchName, $temp_storeBranch[$i]);
							$branchSales = array_merge($branchSales,getBranchSales(substr($temp_storeCode[$i], 2)));
						}

						// if ($value['cStoreCode'] > 0) {
						// 	array_push($branchCode, $value['cStoreCode']);
						// 	array_push($branchName, $value['branchName']);
						// 	$branchSales = array_merge($branchSales,getBranchSales($value['branch']));
						// }

						// if ($value['branch1'] > 0) {
						// 	array_push($branchCode, $value['brandCode1'].str_pad($value['branch1'], 5,0,STR_PAD_LEFT));
						// 	array_push($branchName, $value['branchName1']);
						// 	$branchSales = array_merge($branchSales,getBranchSales($value['branch1']));
						// }

						// if ($value['branch2'] > 0) {
						// 	array_push($branchCode, $value['brandCode2'].str_pad($value['branch2'], 5,0,STR_PAD_LEFT));
						// 	array_push($branchName, $value['branchName2']);
						// 	$branchSales = array_merge($branchSales,getBranchSales($value['branch2']));
						// }

						// if ($value['branch3'] > 0) {
						// 	array_push($branchCode, $value['brandCode3'].str_pad($value['branch3'], 5,0,STR_PAD_LEFT));
						// 	array_push($branchName, $value['branchName3']);
						// 	$branchSales = array_merge($branchSales,getBranchSales($value['branch3']));
						// }
						
						$scrivenerSales = getScrivenerSales(substr($value['cScrivenerCode'], 2));

						$csv_txt .= ($key+1).",\t".$value['cCertifiedId'].",".@implode('_', $branchCode).",".@implode('_', $branchName).",".$value['cOwner'].",";
						$csv_txt .= $value['cBuyer'].",".$value['cTotalMoney'].",".$value['cCertifiedMoney'].",".$applyDate.",".$signDate.",".$endDate.",".$value['cScrivenerName'].",";
						$csv_txt .= $value['cBuildAddressZip'].$value['cBuildAddressCity'].$value['cBuildAddressArea'].$value['cBuildAddress'].",".$value['cCaseStatus'].",".@implode('_', $branchSales).",".@implode('_', $scrivenerSales)."\n";

						
					}
					$csv_txt .= "\n";


				}
			}

		

		$row++;
	}

	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header('Content-type:application/force-download');
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=csv_export.csv");

	// echo iconv("big-5", "utf-8", $csv_txt) ;

		echo "\xEF\xBB\xBF";

		if ($_POST['report'] == 1) {
			echo $csv_txt;
		}else{
			$csv_txt_report2 = '';

			$csv_txt_report2 = ($cat == 2)?"直營":"加盟";
			$csv_txt_report2 .= "特約地政士贈獎活動\n";

			ksort($level);

			foreach ($level as $k => $v) {
				$csv_txt_report2 .= $k."元".$v['count']."位\n";
				$csv_txt_report2 .= "地政士編號,地政士,事務所名稱,數量,金額,代扣稅款10%,實付金額,業務人員,代號(總行+分行),總行代號(1),分行代號(1),指定帳號(1),戶名(1),代號(總行+分行),總行代號(2),分行代號(2),指定帳號(2),戶名(2),代號(總行+分行),總行代號(3),分行代號(3),指定帳號(3),戶名(3)\n";
				// print_r($v['data']);

				// die;
				foreach ($v['data'] as $key => $value) {

					$csv_txt_report2 .= $value;

					// echo $value;

				}
				$csv_txt_report2 .= "\n\n";
				// die;
			}
			echo $csv_txt_report2;
		}
		
	
	exit;
	// die;
	// $_file = '2021ScrivenerAct.xlsx' ;

	// header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	// header("Cache-Control: no-store, no-cache, must-revalidate");
	// header("Cache-Control: post-check=0, pre-check=0", false);
	// header("Pragma: no-cache");
	// header('Content-type:application/force-download');
	// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// header('Content-Disposition: attachment;filename='.$_file);

	// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	// $objWriter->save("php://output");
	exit;
}
##
function getBranchSales($id){
	global $conn;
	$conn->Execute("use www_first1;");
	$sales = array();
	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS Name FROM tBranchSales WHERE bBranch = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['Name'];

		$rs->MoveNext();
	}

	return $sales;
}

function getScrivenerSales($id){
	global $conn;
	$conn->Execute("use www_first1;");
	$sales = array();

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name FROM tScrivenerSales WHERE sScrivener = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['Name'];

		$rs->MoveNext();
	}

	return $sales;
}

function getFeedBackData($id,$type){
	global $conn;
	$conn->Execute("use www_first1;");
	$sql = "SELECT
				fAccountNum AS bank,
				(SELECT bBank4_name FROM tBank WHERE bBank3=fAccountNum AND bBank4 = '') AS bankName,
				fAccountNumB AS bankBranch,
				(SELECT bBank4_name FROM tBank WHERE bBank3=fAccountNum AND bBank4 = fAccountNumB) AS bankBranchName,
				fAccount AS account,
				fAccountName AS accountName
			FROM
				tFeedBackData WHERE fType ='".$type."' AND fStoreId ='".$id."' AND fStatus = 0 AND fStop = 0";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);
	$list = array();
	while (!$rs->EOF) {
		array_push($list, $rs->fields) ;
		$rs->MoveNext();
	}

	return $list;
}
##
$smarty->assign('menuSales',$menuSales);
$smarty->assign('menuCategory',array(1=>'加盟',2=>'直營',3=>'全部'));
$smarty->assign('cat',$cat);
$smarty->assign('sDate',$sDate);
$smarty->assign('eDate',$eDate);
$smarty->display('act_202103.inc.tpl', '', 'actives');
?>