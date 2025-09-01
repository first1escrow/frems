<?php
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../report/getBranchType.php';
require dirname(dirname(__FILE__)).'/vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Style\Fill;
##
// $month = '08';

##查詢字串
if (!empty($month) && !empty($years)) {

	$year = $years ;

	$day = date('t',$year."-".$month);
	
	$date_start = $year."-".$month."-01";
	$date_end = $year."-".$month."-".$day."";


	$query = " cSignDate >='".$date_start." 00:00:00'";

	$query .= " AND cSignDate <='".$date_end." 23:59:59'";
	
}
##

function getGroupBranch($conn,$group){
	$tmp = array();
	$sql = "SELECT bId FROM tBranch WHERE bGroup = '".$group."'";
	// echo $sql;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
			
		$tmp[] = $rs->fields['bId'];

		$rs->MoveNext();
	}

	return $tmp;
}

function getBrandBranch($brand){
	global $conn;

	$sql = "SELECT bId FROM tBranch WHERE bBrand = '".$brand."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['bId'];

		$rs->MoveNext();
	}

	return $tmp;

}

// 20170509把莊威玲及藍海拿掉 加幸褔家

$branch1 = getGroupBranch($conn,1);//游明桐

$branch2 = getGroupBranch($conn,2);//燊氐集團

// $branch3 = getGroupBranch($conn,3);//莊威玲

// $branch4 = getGroupBranch($conn,9);//藍海房屋

$branch5 = getGroupBranch($conn,4);//葉寶桐

$branch6 = getGroupBranch($conn,11);//幸福家

$branch7 = getBrandBranch(75);//飛鷹地產及

$branch8 =  getBrandBranch(72);//群義房屋


// $tmp = array_merge($branch1,$branch2,$branch3,$branch4,$branch5);
$tmp = array_merge($branch1,$branch2,$branch5,$branch6,$branch7,$branch8);

for ($i=0; $i < count($tmp); $i++) { 
	$tmp2[]= "'".$tmp[$i]."'";
}
$branch_str = implode(',',$tmp2);
unset($tmp);


for ($i=0; $i < count($branch1); $i++) { 
	$list1[$branch1[$i]] = 0; 

}
for ($i=0; $i < count($branch2); $i++) { 
	$list2[$branch2[$i]] = 0; 

}

// for ($i=0; $i < count($branch3); $i++) { 
// 	$list3[$branch3[$i]] = 0; 

// }

// for ($i=0; $i < count($branch4); $i++) { 
// 	$list4[$branch4[$i]] = 0; 

// }

for ($i=0; $i < count($branch5); $i++) { 
	$list5[$branch5[$i]] = 0; 

}

for ($i=0; $i < count($branch6); $i++) { 
	$list6[$branch6[$i]] = 0; 

}
for ($i=0; $i < count($branch7); $i++) { 
	$list7[$branch7[$i]] = 0; 

}
for ($i=0; $i < count($branch8); $i++) { 
	$list8[$branch8[$i]] = 0; 

}
//固定店家
if ($query) { $query .= " AND " ; }
	
	$query .= ' (rea.cBranchNum IN ('.$branch_str.') OR rea.cBranchNum1 IN ('.$branch_str.') OR rea.cBranchNum2 IN ('.$branch_str.')) ' ;

##
//110
$sql = "SELECT
			cc.cCertifiedId,
			cc.cApplyDate,
			cc.cEndDate,
			cc.cSignDate,
			rea.cBranchNum AS branch,
			rea.cBranchNum1 AS branch1,
			rea.cBranchNum2 AS branch2,
			rea.cBrand as brand,
			rea.cBrand1 as brand1,
			rea.cBrand2 as brand2,
			(SELECT zCity FROM tZipArea AS z WHERE z.zZip=cp.cZip) AS city,
			(SELECT zArea FROM tZipArea AS z WHERE z.zZip=cp.cZip) AS area,
			cp.cAddr,
			(SELECT sName FROM tScrivener AS s WHERE s.sId=cs.cScrivener) AS scrivener,
			(SELECT sName FROM tStatusCase AS sc WHERE sc.sId=cc.cCaseStatus) AS status,
			(SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum) AS branchname,
			(SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum1) AS branchname1,
			(SELECT bStore FROM tBranch AS b WHERE b.bId=rea.cBranchNum2) AS branchname2,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandname,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandname1,
			(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandname2,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand) AS brandcode,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand1) AS brandcode1,
			(SELECT bCode FROM tBrand WHERE bId = rea.cBrand2) AS brandcode2,
			ci.cTotalMoney
		FROM 
			tContractCase AS cc
		LEFT JOIN 
			tContractRealestate AS rea ON rea.cCertifyId=cc.cCertifiedId
		LEFT JOIN
			tContractProperty AS cp ON cp.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractIncome AS ci ON ci.cCertifiedId = cc.cCertifiedId
		WHERE
			".$query."
		GROUP BY
			cc.cCertifiedId
		ORDER BY 
			cc.cApplyDate,cc.cId;
		";

