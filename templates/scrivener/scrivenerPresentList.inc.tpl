<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>

<{include file='meta2.inc.tpl'}> 

<script type="text/javascript">
$(document).ready(function() {
	$('#dialog').dialog('close');

	

	$("[name='cat']").on('click',function() {

		if ($(this).val() == 1) {
			$("#d").show();
		}else{
			$("#d").hide();
		}
		// event.preventDefault();
		/* Act on the event */
	});
});

function go(val) {
	
	
	$('[name="cat"]').val(val);

	// console.log($('[name="xls"]').val());

	var check = false;
		$('input:checkbox:checked[name="sId[]"]').each(function(i) { 
			check = true; 
		});

		
		
		$('[name="mycal"]').submit();
		
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
		
#dialog {
	background-image:url("/images/animated-overlay.gif") ;
	background-repeat: repeat-x;
	margin: 0px auto;
	width: 300px; 
	height: 30px;
}
.easyui-combobox{
	width: 300px;
}
.tb {
	padding:5px;
	margin-bottom: 20px;
	background-color:#FFFFFF;
}
.tb th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
.tb td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
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
										<h1>地政士生日禮查詢(非禮券類)</h1>
										<div id="container">
										<div id="dialog" class="easyui-dialog" title="" style="display:none"></div>
<div style="border: 1px solid #CCC">
<form name="mycal" method="POST">

	<table cellspacing="5" cellpadding="0" width="100%" border="0">
	
	<tr>
		<th>申請年：</th>
		<td>
			<select id="year" name="year" style="width:80px;">
				<option value="">全部</option>
				<{$option_year}>
			</select>
		</td>
		<th>品項</th>
		<td>
			<select name="gift" id="gift">
				<option value="">全部</option>
				<{$option_gift}>
			</select>
		</td>
		<th>地政士名稱：</th>
		<td>
			<select name="scrivener" id="scrivener_search" class="easyui-combobox">
			<option value="">全部</option>
			<{$scrivener_search}>
			</select>
		</td>	
		
	</tr>
	</table>
	<div style="padding:20px;text-align:center;">
		<!-- <input type="button" value="查詢" onclick="go('search')" class="bt4"> -->
		<input type="button" value="匯出EXCEL" onclick="go('xls')" class="bt4" style="width:100px;height:35px;">
	
		<input type="hidden" name="cat" >
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