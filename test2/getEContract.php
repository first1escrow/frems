<?php
include_once dirname(dirname(__FILE__)).'/configs/config.class.php';
include_once dirname(dirname(__FILE__)).'/class/contract.class.php';
include_once dirname(dirname(__FILE__)).'/openadodb.php' ;
include_once dirname(dirname(__FILE__)).'/openadodb22.php' ;
include_once dirname(dirname(__FILE__)).'/includes/maintain/feedBackData.php';

header("Content-Type:text/html; charset=utf-8");

$_POST = escapeStr($_POST) ;

//合約書存檔
$contract = new Contract();

$vr_code = $_POST['code'];

$vr_code = '60001100567310';

$msg = array();


##
//已使用
$sql = "UPDATE tBankCode SET bUsed = 1 WHERE bAccount = '".$vr_code."'";
// $conn->Execute($sql);

//檢查是否轉換過
$sql = "SELECT cCertifiedId FROM tContractCase WHERE cEscrowBankAccount = '".$vr_code."'";
$rs = $conn->Execute($sql);

// if (!$rs->EOF) {
// 	$msg['code'] = 201;
// 	$msg['msg'] = '已轉換過';
// 	echo json_encode($msg);
// 	exit;	
// }



##查詢合約書版本
$sql = "SELECT bApplication,bSID FROM tBankCode WHERE bAccount = '".$vr_code."'";
$rs = $conn->Execute($sql);
$bankCode = $rs->fields;
$data['certifiedid'] = substr($vr_code, -9);//保證號碼9馬
if (substr($vr_code, 0,5) == '60001') { //一銀
	$data['case_bank'] = '8';//合約銀行
}elseif (substr($vr_code, 0,5) == '99985') {
	$data['case_bank'] = '77';//合約銀行
}elseif(substr($vr_code, 0,5) == '99986'){
	$data['case_bank'] = '80';//合約銀行
}elseif (substr($vr_code, 0,5) == '96988') {
	$data['case_bank'] = '68';//合約銀行
}

##查詢代書資料
$sql = "SELECT sId,sRecall,sSpRecall2 FROM tScrivener WHERE sId = '".$bankCode['bSID']."'";
$rs = $conn->Execute($sql);
$scrivenerData = $rs->fields;

$data['scrivener_id'] = $bankCode['bSID'];

