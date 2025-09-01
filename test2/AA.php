<?php
include_once dirname(dirname(__FILE__)).'/openadodb.php';
include_once dirname(dirname(__FILE__)).'/class/lineMessage.php';
include_once dirname(dirname(__FILE__)).'/tracelog.php' ;
$sales = array(25,90);
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
				echo 'CCCC';
				//certifiedFeeDetail.php?v=848a2ba9be8f8bdca5ff9bb02c94d8eb7006e692d1621a0dc77e6811f1f89ab5a5a2bb61fa11a47053cafbe40e4802103e4320d71a001deb744b6eaa27b5157fe3c64e
				//$str = 'lineId='.$lineId.'&s='.$s.'&c='.$c.'&cId='.$rs->fields['cCertifiedId'] ;
				//$query = 'lineId='.$userId.'&s='.$ide['lTargetCode'].'&c='.$ide['lIdentity'].'&lat='.$arr['lat'].'&lng='.$arr['lng'] ;	
				if ($check == 1) {
				$v = enCrypt('lineId='.$rs->fields['lLineId'].'&s='.$rs->fields['lTargetCode'].'&c='.$rs->fields['lIdentity'].'&cId='.$cId);
				$data['lineId'] = $rs->fields['lLineId'];
				$data['btn_url'] = 'https://www.first1.com.tw/line/firstSales/certifiedFeeDetail.php?v='.$v;
				$data['title'] = '履保費未收足通知';
				$data['text'] = '保證號碼:'.$cId.'，請填寫原因並審核';
				$data['btn_label'] = '點我填寫';
				print_r($data);
				}

				
				
				

				// $line->sendFlexTemplateMsg($data);
				

				$rs->MoveNext();
			}