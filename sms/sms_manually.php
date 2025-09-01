<?php
include_once '../openadodb.php' ;
include_once 'sms_function_manually.php' ;
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;




$save = $_POST['save'];
$mobile = $_POST['mobile'];
$txt = $_POST['txt'];
$cat = $_POST['cat'];
$cId = $_POST['cId'];
$sms = new SMS_Gateway();



if ($save) {
// print_r($_POST);
	switch ($cat) {
		case 'manually':
			$mobile = str_replace(array('\r','\n'), '', $mobile);
	
			// $tmp = $sms->manual_send($mobile,$txt,'n');
			$txt .= "\r\n";
			$sms->manual_send($mobile,$txt,'y',$_SESSION['member_name']);
			

			$smarty->assign('mobile' , $mobile);
			$smarty->assign('post_txt' , $txt);
			break;
		case 'cheque':
			$sql = "SELECT
				cc.cEscrowBankAccount,
				cs.cCertifiedId,
				cs.cScrivener,
				cr.cBranchNum
			FROM
				tContractCase AS cc,
				tContractRealestate AS cr,
				tContractScrivener AS cs
			WHERE
				cc.cCertifiedId = cr.cCertifyId
				AND cc.cCertifiedId = cs.cCertifiedId
				AND cr.cCertifyId = '".$cId."'";

			$rs = $conn->Execute($sql);

			$total = $rs->RecordCount();
			// echo "<pre>";
			// print_r($_POST['mobile2']);
			// echo "</pre>";

			if ($_POST['mobile2']) {
				//($pid , $sid, $bid, $target,$mobile,$stxt='',$ok='n')
				$array = $sms->send($rs->fields["cEscrowBankAccount"] , $rs->fields["cScrivener"], $rs->fields["cBranchNum"], $cat,$_POST['mobile2'],$txt,'y');
				
				foreach ($array['sms'] as $k => $v) {
					$tmp[] = $v['mName'].$v['mMobile'];
				}
				

				$mobile= @implode(',', $tmp);	
				$smarty->assign('mobile' , $mobile);
				$smarty->assign('post_txt' , $txt);
			}
			
			// print_r($array);
			break;
		default:
			# code...
			break;
	}
	
	
}





$smarty->display('sms_manually.inc.tpl', '', 'other');
?>