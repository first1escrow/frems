<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
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
                  
                    var cat = $("[name='cat']:checked").val();


                    if (cat==1) {

                        $('[name="form_search"]').attr('action', 'branch_report_test.php');
                    }else
                    {
                        $('[name="form_search"]').attr('action', 'scrivener_report.php');
                    }

					act("open") ;
					setTimeout("act('close')",100) ;
					$('[name="form_search"]').submit() ;
					
                });


             
                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
            } );
			
			function act(op) {
				$( "#dialog" ).dialog(op) ;
			}
        </script>
		<style>
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
                        <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('http://first.twhg.com.tw/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                                            <form name="form_search" method="POST">
                                            <table border="0" cellspacing="10" cellpadding="10">
                                                <tr>
                                                    <th>類別選擇︰</th>
                                                    <td><input type="radio" name="cat" value="1" checked>仲介店 &nbsp;&nbsp;<input type="radio" name="cat"value="2">地政士</td>
                                                </tr>
                                                <tr>
                                                    <th>查詢編號︰</th>
                                                    <td>
                                                       <input type="text" name="search_id">
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