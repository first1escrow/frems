<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

###經辦##
$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN (5,6) AND pJob = 1";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$Undertaker[$rs->fields['pId']]['name']  = $rs->fields['pName'];
	$Undertaker[$rs->fields['pId']]['pId']  = $rs->fields['pId'];
	$rs->MoveNext();
}

##區域##
$areaTitle = array(
				'屏東縣'=>'tw-pt',
				'台南市'=>'tw-tn',
				'宜蘭縣'=>'tw-il',
				'嘉義縣'=>'tw-ch',
				'台東縣'=>'tw-tt',
				'澎湖縣'=>'tw-ph',
				'金門縣'=>'tw-km',
				'連江縣'=>'tw-lk',
				'台北市'=>'tw-tw',
				'嘉義市'=>'tw-ch',
				'台中市'=>'tw-th',
				'雲林縣'=>'tw-yl',
				'高雄市'=>'tw-kh',
				'新北市'=>'tw-tp',
				'新竹市'=>'tw-hh',
				'新竹縣'=>'tw-hh',
				'基隆市'=>'tw-cl',
				'苗栗縣'=>'tw-ml',
				'桃園市'=>'tw-ty',
				'彰化縣'=>'tw-cg',
				'花蓮縣'=>'tw-hl',
				'南投縣'=>'tw-nt'
			);

$sql = "SELECT * FROM tZipArea GROUP BY zCity ORDER BY nid ASC";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$area[$areaTitle[$rs->fields['zCity']]]['count'] = 0;

	$rowTitle[$rs->fields['zCity']] = $rs->fields['zCity'];
	$rs->MoveNext();
}
$area['未知']['count'] = 0;



##

$sql = "SELECT 
			cc.cCertifiedId,
			s.sUndertaker1 AS cUndertakerId,
			(SELECT zCity FROM tZipArea WHERE zZip = cp.cZip) AS City
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractProperty AS cp ON (cp.cCertifiedId=cc.cCertifiedId AND cp.cItem = 0)
		LEFT JOIN 
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId 
		LEFT JOIN 
			tScrivener AS s ON s.sId = cs.cScrivener
		WHERE cc.cCaseStatus = 2  AND cc.cCertifiedId<>'' AND cc.cCertifiedId !='005030342' GROUP BY cc.cCertifiedId";

$rs = $conn->Execute($sql);
$total = $rs->RecordCount();
// $total = 0;
while (!$rs->EOF) {
	// $data[$rs->fields['cUndertakerId']]++;
	

	if ($rs->fields['cUndertakerId'] == '' || $rs->fields['cUndertakerId'] == '41' || $rs->fields['cUndertakerId'] == '38') {
		$data[$rs->fields['cUndertakerId']]['case'][] = $rs->fields;
	}else{
		// $total++;
		$city = ($rs->fields['City'] == '')? '未知':$areaTitle[$rs->fields['City']]; 
		$city2 = ($rs->fields['City'] == '')? '未知':$rs->fields['City']; 
		
		$area[$city]['count']++;

		$data[$rs->fields['cUndertakerId']]['count'][$city2]++;
	}

	
	$rs->MoveNext();
}
// echo "<pre>";
// print_r($data);
// echo "</pre>";
foreach ($area as $k => $v) {
	
	$area[$k]['part'] = round($v['count']/$total,3)*100;
}


if ($_POST['ok']) {
	include_once 'UndertakerCaseChartsExcel.php';
}


$smarty->assign('total',$total);
$smarty->assign('area',$area);
$smarty->assign('unKnow',$area['未知']['count']);
$smarty->assign('Undertaker',$Undertaker);
$smarty->display('UndertakerCaseCharts.inc.tpl', '', 'charts');
?>