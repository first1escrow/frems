<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#book1').hide();
                $('#book3').hide();
                
                $('#export').live('click', function () {
                    //var yr = $('[name=real_year]').val();
                    //var mn = $('[name=real_month]').val();

                    //window.open('realty_service_charge_excel.php?real_year='+yr+'&real_month='+mn,'','width=660px,height=800px,scrollbars=yes,location=no,menubar=no,location=no,resizable=yes') ;
					
					$('[name="form_search"]').submit() ;
                });
                $('[name="identity"]').live('click', function () {


                        var value =$('[name="identity"]:checked').val();
                        
                        if(value=='scrivener')
                        {
                            $('#book1').hide();
                            $('#book3').hide();
                           
                            $(".show").show();
                        }else
                        {
                            $('#book1').show();
                            $('#book3').show();
                            // $('.b').show();
                             $(".show").hide();
                        }

                     });

				
				$('#citys').change(function() {
					cityChange() ;
				}) ;
				
                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
				
            } );
			/* 取得縣市區域資料 */
			function cityChange() {
				var url = 'zipArea.php' ;
				var _city = $('#citys :selected').val() ;
				$.post(url,{'c':_city,'op':'1'},function(txt) {
					$('#areas').html(txt) ;
				}) ;
			}
			////

			/* 取得區域郵遞區號 */
			function areaChange() {
				var _area = $('#areas :selected').val() ;
				$('#zip').val(_area) ;
			}
			////
        </script>
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
                                            <h1>有缺回饋通知簡訊對象表</h1>
                                            <form name="form_search" method="POST">
                                            <table border="0" cellspacing="10" cellpadding="10">
												
												<tr>
													<th align="right">區域︰</th>
															
													
                                                    <td colspan="3">
                                                        <select name="country" id="citys" class="keyin2b">
                                                                <{$citys}>
                                                            </select>
                                                            <select name="area" id="areas" class="keyin2b">
                                                                <option value="">全部</option>
                                                            </select>
                                                            <input type="hidden" name="zip" id="zip" readonly="readonly" />
                                                    </td>
												</tr>
                                                
                                                <tr class="b">
                                                    <th align="right">回饋時間︰</th>
                                                    <td colspan="3">
                                                        <select name="year" style="width:60px;" class="xxx-select">
                                                        <{$menu_year}>
                                                        </select>
                                                        年度
                                                        <{html_options name="season" style="width:80px;" options=$menu_season selected=$seasons class="xxx-select"}>
                                                    </td>
                                                </tr>
                                               
												
                                                <tr valign="top">
                                                    <th>身分別︰</th>
                                                    <td>
														<input type="radio" name="identity" checked="checked" value="1">
														地政士<br>
														<input type="radio" name="identity" value="2">
														仲介
													</td>
                                                   
                                                    <td><input type="hidden" name="export_ok" value="1"></td>
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
                                            <center><button id="export">匯出檔案</button></center>
                                            
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
    </body>
</html>