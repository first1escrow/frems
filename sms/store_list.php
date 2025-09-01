<?php
// require_once '../bank/Classes/PHPExcel.php' ;
// require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;


//地政士OR 仲介過期

// $objPHPExcel = new PHPExcel();
// //Set properties 設置文件屬性
// $objPHPExcel->getProperties()->setCreator("第一建經");
// $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
// $objPHPExcel->getProperties()->setTitle("第一建經");
// $objPHPExcel->getProperties()->setSubject("報表");
// $objPHPExcel->getProperties()->setDescription("第一建經報表");

//指定目前工作頁
// $objPHPExcel->setActiveSheetIndex(0);


switch ($_POST['check']) {
	case 'b1'://店編、品牌、店名
		$data = branch_case($conn,'1');
		$title = 'b';
		break;
	case 'b2':
		$data = branch_case($conn,'2');
		$title = 'b';
		break;
	case 's1'://編號、姓名、事務所名稱
		$data = scrivener_case($conn,'1');
		$title = 's';
		break;
	case 's2':
		$data = scrivener_case($conn,'2');
		$title = 's';
		break;
	
}




// $row = 2;
// for ($i=0; $i < count($data); $i++) { 
	
// 	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$data[$i]['code']);
// 	$objPHPExcel->getActiveSheet()->setCellValue('B'.$row,$data[$i]['name']);
// 	$objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$data[$i]['store']);
// 	$row++;
// }

##
// $_file = 'store.xlsx' ;

// header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// header("Cache-Control: no-store, no-cache, must-revalidate");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
// header('Content-type:application/force-download');
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename='.$_file);

// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save("php://output");

// exit ;


//店編、品牌、店名
function branch_case($conn,$str)
{
	$today = date("Y-m-d");

	if ($str == 1) {
		$query = "AND b.bStatusDateEnd < '".$today."' AND b.bStatusDateEnd !='0000-00-00'";
	}elseif ($str == 2) {
		$query = "AND b.bStatusDateStart = '0000-00-00' AND b.bStatusDateEnd ='0000-00-00'";
	}

	$sql = "SELECT 
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as code,
				(SELECT bName FROM tBrand AS c WHERE c.bId = b.bBrand) AS name,
				bStore AS store
			FROM
				tBranch AS b 
			WHERE b.bStatus = 3 ".$query." ORDER BY b.bId ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		$list[] = $rs->fields;

		$rs->MoveNext();
	}

	return $list;
}

//編號、姓名、事務所名稱
function scrivener_case($conn,$str){

	$today = date("Y-m-d");

	if ($str == 1) {
		$query = "AND s.sStatusDateEnd < '".$today."' AND s.sStatusDateEnd !='0000-00-00'";
	}elseif ($str == 2) {
		$query = "AND s.sStatusDateStart = '0000-00-00' AND s.sStatusDateEnd ='0000-00-00'";
	}

	$sql = "SELECT 	 
				CONCAT('SC',LPAD(s.sId,4,'0')) as code,
				sName AS name,
				sOffice AS store
				
			FROM
				tScrivener AS s WHERE s.sStatus = 3 ".$query." ORDER BY s.sId ASC";
	// echo $sql;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		
		$list[] = $rs->fields;


		$rs->MoveNext();
	}

	return $list;
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="180" />
<title></title>
<script src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	window.resizeTo('460','700') ;
</script>
<style type="text/css">
	
	table th{
		background-color: #FF8C8C;
		padding-bottom: 5px;
		border: 1px solid #FFFFFF;
	}
	table td{
		background-color: #FFDEDE;
		padding-bottom: 5px;
		border: 1px solid #FFFFFF;
	}
</style>
</head>
<body>
	<table cellspacing="0" cellpadding="0">
		<?php
		if ($title == 'b') {
				echo "<tr>
						<th width='10%'>店編</th>
						<th width='20%'>品牌</th>
						<th width='40%'>店名</th>
					</tr>";
				
		}elseif ($title == 's') {
			//編號、姓名、事務所名稱
			echo "<tr>
						<th width='10%'>編號</th>
						<th width='10%'>姓名</th>
						<th width='40%'>事務所名稱</th>
					</tr>";
		}

		for ($i=0; $i < count($data); $i++) { 
			echo "<tr>
						<td>".$data[$i]['code']."</td>
						<td>".$data[$i]['name']."</td>
						<td>".$data[$i]['store']."</td>
					</tr>";
		}
		?>

	</table>
</body>
</html>