<?php
include_once '../../openadodb.php' ;
// include_once '../../session_check.php' ;

$_POST = escapeStr($_POST) ;

// echo $_POST['cat'];
switch ($_POST['cat']) {
	case 'recall':
		 getRecall($_POST['scrivner'],$_POST['brand']);
		break;
	case 'brdScr':
			getBrandScrList($_POST['scrivner']);
	 	break;
	case 'recall2':
		getRecall2($_POST['scrivner']);
		break;
	default:
		# code...
		break;
}

function getRecall($sId,$brand){

	global $conn;

	$sql = "SELECT sRecall AS recall,sReacllBrand AS reacllBrand FROM tScrivenerFeedSp WHERE sScrivener ='".$sId."' AND sBrand ='".$brand."' AND sDel = 0;";

	$rs = $conn->Execute($sql);

	$data = $rs->fields;

	echo json_encode($data);
}

function getBrandScrList($sId){
	global $conn;

	$sql = "SELECT sReacllBrand,sRecall,(SELECT bName FROM tBrand WHERE bId = sBrand) AS BrandName FROM tScrivenerFeedSp WHERE sScrivener ='".$sId."' AND sDel =0";

	$rs = $conn->Execute($sql);
	// echo $sql;
	while (!$rs->EOF) {
		# code...
		$FeedSp[] = $rs->fields['BrandName'].":".$rs->fields['sReacllBrand']."%(仲介)、".$rs->fields['sRecall']."%(地政士)";
		$rs->MoveNext();
	}

	echo @implode(';', $FeedSp);
}

//新增合約書用
function getRecall2($sId) {
	global $conn;
	$sql = "SELECT sReacllBrand,sRecall,(SELECT bName FROM tBrand WHERE bId = sBrand) AS BrandName FROM tScrivenerFeedSp WHERE sScrivener ='".$sId."' AND sDel =0";
	// $i = 0;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		# code...
		$FeedSp[] = $rs->fields;
		// $i++;
		$rs->MoveNext();
	}

	if (is_array($FeedSp)) {
		echo json_encode($FeedSp);
	}else{
		echo 'error';
	}
	
}

?>