##
//版本對應22上的表，
if ($bankCode['bApplication'] == 1) { //土地
	$sql = "SELECT
				wcl.*,
				wc.buyer_brokerage,
				wc.seller_brokerage,
				wc.add_time,
				wc.signdate	
			FROM
				_web_contract_1 AS wcl
			LEFT JOIN
				_web_contract AS wc ON wc.id=wcl.contract_id
			WHERE
				wcl.guarantee_code = '".$vr_code."'";
    $rs = $conn22->Execute($sql);
	$eContractData = $rs->fields;

	//土地標示
	$sql = "SELECT * FROM _web_land_subject_a WHERE contract_id = '".$eContractData['contract_id']."'";
	$rs = $conn22->Execute($sql);
	$i = 0;

	if (!$rs->EOF) {
		while (!$rs->EOF) {
			$land = array();
			$land['scrivener_bankaccount'] = $vr_code;
			$land['land_zip'] = getZip($rs->fields['city'],$rs->fields['county']);
			$land['land_land1'] = $rs->fields['address1'];
			$land['land_land2'] = $rs->fields['address2'];
			$land['land_land3'] = $rs->fields['land_num'];
			$land['land_land4'] = $rs->fields['land_use'];
			$land['land_measure'] = $rs->fields['area'];//面積
			$land['land_area'] = '';//使用分區
			$land['land_power1'] = $rs->fields['right1'];
			$land['land_power2'] = $rs->fields['right2'];
			$land['land_movedate'] = ($eContractData['land_tw_year']+1911)."-".date('m');
			$land['land_farmland'] = $eContractData['farmland_tax'];
			$land['land_landprice'] = $eContractData['land_money'];
			

			// $contract->AddLand($land, $i); //土地

			$i++;
			$rs->MoveNext();
		}
	}else{
		// $contract->AddLand($land, $i);
	}
	

	//土地標的-未保存登記建物
	
	$sql = "SELECT * FROM _web_land_subject_b WHERE contract_id = '".$eContractData['contract_id']."'";
	$rs = $conn22->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$build = array();
		$build['certifiedid'] = $data['certifiedid'];
		$build['new_property_Item'][$i] = $i;
		$build['new_property_zip'.$build['new_property_Item'][$i]] = getZip($rs->fields['city'],$rs->fields['county']);
		$build['new_property_addr'.$build['new_property_Item'][$i]] = $rs->fields['address'];

		// $contract->AddProperty2($build); //建物
		$rs->MoveNext();
	}

}elseif ($bankCode['bApplication'] == 2) { //建物
	$sql = "SELECT
				wcb.*,
				wc.buyer_brokerage,
				wc.seller_brokerage,
				wc.add_time,
				wc.signdate			
			FROM
				_web_contract_2 AS wcb
			LEFT JOIN
				_web_contract AS wc ON wc.id=wcb.contract_id
			WHERE
				wcb.guarantee_code = '".$vr_code."'";

	$rs = $conn22->Execute($sql);
	$eContractData = $rs->fields;



	//房地標的-土地標示
		$sql = "SELECT * FROM _web_subject_a WHERE contract_id = '".$eContractData['contract_id']."'";
		$rs = $conn22->Execute($sql);
		$i = 0;
		while (!$rs->EOF) {
			$land = array();
			$land['scrivener_bankaccount'] = $vr_code;
			$land['land_zip'] = getZip($rs->fields['city'],$rs->fields['county']);
			$land['land_land1'] = $rs->fields['address1'];
			$land['land_land2'] = $rs->fields['address2'];
			$land['land_land3'] = $rs->fields['land_num'];
			$land['land_land4'] = $rs->fields['land_use'];
			$land['land_measure'] = $rs->fields['area'];//面積
			$land['land_area'] = '';//使用分區
			$land['land_power1'] = $rs->fields['right1'];
			$land['land_power2'] = $rs->fields['right2'];
			// $data['land_movedate'] = ($rs->fields['land_tw_year']+1911)."-".date('m');
			// $data['land_farmland'] = $rs->fields['farmland_tax'];
			// $data['land_landprice'] = $rs->fields['land_money'];
			

			// $contract->AddLand($land, $i);//土地

			$i++;
			$rs->MoveNext();
		}
	

	//房地標的-建物
	$sql = "SELECT * FROM _web_subject_b WHERE contract_id = '".$eContractData['contract_id']."' ORDER BY id ASC";
	// echo $sql;
	
	$rs = $conn22->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$build = array();
		$level = array();
		$level_area = array();
		$propertyObjectCount = 0;
		
		// echo $rs->fields['city']."_".$rs->fields['county']."";

		$build['scrivener_bankaccount'] = $vr_code;
		$build['new_property_Item'][$i] = $i;
		$build['new_property_levelnow'.$i] = 0;//樓層$rs->fields['level_allarea']
		$build['new_property_levelhighter'.$i] = 0;//總樓層
		$build['new_property_zip'.$i] = getZip($rs->fields['city'],$rs->fields['county']);
		$build['new_property_addr'.$i] = $rs->fields['address'];
		$build['new_property_measuretotal'.$i] = $rs->fields['level_allarea'];// 產品面積
		$build['new_property_buildno'.$i] = $rs->fields['build_num'];//建號
		$build['new_property_power1'.$i] = $rs->fields['level_right1'];//權利範圍(分子)
		$build['new_property_power2'.$i] = $rs->fields['level_right2'];//權利範圍(分母)

		//樓層
		$level = json_decode($rs->fields['level']);
		$level_area = json_decode($rs->fields['level_area']);

		for ($j=0; $j < count($level); $j++) { 


			if ($rs->fields['level_right2'] == '' || $rs->fields['level_right2'] == 0) {
				$rs->fields['level_right2'] = 1;
			}

			if ($rs->fields['level_right1'] == '' || $rs->fields['level_right1'] == 0) {
				$rs->fields['level_right1'] = 1;
			}


			$new_cMeasureMain = round($level_area[$j]*($rs->fields['level_right1']/$rs->fields['level_right2']),2);

			$sql = "INSERT INTO 
						tContractPropertyObject
					SET
						cCertifiedId = '".$data['certifiedid']."',
						cItem = '".$propertyObjectCount."',
						cCategory = '1',
						cLevelUse = '".$level[$j]."層',
						cMeasureMain = '".$new_cMeasureMain."',
						cMeasureTotal = '".$level_area[$j]."',
						cPower1 = '".$rs->fields['level_right1']."',
						cPower2 = '".$rs->fields['level_right2']."',
						cBuildItem = '".$i."'
						";
			echo $sql."<br>";

			// die;
			// $conn->Execute($sql);
			$propertyObjectCount++;
		}

		unset($level);
		unset($level_area);
		// level_arcade 騎樓
		$new_cMeasureMain = round($rs->fields['level_arcade']*($rs->fields['level_right1']/$rs->fields['level_right2']),2);
		$sql = "INSERT INTO 
						tContractPropertyObject
					SET
						cCertifiedId = '".$data['certifiedid']."',
						cItem = '".$propertyObjectCount."',
						cCategory = '1',
						cLevelUse = '騎樓',
						cMeasureMain = '".$new_cMeasureMain."',
						cMeasureTotal = '".$rs->fields['level_arcade']."',
						cPower1 = '".$rs->fields['level_right1']."',
						cPower2 = '".$rs->fields['level_right2']."',
						cBuildItem = '".$i."'
						";
		$propertyObjectCount++;
		echo $sql."<br>";
		// $conn->Execute($sql);

		//附屬建物
		//attach_use
		$attach_use = array();
		$attach_area = array();

		$attach_use = json_decode($rs->fields['attach_use']);
		$attach_area = json_decode($rs->fields['attach_area']);
		for ($j=0; $j < count($attach_use); $j++) { 
			if ($rs->fields['level_right2'] == '' || $rs->fields['level_right2'] == 0) {
				$rs->fields['level_right2'] = 1;
			}

			if ($rs->fields['level_right1'] == '' || $rs->fields['level_right1'] == 0) {
				$rs->fields['level_right1'] = 1;
			}


			$new_cMeasureMain = round($level_area[$j]*($rs->fields['level_right1']/$rs->fields['level_right2']),2);

			$sql = "INSERT INTO 
						tContractPropertyObject
					SET
						cCertifiedId = '".$data['certifiedid']."',
						cItem = '".$propertyObjectCount."',
						cCategory = '2',
						cLevelUse = '".$attach_use[$j]."',
						cMeasureMain = '".$new_cMeasureMain."',
						cMeasureTotal = '".$level_area[$j]."',
						cPower1 = '".$rs->fields['level_right1']."',
						cPower2 = '".$rs->fields['level_right2']."',
						cBuildItem = '".$i."'
						";
			echo $sql."<br>";
			$propertyObjectCount++;
			// $conn->Execute($sql);
		}


		print_r($build);

		// $contract->AddProperty2($build); //建物
		// die;
		unset($build);
		$i++;
		$rs->MoveNext();
	}

	//附贈家具
	$equipment = array();
	$equipment_num = array();

	$equipment = explode(',', $eContractData['equipment']);
	$equipment_num = explode(',', $eContractData['equipment_num']);
	// $str = '';
	for ($j=0; $j < count($equipment); $j++) { 
		if (preg_match('/燈飾/', $equipment[$j])) {
			$data['furniture_lamp'] = $equipment_num[$j];
		}elseif (preg_match('/床組/', $equipment[$j])) {
			$data['furniture_bed'] = $equipment_num[$j];
		}elseif (preg_match('/梳妝台/', $equipment[$j])) {
			$data['furniture_dresser'] = $equipment_num[$j];
		}elseif (preg_match('/熱水器/', $equipment[$j])) {
			$data['furniture_geyser'] = $equipment_num[$j];
		}elseif (preg_match('/電話/', $equipment[$j])) {
			$data['furniture_telephone'] = $equipment_num[$j];
		}elseif (preg_match('/洗衣機/', $equipment[$j])) {
			$data['furniture_washer'] = $equipment_num[$j];
		}elseif (preg_match('/瓦斯爐/', $equipment[$j])) {
			$data['furniture_gasStove'] = $equipment_num[$j];
		}elseif (preg_match('/沙發/', $equipment[$j])) {
			$data['furniture_sofa'] = $equipment_num[$j];
		}elseif (preg_match('/冷氣/', $equipment[$j])) {
			$data['furniture_air'] = $equipment_num[$j];
		}elseif (preg_match('/抽油煙機/', $equipment[$j])) {
			$data['furniture_machine'] = $equipment_num[$j];
		}elseif (preg_match('/電視/', $equipment[$j])) {
			$data['furniture_tv'] = $equipment_num[$j];
		}elseif (preg_match('/冰箱/', $equipment[$j])) {
			$data['furniture_refrigerator'] = $equipment_num[$j];
		}elseif (preg_match('/天然瓦斯/', $equipment[$j])) {
			$data['furniture_gas'] = $equipment_num[$j];
		}elseif (preg_match('/流理台/', $equipment[$j])) {
			$data['furniture_sink'] = $equipment_num[$j];
		}else{
			$data['furniture_other'] .= $equipment[$j];
		}
	}
	unset($equipment);
	unset($equipment_num);

    // $contract->AddContractFurniture($data);//家具

	// die;
}



