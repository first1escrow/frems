<?php
include_once '../session_check.php' ;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />

<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
</head>
<body>
<input type="text" name="t1" style="width:300px" id="t1" />
<input type="button" value="送出" onclick="send_msg()">
<script type="text/javascript">
$(function() {
	$('#t1').autocomplete('data_scrivener.php') ;
}) ;

function send_msg() {	
	parent.$('[name="scrivener"]').val($('#t1').val()) ;
	parent.$('.ajax').colorbox.close() ;
}
</script>
</body>
</html>