<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
	<link rel="stylesheet" href="../css/colorbox.css" />
	<script src="../js/jquery-1.10.2.min.js"></script>
	<script src="../js/jquery.colorbox.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />
	<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
	<{include file='meta.inc.tpl'}> 		
	<script type="text/javascript">

	var count =0;
	var tmp='';

	$(document).ready(function() {
		$('#add').hide();
		$( "[name='branch']" ).combobox() ;
		$( "[name='scrivener']" ).combobox() ;
		 <{$script}>
		$( "#dialog" ).dialog({
			autoOpen: false,
			modal: true,
			minHeight:50,
			show: {
				effect: "blind",
				duration: 3000
			},
			hide: {
				effect: "explode",
				duration: 3000
			}
		});

		$('#export').click(function() {
				// $( "#dialog" ).dialog("open") ;
			
		
				var url = 'charge_report_excel2.php';
				
				$('[name="form1"]').attr('action',url);
				
				$('[name="form1"]').submit();
			

		});

		 $('#export').button( {
            icons:{
                primary: "ui-icon-document"
            }
        });
					
	});

	
		
		</script>
		<style>
			.ui-autocomplete-input {
				width:400px;
				font-size: 16px;
			}
			#center_line{
				margin:0px auto;width:500px;text-align:left;padding:10px;border:1px solid #ccc;height:300px; line-height:20px;
			}
			#dialog {
				background-image:url("/images/animated-overlay.gif") ;
				background-repeat: repeat-x;
				margin: 0px auto;
			}
		</style>
</head>
<body id="dt_example">
    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="233" height="72">&nbsp;</td>
                    <td width="753">
                    	<table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right">
                                	<h1><{include file='welcome.inc.tpl'}></h1>
                                </td>
                            </tr>
                            <tr>
                                <td width="81%" align="right">
                                	<!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> -->
                                </td>
                                <td width="14%" align="center">
                                	<h2> 登入者 <{$smarty.session.member_name}></h2>
                                </td>
                                <td width="5%" height="30" colspan="2">
                                	<h3><a href="/includes/member/logout.php">登出</a></h3>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table> 
        </div>
        <{include file='menu1.inc.tpl'}>
		<div id="dialog"></div>
		<table width="1000" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td bgcolor="#DBDBDB">
					<table width="100%" border="0" cellpadding="4" cellspacing="1">
						<tr>
							<td height="17" bgcolor="#FFFFFF">
								<div id="menu-lv2"></div>
								<br/> 
								<h3>&nbsp;</h3>
								<div id="container">
									<h1>個人業績統計表</h1>		
									<form name='form1'  method="POST">
										<table width="100%" border="0" cellpadding="4" cellspacing="0" align="center" >
											<tr style="background-color:#F8ECE9;">
												<td width="25%" ></td><td style="padding:10px;"><input type="radio" name='date_type' value="1" checked>進案時間	<input type="radio" name='date_type' value="2">履保費出款日</td><td width="25%"></td>
											</tr>
											<tr style="background-color:#F8ECE9;">
												<td></td>
												<td style="padding:10px;">
													民國<select name="start_y"><{$y}></select>年<select name="start_m"><{$m}></select>月&nbsp;~&nbsp;<select name="end_y"><{$y}></select>年<select name="end_m"><{$m}></select>月
												</td>
												<td></td>
											</tr>	
											<tr style="background-color:#F8ECE9;">
												<td></td>
												<td style="padding:10px;">案件狀態：<{html_options name="cas_status" options=$menu_status }></td>
												<td></td>
											</tr>
											<tr style="background-color:#F8ECE9;">
												<td></td>
												<td style="padding:10px;">
													仲介店：<{html_options name="branch" options=$menu_branch }>
												</td>
												<td></td>
											</tr>
											<tr style="background-color:#F8ECE9;">
												<td></td>
												<td style="padding:10px;">
													地政士：<{html_options name="scrivener" options=$menu_scrivener }>
												</td>
												<td></td>
											</tr>
											<tr >
												<td></td>
												<td style="padding:10px;" align="center">
													<input type="button" value="匯出EXCEL" id="export">
													<input type="hidden" name="report_type" value="1">
												</td>
												
												<td></td>
											</tr>
										</table>
												
									</form>

									<div id="footer" style="height:50px;">
										<p>2012 第一建築經理股份有限公司 版權所有</p>
									</div>
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