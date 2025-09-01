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
				$(".ui-dialog-titlebar").hide() ;
				
				$('#export').live('click', function () {
                    $('[name="fds"]').val($('[name="_fds"]').val()) ;
                    $('[name="fde"]').val($('[name="_fde"]').val()) ;
					$('[name="bke"]').val($('[name="bank_option"]').val()) ;
					$('[name="exp"]').val('ok') ;
					dia("open") ;
					setTimeout("dia('close')",5000) ;
					$('[name="excel_out"]').submit() ;
					
                });
                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
            } );
			
			function dia(op) {
				$( "#dialog" ).dialog(op) ;
			}
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
		</style>
    </head>
    <body id="dt_example">
        <form name="excel_out" method="POST">
			<input type="hidden" name="fds">
			<input type="hidden" name="fde">
			<input type="hidden" name="bke">
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
            <{include file='menu1.inc.tpl'}>
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
                                            <form name="form_search">
                                            <table border="0" cellspacing="10" cellpadding="10">
                                                <tr>
                                                    <th>查詢結案日期範圍︰</th>
                                                    <td><input type="text" name="_fds" onclick="show_calendar('form_search._fds')" readonly /> ~ <input type="text" name="_fde" onclick="show_calendar('form_search._fde')" readonly /></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">

                                                    </td>
                                                </tr>
                                            </table>
                                            </form>
                                            <center><button id="export">匯出Excel</button></center>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>