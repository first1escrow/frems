<?php
$aaa = $_REQUEST['cl'] ;
$bbb = $_REQUEST['bk'] ;
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
<input type="text" name="t1" style="width:300px" id="t1" value="" />

<input type="button" value="送出" onclick="send_msg()">
<script type="text/javascript">
$(function() {
	$('#t1').autocomplete('data_bank_1.php?bk=<?php echo $bbb?>') ;
}) ;

function send_msg() {
	var str = $('#t1').val() ;
	var tmp = str.split(')') ;
	var str_id = tmp[0] ;
	tmp = new Array() ;
	tmp = str_id.split('(') ;
	str_id = tmp[1] ;
	
	parent.$('.b4<?=$cs?>_<?=$aaa?>').empty() ;
	parent.$('.b4<?=$cs?>_<?=$aaa?>').append('<option selected="selected" value="' + str_id + '">' + str + '</option>') ;
	
	$.ajax({
		url: '../getBankPhone.php',
		type: 'POST',
		dataType: 'html',
		data: {'bank': "<?php echo $bbb?>",'branch':str_id},
	})
	.done(function(txt) {
		
		$("#bankp<?=$aaa?>").html('電話：'+txt);
	});
	
	parent.$('.ajax').colorbox.close() ;
}
</script>
</body>
</html>