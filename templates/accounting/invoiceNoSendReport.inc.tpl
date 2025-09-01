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
                $('#import').button({
                    icons:{
                        primary: "ui-icon-folder-open"
                    }
                }) ;
				
            });
			
			function dia(op) {
				$( "#dialog" ).dialog(op) ;
			}
			
			function validData() {
				var n = $('[name="iNo"]').val() ;
				var d = $('[name="iDate"]').val() ;
				var c = $('[name="iCertifiedId"]').val() ;
				var i = $('[name="iIndex"]').val() ;
				
				//if (n || d || c || i) {
					$('[name="query"]').val('ok') ;
					$('#myform').submit() ;
				//}
				//else {
				//	alert('請至少輸入一筆搜尋條件!!') ;
				//	$('[name="iNo"]').focus() ;
				//	return false ;
				//}
			}
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}

        .tb{
            border: 1px solid #FFFFFF;
            width: 500px;
            background-color: #FCEEEE;
        }
        .tb td{
            /*border-bottom: 1px solid #999;*/
			padding: 3px;
        }

        .div-inline{ 
            display:inline;
            width: 30%;
            float: left;
            padding-bottom: 50px;


            /*padding-right: 20px;*/
        } 
        #show {
            padding: 50px;
           
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
                                            <center>
                                                <form name="myform" id="myform" method="POST" >
                                                	<h1>未寄送名單查詢</h1>
													<table align="center" width="50%" cellpadding="10" cellspacing="10">
														<tr>
															<td colspan="3"></td>
														</tr>
														<tr style="border:1px #FFF solid">
															<th>日期範圍</th>
															<td>
																<input type="text" name="start" style="width:100px;" value="" class="datepickerROC"> 至 <input type="text" name="end" style="width:100px;" value="" class="datepickerROC">
																<input type="hidden" name="ex" value="1">
															</td>
															<td><input type="submit" id="import" value="送出"></td>
														</tr>
														
													</table>
                                                </form>
                                                <br>

                                                
                                           </center>
                                           
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