$data['scrivener_bankaccount'] = $vr_code;//保證號碼
$data['case_applydate'] = $eContractData['add_time'];//建檔時間
$data['case_signdate'] = '';

$data['case_finishdate'] = '';//點交時間
$data['case_finishdate2'] = '';//預計點交日期

$data['case_undertakerid'] = ''; //建檔人(經辦ID)
$data['case_exception'] = '';//異常狀態
$data['case_exceptionreason'] = '';//異常原因
$data['sRecall'] = $scrivenerData['sRecall']; //地政士回饋比率

$data['case_affixdate'] = $eContractData['money2_date']; //用印日期
$data['case_firstdate'] = $eContractData['detail_date'];//第一期付款日期
$data['case_property'] = $eContractData['trading_subject'];//賣方就本買賣標的
$signDate = (!empty($eContractData['c_year']))?($eContractData['c_year']+1911)."-".$eContractData['c_month']."-".$eContractData['c_day']:'0000-00-00';

$data['case_signdate'] = $signDate;//簽約日
$data['case_status'] = 2;
$data['scrivener_sSpRecall'] = $scrivenerData['sSpRecall2']; //
$data['ascription_contribute'] = $eContractData['taxes'];//1:一般稅率 2:自用住宅優惠稅率

//買方付擔 
// $ascription_option = array('1' => '地政規費', '2' => '設定規費', '3' => '印花稅', '4' => '地政士業務執行費', '5' => '公證或監證費', '6' => '簽約費', '7' => '火險及地震險費', '8' => '塗銷費','9' => '貸款相關費用',10=>'實價登錄費',11=>'履保費',12=>'土地增值稅');
//buyer_burden
$menu_tax = array();
$burden = array();

