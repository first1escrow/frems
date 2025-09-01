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
		
		$("[name='ex']").attr('value', 'ok');
		$("#salesForm").submit();
	});

	$('[name="excel2"]').on('click', function() {
		
		$("[name='ex']").attr('value', 'ok2');
		$("#salesForm").submit();
	});
	

	$('[name="su"]').on('click', function() {
		$("[name='ex']").attr('value', '');

		if ($("[name='sales']").val() == '0') {

			alert('請選擇業務');
			return false;
		}
		$("[name='excel']").show();
		$("#salesForm").submit();
	});
	//name="su"
	$('[name="excel"]').button( {
        icons:{
            primary: "ui-icon-document"
        }
    });
    $('[name="excel2"]').button( {
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

function colorbx(url) {
	$.colorbox({href:url});
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
										<h1>電子發票一覽表</h1>
											<font color="red">※電子發票104年09月開始使用，故104年為4個月</font><br>
											<font color="red">※月平均數=加總數/當年已開立發票的月數</font><br>
											<font color="red">※年平均數=月平均數*12</font>
											<br><br>
											<form method="POST" id="salesForm" >
												
												年度：
													<select name="dateYear" style="width:50px;"><{$y}></select>
												月份：
													<select name="dateMonth" style="width:50px;"><{$m}></select>
													
												<input type="button" name="su" value="查詢" >
												<input type="hidden" name="ex">
												<input type="button" value="電子發票統計表EXCEL" name="excel">
												
												<input type="button" value="電子發票統計表EXCEL(2)" name="excel2">
												
											</form>
											<form method="POST" id="xls"> 
												
											</form>
											
										</div>	


<div id="subTabs">
	<ul>
		<li><a href="#subTabs-1">本月一覽表</a></li>
		<li><a href="#subTabs-2">B2B</a></li>
		<li><a href="#subTabs-3">B2C</a></li>
		<li><a href="#subTabs-4">全部</a></li>
		
	</ul>
	

	<div id="subTabs-1">
		<div id="tag2">

			<h1><{$search_y}>年<{$search_m}>月一覽表</h1>
			<h2>數量統計</h2>
				<table cellpadding="0" cellspacing="0" class="statistics">
					<tr>
						<th>&nbsp;</th>	
						<th>本月B2B</th>
						<th>本月B2C</th>
						<th>本月總計</th>
						<th>B2B月平均</th>
						<th>B2C月平均</th>
						<th>月平均總計</th>
						<th>B2B平均年張數</th>
						<th>B2C平均年張數</th>
						<th>年平均總計</th>
					</tr>

					<tr>
						<th>發票開立張數</th>
						<td><span style="font-weight:bold;color:#000088;">
							<{$data[$search_y][$search_m]['三聯']['total']}>							
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">	
							<{$data[$search_y][$search_m]['二聯']['total']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$data[$search_y][$search_m]['total']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_data['total']['三聯']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_data['total']['二聯']}>
						</span></td>

						<td><span style="font-weight:bold;color:#000088;">
							<{$month_data['total']['average']}>							
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">	
							<{$year_data['total']['三聯']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_data['total']['二聯']}>
						</span></td>

						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_data['total']['average']}>
						</span></td>
					</tr>

					<tr>
						<th>列印紙本張數</th>
						<td><span style="font-weight:bold;color:#000088;">
							<{$data[$search_y][$search_m]['三聯']['printY']}>							
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">	
							<{$data[$search_y][$search_m]['二聯']['printY']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$data[$search_y][$search_m]['printY']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_data['printY']['三聯']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_data['printY']['二聯']}>
						</span></td>

						<td><span style="font-weight:bold;color:#000088;">
							<{$month_data['printY']['average']}>							
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">	
							<{$year_data['printY']['三聯']}>
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_data['printY']['二聯']}>
						</span></td>

						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_data['printY']['average']}>
						</span></td>
						
					</tr>
					

					
				</table>
			<h2>各佔比</h2>
				<table cellpadding="0" cellspacing="0" class="statistics">
					<tr>
						<th>&nbsp;</th>	
						<th>本月B2B</th>
						<th>本月B2C</th>
						<th>B2B月平均</th>
						<th>B2C月平均</th>
						<th>B2B平均年張數</th>
						<th>B2C平均年張數</th>
						
					</tr>

					<tr>
						<th>發票開立張數</th>
						<td><span style="font-weight:bold;color:#000088;">
							<{$month_per['total']['三聯']['total']}>%							
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">	
							<{$month_per['total']['二聯']['total']}>%
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_per['total']['三聯']['average']}>%
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_per['total']['二聯']['average']}>%
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_per['total']['三聯']}>%
						</span></td>

						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_per['total']['二聯']}>%
						</span></td>
					</tr>

					<tr>
						<th>列印紙本張數</th>
						<td><span style="font-weight:bold;color:#000088;">
							<{$month_per['printY']['三聯']['total']}>%							
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">	
							<{$month_per['printY']['二聯']['total']}>%
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_per['printY']['三聯']['average']}>%
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$month_per['printY']['二聯']['average']}>%
						</span></td>
						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_per['printY']['三聯']}>%
						</span></td>

						<td><span style="font-weight:bold;color:#000088;">		
							<{$year_per['printY']['二聯']}>%
						</span></td>
					</tr>
					<tr>
						<th>列印紙本總張數</th>
						<td colspan="2">
							<span style="font-weight:bold;color:#000088;">
							<{$arr['A']}>%</span>
						</td>
						<td colspan="2">
							<span style="font-weight:bold;color:#000088;">
							<{$arr['B']}>%</span>
						</td>
						<td colspan="2">
							<span style="font-weight:bold;color:#000088;">
							<{$arr['C']}>%</span>
						</td>
					</tr>

					
				</table>
		</div>
		


		<div style="clear:both;"></div>


	</div>
	
	<div id="subTabs-2">
		<h1>B2B一覽表</h1>
		<div id="tag1">
			<h2>數量統計</h2>

			<table cellpadding="0" cellspacing="0" class="statistics">
				<tr>
					<th>月份</th>
					<{foreach from=$data[$search_y] key=key item=item}>
						<th><{$key}></th>
					<{/foreach}>
					<th>總計</th>
					<th>月平均</th>
					<th>年平均總計</th>
				</tr>
				<tr>
					<td>發票開立張數</td>
					<{foreach from=$data[$search_y] key=key item=item}>
						
						<td class="<{$item.css}>"><{$item['三聯']['total']}></td>
						
					<{/foreach}>
					<td><{$data2[$search_y]['三聯']['total']}></td>
					<td><{$month_data['total']['三聯']}></td>
					<td><{$year_data['total']['三聯']}></td>
					
				</tr>
				<tr>
					<td>列印紙本張數</td>
					<{foreach from=$data[$search_y] key=key item=item}>
						<td class="<{$item.css}>"><{$item['三聯']['printY']}></td>
					<{/foreach}>
					<td><{$data2[$search_y]['三聯']['printY']}></td>
					<td><{$month_data['printY']['三聯']}></td>
					<td><{$year_data['printY']['三聯']}></td>

				</tr>
				
			</table>
		</div>
		<div id="tag1">
			<h2>佔比</h2>
			<table cellpadding="0" cellspacing="0" class="statistics">
				<tr>
					<th>月份</th>
					<{foreach from=$data[$search_y] key=key item=item}>
						<th><{$key}></th>
					<{/foreach}>
					
				</tr>
				<tr>
					<td>發票開立張數</td>
					<{foreach from=$b2b['total'] key=key item=item}>
						<td class="<{$item.css}>">
							
							<{$item.count}>%
						</td>
					<{/foreach}>
					
				</tr>
				<tr>
					<td>列印紙本張數</td>
					<{foreach from=$b2b['printY'] key=key item=item}>
						<td class="<{$item.css}>">
							<{$item.count}>%
						</td>
					<{/foreach}>
					
					
				</tr>
				
			</table>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div id="subTabs-3">
		
		<div id="tag1">
			<h2>數量統計</h2>

			<table cellpadding="0" cellspacing="0" class="statistics">
				<tr>
					<th>月份</th>
					<{foreach from=$data[$search_y] key=key item=item}>
						<th><{$key}></th>
					<{/foreach}>
					<th>總計</th>
					<th>月平均</th>
					<th>年平均總計</th>
				</tr>
				<tr>
					<td>發票開立張數</td>
					<{foreach from=$data[$search_y] key=key item=item}>
						
						<td class="<{$item.css}>"><{$item['二聯']['total']}></td>
						
					<{/foreach}>
					<td><{$data2[$search_y]['二聯']['total']}></td>
					<td><{$month_data['total']['二聯']}></td>
					<td><{$year_data['total']['二聯']}></td>
					
				</tr>
				<tr>
					<td>列印紙本張數</td>
					<{foreach from=$data[$search_y] key=key item=item}>
						<td class="<{$item.css}>"><{$item['二聯']['printY']}></td>
					<{/foreach}>
					<td><{$data2[$search_y]['二聯']['printY']}></td>
					<td><{$month_data['printY']['二聯']}></td>
					<td><{$year_data['printY']['二聯']}></td>

				</tr>
				
			</table>
		</div>
		<div id="tag1">
			<h2>佔比</h2>
			<table cellpadding="0" cellspacing="0" class="statistics">
				<tr>
					<th>月份</th>
					<{foreach from=$data[$search_y] key=key item=item}>
						<th><{$key}></th>
					<{/foreach}>
					
				</tr>
				<tr>
					<td>發票開立張數</td>
					<{foreach from=$b2c['total'] key=key item=item}>
						<td class="<{$item.css}>">
							
							<{$item.count}>%
						</td>
					<{/foreach}>
					
				</tr>
				<tr>
					<td>列印紙本張數</td>
					<{foreach from=$b2c['printY'] key=key item=item}>
						<td class="<{$item.css}>">
							<{$item.count}>%
						</td>
					<{/foreach}>
					
					
				</tr>
				
			</table>
		</div>
		<div style="clear:both;"></div>
	</div>
		<div id="subTabs-4">
		
		<div id="tag1">
			<h2>數量統計</h2>

			<table cellpadding="0" cellspacing="0" class="statistics">
				<tr>
					<th>月份</th>
					<{foreach from=$data[$search_y] key=key item=item}>
						<th><{$key}></th>
					<{/foreach}>
					<th>總計</th>
					<th>月平均</th>
					<th>年平均總計</th>
				</tr>
				<tr>
					<td>發票開立張數</td>
					<{foreach from=$data[$search_y] key=key item=item}>
						
						<td class="<{$item.css}>"><{$item.total}></td>
						
					<{/foreach}>
					<td><{$data2[$search_y].total}></td>
					<td><{$month_data['total']['average']}></td>
					<td><{$year_data['total']['average']}></td>
					
				</tr>
				<tr>
					<td>列印紙本張數</td>
					<{foreach from=$data[$search_y] key=key item=item}>
						<td class="<{$item.css}>"><{$item.printY}></td>
					<{/foreach}>
					<td><{$data2[$search_y].printY}></td>
					<td><{$month_data['printY']['average']}></td>
					<td><{$year_data['printY']['average']}></td>

				</tr>
				
			</table>
		</div>
		<div id="tag1">
			<h2>佔比</h2>
			<table cellpadding="0" cellspacing="0" class="statistics">
				<tr>
					<th>月份</th>
					<{foreach from=$data[$search_y] key=key item=item}>
						<th><{$key}></th>
					<{/foreach}>
					
				</tr>
				<tr>
					<td>發票開立張數</td>
					<{foreach from=$all['total'] key=key item=item}>
						<td class="<{$item.css}>">
							
							<{$item.count}>%
						</td>
					<{/foreach}>
					
				</tr>
				<tr>
					<td>列印紙本張數</td>
					<{foreach from=$all['printY'] key=key item=item}>
						<td class="<{$item.css}>">
							<{$item.count}>%
						</td>
					<{/foreach}>
					
					
				</tr>
				
			</table>
		</div>
		<div style="clear:both;"></div>
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