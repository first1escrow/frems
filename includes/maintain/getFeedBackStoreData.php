<?php
include_once '../../configs/config.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;


$_POST = escapeStr($_POST) ;

$storeId = strtoupper($_POST['id']);

$code = substr($storeId, 0,2);
$id = substr($storeId, 2);



if ($code == 'SC') { //1地政2仲介
	$cat = 1;
}else{
	$cat = 2;
}

$sql = "SELECT
			*,
			(SELECT zCity FROM tZipArea WHERE zZip=fZipC) AS cityC,
			(SELECT zArea FROM tZipArea WHERE zZip=fZipC) AS areaC,
			(SELECT zCity FROM tZipArea WHERE zZip=fZipR) AS cityR,
			(SELECT zArea FROM tZipArea WHERE zZip=fZipR) AS areaR,
			(SELECT bBank4_name FROM tBank WHERE bBank3=fAccountNum AND bBank4='') AS bank,
			(SELECT bBank4_name FROM tBank WHERE bBank3=fAccountNum AND bBank4=fAccountNumB) AS bankBranch
		FROM
			tFeedBackData
		WHERE
			fStatus = 0 AND fType = '".$cat."' AND fStoreId ='".$id."'";

$rs = $conn->Execute($sql);
$total=$rs->RecordCount(); 
$tmp = array();