$ascription = array();
$sql = "SELECT * FROM _web_tax WHERE is_show = 1";
$rs = $conn22->Execute($sql);
while (!$rs->EOF) {
	$menu_tax[$rs->fields['id']] = $menu_tax[$rs->fields['firstid']];
	$rs->MoveNext();
}

if ($eContractData['buyer_burden']) {
	$burden = explode(',', $eContractData['buyer_burden']);

	foreach ($burden as $k => $v) {
		array_push($ascription, $menu_tax[$v]);
	}

	$data['ascription_buy'] = $ascription;

	unset($burden);
}
unset($ascription);
$ascription = array();
if ($eContractData['seller_burden']) {
	$burden = explode(',', $eContractData['seller_burden']);

	foreach ($burden as $k => $v) {
		array_push($ascription, $menu_tax[$v]);
	}

	$data['ascription_owner'] = $ascription;
	unset($burden);
}
unset($ascription);

// 停車位
$sql = "SELECT * FROM _web_subject_c WHERE contract_id = '".$eContractData['contract_id']."'";
$rs = $conn22->Execute($sql);
if (!$rs->EOF) {
	$ground = ($rs->fields['position'] == '地上')?'1':'2'; //1地上、2地下
	$category = 0;
	if ($rs->fields['parking_style'] == '坡道平面式') {
		$category = 1;
	}elseif ($rs->fields['parking_style'] == '昇降平面式') {
		$category = 2;
	}elseif ($rs->fields['parking_style'] == '坡道機械式') {
		$category = 3;
	}elseif ($rs->fields['parking_style'] == '昇降機械式') {
		$category = 4;
	}

	$belong = ($rs->fields['belong'] == '有所有權')?'1':'2';
	$ownerType = ($rs->fields['belong1'] == '有獨立權狀')?'1':'2';
	// $owner = ($rs->fields['belong2'] == '有獨立權狀')?'1':'2';
	if ($rs->fields['belong2'] == '需承租繳租金') {
		$owner = '3';
	}elseif ($rs->fields['belong2'] == '需定期抽籤') {
		$owner = '4';
	}elseif ($rs->fields['belong2'] == '需排隊等候') {
		$owner = '5';
	}
	

	$sql = "INSERT INTO
				tContractParking
			WHERE
				cGround = '".$ground."',
				cFloor = '".$rs->fields['level']."',
				cNo = '".$rs->fields['parking_num']."',
				cCategory = '".$rs->fields['parking_style']."',
				cBelong = '".$belong."',
				cOwner = '".$rs->fields['belong2']."',
				cOwnerType = '".$ownerType."',
				cOther = '".$rs->fields['other']."'";
	// $conn->Execute($sql);
	unset($category);
	unset($ground);
}