$rs = $conn->Execute($sql);
// $type = branch_type($conn,$arr[$i]);
while (!$rs->EOF) {

	$arr[] = $rs->fields;
	
	$rs->MoveNext();
}


for ($i=0; $i < count($arr); $i++) { 

	//改品牌但還是要顯示店家
		$_branch = getBranch_brand($conn,$arr[$i]['branch']);//tBranch的資料(現行的)
		$_branch1 = getBranch_brand($conn,$arr[$i]['branch1']); //tBranch的資料
		$_branch2 = getBranch_brand($conn,$arr[$i]['branch2']);//tBranch的資料

	$type = branch_type($conn,$arr[$i]);
	##
	

	if($type == 'O'){
		
		if ($arr[$i]['brand'] != 1 && $arr[$i]['brand'] != 49 && $arr[$i]['branch'] > 0) {

			BranchCount($arr[$i]);

		}elseif ($arr[$i]['brand1'] != 1 && $arr[$i]['brand1'] != 49 && $arr[$i]['branch1'] > 0) { 
				
			BranchCount($arr[$i],'1');
	

		}elseif ($arr[$i]['brand2'] != 1 && $arr[$i]['brand2'] != 49 && $arr[$i]['branch2'] > 0){

			BranchCount($arr[$i],'2');
		}

	}elseif ($type == 'T') {
			//判斷那一間店為台灣房屋加盟

		if (($arr[$i]['brand'] == 1 && $_branch['bCategory'] ==1 ) && $arr[$i]['branch'] > 0 ) {

			BranchCount($arr[$i]);

		}elseif (($arr[$i]['brand1'] == 1 && $_branch1['bCategory'] ==1) && $arr[$i]['branch1'] > 0) { 
				
			BranchCount($arr[$i],'1');

		}elseif (($arr[$i]['brand2'] == 1 && $_branch2['bCategory'] ==1) && $arr[$i]['branch2'] > 0){

			BranchCount($arr[$i],'2');

		}
	}elseif ($type == 'U') {
			//判斷那一間店為優美加盟
		if ($arr[$i]['brand'] == 49 && $arr[$i]['branch'] > 0) {

			BranchCount($arr[$i]);

		}elseif ($arr[$i]['brand1'] == 49 && $arr[$i]['branch1'] > 0) { 
				
			BranchCount($arr[$i],'1');
				
		}elseif ($arr[$i]['brand2'] == 49 && $arr[$i]['branch2'] > 0){

			BranchCount($arr[$i],'2');
		}
	}elseif ($type == '3') {
			//非仲介成交
		if ($arr[$i]['brand'] == 2 && $arr[$i]['branch'] > 0) {

			BranchCount($arr[$i]);

		}elseif ($arr[$i]['brand1'] == 2 && $arr[$i]['branch1'] > 0) { 
				
			BranchCount($arr[$i],'1');
				
		}elseif ($arr[$i]['brand2'] == 2 && $arr[$i]['branch2'] > 0){

			BranchCount($arr[$i],'2');
		}
	}elseif ($type == '2') {
			//直營
		if ($_branch['bCategory'] == 2 && $arr[$i]['branch'] > 0) {

			BranchCount($arr[$i]);

		}elseif ($_branch1['bCategory'] == 2 && $arr[$i]['branch1'] > 0) { 
				
			BranchCount($arr[$i],'1');
				
		}elseif ($_branch2['bCategory'] == 2 && $arr[$i]['branch2'] > 0){

			BranchCount($arr[$i],'2');
		}
	}elseif ($type == '1') {
			//加盟
			if ($_branch['bCategory'] == 1 && $arr[$i]['branch'] > 0) {

				BranchCount($arr[$i]);

			}elseif ($_branch1['bCategory'] == 1 && $arr[$i]['branch1'] > 0) { 
				
				BranchCount($arr[$i],'1');
				
			}elseif ($_branch2['bCategory'] == 1 && $arr[$i]['branch2'] > 0){

				BranchCount($arr[$i],'2');
			}
		}elseif ($type == 'N') {
			//未知
			BranchCount($arr[$i]);
		}

		
}
##
function BranchCount($arr,$id=''){
	global $list1,$list2,$list3,$list4,$list5,$list6,$list7,$list8;
	global $branch1,$branch2,$branch3,$branch4,$branch5,$branch6,$branch7,$branch8;
	global $data1,$data2,$data3,$data4,$data5,$data6,$data7,$data8;


	
	if (in_array($arr['branch'.$id], $branch1)) {  //找尋陣列是否符合要的店家
			
		$list1[$arr['branch'.$id]]=$list1[$arr['branch'.$id]]+1; //游明桐
		$data1[$arr['branch'.$id]][] = $arr;

	}elseif (in_array($arr['branch'.$id], $branch2)) {

		$list2[$arr['branch'.$id]]=$list2[$arr['branch'.$id]]+1;//燊氐集團
		$data2[$arr['branch'.$id]][] = $arr;

	}elseif (in_array($arr['branch'.$id], $branch5)) {

		$list5[$arr['branch'.$id]]=$list5[$arr['branch'.$id]]+1; //葉寶桐
		$data5[$arr['branch'.$id]][] = $arr;
	}elseif (in_array($arr['branch'.$id], $branch6)) {

		$list6[$arr['branch'.$id]]=$list6[$arr['branch'.$id]]+1; //幸福家
		$data6[$arr['branch'.$id]][] = $arr;
	}elseif (in_array($arr['branch'.$id], $branch7)) {

		$list7[$arr['branch'.$id]]=$list7[$arr['branch'.$id]]+1; //飛鷹
		$data7[$arr['branch'.$id]][] = $arr;
	}elseif (in_array($arr['branch'.$id], $branch8)) {

		$list8[$arr['branch'.$id]]=$list8[$arr['branch'.$id]]+1; //群義
		$data8[$arr['branch'.$id]][] = $arr;
	}

	// elseif (in_array($arr['branch'.$id], $branch3)) {

	// 	$list3[$arr['branch'.$id]]=$list3[$arr['branch'.$id]]+1; //莊威玲
	// 	$data3[$arr['branch'.$id]][] = $arr;

	// }elseif (in_array($arr['branch'.$id], $branch4)) {

	// 	$list4[$arr['branch'.$id]]=$list4[$arr['branch'.$id]]+1; //藍海房屋
	// 	$data4[$arr['branch'.$id]][] = $arr;

	// }
}

