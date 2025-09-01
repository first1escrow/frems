<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta2.inc.tpl'}>

        <script type="text/javascript">
           


            $(document).ready(function() {
                


                $('#dialog').dialog('close');
                $('#book1').hide();
                $('#book3').hide();
                
                $('#export').on('click', function () {
                    //var yr = $('[name=real_year]').val();
                    //var mn = $('[name=real_month]').val();

                    //window.open('realty_service_charge_excel.php?real_year='+yr+'&real_month='+mn,'','width=660px,height=800px,scrollbars=yes,location=no,menubar=no,location=no,resizable=yes') ;
					
					$('[name="form_search"]').submit() ;
                });
                $('[name="identity"]').on('click', function () {


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
                });

                $('#export2').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
				
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

            function xls(val){
               
                // $( "#dialog" ).dialog("open") ;
                // $( "#dialog" ).dialog({
                //     autoOpen: false,
                //     modal: true,
                //     minHeight:50,
                //     show: {
                //         effect: "blind",
                //         duration: 1000
                //     },
                //     hide: {
                //         effect: "explode",
                //         duration: 1000
                //     }
                // });
                $('[name="report"]').val(val);

                $('[name="form_search"]').submit();


             

            }

            
			////
        </script>
        <style>
            #dialog {
                background-image:url("/images/animated-overlay.gif") ;
                background-repeat: repeat-x;
                margin: 0px auto;
                width: 300px;
                height: 30px;
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
                                            <div id="dialog" class="easyui-dialog" style="display:none"></div>
                                        <{if $smarty.session.member_pFeedBackModify == '1'}>
                                            <div style="text-align:right;"><a href='feedbacksms_excel.php' target="_blank">下載回饋金簡訊對象</a></div>
                                        <{/if}>
                                        <div id="progressbar"></div>
                                            <form name="form_search" method="POST" >
                                                <input type="hidden" name="report" value="">
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
                                                    <th align="right">條件︰</th>
                                                    <td colspan="3">
                                                        <!-- <{html_checkboxes name=BrandContract options=$BrandContract}> -->
                                                        <label id="book1"><input type="checkbox" name="book[]" value="1" style="display: inline-block;">特約</label>
                                                        <label id="book2"><input type="checkbox" name="book[]" value="2">合契</label>
                                                        <label id="book3"><input type="checkbox" name="book[]" value="3" style="display: inline-block;">先行撥付同意書</label>
                                                    </td>
                                                </tr>
                                                <tr class="show">
                                                    <th>合作品牌</th>
                                                    <td  colspan="3">
                                                        <{html_checkboxes name="BrandContract" options=$menuBrandContract }>
                                                    </td>
                                                </tr>
												
                                                <tr valign="top">
                                                    <th>仲介、地政士CSV檔案下載︰</th>
                                                    <td>
														<input type="radio" name="identity" checked="checked" value="scrivener">
														地政士<br>
														<input type="radio" name="identity" value="realty">
														仲介
													</td>
                                                    <td align="left">
														<input type="checkbox" name="field_tel" checked="checked" value="1">
														電話<br>
														<input type="checkbox" name="field_fax" value="1">
														傳真<br>
														<input type="checkbox" name="field_email" value="1">
														E-Mail<br>
														<input type="checkbox" name="field_mobile" value="1">
														行動電話<br>
														<input type="checkbox" name="field_address" value="1">
														地址<br>
                                                        <div class='show'> <input Type ="checkbox" name="field_office" value = "1">
                                                        事務所名稱</div>
														<input type="checkbox" name="field_sales" value="1">
														負責業務<br>
                                                       <div class='show'>
                                                            <input type="checkbox" name="field_undertaker" value="1">
                                                           
                                                            負責經辦<br>
                                                        </div>
                                                          <div class='show'> <input Type ="checkbox" name="field_sBrand" value = "1">
                                                        合作品牌</div>
                                                       <input type="checkbox" name="field_signSales" id="" value="1"> 簽約業務
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
                                            <center> <input type="button" id="export" value="匯出CSV檔案" onclick="xls(1)"> &nbsp;&nbsp;<input id="export2" type="button" value="匯出Excel檔" onclick="xls(2)"></center>
                                            
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