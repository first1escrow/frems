<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';

use First1\V1\PayByCase\PayByCase;

$_POST = escapeStr($_POST) ;

$id = $_POST['id'];

if ($id) {
	$sql= "SELECT fStatus, fCertifiedId FROM tFeedBackMoneyReview WHERE fId = '".$id."'";
	$rs  = $conn->Execute($sql);

	if ($rs->fields['fStatus'] == 0) {
		$paybycase = new PayByCase(new first1DB);

		$sql = "UPDATE tFeedBackMoneyReview SET fFail = 1,tFailName = '".$_SESSION['member_id']."' WHERE fId ='".$id."'";
		$conn->Execute($sql);

		$sql = "UPDATE  tFeedBackMoneyReviewList SET fDelete = 1 WHERE fRId ='".$id."'";
		$conn->Execute($sql);

		$paybycase->salesConfirmList($rs->fields['fCertifiedId']);

		echo 'ok';
	}else{
		echo 'fail';
	}

}else{
	echo 'fail(1)';
}
?>
