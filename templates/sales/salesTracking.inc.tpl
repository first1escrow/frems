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
	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"300"});
	
	
	var count = 0 ;
	
	$.colorbox({
		iframe:true,
		showClose:false,
		scrolling:false,
		escKey:false,
		overlayClose:false,
		width:"400px",
		height:"300px",
		href:"/images/loading-gears-animation-3.gif"
	}) ;
	
	$.ajax({
		url: "getNewSignList.php",
		type: "POST",
		data: {s:<{$smarty.session.member_id}>},
		//data: {s:25},
		dataType: "text",
		async: true,
		success: function(txt) {
			
			var data = txt.split('＿') ;
			
			$('#scrNewList').html(data[0]) ;
			$('#realNewList').html(data[1]) ;
			
			if (count == 0) {
				count = count + 1 ;
			}
			else {
				count = 0 ;
				$.fn.colorbox.close() ;
				<{$script}>
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
			$.fn.colorbox.close() ;
			
			alert(xhr.status) ;
			alert(thrownError) ;
		}
	}) ;
		
	$.ajax({
		url: "getTrackingList.php",
		type: "POST",
		data: {s:<{$smarty.session.member_id}>},
		//data: {s:25},
		dataType: "text",
		async: true,
		success: function(txt) {
			var data = txt.split('＿') ;
			
			$('#scrList').html(data[0]) ;
			$('#realList').html(data[1]) ;
			if (count == 0) {
				count = count + 1 ;
			}
			else {
				count = 0 ;
				$.fn.colorbox.close() ;
				<{$script}>
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
			$.fn.colorbox.close() ;
			
			alert(xhr.status) ;
			alert(thrownError) ;
		}
	}) ;
	
	// enter 輸入
	$(this).keypress(function(e) {
		if (e.keyCode == 13) {
			save() ;
		}
	}) ;

	$('[name="excel"]').on('click', function() {
		
		// $("#salesForm").attr('action', 'salesSummaryExcel.php');
		$("[name='ex']").attr('value', 'ok');
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

function colorbx(url) {
	$.colorbox({href:url});
}

function redirectScr(no) {
	$("#rSales").val(no) ;
	$("#redirectSales").attr("action","/maintain/formscriveneredit.php").submit() ;	
}

function redirectreal(no) {
	$("#rSales").val(no) ;
	$("#redirectSales").attr("action","/maintain/formbranchedit.php").submit() ;	
}
</script>
<style>
#cboxClose{display:none !important;}
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
	border: 1px solid #CCC;
}
.statistics td {
	width: 8%;
	padding: 5px;
	border: 1px solid #CCC;
}

.statistics_s table{
	width: 100%
	padding-bottom:20px;
}
.statistics_s th{
	width: 5%;
	padding: 5px;
	border: 1px solid #CCC;
}
.statistics_s td {
	width: 5%;
	padding: 5px;
	border: 1px solid #CCC;
}

#scrTable {
	padding-bottom: 20px;
}
#scrTable th{
	padding: 5px;
}
#scrTable td{
	padding: 5px;
}
#realtyTable {
	padding-bottom: 20px;
}

#realtyTable th{
	padding: 5px;
}
#realtyTable td{
	padding: 5px;
}

#trackBranch th {
	border: 1px solid #CCC;
	padding: 5px;
	width: 100px;
	background-color: #E4BEB1;
}

#trackBranch td {
	border: 1px solid #CCC;
	padding: 2px;
	width: 100px;
	/*background-color: #77FFEE;*/
	text-align: center;
}

#ListTb th {
	padding: 5px;
	background-color: #FFCCCC;
}

#ListTb td {
	padding: 5px;
}
</style>
</head>
    <body id="dt_example">
		<form id="redirectSales" method="POST">
			<input type="hidden" id="rSales" name="id">
		</form>
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
										<table id="ListTb" border=1 style="width:860px;">
											<tr>
												<td valign="top" style="padding-bottom:20px;">
												
													<div style="">
														<center>
														<div style="width:400px;text-align:center;font-weight:bold;">新簽地政士未進案天數</div>
														<table style="width:400px;" border=0>
															<thead>
																<tr>
																	<th style="width:250px;">地政士</th>
																	<th>未進案天數</th>
																</tr>
															</thead>
															<tbody id="scrNewList">
															</tbody>
														</table>
														</center>
													</div>
										
												</td>
												<td valign="top" style="padding-bottom:20px;">
												
													<div style="">
														<center>
														<div style="width:400px;text-align:center;font-weight:bold;">新簽仲介店未進案天數</div>
														<table style="width:400px;" border=0>
															<thead>
																<tr>
																	<th>品牌</th>
																	<th>店名</th>
																	<th>未進案天數</th>
																</tr>
															</thead>
															<tbody id="realNewList">
															</tbody>
														</table>
														</center>
													</div>
										
												</td>
											</tr>
											<tr>
												<td valign="top">
										
													<div style="">
														<center>
														<div style="width:400px;text-align:center;font-weight:bold;">未進案地政士</div>
														<table style="width:400px;" border=0>
															<thead>
																<tr>
																	<th style="width:150px;">地政士</th>
																	<th><{$last2Month}>月件數</th>
																	<th><{$lastMonth}>月件數</th>
																</tr>
															</thead>
															<tbody id="scrList">
															</tbody>
														</table>
														</center>
													</div>
										
												</td>
												<td valign="top">
										
													<div style="">
														<center>
														<div style="width:400px;text-align:center;font-weight:bold;">未進案仲介店</div>
														<table style="width:400px;" border=0>
															<thead>
																<tr>
																	<th>品牌</th>
																	<th>店名</th>
																	<th><{$last2Month}>月件數</th>
																	<th><{$lastMonth}>月件數</th>
																</tr>
															</thead>
															<tbody id="realList">
															</tbody>
														</table>
														</center>
													</div>
										
												</td>
											</tr>
										</table>
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