<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<link rel="stylesheet" href="colorbox.css" />
<script src="/js/jquery-1.7.2.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<{include file='meta.inc.tpl'}> 		
<script type="text/javascript">
$(document).ready(function() {
	var aSelected = [];
	
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
	
	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"100"});
	
	$('#citys').change(function() {
		cityChange() ;
	}) ;
	
	$('#areas').change(function() {
		areaChange() ;
	}) ;
});

/* 取得縣市區域資料 */
function cityChange() {
	var url = 'zipArea.php' ;
	var _city = $('#citys :selected').val() ;
	$.post(url,{'c':_city,'op':'1'},function(txt) {
		$('#areas').html(txt) ;
	}) ;
}
////

/* 取得區域郵遞區號 */
function areaChange() {
	var _area = $('#areas :selected').val() ;
	$('#zip').val(_area) ;
}
////

function output() {
	$('[name="go"]').val('excel') ;
	//$( "#dialog" ).dialog("open") ;
	$('form[name="mycal"]').submit() ;
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
	background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
}
.ui-autocomplete-input {
	width:300px;
}
#dialog {
	background-image:url("/images/animated-overlay.gif") ;
	background-repeat: repeat-x;
	margin: 0px auto;
}
#searchTb td {
	width:300px;
	background-color:#F8ECE9;
	padding:10px;
}
</style>
</head>
    <body id="dt_example">
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="id" id="id" value='3' />
        </form>
        <form name="form_add" id="form_add" method="POST">
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
<div>
	<form name="mycal" method="POST">
	
	<table id="searchTb" cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">
		<tr>
			<td style="">
				<div style="width:100px;float:left;">案件地區</div>
				<div>
					<select name="city" id="citys" style="width:80px;">
						<{$citys}>
					</select>
					<select name="area" id="areas" style="width:70px;">
						<option value="">鄉鎮市區</option>
					</select>
				</div>
				<input type="hidden" name="zip" id="zip" readonly="readonly" />
			</td>
			<td style="">
				<div style="width:100px;float:left;">簽約日期(起)</div>
				<div>
					<input type="text" name="cSignDateFrom" class="datepickerROC" style="width:100px;" value="<{$sSingDate}>">
				</div>
			</td>
			<td style="">
				<div style="width:100px;float:left;">簽約日期(迄)</div>
				<div>
					<input type="text" name="cSignDateTo" class="datepickerROC" style="width:100px;" value="<{$eSignDate}>">
				</div>
			</td>
		</tr>
	</table>
	
	<div style="padding:20px;text-align:center;">
		<input type="button" value="產出 excel 檔" onclick="output()"class="bt4" style="width:100px;height:35px;">
		<input type="hidden" name="go">
	</div>
	
	</form>
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