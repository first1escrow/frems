<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

if ($_POST) {
	$act = ($_POST['act'] == 1)?$_POST['act']:'2';

	if ($_POST['scrivener']) {
		$sql = "UPDATE tScrivenerLevel SET sLock = '".$act."' WHERE sScrivener = '".$_POST['scrivener']."' AND sYear = '".($_POST['year']+1911)."'";
		// echo $sql."<br>";
		

		if ($conn->Execute($sql)) {
			echo "ok";
		}else{
			echo "error";
		}

	}else{
		$updateData = array();
		$check = 0;
		$sql = "SELECT sId FROM tScrivener WHERE MONTH(sBirthday)='".$_POST['month']."'";
		// echo $sql."<br>"; 
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			array_push($updateData, $rs->fields['sId']);


			$rs->MoveNext();
		}
		// print_r($updateData);
		foreach ($updateData as $k => $v) {
			$sql = "UPDATE tScrivenerLevel SET sLock = '".$act."' WHERE sYear='".($_POST['year']+1911)."' AND sScrivener = '".$v."'";
			// echo $sql."<br>";
			$conn->Execute($sql);
			$check++;
			
		}


		$sql = "SELECT * FROM tScrivenerBirthdayLock WHERE sDate = '".($_POST['year']+1911)."-".$_POST['month']."-01'";
		$rs = $conn->Execute($sql);

		if ($rs->EOF) {
			$sql = "INSERT INTO tScrivenerBirthdayLock (sDate,sLock) VALUES('".($_POST['year']+1911)."-".$_POST['month']."-01',".$act.")";
			$conn->Execute($sql);
		}


		

		if ($check == count($updateData)) {
			echo "ok";
		}else{
			echo "error";
		}

		unset($updateData);
		
	}
}
$conn->close();
die;
?>