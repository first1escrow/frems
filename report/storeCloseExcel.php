<?php
require_once dirname(dirname(__FILE__)).'/libs/PHPExcel/Classes/PHPExcel.php' ;
require_once dirname(dirname(__FILE__)).'/libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php' ;
require_once dirname(dirname(__FILE__)).'/session_check.php' ;
require_once dirname(dirname(__FILE__)).'/openadodb.php' ;
require_once dirname(dirname(__FILE__)).'/includes/lib.php' ;

//取得資料內容(仲介)
Function getData ($_status, $_time, $_zip, $_brand='', $_cat='') {
	global $conn, $exceptbId ;
	$arr = array() ;
	
	if ($_brand) $_brand = ' AND bBrand = "'.$_brand.'" ' ;
	if ($_cat) $_cat = ' AND bCategory = "'.$_cat.'" ' ;
	
	/* 	$sql = '
			SELECT
				*,
				(SELECT bCode FROM tBrand WHERE a.bBrand=bId) as code,
				(SELECT bName FROM tBrand WHERE a.bBrand=bId) as brand,
				(SELECT zCity FROM tZipArea WHERE a.bZip=zZip) as city,
				(SELECT zArea FROM tZipArea WHERE a.bZip=zZip) as district
			FROM
				tBranch AS a
			WHERE
				bStatus="'.$_status.'"
				'.$_zip.$_brand.$_cat.'
				AND bModify_time >= "'.$_time.'-01 00:00:00"
				AND bModify_time <= "'.$_time.'-31 23:59:59"
				AND bId NOT IN ('.implode(',',$exceptbId).')
			ORDER BY
				bBrand, bCategory, bId
			ASC
		;' ;
	 */	
	$sql = '
		SELECT
			*,
			(SELECT bCode FROM tBrand WHERE a.bBrand=bId) as code,
			(SELECT bName FROM tBrand WHERE a.bBrand=bId) as brand,
			(SELECT zCity FROM tZipArea WHERE a.bZip=zZip) as city,
			(SELECT zArea FROM tZipArea WHERE a.bZip=zZip) as district
		FROM
			tBranch AS a
		WHERE
			bStatus="'.$_status.'"
			'.$_zip.$_brand.$_cat.'
			AND bStatusTime >= "'.$_time.'-01 00:00:00"
			AND bStatusTime <= "'.$_time.'-31 23:59:59"
			AND bId NOT IN ('.implode(',',$exceptbId).')
		ORDER BY
			bBrand, bCategory, bId
		ASC
	;' ;


	
	$rs = $conn->Execute($sql) ;
	
	while (!$rs->EOF) {
		$vv = $rs->fields ;
		
		$vv['bStatus'] = StatusConvert($vv['bStatus']) ;
		$vv['bCategory'] = CategoryConvert($vv['bCategory']) ;
		
		$addr = '' ;
		$addr = convtAddr($vv['city'],$vv['district'],$vv['bAddress']) ;

		if ($vv['bReStart'] != '0000-00-00 00:00:00' && $vv['bStatus'] == '營業中') {
		
			$tmp = explode(' ', $vv['bReStart']);
			$statusTime = $tmp[0];
			unset($tmp);
		}elseif ($vv['bStatus'] == '退店' || $vv['bStatus'] == '暫停') {
			$tmp = explode(' ', $vv['bStatusTime']);
			$statusTime = $tmp[0];
			unset($tmp);
		}else{
			$statusTime = '0000-00-00';
		}

		$vv['sales'] = getSales($vv['bId'],2);
		
		$arr[] = array('id'=>$vv['bId'], 'code'=>$vv['code'], 'name'=>$vv['brand'], 'store'=>$vv['bStore'], 'status'=>$vv['bStatus'], 'category'=>$vv['bCategory'], 'addr'=>$addr,'sales'=>$vv['sales'],'statusTime'=>$statusTime) ;
		unset($vv) ;
		$rs->MoveNext() ;
	}
	
	return $arr ;
}

//整理門牌地址
Function convtAddr($city='', $district='', $addr='') {
	$addr = preg_replace("/$city/iu","",$addr) ;
	$addr = preg_replace("/$district/iu","",$addr) ;
	$addr = $city.$district.$addr ;
	
	return $addr ;
}
##

