<?php
include_once '../session_check.php' ;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript">

</script>
</head>
<body>
<input type="text" name="t1" style="width:200px" id="t1" />
<input type="button" value="送出" onclick="send_msg()">
<script type="text/javascript">
$(function() {
	$('#t1').autocomplete('branch_data.php') ;
	$('[name="t1"]').focus() ;
	$('[name="t1"]').select() ;
}) ;

function send_msg() {	
	var str = $('#t1').val() ;
	var tmp = str.split('(') ;
	str = tmp[1] ;
	var tmp = str.split(')') ;
	str = tmp[0].substr(2) ;
	str = parseInt(str) ;
	
	parent.$('[name="realestate_branch"]').children().each(function() {
		var val = $(this).val() ;
		
		if (val == str) {
			this.selected = true ;
		}
	}) ;
	
	parent.$('.ajax').colorbox.close() ;
	
}
</script>
</body>
</html>