<?php
include_once '../session_check.php' ;

$upload_file = $_REQUEST['upload_file'] ;
?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript">
$(function() {
	$('name=["upload"]').click(function() {
		$('name=["myform"]').submit() ;
	}) ;
	$('#cancel').click(function() {
		window.close() ;
	}) ;
	$('#upload').button({
		icons:{
			primary: "ui-icon-folder-open"
		}
	}) ;
	$('#cancel').button({
		icons:{
			primary: "ui-icon-cancel"
		}
	}) ;
}) ;
</script>
<style>
.small_font {
	font-size: 9pt;
	line-height:1;
}
input.bt4 {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background: #F8EDEB;border:1px #727272 dotted;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 dotted;font-size:12px;margin-left:2px;cursor:pointer
}
.btn {
	padding:10px 20px 10px 20px ;
	color:#212121 ;
	background-color:#F8ECE9 ;
	margin:2px ;
	border:1px outset #F8ECE0 ;
	cursor:pointer ;
}
.btn:hover {
	padding:10px 20px 10px 20px ;
	color:#212121 ;
	background-color:#EBD1C8 ;
	margin:2px;
	border:1px outset #F8ECE0;
	cursor:pointer;
}
</style>
</head>
<body bgcolor="#F0F0F0">
<form name="myform" method="POST" enctype="multipart/form-data" action="upload_feedback_ok.php">
<input type="hidden" name="save_ok" value="1">
<table style="margin:5px;width:650px;border:1px solid #ccc;padding:10px;">
<tr>
	<td colspan="3" style="background-color:#E4BEB1;text-align:center;">
	<h4>
	匯入個人季回饋金額
	</h4>
	</td>
</tr>
<tr>
	<td style="height:30px;">
	*選擇年度
	<select name="FBYear" style="width:100px;">
<?php
$y = date("Y") ;
$yb = 2012 ;
$ye = $y + 10 ;

for ($i = $yb ; $i < $ye ; $i ++) {
	echo "\t<option value='".$i."'" ;
	if ($i == $y) { echo " selected='selected'" ; }
	echo ">".($i - 1911)."</option>\n" ;
}
?>
	</select>
	年
	</td>
	<td>
	*選擇季
	<select name="FBSeason" style="width:100px;">
	<option value="1" seletec="selected">第 1 季</option>
	<option value="2">第 2 季</option>
	<option value="3">第 3 季</option>
	<option value="4">第 4 季</option>
	</select>
	</td>
	<td>
	&nbsp;
	</td>
</tr>
<tr>
	<td colspan="3" style="height:30px;">
		<input type="hidden" name="max_file_size" value="10240000">
		<input style="width:400px;background-color:#F0F1FF;" type="file" name="upload_file">　　
		格式：excel
	</td>
</tr>
<tr>
	<td colspan="3" valign="bottom" style="height:30px;text-align:center;">
		<button id="upload">確定</button>
		<button id="cancel">取消</button>
	</td>
</tr>
</table>
</form>
</body>
</html>
