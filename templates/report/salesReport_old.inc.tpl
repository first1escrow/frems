<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" href="../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />
<script src="../js/jquery-1.7.2.min.js"></script>

<script src="../js/jquery.colorbox.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$( "#subTabs" ).tabs();
	var ef = "<{$effect_type}>";

	if (ef == 1) {
		$("#e1").show();
		$("#e2").hide();
	}else{
		$("#e2").show();
		$("#e1").hide();
	}
	// $("[name='ex']").attr('value', '');
	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"300"});
	//$('input[name="sn"]').focus() ;
	 <{$script}>
	
	// enter 輸入
	$(this).keypress(function(e) {
		if (e.keyCode == 13) {
			save() ;
		}
	}) ;

	$('[name="excel"]').on('click', function() {
		
		// $("#salesForm").attr('action', 'salesSummaryExcel.php');
		$("[name='ex']").attr('value', 'ok');
		$('[name="traceXls"]').val('') ;
		$("#salesForm").submit();
	});
	$('[name="su"]').on('click', function() {
		$("[name='ex']").attr('value', '');

		if ($("[name='sales']").val() == '0') {

			alert('請選擇業務對象!!');
			return false;
		}
		$('[name="traceXls"]').val('') ;
		$("[name='excel']").show();
		$("#salesForm").submit();
	});
	
	

	//name="su"
	$('[name="excel"]').button( {
        icons:{
            primary: "ui-icon-document"
        }
    });
    $('[name="su"]').button( {
        icons:{
            primary: "ui-icon-document"
        }
    });
});
function tab2(v,name)
{
	$(".focus_page li").attr('class', '');
		
	if (v == 1) {
		$("#e1").show();
		$("#e2").hide();
	}else{
		$("#e1").hide();
		$("#e2").show();		
	}

	$("#"+name).attr('class', 'focus_end');
	
}
function colorbx(url) {
	$.colorbox({href:url});
}

function teaceXls() {
	var ss = $('[name="sales"]').val() ;
	var yy = $('[name="dateYear"]').val() ;
	var mm = $('[name="dateMonth"]').val() ;
	
	if (ss == '0') {
		$('[name="traceXls"]').val('') ;
		alert("請選擇業務對象!!") ;
		$('[name="sales"]').focus() ;
		return false ;
	}
	else {
		$('[name="traceXls"]').val('trace') ;
		//alert("ok") ;
		$("#salesForm").submit();
		//$("#tracingform").submit() ;
	}
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
.statistics table{
	width: 100%
	padding-bottom:20px;
}
.statistics th{
	width: 8%;
	padding: 5px;
	border: 1px solid #000;
}
.statistics td {
	width: 8%;
	padding: 5px;
	border: 1px solid #000;
}

.statistics_s table{
	width: 100%
	padding-bottom:20px;
}
.statistics_s th{
	width: 5%;
	padding: 5px;
	border: 1px solid #000;
}
.statistics_s td {
	width: 5%;
	padding: 5px;
	border: 1px solid #000;
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

#realtyTable {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#realtyTable th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#realtyTable td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag1 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag1 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag1 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag11 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag11 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag11 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag2 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag2 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag2 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag21 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag21 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag21 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag3 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag3 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag3 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag31 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag31 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag31 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag4 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag4 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag4 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

#tag41 {
	margin-bottom: 20px;
	padding:5px;
	background-color:#FFFFFF;
}
#tag41 th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
}
#tag41 td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
}

.show{
	background-color:  #FFFF00;
}

#subTabs-1 div {
	margin-bottom:10px;
}