//取得資料內容(地政士)
Function getData2 ($_status, $_time, $_zip,  $_cat='') {
	global $conn, $exceptbId ;
	$arr = array() ;

	if ($_cat) {
		$_cat = ' AND sCategory ="'.$_cat.'"';
	}

	/* 	$sql = "SELECT
					*
				FROM 
					tScrivener
				WHERE 
					sStatus = '".$_status."' 
					".$_zip."
					AND sModify_time >= '".$_time."-01 00:00:00'
					AND sModify_time <= '".$_time."-31 23:59:59'
					AND sId NOT IN (".implode(',',$exceptbId).")
				ORDER BY
		 			sId
		 		ASC";
		 		// echo $sql;
	 */
	$sql = "SELECT
				*
			FROM 
				tScrivener
			WHERE 
				sStatus = '".$_status."' 
				".$_zip."
				AND sStatusTime >= '".$_time."-01 00:00:00'
				AND sStatusTime <= '".$_time."-31 23:59:59'
				AND sId NOT IN (".implode(',',$exceptbId).")
			ORDER BY
	 			sId
	 		ASC";
	 		// echo $sql;
	
	$rs = $conn->Execute($sql) ;
	
	while (!$rs->EOF) {
		$k = $rs->fields ;
		
		$k['sStatus'] = StatusConvert2($k['sStatus']) ;
		$k['sCategory'] = CategoryConvert($k['sCategory']) ;
		$k['sBrand'] = CategoryScrinver($k['sBrand']);
		
		
		// unset($tmp);
		if ($k['sReStart'] != '0000-00-00 00:00:00' && $k['sStatus'] == '合作') {
			$statusTime = $k['sReStart'] ;
		}elseif ($k['sStatus'] == '終止') {
			$tmp = explode(' ', $k['sStatusTime']);
			$statusTime = $tmp[0];
			unset($tmp);
		}else{
			$statusTime = '0000-00-00';
		}

		$k['sales'] = getSales($k['sId'],2);

		$arr[] = array('id'=>$k['sId'], 'name'=>$k['sName'], 'store'=>$k['sOffice'], 'status'=>$k['sStatus'], 'category'=>$k['sCategory'],'brand'=>$k['sBrand'],'statusTime'=>$statusTime,'sales'=>$k['sales']) ;
		
		unset($vv) ;
		$rs->MoveNext() ;
	}
	
	return $arr ;
}
##

//加盟直營
Function CategoryConvert($vv='') {
	if ($vv == '1') $vv = '加盟' ;
	else if ($vv == '2') $vv = '直營' ;
	else if ($vv == '3') $vv = '非仲介成交' ;
	
	return $vv ;
}
##

//狀態轉換(Branch)
Function StatusConvert($ss='') {
	if ($ss == '1') $ss = '營業中' ;
	else if ($ss == '2') $ss = '退店' ;
	else if ($ss == '3') $ss = '暫停' ;
	
	return $ss ;
}

//狀態轉換(Scrivener)
Function StatusConvert2($ss='') {
	if ($ss == '1') $ss = '合作' ;
	else if ($ss == '2') $ss = '終止' ;
	else if ($ss == '3') $ss = '重複建檔' ;
	else if ($ss == '4') $ss = '未簽約' ;
	
	return $ss ;
}
function CategoryScrinver($arr){

	$ss = array();
	$tmp = explode(',', $arr);

	for ($i=0; $i < count($tmp); $i++) { 
		if ($tmp[$i] == 2) {
			$ss[]= '非仲介成交';
		}elseif ($tmp[$i] == 1) {
			$ss[]= '台灣房屋';
		}elseif ($tmp[$i] == 49) {
			$ss[]= '優美地產';
		}
	}

	rsort($ss);
	return @implode(',', $ss);
	// $menu_brand = array(2 => '非仲介成交',1=> '台灣房屋',49=>'優美地產');
}

function getSales($id,$type){
	global $conn;

	if ($type == 1) { //地政士
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales  ) AS sales FROM tScrivenerSales WHERE sScrivener ='".$id."'";
	}else{
		// $sql = "";
		$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales ) AS sales FROM tBranchSales WHERE bBranch ='".$id."'";
	}
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$tmp[] = $rs->fields['sales'];

		$rs->MoveNext();
	}
	return @implode($tmp, ',');
}
##


//基準時間(沒用到)
// $baseDate = '2016-03-03' ;
// $baseDate = '2016-01-01' ;
##

//取得變數
$v = escapeStr($_POST) ;
//print_r($v) ; exit ;

