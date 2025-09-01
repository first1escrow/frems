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
function first() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	if (current_page <= 1) { return false ; }
	else { current_page = 1 ; }

	$('[name="current_page"]').val(current_page);
	postData(0);
}
function back() {
	var current_page = parseInt($('[name="current_page"]').val()) - 1 ;

	if (current_page <= 0) { return false ; }
	$('[name="current_page"]').val(current_page);
	postData(0);
	
}
function next() {
	var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
	var total_page = parseInt($('[name="total_page"]').val()) ;
	
	if (current_page > total_page) { return false ; }

	$('[name="current_page"]').val(current_page);
	postData(0);

}
function last() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	$('[name="current_page"]').val(current_page);
	postData(0);
	
}
function direct() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { current_page = total_page ; }
	else if (current_page <= 0) { current_page = 1 ; }

	$('[name="current_page"]').val(current_page);
	postData(0);
	
}
function show_limit() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	$('[name="current_page"]').val(current_page);
	postData(0);
}

function postData(cat,act) {

	// var d1 = $("[name='sDate']").val();
	// var d2 = $("[name='eDate']").val();
	
	// if (d1 == '' || d2 == '') {
		
	// 	alert('請先選擇日期');
	// 	return false;
	// }
	
	// console.log($('[name="current_page"]').val());
	
	if (act == 1) {
		$("[name='ok2']").val('ok');
	}else{
		$("[name='ok2']").val('');
	}
	
	$("[name='ok']").val('ok');
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
function checkALL(){

	$("[name='cId[]']").each(function() {
			
		if ($("[name='all']").prop('checked')==true) {
			$(this).prop('checked', true);
		
		}else{
			$(this).prop('checked', false);
		}
	});

	
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
		.tb2 th{
			background-color:#E4BEB1;
			font-size: 12px;
		}
		.tb2 td{
			text-align:center;
			font-size: 12px;
			
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
<input type="hidden" name="xls">
<input type="hidden" name="ok">
<input type="hidden" name="ok2">
<h1>未收足案件</h1>
<center>
<table cellspacing="0" cellpadding="0" style="" class="tb">

<tr>
	<th style="">日期</th>
	<td >
		<input type="radio" name="cat" value="sign" <{$checked1}>>簽約日
		<!-- <input type="radio" name="cat" value="end" <{$checked2}>>結案日 -->
		<input type="radio" name="cat" value="check_date" <{$checked3}>>業務審核日
	</td>
</tr>
<tr>
	<th>
	</th>
	<td >
	<input type="text" name="sDate" class="datepickerROC" style="width:100px;" value="<{$sDate}>">(起)~
	<input type="text" name="eDate" class="datepickerROC" style="width:100px;" value="<{$eDate}>">
	(迄)
	</td>
</tr>
<tr>
	<th>地政士</th>
	<td >
		
		<select name="scrivener" id="scrivener_search" class="easyui-combobox">
		<option></option>
		<{$scrivener_search}>
		</select>
	</td>
</tr>
<tr>
	<th>保證號碼</td>
	<td ><input type="text" name="cCertifiedId" id="" maxlength="9" value="<{$cCertifiedId}>"></td>
</tr>
<tr>
	<th>審核</td>
	<td>
		<{html_options name=review options=$reviewMenu selected=$review}>
	</td>
</tr>
<tr>
	<th>案件狀態</td>
	<td>
		<{html_options name=caseStatus options=$statusMenu selected=$caseStatus}>
	</td>
</tr>


</table>
</center>
<div style="padding:20px;text-align:center;">
<input type="button" value="查詢" onclick="postData(0);" class="bt4" style="display:;width:100px;height:35px;">
<input type="button" value="清除" class="bt4" style="display:;width:100px;height:35px;" onclick="clearFrom()">

</div>

</div>
<div id="data" style="width: 100%;z-index: 9999">
	<table width="100%" border="0" class="tb2" cellspacing="0" cellpadding="0">
		<tr style="background-color:#E4BEB1;line-height:15px;">
			<th  style="padding-left: 10px;">
				<input type="checkbox" name="all" id="" onclick="checkALL();">
			</th>
			<th width="5%">保證號碼</th>
			<th width="10%">仲介品牌</th>
			<th width="10%">仲介店名</th>
			<th width="10%">賣方</th>
			<th width="10%">買方</th>
			<th width="10%">總價金</th>
			<th width="5%">合約保<br>證費</th>
			<th width="5%">簽約日</th>
			<th width="10%">地政士姓名</th>
			<th width="15%">原因</th>
			<th width="5%">業務</th>
			<th width="5%">業務主管</th>
		</tr>
		<{foreach from=$list key=key item=item}>
		<tr style="background-color:<{$item.color}>;">
			<td style="padding-left: 10px;">

				<{if $item.show == 1}>
				<input type="checkbox" name="cId[]" id="" value="<{$item.cCertifiedId}>">
				
				<{/if}>

			</td>
			<td><{$item.cCertifiedId}></td>
			<td><{$item.showBrand}></td>
			<td><{$item.showBranch}></td>
			<td><{$item.owner}></td>
			<td><{$item.buyer}></td>
			<td><{$item.cTotalMoney}></td>
			<td><{$item.cCertifiedMoney}></td>
			<td><{$item.cSignDate}></td>
			<td><{$item.sName}></td>
			<td><{$item.cNote}></td>
			<td><{$item.cInspetorName}></td>
			<td><{$item.cInspetorName2}></td>
		</tr>
		<{/foreach}>
	</table>
	<br>
	<div style="text-align: center">
		
		<input type="button" name="audit" value="審核" onclick="postData(0,1)" class="bt4" style="display:;width:100px;height:35px;">
		
		<{if $smarty.session.member_pDep != 7}>
			<!-- <input type="button" name="audit" value="審核不通過" onclick="postData(3)" class="bt4" style="display:;width:100px;height:35px;"> -->
		<{/if}>
	</div>
	
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