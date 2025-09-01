<?php
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$check = addslashes(trim($_POST['check']));

$member_power = $_SESSION['member_banktrans'];

$member_id = $_SESSION['member_id'] ;


switch ($check) {
	case 'o1':
		$title1 = '案件資訊';
		$title2 = '七日未入帳案件';
		$data = case1($conn,$member_id);
		break;
	case 'o2':
		$title1 = '案件資訊';
		$title2 = '2個月未結案之案件[只計算無交屋日的案件]';
		$data = case2($conn,$member_id);
		break;
	case 'a1':
		$title1 = '全部案件資訊';
		$title2 = '七日未入帳案件';
		$data = case1($conn);
		break;
	case 'a2':
		$title1 = '全部案件資訊';
		$title2 = '2個月未結案之案件[只計算無交屋日的案件]';
		$data = case2($conn);
		break;
	case 'u1':
		$title1 = '案件資訊';
		$title2 = '超過點交日尚未結案(不含2個月未結案之案件)[只計算有交屋日的案件]';
		$data_check = case2($conn);
		$data = case3($conn,$member_id,$data_check);
		break;
	case 'u2':
		$title1 = '全部案件資訊';
		$title2 = '超過點交日尚未結案(不含2個月未結案之案件)[只計算有交屋日的案件]';
		$data_check = case2($conn);
		$data = case3($conn,'',$data_check);
		break;
	default:
		# code...
		break;
}


$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("案件報表");
$objPHPExcel->getProperties()->setDescription("第一建經案件報表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('A1',$title1."-".$title2);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(42) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14) ;
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14) ;

if ($check =='u2') {
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(60) ;
}else{
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(60) ;
}

$objPHPExcel->getActiveSheet()->setCellValue('A2','保證號碼');//65
$objPHPExcel->getActiveSheet()->setCellValue('B2','簽約日期');
$objPHPExcel->getActiveSheet()->setCellValue('C2','預計交屋日');
$objPHPExcel->getActiveSheet()->setCellValue('D2','地政士');
$objPHPExcel->getActiveSheet()->setCellValue('E2','仲介店');
// $objPHPExcel->getActiveSheet()->setCellValue('F2','買方');
// $objPHPExcel->getActiveSheet()->setCellValue('G2','賣方');

$col = 70; //72
if ($check == 'u2') {
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','專戶收支餘額');
}

$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','經辦');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','原因');
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','原因日期');
$objPHPExcel->getActiveSheet()->getStyle('A1:K2')->getFont()->setBold(true) ;
$objPHPExcel->getActiveSheet()->getStyle('A2:K2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER) ;
$objPHPExcel->getActiveSheet()->getStyle('A2:K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER) ;
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(24) ;
$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(24) ;

if ($check == 'u1' || $check == 'u2' || $check == 'o2' || $check == 'a2') {
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','預計交屋日(紅字為簽約日加2個月)');
		// // $objPHPExcel->getActiveSheet()->setCellValue('K2','簽約加2個月');
		if ($check == 'u1' || $check == 'u2'){
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','過期天數');
		}
}
$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).'2','銀行');
$row = 3;

for ($i=0; $i < count($data); $i++) { 
	$col = 65;
	$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $data[$i]['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['cCertifiedId']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['cSignDate']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['cClosingDay']);

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['scrivener']);

	if ($data[$i]['brand']=='非仲介成交') {
		$store = $data[$i]['store'];
	}else{
		$store = $data[$i]['brand'].$data[$i]['store'];
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$store);

	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['buyer']);
	// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['owner']);
	if ($check == 'u2') {
		$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['money']);
	}
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['sUndertaker1']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['remark']);
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['remarkTime']);

	if ($check == 'u1' || $check == 'u2' || $check == 'o2' || $check == 'a2') {
		// if ($data[$i]['color'] != '') {
		// 	$objPHPExcel->getActiveSheet()->getStyle(chr($col).$row)->getFont()->getColor()->setARGB('FF0000'); 
		// }
		
		// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['tmp']);
		
		if ($check == 'u1' || $check == 'u2')
		{
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$data[$i]['date_range']);
		}
		
	}

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,checkBank($data[$i]['cEscrowBankAccount']));

	$row++;
}



##
$_file = 'case.xlsx' ;

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

