<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php';
include_once dirname(dirname(dirname(__FILE__))).'/class/lineMessage.php';
include_once dirname(dirname(dirname(__FILE__))).'/tracelog.php' ;


$_REQUEST = escapeStr($_REQUEST) ;

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_REQUEST), '發送未收足通知') ;


$cId = $_REQUEST['cId'];
$cat = $_REQUEST['cat'];//


//實習業務
$TraineeZip = array();
$sql = "SELECT zZip,zTrainee,(SELECT pId FROM tPeopleInfo WHERE pTest =zTrainee) AS sales FROM tZipArea WHERE zTrainee != '' AND zTrainee != 2";//2是特殊用 立寰限定
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$TraineeZip[$rs->fields['sales']][] = $rs->fields['zZip'];
	// array_push($TraineeZip, $rs->fields['zZip']);

	$rs->MoveNext();
}




$data = array();
$line = new LineMsg();

$sql = "SELECT
			cc.cCertifiedId,
			cs.cScrivener,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cr.cBranchNum3
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId

		WHERE
			cc.cCertifiedId = '".$cId."'";
				
$rs = $conn->Execute($sql);
$caseData = $rs->fields;

// print_r($caseData);
// die;
switch ($cat) {
	case 1:
		$store = array();
		$sql = "SELECT bId fROM tBranch WHERE bCategory = 2 AND bBrand = 1";
		
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			array_push($store, $rs->fields['bId']);
			$rs->MoveNext();
		}
		##


		$sales = array();
		$salesId = array();
		$salesId = array_merge($salesId,getScrivenerSales($caseData['cScrivener']));

		$checkTwStore = 0;//1:直營，直營給政耀

		if ($caseData['cBranchNum'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum']));

			if (in_array($caseData['cBranchNum'], $store)) {
				$checkTwStore = 1;
			}
		}

		if ($caseData['cBranchNum1'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum1']));

			if (in_array($caseData['cBranchNum1'], $store)) {
				$checkTwStore = 1;
			}
		}

		if ($caseData['cBranchNum2'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum2']));

			if (in_array($caseData['cBranchNum2'], $store)) {
				$checkTwStore = 1;
			}
		}

		if ($caseData['cBranchNum3'] > 0) {
			$salesId = array_merge($salesId,getBranchSales($caseData['cBranchNum3']));

			if (in_array($caseData['cBranchNum3'], $store)) {
				$checkTwStore = 1;
			}
		}

		foreach ($salesId as $v) {
			if ($v != '') {
				$sales[$v] = $v;
			}
			
		}
		
		
		if (!empty($sales)) {

			if ($checkTwStore == 1) { //直營給政耀
				$data = array();
				$v = enCrypt('lineId=Ue3a988aae4cc2d611cd4b4ed56420d85&s=SC0224&c=O&cId='.$cId);
				$data['lineId'] = 'Ue3a988aae4cc2d611cd4b4ed56420d85';
				$data['btn_url'] = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v='.$v;
				$data['title'] = '履保費未收足通知';
				$data['text'] = '保證號碼:'.$cId.'，請審核';
				$data['btn_label'] = '點我審核';

				$line->sendFlexTemplateMsg($data);
			}else{
				$sql = "SELECT lLineId,lTargetCode FROM tLineAccount WHERE lpId IN(".implode(',', $sales).")";
				$rs = $conn->Execute($sql);
				$total=$rs->RecordCount();
				while (!$rs->EOF) {
					// $rs->fields['lLineId'] = 'U4b14569b842b0d5d4613b77b94af02b6';
					// $cId = '100015483';
					$check = 1;
					if ($rs->fields['lLineId'] == 'U4f65df67029fcc7f814ea1d66b08ca41' && $total == 1) { //基隆指幼校成，所以只有一個業務的時候給正要
						$rs->fields['lLineId'] = 'Ue3a988aae4cc2d611cd4b4ed56420d85';


					}elseif ($rs->fields['lLineId'] == 'U4f65df67029fcc7f814ea1d66b08ca41') {
						$check = 0;
					}

					//certifiedFeeDetail.php?v=848a2ba9be8f8bdca5ff9bb02c94d8eb7006e692d1621a0dc77e6811f1f89ab5a5a2bb61fa11a47053cafbe40e4802103e4320d71a001deb744b6eaa27b5157fe3c64e
					//$str = 'lineId='.$lineId.'&s='.$s.'&c='.$c.'&cId='.$rs->fields['cCertifiedId'] ;
					//$query = 'lineId='.$userId.'&s='.$ide['lTargetCode'].'&c='.$ide['lIdentity'].'&lat='.$arr['lat'].'&lng='.$arr['lng'] ;	
					// $rs->fields['lLineId'] = 'U4b14569b842b0d5d4613b77b94af02b6';
					if ($check == 1) {
						$v = enCrypt('lineId='.$rs->fields['lLineId'].'&s='.$rs->fields['lTargetCode'].'&c='.$rs->fields['lIdentity'].'&cId='.$cId);
						$data['lineId'] = $rs->fields['lLineId'];
						$data['btn_url'] = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v='.$v;
						$data['title'] = '履保費未收足通知';
						$data['text'] = '保證號碼:'.$cId.'，請填寫原因並審核';
						$data['btn_label'] = '點我填寫';

						$line->sendFlexTemplateMsg($data);
						$tlog->updateWrite($_SESSION['member_id'], json_encode($data), '發送未收足通知') ;
					}
					

					
					

					$rs->MoveNext();
				}
			}

			
			// print_r($sales);
		}
		unset($store);
		break;
	case 2:
		$v = enCrypt('lineId=U4b14569b842b0d5d4613b77b94af02b6&s=SC0224&c=O&cId='.$cId);
				$data['lineId'] = 'U4b14569b842b0d5d4613b77b94af02b6';
				$data['btn_url'] = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v='.$v;
				$data['title'] = '履保費未收足通知';
				$data['text'] = '保證號碼:'.$cId.'，請審核';
				$data['btn_label'] = '點我審核';

				
				

		$v = enCrypt('lineId=Ue3a988aae4cc2d611cd4b4ed56420d85&s=SC0224&c=O&cId='.$cId);
				$data['lineId'] = 'Ue3a988aae4cc2d611cd4b4ed56420d85';
				$data['btn_url'] = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v='.$v;
				$data['title'] = '履保費未收足通知';
				$data['text'] = '保證號碼:'.$cId.'，請審核';
				$data['btn_label'] = '點我審核';

				$line->sendFlexTemplateMsg($data);
		break;
	default:
		# code...
		break;
}

function getScrivenerSales($id){
	global $conn;
	global $TraineeZip;

	$sales = array();

	//實習業務
	
	if (is_array($TraineeZip)) {
		foreach ($TraineeZip as $key => $value) {
			$sql = "SELECT sId FROM tScrivener WHERE sCpZip1 IN (".@implode(',', $value).") AND sId = '".$id."'";
			$rs = $conn->Execute($sql);
			
			if ($rs->RecordCount() > 0) {
				array_push($sales, $key);
			}
		}
		
	}
	

	//地政士業務
	$sql = "SELECT sSales FROM tScrivenerSales WHERE sScrivener = '".$id."'";
	// echo $sql;
	$rs = $conn->Execute($sql);
	
	while (!$rs->EOF) {
		array_push($sales, $rs->fields['sSales']);

		$rs->MoveNext();
	}

	return $sales;

}
function getBranchSales($id){
	global $conn;
	global $TraineeZip;

	$sales = array();
	if (is_array($TraineeZip)) {
		foreach ($TraineeZip as $key => $value) {
			$sql = "SELECT bId FROM tBranch WHERE bZip IN (".@implode(',', $value).") AND bId = '".$id."'";
			$rs = $conn->Execute($sql);
			
			if ($rs->RecordCount() > 0) {
				array_push($sales, $key);
			}
		}
		
	}

	//仲介業務
	$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$id."'";
	$rs = $conn->Execute($sql);
	
	while (!$rs->EOF) {
		array_push($sales, $rs->fields['bSales']);

		$rs->MoveNext();
	}
	return $sales;
}

?>