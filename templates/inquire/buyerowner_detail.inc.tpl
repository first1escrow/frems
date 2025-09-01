<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>客戶明細表</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<link rel="stylesheet" type="text/css" href="/css/colorbox.css" rel="Stylesheet" />
<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/ROCcalender_limit.js"></script>
<script type="text/javascript" src="/js/jquery.colorbox.js"></script>
<script type="text/javascript">

$(function() {
	$(".iframe").colorbox({
		iframe:true,
		fastIframe: true,
		width:"550px",
		height:"90%",
		onClosed: function() {
			$('[name="reopen"]').submit() ;
			//alert('AAA') ;
		}
	}) ;
	
	<{if $save_ok == '1'}>
		<{if $cmChange == 'ok'}>
	$('#dialog').html('<div style="text-align:center;"><p style="font-weight:bold;">檔案已更新!!</p><div style="font-size:10pt;font-weight:bold;color:red;">回饋金已重新分配!!請至合約書中查詢...</div></div>') ;
		<{else}>
	$('#dialog').html('<div style="text-align:center;"><p style="font-weight:bold;">檔案已更新!!</p></div>') ;
		<{/if}>
	$('#dialog').prop({'title':'更新成功!!'}) ;
	$('#dialog').dialog({
		modal: true,
		buttons: {
			"OK": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
	<{/if}>
	
	// callback function
	$('#trans_build').click(function() {
		$('form[name="myform"]').submit() ;
	}) ;
	$('#save').click(function() {
		var st = $('[name="caseStatus"]').val() ;
						
		if ((st=='3')||(st=='4')) {
			/* 發票金額檢核 */
			var cerMoney = parseInt($('[name="cCertifiedMoney"]').val()) ;			//履保費
			
			var invOwner = parseInt($('[name="invoiceOwner"]').val()) ;				//賣方發票金額
			var invBuyer = parseInt($('[name="invoiceBuyer"]').val()) ;				//買方發票金額
			var invRealty = parseInt($('[name="invoiceRealestate"]').val()) ;		//仲介發票金額
			var invScr = parseInt($('[name="invoiceScrivener"]').val()) ;			//代書發票金額
			var invOther = parseInt($('[name="invoiceOther"]').val()) ;				//其他發票金額
			
			var invTotal = invOwner + invBuyer + invRealty + invScr + invOther ;	//實際發票金額加總
			if (cerMoney != invTotal) {
				alert('請確認發票對象金額是否正確分配!?') ;
				return false ;
			}
			////
			
			/* 利息金額檢核 */
			var intT = $('[name="int_total"]').val() ;
			var intM = $('[name="int_money"]').val() ;
			
			if (intT < 0) {
				alert('尚未產生利息資訊!!請至合約書產生點交表以產出利息...') ;
				return false ;
			}
			else {
				if (intT != intM) {
					alert('請確認利息金額是否正確分配!?') ;
					return false ;
				}
			}
			////
		}
		
		$('input[name="save_ok"]').val(1) ;
		$('form[name="saveForm"]').submit() ;
		
	}) ;
	$('[name="zip_city"]').change(function() {
		var url = 'get_zip_area.php' ;
		var ct = $('[name="zip_city"]').val() ;
		$.post(url,{'city':ct},function(txt) {
			$('[name="zip_area"]').html(txt) ;
		}) ;
	}) ;
	$('[name="zip_area"]').change(function() {
		var url = 'get_zip_no.php' ;
		var ct = $('[name="zip_city"]').val() ;
		var ar = $('[name="zip_area"]').val() ;
		$.post(url,{'city':ct,'area':ar},function(txt) {
			var patt = txt.split('_') ;
			var str = patt[1] ;
			$('[name="addrZip"]').val(str) ;
		}) ;
	}) ;
	$('[name="signDate"]').click(function() {
		show_calendar('saveForm.signDate') ;
	}) ;
	
	$('.incomeDetail').mouseover(function(e) {
		var url = 'expenseInfo.php' ;
		var tags = 'infoWin' ;
		var exp = $(this).attr('id') ;
		var cer = '<{$cCertifiedId}>' ;
		
		$.post(url,{'eid':exp,'cid':cer},function(txt) {
			$('#'+tags).html(txt) ;
			set_w_word(tags) ;
			$('#'+tags).css({'position':'absolute','left':(e.pageX+10),'top':(e.pageY+10),'display':''});
		}) ;
	}) ;
	
	$('.incomeDetail').mouseout(function() {
		var tags = 'infoWin' ;
		hide_w(tags) ;
	}) ;
	
	$('#show1').mouseout(function() {
		var tags = 'infoWin' ;
		hide_w(tags) ;
	}) ;
	
	// button icon
	$('#trans_build').button({
		icons:{
			primary: "ui-icon-transfer-e-w"
		}
	}) ;
	$('#save').button({
		icons:{
			primary: "ui-icon-check"
		}
	}) ;
}) ;
	
/* 設定提醒視窗 */
function set_w_word(tags) {
	var id_tag = '#' + tags ;
	$(id_tag).css({
		'padding':'5px',
		'background-color':'#FFFFFF',
		'font-size':'9pt',
		'margin':'0px auto',
		'line-height':'1.5em',
		'border-width':'1px',
		'border-style':'solid',
		'border-color':'#00000000',
		'text-align':'left'
	}) ;
}
//

/* 關閉提醒視窗 */
function hide_w(tn) {
	var tags = tn ;
	$('#'+tags).css({'display':'none'});
}
//

function processing(step) {
	var _original = "<{$list.status}>" ;
	$('[name="cCaseProcessing"]').val(step) ;
	for (var i = 1 ; i < 7 ; i ++) {
		if (i <= step) {
			$('#ps'+i).addClass('step_class') ;
		}
		else {
			$('#ps'+i).removeClass('step_class') ;
		}
	}
	
	var _last = $('[name="caseStatus"]').val() ;
	if (_last!='3') {
		$('[name="caseStatus"]').children().each(function() {
			if (step=='6') {
				if ($(this).text()=="已結案") {
					$(this).attr("selected","true") ;
				}
			}
		}) ;
	}
	else {
		$('[name="caseStatus"]').children().each(function() {
			if (step!='6') {
				if ($(this).val()==_original) {
					$(this).attr("selected","true") ;
				}
			}
		}) ;
	}

}

function total_money() {
	var sm = $('[name="cSignMoney"]').val() ;
	var am = $('[name="cAffixMoney"]').val() ;
	var dm = $('[name="cDutyMoney"]').val() ;
	var em = $('[name="cEstimatedMoney"]').val() ;

	var tm = parseInt(sm) + parseInt(am) + parseInt(dm) + parseInt(em) ;
	$('[name="cTotalMoney"]').val(tm) ;
	certify_money() ;
}

/* 自動計算調整履保費 */
function certify_money() {
	var tm = parseInt($('[name="cTotalMoney"]').val()) ;		//總價金
	var cm = Math.round(tm * 0.0006) ;
	if (cm<=600) {
		cm=600;
	}						//履保費
	$('[name="cCertifiedMoney"]').val(cm) ;
	$('[name="cCMChange"]').val('ok') ;
}
////

function real_money(type) {

	if (type=='buy') {//cRealestateMoneyBuyer

		var am = $('[name="cAdvanceMoneyBuyer"]').val() ;
		var rm = $('[name="cRealestateMoneyBuyer"]').val() ;
		
		var tm = parseInt(rm) - parseInt(am) ;
		$('[name="cDealMoneyBuyer"]').val(tm) ;
	}else
	{
		var am = $('[name="cAdvanceMoney"]').val() ;
		var rm = $('[name="cRealestateMoney"]').val() ;
		
		var tm = parseInt(rm) - parseInt(am) ;
		$('[name="cDealMoney"]').val(tm) ;
	}
	
}
function status_change() {
	var st = $('[name="caseStatus"]').val() ;
	if (st=='3') {
		for (var i = 1 ; i < 7 ; i ++) {
			$('#ps'+i).removeClass('step_class') ;
			$('#ps'+i).addClass('step_class') ;
		}
	}
}

function reload_page(){
	location.reload();
}

 function getArea2(ct,ar,zp) {
    var url = '../escrow/listArea.php' ;


    var ct = $('#' + ct + ' :selected').val() ;
                
    $('#' + zp + '').val('') ;
    $('#' + zp + 'F').val('') ;
    $('#' + ar + ' option').remove() ;
                
    $.post(url,{"city":ct},function(txt) {
        var str = '' ;
        str = str + txt  ;
        $('#' + ar ).append(str) ;
    }) ;
}
            
function getZip2(ar,zp) {

    var zips = $('#' + ar + ' :selected').val() ;

    $('#' + zp + '').val(zips);
    $('#' + zp + 'F').val(zips.substr(0,3));

   
}
</script>
<style type="text/css">
td {
	border-width:1px ;
	border-style:solid ;
	border-color:#ccc ;
}
.step_class {
	background-color: red;
}
.dollars {
	text-align: right;
}
</style>
</head>
<body>
<div id="dialog">
	
</div>

<div id="infoWin">
	
</div>
<form method="POST" name="reopen">
<input type="hidden" name="sn" value="<{$cCertifiedId}>">
</form>
<form method="POST" name="myform" action="/bank/new/out1.php">
<input type="hidden" name="vr" value="<{$list.account}>">
</form>
<center>
<form method="POST" name="saveForm">
<input type="hidden" name="cCertifiedId" value="<{$cCertifiedId}>">
<input type="hidden" name="cCaseProcessing" value="<{$list.cCaseProcessing}>">

<input type="hidden" name="recall" value="<{$list.recall}>">
<input type="hidden" name="recall1" value="<{$list.recall1}>">
<input type="hidden" name="recall2" value="<{$list.recall2}>">
<input type="hidden" name="recall_branch" value="<{$list.branch}>">
<input type="hidden" name="recall_branch1" value="<{$list.branch1}>">
<input type="hidden" name="recall_branch2" value="<{$list.branch2}>">

<input type="hidden" name="cCMChange" value="">

<input type="hidden" name="save_ok" value="">
<input type="hidden" name="invoiceOwner" value="<{$invoice.cInvoiceOwner}>">
<input type="hidden" name="invoiceBuyer" value="<{$invoice.cInvoiceBuyer}>">
<input type="hidden" name="invoiceRealestate" value="<{$invoice.cInvoiceRealestate}>">
<input type="hidden" name="invoiceScrivener" value="<{$invoice.cInvoiceScrivener}>">
<input type="hidden" name="invoiceOther" value="<{$invoice.cInvoiceOther}>">

<input type="hidden" name="cCaseFeedback" value="<{$list.cCaseFeedback}>">
<input type="hidden" name="cFeedbackTarget" value="<{$list.cFeedbackTarget}>">
<input type="hidden" name="sRecall" value="<{$list.sRecall}>">

<input type="hidden" name="int_total" value="<{$int_total}>">
<input type="hidden" name="int_money" value="<{$int_money}>">

<div style="width:660px;text-align:left;">查詢結果頁</div>
<table cellspacing=0px padding=0px style="width:660px;">
	<tr>
		<td style="background-color:#E4BEB1;">進度圖</td>
		<{$processing}>
	</tr>
	<tr style="background-color:#F8ECE9;">
		<td style="background-color:#E4BEB1;width=110px;">步驟</td>
		<td style="width=91px;">簽約</td>
		<td style="width=91px;">用印</td>
		<td style="width=91px;">完稅</td>
		<td style="width=91px;">過戶</td>
		<td style="width=91px;">代償</td>
		<td style="width=95px;">點交(結案)</td>
	</tr>
</table>

<div style="height:20px;"></div>
<div style="width:660px;text-align:left;">基本資料</div>
<table cellspacing=0px padding=0px style="width:660px;">
	<tr>
		<td style="background-color:#E4BEB1;width:110px;">承辦人</td>
		<td style="width:220px;" style=""><{$list.undertaker}>&nbsp;</td>
		<td style="background-color:#E4BEB1;width:110px;">地政士</td>
		<td style="width:220px;" style=""><{$list.scrivener}>&nbsp;</td>
	</tr>
	<tr style="background-color:#F8ECE9;">
		<td style="background-color:#E4BEB1;width:110px;">案件狀態</td>
		<td style="width:220px;" style="">
			<select name="caseStatus" style="width:110px" onchange="status_change()">
			<{$status}>
			</select>
		</td>
		<td style="background-color:#E4BEB1;width:110px;">簽約日期</td>
		<td style="width:220px;"><input type="text" name="signDate" value="<{$list.signdate}>"></td>
	</tr>
	<tr>
		<td style="background-color:#E4BEB1;width:110px;">保證號碼</td>
		<td style=""><{$cCertifiedId}>&nbsp;</td>
		<td style="background-color:#E4BEB1;width:110px;">專屬帳號</td>
		<td style=""><{$list.account}>&nbsp;</td>
	</tr>
	<tr style="background-color:#F8ECE9;">
		<td style="background-color:#E4BEB1;width:110px;">賣方姓名</td>
		<td><input type="text" name="owner" style="" value="<{$list.owner}>"></td>
		<td style="background-color:#E4BEB1;width:110px;">賣方 ID</td>
		<td><input type="text" name="ownerID" style="" value="<{$list.owner_id}>"></td>
	</tr>
	<tr>
		<td style="background-color:#E4BEB1;width:110px;">賣方代理人</td>
		<td colspan="3"><span title="<{$list.owner_agent_name}>"><{$list.owner_agent_name}></span><!-- <input type="text" name="owner_agent" style="" value="<{$list.owner_agent}>"> --></td>
	</tr>
	<tr style="background-color:#F8ECE9;">
		<td style="background-color:#E4BEB1;width:110px;">買方姓名</td>
		<td style="background-color:#F8ECE9;"><input type="text" name="buyer" style="" value="<{$list.buyer}>"></td>
		<td style="background-color:#E4BEB1;width:110px;">買方 ID</td>
		<td style="background-color:#F8ECE9;"><input type="text" name="buyerID" style="" value="<{$list.buyer_id}>"></td>
	</tr>
	<tr>
		<td style="background-color:#E4BEB1;width:110px;">買方代理人</td>
		<td colspan="3" style=""><span title="<{$list.buyer_agent_name}>"><{$list.buyer_agent_name}></span><!-- <input type="text" name="buyer_agent" style="" value="<{$list.buyer_agent}>"> --></td>
	</tr>
	<tr>
		<td style="background-color:#E4BEB1;width:110px;">授權對象</td>
		<td colspan="3" ><input type="text" name="buyer_authorized" value="<{$list.buyer_authorized}>"></td>
	</tr>
	<tr style="background-color:#F8ECE9;">
		<td style="background-color:#E4BEB1;width:110px;">仲介品牌</td>
		<td><{$list.brand}></td>
		<td style="background-color:#E4BEB1;width:110px;">仲介店名</td>
		<td><{$list.branch}></td>
	</tr>
	<{$realty2}>
	<{$realty3}>

	<{foreach from=$property key=key item=item}>
		<tr style="<{$colorIndex}>">
			<td style="background-color:#E4BEB1;width:110px;"><{$item.title}></td>
			<td colspan="3" style="">
			<input type="hidden" maxlength="6" name="addrZip<{$item.cItem}>" id="addrZip<{$item.cItem}>F" size="3" readonly="readonly"  value="<{$item.cZip}>"/>				
				<select name="zip_city<{$item.cItem}>" style="width:80px;" id="zip_city<{$item.cItem}>" onchange="getArea2('zip_city<{$item.cItem}>','zip_area<{$item.cItem}>','addrZip<{$item.cItem}>')">
					<{$item.cCity}>
				</select>
				<select name="zip_area<{$item.cItem}>" id="zip_area<{$item.cItem}>" style="width:80px;" onchange="getZip2('zip_area<{$item.cItem}>','addrZip<{$item.cItem}>')">
					<{$item.cArea}>
				</select>
				<input type="hidden" name="cItem[]" value="<{$item.cItem}>" >
				<input type="hidden" name="addrZip[]" value="<{$item.cZip}>" id="addrZip<{$item.cItem}>">
				<input type="text" name="cAddr[]"  style="width:330px;" value="<{$item.cAddr}>" id="cAddr<{$item.cItem}>">
			</td>
		</tr>
	<{/foreach}>
	<!-- <tr style="<{$colorIndex}>">
		<td style="background-color:#E4BEB1;width:110px;">標的物坐落</td>
		<td colspan="3" style="">
			<select name="zip_city" style="width:80px;">
			<{$addr_city}>
			</select>
			<select name="zip_area" style="width:80px;">
			<{$addr_area}>
			</select>
			<input type="hidden" name="addrZip" value="<{$list.cZip}>">
			<input type="text" name="cAddr"  style="width:330px;" value="<{$list.address}>">
		</td>
	</tr> -->
</table>

<div style="height:20px;"></div>
<div style="width:660px;text-align:left;">各期價款</div>

<table cellspacing=0px padding=0px style="width:660px;">
	<tr style="<{$colorIndex1}>">
		<td style="background-color:#E4BEB1;width:120px;">簽約款</td>
		<td style="">
			<input type="text" name="cSignMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.signmoney}>" onKeyUp="total_money()">
		</td>
		<td style="background-color:#E4BEB1;width:120px;">用印款</td>
		<td style="">
			<input type="text" name="cAffixMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.affixmoney}>" onKeyUp="total_money()">
		</td>
	</tr>
	<tr style="<{$colorIndex}>">
		<td style="background-color:#E4BEB1;width:120px;">完稅款</td>
		<td style="">
			<input type="text" name="cDutyMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.dutymoney}>" onKeyUp="total_money()">
		</td>
		<td style="background-color:#E4BEB1;width:120px;">尾款</td>
		<td style="">
			<input type="text" name="cEstimatedMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.estimatedmoney}>" onKeyUp="total_money()">
		</td>
	</tr>
	<tr style="<{$colorIndex1}>">
		<td style="background-color:#E4BEB1;width:120px;">買賣總價金</td>
		<td style="">
			<input type="text" name="cTotalMoney" class="dollars" style="background-color:#FFE4E1;color:#A0A0A0;" value="<{$list.totalmoney}>" readonly>
		</td>
		<td style="background-color:#E4BEB1;width:120px;">保證費金額</td>
		<td style="">
			<input type="text" name="cCertifiedMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.cerifiedmoney}>">
		</td>
	</tr>
</table>

<div style="height:20px;"></div>
<table style="width:660px;">
<tr>
	<td style="border:0px;width:50%;">
	代收款項(買方)
		<table cellspacing=0px padding=0px style="width:325px;">
			<tr style="<{$colorIndex}>">
				<td style="background-color:#E4BEB1;width:120px;">應付仲介費總額</td>
				<td style="">
					<input type="text" name="cRealestateMoneyBuyer" class="dollars" style="background-color:#FFFAFA;" value="<{$list.realestatemoneyBuyer}>" onKeyUp="real_money('buy')">
				</td>
			</tr>
			<tr style="<{$colorIndex1}>">
				<td style="background-color:#E4BEB1;width:120px;">先行收受仲介費</td>
				<td style="">
					<input type="text" name="cAdvanceMoneyBuyer" class="dollars" style="background-color:#FFFAFA;" value="<{$list.advancemoneyBuyer}>" onKeyUp="real_money('buy')">
				</td>
			</tr>
			<tr style="<{$colorIndex}>">
				<td style="background-color:#E4BEB1;width:120px;">應付仲介費餘額</td>
				<td style="">
					<input type="text" name="cDealMoneyBuyer" class="dollars" style="background-color:#FFE4E1;color:#A0A0A0;" value="<{$list.dealmoneyBuyer}>" readonly>
				</td>
			</tr>
			<tr style="<{$colorIndex1}>">
				<td style="background-color:#E4BEB1;width:120px;">地政士費</td>
				<td style="">
					<input type="text" name="cScrivenerMoneyBuyer" class="dollars" style="background-color:#FFFAFA;" value="<{$list.scrivenermoneyBuyer}>">
				</td>
			</tr>
		</table>
	</td>
	<td style="border:0px;width:50%;">
	代收款項(賣方)
		<table cellspacing=0px padding=0px style="width:325px;">
			<tr style="<{$colorIndex}>">
				<td style="background-color:#E4BEB1;width:120px;">應付仲介費總額</td>
				<td style="">
					<input type="text" name="cRealestateMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.realestatemoney}>" onKeyUp="real_money('owner')">
				</td>
			</tr>
			<tr style="<{$colorIndex1}>">
				<td style="background-color:#E4BEB1;width:120px;">先行收受仲介費</td>
				<td style="">
					<input type="text" name="cAdvanceMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.advancemoney}>" onKeyUp="real_money('owner')">
				</td>
			</tr>
			<tr style="<{$colorIndex}>">
				<td style="background-color:#E4BEB1;width:120px;">應付仲介費餘額</td>
				<td style="">
					<input type="text" name="cDealMoney" class="dollars" style="background-color:#FFE4E1;color:#A0A0A0;" value="<{$list.dealmoney}>" readonly>
				</td>
			</tr>
			<tr style="<{$colorIndex1}>">
				<td style="background-color:#E4BEB1;width:120px;">地政士費</td>
				<td style="">
					<input type="text" name="cScrivenerMoney" class="dollars" style="background-color:#FFFAFA;" value="<{$list.scrivenermoney}>">
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
<div style="height:20px;"></div>
<div style="width:660px;text-align:left;">帳務收支明細</div>
<table id="show1" cellspacing=0px padding=0px style="width:660px;">
	<tr>
		<td style="background-color:#E4BEB1;width:110px;">日期</td>
		<td style="background-color:#E4BEB1;width:150px;">帳款摘要</td>
		<td style="background-color:#E4BEB1;width:110px;">收入</td>
		<td style="background-color:#E4BEB1;width:90px;">支出</td>
		<td style="background-color:#E4BEB1;width:90px;">小計</td>
		<td style="background-color:#E4BEB1;width:220px;">備註</td>
	</tr>
	<{$tbl}>
	<tr>
		<td style="background-color:#E4BEB1;width:110px;">&nbsp;</td>
		<td style="background-color:#E4BEB1;width:150px;">&nbsp;</td>
		<td style="background-color:#E4BEB1;width:110px;">&nbsp;</td>
		<td style="background-color:#E4BEB1;width:90px;">&nbsp;</td>
		<td style="background-color:#E4BEB1;width:90px;">&nbsp;</td>
		<td style="background-color:#E4BEB1;width:220px;">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="text-align:right;">專戶收支餘額：</td>
		<td colspan="3" style="text-align:right;"><{$total}>&nbsp;</td>
		<td>(收入-支出)&nbsp;</td>
	</tr>
</table>
</form>
<div style="height:20px;"></div>
<{if $cSignCategory==1}>
<button id="save">更新存檔</button>&nbsp;&nbsp;
<button id="trans_build">出款建檔</button>
<{/if}>
</center>
</body>
</html>