function case1($conn,$mid='')
{
	if ($mid != '') {
		$query = " AND scr.sUndertaker1 ='".$mid."'";
	}
	 //所有符合條件案件
	 $sql = "
 		SELECT 
 			cc.cCertifiedId,
 			cc.cSignDate,
			(SELECT cNote FROM tContractNote WHERE cCertifiedId = cc.cCertifiedId AND cDel = 0 AND cCategory =1 ORDER BY cModify_Time DESC LIMIT 1) as remark,
			(SELECT cModify_Time FROM tContractNote WHERE cCertifiedId = cc.cCertifiedId AND cDel = 0 AND cCategory =1 ORDER BY cModify_Time DESC LIMIT 1) as remarkTime,		
 			(SELECT pName FROM tPeopleInfo WHERE pId=scr.sUndertaker1) AS sUndertaker1,
 			scr.sName AS scrivener,
 			(SELECT bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum) AS store,
 			(SELECT bName FROM tBrand AS b WHERE b.bId=cr.cBrand) AS brand,
 			cb.cName AS buyer,
 			co.cName AS owner,
			pr.cClosingDay,
			cc.cEscrowBankAccount			 
 		FROM 
 			tContractCase AS cc
 		LEFT JOIN 
 			tContractRealestate AS cr ON cc.cCertifiedId = cr.cCertifyId 
 		LEFT JOIN 
 			tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
 		LEFT JOIN 
 			tScrivener AS scr ON scr.sId = cs.cScrivener
 		LEFT JOIN 
 			tContractBuyer AS cb  ON cb.cCertifiedId = cs.cCertifiedId 
 		LEFT JOIN 
 			tContractOwner AS co  ON co.cCertifiedId = cs.cCertifiedId 	
		LEFT JOIN
			tContractProperty AS pr ON cc.cCertifiedId=pr.cCertifiedId
 		WHERE
 			cc.cCaseStatus=2 
 			AND cc.cSignDate != '00-00-00 00:00:00'
 			AND cc.cSignDate <= '".date('Y-m-d',strtotime('-7 day'))." 00:00:00'
 			".$query."
 			GROUP BY cc.cCertifiedId 
 			ORDER BY  cc.cSignDate DESC
 		";


 		
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		// $tmp = explode(' ', $rs->fields['cSignDate']) ;
		$rs->fields['cSignDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);
		$rs->fields['cClosingDay'] = substr($rs->fields['cClosingDay'],0,10) ;
		if ($rs->fields['cClosingDay'] == '0000-00-00') $rs->fields['cClosingDay'] = '' ;
		$rs->fields['remarkTime'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['remarkTime']);



		$list[] = $rs->fields;

		// unset($tmp);
		$rs->MoveNext();
	}

	//有入帳

 	$sql = "
	 		SELECT 
	 			cc.cCertifiedId
	 		FROM 
	 			tExpense AS ex
	 		LEFT JOIN 
	 			tContractCase AS cc	 ON cc.cEscrowBankAccount=SUBSTRING(ex.eDepAccount,-14)
	 		JOIN 
	 			 tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
	 		JOIN 
	 			tScrivener AS scr ON scr.sId = cs.cScrivener	 			
	 		WHERE
	 			cc.cCaseStatus=2 
	 			AND cc.cSignDate != '00-00-00 00:00:00'
	 			AND cc.cSignDate <= '".date('Y-m-d',strtotime('-7 day'))." 00:00:00'
	 			".$query."
	 			GROUP BY cc.cCertifiedId
	 		";
	 	// echo $sql;
	 $rs2 = $conn->Execute($sql);

	 while (!$rs2->EOF) {
		$list2[] = $rs2->fields;


		$rs2->MoveNext();
	}

	$count = count($list);
	for ($i=0; $i < $count; $i++) { 
			for ($j=0; $j < count($list2); $j++) { 

				if ($list[$i]['cCertifiedId']==$list2[$j]['cCertifiedId']) {
						
						unset($list[$i]);
				}
			}
	}

	sort($list,SORT_NUMERIC);
	// // echo '123';
	// echo "<pre>";
	// 	print_r($list);
	// echo "</pre>";
	// die;
	return $list;
}