while (!$rs->EOF) {
	// $data['data'][] = $rs->fields;
	$fFeedBack = ($rs->fields['fFeedBack'] == 3)?'結案':($rs->fields['fFeedBack'] == 2)?'整批':'';
	
	if ($rs->fields['fIdentity'] == 2) {
		$fIdentity = '身份證編號';
	}elseif ($rs->fields['fIdentity'] == 3) {
		$fIdentity = '統一編號';
	}elseif ($rs->fields['fIdentity'] == 4) {
		$fIdentity = '護照號碼';
	}else{
		$fIdentity = '';
	}



	$rs->fields['fAddrC'] = str_replace($rs->fields['cityC'].$rs->fields['areaC'], '', $rs->fields['fAddrC']);
	$rs->fields['fAddrR'] = str_replace($rs->fields['cityR'].$rs->fields['areaR'], '', $rs->fields['fAddrR']);

	

	$html .= '<table cellpadding="0" cellspacing="0" class="tb">';
	$html .= '<tr>';
	$html .= '<td><input type="checkbox" name="id"  value="'.$rs->fields['fId'].'" checked=checked></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<th>回饋方式︰</th>';
	$html .= '<td>'.$fFeedBack.'<input type="hidden" name="fFeedBack_'.$rs->fields['fId'].'" value="'.$rs->fields['fFeedBack'].'"></td>';
	
	$html .= '<th>姓名/抬頭︰</th>';
	$html .= '<td>'.$rs->fields['fTitle'].'<input type="hidden" name="fTitle_'.$rs->fields['fId'].'" value="'.$rs->fields['fTitle'].'"></td>';
	
	$html .= '<th>店長行動電話︰</th>';
	$html .= '<td>'.$rs->fields['fMobileNum'].'<input type="hidden" name="fMobileNum_'.$rs->fields['fId'].'" value="'.$rs->fields['fMobileNum'].'"></td>';
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<th>身份別︰</th>';
	$html .= '<td>'.$fIdentity.'<input type="hidden" name="fIdentity_'.$rs->fields['fId'].'" value="'.$rs->fields['fIdentity'].'"></td>';
	
	$html .= '<th>證件號碼︰</th>';
	$html .= '<td>'.$rs->fields['fIdentityNumber'].'<input type="hidden" name="fIdentityNumber_'.$rs->fields['fId'].'" value="'.$rs->fields['fIdentityNumber'].'"></td>';
	
	$html .= '<th>收件人稱謂︰</th>';
	$html .= '<td>'.$rs->fields['fRtitle'].'<input type="hidden" name="fRtitle_'.$rs->fields['fId'].'" value="'.$rs->fields['fRtitle'].'"></td>';
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<th>聯絡地址︰</th>';
	$html .= '<td colspan="5">'.$rs->fields['fZipC'].$rs->fields['cityC'].$rs->fields['areaC'].$rs->fields['fAddrC'];
	$html .= '<input type="hidden" name="fZipC_'.$rs->fields['fId'].'" value="'.$rs->fields['fZipC'].'">';
	$html .= '<input type="hidden" name="cityC_'.$rs->fields['fId'].'" value="'.$rs->fields['cityC'].'">';
	$html .= '<input type="hidden" name="areaC_'.$rs->fields['fId'].'" value="'.$rs->fields['areaC'].'">';
	$html .= '<input type="hidden" name="fAddrC_'.$rs->fields['fId'].'" value="'.$rs->fields['fAddrC'].'">';
	$html .= '</td>';

	$html .= '<tr>';
	$html .= '<th>戶藉地址︰</th>';
	$html .= '<td colspan="5">'.$rs->fields['fZipR'].$rs->fields['cityR'].$rs->fields['areaR'].$rs->fields['fAddrR'];
	$html .= '<input type="hidden" name="fZipR_'.$rs->fields['fId'].'" value="'.$rs->fields['fZipR'].'">';
	$html .= '<input type="hidden" name="cityR_'.$rs->fields['fId'].'" value="'.$rs->fields['cityR'].'">';
	$html .= '<input type="hidden" name="areaR_'.$rs->fields['fId'].'" value="'.$rs->fields['areaR'].'">';
	$html .= '<input type="hidden" name="fAddrR_'.$rs->fields['fId'].'" value="'.$rs->fields['fAddrR'].'">';
	$html .= '</td>';
	$html .= '</tr>';

	$html .= '<tr>';
	$html .= '<th>電子郵件︰</th>';
	$html .= '<td  colspan="5">'.$rs->fields['fEmail'];
	$html .= '<input type="hidden" name="fEmail_'.$rs->fields['fId'].'" value="'.$rs->fields['fEmail'].'">';
	$html .= '</td>';
	$html .= '</tr>';


	$html .= '<tr>';
	$html .= '<th>總行︰</th>'; ///fAccountNum
	$html .= '<td>'.$rs->fields['bank'].'<input type="hidden" name="fAccountNum_'.$rs->fields['fId'].'" value="'.$rs->fields['fAccountNum'].'"></td>';
	
	$html .= '<th>分行︰</th>';
	$html .= '<td>'.$rs->fields['bankBranch'].'<input type="hidden" name="fAccountNumB_'.$rs->fields['fId'].'" value="'.$rs->fields['fAccountNumB'].'"></td>';
	
	$html .= '<th>指定帳號︰</th>';
	$html .= '<td>'.$rs->fields['fAccount'].'<input type="hidden" name="fAccount_'.$rs->fields['fId'].'" value="'.$rs->fields['fAccount'].'"></td>';
	$html .= '</tr>';



	$html .= '<tr>';
	$html .= '<th>戶名︰</th>'; ///fAccountNum
	$html .= '<td>'.$rs->fields['fAccountName'].'<input type="hidden" name="fAccountName_'.$rs->fields['fId'].'" value="'.$rs->fields['fAccountName'].'"></td>';
	
	$html .= '<th>發票種類︰</th>';
	$html .= '<td>'.$rs->fields['fNote'].'<input type="hidden" name="fNote_'.$rs->fields['fId'].'" value="'.$rs->fields['fNote'].'"></td>';
	
	$html .= '<th></th>';
	$html .= '<td></td>';
	$html .= '</tr>';
	// $html .= '<th>證件號碼︰</th>';
	// $html .= '<td>'.$rs->fields['fIdentityNumber'].'<input type="hidden" name="newTtitle'.$rs->fields['fId'].'" value="'.$rs->fields['fIdentityNumber'].'"></td>';
	
	// $html .= '<th>收件人稱謂︰</th>';
	// $html .= '<td>'.$rs->fields['fRtitle'].'<input type="hidden" name="newFeedBack_'.$rs->fields['fId'].'" value="'.$rs->fields['fRtitle'].'"></td>';
	// $html .= '</tr>';

	

	$html .= '</table><Br>';
	$rs->MoveNext();
}



echo $html;
?>