##
function getBranch_brand($conn,$bid)
{
	$sql = "
		SELECT 
		bId,
		bBrand,
		bCategory,
		bStore,
		bManager,
		(SELECT bName FROM tBrand AS b WHERE bId =a.bBrand ) AS BradnName
		FROM 
			tBranch AS a
		WHERE bId='".$bid."'";

	$rs = $conn->Execute($sql);
	$tmp = $rs->fields;
	return $tmp;
}
##


##


// $objPHPExcel = new PHPExcel();
$objPHPExcel = new Spreadsheet();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("進案數報表");
$objPHPExcel->getProperties()->setDescription("第一建經出款數報表");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('群組店家統計表');
//寫入表頭資料
##顏色
$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FDFF37');
// $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->getStartColor()->setARGB('FDFF37');


$objPHPExcel->getActiveSheet()->getStyle("D1:E1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FDFF37');
// $objPHPExcel->getActiveSheet()->getStyle("D1:E1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle("D1:E1")->getFill()->getStartColor()->setARGB('FDFF37');

$objPHPExcel->getActiveSheet()->getStyle("G1:H1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FDFF37');
// $objPHPExcel->getActiveSheet()->getStyle("G1:H1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle("G1:H1")->getFill()->getStartColor()->setARGB('FDFF37');

$objPHPExcel->getActiveSheet()->getStyle("K1:L1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FDFF37');
// $objPHPExcel->getActiveSheet()->getStyle("K1:L1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle("K1:L1")->getFill()->getStartColor()->setARGB('FDFF37');

$objPHPExcel->getActiveSheet()->getStyle("N1:O1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FDFF37');
// $objPHPExcel->getActiveSheet()->getStyle("N1:O1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle("N1:O1")->getFill()->getStartColor()->setARGB('FDFF37');

$objPHPExcel->getActiveSheet()->getStyle("Q1:R1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FDFF37');
// $objPHPExcel->getActiveSheet()->getStyle("Q1:R1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle("Q1:R1")->getFill()->getStartColor()->setARGB('FDFF37');



// $objPHPExcel->getActiveSheet()->getStyle("M1:N1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
// $objPHPExcel->getActiveSheet()->getStyle("M1:N1")->getFill()->getStartColor()->setARGB('FDFF37');


$objPHPExcel->getActiveSheet()->getStyle('A1:Z50')->getFont()->setSize(16);

##合併儲存格
$objPHPExcel->getActiveSheet()->mergeCells("A1:B1");
$objPHPExcel->getActiveSheet()->mergeCells("D1:E1");
$objPHPExcel->getActiveSheet()->mergeCells("G1:H1");
// $objPHPExcel->getActiveSheet()->mergeCells("J1:K1");
// $objPHPExcel->getActiveSheet()->mergeCells("M1:O1");
$objPHPExcel->getActiveSheet()->mergeCells("K1:L1");
##標頭
$objPHPExcel->getActiveSheet()->setCellValue('A1','游明桐 日期：'.$date_start."至".$date_end);
$objPHPExcel->getActiveSheet()->setCellValue('D1','燊氐集團(林映成) 日期：'.$date_start."至".$date_end);
// $objPHPExcel->getActiveSheet()->setCellValue('G1','莊威玲 日期：'.$date_start."至".$date_end);
// $objPHPExcel->getActiveSheet()->setCellValue('J1','藍海集團 日期：'.$date_start."至".$date_end);
// $objPHPExcel->getActiveSheet()->setCellValue('M1','葉寶桐 日期：'.$date_start."至".$date_end);
$objPHPExcel->getActiveSheet()->setCellValue('G1','葉寶桐 日期：'.$date_start."至".$date_end);
$objPHPExcel->getActiveSheet()->setCellValue('K1','幸福家 日期：'.$date_start."至".$date_end);

$objPHPExcel->getActiveSheet()->setCellValue('N1','飛鷹 日期：'.$date_start."至".$date_end);

$objPHPExcel->getActiveSheet()->setCellValue('Q1','群義 日期：'.$date_start."至".$date_end);
##寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(40);
// $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(40);
##
// $alldata = array(); 

if (!is_array($list1)) {$list1 = array();}
if (!is_array($list2)) {$list2 = array();}
// if (!is_array($list3)) {$list3 = array();}
// if (!is_array($list4)) {$list4 = array();}
if (!is_array($list5)) {$list5 = array();}
if (!is_array($list6)) {$list6 = array();}
if (!is_array($list7)) {$list7 = array();}
if (!is_array($list8)) {$list8 = array();}

$alldata[] = $list1;
$alldata[] = $list2;
// $alldata[] = $list3;
// $alldata[] = $list4;
$alldata[] = $list5;
$alldata[] = $list6;
$alldata[] = $list7;
$alldata[] = $list8;
$col = 65;
$c = 2;
for ($i=0; $i < count($alldata); $i++) { 
	$row = 2;
	$col2 = $col;
	$total = 0;
	
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店名稱');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row++,'進件');


	foreach ($alldata[$i] as $k => $v) {
		
		$col = $col2;
		$sql ="SELECT bStore,bStatus,bGroup,(SELECT bName FROM tBrand AS b WHERE b.bId=bBrand) AS brandName FROM tBranch WHERE bId='".$k."'";

		$rs = $conn->Execute($sql);

		if ($rs->fields['bStatus'] == 1 || ($rs->fields['bStatus'] == 2 &&$v > 0)) {
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bStore']);
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
			$total = $total+$v;

			if ($rs->fields['bGroup'] == 4) {
				$col++;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['brandName']);
			}
			$row++;
		}
		
	}

	$col = $col2;
	##顏色

	$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'共計');
	$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$total);
	if ($i == 2) { //葉寶桐 旁邊要有空一欄
		$col++;
	}
	$col = $col+$c;
}


##

##
$sheetIndex = 1;
$objPHPExcel->createSheet($sheetIndex) ;
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('游明桐');
$row = 1;

if (is_array($list1)) {
	foreach ($list1 as $k => $v) {
		$col = 65;
		$sql ="SELECT bStore,bStatus FROM tBranch WHERE bId='".$k."'";

		$rs = $conn->Execute($sql);
		if ($rs->fields['bStatus'] == 1 || ($rs->fields['bStatus'] == 2 && $v > 0)) {
			$i = 1;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bStore']);
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
			$row++;

			$col = 65;

			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店家編號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');

			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->getStartColor()->setARGB('BFBFBF');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');

			$row++;

			foreach ($data1[$k] as $key => $value) {
				$col = 65;
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i++));
				// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,);

				// $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
				$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row,$value['cCertifiedId'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);


				$brandcode[] = $value['brandcode'].str_pad($value['branch'],5,'0',STR_PAD_LEFT);
				$branch[] = $value['branchname'];
				if ($value['branch1'] > 0) {
					$brandcode[] = $value['brandcode1'].str_pad($value['branch1'],5,'0',STR_PAD_LEFT);
					$branch[]=$value['branchname1'];
				}

				if ($value['branch2'] > 0) {
					$brandcode[] = $value['brandcode2'].str_pad($value['branch2'],5,'0',STR_PAD_LEFT);
					$branch[]=$value['branchname2'];
				}

				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $brandcode));
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $branch));

				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,number_format($value['cTotalMoney']));

				if ($status=='已結案') { 
					$cEndDate = $value['cEndDate'];
				}
				else {
					$cEndDate = $value['cSignDate'];
				}
					//結案日期
					$cEndDate = dateformate($cEndDate);
					//進案日期
					$value['cApplyDate'] = dateformate($value['cApplyDate']);
					
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$cEndDate);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cApplyDate']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['scrivener']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['city'].$value['area'].$value['cAddr']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['status']);

				unset($branch); unset($brandcode);
				$row++;
			}
			$row++;
		}

		
		
	}

}

