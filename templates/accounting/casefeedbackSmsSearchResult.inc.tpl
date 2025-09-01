<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
}) ;

function download(){
	
	$('[name="excel"]').submit();
}
</script>
<style>
.xxx-button {
color:#FFFFFF;
	font-size:14px;
	font-weight:normal;
	
	text-align: center;
	white-space:nowrap;
	height:40px;
	
	background-color: #a63c38;
    border: 1px solid #a63c38;
    border-radius: 0.35em;
    font-weight: bold;
    padding: 0 20px;
    margin: 5px auto 5px auto;
}
.xxx-button:hover {
	background-color:#333333;
	border:1px solid #333333;
}
</style>
</head>
<body>

<center>
<div>


<{if $cat == 1}>


<form action="<{$link2}>" target="_blank" name="pdf">
<input type="submit" value="下載檔案"  class="xxx-button">

<input type="button" value="下載檔案EXCEL" onclick="download()" class="xxx-button">

<input type="button" value="返回" onclick="javascript:window.location.reload()" class="xxx-button">

</form>

<form action="casefeedbackPDF_result.php" name="ex" method="POST">

	

	<input type="hidden" value="<{$bank}>" name="bank">
	<input type="hidden" value="<{$bStoreClass}>" name="bStoreClass">
	<input type="hidden" value="<{$sales_year}>" name="sales_year">
	<input type="hidden" value="<{$sales_season}>" name="sales_season">
	<input type="hidden" value="<{$certifiedid}>" name="certifiedid">
	<input type="hidden" value="<{$bCategory}>" name="bCategory">
	<input type="hidden" value="<{$branch}>" name="branch">
	<input type="hidden" value="<{$scrivener}>" name="scrivener">
	<input type="hidden" value="<{$storeSearch}>" name="bck">
	<input type="hidden" value="excel" name="filetype">


</form>

<form action="<{$link3}>" name="excel" target="_blank">

</form>
<{else}>
<div>無回饋資料</div>
<{/if}>

</center>
</form>
</body>
</html>