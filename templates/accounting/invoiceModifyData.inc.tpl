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
				$("#export_file").click(function() {

				})
			function detail(no) {
				//alert(no) ;
				$.colorbox({	
					iframe:true, width:"1100", height:"500",						
					href: "invoiceDetail.php?sn=" + no,
					onClosed: function() {
						$('#queryAgain').submit() ;
					}
				});
			}

			function del(no){

				if(confirm('是否確認刪除?'))
				{
					$.post('invoiceDeleteData.php', {'cId': no}, function(txt) {
					// $("#show").html(txt);

						if (txt==1) {
							alert('刪除成功');
							$('#queryAgain').submit() ;
						}else{
							alert('刪除失敗');
						}

					});
				}

				
			}

			function nosend()
			{
				var ck1 = new Array();
				var ck2 = new Array();

				

				$('[name="nosend[]"]').each(function(i) { ck1[i] = this.value; });
				var txt = ck1.join(',');

				$('input:checkbox:checked[name="nosend[]"]').each(function(i) { ck2[i] = this.value; });
				var txt2 = ck2.join(',');

				$.post('invoiceNoSend.php', {'id': txt,'ck':txt2}, function(txt) {
					alert(txt);
					$('#queryAgain').submit() ;
				});
			}
			
			function research() {
				location = 'invoiceModify.php' ;
			}
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
		
        .tb td{
            border-bottom: 1px solid #999;
			padding: 5px;
			font-size: 10pt;
        }
		
		.tb th {
			text-align:center;
			border-bottom: 1px solid #999;
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
                                                <form name="myform" id="myform" method="POST" enctype="multipart/form-data">
													<div style="margin-bottom:10px; width:900px; text-align:left;">
														<input type="button" value="重新查詢" onclick="research()" style="padding:5px;">
														<input type="button" value="儲存" onclick="nosend()" style="padding:5px;">
													</div>
													<!-- <div id="show">
                                                   
                                                </div> -->
													<table align="center" class="tb" cellpadding="0" cellspacing="0">
														<tr style="height:60px;background-color:#FFE4E1;">
															<th style="width:100px">未寄送發票</th>
															<th style="width:100px;">可查詢</th>
															<th style="width:100px;">可列印</th>
															<th style="width:100px;">對象</th>
															<th style="width:100px;">保證號碼</th>
															<th style="width:100px;">發票號碼</th>
															<th style="width:100px;">發票日期</th>
															<th style="width:100px;">發票金額</th>
															<th style="width:100px;">發票抬頭</th>
															<th style="width:150px;">統編/身分證號</th>
															<th style="width:100px;">帳號</th>
															<th style="width:100px;">密碼</th>
															<th style="width:100px;">經辦</th>
															<th style="width:100px;">刪除</th>
														</tr>
													<{foreach from=$list key=k item=v}>
														<{if $k is even}>
															<{$color = '#FFFAFA;'}>
														<{else}>
															<{$color = '#FCEEEE;'}>
														<{/if}>
														<tr ondblclick="detail('<{$v.cId}>')" style="background-color:<{$color}>">
															<td style="text-align:center;">
																<{if $v.cNoSend == 1}>
																	<input type="checkbox" name="nosend[]" id="" value="<{$v.cId}>" checked>
																<{else}>
																	<input type="checkbox" name="nosend[]" id="" value="<{$v.cId}>">
																<{/if}>
															</td>
															<td style="text-align:center;"><{$v.cQuery}></td>
															<td style="text-align:center;"><{$v.cPrint}></td>
															<td style="text-align:center;"><{$v.iden}></td>
															<td style="text-align:center;"><{$v.cCertifiedId}></td>
															<td><{$v.cInvoiceNo}></td>
															<td><{$v.cInvoiceDate}></td>
															<td style="text-align:right;"><{$v.cMoney}></td>
															<td><{$v.cName}></td>
															<td><{$v.cIdentifyId}></td>
															<td><{$v.cAcc}></td>
															<td><{$v.cPass}></td>
															<td><{$v.member_name}></td>
															<td style="text-align:center;"> <input type="button" value="刪除" onclick="del('<{$v.cId}>')"></td>
														</tr>
													<{/foreach}>
													</table>
													<div style="margin-top:10px; width:890px; text-align:right;">
														<input type="button" value="重新查詢" onclick="research()" style="padding:5px;">
													</div>
                                                </form>
                                                <br>
												
												<form id="queryAgain" method="POST" action="invoiceModifyData.php">
													<input type="hidden" name="iNo" value="<{$q.iNo}>">
													<input type="hidden" name="iDate" value="<{$q.iDate}>">
													<input type="hidden" name="iCertifiedId" value="<{$q.iCertifiedId}>">
													<input type="hidden" name="iName" value="<{$q.iName}>">
													<input type="hidden" name="query" value="<{$q.query}>">
												</form>
                                                
                                                
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