$store = array();
##
//不確定是否有同間仲介店分開填寫的情況，所以加入判斷
if ($eContractData['buyer_brokerage'] != $eContractData['seller_brokerage']) {

	if ($eContractData['buyer_brokerage']) { //買方仲介店
		$store = getStoreData2($eContractData['buyer_brokerage']);

		if (!empty($store)) {
			$data['realestate_branch'] = $store['bId'];
			$data['realestate_bRecall'] = $store['bRecall'];
			$data['realestate_bScrRecall'] = $store['bScrRecall'];
			$data['realestate_brand'] = $store['bBrand'];
			$data['realestate_name'] = $store['bName'];
			$data['realestate_branchnum'] = $store['bId'];
			$data['cServiceTarget'] = 3; //服務對象：1.買賣方、2.賣方、3.買方
			$data['realestate_serialnumber'] = $store['bSerialnum'];
			$data['realestate_telarea'] = $store['bTelArea'];
			$data['realestate_telmain'] = $store['bTelMain'];
			$data['realestate_faxarea'] = $store['bFaxArea'];
			$data['realestate_faxmain'] = $store['bFaxMain'];
			$data['realestate_zip'] = $store['bZip'];
			$data['realestate_addr'] = $store['bAddress'];
		}


		unset($store);
	}

	
	if ($eContractData['seller_brokerage']) { //有賣方仲介店

		$store = getStoreData2($eContractData['seller_brokerage']);
		
		if (!empty($store)) {
			$data['realestate_branch1'] = $store['bId'];
			$data['realestate_bRecall2'] = $store['bRecall'];
			$data['realestate_bScrRecall2'] = $store['bScrRecall'];
			$data['realestate_brand1'] = $store['bBrand'];
			$data['realestate_name1'] = $store['bName'];
			$data['realestate_branchnum1'] = $store['bId'];
			$data['cServiceTarget1'] = 2;
			$data['realestate_serialnumber1'] = $store['bSerialnum'];
			$data['realestate_telarea1'] = $store['bTelArea'];
			$data['realestate_telmain1'] = $store['bTelMain'];
			$data['realestate_faxarea1'] = $store['bFaxArea'];
			$data['realestate_faxmain1'] = $store['bFaxMain'];
			$data['realestate_zip1'] = $store['bZip'];
			$data['realestate_addr1'] = $store['bAddress']; 
		}


		unset($store);
	}

	if ($eContractData['buyer_brokerage'] == '' && $eContractData['seller_brokerage'] == '') {
		//沒有寫當非仲成交
		$data['realestate_branch'] = 505;
		$data['realestate_bRecall'] = 33.33;
		$data['realestate_bScrRecall'] = '';
		$data['realestate_brand'] = 2;
		$data['realestate_name'] = '非仲介成交';
		$data['realestate_branchnum'] = 505;
		$data['cServiceTarget'] = 1; //服務對象：1.買賣方、2.賣方、3.買方
		$data['realestate_serialnumber'] = '';
		$data['realestate_telarea'] = '';
		$data['realestate_telmain'] = '';
		$data['realestate_faxarea'] = '';
		$data['realestate_faxmain'] = '';
		$data['realestate_zip'] = '';
		$data['realestate_addr'] = '';
	}

}else{
	$store = getStoreData2($eContractData['buyer_brokerage']);

	if (!empty($store)) {
		$data['realestate_branch'] = $store['bId'];
		$data['realestate_bRecall'] = $store['bRecall'];
		$data['realestate_bScrRecall'] = $store['bScrRecall'];
		$data['realestate_brand'] = $store['bBrand'];
		$data['realestate_name'] = $store['bName'];
		$data['realestate_branchnum'] = $store['bId'];
		$data['cServiceTarget'] = 1; //服務對象：1.買賣方、2.賣方、3.買方
		$data['realestate_serialnumber'] = $store['bSerialnum'];
		$data['realestate_telarea'] = $store['bTelArea'];
		$data['realestate_telmain'] = $store['bTelMain'];
		$data['realestate_faxarea'] = $store['bFaxArea'];
		$data['realestate_faxmain'] = $store['bFaxMain'];
		$data['realestate_zip'] = $store['bZip'];
		$data['realestate_addr'] = $store['bAddress'];
	}else{
		//沒有寫當非仲成交
		$data['realestate_branch'] = 505;
		$data['realestate_bRecall'] = 33.33;
		$data['realestate_bScrRecall'] = '';
		$data['realestate_brand'] = 2;
		$data['realestate_name'] = '非仲介成交';
		$data['realestate_branchnum'] = 505;
		$data['cServiceTarget'] = 1; //服務對象：1.買賣方、2.賣方、3.買方
		$data['realestate_serialnumber'] = '';
		$data['realestate_telarea'] = '';
		$data['realestate_telmain'] = '';
		$data['realestate_faxarea'] = '';
		$data['realestate_faxmain'] = '';
		$data['realestate_zip'] = '';
		$data['realestate_addr'] = '';
	}


	unset($store);
}

