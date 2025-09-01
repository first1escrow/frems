<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../includes/first1Sales.php';
include_once '../includes/sales/getSalesArea.php';
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$city = $_POST['city'];

$menu = '';
if ($city == 'city') {
	$col = "zCity as area";
	$str = '1=1 GROUP BY zCity';
	$count = 6;
	$i = 1;

}else{
	$menu = "<input type=\"button\" value=\"重新選擇縣市\" class=\"btnC\" onclick=\"getMenuArea('city')\">&nbsp;&nbsp;";
	$menu .= "<input type=\"button\" value=\"確定\" class=\"btnC\" onclick=\"setZip()\">&nbsp;&nbsp;<br>";
	$menu .= "<span class=\"btnC\"><input type=\"checkbox\" name=\"all\" value=\"".$city."\" onclick=\"clickAll()\" >全部</span>";
	$col = "zArea as area";
	$str = "zCity ='".$city."'";
	$class = "";
	$count = 4;
	$i = 2;
}

$sql = "SELECT zZip as zip ,".$col." FROM tZipArea WHERE ".$str." ORDER BY nid,zZip ASC";

$rs = $conn->Execute($sql);
$total = $rs->RecordCount();
while (!$rs->EOF) {
	// $option .= "<input type=\"checkboxes\" value=\"".$rs->fields['zArea']."\">";
	$tmp[$rs->fields['zip']] = $rs->fields['area'];
	$rs->MoveNext();
}




foreach ($tmp as $k => $v) {
	

	if ($city == 'city') {
		$menu .= "<input type=\"button\" value=\"".$v."\" class=\"btnC\" onclick=\"getMenuArea('".$v."')\">";
	}else{
		$menu .= "<span class=\"btnC\"><input type=\"checkbox\" class=\"zip\" onclick=\"checkClick()\" id=\"".$city.$v."\" value=\"".$k."\" >".$v."</span>";
	}
	

	if ($i != 1 && $i % $count ==0) {
		$menu .= "<br>";
	}

	$i++;
}

$menu .= "<input type=\"hidden\" name=\"areaCount\" value=\"".$total."\">";
$menu .= "<br><input type='button' value='關閉' onclick='closeCityMenu()' class=\"btnC\">";
echo $menu; 
?>