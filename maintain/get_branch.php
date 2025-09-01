<?php
require_once dirname(__DIR__) . '/session_check.php';
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script type="text/javascript">

</script>
</head>
<body>
<input type="text" name="t1" style="width:300px" id="t1" />
<input type="button" value="送出" onclick="send_msg()">
<script type="text/javascript">
$(function() {
	$('#t1').autocomplete('data_branch.php') ;
}) ;

function send_msg() {
	var str = parent.$('[name="bClassBranch"]').val() ;
	var new_branch = $('#t1').val() ;

	var tmp = new_branch.split(')') ;
	var temp = tmp[0].split('(') ;
	new_branch = temp[1] ;

	if (str) { str = str + ';' ; }
	str = str + new_branch ;
	parent.$('[name="bClassBranch"]').val(str) ;
	parent.$('.ajax').colorbox.close() ;
}
</script>
</body>
</html>
