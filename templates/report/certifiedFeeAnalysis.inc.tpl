<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<!-- <link rel="stylesheet" href="colorbox.css" />
<script src="/js/jquery-1.7.2.min.js"></script>
<script src="/js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" /> -->

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
	
	$(".ajax").colorbox({width:"400",height:"100"});
	
	
});

function postData(cat) {

	
	$("[name='ok']").val('ok');
	$('[name="form"]').submit();
}

function clearFrom(){

	$("[name='sEndDate']").val('');
	$("[name='eEndDate']").val('');
	$("[name='ok']").val('');
	$("[name='target']").val(0);
}

</script>
<style>
		#container2{
			width: 100%
		}
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
		.row{
			background-color:#FFFFFF;padding-top:5px;padding-left:5px;
		}
		.tb th{
			width:20%;background-color:#E4BEB1;padding:4px;
		}
		.tb td{
			background-color:white;padding:4px;padding-left:5px;
			
		}
		.tb{
			width:60%;border:1px solid #CCC;
		}

		.tb2{
			width:60%;border:1px solid #CCC;
		}
		.tb2 th{
			background-color:#E4BEB1;
			text-align:left;
			padding: 2px;
		}
		.tb2 td{
			text-align:left;

			padding: 2px;
			
			
		}
		
</style>
</head>
    <body id="dt_example">
        
        
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
                                    <td width="81%" align="right"></td>
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
										<div id="container2">
										<div id="dialog"></div>
<div>
<form name="form" method="POST">

<input type="hidden" name="ok">
<h1>未收足案件統計</h1>
<center>
	※比率(未收足案件數/案件總數)※配件狀況案件數不平分
<table cellspacing="0" cellpadding="0" style="" class="tb">
<tr>
	<th>統計對象</th>
	<td><{html_options name=target options=$menuTarget selected=<{$target}>}></td>
</tr>
<tr>
	<th style="">簽約日期</th>
	<td >
		<input type="text" name="sDate" class="datepickerROC" style="width:100px;" value="<{$sDate}>">(起)~
		<input type="text" name="eDate" class="datepickerROC" style="width:100px;" value="<{$eDate}>">
		(迄)
	
	</td>
</tr>

</table>
</center>
<div style="padding:20px;text-align:center;">
<input type="button" value="查詢" onclick="postData(0);" class="bt4" style="display:;width:100px;height:35px;">

</div>

</div>
<div id="data" style="width: 100%;z-index: 9999">
<center>
	<table width="100%" cellpadding="0" cellspacing="0" class="tb2">
		<tr>
			<th>名稱</th>
			<th>未收足件數</th>
			<th>案件總數</th>
			<th>比率</th>
		</tr>
		<{foreach from=$targetData key=key item=item}>
		<{if $item.count > 0}>
		<tr>
			<td><{$item.name}></td>
			<td><{$item.count}></td>
			<td><{$item.countTotal}></td>
			<td><{$item.ratio}>%</td>
		</tr>
		<{/if}>
		<{/foreach}>
	</table>
	
	
</div>
</center>
</div>
</form>
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