$db = '' ;
if ($v['type'] == '1') $db = 'tScrivener' ;
else if ($v['type'] == '2') $db = 'tBranch' ;
//die($db) ; 
##
$zip = '' ;
##業務只能看自己負責的人
if ($_SESSION['member_test'] != 0) {
	if ($_SESSION['member_test'] == 1) {
		$sql = "SELECT zZip FROM `tZipArea` WHERE `zCity` LIKE  '%苗栗%'   OR `zCity` LIKE  '%新竹%' ";
	}elseif ($_SESSION['member_test'] == 2) {
		$sql = "SELECT zZip FROM `tZipArea` WHERE `zCity` LIKE  '%台中%' OR `zCity` LIKE  '%彰化%' OR `zCity` LIKE  '%南投%' OR `zCity` LIKE  '%新竹%' ";
	}elseif ($_SESSION['member_test'] == 3 || $_SESSION['member_test'] >= 5) {
		$sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '".$_SESSION['member_test']."'";
	}elseif ($_SESSION['member_test'] == 4) {
		$sql = "SELECT zZip FROM `tZipArea` WHERE `zCity` = '高雄市' OR `zCity` = '屏東縣'  OR `zCity` = '澎湖縣'";
	}

		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$test_tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}
		if ($db=='tScrivener') {
			$queryStr = ' AND sCpZip1 IN('.@implode(',', $test_tmp).')  AND sCategory != 2'; 
		}else{
			$queryStr = ' AND bZip IN('.@implode(',', $test_tmp).')  AND bCategory != 2'; 
		}
			// $query .= "bZip IN(".implode(',', $test_tmp).")";
		
		unset($test_tmp);
}elseif ($_SESSION['member_pDep'] == 7) {
		// $sql = "SELECT FROM WHERE pDep IN()";
		if ($db == 'tScrivener') {
			$sql = "SELECT sScrivener AS store FROM tScrivenerSales WHERE sSales = '".$_SESSION['member_id']."'";
			$col = 'sId';
		}else{
			$sql = "SELECT bBranch AS store FROM tBranchSales WHERE bSales = '".$_SESSION['member_id']."'";	
			$col = 'bId';
		}

		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
				$test_tmp[] = "'".$rs->fields['store']."'";;

			$rs->MoveNext();
		}

		$queryStr = " AND ".$col." IN (".@implode(',', $test_tmp).")";

}
##

