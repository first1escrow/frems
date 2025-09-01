<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>

<{include file='meta2.inc.tpl'}> 

<script type="text/javascript">
$(document).ready(function() {
	$('#dialog').dialog('close');

	<{if $cat == 1}>
		$("#d").show();
	<{else}>
		$("#d").hide();
	<{/if}>

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
	$('[name="xls"]').val(val);


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
#scrTable {
	padding:5px;
	margin-bottom: 20px;
	background-color:#FFFFFF;
}
#scrTable th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#scrTable td{
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
										<h3>&nbsp;</h3>
										<div id="container">
										<div id="dialog" class="easyui-dialog" title="" style="display:none"></div>
<div style="border: 1px solid #CCC">
<form name="mycal" method="POST">

	<table cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">
	
	
	<tr>
		<th style="padding-bottom:10px;">查詢時間類別</th>
		<td style="padding-bottom:10px;" colspan="5">
		<{html_radios name=cat options=$menuCat selected=$cat}>
			<!-- <input type="radio" name="cat" value="1" checked>地政士生日
			<input type="radio" name="cat" value="2">進案時間 -->
		</td>
	</tr>
	
	<tr>
		<th>年度：</th>
		<td >
			
			<select id="year" name="year" style="width:50px;">
				
				<{$option_year}>
			</select>年
			
			<span id="d">
				
			
				<select id="month" name="month" style="width:50px;">
					
					<{$option_month}>
				</select>月
			</span>
			
		</td>
	
		<th>仲介商類型</th>
		<td>
		<select name="realestate" id="">
			<{$category}>
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
		<input type="button" value="查詢" onclick="go('')" class="bt4" style="width:100px;height:35px;display:;">
		<{if $smarty.session.member_id == 6 }>
		
		<{if $xls == 1}>
			<input type="button" value="EXCEL" onclick="go(1)" class="bt4" style="width:100px;height:35px;display:;">
		<{/if}>
		<{/if}>
		<input type="hidden" name="xls" >
	</div>
</form>

</div>

<div>
	<div id="scrTable">
	<h2>等級1 <font color="red">[50萬以上]</font>	(數量:<{$Datalevel[1]['count']}>)</h2>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<th width="8%">編號</th>
						<th width="16%">地政士</th>
						<th width="15%">生日</th>
						<th width="15%">保證費</th>
						<th width="15%">回饋金</th>
						<th width="15%">收入</th>
					</tr>
					<{foreach from=$Datalevel[1]['data'] key=key item=item}>
					<tr>
						<td><{$item.Code}></td>
						<td><{$item.sName}></td>
						<td><{$item.sBirthday}></td>
						<td><{$item.certifiedMoney}></td>
						<td><{$item.caseFeedBackMoney}></td>
						<td><{$item.income}></td>
						
					</tr>
					<{/foreach}>
					
					<{if $Datalevel[1]['count'] == 0}>
					<tr><td colspan="6">無資料</td></tr>
					<{/if}>
				</table>
	</div>
	<div id="scrTable">
	<h2>等級2<font color="red">[30萬以上至50萬以下]</font>(數量:<{$Datalevel[2]['count']}>)</h2>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<th width="8%">編號</th>
						<th width="16%">地政士</th>
						<th width="15%">生日</th>
						<th width="15%">保證費</th>
						<th width="15%">回饋金</th>
						<th width="15%">收入</th>
					</tr>
					<{foreach from=$Datalevel[2]['data'] key=key item=item}>
					<tr>
						<td><{$item.Code}></td>
						<td><{$item.sName}></td>
						<td><{$item.sBirthday}></td>
						<td><{$item.certifiedMoney}></td>
						<td><{$item.caseFeedBackMoney}></td>
						<td><{$item.income}></td>
						
					</tr>
					<{/foreach}>
					
					<{if $Datalevel[2]['count'] == 0}>
					<tr><td colspan="6">無資料</td></tr>
					<{/if}>
				</table>
	</div>
	<div id="scrTable">
	<h2>等級3<font color="red">[10萬以上至30萬以下]</font>(數量:<{$Datalevel[3]['count']}>)</h2>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<th width="8%">編號</th>
						<th width="16%">地政士</th>
						<th width="15%">生日</th>
						<th width="15%">保證費</th>
						<th width="15%">回饋金</th>
						<th width="15%">收入</th>
					</tr>
					<{foreach from=$Datalevel[3]['data'] key=key item=item}>
					<tr>
						<td><{$item.Code}></td>
						<td><{$item.sName}></td>
						<td><{$item.sBirthday}></td>
						<td><{$item.certifiedMoney}></td>
						<td><{$item.caseFeedBackMoney}></td>
						<td><{$item.income}></td>
						
					</tr>
					<{/foreach}>
					
					<{if $Datalevel[3]['count'] == 0}>
					<tr><td colspan="6">無資料</td></tr>
					<{/if}>
				</table>
	</div>
	<div id="scrTable">
	<{if $smarty.session.member_id == 6}>
	<h2>不在等級範圍內的(數量:<{$Datalevel[0]['count']}>)</h2>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<th width="8%">編號</th>
						<th width="16%">地政士</th>
						<th width="15%">生日</th>
						<th width="15%">保證費</th>
						<th width="15%">回饋金</th>
						<th width="15%">收入</th>
					</tr>
					<{foreach from=$Datalevel[0]['data'] key=key item=item}>
					<tr>
						<td><{$item.Code}></td>
						<td><{$item.sName}></td>
						<td><{$item.sBirthday}></td>
						<td><{$item.certifiedMoney}></td>
						<td><{$item.caseFeedBackMoney}></td>
						<td><{$item.income}></td>
						
					</tr>
					<{/foreach}>
					
					<{if $Datalevel[0]['count'] == 0}>
					<tr><td colspan="6">無資料</td></tr>
					<{/if}>
				</table>
	</div>
	<{/if}>
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