$sheetIndex++;


##
$objPHPExcel->createSheet($sheetIndex) ;
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('燊氐集團');
$row = 1;
if (is_array($list2)) {
	foreach ($list2 as $k => $v) {
		$col = 65;
		$sql ="SELECT bStore,bStatus FROM tBranch WHERE bId='".$k."'";

		$rs = $conn->Execute($sql);
		$i = 1;

		if ($rs->fields['bStatus'] == 1 || ($rs->fields['bStatus'] == 2 && $v > 0)) {
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bStore']);
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
			$row++;
			$col = 65;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店家編號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->getStartColor()->setARGB('BFBFBF');
			$row++;

			if (is_array($data2[$k])) {
				foreach ($data2[$k] as $key => $value) {
					$col = 65;
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i++));
					// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,);

					// $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
					$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row,$value['cCertifiedId'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

					$brandcode[] = $value['brandcode'].str_pad($value['branch'],5,'0',STR_PAD_LEFT);
					$branch[] = $value['branchname'];
					if ($value['branch1'] > 0) {
						$brandcode[] = $value['brandcode1'].str_pad($value['branch1'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname1'];
					}

					if ($value['branch2'] > 0) {
						$brandcode[] = $value['brandcode2'].str_pad($value['branch2'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname2'];
					}

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $brandcode));
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $branch));

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,number_format($value['cTotalMoney']));

					if ($status=='已結案') { 
						$cEndDate = $value['cEndDate'];
					}
					else {
						$cEndDate = $value['cSignDate'];
					}
						//結案日期
						$cEndDate = dateformate($cEndDate);
						//進案日期
						$value['cApplyDate'] = dateformate($value['cApplyDate']);
						
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$cEndDate);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cApplyDate']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['scrivener']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['city'].$value['area'].$value['cAddr']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['status']);

					unset($branch); unset($brandcode);
					$row++;
				}
			}
			
			$row++;
		}

		
		
	}
}

