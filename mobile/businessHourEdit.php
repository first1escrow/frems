<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_GET = escapeStr($_GET) ;
// $_POST = escapeStr($_POST) ;

$id = empty($_GET['id']) ? $_POST['id'] : $_GET['id'];
$cat = empty($_GET['cat']) ? $_POST['cat'] : $_GET['cat']; //1add 2modify 3delete
// echo $cat ;
if ($_POST['cat']) {

	$tmp = explode('-', $_POST['DateStart']);
	$day = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];

	$dateStart =  $day." ".$_POST['DateStartHour'].":".$_POST['DateStartMin'].":00";

	$dateEnd = $day." ".$_POST['DateEndHour'].":".$_POST['DateEndMin'].":00";
	
	unset($tmp);

	
	if ($_POST['cat'] == 2) { //修改
		
		if (checkAdd($day)) {
			$sql = "UPDATE 
						tOpen
					SET
						oStart = '".$dateStart."',
						oEnd = '".$dateEnd."'
					WHERE
						oId = '".$id."'
					";
			// echo $sql;
			$conn->Execute($sql);
		}else{
			echo "<script>alert('日期已新增過');parent.$.fn.colorbox.close();</script>";
		}
	}elseif ($_POST['cat'] == 1) { //新增

		if (checkAdd($day)) {
			$sql = "INSERT INTO
				tOpen 
				(
					oStart,
					oEnd
					
				)VALUES(
					'".$dateStart."',
					'".$dateEnd."'
				) ";
			$conn->Execute($sql);
			$id = $conn->Insert_ID();
		}else{
			echo "<script>alert('日期已新增過');parent.$.fn.colorbox.close();</script>";
		}

		
	}elseif ($_POST['cat'] == 3) {
		$sql = "UPDATE
					tOpen
				SET
					oDel = 1
				WHERE
					oId = '".$id."'";
				
		$conn->Execute($sql);

		echo "<script>parent.$.fn.colorbox.close();</script>";
	}

	// echo $sql;
}

function checkAdd($day){
	global $conn;
	
	$s = $day." 00:00:00";
	$e = $day." 23:59:59";

	$sql = "SELECT
				*
			FROM
				tOpen
			WHERE
				oStart >'".$s."'
				AND oEnd < '".$e."'";
	

	$rs = $conn->Execute($sql);

	$total=$rs->RecordCount();

	if ($total > 0) {
		return false; //已經新增過了
	}else{
		return true; //可以新增
	}
}

$sql = "SELECT
			Year(oStart)-1911 AS sYear,
			LPAD(Month(oStart),2,'0') AS sMonth,
			LPAD(Day(oStart),2,'0') AS sDay,
			LPAD(Hour(oStart),2,'0') AS sHour,
			LPAD(MINUTE(oStart),2,'0') AS sMinutes,
			LPAD(Second(oStart),2,'0') AS sSecond,
			Year(oEnd)-1911 AS oYear,
			LPAD(Month(oEnd),2,'0') AS sMonth,
			LPAD(Day(oEnd),2,'0') AS oDay,
			LPAD(Hour(oEnd),2,'0') AS oHour,
			LPAD(MINUTE(oEnd),2,'0') AS oMinutes,
			LPAD(Second(oEnd),2,'0') AS oSecond
		FROM
			tOpen
		WHERE
			 oId ='".$id."'";
// echo $sql;
$rs = $conn->Execute($sql);
if ($rs->fields['sYear'] != '-1911' && $rs->fields['sYear'] != '') {
	$rs->fields['sDate'] = $rs->fields['sYear']."-".$rs->fields['sMonth']."-".$rs->fields['sDay'];
}
// echo $data['sDate'];
// if ($rs->fields['oYear']) {
// 	$data['oDate'] = $rs->fields['oYear']."-".$rs->fields['oMonth']."-".$rs->fields['oDay'];
// }

for ($i=0; $i < 25 ; $i++) { 
	$menuHour[str_pad($i,2,'0',STR_PAD_LEFT)] = str_pad($i,2,'0',STR_PAD_LEFT);
}

###
$smarty->assign('data',$rs->fields);
$smarty->assign('menuHour',$menuHour);
$smarty->assign('menuMinutes',array('00'=>'00','10'=>10,'20'=>20,'30'=>30,'40'=>40,'50'=>50));
$smarty->assign('cat',$cat);
$smarty->display('businessHourEdit.inc.tpl', '', 'mobile');
?>
