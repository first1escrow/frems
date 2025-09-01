<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$id = (!empty($_POST['id'])) ? $_POST['id'] : $_GET['id'];
$cat = (!empty($_POST['cat'])) ? $_POST['cat'] : $_GET['cat']; //1add 2modify 3delete

$msg = '';


##
if (!empty($_POST)) {
	// print_r($_POST);

	
	$city = ($_POST['managercity'])?implode(',', $_POST['managercity']):'';
	
	sort($_POST['managerzip']);
	$zip = ($_POST['managerzip'])?implode(',', $_POST['managerzip']):'';
	
	
	if ($cat == '1') {
		$sql = "INSERT INTO
					tCategoryTaxGoverment
				SET
					cName = '".$_POST['name']."',
					cManagerCity = '".$city."',
					cManagerArea = '".$zip."',
					cZip = '".$_POST['zip']."',
					cAddress = '".$_POST['addr']."',
					cTel ='".$_POST['phone']."'";
		
		$msgCode = 1;
		if ($conn->Execute($sql)) {
			
			$msg = '新增成功';
		}else{
			$msg = '新增失敗';
		}

		$id = $conn->Insert_ID(); 

	}else if ($cat == '2') {
		$sql = "UPDATE
					tCategoryTaxGoverment
				SET
					cName = '".$_POST['name']."',
					cManagerCity = '".$city."',
					cManagerArea = '".$zip."',
					cZip = '".$_POST['zip']."',
					cAddress = '".$_POST['addr']."',
					cTel ='".$_POST['phone']."'
				WHERE
					cId = '".$_POST['id']."'";
		
		$msgCode = 2;
		if ($conn->Execute($sql)) {
			
			$msg = '更新成功';
		}else{
			$msg = '更新失敗';
		}
	}elseif ($cat == 3) {
		$sql = "DELETE FROM tCategoryTaxGoverment WHERE cId = '".$id."'" ;
		// echo $sql;
		$msgCode = 3;
		if ($conn->Execute($sql)) {
			$msg = '刪除成功';
		}else{
			$msg = '刪除失敗';
		}

		
		
	}
	
}
##
$sql = "SELECT *,(SELECT zCity FROM tZipArea WHERE zZip=cZip) AS city FROM tCategoryTaxGoverment WHERE cId = '".$id."'";
$rs = $conn->Execute($sql);
$data = $rs->fields;

##
$menuCity = '<option value="">請選擇</option>';
$sql = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$selected = "";
	if ($data['city'] == $rs->fields['zCity']) {
		$selected = "selected=selected";
	}

	$menuCity .= "<option value='".$rs->fields['zCity']."' ".$selected.">".$rs->fields['zCity']."</option>";

	$rs->MoveNext();
}

##
$menuArea = '<option value="">請選擇</option>';
$sql = "SELECT zZip,zArea FROM tZipArea WHERE zCity='".$data['city']."' ";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$selected = "";
	if ($data['cZip'] == $rs->fields['zZip']) {
		$selected = "selected=selected";
	}
	$menuArea .= "<option value='".$rs->fields['zZip']."' ".$selected.">".$rs->fields['zArea']."</option>";

	$rs->MoveNext();
}
##
//管轄區域
$data['managerArea'] = '';
if (!empty($data['cManagerCity'])) {
	$exp = explode(',', $data['cManagerCity']);
	foreach ($exp as $value) {
		// $city .= "'".$value."',";
		$data['managerArea'] .= "<span class=\"btnC showZip\" id=\"".$value."\" name=\"".$value."\"><span onClick=\"delZip('".$value."')\" class=\"del\">X</span>".$value."<input type=\"hidden\" name=\"managercity[]\" value=\"".$value."\"></span>";
	}
	unset($exp);
}
if (!empty($data['cManagerArea'])) {
	$sql = "SELECT * FROM tZipArea WHERE zZip IN (".$data['cManagerArea'].")";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$id = $rs->fields['zCity'].$rs->fields['zArea'];
		$data['managerArea'] .= "<span class=\"btnC showZip\" id=\"".$id."\" name=\"".$rs->fields['zZip']."\"><span onClick=\"delZip('".$id."')\" class=\"del\">X</span>".$id."<input type=\"hidden\" name=\"managerzip[]\" value=\"".$rs->fields['zZip']."\"></span>";
		// <span class=\"btnC showZip\" id=\""+$(this).attr("id")+"\" name=\""+$(this).val()+"\"><span onClick=\"delZip('"+$(this).attr("id")+"')\" class=\"del\">X</span>"+$(this).attr("id")+"<input type=\"hidden\" name=\"managerzip[]\" value=\""+$(this).val()+"\"></span>

		$rs->MoveNext();
	}
}

###
$smarty->assign('menuCity',$menuCity);
$smarty->assign('menuArea',$menuArea);
$smarty->assign('data',$data);
$smarty->assign('cat',$cat);
$smarty->assign('msgCode',$msgCode);
$smarty->assign('msg',$msg);
$smarty->display('TaxGovermentEdit.inc.tpl', '', 'line');
?>