.xxx-page {
	margin:0 0 0px 0;
}
.xxx-page .focus_menu {
	text-align:center;
}
.focus_page {
	vertical-align: top;
	display:inline-block;
}
.focus_page li {
	float:left;
	margin: 0 3px 0 3px;
}
.focus_page li a {
	color: #b48400;
	font-family: Verdana;
	font-size: 16px;
	font-weight: bold;
	line-height: 20px;
	background-color: #FFFFFF;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #DDDDDD;
}
.focus_page li a:hover {
	color: #FFFFFF;
	font-size:16px;
	background-color: #FFFF96;
	border: 1px solid #FFFF96;
}
.focus_page li.focus_end a {
	color: #FFFFFF;
	font-family: Verdana;
	font-size: 16px;
	font-weight: bold;
	line-height: 20px;
	background-color: #FFFF96;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #FFFF96;
}
.category{
	font-size: 16px;
	font-weight: bold;
	line-height: 20px;
}
</style>
</head>
    <body id="dt_example">
        <form action="/calendar/calendar.php" target="_blank"></form>
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
										<h3></h3>
										<div id="container">

										<div style="padding-bottom:20px;">
										<h1>績效一覽表</h1>
											<form method="POST" id="salesForm" >
												業務： <{html_options name=sales options=$menu_sales selected=$sales}>
												年度：
													<select name="dateYear" style="width:50px;"><{$y}></select>
												月份：
													<select name="dateMonth" style="width:50px;"><{$m}></select>
													
												<input type="button" name="su" value="查詢" >
												<input type="hidden" name="ex">
												<input type="hidden" name="traceXls">
												<input type="button" value="匯出Excel" name="excel"></h2>
											</form>
											<form method="POST" id="xls"> 
												
											</form>
										</div>	


