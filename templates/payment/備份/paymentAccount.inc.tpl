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

                // if ("<{$Link}>" != '') {
                //     window.open("<{$Link}>");
                // }
				
				// $('#export').live('click', function () {
                // $('[name="fds"]').val($('[name="_fds"]').val()) ;
                // $('[name="fde"]').val($('[name="_fde"]').val()) ;
				// 	$('[name="bke"]').val($('[name="bank_option"]').val()) ;
				// 	$('[name="exp"]').val('ok') ;
				// 	// dia("open") ;
				// 	// setTimeout("dia('close')",5000) ;
				// 	$('[name="excel_out"]').submit() ;
					
                //});
                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
                $('#search').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
            } );
			
			function dia(op) {
				$( "#dialog" ).dialog(op) ;
			}
            function search(cat){

                if (cat == 'export') {
                    $('[name="form_search"]').attr('target', '_blank');
                }
              
                 $('[name="exp"]').val(cat) ;
                 $('[name="form_search"]').submit() ;
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
                                            <h1>分戶上傳</h1>
                                            <form name="form_search" method="POST">

                                               
                                                <input type="hidden" name="bke">
                                                <input type="hidden" name="exp">
       
                                            <table border="0" cellspacing="10" cellpadding="10">
                                                <tr>
                                                    <td>撥款日為付款日+1天</td>
                                                </tr>
                                              
                                                <tr>
                                                    <th>撥款日期範圍︰</th>
                                                    <td><input type="text" name="fds" class="datepickerROC" value="<{$startDate}>" readonly /> ~ <input type="text" name="fde" class="datepickerROC" readonly value="<{$endDate}>" /></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                
                                            </table>
                                            <table cellspacing="0" cellpadding="0" class="tb">
                                                <tr>
                                                    <th>選取</th>
                                                    <th>案件編號</th>
                                                    <th>付款日期</th>
                                                    <th>斡旋金額</th>
                                                    <th>交易類別</th>
                                                </tr>
                                                <{foreach from=$list key=key item=item}>
                                                <tr>
                                                    <td><input type="checkbox" name="aId[]" id="" value="<{$item.allotId}>" <{$item.checked}> ></td>
                                                    <td><{$item.cCaseNo}></td>
                                                    <td><{$item.cCasePayTime}></td>
                                                    <td><{$item.cAmount}></td>
                                                    <td><{$item.TSType}></td>
                                                </tr>
                                                <{/foreach}>
                                            </table>
                                            </form>
                                            <center>
                                                <button id="search" onclick="search('search')">搜尋</button>
                                                <button id="export" onclick="search('export')">出款打包</button>
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