function case2($conn,$mid='')
{
	if ($mid != '') {
		$query = " AND scr.sUndertaker1 ='".$mid."'";
	}

	$today = date("Y-m-d");
	$month_range = 2;
	$i = 0;

	 $sql = "
 		SELECT 
 			cc.cCertifiedId,
 			cc.cSignDate,
 			cc.cFinishDate2,
			(SELECT cNote FROM tContractNote WHERE cCertifiedId = cc.cCertifiedId AND cDel = 0 AND cCategory =2 ORDER BY cModify_Time DESC LIMIT 1) as remark,
			(SELECT cModify_Time FROM tContractNote WHERE cCertifiedId = cc.cCertifiedId AND cDel = 0 AND cCategory =2 ORDER BY cModify_Time DESC LIMIT 1) as remarkTime,		
 			(SELECT pName FROM tPeopleInfo WHERE pId=scr.sUndertaker1) AS sUndertaker1,
 			scr.sName AS scrivener,
 			(SELECT bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum) AS store,
 			(SELECT bName FROM tBrand AS b WHERE b.bId=cr.cBrand) AS brand,
 			cb.cName AS buyer,
 			co.cName AS owner,
			pr.cClosingDay,
			cc.cEscrowBankAccount 
 		FROM 
 			tContractCase AS cc
 		LEFT JOIN 
 			tContractRealestate AS cr ON cc.cCertifiedId = cr.cCertifyId 
 		LEFT JOIN 
 			tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
 		LEFT JOIN 
 			tScrivener AS scr ON scr.sId = cs.cScrivener
 		LEFT JOIN 
 			tContractBuyer AS cb  ON cb.cCertifiedId = cs.cCertifiedId 
 		LEFT JOIN 
 			tContractOwner AS co  ON co.cCertifiedId = cs.cCertifiedId 
		LEFT JOIN
			tContractProperty AS pr ON cc.cCertifiedId=pr.cCertifiedId
 		WHERE
 			cc.cCaseStatus=2 
 			AND cc.cSignDate != '0000-00-00 00:00:00'
 			AND cc.cSignDate <= '".date('Y-m-d',strtotime('-2 month'))." 00:00:00'
 			".$query."
 			GROUP BY cc.cCertifiedId 
 			ORDER BY  cc.cSignDate DESC
 		";


	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$rs->fields['remarkTime'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['remarkTime']);	
		$rs->fields['cSignDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);	
		$tmp1 = explode('-',$rs->fields['cSignDate']) ;
		$valid_date = date("Y-m-d",mktime(0,0,0,($tmp1[1]+$month_range),$tmp1[2],$tmp1[0])) ; //+月的
		$valid_date2 = strtotime($valid_date) ;
		unset($tmp1);

		$rs->fields['cClosingDay'] = substr($rs->fields['cClosingDay'],0,10) ;
		$finishDate2 = strtotime($rs->fields['cClosingDay']) ;//預計點交日

		if ($rs->fields['cClosingDay'] == '0000-00-00') $rs->fields['cClosingDay'] = '' ;

	
		##
		//簽約日
		
		$SignDate = strtotime($rs->fields['cSignDate']) ;
		
		
		##
		
		// if (($today > $rs->fields['cClosingDay']) && ($rs->fields['cClosingDay'] != '')) { // 預計點交日不為空且今日大於預計點交日
		// 	$list[$i] = $rs->fields;
		// 	$list[$i]['tmp'] = $rs->fields['cClosingDay'];
		// 	$list[$i]['valid_date'] = $valid_date;
		// 	$list[$i]['date_range'] = floor(($today2 - $finishDate2)/3600/24); ///86400天
		// 	$i++;
			
		// }elseif (($rs->fields['cClosingDay'] == '') && ($today > $valid_date)) {
		// 	$list[$i] = $rs->fields;
		// 	$list[$i]['tmp'] = $valid_date;
		// 	$list[$i]['valid_date'] = $valid_date;
		// 	$list[$i]['date_range'] = floor(($today2 - $valid_date2)/3600/24); ///86400天
		// 	$list[$i]['color'] ="1";
		// 	$i++;
		// }

		if (($rs->fields['cClosingDay'] == '') && ($today > $valid_date)) {
			$list[$i] = $rs->fields;
			$list[$i]['tmp'] = $valid_date;
			$list[$i]['valid_date'] = $valid_date;
			$list[$i]['date_range'] = floor(($today2 - $valid_date2)/3600/24); ///86400天
			$list[$i]['color'] ="1";
			$i++;
		}
		##

		// $list[] = $rs->fields;

		unset($tmp);
		

		$rs->MoveNext();
	}

	return $list;
}

