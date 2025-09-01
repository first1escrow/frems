<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {	
	/* enter 輸入 */
	$(this).keypress(function(e) {
		if (e.keyCode == 13) {
			comp() ;
		}
	}) ;
	////
	
	// $(".inline").colorbox({inline:true, width:"70%", height:"90%"}) ;
});

function comp() {
	var dd = $('#qDate').val() ;
	
	if (dd == '') {
		alert("請選擇欲比對的日期!!") ;
		return false ;
	}
	else {
		$('#qC').val('ok') ;
		$('#myform').submit() ;
	}
}
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
.qScr {
	-webkit-border-radius: 10px;
	/* support firefox */
	-moz-border-radius: 10px;
	border-radius: 10px;
}
#qs td {
	padding: 5px;
}
</style>
</head>
<body>
	

	<{if $type == 'all'}>

		<div id="alls"><{$alls}></div>
	<{elseif $type == 'add'}>
		<div id="adds"><{$adds}></div>
	<{else}>
		<div id="dels"><{$dels}></div>
	<{/if}>	
			

   

</body>
</html>