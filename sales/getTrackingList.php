<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php';
include_once '../report/getBranchType.php';

$salesId = $_POST["s"] ;
if (preg_match("/^\D+$/",$salesId)) die() ;

if($salesId == 6) $salesId = 25 ;

$lastYear = date("Y",strtotime("-1 month")) ;
$lastMonth = date("m",strtotime("-1 month")) ;

$last2Year = date("Y",strtotime("-2 month")) ;
$last2Month = date("m",strtotime("-2 month")) ;



$sql = "SELECT * FROM tSalesTracking WHERE sStatus = 0 AND sCategory  = 2 AND sSales ='".$salesId."'";
$rs = $conn->Execute($sql);
$scr = '';
$i = 0; $j =0;
while (!$rs->EOF) {

	if ($rs->fields['sType'] == 1) {
		if ($trace == 'trace') {
			$_scr[] = array('sId' => $rs->fields['sStoreId'], 'name' => $rs->fields['sName'], 'office' => $rs->fields['sOffice'], 'lastMonth' => $lastMonth, 'last2Month' => $last2Month, 'lastTotal' => $rs->fields['sLastMonthCase'], 'thisTotal' => $rs->fields['sMonthCase']) ;
		}else{
			$colorIndex = '#99FFFF' ;
			if ($i % 2 == 1) $colorIndex = '#AAFFEE' ;
						
			if ($rs->fields['sOffice']) $rs->fields['sOffice'] = '<br>('.$rs->fields['sOffice'].')';
			$scr .= '
					<tr style="background-color: '.$colorIndex.';">
						<td style="width:150px;"><a href="#" onclick="redirectScr('.$rs->fields['sStoreId'].')">'.$rs->fields['sName'].$rs->fields['sOffice'].'</a>&nbsp;</td>
						<td style="text-align:center;">'.number_format($rs->fields['sLastMonthCase']).'</td>
						<td style="text-align:center;">'.number_format($rs->fields['sMonthCase']).'</td>
					</tr>
				' ; 
			$i++;
		}
			
	}else{
		if ($trace == 'trace') {
			$val['brand'] = str_replace('自有品牌(類型點選加盟)','自有品牌',$val['brand']) ;
			$_realty[] = array('bId' => $rs->fields['sStoreId'], 'brand' => $rs->fields['sName'], 'store' => $rs->fields['sOffice'], 'lastMonth' => $lastMonth, 'last2Month' => $last2Month, 'lastTotal' => $rs->fields['sLastMonthCase'], 'thisTotal' => $rs->fields['sMonthCase']) ;
		}
		else {
			$colorIndex = '#99FFFF' ;
			if ($j % 2 == 1) $colorIndex = '#AAFFEE' ;
					
					
			$realty .= '
				<tr style="background-color: '.$colorIndex.';">
					<td style="width:100px;">'.$rs->fields['sName'].'</td>
					<td style="width:100px;"><a href="#" onclick="redirectreal('.$rs->fields['sStoreId'].')">'.$rs->fields['sOffice'].'</a></td>
					<td style="text-align:center;">'.number_format($rs->fields['sLastMonthCase']).'</td>
					<td style="text-align:center;">'.number_format($rs->fields['sMonthCase']).'</td>
				</tr>
			' ;
			$j++;
		}
			
	}

		

		
	$rs->MoveNext();
}
				




// //取得地政士列表
// $scriverSales = array() ;
// $scr = '' ;

// $sql = 'SELECT a.* FROM tScrivener AS a JOIN tScrivenerSales AS b ON a.sId = b.sScrivener WHERE b.sSales = "'.$salesId.'" AND a.sStatus = "1" ORDER BY a.sId ASC;' ;
// $rs = $conn->Execute($sql) ;
// $i = 0 ;
// //print_r($rs) ; exit ;
// while (!$rs->EOF) {
// 	$scriverSales[] = $rs->fields ;
// 	$rs->MoveNext() ;
// }
// //print_r($scriverSales) ; exit ;

// foreach ($scriverSales as $k => $v)  {
// 	//上月件數
// 	$thisTotal = 0 ;
	
// 	$sql = '
// 		SELECT
// 			COUNT(a.cCertifiedId) as total
// 		FROM
// 			tContractCase AS a
// 		JOIN
// 			tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId
// 		WHERE
// 			a.cSignDate >= "'.$lastYear.'-'.$lastMonth.'-01 00:00:00"
// 			AND a.cSignDate <= "'.$lastYear.'-'.$lastMonth.'-31 23:59:59"
// 			AND b.cScrivener = "'.$v['sId'].'"
// 	;' ;
// 	$rel = $conn->Execute($sql) ;
// 	$thisTotal = (int)$rel->fields['total'] ;
// 	##
// 	//echo $sql ; exit ;
// 	if ($thisTotal <= 0) {
// 		//上上月件數
// 		$lastTotal = 0 ;
		
// 		$sql = '
// 			SELECT
// 				COUNT(a.cCertifiedId) as total
// 			FROM
// 				tContractCase AS a
// 			JOIN
// 				tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId
// 			WHERE
// 				a.cSignDate >= "'.$last2Year.'-'.$last2Month.'-01 00:00:00"
// 				AND a.cSignDate <= "'.$last2Year.'-'.$last2Month.'-31 23:59:59"
// 				AND b.cScrivener = "'.$v['sId'].'"
// 		;' ;
// 		$rel = $conn->Execute($sql) ;
// 		$lastTotal = (int)$rel->fields['total'] ;
// 		##
// 		//echo $sql ; exit ;
// 		if ($lastTotal > 0) {
// 			if ($trace == 'trace') {
// 				$_scr[] = array('sId' => $v['sId'], 'name' => $v['sName'], 'office' => $v['sOffice'], 'lastMonth' => $lastMonth, 'last2Month' => $last2Month, 'lastTotal' => $lastTotal, 'thisTotal' => $thisTotal) ;
// 			}
// 			else {
// 				$colorIndex = '#99FFFF' ;
// 				if ($i % 2 == 1) $colorIndex = '#AAFFEE' ;
				
