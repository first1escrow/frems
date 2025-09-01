<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

##
$city = $_POST['city'];
$area = $_POST['area'];
$cat = $_POST['cat'];//身分
$export = $_POST['export'] ;
$storeType = ($_POST['storeType'])?$_POST['storeType']:0;
$sales = ($_SESSION['member_pDep'] == 7)?$_SESSION['member_id']:'';
$branchId = $_POST['branchId'];
$scrivenerId = $_POST['scrivenerId'];

##

if ($export) {
	$zip = array();
	$store = array();
	$branchStoreId = array();
	$scrivenerStoreId = array();
	$str = '';//SQL查詢條件字串

	if ($area) {
		$sql = "SELECT zZip FROM tZipArea WHERE zCity = '".$city."' AND zArea = '".$area."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			array_push($zip, $rs->fields['zZip']);

			$rs->MoveNext();
		}
	}elseif ($city) {
		$sql = "SELECT zZip FROM tZipArea WHERE zCity = '".$city."'";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			array_push($zip, $rs->fields['zZip']);

			$rs->MoveNext();
		}
	}

	if ($cat == 's') {
		if ($storeType == 1) {
			
			$str .= " AND sId IN(".@implode(',', $scrivenerId).")";
		}

		if (!empty($zip)) {
			$str .= " AND sCpZip1 IN (".@implode(',', $zip).")";
		}

		$sql = "SELECT sId,sOffice,sName,sCreat_time FROM tScrivener WHERE sId NOT IN(632,224) AND sStatus = 1 AND sName NOT LIKE '%業務%' ".$str." ORDER BY sId ASC";
		
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$store[$rs->fields['sId']]['name'] = 'SC'.str_pad($rs->fields['sId'],4,0,STR_PAD_LEFT).$rs->fields['sName']."(".$rs->fields['sOffice'].")";
			$store[$rs->fields['sId']]['creatTime'] = $rs->fields['sCreat_time'];
			array_push($scrivenerStoreId , $rs->fields['sId']);

			$rs->MoveNext();
		}

		$data = array();
		$sql = "SELECT
					cs.cScrivener,
					cc.cSignDate
				FROM
					tContractCase AS cc
				JOIN
					tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
				WHERE
					cs.cScrivener IN(".implode(',', $scrivenerStoreId).")
				";
		
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$date = str_replace('-', '', substr($rs->fields['cSignDate'], 0,10));
			$data[$rs->fields['cScrivener']]['date'][] = $date;
			$rs->MoveNext();
		}
	}else if($cat == 'b'){
		if ($storeType == 1) {
			$str = " AND bId IN(".@implode(',', $branchId).")";
		}


		if (!empty($zip)) {
			$str .= " AND bZip IN (".@implode(',', $zip).")";
		}

		$sql = "SELECT
					bId,
					bStore,
					(SELECT bName FROM tBrand WHERE bId = bBrand) AS brandName,
					(SELECT bCode FROM tBrand WHERE bId = bBrand) AS brandCode,
					bCreat_time
				FROM
					tBranch
				WHERE
					bId NOT IN(1372) AND bStatus = 1 ".$str." ORDER BY bId ASC";

		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			// $store[$rs->fields['bId']]['name'] = $rs->fields['brandCode'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT).$rs->fields['brandName'].$rs->fields['bStore'];

			$store[$rs->fields['bId']]['name'] = $rs->fields['brandCode'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT).$rs->fields['brandName'].$rs->fields['bStore'];
			$store[$rs->fields['bId']]['creatTime'] = $rs->fields['bCreat_time'];
			array_push($branchStoreId , $rs->fields['bId']);
			$rs->MoveNext();
		}

		$sql = "SELECT 
					cr.cBranchNum,
					cr.cBranchNum1,
					cr.cBranchNum2,
					cr.cBranchNum3,
					cc.cSignDate
				FROM
					tContractCase AS cc
				JOIN
					tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
				WHERE
					cr.cBranchNum IN(".@implode(',', $branchStoreId).") OR cr.cBranchNum1 IN (".@implode(',', $branchStoreId).") OR cr.cBranchNum2 IN (".@implode(',', $branchStoreId).")";
		
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$date = str_replace('-', '', substr($rs->fields['cSignDate'], 0,10));
			$data[$rs->fields['cBranchNum']]['date'][] = $date;

			if ($rs->fields['cBranchNum1'] > 0) {
				$data[$rs->fields['cBranchNum1']]['date'][] = $date; 
			}

			if ($rs->fields['cBranchNum2'] > 0) {
				$data[$rs->fields['cBranchNum2']]['date'][] = $date; 
			}

			if ($rs->fields['cBranchNum3'] > 0) {
				$data[$rs->fields['cBranchNum3']]['date'][] = $date; 
			}


			$rs->MoveNext();
		}
	}



	unset($branchStoreId);
	unset($scrivenerStoreId);
	unset($zip);
	include_once 'storeTrackingListExcel.php';
}
##
//設定顯示年份
$y = '' ;
$yr = date("Y") - 1911 ;

