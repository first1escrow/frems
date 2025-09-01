<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
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
            }) ;
			
			function sendDate() {
				$('#myform').submit() ;
			}
			
			function contract(cid) {
				$('form[name="form_edit"] input[name="id"]').val(cid) ;
				var _cid = $('form[name="form_edit"] input[name="id"]').val() ;
				//alert(_cid) ;
				$('form[name="form_edit"]').submit() ;
			}
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
		
		#tbList th {
			width: 100px;
			padding: 10px;
			background-color: #E4BEB1;
		}
		
		#tbList td {
			width: 100px;
			padding: 10px;
			text-align: center;
		}
		</style>
    </head>
    <body id="dt_example">
		<form name="form_edit" method="POST" action="/escrow/formbuyowneredit.php">
			<input type="hidden" name="id" value='' />
		</form>
        <form name="excel_out" method="POST">
			<input type="hidden" name="fds">
			<input type="hidden" name="fde">
			<input type="hidden" name="peo">
			<input type="hidden" name="exp">
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{if $checkRemind != 1}>
            <{include file='menu1.inc.tpl'}>
            <{/if}>
			<ul id="menu">
			<div id="dialog"></div>
			</ul>
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
										
											<form method="POST" id="myform">
											<div>
												<!-- <select name="listDate" onchange="sendDate()">
												<{$thisYear = $smarty.now|date_format:"%Y"}>
												<{for $i = 0 to $thisYear - 2011}>
												<option value="<{$thisYear - $i}>"<{if $yr == $thisYear - $i}> selected="selected"<{/if}>><{$thisYear - $i - 1911}></option>
												<{/for}>
												</select>
												年度 -->
											</div>
											<div style="height:20px;"></div>
											<div>
												<center>
												<table border="0" id="tbList">
													<thead>
														<tr>
															<th>項次</th>
															<th>保證號碼</th>
															<th>進案日期</th>
															<th>簽約日期</th>
															<th>案件狀態</th>
															<th>履保金額</th>
															<th>出款日期</th>
															<th>負責經辦</th>
														</tr>
													<thead>
													<tbody>
														<{$tbl}>
													</tbody>
												</table>
												</center>
											</div>
											</form>
											
										</div>
                                    </td>
                            </table>
                        </td>
                    </tr>
                </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>