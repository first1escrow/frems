<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="colorbox.css" />
<link rel="stylesheet" type="text/css" href="../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="..js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
	alert_msg() ;
	
	$( "#dialog" ).dialog({
		autoOpen: false,
		modal: true,
		minHeight:50,
		show: {
			effect: "blind",
			duration: 1000
		},
		hide: {
			effect: "explode",
			duration: 1000
		}
	});
	$(".ui-dialog-titlebar").hide() ;
	
	$('[name="identity"]').change(function() {
		alert_msg() ;		
	}) ;
	$('#cancel').click(function() {
		window.reload() ;
	}) ;
	$('#search').click(function() {
		var url = 'taxreceipt_result.php' ;
		var _iden = $('[name="identity"]').val() ;
		var _year = $('[name="feedback_year"]').val() ;
		var _month = $('[name="feedback_month"]').val() ;
		var _sn = $('[name="sn"]').val() ;
		var _name = $('[name="tax_name"]').val() ;
		var _id = $('[name="tax_id"]').val() ;
		
		$( "#dialog" ).dialog("open") ;
		
		$.post(url,{'identity':_iden,'feedback_year':_year,'feedback_month':_month,'sn':_sn,'tax_name':_name,'tax_id':_id},function(txt) {
			$( "#dialog" ).dialog("close") ;
			$('#container').html(txt) ;
		}) ;
	}) ;
	$('#import').click(function() {
		//var identity = $('[name="identity"]').val() ;
		
		//if (identity=='1') {
		//	var sn_no = $('[name="sn"]').val() ;
		//	if (!sn_no.match(//) ;
		//}
		//else {
		
		//}
		
		$('[name="upload_form"]').submit() ;
	}) ;
	
	
	$('#search').button({
		icons:{
			primary: "ui-icon-check"
		}
	}) ;
	$('#cancel').button({
		icons:{
			primary: "ui-icon-refresh"
		}
	}) ;
	$('#import').button({
		icons:{
			primary: "ui-icon-folder-open"
		}
	}) ;
	$('#export_receipt_output').button({
		icons:{
			primary: "ui-icon-disk"
		}
	}) ;

});

function alert_msg() {
	var ide = $('[name="identity"]').val() ;
	if (ide == '2') {
		$('#alert_message').html('(季範圍：去年度第 4 季及今年度 1~3 季)') ;
		$('#id_title').html('仲介編號') ;
	}
	else {
		$('#alert_message').html('') ;
		$('#id_title').html('保證號碼') ;
	}
	
	$('[name="feedback_month"]').prop("disabled",false) ;
	var bb = $('[name="identity"]').val() ;
	if (bb=='2') {
		$('[name="feedback_month"]').val('') ;
		$('[name="feedback_month"]').prop("disabled",true) ;
	}
}
function opt() {
	var url = 'taxreceipt_result_output.php' ;
	var vals = $('[name="feedback_year"]').val() ;
	
	$('[name="yr"]').val(vals) ;
	$('form[name="output_form"]').submit() ;
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
#dialog {
	background-image:url("../images/animated-overlay.gif") ;
	background-repeat: repeat-x;
	margin: 0px auto;
}
</style>
</head>
    <body id="dt_example">
		<form name="upload_form" method="POST" action="upload_feedback.php" target="_blank">
		</form>
		<form name="output_form" method="POST" action="taxreceipt_result_output.php" target="_blank">
		<input type="hidden" name="yr">
		</form>
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
				<table width="1000" border="0" cellpadding="4" cellspacing="0">
					<tr>
						<td bgcolor="#DBDBDB">
							<table width="100%" border="0" cellpadding="4" cellspacing="1">
								<tr>
									<td height="17" bgcolor="#FFFFFF">
										<div id="menu-lv2">
                                                        
										</div>
										<br/> 
										<h3>&nbsp;</h3>
										<div id="container">
										<div id="dialog"></div>
<table cellspacing="0" cellpadding="0" style="padding:20px;">
	<tr>
		<td colspan="3" style="width:900px;background-color:#E4BEB1;padding:4px;text-align:left;">查詢條件︰</td>
	</tr>
	<tr style="background-color:#F8ECE9;">
		<td style="width:300px;text-align:left;padding:4px;">
			身分*　&nbsp;
			<select name="identity" style="width:100px;">
				<option value="1" selected="selected">買賣方利息</option>
				<option value="2" >個人回饋金</option>
				<option value="3" >回饋扣繳表</option>
			</select>
		</td>
		<td style="width:300px;text-align:left;padding:4px;">
			年度*
			<select name="feedback_year" style="width:50px;">
				<{$feedback_year}>
			</select>
			年　
			<select name="feedback_month" style="width:50px;">
				<{$feedback_month}>
			</select>
			月份
		</td>
		<td id="alert_message" style="width:300px;text-align:left;padding:4px;font-size:10pt;color:#C67171;">
		</td>
	</tr>
	<tr style="background-color:#F8ECE9;">
		<td style="width:300px;text-align:left;padding:4px;">
			<span id="id_title">仲介店編號</span>
			<input type="text" name="sn" style="width:100px;">
		</td>
		<td style="width:300px;text-align:left;padding:4px;">
			姓名&nbsp;
			<input type="text" name="tax_name" style="width:100px;">
		</td>
		<td style="width:300px;text-align:left;padding:4px;">
			身份證字號
			<input type="text" name="tax_id" style="width:100px;">
		</td>
	</tr>	
</table>

<div style="padding:20px;text-align:center;">
<center>
<button id="export_receipt_output" onclick="opt()">輸出格式檔</button>
<button id="search">確定</button>
<button id="cancel">取消</button>
<button id="import">匯入季回饋金額</button>
</center>
</div>

				</div>
<div id="footer" style="height:50px;">
<p>2012 第一建築經理股份有限公司 版權所有</p>
</div>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</div>


</body>
</html>