###

// $data['income_firstmoney'] = 0;//降保金額
// $data['income_bankloan'] = 0;//貸款銀行
// $data['income_loanmoney'] = 0;//貸款金額
// print_r($eContractData);

$data['income_signmoney'] = $eContractData['money1'];//簽約
$data['income_depositMoney'] = $eContractData['money1_deposit'];//(含定金 元整)交由特約地政士存匯入履保專戶
$data['income_affixmoney'] = $eContractData['money2']; //用印款
$data['income_dutymoney'] = $eContractData['money3'];//完稅款
$data['income_estimatedmoney'] = $eContractData['money4'];//尾款
$data['income_totalmoney'] = $eContractData['total'];//總價金
$data['income_certifiedmoney'] = round($eContractData['total']*0.0006);
$data['income_parking'] = $eContractData['parking_money'];//含車位價款
$data['income_businessTax'] = $eContractData['business_tax'];//含營業稅

$detail_type = array();
$detail_money = array();
if ($data['detail_type']) {
	$detail_type = explode(',', $eContractData['detail_type']);
	$detail_money = explode(',', $eContractData['detail_money']);
	for ($i=0; $i < count($detail_type); $i++) { 
		if ($detail_type[$i] == 1) {
			$data['income_paycash'] = $detail_money[$i]; //支付方式-現金
		}elseif ($detail_type[$i] == 2) {
			$data['income_ticket'] = $detail_money[$i]; //支付方式-現金
		}elseif ($detail_type[$i] == 3) {
			$data['income_paycommercialpaper'] = $detail_money[$i]; //支付方式-現金
		}
			
		
	}
	
}
unset($detail_type);
unset($detail_money);

//買賣方
$sql = "SELECT * FROM _web_trader WHERE contract_id = '".$eContractData['contract_id']."'";
$rs = $conn22->Execute($sql);
$buyerCount = 0;//買方數量
$ownerCount = 0;//賣方數量

$owner = array();