//取出資料(地政士)
if ($db == 'tScrivener') {
	$exceptbId = array(632, 575,552,620,411,224) ;//地政士(排除奇怪的:632=業務專用 575=陳政祺 552=王泰翔 620=吳效承 411=吳)
	//郵遞區號
	
	if (empty($v['qDistinct'])) {
		if (!empty($v['qCity'])) {
			$arr = array() ;
			$arr = getDistinct($v['qCity']) ;
			
			$_arr = array() ;
			foreach ($arr as $_k => $_v) {
				$_arr[] = $_k ;
			}
			
			if (count($_arr) > 0) $zip = ' AND sZip1 IN ('.implode(',',$_arr).') ' ;
		}
	}
	else $zip = ' AND sZip1 = "'.$v['qDistinct'].'" ' ;

	$zip .= $queryStr;

	unset($queryStr);
	##
	
	//時間區間
	$monthGap = array() ;
	$fromDate = '' ;
	$toDate = '' ;
	if ($v['fyr'] && $v['fmn'] && $v['tyr'] && $v['tyr']) {
		$fromDate = $v['fyr'].'-'.$v['fmn'] ;
		$toDate = $v['tyr'].'-'.$v['tmn'] ;
		
		//區間月份
		$monthGap = countDiffDate($fromDate, $toDate, 'm') ;
		//print_r($monthGap) ; exit ;
		##
	}
	##
	
	//歷史以來地政士總數
	$scr = array() ;
	// $sql = 'SELECT * FROM '.$db.' AS a WHERE sStatus IN (1, 2) AND sCreat_time <= "'.$toDate.'-31 23:59:59" AND sId NOT IN ('.implode(',',$exceptbId).') '.$zip.' ORDER BY sId ASC;' ;
	$sql = 'SELECT * FROM '.$db.' AS a WHERE sStatus IN (1, 2) AND sId NOT IN ('.implode(',',$exceptbId).') '.$zip.' ORDER BY sId ASC;' ;
	$rs = $conn->Execute($sql)  ;
	while (!$rs->EOF) {
		$k = $rs->fields ;
		$k['sStatus'] = StatusConvert2($k['sStatus']) ;
		$k['sBrand'] = CategoryScrinver($k['sBrand']);
		$k['sales'] = getSales($k['sId'],1);
		$k['sCategory'] = CategoryConvert($k['sCategory']) ;

		if ($k['sReStart'] != '0000-00-00 00:00:00' && $k['sStatus'] == '合作') {
			$statusTime = $k['sReStart'] ;
		}elseif ($k['sStatus'] == '終止') {
			$tmp = explode(' ', $k['sStatusTime']);
			$statusTime = $tmp[0];
			unset($tmp);
		}else{
			$statusTime = '0000-00-00';
		}
		
		unset($tmp);
		$scr[] = array('id'=>$k['sId'], 'name'=>$k['sName'], 'store'=>$k['sOffice'], 'status'=>$k['sStatus'], 'category'=>$k['sCategory'],'brand'=>$k['sBrand'],'sales'=>$k['sales'],'statusTime'=>$statusTime) ;
		
		unset($k) ;
		$rs->MoveNext() ;
	}
	// print_r($scr) ; exit ;
	##
	
	//時間點之前的歇業地政士數
	$stop_scr = array() ;
	/* 	$sql = '
			SELECT
				*
			FROM
				'.$db.' AS a
			WHERE 
				sId NOT IN ('.implode(',',$exceptbId).') 
				AND sModify_time < "'.$fromDate.'-01 00:00:00"
				AND sStatus = "2"
				'.$zip.'
			ORDER BY
				sId
			ASC
		;' ;
	 */	
	$sql = '
		SELECT
			*
		FROM
			'.$db.' AS a
		WHERE 
			sId NOT IN ('.implode(',',$exceptbId).') 
			AND sStatusTime < "'.$fromDate.'-01 00:00:00"
			AND sStatus = "2"
			'.$zip.'
		ORDER BY
			sId
		ASC
	;' ;
	// echo $sql;
	$rs = $conn->Execute($sql)  ;
	while (!$rs->EOF) {
		$tmp = $rs->fields;

		$tmp['sStatus'] = StatusConvert2($tmp['sStatus']) ;
		$tmp['sCategory'] = CategoryConvert($tmp['sCategory']) ;
		$tmp['sBrand'] = CategoryScrinver($tmp['sBrand']);
		$tmp['sales'] = getSales($tmp['sId'],1);

		if ($k['sReStart'] != '0000-00-00 00:00:00' && $k['sStatus'] == '合作') {
			$statusTime = $k['sReStart'] ;
			$tmp = explode(' ', $k['sReStart']);
			$statusTime = $tmp[0];
			unset($tmp);
		}elseif ($k['sStatus'] == '終止') {
			$tmp = explode(' ', $k['sStatusTime']);
			$statusTime = $tmp[0];
			unset($tmp);
		}else{
			$statusTime = '0000-00-00';
		}

		$stop_scr[] = array('id'=>$tmp['sId'], 'name'=>$tmp['sName'], 'store'=>$tmp['sOffice'], 'status'=>$tmp['sStatus'], 'category'=>$tmp['sCategory'],'brand'=>$tmp['sBrand'],'sales'=>$tmp['sales'],'statusTime'=>$statusTime) ;

		unset($tmp);

		$rs->MoveNext();
	}
	

	//取得大於查詢時間的店家(建立時間)
	$sql = "
			SELECT
				sId,
				sCreat_time
			FROM
				".$db."
			WHERE
				sCreat_time > '".$toDate."-31 23:59:59'
				AND sId NOT IN (".implode(',',$exceptbId).")
				AND sStatus IN (1, 2)
			ORDER BY
				sId ASC";

	$new_scr = array();
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$new_scr[] = $rs->fields;
		$rs->MoveNext();
	}
	
	
	//相減取得存活店家數(需再扣除建立時間大於查詢時間的)
	$tmpTime = $toDate.'-31 23:59:59';
	$aliveScr = array() ;
	foreach ($scr as $_k => $_v) {
		$sid = $_v['id'] ;
		$tf = false ;
		foreach ($stop_scr as $_ka => $_va) {
			if ($sid == $_va['sId'])  $tf = true ;
		}

		foreach ($new_scr as $_ka2 => $_va2) {
			if ($sid == $_va2['sId'])  $tf = true ;
		}

		
		if (!$tf) {
			if ($_v['statusTime'] > $tmpTime) { //更改非查詢月關店的狀態錯誤(EX:該店3月關店，查詢一月時狀態為關店狀況)
				
				$_v['status'] = '營業中';
			}
			$aliveScr[] = $_v ;
			//print_r($aliveStore) ; exit ;
		}
	}
	unset($tmpTime);
	//print_r($aliveStore) ; exit ;
	$totalScr = count($aliveScr) ;
	##
	
	//取得相關資料
	$data = array() ;
	$closeScr = array() ;
	$pauseScr = array() ;
	foreach ($monthGap as $_k => $_v) {
		$data[$_k]['date'] = $_v ;
		$data[$_k]['total'] = $totalScr ;
		
		//終止(停用)
		$_arr = array() ;
		$_arr = getData2 (2, $_v, $zip) ;
		$data[$_k]['close'] = count($_arr) ;
		$closeScr = array_merge($closeScr, $_arr) ;
		unset($_arr) ;
		##
		
		//小計
		$totalScr -= $data[$_k]['close'] ;
		$data[$_k]['balance'] = $totalScr ;
		##
	}

	require_once dirname(dirname(__FILE__)).'/includes/report/storeCloseScrXLS.php' ;
	// require_once 'includes/storeCloseScrXLS.php' ;
}
##

