<?php
include_once '../session_check.php' ;
include_once '../class/getAddress.php' ;
include_once '../openadodb.php' ;
include_once '../class/myClass.php' ;

$zips = trim(addslashes($_POST['zipArea'])) ;

$fromYear = (int)trim(addslashes($_POST['fromYear'])) ;
if (!$fromYear) {
	$fromYear = (int)date("Y",mktime(0,0,0,(date("m")-5),1,date("Y"))) ;
}

$fromMonth = (int)trim(addslashes($_POST['fromMonth'])) ;
if (!$fromMonth) {
	$fromMonth = (int)date("m",mktime(0,0,0,(date("m")-5),1,date("Y"))) ;
}

$toYear = (int)trim(addslashes($_POST['toYear'])) ;
if (!$toYear) {
	$toYear = (int)date("Y") ;
}

$toMonth = (int)trim(addslashes($_POST['toMonth'])) ;
if (!$toMonth) {
	$toMonth = (int)date("m") ;
}

if ($fromYear && $fromMonth && $toYear && $toMonth) {
	$totalMonths = ($toYear - $fromYear) * 12 + $toMonth - $fromMonth + 1 ;		//計算期間總月份
	
	$date_array = array() ;
	for ($i = 0 ; $i < $totalMonths ; $i ++) {
		$mm = date("Y.m",mktime(0,0,0,($fromMonth + $i),1,$fromYear)) ;
		$date_array[$i]['date'] = $mm ;
	}
}

if (!$zips) {
	$zips = '106' ;		//預設台北市大安區
}
$zoom = 12 ;

//取得區域地點
$sql = 'SELECT zCity,zArea FROM tZipArea WHERE zZip="'.$zips.'";' ;
$rs = $conn->Execute($sql) ;

$city = $rs->fields['zCity'] ;
$area = $rs->fields['zArea'] ;
##

//取得時間範圍內區域之案件地點
$totalCount = 0 ;
$totalAvg = 0 ;
$count = 0 ;
for ($i = 0 ; $i < count($date_array) ; $i ++) {
	$tmp = array() ;
	$tmp = explode('.',$date_array[$i]['date']) ;
	
	$sql = '
		SELECT
			SUBSTR(cas.cApplyDate,1,10) as cApplyDate,
			cpr.cAddr as cAddr,
			cpr.cMeasureTotal as cMeasureTotal,
			inc.cTotalMoney as cTotalMoney
		FROM
			tContractCase AS cas
		JOIN
			tContractProperty AS cpr ON cas.cCertifiedId=cpr.cCertifiedId
		JOIN
			tContractIncome AS inc ON cas.cCertifiedId=inc.cCertifiedId
		WHERE
			cpr.cZip="'.$zips.'"
			AND cApplyDate >= "'.$tmp[0].'-'.$tmp[1].'-01 00:00:00"
			AND cApplyDate <= "'.$tmp[0].'-'.$tmp[1].'-31 23:59:59"
	' ;
	//echo "SQL=".$sql."<br>\n" ;
	
	$rs = $conn->Execute($sql) ;
	while (!$rs->EOF) {
		$avg = 0 ;
		$str = $rs->fields['cAddr'] ;
		$str = preg_replace("/$city/","",$str) ;
		$str = preg_replace("/$area/","",$str) ;
		//$date_array[$i]['cAddr'] .= $city.$area.$str.':' ;
		$date_array[$i]['cAddr'][] = $city.$area.$str ;
		$date_array[$i]['aDate'][] = $rs->fields['cApplyDate'] ;
		$totalCount ++ ;
		
		$ping = ($rs->fields['cMeasureTotal'] + 1 - 1) * 0.3065 ;
		$tmoney = $rs->fields['cTotalMoney'] + 1 - 1 ;
		//echo 'ping='.$ping.',money='.$tmoney ;
		if ($ping > 0) {
			$avg = $tmoney / $ping + 1 - 1 ;
			$date_array[$i]['avg'][] = round($avg,2) ;
			$date_array[$i]['ping'][] = round($ping,2) ;
			
			$totalAvg += $avg ;
			$count ++ ;
		}
		
		$rs->MoveNext() ;
	}
	unset($tmp) ;
	
	//$date_array[$i]['cAddr'] = preg_replace("/:$/","",$date_array[$i]['cAddr']) ;
}
##

$showOptions = new MyClass() ;

if ($count > 0) {
	$totalAvg = round(($totalAvg / $count / 10000),2) ;
}
else {
	$totalAvg = 0 ;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>第一建經區域成交件數統計</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">

/* 呼叫 Google Map */
var geocoder;
var map ;
var bermudaTriangle ;

function initialize() {
	var mapOptions = {
		zoom: 12,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	} ;
	geocoder = new google.maps.Geocoder() ;
	map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions) ;
	
	centerCodeAddress() ;
	codeAddress() ;
}
////

google.maps.event.addDomListener(window, 'load', initialize);

/* 取得區域中心點*/
function centerCodeAddress() {
	geocoder.geocode({ 'address': '<?=$city.$area?>' }, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
		}
	});
}
////

