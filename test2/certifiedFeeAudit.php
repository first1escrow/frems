<?php



function Audit($member_id,$certifiedId){
	global $conn;
	// echo 'a';
	$sql = 'SELECT pDep,pId FROM tPeopleInfo WHERE pId = "'.$member_id.'"';

	$rs = $conn->Execute($sql);
	$checkSales = 1; // 業務
	$col = '';

	if ($rs->fields['pDep'] == 4 || $member_id == 1) {
		$checkSales = 2;//主管
	}

	// if ($member_id == 6 ) {
	// 	$checkSales = 2;
	// }

	// print_r($certifiedId);
	// echo $checkSales;
	if (is_array($certifiedId)) {
		foreach ($certifiedId as $k => $v) {
			$str ='';
			if ($checkSales == 1) {
				$str = 'cStatus="1",cInspetor = "'.$member_id.'",cInspetorTime ="'.date('Y-m-d H:i:s').'"';
				// echo "http://first.twhg.com.tw/includes/escrow/sendLineMessage.php?cId=".$v."&cat=2";


				 file_get_contents("https://www.first1.com.tw/line/firstSales/includes/sendLineMessage.php?cId=".$v."&cat=2");
				// die;
			}elseif ($checkSales == 2) {
				//如果主管自己審核自己的案件，則幫他直接審核通過
				$sql = "SELECT cId FROM tContractIncome WHERE cCertifiedId = '".$v."' AND cInspetor =''";
				$rs = $conn->Execute($sql);

				if ($rs->fields['cId']) {
					$str = 'cInspetor = "'.$member_id.'",cInspetorTime ="'.date('Y-m-d H:i:s').'",';
				}
				$str .= 'cStatus="2",cInspetor2 = "'.$member_id.'",cInspetorTime2 ="'.date('Y-m-d H:i:s').'"';
			}

			$sql = "UPDATE tContractIncome SET ".$str." WHERE cCertifiedId = '".$v."'";
			// echo $sql."<br>";

			$conn->Execute($sql);

		}

	}else{
		$str ='';
		if ($checkSales == 1) {
			$str = 'cStatus="1",cInspetor = "'.$member_id.'",cInspetorTime ="'.date('Y-m-d H:i:s').'"';
			file_get_contents("https://www.first1.com.tw/line/firstSales/includes/sendLineMessage.php?cId=".$certifiedId."&cat=2");
		}elseif ($checkSales == 2) {
			//如果主管自己審核自己的案件，則幫他直接審核通過
			$sql = "SELECT cId FROM tContractIncome WHERE cCertifiedId = '".$certifiedId."' AND cInspetor =''";
			$rs = $conn->Execute($sql);

			if ($rs->fields['cId']) {
				$str = 'cInspetor = "'.$member_id.'",cInspetorTime ="'.date('Y-m-d H:i:s').'",';
			}
			$str .= 'cStatus="2",cInspetor2 = "'.$member_id.'",cInspetorTime2 ="'.date('Y-m-d H:i:s').'"';
		}

		$sql = "UPDATE tContractIncome SET ".$str." WHERE cCertifiedId = '".$certifiedId."'";
		// echo $sql."<br>";
		$conn->Execute($sql);
	}

}


// $member_id = ($sales['lpId'])?$sales['lpId']:$_SESSION['member_id'];






?> 