$sheetIndex++;
##

##
$objPHPExcel->createSheet($sheetIndex) ;
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('葉寶桐');
$row = 1;


if (is_array($list5)) {
	foreach ($list5 as $k => $v) {
		$col = 65;
		$sql ="SELECT bStore,bStatus FROM tBranch WHERE bId='".$k."'";

		$rs = $conn->Execute($sql);
		if ($rs->fields['bStatus'] == 1 || ($rs->fields['bStatus'] == 2 && $v > 0)) {
			$i = 1;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bStore']);
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
			$row++;
			$col = 65;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店家編號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->getStartColor()->setARGB('BFBFBF');
			$row++;
			if (is_array($data5[$k])) {
				foreach ($data5[$k] as $key => $value) {
					$col = 65;
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i++));
					// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,);

					// $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
					$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row,$value['cCertifiedId'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

					$brandcode[] = $value['brandcode'].str_pad($value['branch'],5,'0',STR_PAD_LEFT);
					$branch[] = $value['branchname'];
					if ($value['branch1'] > 0) {
						$brandcode[] = $value['brandcode1'].str_pad($value['branch1'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname1'];
					}

					if ($value['branch2'] > 0) {
						$brandcode[] = $value['brandcode2'].str_pad($value['branch2'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname2'];
					}

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $brandcode));
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $branch));

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,number_format($value['cTotalMoney']));

					if ($status=='已結案') { 
						$cEndDate = $value['cEndDate'];
					}
					else {
						$cEndDate = $value['cSignDate'];
					}
						//結案日期
						$cEndDate = dateformate($cEndDate);
						//進案日期
						$value['cApplyDate'] = dateformate($value['cApplyDate']);
						
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$cEndDate);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cApplyDate']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['scrivener']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['city'].$value['area'].$value['cAddr']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['status']);

					unset($branch); unset($brandcode);
					$row++;
				}
			}
			

			$row++;
		}

		
		
	}
}