/* 取得地址經緯度 */
function codeAddress() {
	if (geocoder) {
<?php
	for ($i = 0 ; $i < count($date_array) ; $i ++) {
		$arr = array() ;
		//$arr = explode(':',$date_array[$i]['cAddr']) ;
		for ($j = 0 ; $j < count($date_array[$i]['cAddr']) ; $j ++) {
			
?>
		var address<?=$i.$j?> = '<?=$date_array[$i]['cAddr'][$j]?>' ;
		geocoder.geocode({ 'address': address<?=$i.$j?> }, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var view<?=$i.$j?> = new google.maps.InfoWindow({
					content: address<?=$i.$j?>
				}) ;
				
				var marker<?=$i.$j?> = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
				
				google.maps.event.addListener(marker<?=$i.$j?>,"mouseover",function() {
					view<?=$i.$j?>.open(map,marker<?=$i.$j?>) ;
				}) ;
				
				google.maps.event.addListener(marker<?=$i.$j?>,"mouseout",function() {
					view<?=$i.$j?>.close() ;
				}) ;
			}
		});
<?php
	}
}
?>
	}
}
////

/* 取得鄉鎮市區 */
function getArea() {
	var url = 'listArea.php' ;
	var cities = $('[name="city"] :selected').val() ;
	var zArea = '<?=$area?>' ;
	
	$.post(url,{city:cities},function(txt) {
		$('#areaR').html('') ;
		txt = '<select name="area" onchange="getZip()">' + txt + '</select>' ;
		$('#areaR').html(txt) ;
	}) ;
}
//

/* 取得郵遞區號 */
function getZip() {
	var zz = $('[name="area"] :selected').val() ;
	
	if (zz == "0") {
		alert('請選擇鄉鎮市區!!') ;
		return false ;
	}
	else {
		$('[name="zipArea"]').val(zz) ;
		$('[name="myform"]').submit() ;
	}
}
//

/* 顯示/關閉明細列表 */
function showlist() {
	$('#list').toggle("normal") ;
	
	if ($('#msg').html() == '查看') {
		$('#msg').html('關閉') ;
	}
	else {
		$('#msg').html('查看') ;
	}
}
//

window.resizeTo(700,850) ;
</script>
<style>
#map-canvas {
	margin:0px auto;
	height: 500px;
	width: 500px;
	margin: 0px;
	padding: 0px
}
ul {
	list-style-type: none;
	text-align: left;
	margin: 0px;
	padding-top: 20px;
	padding-bottom:	20px;
}
#list td {
	border: 1px solid #CCC;
	margin: 0px;
	padding: 2px;
	text-align: center;
}

</style>
</head>
<body>
<center>
<form name="myform" method="POST">
<div id="areaChoose">
	縣市：
	<select name="city" onchange="getArea()">
		<?=listCity($conn,$zips)?>
	</select>　
	鄉鎮市區：
	<span id="areaR">
	<select name="area" onchange="getZip()">
		<?=listArea($conn,$zips)?>
	</select>
	</span>
	<input type="hidden" name="zipArea">　
<div>
<div style="margin-top:10px;">
	日期範圍：
	<select name="fromYear">
<?php
echo $showOptions->FromToYear($fromYear,2012) ;
?>
	</select>
	年度
	<select name="fromMonth">
<?php
echo $showOptions->FromToMonth($fromMonth) ;
?>
	</select>
	月份
	~
	<select name="toYear">
<?php
echo $showOptions->FromToYear($toYear,2012) ;
?>
	</select>
	年度
	<select name="toMonth">
<?php
echo $showOptions->FromToMonth($toMonth) ;
?>
	</select>
	月份
	<input type="button" style="margin-left:20px;" value="查詢" onclick="getZip()">
</div>
<div style="height:20px;border-top-style:dashed;border-top-color:#CCC;border-top-width:1px;margin-top:20px;">　</div>

<div id="summary" style="margin:20px 0px 20px 0px;border:1px solid #CCC;padding-bottom:20px;width:490px;">
	<ul>
		<li>日期範圍件數：<span style="font-size:14pt;font-weight:bold;color:green;"><?=number_format($totalCount)?> 件</span></li>
		<li>
			<div style="float:left;">區域平均單價：</div><div style="float:left;font-size:14pt;font-weight:bold;color:blue;"><?=$totalAvg?>萬元</div>
			<div id="showoff" style="margin-right:20px;float:right;font-size:9pt;text-align:right;"><a href="#" onclick="showlist()"><span id="msg">查看</span>更多...</a></div>
		</li>
	</ul>
</div>

<div id="list" style="display:none;">
	<table cellspacing="0" style="width:500px;">
		<tr>
			<td style="width:90px;">日期</td>
			<td style="width:250px;">地址</td>
			<td style="width:80px;">單價</td>
			<td style="width:80px;">坪數</td>
		</tr>
<?php
//統計列印

for ($i = 0 ; $i < count($date_array) ; $i ++) {
	for ($j = 0 ; $j < count($date_array[$i]['cAddr']) ; $j ++) {
		echo "\t\t<tr>\n" ;
		echo "\t\t\t".'<td>'.$date_array[$i]['aDate'][$j].'</td><td style="text-align:left;">'.$date_array[$i]['cAddr'][$j].'</td>' ;
		echo '<td style="text-align:right;">'.round(($date_array[$i]['avg'][$j]/10000),2).'萬元</td><td style="text-align:right;">'.round($date_array[$i]['ping'][$j],2)."坪</td>\n" ;
		echo "\t\t</tr>\n" ;
	}
}
?>
	</table>
	<div style="height:20px;">&nbsp;</div>
</div>
<div id="map-canvas" style="border:1px solid #000;"></div>
</form>
</center>
</body>
</html>