function case3($conn,$mid='',$arr)
{
	if ($mid != '') {
		$query = " AND scr.sUndertaker1 ='".$mid."'";
	}

	$today = date("Y-m-d");
	$today2 = strtotime($today);
	$month_range = 2;//2個月
	$i = 0;
	
	$sql = "SELECT 
 			cc.cCertifiedId,
 			cc.cSignDate,
 			cc.cEscrowBankAccount,
 			cc.cCaseMoney,
			(SELECT cNote FROM tContractNote WHERE cCertifiedId = cc.cCertifiedId AND cDel = 0 AND cCategory =3 ORDER BY cModify_Time DESC LIMIT 1) as remark,
			(SELECT cModify_Time FROM tContractNote WHERE cCertifiedId = cc.cCertifiedId AND cDel = 0 AND cCategory =3 ORDER BY cModify_Time DESC LIMIT 1) as remarkTime,		
			cc.cFinishDate2,
 			(SELECT pName FROM tPeopleInfo WHERE pId=scr.sUndertaker1) AS sUndertaker1,
 			scr.sName AS scrivener,
 			(SELECT bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum) AS store,
 			(SELECT bName FROM tBrand AS b WHERE b.bId=cr.cBrand) AS brand,
 			cb.cName AS buyer,
 			co.cName AS owner,
			pr.cClosingDay,
			cc.cEscrowBankAccount 
 		FROM 
 			tContractCase AS cc
 		LEFT JOIN 
 			tContractRealestate AS cr ON cc.cCertifiedId = cr.cCertifyId 
 		LEFT JOIN 
 			tContractScrivener AS cs  ON cc.cCertifiedId = cs.cCertifiedId
 		LEFT JOIN 
 			tScrivener AS scr ON scr.sId = cs.cScrivener
 		LEFT JOIN 
 			tContractBuyer AS cb  ON cb.cCertifiedId = cs.cCertifiedId 
 		LEFT JOIN 
 			tContractOwner AS co  ON co.cCertifiedId = cs.cCertifiedId 
		LEFT JOIN
			tContractProperty AS pr ON cc.cCertifiedId=pr.cCertifiedId
 		WHERE
 			cc.cCaseStatus=2 AND cc.cSignDate != '0000-00-00 00:00:00' ".$query."
 			GROUP BY cc.cCertifiedId ORDER BY  cc.cSignDate DESC";
 		
 		

	$rs = $conn->Execute($sql);
	$jjj = 0;
	while (!$rs->EOF) {

		$rs->fields['money'] = $rs->fields['cCaseMoney'];
		
		$rs->fields['cSignDate'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['cSignDate']);	
		$tmp1 = explode('-',$rs->fields['cSignDate']) ;
		$valid_date = date("Y-m-d",mktime(0,0,0,($tmp1[1]+$month_range),$tmp1[2],$tmp1[0])) ; //+月的
		$valid_date2 = strtotime($valid_date) ;
		unset($tmp1);

		$rs->fields['cClosingDay'] = substr($rs->fields['cClosingDay'],0,10) ;
		$finishDate2 = strtotime($rs->fields['cClosingDay']) ;
		if ($rs->fields['cClosingDay'] == '0000-00-00') $rs->fields['cClosingDay'] = '' ;

		$rs->fields['remarkTime'] = preg_replace("/[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/",'',$rs->fields['remarkTime']);
		##
		//簽約日
		
		$SignDate = strtotime($rs->fields['cSignDate']) ;
		
		
		##
		

		if (($today > $rs->fields['cClosingDay']) && ($rs->fields['cClosingDay'] != '')) { // 預計點交日不為空且今日大於預計點交日
			$list[$i] = $rs->fields;
			$list[$i]['valid_date'] = $valid_date;
			$list[$i]['tmp'] = $rs->fields['cClosingDay'];
			$list[$i]['date_range'] = floor(($today2 - $finishDate2)/3600/24); ///86400天
			$i++;
			
		}
		// elseif (($rs->fields['cClosingDay'] == '') && ($today > $valid_date)) {
		// 	$list[$i] = $rs->fields;
		// 	$list[$i]['tmp'] = $valid_date;
		// 	$list[$i]['valid_date'] = $valid_date;
		// 	$list[$i]['date_range'] = floor(($today2 - $valid_date2)/3600/24); ///86400天
		// 	$list[$i]['color'] ="1";
		// 	$i++;
		// }elseif (($rs->fields['cClosingDay'] == '') && ($rs->fields['cSignDate'] == '0000-00-00')) {
		// 	$list[$i]['date_range'] = 0;
		// }
		
		$rs->MoveNext();
	}

	// for ($i=0; $i < count($list); $i++) { 
	// 	$check = 0;
	// 	for ($j=0; $j < count($arr); $j++) { 
	// 		if ($arr[$j]['cCertifiedId'] == $list[$i]['cCertifiedId']) {
	// 			$check =1;
	// 			// die($arr[$i]['cCertifiedId']);
	// 			break;
	// 		}
	// 	}

	// 	if ($check == 0) {
	// 		$tmp[] = $list[$i];
	// 	}
	// }

	// unset($list);

	return $list;
}

function checkBank($val){

	if (preg_match("/60001/", $val)) {
		$bank = '一銀';
	}elseif (preg_match("/99985/", $val)) {
		$bank = '永豐西門';
	}elseif (preg_match("/99986/", $val)) {
		$bank = '永豐城中';
	}elseif (preg_match("/96988/", $val)) {
		$bank = '台新';
	}

	return $bank;
}
?>