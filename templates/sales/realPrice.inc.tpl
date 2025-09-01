<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	
	/* enter 輸入 */
	$(this).keypress(function(e) {
		if (e.keyCode == 13) {
			save() ;
		}
	}) ;
	////
	
	//姓名搜尋
	$('[name="buyer"]').autocomplete('data_buyer.php') ;
	$('[name="owner"]').autocomplete('data_owner.php') ;
	$('[name="buyer_agent"]').autocomplete('data_buyer_agent.php') ;
	$('[name="owner_agent"]').autocomplete('data_owner_agent.php') ;
	$('[name="scrivener"]').autocomplete('data_scrivener.php') ;
	$('[name="branch"]').autocomplete('data_branch.php') ;
	

});

function checkSelection() {
	var ct = $('[name="city"]').val() ;
	var s_yc = '' ;
	var s_sy = '' ;
	
	$('.s_check').each(function() {
		if ($(this).prop('checked') == true) {
			if ($(this).val() == 'yc') {
				s_yc = 'yc' ;
			}
			
			if ($(this).val() == 'sy') {
				s_sy = 'sy' ;
			}
		}
	}) ;
	
	if ((s_yc == 'yc') && (s_sy == 'sy')) {
		$('[name="source"]').val('') ;
	}
	else if (s_yc == 'yc') {
		$('[name="source"]').val('永慶') ;
	}
	else if (s_sy == 'sy') {
		$('[name="source"]').val('信義') ;
	}
	else {
		alert('請至少選擇一家仲介品牌!!') ;
		return false ;
	}
	
	$('[name="xls"]').val('ok') ;
	$('#myform').submit() ;
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
<div>	<font color="red">※每月1號抓取上個月的資料</font></div>										
<div style="text-align:center;">
	<form method="POST" id="myform">
		<input type="hidden" name="xls">
		<input type="hidden" name="source">
		
		<div style="float: left;width:300px; padding:5px;">
			查詢時間：
			<select name="years">
			<{$yearMenu}>
			</select>
			年
			<select name="months">
			<{$monthMenu}>
			</select>	
		</div>
		
		<div style="float: left;width:200px; padding:5px;">
			查詢地區：
			<select name="city">
				<{$cityMenu}>
			</select>
		</div>
		
		<div style="float: left;width:200px; padding:7px;">
			<input type="checkbox" class="s_check" value="yc" checked="checked">&nbsp;永慶
			<input type="checkbox" class="s_check" value="sy" checked="checked">&nbsp;信義
		</div>
		
		
		<div style="padding:1px;">
			<input type="button" value="產出檔案" onclick="checkSelection()" style="padding: 5px;">
		</div>
		
		<div style="clear:both;"></div>
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