//取出資料(仲介)
else if ($db == 'tBranch') {
	$exceptbId = array(0, 505, 980, 1012, 1372) ; //剔除名單
	//郵遞區號
	$zip = '' ;
	if (empty($v['qDistinct'])) {
		if (!empty($v['qCity'])) {
			$arr = array() ;
			$arr = getDistinct($v['qCity']) ;
			
			$_arr = array() ;
			foreach ($arr as $_k => $_v) {
				$_arr[] = $_k ;
			}
			
			if (count($_arr) > 0) $zip = ' AND bZip IN ('.implode(',',$_arr).') ' ;
		}
	}
	else $zip = ' AND bZip = "'.$v['qDistinct'].'" ' ;
	$zip .= $queryStr;
	
	unset($queryStr);
	##
	
	//時間區間
	$monthGap = array() ;
	$fromDate = '' ;
	$toDate = '' ;
	if ($v['fyr'] && $v['fmn'] && $v['tyr'] && $v['tyr']) {
		$fromDate = $v['fyr'].'-'.$v['fmn'] ;
		$toDate = $v['tyr'].'-'.$v['tmn'] ;
		
		//區間月份
		$monthGap = countDiffDate($fromDate, $toDate, 'm') ;
		//print_r($monthGap) ; exit ;
		##
	}
	##
	
	//歷史以來仲介總數
	$realty = array() ;
	$sql = '
		SELECT
			*,
			(SELECT bCode FROM tBrand WHERE a.bBrand=bId) as code,
			(SELECT bName FROM tBrand WHERE a.bBrand=bId) as brand,
			(SELECT zCity FROM tZipArea WHERE a.bZip=zZip) as city,
			(SELECT zArea FROM tZipArea WHERE a.bZip=zZip) as district
		FROM
			'.$db.' AS a
		WHERE 1 
			'.$zip.'
			AND bId NOT IN ('.implode(',',$exceptbId).')
		ORDER BY
			bBrand, bCategory, bId
		ASC
	;' ;
	// echo $sql;
	// echo "<br>";
	$rs = $conn->Execute($sql)  ;
	//echo $sql ; exit ;
	while (!$rs->EOF) {
		$k = $rs->fields ;
		
		$k['bStatus'] = StatusConvert($k['bStatus']) ;
		$k['bCategory'] = CategoryConvert($k['bCategory']) ;
		$k['sales'] = getSales($k['bId'],2);
		$addr = '' ;
		$addr = convtAddr($k['city'],$k['district'],$k['bAddress']) ;


		if ($k['bReStart'] != '0000-00-00 00:00:00' && $k['bStatus'] == '營業中') {
		
			$tmp = explode(' ', $k['bReStart']);
			$statusTime = $tmp[0];
			unset($tmp);
		}elseif ($k['bStatus'] == '退店' || $k['bStatus'] == '暫停') {
			$tmp = explode(' ', $k['bStatusTime']);
			$statusTime = $tmp[0];
			unset($tmp);
		}else{
			$statusTime = '0000-00-00';
		}
		
		$realty[] = array('id'=>$k['bId'], 'code'=>$k['code'], 'name'=>$k['brand'], 'store'=>$k['bStore'], 'status'=>$k['bStatus'], 'category'=>$k['bCategory'], 'addr'=>$addr,'sales'=>$k['sales'],'statusTime'=>$statusTime) ;
		
		unset($k) ;
		$rs->MoveNext() ;
	}
	//print_r($realty) ; exit ;
	##
	
	//時間點之前的歇業店家數
/* 	$sql = '
		SELECT
			*,
			(SELECT bName FROM tBrand WHERE a.bBrand=bId) as brand
		FROM
			'.$db.' AS a
		WHERE
			bStatus <> "1"
			AND bId NOT IN ('.implode(',',$exceptbId).')
			AND bModify_time < "'.$fromDate.'-01 00:00:00"
			'.$zip.'
		ORDER BY
			bBrand, bCategory, bId
		ASC
	;' ;
 */	$sql = '
		SELECT
			*,
			(SELECT bName FROM tBrand WHERE a.bBrand=bId) as brand
		FROM
			'.$db.' AS a
		WHERE
			bStatus <> "1"
			AND bId NOT IN ('.implode(',',$exceptbId).')
			AND bStatusTime < "'.$fromDate.'-01 00:00:00"
			'.$zip.'
		ORDER BY
			bBrand, bCategory, bId
		ASC
	;' ;
	$rs = $conn->Execute($sql)  ;
	
	$derealty = array() ;
	while (!$rs->EOF) {
		$derealty[] = $rs->fields ;
		$rs->MoveNext() ;
	}
	
	//print_r($derealty) ; exit ;

	//取得大於查詢時間的店家(建立時間)
	$sql = "
			SELECT
				bId,
				bCreat_time
			FROM 
				tBranch
			WHERE
				bCreat_time > '".$toDate."-31 23:59:59'
				AND bId NOT IN (".implode(',',$exceptbId).")
				".$zip."
			ORDER BY
				bBrand, bCategory, bId
			ASC
			";
	
	$derealty2 = array();
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$derealty2[] = $rs->fields;
		$rs->MoveNext();
	}
	##
	
	//相減取得存活店家數(需再扣除建立時間大於查詢時間的)
	$aliveStore = array() ;
	$tmpTime = $toDate.'-31 23:59:59';
	foreach ($realty as $_k => $_v) {
		$bid = $_v['id'] ;
		$tf = false ;
		foreach ($derealty as $_ka => $_va) {
			if ($bid == $_va['bId'])  $tf = true ;
		}

		foreach ($derealty2 as $_ka2 => $_va2) {
			if ($bid == $_va2['bId'])  $tf = true ;
		}

		
		if (!$tf) {
				
			if ($_v['statusTime'] > $tmpTime) { //更改非查詢月關店的狀態錯誤(EX:該店3月關店，查詢一月時狀態為關店狀況)
				
				$_v['status'] = '營業中';
			}
			$aliveStore[] = $_v ;
			//print_r($aliveStore) ; exit ;
		}
	}
	unset($tmpTime);
	
	
	//print_r($aliveStore) ; exit ;
	$totalStore = count($aliveStore) ;
	##
	
	//取得相關資料
	$data = array() ;
	$closeStore = array() ;
	$pauseStore = array() ;


	foreach ($monthGap as $_k => $_v) {
		$data[$_k]['date'] = $_v ;
		$data[$_k]['total'] = $totalStore ;
		
		//退店()
		$_arr = array() ;
		$_arr = getData (2, $_v, $zip) ;
		$data[$_k]['close'] = count($_arr) ;
		$closeStore = array_merge($closeStore, $_arr) ;
		unset($_arr) ;
		##
		
		//暫停
		$_arr = array() ;
		$_arr = getData (3, $_v, $zip) ;
		$data[$_k]['pause'] = count($_arr) ; ;
		$pauseStore = array_merge($pauseStore, $_arr) ;
		unset($_arr) ;
		##
		
		//小計
		$totalStore -= ($data[$_k]['close'] + $data[$_k]['pause']) ;
		$data[$_k]['balance'] = $totalStore ;
		##
	}
	
	##
	
	require_once dirname(dirname(__FILE__)).'/includes/report/storeCloseBranchXLS.php' ;
	// require_once 'storeCloseBranchXLS.php' ;
}
##
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// die;

$_file = 'storeClose.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");

exit ;
?>