// 				if ($v['sOffice']) $v['sOffice'] = '<br>('.$v['sOffice'].')' ;
// 				$scr .= '
// 					<tr style="background-color: '.$colorIndex.';">
// 						<td style="width:150px;"><a href="#" onclick="redirectScr('.$v['sId'].')">'.$v['sName'].$v['sOffice'].'</a>&nbsp;</td>
// 						<td style="text-align:center;">'.number_format($lastTotal).'</td>
// 						<td style="text-align:center;">'.number_format($thisTotal).'</td>
// 					</tr>
// 				' ;
				
// 				$i ++ ;
// 			}
// 		}
// 	}
// }

// if (empty($scr) && ($trace != 'trace')) {
// 	$scr = '
// 				<tr style="background-color: #99FFFF;">
// 					<td colspan="3">查無相關資料 ...</td>
// 				</tr>
// 	' ;
// }
// ##

// //取得仲介店列表
// $realtySales = array() ;
// $realty = '' ;

// $sql = '
// 	SELECT
// 		a.*,
// 		c.bCode,
// 		c.bName as brand
// 	FROM
// 		tBranch AS a 
// 	JOIN
// 		tBranchSales AS b ON a.bId = b.bBranch
// 	JOIN
// 		tBrand AS c ON a.bBrand=c.bId
// 	WHERE
// 		b.bSales = "'.$salesId.'"
// 		AND a.bStatus = "1"
// 	ORDER BY
// 		a.bId
// 	ASC
// ;' ;
// $rs = $conn->Execute($sql) ;
// $i = 0 ;
// while (!$rs->EOF) {
// 	$realtySales[] = $rs->fields ;
// 	$rs->MoveNext() ;
// }

// foreach ($realtySales as $k => $v)  {
// 	//上月件數
// 	$thisTotal = 0 ;
	
// 	$sql = '
// 		SELECT
// 			COUNT(a.cCertifiedId) as total
// 		FROM
// 			tContractCase AS a
// 		JOIN
// 			tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId
// 		WHERE
// 			a.cSignDate >= "'.$lastYear.'-'.$lastMonth.'-01 00:00:00"
// 			AND a.cSignDate <= "'.$lastYear.'-'.$lastMonth.'-31 23:59:59"
// 			AND (b.cBranchNum = "'.$v['bId'].'" OR b.cBranchNum1 = "'.$v['bId'].'" OR b.cBranchNum2 = "'.$v['bId'].'")
// 	;' ;
// 	$rel = $conn->Execute($sql) ;
// 	$thisTotal = (int)$rel->fields['total'] ;
// 	##
	
// 	if ($thisTotal <= 0) {
// 		//上上月件數
// 		$lastTotal = 0 ;
		
// 		$sql = '
// 			SELECT
// 				COUNT(a.cCertifiedId) as total
// 			FROM
// 				tContractCase AS a
// 			JOIN
// 				tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId
// 			WHERE
// 				a.cSignDate >= "'.$last2Year.'-'.$last2Month.'-01 00:00:00"
// 				AND a.cSignDate <= "'.$last2Year.'-'.$last2Month.'-31 23:59:59"
// 				AND (b.cBranchNum = "'.$v['bId'].'" OR b.cBranchNum1 = "'.$v['bId'].'" OR b.cBranchNum2 = "'.$v['bId'].'")
// 		;' ;
// 		$rel = $conn->Execute($sql) ;
// 		$lastTotal = (int)$rel->fields['total'] ;
// 		##
		
// 		if ($lastTotal > 0) {
// 			if ($trace == 'trace') {
// 				$v['brand'] = str_replace('自有品牌(類型點選加盟)','自有品牌',$v['brand']) ;
// 				$_realty[] = array('bId' => $v['bId'], 'brand' => $v['brand'], 'store' => $v['bStore'], 'lastMonth' => $lastMonth, 'last2Month' => $last2Month, 'lastTotal' => $lastTotal, 'thisTotal' => $thisTotal) ;
// 			}
// 			else {
// 				$colorIndex = '#99FFFF' ;
// 				if ($i % 2 == 1) $colorIndex = '#AAFFEE' ;
				
// 				$v['brand'] = str_replace('自有品牌(類型點選加盟)','自有品牌',$v['brand']) ;
				
// 				$realty .= '
// 					<tr style="background-color: '.$colorIndex.';">
// 						<td style="width:100px;">'.$v['brand'].'</td>
// 						<td style="width:100px;"><a href="#" onclick="redirectreal('.$v['bId'].')">'.$v['bStore'].'</a></td>
// 						<td style="text-align:center;">'.number_format($lastTotal).'</td>
// 						<td style="text-align:center;">'.number_format($thisTotal).'</td>
// 					</tr>
// 				' ;
				
// 				$i ++ ;
// 			}
// 		}
// 	}
// }

// if (empty($realty) && ($trace != 'trace')) {
// 	$realty = '
// 				<tr style="background-color: #99FFFF">
// 					<td colspan="4">查無相關資料 ...</td>
// 				</tr>
// 	' ;
// }
##

//$smarty->assign('scr',$scr);
//$smarty->assign('realty',$realty) ;

//echo json_encode(array($scr, $realty)) ;
if ($trace != 'trace') echo $scr.'＿'.$realty ;
?>