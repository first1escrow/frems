<?php
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../../sms/sms_function.php';


$_POST = escapeStr($_POST) ;
$vr = $_POST['vr'];
$radiokind = $_POST['radiokind'];

// print_r($_POST);


$sms = new SMS_Gateway();

if (is_numeric($_POST['vr'])) {
	$_all = array(); $data = array(); $scrivener = array(); $branchArr = array(); $branchArr2 = array(); $branchArr3 = array(); 
	$branch = array(); $branch1= array(); $branch2 = array(); $branch3  = array(); $branch4 = array(); 
	$buyer = array();$owner = array(); $buyerAgent = array(); $buyerAgent2 = array(); $ownerAgent = array(); 
	$ownerAgent2 = array();

	$branchAccounting  = array(); $branchAccounting1  = array(); $branchAccounting2  = array(); $branchAccounting3 = array();
	$caseData = $sms->getContractData($vr);

	$scrivener = $sms->getsScrivenerMobile($vr,$caseData[0]['cScrivener']);//地政士
	//仲介1
	$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum'],'店長'); // 取得店發送簡訊對象
	$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum'],'店東'); // 取得店發送簡訊對象
	$branchArr3 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum'],'經紀人'); // 取得店發送簡訊對象
	// print_r($branchArr3);
	$branch = array_merge($branchArr,$branchArr2,$branchArr3);
	unset($branchArr);unset($branchArr2);unset($branchArr3);

	//服務費
	$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum'],'會計'); // 取得店發送簡訊對象
	$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum'],'秘書'); // 取得店發送簡訊對象
	$branchAccounting =  array_merge($branchArr,$branchArr2);
	unset($branchArr);unset($branchArr2);

	//仲介2
	if ($caseData[0]['cBranchNum1'] > 0) {
		$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum1'],'店長'); // 取得店發送簡訊對象
		$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum1'],'店東'); // 取得店發送簡訊對象
		$branchArr3 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum1'],'經紀人'); // 取得店發送簡訊對象
		$branch1 = array_merge($branchArr,$branchArr2,$branchArr3);


		unset($branchArr,$branchArr2,$branchArr3);

		//服務費
		$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum1'],'會計'); // 取得店發送簡訊對象
		$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum1'],'秘書'); // 取得店發送簡訊對象
		$branchAccounting1 =  array_merge($branchArr,$branchArr2);
		unset($branchArr);unset($branchArr2);
	}

	//仲介3
	if ($caseData[0]['cBranchNum2'] > 0) {
		$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum2'],'店長'); // 取得店發送簡訊對象
		$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum2'],'店東'); // 取得店發送簡訊對象
		$branchArr3 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum2'],'經紀人'); // 取得店發送簡訊對象
		$branch2 = array_merge($branchArr,$branchArr2,$branchArr3);
		unset($branchArr,$branchArr2,$branchArr3);


		//服務費
		$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum2'],'會計'); // 取得店發送簡訊對象
		$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum2'],'秘書'); // 取得店發送簡訊對象
		$branchAccounting2 =  array_merge($branchArr,$branchArr2);
		unset($branchArr);unset($branchArr2);
	}

	//仲介4
	if ($caseData[0]['cBranchNum3'] > 0) {
		$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum3'],'店長'); // 取得店發送簡訊對象
		$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum3'],'店東'); // 取得店發送簡訊對象
		$branchArr3 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum3'],'經紀人'); // 取得店發送簡訊對象
		$branch3 = array_merge($branchArr,$branchArr2,$branchArr3);
		unset($branchArr,$branchArr2,$branchArr3);


		//服務費
		$branchArr = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum3'],'會計'); // 取得店發送簡訊對象
		$branchArr2 = $sms->getsBranchMobile($vr,$caseData[0]['cBranchNum3'],'秘書'); // 取得店發送簡訊對象
		$branchAccounting3 =  array_merge($branchArr,$branchArr2);
		unset($branchArr);unset($branchArr2);
	}

	//仲介4
	//買方
	$bCount = 0;
	$buyer[$bCount]["mName"] = $caseData[0]["b_name"]	; 					//主買方姓名
	$buyer[$bCount]["mMobile"] = $caseData[0]["b_mobile"]	;				//主買方手機
	$buyer[$bCount]["tTitle"] = '買方';
	$bCount++;
	//主買方其他電話
	$other_phone = $sms->get_phone(1,$vr);

	for ($i=0; $i < count($other_phone); $i++) { 
		$buyer[$bCount]["mName"] = $caseData[0]["b_name"]	; 					
		$buyer[$bCount]["mMobile"] = $other_phone[$i]['cMobileNum']	;
		$buyer[$bCount]["tTitle"] = '買方';
		$bCount++ ;
	}
	unset($other_phone);
	##
				
	//其他買方
	$_other_buyers = $sms->get_others($vr,'1') ;
	//print_r($_other_owners) ;
	for ($i = 0 ; $i < count($_other_buyers) ; $i ++) {
		$buyer[$bCount]["mName"] = $_other_buyers[$i]['cName'] ;					//其他買方姓名
		$buyer[$bCount]["mMobile"] = $_other_buyers[$i]['cMobileNum'] ;			//其他買方手機
		$buyer[$bCount]["tTitle"] = '買方';
		$bCount++ ;	
	}
	unset($_other_buyers);

	//6買方代理人
	$_other_buyers = $sms->get_others($vr,'6') ;
	
	for ($i = 0 ; $i < count($_other_buyers) ; $i ++) {
		$buyerAgent[$i]["mName"] = $_other_buyers[$i]['cName'] ;					//6買方代理人姓名
		$buyerAgent[$i]["mMobile"] = $_other_buyers[$i]['cMobileNum'] ;			//6買方代理人手機
		$buyerAgent[$i]["tTitle"] = '買方代理人';

		
	}
	unset($_other_buyers);
	

	//買方經紀人
	$other_phone = $sms->get_phone(3,$vr);
	for ($i=0; $i < count($other_phone); $i++) { 
		$buyerAgent2[$i]["mName"] = $other_phone[$i]["cName"]	; 					
		$buyerAgent2[$i]["mMobile"] = $other_phone[$i]['cMobileNum']	;
		$buyerAgent2[$i]["tTitle"] = '買方經紀人';
	}

	//主賣方
	$oCount = 0 ;							
	$owner[$oCount]["mName"] = $caseData[0]["o_name"] ; 					//賣方姓名
	$owner[$oCount]["mMobile"] = $caseData[0]["o_mobile"] ;				//賣方手機
	$owner[$oCount]["tTitle"] = '賣方';
	$oCount++ ;

	//主賣方其他電話
	$other_phone = $sms->get_phone(2,$vr);

	for ($i=0; $i < count($other_phone); $i++) { 
		$owner[$oCount]["mName"] = $caseData[0]["o_name"]	; 					
		$owner[$oCount]["mMobile"] = $other_phone[$i]['cMobileNum']	;
		$owner[$oCount]['tTitle'] = '賣方';
		$oCount ++ ;
	}

	unset($other_phone);
	
	//其他賣方$pid
	$_other_owners = $sms->get_others($vr,'2') ;
	//print_r($_other_owners) ;
	for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
		$owner[$oCount]["mName"] = $_other_owners[$i]['cName'] ;					//其他賣方姓名
		$owner[$oCount]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他賣方手機
		$owner[$oCount]['tTitle'] = '賣方';
		$oCount ++ ;
	}
	unset($_other_owners);
	##
	//7賣方代理人
	$_other_owners = $sms->get_others($vr,'7') ;
				//print_r($_other_owners) ;
	for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
		$ownerAgent[$i]["mName"] = $_other_owners[$i]['cName'] ;					//7賣方代理人姓名
		$ownerAgent[$i]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//7賣方代理人手機
		$ownerAgent[$i]['tTitle'] = '賣方代理人';
		// $oCount ++ ;
	}
	unset($_other_owners);
				##

	//賣方經紀人
	$other_phone = $sms->get_phone(4,$vr);
	for ($i=0; $i < count($other_phone); $i++) { 
		$ownerAgent2[$i]["mName"] = $other_phone[$i]["cName"]	; 					
		$ownerAgent2[$i]["mMobile"] = $other_phone[$i]['cMobileNum']	;
		$ownerAgent2[$i]["tTitle"] = '賣方經紀人';
	}


	// $_all = array_merge($_all, $scrivener,$branch,$branch2,$branch3,$buyerAgent);
	
	$_all = array_merge($_all,$scrivener,$branch,$branchAccounting,$branch1,$branchAccounting1,$branch2,$branchAccounting2,$branch3,$branchAccounting3,$buyer,$buyerAgent,$buyerAgent2,$owner,$ownerAgent,$ownerAgent2);
	switch ($radiokind) {
		case '扣繳稅款': //地政士、店東、店長、買方經紀人、賣方經紀人
		
			$data = array_merge($data,$scrivener,$branch,$branch1,$branch2,$buyerAgent2,$ownerAgent2); //
			// print_r($data);
			// die;

			break;
		case '點交'://地政士、店東、店長、買方、買方代理人、買方經紀人、賣方、賣方代理人、賣方經紀人 (服務費:店會計、店秘書)
			$data = array_merge($data,$scrivener,$branch,$branchAccounting,$branch1,$branchAccounting1,$branch2,$branchAccounting2,$branch3,$branchAccounting3,$buyer,$buyerAgent,$buyerAgent2,$owner,$ownerAgent,$ownerAgent2);

			break;
		case '解除契約'://地政士、店東、店長、買方、買方代理人、買方經紀人、賣方、賣方代理人、賣方經紀人 
			$data = array_merge($scrivener,$branch,$branch1,$branch2,$branch3,$buyer,$buyerAgent,$buyerAgent2,$owner,$ownerAgent,$ownerAgent2);
			break;
		case '賣方仲介服務費': //仲介服務費: 地政士(地政士只撈取案件設定的服務費寄送對象tContractScrivener. cSend2)、店東、店長、店會計、店秘書
			unset($scrivener);
			$scrivener = $sms->getsScrivenerMobile2($vr,$caseData[0]['cScrivener']);
			// $data = array_merge($data,$scrivener);
			// if ($caseData[0]['cServiceTarget'] == 1 || $caseData[0]['cServiceTarget'] == 2) {
			// 	$data = array_merge($data,$branch,$branchAccounting);
			// }

			// if (($caseData[0]['cServiceTarget1'] == 1 || $caseData[0]['cServiceTarget1'] == 2) && $caseData[0]['cBranchNum1'] > 0) {
			// 	$data = array_merge($data,$branch1,$branchAccounting1);
			// }

			// if (($caseData[0]['cServiceTarget2'] == 1 || $caseData[0]['cServiceTarget2'] == 2) && $caseData[0]['cBranchNum2'] > 0) {
			// 	$data = array_merge($data,$branch2,$branchAccounting2);
			// }

			// if (($caseData[0]['cServiceTarget3'] == 1 || $caseData[0]['cServiceTarget3'] == 2) && $caseData[0]['cBranchNum3'] > 0) {
			// 	$data = array_merge($data,$branch3,$branchAccounting3);
			// }


			$data = array_merge($scrivener,$branch,$branchAccounting,$branch1,$branchAccounting1,$branch2,$branchAccounting2,$branch3,$branchAccounting3);
			break;
		case '買方仲介服務費':
			unset($scrivener);
			$scrivener = array();
			$scrivener = $sms->getsScrivenerMobile2($vr,$caseData[0]['cScrivener']);

			// $data = array_merge($data,$scrivener);
			// if ($caseData[0]['cServiceTarget'] == 1 || $caseData[0]['cServiceTarget'] == 3) {
			// 	$data = array_merge($data,$branch,$branchAccounting);
			// }

			// if (($caseData[0]['cServiceTarget1'] == 1 || $caseData[0]['cServiceTarget1'] == 3) && $caseData[0]['cBranchNum1'] > 0) {
			// 	$data = array_merge($data,$branch1,$branchAccounting1);
			// }

			// if (($caseData[0]['cServiceTarget2'] == 1 || $caseData[0]['cServiceTarget2'] == 3) && $caseData[0]['cBranchNum2'] > 0) {
			// 	$data = array_merge($data,$branch2,$branchAccounting2);
			// }

			// if (($caseData[0]['cServiceTarget3'] == 1 || $caseData[0]['cServiceTarget3'] == 3) && $caseData[0]['cBranchNum3'] > 0) {
			// 	$data = array_merge($data,$branch3,$branchAccounting3);
			// }
			// $data = array_merge($scrivener,$branch,$branch1,$branch2,$branch3,$branchAccounting,$branchAccounting1,$branchAccounting2,$branchAccounting3);
			$data = array_merge($scrivener,$branch,$branchAccounting,$branch1,$branchAccounting1,$branch2,$branchAccounting2,$branch3,$branchAccounting3);
			break;
		case '代清償'://地政士、店東、店長、買方經紀人、賣方經紀人
			$data = array_merge($scrivener,$branch,$branch1,$branch2,$branch3,$buyerAgent2,$ownerAgent2);
			break;
		case '賣方先動撥'://地政士、店東、店長、買方經紀人、賣方經紀人
			$data = array_merge($scrivener,$branch,$branch1,$branch2,$branch3,$buyerAgent2,$ownerAgent2);
			break;
		case '保留款撥付'://地政士、店東、店長、買方、買方代理人、買方經紀人、賣方、賣方代理人、賣方經紀人
			$data = array_merge($scrivener,$branch,$branch1,$branch3,$buyer,$buyerAgent,$buyerAgent2,$owner,$ownerAgent,$ownerAgent2);


			break;
		
	}

	
	$_all = $sms->filter_array($_all);

	$data = $sms->filter_array($data);
	// unset($data);

}

