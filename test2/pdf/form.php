<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
require_once('../../tcpdf/tcpdf.php');

$_POST = escapeStr($_POST) ;
$cId = '005079426' ;
$cat = 'BuildContract';
$today = array('year'=>(date('Y')-1911),'month'=>date('m'),'day'=>date('d'));

##

switch ($cat) {
	case 'taxMoney': //土地增值稅
		$land = getLand($cId);
		$owner = getOwner($cId);
		$buyer = getBuyer($cId);
		$scrivener = getScrivener($cId);
		include_once 'AddedTaxMoney.php';
		break;
	case 'deedTax': //契稅申報書
		$land = getLand($cId);
		$property = getProperty($cId);
		$owner = getOwner($cId);
		$buyer = getBuyer($cId);
		$scrivener = getScrivener($cId);
		include_once 'DeedTax.php';
		break;
	case 'landApply':
		include_once 'LandApply.php';
		break;
	case 'BuildContract':
		$property = getProperty($cId);
		$income = getIncome($cId);
		$owner = getOwner($cId);
		$buyer = getBuyer($cId);
		include_once 'BuildContract.php';
		break;
	default:
		# code...
		break;
}

##建物##
function getProperty($cId){
	global $conn;

	$sql = "SELECT
				*,
				(SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip = cZip) AS Area,
				(SELECT zArea FROM tZipArea WHERE zZip = cZip) AS Area2,
				(SELECT bTypeName FROM tBuildingMaterials WHERE bTypeId = cBudMaterial) AS BudMaterial
			FROM
				tContractProperty
			WHERE
				cCertifiedId = '".$cId."'";
	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$arr[$i] = $rs->fields;
		// $tmp = explode('-', $arr[$i]['cBuildDate']);
		// $arr[$i]['cBuildDate']
		$arr[$i]['cBuildDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$arr[$i]['cBuildDate'])) ;
		$tmp = explode('-',$arr[$i]['cBuildDate']) ;
			
		if (preg_match("/0000/",$tmp[0])) {	$tmp[0] = '000' ; }
		else { $tmp[0] -= 1911 ; }
			
		$arr[$i]['cBuildDateYear'] = $tmp[0];
		$arr[$i]['cBuildDateMonth'] = $tmp[1];
		$arr[$i]['cBuildDateDay'] = $tmp[2];
		unset($tmp) ;
		##
		preg_match("/(\D(.*)[路|街]{1})?(.*)?/isu",$arr[$i]['cAddr'],$tmp) ;
		$arr[$i]['AddrRoad'] = $tmp[1];
		$txt = $tmp[3];
		unset($tmp);
		preg_match("/((.*)[段|巷|弄]{1})?(.*)?/isu", $txt,$tmp);
		$arr[$i]['AddrSec'] = $tmp[1];
		$arr[$i]['no'] = $tmp[3];
		unset($tmp);
		
		$i++;
		$rs->MoveNext();
	}
	
	return $arr;
}
##附屬建物##
function getPropertyObject($cId,$item='',$cat=''){
	global $conn;

	$str = '';

	if ($item) {
		$str .= "AND cBuildItem = '".$item."'";
	}

	if ($cat) {
		$str .= "AND cCategory = '".$cat."'";
	}
	
	$sql = "SELECT *,(SELECT cName FROM tCategoryBuild WHERE cId=cCategory) AS Category FROM tContractPropertyObject WHERE cCertifiedId = '".$cId."' ".$str." ORDER BY cBuildItem ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		if ($rs->fields['cLevelUse'] != '' && $rs->fields['cMeasureMain'] != 0 && $rs->fields['cMeasureTotal'] != 0 && $rs->fields['cPower1'] != 0 && $rs->fields['cPower2'] != 0) {
			$arr[] = $rs->fields;

		}
		

		$rs->MoveNext();
	}

	return $arr;
}

