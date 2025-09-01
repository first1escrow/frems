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
					// dia("open") ;
					// setTimeout("dia('close')",5000) ;
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
            function Refund(no){
                $.ajax({
                    url: 'Refund.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {no: no},
                })
                .done(function(msg) {
                    // console.log(msg);
                    alert(msg);
                    $('[name="reload"]').submit();
                });
                
            }
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
        .tb {
            padding:5px;
            margin-bottom: 20px;
            background-color:#FFFFFF;
            width: 100%;
        }
        .tb th{
            padding: 5px;
            border: 1px solid #CCC;
            background-color: #CFDEFF;
        }
        .tb td{
            text-align: center;
            padding: 5px;
            border: 1px solid #CCC;
        }
		</style>
    </head>
    <body id="dt_example">
        <form name="reload" method="POST">
			
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
                                    <td width="81%" align="right"></td>
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
                                            <h1>斡旋失敗案件(已請款)</h1>
                                            <form name="form_search">   
                                            <table cellspacing="0" cellpadding="0" class="tb">
                                                <tr>
                                                    <th width="20%">案件編號</th>
                                                    <th width="20%">斡旋失敗日期</th>
                                                    <th width="10%">付款方式</th>
                                                    <th width="10%">金額</th>
                                                    <th width="20%">買方</th>
                                                    <th></th>
                                                </tr>
                                                <{foreach from=$list key=key item=item}>
                                                <tr>
                                                    <td><{$item.cCaseNo}></td>
                                                    <td><{$item.cFailTime}></td>
                                                    <td><{$item.cPayType}></td>
                                                    <td><{$item.cAmount}></td>
                                                    <td><{$item.cName}></td>
                                                    <td>
                                                        <{if $item.cPayType == '信用卡'}>
                                                            <input type="button" value="退款" onclick="Refund('<{$item.cCaseNo}>')">
                                                        <{/if}>
                                                    </td>
                                                </tr>
                                                <{/foreach}>
                                            </table>
                                               
                                            
                                            </form>
                                          
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