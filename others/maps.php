<?php
include_once '../openadodb.php' ;

$addr = $_REQUEST['addr'] ;
$zips = trim(addslashes($_REQUEST['zips'])) ;
$zoom = $_REQUEST['zoom'] ;

if (!$zoom) {
	$zoom = 14 ;
}

if ($zips) {
	$sql = 'SELECT * FROM tZipArea WHERE zZip="'.$zips.'";' ;
	$rs = $conn->Execute($sql) ;
	$_city = $rs->fields['zCity'] ;
	$_area = $rs->fields['zArea'] ;
	
	$addr = preg_replace("/$_city/","",$addr) ;
	$addr = preg_replace("/$_area/","",$addr) ;
	$addr = $_city.$_area.$addr ;
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>地圖顯示</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
$(document).ready(function() {

}) ;

/* 呼叫 Google Map */
var geocoder;
var map ;

function initialize() {
	var mapOptions = {
		zoom: <?=$zoom?>,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	} ;
	geocoder = new google.maps.Geocoder() ;
	map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions) ;
	
	codeAddress() ;
}
//
google.maps.event.addDomListener(window, 'load', initialize);

/* 取得地址經緯度 */
function codeAddress() {
	var address = '<?=$addr?>' ;
	if (geocoder) {
		geocoder.geocode({ 'address': address }, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				
				var view = new google.maps.InfoWindow({
					content: address
				}) ;
				
				var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
				view.open(map,marker) ;
				
				google.maps.event.addListener(marker,"mouseover",function() {
					view.open(map,marker) ;
				}) ;
				
				google.maps.event.addListener(marker,"mouseout",function() {
					view.close() ;
				}) ;
			} 
			else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
	}
}
</script>
<style>
#map-canvas {
	height: 420px;
	width: 750px;
	margin: 0px;
	padding: 0px
}
</style>
</head>
<body>

<form name="myform" method="POST">
<div id="map-canvas" style="margin:0px auto;"></div>
</body>
</html>
