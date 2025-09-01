<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php';
include_once '../report/getBranchType.php';

$salesId = $_POST["s"] ;
if (preg_match("/^\D+$/",$salesId)) die() ;

if($salesId == 6) $salesId = 25 ;

$nowDate = date("Y-m-d H:i:s") ;

$sql = "SELECT * FROM tSalesTracking WHERE sStatus = 0 AND sCategory  = 1 AND sSales ='".$salesId."'";
$rs = $conn->Execute($sql);
$scr = '';
$i = 0; $j =0;
while (!$rs->EOF) {

	if ($rs->fields['sType'] == 1) {
		if ($trace == 'trace') {
			$_scr1[] = array('sId' => $rs->fields['sStoreId'], 'name' => $rs->fields['sName'], 'office' => $rs->fields['sOffice'], 'diff' => $rs->fields['sNoCaseDay']) ;
		}else{
			$colorIndex = '#99FFFF' ;
			if ($i % 2 == 1) $colorIndex = '#AAFFEE' ;
						
			if ($rs->fields['sOffice']) $rs->fields['sOffice'] = '('.$rs->fields['sOffice'].')' ;
			
			$scr1 .= '
				<tr style="background-color: '.$colorIndex.';">
					<td style="width:250px;"><a href="#" onclick="redirectScr('.$rs->fields['sStoreId'].')">'.$rs->fields['sName'].$rs->fields['sOffice'].'</a>&nbsp;</td>
					<td style="text-align:right;"><span style="color:#FF0000;font-size:14pt;font-weight:bold;padding-right:20px;">'.number_format($rs->fields['sNoCaseDay']).'<span></td>
				</tr>
			' ;
			$i++;
		}
			
	}else{
		if ($trace == 'trace') {
			$_realty1[] = array('bId' => $rs->fields['sStoreId'], 'brand' => $rs->fields['sName'], 'store' => $rs->fields['sOffice'], 'diff' => $rs->fields['sNoCaseDay']) ;
		}else {
			$colorIndex = '#99FFFF' ;
			if ($j % 2 == 1) $colorIndex = '#AAFFEE' ;
					
					
			$realty1 .= '
				<tr style="background-color: '.$colorIndex.';">
					<td>'.$rs->fields['sName'].'</td>
					<td><a href="#" onclick="redirectreal('.$rs->fields['sStoreId'].')">'.$rs->fields['sOffice'].'</a></td>
					<td style="text-align:right;"><span style="color:#FF0000;font-size:14pt;font-weight:bold;padding-right:20px;">'.number_format($rs->fields['sNoCaseDay']).'<span></td>
				</tr>
			' ;
			$j++;
		}
			
	}

		

		
	$rs->MoveNext();
}



// if ($trace == 'trace') {
// 	$lastYear = $dateYear + 1911 ;
// 	$lastMonth = $dateMonth ;
// 	$lastDate = date("d") ;
	
// 	$nowDate = date("Y-m-d",mktime(0,0,0,$lastMonth,$lastDate,$lastYear)).' '.date("H:i:s") ;
// 	//echo $nowDate ; exit ;
// }
// else $nowDate = date("Y-m-d H:i:s") ;

// //取得地政士列表
// $scriverSales = array() ;
// $scr1 = '' ;

// $sql = '
// 	SELECT
// 		b.sId,
// 		a.sSignDate,
// 		b.sName,
// 		b.sOffice
// 	FROM
// 		tSalesSign AS a
// 	JOIN
// 		tScrivener AS b ON a.sStore = b.sId
// 	WHERE
// 		a.sSales = "'.$salesId.'"
// 		AND a.sType = "1"
// 	ORDER BY
// 		a.sId
// 	ASC;
// ' ;

// $rs = $conn->Execute($sql) ;

// //print_r($rs) ; exit ;
// while (!$rs->EOF) {
// 	$scriverSales[] = $rs->fields ;
// 	$rs->MoveNext() ;
// }
// //print_r($scriverSales) ; exit ;
// $i = 0 ;
// foreach ($scriverSales as $k => $v)  {
// 	//件數
// 	$thisTotal = 0 ;
// 	$sql_ext = '' ;
	
// 	if ($trace == 'trace') $sql_ext = ' AND a.cApplyDate <= "'.$nowDate.'" ' ;
// 	$sql = '
// 		SELECT
// 			COUNT(b.cCertifiedId) as total
// 		FROM
// 			tContractScrivener AS b 
// 		JOIN
// 			tContractCase AS a ON a.cCertifiedId=b.cCertifiedId
// 		WHERE
// 			b.cScrivener = "'.$v['sId'].'"
// 			'.$sql_ext.'
// 	;' ;
// 	$rel = $conn->Execute($sql) ;
// 	$thisTotal = (int)$rel->fields['total'] ;
// 	##
	
// 	if ($thisTotal > 0) unset ($scriverSales[$k]) ;
// 	else {
// 		$diff = 0 ;
// 		$diff = round((strtotime($nowDate) - strtotime($v['sSignDate'])) / 3600 / 24) ;
		
// 		if ($trace == 'trace') {
// 			$_scr1[] = array('sId' => $v['sId'], 'name' => $v['sName'], 'office' => $v['sOffice'], 'diff' => $diff) ;
// 		}
// 		else {
// 			$colorIndex = '#99FFFF' ;
// 			if ($i % 2 == 1) $colorIndex = '#AAFFEE' ;
			
// 			if ($v['sOffice']) $v['sOffice'] = '('.$v['sOffice'].')' ;
			