if (!$rs->EOF) {
	while (!$rs->EOF) {
		$customer = array();
		$address = array();//戶籍地址
		$address1 = array();//聯絡地址
		preg_match_all("/(.*[市|縣])(.*[區|鄉|鎮|市])(.*)/isu", $rs->fields['address'], $address); //切割字串
		$rZip = getZip($address[1][0],$address[2][0]);

		preg_match_all("/(.*[市|縣])(.*[區|鄉|鎮|市])(.*)/isu", $rs->fields['address2'], $address1); //切割字串
		$cZip = getZip($address1[1][0],$address1[2][0]);
		// $str
		// print_r($address);
		$customer['cName'] = $rs->fields['name'];
		$customer['cMobileNum'] = $rs->fields['phone'];
		$customer['cBirthdayDay'] = $rs->fields['birthday'];
		$customer['cRegistZip'] = $rZip;
		$customer['cRegistAddr'] = $address[3][0];
		$customer['cBaseZip'] = $cZip;
		$customer['cBaseAddr'] = $address1[3][0];
		$customer['cIdentifyId'] = $rs->fields['editor'];

		if (mb_strlen($rs->fields['editor']) == 8) {
			$customer['cCategoryIdentify'] = 2;
		}elseif (mb_strlen($rs->fields['editor']) == 10) {
			$customer['cCategoryIdentify'] = 1;
		}

		if ($rs->fields['people_type'] == 1) {//1買方
			if ($buyerCount == 0) {
				$sql = "INSERT INTO
							tContractBuyer
						SET
							cCertifiedId = '".$data['certifiedid']."',
							cIdentifyId = '".$customer['cIdentifyId']."',
							cCategoryIdentify = '".$customer['cCategoryIdentify']."',
							cName = '".$customer['cName']."',
							cMobileNum = '".$customer['cMobileNum']."',
							cBirthdayDay = '".$customer['cBirthdayDay']."',
							cRegistZip = '".$customer['cRegistZip']."',
							cRegistAddr = '".$customer['cRegistAddr']."',
							cBaseZip = '".$customer['cBaseZip']."',
							cBaseAddr = '".$customer['cBaseAddr']."'
							";
				// $conn->Execute($sql);
			}else{
				$sql = "INSERT INTO
							tContractOthers
						SET
							cIdentity = 1,
							cCertifiedId = '".$data['certifiedid']."',
							cIdentifyId = '".$customer['cIdentifyId']."',
							cCategoryIdentify = '".$customer['cCategoryIdentify']."',
							cName = '".$customer['cName']."',
							cMobileNum = '".$customer['cMobileNum']."',
							cBirthdayDay = '".$customer['cBirthdayDay']."',
							cRegistZip = '".$customer['cRegistZip']."',
							cRegistAddr = '".$customer['cRegistAddr']."',
							cBaseZip = '".$customer['cBaseZip']."',
							cBaseAddr = '".$customer['cBaseAddr']."'
							";
				// $conn->Execute($sql);
			}

			$buyerCount++;
		}elseif ($rs->fields['people_type'] == 2) { //2賣方
			if ($ownerCount == 0) {
				$sql = "INSERT INTO
							tContractOwner
						SET
							cCertifiedId = '".$data['certifiedid']."',
							cIdentifyId = '".$customer['cIdentifyId']."',
							cCategoryIdentify = '".$customer['cCategoryIdentify']."',
							cName = '".$customer['cName']."',
							cMobileNum = '".$customer['cMobileNum']."',
							cBirthdayDay = '".$customer['cBirthdayDay']."',
							cRegistZip = '".$customer['cRegistZip']."',
							cRegistAddr = '".$customer['cRegistAddr']."',
							cBaseZip = '".$customer['cBaseZip']."'
							";
				// $conn->Execute($sql);
			}else{
				$sql = "INSERT INTO
							tContractOthers
						SET
							cIdentity = 2,
							cCertifiedId = '".$data['certifiedid']."',
							cIdentifyId = '".$customer['cIdentifyId']."',
							cCategoryIdentify = '".$customer['cCategoryIdentify']."',
							cName = '".$customer['cName']."',
							cMobileNum = '".$customer['cMobileNum']."',
							cBirthdayDay = '".$customer['cBirthdayDay']."',
							cRegistZip = '".$customer['cRegistZip']."',
							cRegistAddr = '".$customer['cRegistAddr']."',
							cBaseZip = '".$customer['cBaseZip']."'
							";
				// $conn->Execute($sql);
			}

			$ownerCount++;
		}elseif ($rs->fields['people_type'] == 3 || $rs->fields['people_type'] == 4 || $rs->fields['people_type'] == 5) {//3買登記名義人、4買方代理、5賣方代理
			$cIdentity = 0;
			//5買方登記名義人6買方代理人7賣方代理人
			if ($rs->fields['people_type'] == 3) {
				$cIdentity = 5;
			}elseif ($rs->fields['people_type'] == 4) {
				$cIdentity = 6;
			}elseif ($rs->fields['people_type'] == 5) {
				$cIdentity = 7;
			}
			$sql = "INSERT INTO
							tContractOthers
						SET
							cIdentity = '".$cIdentity ."',
							cCertifiedId = '".$data['certifiedid']."',
							cIdentifyId = '".$customer['cIdentifyId']."',
							cName = '".$customer['cName']."',
							cMobileNum = '".$customer['cMobileNum']."',
							cBirthdayDay = '".$customer['cBirthdayDay']."',
							cRegistZip = '".$customer['cRegistZip']."',
							cRegistAddr = '".$customer['cRegistAddr']."',
							cBaseZip = '".$customer['cBaseZip']."',
							cBaseAddr = '".$customer['cBaseAddr']."'
							";
			// echo $sql;
			// $conn->Execute($sql);
		}

		

		$rs->MoveNext();
	}
}else{
	// $contract->AddOwner($data);
	// $contract->AddBuyer($data);
}

##
// print_r($data);


// $contract->AddContract($data);//合約書
// $contract->AddRealstate($data);//仲介
// $contract->AddScrivener($data);//地政士
// $contract->AddIncome($data);//價款
// $contract->AddExpenditure($data);//
// $contract->AddInvoice($data);//發票
// $contract->AddContractAscription($data);//契稅之歸屬

