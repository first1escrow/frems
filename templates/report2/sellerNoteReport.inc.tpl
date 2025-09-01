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
	
	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"100"});
	
	$( "#branch_search" ).combobox() ;
	$( "#scrivener_search" ).combobox() ;
	
	$('#citys').change(function() {
		cityChange() ;
	}) ;
	
	$('#areas').change(function() {
		areaChange() ;
	}) ;
});

function Case(cId){
	//name="detail"
	// console.log(cId);
	$('[name="id"]').val(cId);
	$('[name="detail"]').submit();
}

function postData(cat) {	
	$('[name="form"]').submit();
}

function clearFrom(){

	$("[name='sEndDate']").val('');
	$("[name='eEndDate']").val('');
	$("[name='ok']").val('');
	$("[name='xls']").val('');
	$('[name="current_page"]').val('');
	$("[name='sales']").val('');
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
		.row{
			background-color:#FFFFFF;padding-top:5px;padding-left:5px;
		}
		.tb th{
			background-color:#E4BEB1;padding:4px;
		}
		.tb td{
			background-color:#F8ECE9;padding:4px;padding-left:5px;
		}
		.tb{
			border: 1px solid #CCC;
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
										<div id="container">
										<div id="dialog"></div>
<div>
<form action="../escrow/formbuyowneredit.php" method="POST" name="detail" target="_blank">
		<input type="hidden" name="id">
</form>
<form name="form" method="POST">

<input type="hidden" name="ok">
<h1>非賣方本人備註未填寫表</h1>
<center>
<table cellspacing="0" cellpadding="0" style="width:60%;border:1px solid #CCC">


<tr>
	<td style="width:20%;background-color:#E4BEB1;padding:4px;">
        
        出款日
       
        <!-- 結案日期 -->
       
	       

	</td>
	<td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
	<input type="text" name="today" class="datepickerROC" style="width:100px;" value="<{$today}>">
	
	</td>
	
</tr>


</table>
</center>
<div style="padding:20px;text-align:center;">
<input type="button" value="查詢" onclick="postData(0);" class="bt4" style="display:;width:100px;height:35px;">

</div>

</div>
<div id="data">
	<center>
		<table width="60%" border="0" class="tb">
			<tr>
				<th>保證號碼</th>
				<th>經辦</th>
			</tr>
			<{foreach from=$sellerNote key=key item=item}>
			<tr>
				<td><a href="#" onclick="Case('<{$item.cCertifiedId}>')"><{$item.cCertifiedId}></a></td>
				<td><{$item.Undertaker}></td>
			</tr>
			<{/foreach}>
		</table>
	</center>
	<br>
	
</div>

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