$sheetIndex++;


##
$objPHPExcel->createSheet($sheetIndex) ;
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('幸福家');
$row = 1;
if (is_array($list6)) {
	foreach ($list6 as $k => $v) {
		$col = 65;
		$sql ="SELECT bStore,bStatus FROM tBranch WHERE bId='".$k."'";

		$rs = $conn->Execute($sql);
		if ($rs->fields['bStatus'] == 1 || ($rs->fields['bStatus'] == 2 && $v > 0)) {
			$i = 1;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bStore']);
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
			$row++;
			$col = 65;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店家編號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->getStartColor()->setARGB('BFBFBF');
			$row++;
			if (is_array($data6[$k])) {
				foreach ($data6[$k] as $key => $value) {
					$col = 65;
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i++));
					// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,);

					// $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
					$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row,$value['cCertifiedId'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

					$brandcode[] = $value['brandcode'].str_pad($value['branch'],5,'0',STR_PAD_LEFT);
					$branch[] = $value['branchname'];
					if ($value['branch1'] > 0) {
						$brandcode[] = $value['brandcode1'].str_pad($value['branch1'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname1'];
					}

					if ($value['branch2'] > 0) {
						$brandcode[] = $value['brandcode2'].str_pad($value['branch2'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname2'];
					}

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $brandcode));
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $branch));

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,number_format($value['cTotalMoney']));

					if ($status=='已結案') { 
						$cEndDate = $value['cEndDate'];
					}
					else {
						$cEndDate = $value['cSignDate'];
					}
						//結案日期
						$cEndDate = dateformate($cEndDate);
						//進案日期
						$value['cApplyDate'] = dateformate($value['cApplyDate']);
						
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$cEndDate);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cApplyDate']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['scrivener']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['city'].$value['area'].$value['cAddr']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['status']);

					unset($branch); unset($brandcode);
					$row++;
				}
			}
			

			$row++;
		}

		
		
	}
}

$sheetIndex++;