//回饋金
// getFeedMoney('c',substr($vr_code, -9));
##
//地政士簡訊對象
//取出地政士的預設紀錄
$sql = 'SELECT sMobile,sDefault,sSend FROM tScrivenerSms WHERE sScrivener="'.$data['scrivener_id'].'" AND sDel = 0 ORDER BY sNID,sId ASC' ;
$rs = $conn->Execute($sql) ;
$smsTarget = array() ;
while (!$rs->EOF) {
	
	if ($rs->fields['sDefault']==1) {
		$smsTarget[] = $rs->fields['sMobile'] ;		
	}
		
	if ($rs->fields['sSend']==1) {
		$send[]=$rs->fields['sMobile'];
	}
	$rs->MoveNext() ;
}
##
//複製到案件的預設簡訊對象
if (count($smsTarget) > 0) {
	$sql = 'UPDATE tContractScrivener SET cSmsTarget="'.implode(',',$smsTarget).'",cSend2 = "'.@implode(',',$send).'" WHERE cCertifiedId="'.$data['certifiedid'].'" AND cScrivener="'.$data['scrivener_id'].'";' ;
	$conn->Execute($sql) ;
		// echo $sql."<br><br>";
}
##
//已轉換過
$sql = "UPDATE _web_contract SET close = 1 WHERE id = '".$eContractData."'";
// $conn22->Execute($sql);

##檢查是否OK
$query ='
SELECT 
    cas.cCertifiedId,
    rea.cBranchNum,
    rea.cBranchNum1,
    rea.cBranchNum2,
    rea.cBrand,
    rea.cBrand1,
    rea.cBrand2,
    csc.cScrivener AS sId, 
    scr.sCategory as scrivenerCategory,
    (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) category,
    (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) category1,
    (SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) category2 
FROM 
    tContractCase AS cas 
JOIN 
    tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId 
JOIN 
    tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId 
JOIN 
    tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
JOIN 
    tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
JOIN 
    tScrivener AS scr ON scr.sId=csc.cScrivener 
LEFT JOIN 
    tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId  
LEFT JOIN 
    tPeopleInfo AS peo ON peo.pId=scr.sUndertaker1 
LEFT JOIN 
    tContractOthers  AS  other ON other.cCertifiedId=cas.cCertifiedId
WHERE
	cas.cCertifiedId = "'.$data['certifiedid'].'"
GROUP BY cas.cCertifiedId';

$rs = $conn->Execute($query);

if (!$rs->EOF) {
	$msg['code'] = 200;
	$msg['msg'] = '轉換成功';
	echo json_encode($msg);
}else{
	$msg['code'] = 201;
	$msg['msg'] = '轉換失敗';
	echo json_encode($msg);
	exit;	
}
##
function getStoreData2($name){
	global $conn;

	$storeName = array(); //(.*[店|店+\(\d\)])(\(.*\))
	$branchData = array();
	preg_match_all("/(.*[房屋|地產|不動產]{2,3})(.*[店|店+\(\d\)])(\(.*\))/isu", $name, $storeName); //切割字串

	//要濾掉括弧
	$storeName[3][0] = str_replace('(', '', $storeName[3][0]);
	$storeName[3][0] = str_replace(')', '', $storeName[3][0]);
	//查詢店家是否存在
	$sql = "SELECT
				bc.bId,
				bc.bScrRecall,
				bc.bName,
				bc.bStore,
				bc.bBrand,
				bc.bRecall,
				bc.bSerialnum,
				bc.bTelArea,
				bc.bTelMain,
				bd.bName AS brandName,
				bc.bFaxArea,
				bc.bFaxMain,
				bc.bZip
				
			FROM
				tBranch AS bc
			JOIN
				tBrand AS bd ON bd.bId = bc.bBrand
			WHERE 
				bd.bName = '".$storeName[1][0]."'
				AND bc.bName = '".$storeName[3][0]."'
				AND bc.bStore = '".$storeName[2][0]."'
			";
	// echo $sql;
	$rs = $conn->Execute($sql);
	$branchData = $rs->fields;
	return $branchData;
	
}
function getZip($city,$area=''){

	global $conn;

	$str = '';

	if ($city == '桃園縣') {
		$city ='桃園市';

		$area = mb_substr($area,0,2,'UTF-8')."區";
		
	}

	
	if ($area) {
		$str .= " AND zArea = '".$area."'";
	}

	

	$sql = "SELECT zZip FROM tZipArea WHERE zCity = '".$city."' ".$str;
	
	$rs = $conn->Execute($sql);



   return $rs->fields['zZip'];



}
function SmsDefault($bid) {
	global $conn;
    $sql = 'SELECT bMobile FROM tBranchSms WHERE bBranch="'.$bid.'" AND bDefault="1" AND bNID NOT IN ("14","15") AND bDel = 0 ORDER BY bNID,bId ASC;' ;
    $rs = $conn->Execute($sql);

    $smsTarget = array() ; 

    while (!$rs->EOF) {
      	
      	$smsTarget[] = $rs->fields['bMobile'] ;

      	$rs->MoveNext();
    } 
    
  
    return implode(",",$smsTarget) ;
}
?>