##土地##
function getLand($cId){
	global $conn;


	$sql = "SELECT
				cl.*,
				(SELECT cName FROM tCategoryLand AS c WHERE c.cId =cl.cLand4) AS land4,
				(SELECT zArea as area FROM tZipArea WHERE zZip = cl.cZip) as area
			FROM
				tContractLand AS cl
			WHERE
				cl.cCertifiedId = '".$cId."' ORDER BY cl.cId DESC";

	$rs = $conn->Execute($sql);
	$i = 0;
	while (!$rs->EOF) {
		$tmp = explode('-', $arr[$i]['cMoveDate']);
		$arr[$i]['cMoveDateYear'] = ($tmp[0] =='0000')? '000' : ($tmp[0]-1911);
		$arr[$i]['cMoveDateMonth'] = $tmp[1];
		$arr[$i] = $rs->fields;
		$i++;
		$rs->MoveNext();
	}

	return $arr;
}
##賣方##
function getOwner($cId){
	global $conn;

	##賣方##
	$sql = "SELECT *,(SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip = cRegistZip) AS RegistArea,(SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip = cBaseZip) AS BaseArea FROM tContractOwner WHERE cCertifiedId = '".$cId."'";
	$rs = $conn->Execute($sql);
	$arr = $rs->fields;
	$tmp = explode('-', $arr['cBirthdayDay']);
	$arr['cBirthdayDayYear'] = ($tmp[0] =='0000')? '000' : ($tmp[0]-1911);
	$arr['cBirthdayDayMonth'] = $tmp[1];
	$arr['cBirthdayDayDay'] = $tmp[2];
	unset($tmp);

	return $arr;
}
##買方##
function getBuyer($cId){
	global $conn;

	##買方##
	$sql = "SELECT *,(SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip = cRegistZip) AS RegistArea,(SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip = cBaseZip) AS BaseArea FROM tContractBuyer WHERE cCertifiedId = '".$cId."'";
	$rs = $conn->Execute($sql);
	$arr = $rs->fields;
	$tmp = explode('-', $arr['cBirthdayDay']);
	$arr['cBirthdayDayYear'] = ($tmp[0] =='0000')? '000' : ($tmp[0]-1911);
	$arr['cBirthdayDayMonth'] = $tmp[1];
	$arr['cBirthdayDayDay'] = $tmp[2];
	unset($tmp);

	return $arr;
}
##地政士##
function getScrivener($cId){
	global $conn;

	$sql = "SELECT
				s.*,
				(SELECT CONCAT(zCity,zArea) FROM tZipArea WHERE zZip = sCpZip1) AS Area
			FROM
				tContractScrivener AS cs
			LEFT JOIN
				tScrivener AS s ON cs.cScrivener = s.sId
			WHERE
				
				cs.cCertifiedId = '".$cId."'";

	$rs = $conn->Execute($sql);
	$arr = $rs->fields;

	return $arr;
}
##價金部分
function getIncome($cId)
{
	global $conn;
	$sql = "SELECT * FROM tContractIncome WHERE cCertifiedId = '".$cId."'";
	$rs = $conn->Execute($sql);

	$arr = $rs->fields;

	return $arr;
}
##數字轉中字
//
function NumtoStr($num){
	$numc	="零,壹,貳,參,肆,伍,陸,柒,捌,玖";
	$unic	=",拾,佰,仟";
	$unic1	=" 元整,萬,億,兆,京";
	
	//$numc_arr	=split("," , $numc);
	$numc_arr	= explode("," , $numc);
	//$unic_arr	=split("," , $unic);
	$unic_arr	= explode("," , $unic);
	//$unic1_arr	=split("," , $unic1);
	$unic1_arr	= explode("," , $unic1);
	
	$i = str_replace(',','',$num);#取代逗號
	$c0 = 0;
	$str=array();
	do{
		$aa = 0;
		$c1 = 0;
		$s = "";
		#取最右邊四位數跑迴圈,不足四位就全取
		$lan=(strlen($i)>=4)?4:strlen($i);
		$j = substr($i, -$lan);
		while($j>0){
			$k = $j % 10;#取餘數
			if($k > 0){
				$aa = 1;
				$s = $numc_arr[$k] . $unic_arr[$c1] . $s ;
			}elseif ($k == 0){
				if($aa == 1)	$s = "0" . $s;
			}
			$j = intval($j / 10);#只取整數(商)
			$c1 += 1;
		}
		#轉成中文後丟入陣列,全部為零不加單位
		$str[$c0]=($s=='')?'':$s.$unic1_arr[$c0];
		#計算剩餘字串長度
		$count_len=strlen($i) - 4;
		$i=($count_len > 0 )?substr($i, 0, $count_len):'';

		$c0 += 1;
	}while($i!='');
	
	#組合陣列
	foreach($str as $v)	$string .= array_pop($str);

	#取代重複0->零
	$string=preg_replace('/0+/','零',$string);

	return $string;
}
?>