// 			$scr1 .= '
// 				<tr style="background-color: '.$colorIndex.';">
// 					<td style="width:250px;"><a href="#" onclick="redirectScr('.$v['sId'].')">'.$v['sName'].$v['sOffice'].'</a>&nbsp;</td>
// 					<td style="text-align:right;"><span style="color:#FF0000;font-size:14pt;font-weight:bold;padding-right:20px;">'.number_format($diff).'<span></td>
// 				</tr>
// 			' ;
			
// 			$i ++ ;
// 		}
// 	}
// }
// //print_r($scriverSales) ; exit ;
// if (empty($scr1) && ($trace != 'trace')) {
// 	$scr1 = '
// 				<tr style="background-color: #99FFFF;">
// 					<td colspan="2">查無相關資料 ...</td>
// 				</tr>
// 	' ;
// }
// ##

// //取得仲介店列表
// $realtySales = array() ;
// $realty1 = '' ;

// $sql = '
// 	SELECT
// 		b.bId,
// 		a.sSignDate,
// 		(SELECT bName FROM tBrand WHERE b.bBrand=bId) as bBrand,
// 		b.bStore
// 	FROM
// 		tSalesSign AS a
// 	JOIN
// 		tBranch AS b ON a.sStore = b.bId
// 	WHERE
// 		a.sSales = "'.$salesId.'"
// 		AND a.sType = "2"
// 	ORDER BY
// 		a.sId
// 	ASC;
// ' ;

// $rs = $conn->Execute($sql) ;
// $i = 0 ;
// while (!$rs->EOF) {
// 	$realtySales[] = $rs->fields ;
// 	$rs->MoveNext() ;
// }

// foreach ($realtySales as $k => $v)  {
// 	//件數
// 	$thisTotal = 0 ;
// 	$sql_ext = '' ;
// 	/*
// 	if ($trace == 'trace') $sql_ext = ' AND a.cApplyDate <= "'.$nowDate.'" ' ;
// 	$sql = '
// 		SELECT
// 			COUNT(b.cCertifyId) as total
// 		FROM
// 			tContractRealestate AS b
// 		JOIN
// 			tContractCase AS a ON a.cCertifiedId=b.cCertifyId
// 		WHERE
// 			b.cBranchNum = "'.$v['bId'].'"
// 			OR b.cBranchNum1 = "'.$v['bId'].'"
// 			OR b.cBranchNum2 = "'.$v['bId'].'"
// 			'.$sql_ext.'
// 	;' ;
// 	$rel = $conn->Execute($sql) ;
// 	$thisTotal = (int)$rel->fields['total'] ;
// 	##
// 	*/
// 	//if ($thisTotal > 0) unset ($realtySales[$k]) ;
// 	$sql = '
// 		SELECT
// 			b.cCertifyId
// 		FROM
// 			tContractRealestate AS b
// 		WHERE
// 			b.cBranchNum = "'.$v['bId'].'"
// 			OR b.cBranchNum1 = "'.$v['bId'].'"
// 			OR b.cBranchNum2 = "'.$v['bId'].'"
// 	;' ;
// 	$rel = $conn->Execute($sql) ;
	
// 	if ($rel->EOF) $realtySales[$k]['cid'] = '' ;
// 	else $realtySales[$k]['cid'] = $rel->fields['cCertifyId'] ;
	
// 	if ($realtySales[$k]['cid']) {
// 		$sql = 'SELECT * FROM tContractCase WHERE cCertifiedId="'.$realtySales[$k]['cid'].'" AND cApplyDate <= "'.$nowDate.'";' ;
// 		$rel = $conn->Execute($sql) ;
// 		if ($rel->EOF) $realtySales[$k]['cid'] = '' ;
// 	}
// 	//$thisTotal = (int)$rel->fields['total'] ;
// 	##
// 	//print_r($bid) ;
// 	//if ($thisTotal > 0) unset ($realtySales[$k]) ;
// 	if ($realtySales[$k]['cid']) unset($realtySales[$k]) ;
// 	else {
// 		$diff = 0 ;
// 		$diff = round((strtotime($nowDate) - strtotime($v['sSignDate'])) / 3600 / 24) ;

// 		if ($trace == 'trace') {
// 			$v['bBrand'] = str_replace('自有品牌(類型點選加盟)','自有品牌',$v['bBrand']) ;
// 			$_realty1[] = array('bId' => $v['bId'], 'brand' => $v['bBrand'], 'store' => $v['bStore'], 'diff' => $diff) ;
// 		}
// 		else {
// 			$colorIndex = '#99FFFF' ;
// 			if ($i % 2 == 1) $colorIndex = '#AAFFEE' ;
			
// 			$v['bBrand'] = str_replace('自有品牌(類型點選加盟)','自有品牌',$v['bBrand']) ;
			
// 			$realty1 .= '
// 				<tr style="background-color: '.$colorIndex.';">
// 					<td>'.$v['bBrand'].'</td>
// 					<td><a href="#" onclick="redirectreal('.$v['bId'].')">'.$v['bStore'].'</a></td>
// 					<td style="text-align:right;"><span style="color:#FF0000;font-size:14pt;font-weight:bold;padding-right:20px;">'.number_format($diff).'<span></td>
// 				</tr>
// 			' ;
				
// 			$i ++ ;
// 		}
// 	}
// }

// if (empty($realty1) && ($trace != 'trace')) {
// 	$realty1 = '
// 				<tr style="background-color: #99FFFF">
// 					<td colspan="3">查無相關資料 ...</td>
// 				</tr>
// 	' ;
// }
##

//$smarty->assign('scr',$scr);
//$smarty->assign('realty',$realty) ;

//echo json_encode(array($scr, $realty)) ;
if ($trace != 'trace') echo $scr1.'＿'.$realty1 ;
?>