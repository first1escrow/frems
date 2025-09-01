<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<link rel="stylesheet" href="colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />

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
	
});

function go() {
	$('[name="xls"]').val('ok') ;
	$('[name="myfrom"]').submit() ;
}

function getDist() {
	var url = '/includes/report/getDist.php' ;
	var c = $('[name="qCity"]').val() ;
	
	$.post(url,{'ct':c},function (txt) {
		$('[name="qDistinct"]').html(txt) ;
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
		.ui-autocomplete-input {
			width:300px;
		}
		#dialog {
			background-image:url("/images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
		.div-class {
			padding: 20px;
			text-align: left;
			height:20px;
		}
		#btnExcel {
			/* support Safari, Chrome */
			-webkit-border-radius: 5px;
			/* support firefox */
			-moz-border-radius: 5px;
			border-radius: 5px;
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
											<div id="dialog"></div>
											
											<div>
												<form name="myfrom" method="POST">
													<h2 style="margin-left:130px;">仲介地政士開關店統計</h2>
													<center>
													
														<div class="div-class" style="width:600px;background-color:#F8ECE9;">
															<div style="width:290px;float:left;">
																身分：
																<input type="radio" id="scr" name="type" value="1">
																<label for="scr">&nbsp;地政士</label>
																<input type="radio" id="realty" name="type" value="2" checked="checked">
																<label for="realty">&nbsp;仲介</label>
															</div>
															<div style="width:290px;float:left;">
																區域：
																<select name="qCity" onchange="getDist()">
																	<option value="">全部</option>
																	<{$qCity}>
																</select>
																 　
																<select name="qDistinct">
																	<option value="">全區</option>
																</select>
															</div>															
														</div>
														<div class="div-class" style="width:600px;background-color:#E4BEB1;">
															<div style="width:290px;float:left;">
																<div style="width:120px;float:left;">
																	年度：
																	<select name="fyr" style="width:60px;">
																	<{for $i =$smarty.now|date_format:"%Y" to 2012 step -1}>
																		<{$year = ($i - 1911)}>
																		<option value="<{$year+1911}>"<{if $year == $smarty.now|date_format:"%Y"}> selected="selected"<{/if}>><{$year}></option>
																	<{/for}>
																	</select>
																</div>
																<div style="width:120px;float:left;">
																	月份：
																	<select name="fmn" style="width:60px;">
																	<{for $i = 1 to 12 }>
																		<{$month = $i|string_format:"%02d"}>
																		<option value="<{$month}>"<{if $month == $smarty.now|date_format:"%m"}> selected="selected"<{/if}>><{$month}></option>
																	<{/for}>
																	</select>
																</div>
															</div>
															<div style="width:290px;float:left;">
																<div style="width:120px;float:left;">
																	年度：
																	<select name="tyr" style="width:60px;">
																	<{for $i = $smarty.now|date_format:"%Y" to 2012 step -1}>
																		<{$year = ($i - 1911)}>
																		<option value="<{$year+1911}>"<{if $year == $smarty.now|date_format:"%Y"}> selected="selected"<{/if}>><{$year}></option>
																	<{/for}>
																	</select>
																</div>
																<div style="width:120px;float:left;">
																	月份：
																	<select name="tmn" style="width:60px;">
																	<{for $i = 1 to 12 }>
																		<{$month = $i|string_format:"%02d"}>
																		<option value="<{$month}>"<{if $month == $smarty.now|date_format:"%m"}> selected="selected"<{/if}>><{$month}></option>
																	<{/for}>
																	</select>
																</div>
															</div>
														</div>
													
														<div style="width:600px;padding-top:20px;text-align:center;">
															<input type="hidden" name="xls">
															<!--<div id="btnExcel" style="width:100px;padding:2px;border:1px solid #CCC;background-color:#CCC; lk;1">-->
																<img style="cursor:pointer;" title="excel 報表下載" src="/images/Excel_2013.png" onclick="go()">
															<!--</div>-->
														</div>
													
													</center>
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