for ($i = 0 ; $i < $yr ; $i ++) {
	$y .= '<option value="'.($yr - $i).'"' ;
	if ($i == 0) {
		$y .= ' selected="selected"' ;
	}
	$y .= '>'.($yr - $i)."</option>\n" ;
}
##

//設定顯示月份
$m = '' ;
$mn = date("n") ;

for ($i = 1 ; $i <= 12 ; $i ++) {
	$m .= '<option value="'.$i.'"' ;
	if ($i == $mn) {
		$m .= ' selected="selected"' ;
	}
	$m .= '>'.$i."</option>\n" ;
}
##

//設定顯示區域
$menuArea = '' ;
$str = '1=1';
if ($sales) {
	$str .= ' AND FIND_IN_SET('.$_SESSION['member_id'].',zSales)';
}
$sql = 'SELECT * FROM tZipArea WHERE '.$str.' GROUP BY zCity ORDER BY zZip ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$menuArea .= '<option value="'.$rs->fields['zCity'].'">'.$rs->fields['zCity']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//仲介店
$str = ($sales)?" AND bs.bSales = '".$sales."'":'';
$menuBranch = '' ;

$sql = 'SELECT
			b.bId,
			b.bStore,
			(SELECT bName FROM tBrand WHERE bId = b.bBrand) AS brandName,
			(SELECT bCode FROM tBrand WHERE bId = b.bBrand) AS brandCode
		FROM
			tBranch AS b
		LEFT JOIN
			tBranchSales AS bs ON b.bId=bs.bBranch
		WHERE
			b.bId NOT IN(1372) '.$str.'
		GROUP BY b.bId
		ORDER BY b.bId ASC;' ;
// echo $sql;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$menuBranch .= '<option value="'.$rs->fields['bId'].'">'.$rs->fields['brandCode'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT).$rs->fields['brandName'].$rs->fields['bStore']."</option>\n" ;
	
	$rs->MoveNext() ;
}

//地政士
$str = ($sales)?" AND ss.sSales = '".$sales."'":'';
$menuScrivener = '';
$sql = "SELECT
			s.sId,
			s.sName,
			s.sOffice
		FROM
			tScrivener AS s
		LEFT JOIN
			tScrivenerSales AS ss ON ss.sScrivener = s.sId
		WHERE
			s.sId NOT IN(632) ".$str." GROUP BY s.sId ORDER BY s.sId ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuScrivener .= '<option value="'.$rs->fields['sId'].'">SC'.str_pad($rs->fields['sId'],4,0,STR_PAD_LEFT).$rs->fields['sName']."</option>\n" ;

	$rs->MoveNext();
}
##
$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign("menuArea",$menuArea) ;
$smarty->assign('menuBranch',$menuBranch);
$smarty->assign('menuScrivener',$menuScrivener);
$smarty->assign("menuStoreType",array(0=>'全部',1=>"查詢店家"));
$smarty->assign('storeType',$storeType);
$smarty->display('storeTrackingList.inc.tpl', '', 'report');
?>