<div id="subTabs">
	<ul>
		<li><a href="#subTabs-1">業績一覽表</a></li>
		<li><a href="#subTabs-2">簽約數/達成率</a></li>
		<li><a href="#subTabs-3">進件量/成長率</a></li>
		<li><a href="#subTabs-4">使用量/使用率</a></li>
		<li><a href="#subTabs-5">貢獻率</a></li>
		<li><a href="#subTabs-7">有效率</a></li>
		<li><a href="#subTabs-6">未進案統計</a></li>
	</ul>
	
	<div id="subTabs-1">
		<h1>本季考核評分 <span style="font-weight:bold;color:#FF0000;font-size:24pt;"><{$grade}></span> 分</h1>
		<div>
			達成率：
			<{if $season1[$sess].target > 100}>
				<{$season1[$sess].target = 100}>
			<{/if}>
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].target}></span>
			%
			、
			得分：
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].target * 0.4}></span>
			分
		</div>
		
		<div>
			成長率：
			<{if $season1[$sess].group > 100}>
				<{$season1[$sess].group = 100}>
			<{/if}>
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].group}></span>
			%
			、
			得分：
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].group * 0.3}></span>
			分
		</div>
		
		<div>
			使用率：
			<{if $season1[$sess].use > 100}>
				<{$season1[$sess].use = 100}>
			<{/if}>
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].use}></span>
			%
			、
			得分：
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].use * 0.2}></span>
			分
		</div>
		
		<div>
			貢獻率：
			<{if $season1[$sess].contribution > 100}>
				<{$season1[$sess].contribution = 100}>
			<{/if}>
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].contribution}></span>
			%
			、
			得分：
			<span style="font-weight:bold;color:#000088;"><{$season1[$sess].contribution * 0.1}></span>
			分
		</div>
		
		<div style="clear:both;"></div>
	</div>
	
	<div id="subTabs-2">
		<div id="scrTable">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<th width="8%">序號</th>
						<th width="16%">建檔日</th>
						<th width="15%">地政士</th>
						<th width="30%">事務所名稱</th>
						<th width="12%">區域</th>
						<!--<th>備註</th>-->
					</tr>
					<{foreach from=$Scrivener key=key item=item}>
					<tr>
						<td><{$item.no}></td>
						<td><{$item.sSignDate}></td>
						<td><{$item.sName}></td>
						<td><{$item.sOffice}></td>
						<td><{$item.city}><{$item.area}></td>
						<!--<td><{$item.sRemark4}></td>-->
					</tr>
					<{/foreach}>
					
					<{if $ScrivenerCount == 0}>
					<tr><td colspan="5">無資料</td></tr>
					<{/if}>
				</table>
		</div>
									
		<div id="realtyTable">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<th width="8%">序號</th>
					<th width="16%">建檔日</th>
					<th width="15%">品牌</th>
					<th width="15%">加盟店</th>
					<th width="30%">仲介店名稱</th>
					<th width="12%">區域</th>
					<!--<th>備註</th>-->
					
				</tr>
				<{foreach from=$Branch key=key item=item}>
				<tr>
					<td><{$item.no}></td>
					<td><{$item.sSignDate}></td>
					
					<td><{$item.brand}></td>
					<td><{$item.bStore}></td>
					<td><{$item.bName}></td>
					
					<td><{$item.city}><{$item.area}></td>
					<!--<td><{$item.bCashierOrderMemo}></td>-->
				</tr>
				<{/foreach}>
				<{if $BranchCount == 0}>
					<tr><td colspan="6">無資料</td></tr>
				<{/if}>
			</table>
		</div>
		<div id="tag1">
			<h2><{$now_year}>年度各月份簽約數/達成率(<font color="red">本月份達成率：<{$target}>%</font>)</h2>
			<table cellspacing="0" cellpadding="0" class="statistics">
				<thead>
					<tr>
						<th>月份</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						<th>五</th>
						<th>六</th>
						<th>七</th>
						<th>八</th>
						<th>九</th>
						<th>十</th>
						<th>十一</th>
						<th>十二</th>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td>簽約數</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>"><{$item.targetcount}></td>
					
					<{/foreach}>
				</tr>
				<tr style="background-color:#F0FFF0;">
					<td>達成率</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>">
						
							<{$item.target}>%
						</td>
					
					<{/foreach}>
				</tr>
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>
		<div id="tag11">
			<h2><{$now_year}>年度各季簽約數/達成率(<font color="red">本季達成率：<{$showseason.target}>%</font>)</h2>
			<table cellspacing="0" cellpadding="0"  class="statistics_s">
				<thead>
					<tr>
						<th>季</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						
					</tr>
				</thead>
				<tbody>
				<!-- <tr>
					<td>簽約數</td>
					<{foreach from=$season1 key=key item=item}>
						

						<td class="<{$item.class}>"><{$item.targetcount}></td>
					
					<{/foreach}>
				</tr> -->
				<tr style="background-color:#F0FFF0;">
					<td>達成率</td>
					<{foreach from=$season2 key=key item=item}>
					
						<td class="<{$item.class}>">
							<{if $item.target > 100}>100%
							<{else}><{$item.target}>%<{/if}>
						</td>
					
					<{/foreach}>
				</tr>	
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>		
	</div>
	
	<div id="subTabs-3">
		<div id="tag2">
			<h2><{$now_year}>年度各月份進件量/成長率(<font color="red">本月份成長率：<{$group}>%</font>)</h2>
			<table cellspacing="0" cellpadding="0"  class="statistics">
				<thead>
					<tr>
						<th>月份</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						<th>五</th>
						<th>六</th>
						<th>七</th>
						<th>八</th>
						<th>九</th>
						<th>十</th>
						<th>十一</th>
						<th>十二</th>
					</tr>
				</thead>
				<tbody>
				<{if $sales != 0}>
				<tr>
						<td style="font-size:10pt;">進件量(台屋)</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>"><{$item.twcount}></td>
					
					<{/foreach}>
				</tr>
				
					<tr>
						<td style="font-size:10pt;">進件量(他牌)</td>
						<{foreach from=$summary1 key=key item=item}>
						
							<td class="<{$item.class}>"><{$item.Untw}></td>
						
						<{/foreach}>
					</tr>
					<tr>
							<td style="font-size:10pt;">進件量(非仲介成交)</td>
						<{foreach from=$summary1 key=key item=item}>
						
							<td class="<{$item.class}>"><{$item.scrivener}></td>
						
						<{/foreach}>
					</tr>
				
				<!-- <tr>
						<td style="font-size:10pt;">進件量(非台屋)</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>"><{$item.othercount}></td>
					
					<{/foreach}>
				</tr> -->
				
				<{else}>
				<tr>
						<td style="font-size:10pt;">進件量</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>"><{$item.groupcount}></td>
					
					<{/foreach}>
				</tr>
				<{/if}>
				<tr style="background-color:#F0FFF0;">
					<td>成長率</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>" >
							<{if $now_month < $key && $now_check != 1}>
								0%
							<{else}>
								<{$item.group}>%
							<{/if}>
						
							
						</td>
					
					<{/foreach}>
				</tr>	
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>
		<div id="tag21">
			<h2><{$now_year}>年度各季進件量/成長率(<font color="red">本季成長率：<{$showseason.group}>%</font>)</h2>
			<table cellspacing="0" cellpadding="0"  class="statistics_s">
				<thead>
					<tr>
						<th>季</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						
					</tr>
				</thead>
				<tbody>
				<!-- <tr>
					<td>進件量(台屋)</td>
					<{foreach from=$season1 key=key item=item}>
					
						<td class="<{$item.class}>"><{$item.twcount}></td>
					
					<{/foreach}>
				</tr>
				<tr>
					<td>進件量(非台屋)</td>
					<{foreach from=$season1 key=key item=item}>
						<td class="<{$item.class}>"><{$item.othercount}></td>
					
					<{/foreach}>
				</tr> -->
				<tr style="background-color:#F0FFF0;">
					<td>成長率</td>
					<{foreach from=$season2 key=key item=item}>
					
						<td class="<{$item.class}>">
							<{if $item.group > 100}>100%
							<{else}><{$item.group}>%<{/if}>
						</td>
					
					<{/foreach}>
				</tr>	
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>
	</div>
	
	<div id="subTabs-4">
		<div id="tag3">
			<h2><{$now_year}>年度各月份使用量/使用率<!-- (<font color="red">本月份使用率：<{$use}>%</font>) --></h2>
			<table cellspacing="0" cellpadding="0"  class="statistics">
				<thead>
					<tr>
						<th>月份</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						<th>五</th>
						<th>六</th>
						<th>七</th>
						<th>八</th>
						<th>九</th>
						<th>十</th>
						<th>十一</th>
						<th>十二</th>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td>使用量</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>"><{$item.usecount}></td>
					
					<{/foreach}>
				</tr>
			
			<!-- 	<tr style="background-color:#F0FFF0;">
					<td>使用率</td>
					<{foreach from=$summary1 key=key item=item}>
					
						<td class="<{$item.class}>">
							<{if $now_month < $key && $now_check != 1}>
								0%
							<{else}>
								<{$item.use}>%
							<{/if}>
							
						</td>
					
					<{/foreach}>
				</tr>	 -->
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>
		<div id="tag31">
			<h2><{$now_year}>年度各季使用量/使用率(<font color="red">本季使用率：<{$showseason.use}>%</font>)</h2>
			<table cellspacing="0" cellpadding="0"  class="statistics_s">
				<thead>
					<tr>
						<th>季</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						
					</tr>
				</thead>
				<tbody>
				<!-- <tr>
					<td>使用量</td>
					<{foreach from=$season1 key=key item=item}>
					
						<td class="<{$item.class}>"><{$item.usecount}></td>
					
					<{/foreach}>
				</tr> -->
				
				<tr style="background-color:#F0FFF0;">
					<td>使用率</td>
					<{foreach from=$season2 key=key item=item}>
					
						<td class="<{$item.class}>">
							<{if $item.use > 100}>100%
							<{else}><{$item.use}>%<{/if}>
						</td>
					
					<{/foreach}>
				</tr>	
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>
	</div>
	
	<div id="subTabs-5">
		<div id="tag4">
			<h2><{$now_year}>年度各月份貢獻率(<font color="red">本月份貢獻率：<{$contribution}>%</font>)</h2>
			<table cellspacing="0" cellpadding="0" class="statistics">
				<thead>
					<tr>
						<th>月份</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						<th>五</th>
						<th>六</th>
						
					</tr>
				</thead>
				<tbody>
				<tr>
					<td>保證費<{$i = 1}></td>
					<{foreach from=$summary1 key=key item=item}>
						<{if $i < 7}>
							<td id="<{$i++}>"><{$item.crtifiedMoney|number_format}></td>
						<{/if}>
					<{/foreach}>
				</tr>
				<tr>
					<td>回饋金<{$i = 1}></td>
					<{foreach from=$summary1 key=key item=item}>
						<{if $i < 7}>
						<td id="<{$i++}>"><{$item.feedBackMoney|number_format}></td>
						<{/if}>
					<{/foreach}>
				</tr>
				<tr style="background-color:#F0FFF0;">
					<td>貢獻率</td>
					<{$i = 1}>
					<{foreach from=$summary1 key=key item=item}>
						<{if $i < 7}>
						<td id="<{$i++}>" class="<{$item.class}>">
							<{if $now_month < $key && $now_check != 1}>
								0%
							<{else}>
								<{$item.contribution}>%
							<{/if}>
							
						</td>
						<{/if}>
						
					<{/foreach}>
				</tr>
				<tr><td colspan="7"></td></tr>	
				<tr>
					<th>月份</th>
					<th>七</th>
					<th>八</th>
					<th>九</th>
					<th>十</th>
					<th>十一</th>
					<th>十二</th>
				</tr>
				<tr>
					<td>保證費<{$i = 1}></td>
					<{foreach from=$summary1 key=key item=item}>
						<{if $i > 6}>
							<td><{$item.crtifiedMoney|number_format}></td>
						<{/if}>
						<div id="<{$i++}>"></div>
					<{/foreach}>
				</tr>
				<tr>
					<td>回饋金<{$i = 1}></td>
					<{foreach from=$summary1 key=key item=item}>
						<{if $i > 6}>
						<td><{$item.feedBackMoney|number_format}></td>
						<{/if}>
						<div id="<{$i++}>"></div>
					<{/foreach}>
				</tr>
				<tr style="background-color:#F0FFF0;">
					<td>貢獻率</td>
					<{$i = 1}>
					<{foreach from=$summary1 key=key item=item}>
						<{if $i > 6}>
						<td class="<{$item.class}>" id="<{$now_check}>">
							<{if $now_month < $key && $now_check != 1}>
								0%
							<{else}>
								<{$item.contribution}>%
							<{/if}>
							
						</td>
						
						<{/if}>
						<div id="<{$i++}>"></div>
					<{/foreach}>
				</tr>
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>
		<div id="tag41">
			<h2><{$now_year}>年度各季貢獻率(<font color="red">本季貢獻率：<{$showseason.contribution}>%</font>)</h2>
			<table cellspacing="0" cellpadding="0"  class="statistics_s">
				<thead>
					<tr>
						<th>季</th>
						<th>一</th>
						<th>二</th>
						<th>三</th>
						<th>四</th>
						
					</tr>
				</thead>
				<tbody>
				
				<tr style="background-color:#F0FFF0;">
					<td>貢獻率</td>
					<{foreach from=$season2 key=key item=item}>
					
						<td class="<{$item.class}>">
							<{if $item.contribution > 100}>100%
							<{else}><{$item.contribution}>%<{/if}>
						</td>
					
					<{/foreach}>
				</tr>	
				</tbody>
			</table>
			<div style="height:20px;"></div>
		</div>
	</div>

	<div id="subTabs-6">
		<div id="xlsdownload" style="cursor:pointer;" onclick="teaceXls()"><img src="/images/Excel_2013.png" title="excel 報表下載">
		</div>
	</div>
	<div id="subTabs-7">
		
			<ul class="focus_page">
				<li class="focus_end" onclick="tab2(<{$effect_type}>,'em1')" id="em1"><a href="#">正確值</a></li>
				<li onclick="tab2('1','em2')" id="em2"><a href="#" >算法一</a></li>
				<li onclick="tab2('2','em3')" id="em3"><a href="#" >算法二</a></li>
			</ul>
			<br>(算法一:規定日期前;算法二:規定日期後)
			<div id="e1">
				<h1>有效率 <span style="font-weight:bold;color:#FF0000;font-size:24pt;"><{$eff1.effective}></span> % (有效率 = B / A%)</h1>
				<div id="tag41">
					<table cellspacing="0" cellpadding="0"  class="statistics_s">
						<tr>
							<th>
								已特約地政士與仲介<br>(A、<{$eff1.range_start}>~<{$eff1.range_end}>)
							</th>
							<th>該進案數(B、<{$eff1.range_start}>~<{$eff1.range_end2}>)</th>
								
							
						</tr>
						<tr>
							<td><{$eff1.efftotal}></td>
							<td><{$eff1.effcase}></td>
							
						</tr>
						
					</table>
				</div>
				<div id="tag41">
					<h2>有進案</h2>
					<div id="scrTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">地政士</th>
								<th width="30%">事務所名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
							</tr>
							 <{assign var='cc' value=1}> 
							<{foreach from=$eff1.data['scrcase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								<td><{$item.sName}></td>
								<td><{$item.sOffice}></td>
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.sRemark4}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
							<tr><td colspan="5">無資料</td></tr>
							<{/if}>
							
						</table>
					</div>
					<div id="realtyTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">品牌</th>
								<th width="15%">加盟店</th>
								<th width="30%">仲介店名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
								
							</tr>
							<{assign var='cc' value=1}>
							<{foreach from=$eff1.data['branchcase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								
								<td><{$item.brand}></td>
								<td><{$item.bStore}></td>
								<td><{$item.bName}></td>
								
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.bCashierOrderMemo}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
								<tr><td colspan="6">無資料</td></tr>
							<{/if}>
						</table>
					</div>
				</div>
				<div id="tag41">
					<h2>未進案</h2>
					<div id="scrTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">地政士</th>
								<th width="30%">事務所名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
							</tr>
							<{assign var='cc' value=1}>
							<{foreach from=$eff1.data['scrnocase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								<td><{$item.sName}></td>
								<td><{$item.sOffice}></td>
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.sRemark4}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
							<tr><td colspan="5">無資料</td></tr>
							<{/if}>
							
						</table>
					</div>
					<div id="realtyTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">品牌</th>
								<th width="15%">加盟店</th>
								<th width="30%">仲介店名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
								
							</tr>
							<{assign var='cc' value=1}>
							<{foreach from=$eff1.data['branchnocase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								
								<td><{$item.brand}></td>
								<td><{$item.bStore}></td>
								<td><{$item.bName}></td>
								
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.bCashierOrderMemo}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
								<tr><td colspan="6">無資料</td></tr>
							<{/if}>
						</table>
					</div>
				</div>
			</div>
			<div id="e2">
				<h1>有效率 <span style="font-weight:bold;color:#FF0000;font-size:24pt;"><{$eff2.effective}></span> % (有效率 = B / A%)</h1>
				<div id="tag41">
					<table cellspacing="0" cellpadding="0"  class="statistics_s">
						<tr>
							<th>
								已特約地政士與仲介<br>(A、120)
							</th>
							<th>
								該進案數(B、<{$eff2.range_start}>~<{$eff2.range_end}>)</th>
							
						</tr>
						<tr>
							<td><{$eff2.efftotal}></td>
							<td><{$eff2.effcase}></td>
							
						</tr>
						
					</table>
				</div>
				<div id="tag41">
					<h2>有進案</h2>
					<div id="scrTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">地政士</th>
								<th width="30%">事務所名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
							</tr>
							 <{assign var='cc' value=1}> 
							<{foreach from=$eff2.data['scrcase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								<td><{$item.sName}></td>
								<td><{$item.sOffice}></td>
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.sRemark4}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
							<tr><td colspan="5">無資料</td></tr>
							<{/if}>
							
						</table>
					</div>
					<div id="realtyTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">品牌</th>
								<th width="15%">加盟店</th>
								<th width="30%">仲介店名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
								
							</tr>
							<{assign var='cc' value=1}>
							<{foreach from=$eff2.data['branchcase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								
								<td><{$item.brand}></td>
								<td><{$item.bStore}></td>
								<td><{$item.bName}></td>
								
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.bCashierOrderMemo}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
								<tr><td colspan="6">無資料</td></tr>
							<{/if}>
						</table>
					</div>
				</div>
				<div id="tag41">
					<h2>未進案</h2>
					<div id="scrTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">地政士</th>
								<th width="30%">事務所名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
							</tr>
							<{assign var='cc' value=1}>
							<{foreach from=$eff2.data['scrnocase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								<td><{$item.sName}></td>
								<td><{$item.sOffice}></td>
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.sRemark4}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
							<tr><td colspan="5">無資料</td></tr>
							<{/if}>
							
						</table>
					</div>
					<div id="realtyTable">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th width="8%">序號</th>
								<th width="16%">建檔日</th>
								<th width="15%">品牌</th>
								<th width="15%">加盟店</th>
								<th width="30%">仲介店名稱</th>
								<th width="12%">區域</th>
								<!--<th>備註</th>-->
								
							</tr>
							<{assign var='cc' value=1}>
							<{foreach from=$eff2.data['branchnocase'] key=key item=item}>
							<tr>
								<td><{$cc++}></td>
								<td><{$item.sSignDate}></td>
								
								<td><{$item.brand}></td>
								<td><{$item.bStore}></td>
								<td><{$item.bName}></td>
								
								<td><{$item.city}><{$item.area}></td>
								<!--<td><{$item.bCashierOrderMemo}></td>-->
							</tr>
							<{/foreach}>
							<{if $cc == 1}>
								<tr><td colspan="6">無資料</td></tr>
							<{/if}>
						</table>
					</div>
				</div>
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