<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;


if ($_POST['category'] == 1) {
	$sql = "UPDATE tFeedBackMoneyReviewList SET fDelete = 1,fCaseFeedBackNote = '".$_POST['note']."' WHERE fId = '".$_POST['id']."'";

	$conn->Execute($sql);
}else{
	//先查詢是否有申請未審核過的
	$sql = "SELECT * FROM tFeedBackMoneyReview WHERE fCertifiedId = '".$_POST['cId']."' AND fFail = 0";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);

	if ($rs->EOF) {//沒資料
		
		//刪除暫存的刪除資料資料
		$sql = "DELETE FROM tFeedBackMoneyReviewList WHERE fCertifiedId = '".$_POST['cId']."' AND fRId = '0'";
		// echo $sql."<br>";
	
		$conn->Execute($sql);
		$_POST['data']['newotherFeedType'.$_POST['index']] = ($_POST['data']['newotherFeedType'.$_POST['index']] == 2)?'1':'2';//原資料是相反的，所以要調整

		//寫入新的資料
		$sql = "INSERT INTO
					tFeedBackMoneyReviewList
				SET
					fCertifiedId ='".$_POST['cId']."',
					fCategory = 5,
					fCaseFeedback = 0,
					fFeedbackTarget = '".$_POST['data']['newotherFeedType'.$_POST['index']]."',
					fFeedbackStoreId = '".$_POST['data']['newotherFeedstoreId'.$_POST['index']]."',
					fCaseFeedBackMoney = '".$_POST['data']['newotherFeedMoney'.$_POST['index']]."',
					fCaseFeedBackNote = '".$_POST['data']['newotherFeedMoneyNote'.$_POST['index']]."',
					fCaseFeedBackMark = '".$_POST['data']['oId'.$_POST['index']]."',
					fDelete = 1";
		// echo $sql."<br>";
		$conn->Execute($sql);
	}
	
}



?>