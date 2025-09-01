<?php
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

$_POST = escapeStr($_POST) ;

$from = explode(',', $_POST['form']);

$count = 0;
if (is_array($from)) {
	foreach ($from as $k => $v) {
		$sql = "UPDATE tStoreFeedBackMoneyFrom SET sStatus = 1,sLock = 1 WHERE sId = '".$v."'";

		if ($conn->Execute($sql)) {
			$count++;
		}
		
	}

	if ($count == count($from)) {
		echo "發佈成功";
	}else{
		echo "錯誤";
	}

}else{
	echo '請勾選後再試';
}




?>