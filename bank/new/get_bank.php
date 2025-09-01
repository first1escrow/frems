<?php
include_once '../../session_check.php' ;

$aaa = $_REQUEST['cl'] ;
$cs = $_REQUEST['cs'] ;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../css/jquery.autocomplete.css" />
<script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>
<script type="text/javascript">

</script>

</head>
<body>
<input type="text" name="t1" style="width:300px" id="t1"/>

<input type="button" value="送出" onclick="send_msg()">
<script type="text/javascript">
$(function() {
	$('#t1').focus() ;
	$('#t1').autocomplete('data_bank.php') ;
}) ;

function send_msg() {
	var str = $('#t1').val() ;
	var tmp = str.split(')') ;
	var str_id = tmp[0] ;
	tmp = new Array() ;
	tmp = str_id.split('(') ;
	str_id = tmp[1] ;
	
	parent.$('.b3<?=$cs?>_<?=$aaa?>').empty() ;
	parent.$('.b3<?=$cs?>_<?=$aaa?>').append('<option selected="selected" value="' + str_id + '">' + str + '</option>') ;
	
	window.parent.bank_select_index(str_id,'b4<?=$cs?>_<?php echo $aaa;?>','branch<?=$cs?>_<?php echo $aaa;?>',<?php echo $aaa;?>,'<?=$cs?>') ;

	parent.$('.ajax').colorbox.close() ;
}
</script>
</body>
</html>