$conn->close();
// echo "<pre>";
// 			print_r($data);


// print_r($data);
?>

<table cellspacing="0" cellpadding="0" border="1" width="100%" class="tb">
	<tr>
		<th width="10%"><input type="checkbox" name="all" id="" checked="checked" onclick="checkALL()"></th>
		<th width="20%">職稱</th>
		<th width="20%">姓名</th>
		<th width="20%">手機</th>
		<th>&nbsp;</th>

	</tr>
	<?php foreach ($_all as $k => $v){  $checked = '';  ?>

		<?php foreach ($data as $key => $value){
			
			if ($v['tTitle'] == $value['tTitle'] && $v['mMobile'] == $value['mMobile'] && $v['storeId'] == $value['storeId']) {
				$checked = 'checked="checked"';
			}
		} ?>
		<tr>
			<td align="center"><input type="checkbox" <?=$checked?> name="allForm[]" value="<?=$v['tTitle']."_".$v['mName']."_".$v['mMobile']."_".$v['storeId']?>"></td>
			<td><?=$v['tTitle']?></td>
			<td><?=$v['mName']?></td>
			<td><?=$v['mMobile']?></td>
			<td>
				<?=$v['storeName']?>
			

				<!-- <?php if (($v['tTitle'] == '會計' || $v['tTitle'] == '秘書') && $radiokind == '點交'): ?>
					有服務費會發送
				<?php endif ?> -->
				&nbsp;
			</td>
		</tr>
	<?php } ?>
</table>