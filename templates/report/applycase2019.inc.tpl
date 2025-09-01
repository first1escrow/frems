<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>

<!-- <link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	 -->
<!-- <script type="text/javascript" src="/js/jquery-easyui/jquery.min.js"></script> -->


<{include file='meta2.inc.tpl'}> 
<!-- <link rel="stylesheet" type="text/css" href="/js/datepicker/jquery-ui-datepicker.css" rel="Stylesheet" /> -->



<script type="text/javascript">
$(document).ready(function() {
	var aSelected = [];
	
	
	
	
	$('#dialog').dialog('close');
	
	$(".ajax").colorbox({width:"400",height:"100"});
	
	// $( "#branch_search" ).combobox() ;
	// $( "#scrivener_search" ).combobox() ;
	
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

function go(url) {
	var bk = $('[name="bank"]').val() ;	
	/*
	var sad = formatDate('sApplyDate') ;
	sad = sad.replace("e","") ;
	if (sad == 'f') {
		alert('進案日期(起)有誤!!') ;
		return false ;
	}
	
	var ead = formatDate('eApplyDate') ;
	ead = ead.replace("e","") ;
	if (ead == 'f') {
		alert('進案日期(迄)有誤!!') ;
		return false ;
	}
	
	var sed = formatDate('sEndDate') ;
	sed = sed.replace("e","") ;
	if (sed == 'f') {
		alert('結案日期(起)有誤!!') ;
		return false ;
	}
	
	var eed = formatDate('eEndDate') ;
	eed = eed.replace("e","") ;
	if (eed == 'f') {
		alert('結案日期(迄)有誤!!') ;
		return false ;
	}
	
	var ssd = formatDate('sSignDate') ;
	ssd = ssd.replace("e","") ;
	if (ssd == 'f') {
		alert('簽約日期(起)有誤!!') ;
		return false ;
	}
	
	var esd = formatDate('eSignDate') ;
	esd = esd.replace("e","") ;
	if (esd == 'f') {
		alert('簽約日期(迄)有誤!!') ;
		return false ;
	}
	*/
	var sad = $('[name="sApplyDate"]').val() ;
	var ead = $('[name="eApplyDate"]').val() ;
	var sed = $('[name="sEndDate"]').val() ;
	var eed = $('[name="eEndDate"]').val() ;
	var ssd = $('[name="sSignDate"]').val() ;
	var esd = $('[name="eSignDate"]').val() ;
	
	var br = $('[name="branch"]').val() ;
	var sc = $('[name="scrivener"]').val() ;
	var zp = $('[name="zip"]').val() ;
	var ct = $('#citys :selected').val() ;
	var bd = $('[name="brand"]').val() ;
	var ut = $('[name="undertaker"]').val() ;
	var st = $('[name="status"]').val() ;
	var es = $('[name="realestate"]').val() ;
	var cid = $('[name="cCertifiedId"]').val() ;
	var byr = $('[name="buyer"]').val() ;
	var owr = $('[name="owner"]').val() ;
	var sales = $('[name="sales"]').val();

	var s_cat = $("[name='scrivener_category']:checked").val();
	
	$( "#dialog" ).dialog("open") ;
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
	
	// alert(s_cat);
	$.post(url,
		{'bank':bk,'sApplyDate':sad,'eApplyDate':ead,'sEndDate':sed,'eEndDate':eed,'sSignDate':ssd,'eSignDate':esd,'branch':br,
		'scrivener':sc,'zip':zp,'citys':ct,'brand':bd,'undertaker':ut,'status':st,'realestate':es,
		'cCertifiedId':cid,'buyer':byr,'owner':owr,'show_hide':'hide','scrivener_category':s_cat,"sales":sales},
		function(txt) {
			$('#container').html(txt) ;
			$( "#dialog" ).dialog("close") ;
	}) ;
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
<div>
<form name="mycal">

	<h1>案件統計表</h1>

<{if $smarty.session.member_id == 6}>
<div>※配件狀況，數量平分(不含其他回饋對象)；保證費平分(以回饋金對象去分) ;回饋金按照實際分配 ;總價金不平分</div>
<{/if}>


<table cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">

<tr>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	系統別&nbsp;&nbsp;*　
	<select name="bank" size="1" style="width:150px;">
		<option value="">全部</option>
		<{$contract_bank}>
	</select>
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	進案日期(起)
	<input type="text" name="sApplyDate" class="datepickerROC" style="width:100px;">
	
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	進案日期(迄)
	<input type="text" name="eApplyDate" class="datepickerROC" style="width:100px;">
	
	</td>
</tr>
<tr>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	保證號碼　&nbsp;
	<input type="text" name="cCertifiedId" style="width:150px;" maxlength="9">
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	結案日期(起)
	<input type="text" name="sEndDate" class="datepickerROC" style="width:100px;">
	
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	結案日期(迄)
	<input type="text" name="eEndDate" class="datepickerROC" style="width:100px;">
	
	</td>
</tr>
<tr>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	&nbsp;
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	簽約日期(起)
	<input type="text" name="sSignDate" class="datepickerROC" style="width:100px;">
	
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	簽約日期(迄)
	<input type="text" name="eSignDate" class="datepickerROC" style="width:100px;">
	
	</td>
</tr>
<tr>
	<td colspan="3" style="background-color:#F8ECE9;">&nbsp;</td>
</tr>
<tr>
	<td colspan="2" style="width:600px;background-color:#E4BEB1;padding:4px;">
	仲介店名　
	<select name="branch" id="branch_search" class="easyui-combobox">
	<option></option>
	<{$branch_search}>
	</select>
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	仲介品牌　
	<select name="brand" size="1" style="width:130px;" >
	<option value="">全部</option>
	<{$brand}>
	</select>
	</td>
</tr>
<tr>
	<td colspan="2" style="width:600px;background-color:#E4BEB1;padding:4px;">
	地政士名稱
	<select name="scrivener" id="scrivener_search" class="easyui-combobox">
	<option></option>
	<{$scrivener_search}>
	</select>
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	仲介商類型
	<select name="realestate" size="1" style="width:130px;">
		<option value="">全部</option>
		<{$category}>
	</select>
	</td>
</tr>
<tr>
	<td colspan="2" style="width:600px;background-color:#E4BEB1;padding:4px;">
		地政士類別
		<input type="radio" name='scrivener_category' value="" checked>全部 <input type="radio" name="scrivener_category" value="1">台灣房屋加盟

	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	案件地區　
	<select name="country" id="citys" class="keyin2b">
		<{$citys}>
	</select>
	<select name="area" id="areas" class="keyin2b">
		<option value="">全部</option>
	</select>
	<input type="hidden" name="zip" id="zip" readonly="readonly" />
	</td>
</tr>
<tr>
	<td colspan="2" style="width:300px;background-color:#E4BEB1;padding:4px;">
	&nbsp;
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	案件狀態　
	<select name="status" size="1" style="width:130px;">
		<option value="">全部</option>
		<{$status}>
	</select>
	</td>
</tr>
<tr>
	<td style="width:300px;background-color:#E4BEB1;padding:4px;">
	&nbsp;
	</td>
	<td style="width:300px;background-color:#E4BEB1;">
	&nbsp;
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	承辦人　　
	<select name="undertaker" size="1" style="width:130px;">
		<option value="">全部</option>
		<{$undertaker}>
	</select>
	</td>
</tr>
<{if $smarty.session.member_pDep != 7}>
<tr>
	<td colspan="2" style="width:300px;background-color:#E4BEB1;padding:4px;">
	&nbsp;
	</td>
	<td style="width:300px;background-color:#F8ECE9;padding:4px;">
	負責業務&nbsp;&nbsp;&nbsp;
	<select name="sales" size="1" style="width:130px;">
		<option value="">全部</option>
		<{$menuSalse}>
	</select>
	</td>
</tr>


<{/if}>
</table>
<div style="padding:20px;text-align:center;">
<input type="button" value="查詢" onclick="go('applycase2019_result.php')" class="bt4" style="display:;width:100px;height:35px;">
<input type="button" value="匯出 excel 檔" onclick="xls('applycase2019_excel.php')" class="bt4" style="display:none;width:100px;height:35px;display:;">
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