##
$objPHPExcel->createSheet($sheetIndex) ;
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('飛鷹');
$row = 1;
if (is_array($list7)) {
	foreach ($list7 as $k => $v) {
		$col = 65;
		$sql ="SELECT bStore,bStatus FROM tBranch WHERE bId='".$k."'";

		$rs = $conn->Execute($sql);
		if ($rs->fields['bStatus'] == 1 || ($rs->fields['bStatus'] == 2 && $v > 0)) {
			$i = 1;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bStore']);
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
			$row++;
			$col = 65;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店家編號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->getStartColor()->setARGB('BFBFBF');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
			$row++;
			if (is_array($data7[$k])) {
				foreach ($data7[$k] as $key => $value) {
					$col = 65;
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i++));
					// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,);

					// $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
					$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row,$value['cCertifiedId'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

					$brandcode[] = $value['brandcode'].str_pad($value['branch'],5,'0',STR_PAD_LEFT);
					$branch[] = $value['branchname'];
					if ($value['branch1'] > 0) {
						$brandcode[] = $value['brandcode1'].str_pad($value['branch1'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname1'];
					}

					if ($value['branch2'] > 0) {
						$brandcode[] = $value['brandcode2'].str_pad($value['branch2'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname2'];
					}

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $brandcode));
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $branch));

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,number_format($value['cTotalMoney']));

					if ($status=='已結案') { 
						$cEndDate = $value['cEndDate'];
					}
					else {
						$cEndDate = $value['cSignDate'];
					}
						//結案日期
						$cEndDate = dateformate($cEndDate);
						//進案日期
						$value['cApplyDate'] = dateformate($value['cApplyDate']);
						
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$cEndDate);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cApplyDate']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['scrivener']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['city'].$value['area'].$value['cAddr']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['status']);

					unset($branch); unset($brandcode);
					$row++;
				}
			}
			

			$row++;
		}

		
		
	}
}

$sheetIndex++;
##
$objPHPExcel->createSheet($sheetIndex) ;
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('群義');
$row = 1;
if (is_array($list8)) {
	foreach ($list8 as $k => $v) {
		$col = 65;
		$sql ="SELECT bStore,bStatus FROM tBranch WHERE bId='".$k."'";

		$rs = $conn->Execute($sql);
		if ($rs->fields['bStatus'] == 1 || ($rs->fields['bStatus'] == 2 && $v > 0)) {
			$i = 1;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$rs->fields['bStore']);
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col).$row,$v);
			$row++;
			$col = 65;
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'序號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'保證號碼');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'店家編號');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'仲介店名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'總價金');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'案件狀態日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'進案日期');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'地政士姓名');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'標的物座落');
			$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,'狀態');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('BFBFBF');
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':J'.$row)->getFill()->getStartColor()->setARGB('BFBFBF');
			$row++;
			if (is_array($data8[$k])) {
				foreach ($data8[$k] as $key => $value) {
					$col = 65;
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,($i++));
					// $objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,);

					// $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row, $value['cCertifiedId'],PHPExcel_Cell_DataType::TYPE_STRING); 
					$objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++).$row,$value['cCertifiedId'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

					$brandcode[] = $value['brandcode'].str_pad($value['branch'],5,'0',STR_PAD_LEFT);
					$branch[] = $value['branchname'];
					if ($value['branch1'] > 0) {
						$brandcode[] = $value['brandcode1'].str_pad($value['branch1'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname1'];
					}

					if ($value['branch2'] > 0) {
						$brandcode[] = $value['brandcode2'].str_pad($value['branch2'],5,'0',STR_PAD_LEFT);
						$branch[]=$value['branchname2'];
					}

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $brandcode));
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,implode(' ', $branch));

					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,number_format($value['cTotalMoney']));

					if ($status=='已結案') { 
						$cEndDate = $value['cEndDate'];
					}
					else {
						$cEndDate = $value['cSignDate'];
					}
						//結案日期
						$cEndDate = dateformate($cEndDate);
						//進案日期
						$value['cApplyDate'] = dateformate($value['cApplyDate']);
						
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$cEndDate);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['cApplyDate']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['scrivener']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['city'].$value['area'].$value['cAddr']);
					$objPHPExcel->getActiveSheet()->setCellValue(chr($col++).$row,$value['status']);

					unset($branch); unset($brandcode);
					$row++;
				}
			}
			

			$row++;
		}

		
		
	}
}

$sheetIndex++;

##
$objPHPExcel->setActiveSheetIndex(0);
function dateformate($txt){
	$txt = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$txt)) ;
	$tmp = explode('-',$txt) ;
				
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }
				
	$txt = $tmp[0].'/'.$tmp[1].'/'.$tmp[2] ;
	unset($tmp) ;

	return $txt;
}
// echo "<pre>";
// print_r($list2);
// echo "</pre>";
// die;

##
$_file = 'branch_sp.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$_file.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($objPHPExcel);
$writer->save('php://output');

exit ;


?>