<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                var ck = "<{$smarty.session.member_id}>";
               
                if (ck!=10 && ck!= 6 && ck !=1) {
                    alert("非權限使用者");
                    location.href="http://www.first1.com.tw/";

                }

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
                
               

                $('#export').live('click', function(event) {

                    
                    
                   
                    
                    $("[name='form_search']").submit();
                    
                });


                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

           
           

            });
            
            function show(op) {
                $( "#dialog" ).dialog(op) ;
            }

          

        </script>
        <style>
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }

        #search{
            width: 80px;
            height: 50px;
            font-size: 15px;
        }
        #b{
            padding-top: 10px;
            padding-bottom: 10px;


        }
        .tb{
           
            border: solid 1px #ccc;

        }
        .tb th{
           
            /*background-color:#F8ECE9;*/
            padding-top: 5px;
            padding-bottom: 5px;
            /**/

        }
        .tb td{
           
            /*background-color:#F8ECE9;*/
            padding-top: 5px;
            padding-bottom: 5px;
            /**/

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
                                    <td colspan="3" align="right"></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"></td>
                                    <td width="14%" align="center"></td><td width="5%" height="30" colspan="2"></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            
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
                                        <h1>區域案件統計</h1>
                                        <div id="container">
                                            

                                            <center>
                                            <div style="text-align:left">
                                                <font color="red">
                                                ※仲介店案件數量 可能會跟地區案件數量有差異(有可能該店有別區案件) <br>
                                                ※仲介店比較表的比較時間會影響店的顯示(只顯示在簽約時間(迄)之前建立的店家)<br>
                                               
                                                <!--※如果想查某地區的案件(EX:台南市案件)，只查案件地區即可<br>
                                                ※如果想查某地區的仲介案件(EX:台南市所有店家案件)，只查仲介地區即可[有可能該店有別區案件] <br>
                                               
                                                ※如果想查某地區的仲介案件且只看某地區仲介的案件(EX:台南市案件只想看台南市店家的案件)，案件地區跟仲介地區都要設定<br> -->

                                                </font>
                                                <!-- <font color="blue">●仲介店比較表的比較時間會影響店的顯示(只顯示在比較時間之前建立的店家)[目前這個查詢是都會列]<br>
                                                ●可能會發生該案件未填寫地區卻有填寫店家情況<br>
                                                </font> -->
                                            </div>
                                            <br>
                                            <form name="form_search" method="post">

                                                <table border="0" cellspacing="0" cellpadding="0" class="tb">
                                             
                                                    <tr>
                                                        <th>簽約時間：</th>
                                                        <td>
														<select name="date_start_y" id="">
														<{for $i =$smarty.now|date_format:"%Y" to 2012 step -1}>
															<{$year = ($i - 1911)}>
															<option value="<{$year+1911}>"<{if $year == $smarty.now|date_format:"%Y"}> selected="selected"<{/if}>><{$year}></option>
														<{/for}>
														</select>
														<select name="date_start_m" style="width:60px;">
															<{for $i = 1 to 12 }>
																<{$month = $i|string_format:"%02d"}>
																<option value="<{$month}>"<{if $month == $smarty.now|date_format:"%m"}> selected="selected"<{/if}>><{$month}></option>
															<{/for}>
														</select>(起)～
														
														<select name="date_end_y" id="">
															<{for $i =$smarty.now|date_format:"%Y" to 2012 step -1}>
																<{$year = ($i - 1911)}>
																<option value="<{$year+1911}>"<{if $year == $smarty.now|date_format:"%Y"}> selected="selected"<{/if}>><{$year}></option>
															<{/for}>
															</select>
															<select name="date_end_m" style="width:60px;">
																<{for $i = 1 to 12 }>
																	<{$month = $i|string_format:"%02d"}>
																	<option value="<{$month}>"<{if $month == $smarty.now|date_format:"%m"}> selected="selected"<{/if}>><{$month}></option>
																<{/for}>
														</select>(迄)	  
                                                        </td>
                                                       
                                                    </tr>
                                                    <tr>
                                                    	<th>地區：</th>
                                                    	<td>
                                                    		<select name="country" >
																	<{$citys}>
															</select>
                                                    	</td>
                                                    </tr>
                                                     
                                                    <tr>
                                                        <th>仲介類型:</th>
                                                        <td>
                                                            <select name="realestate" size="1" style="width:130px;">
                                                                <option value="">全部</option>
                                                                <option value="11">加盟(其他品牌)</option>
                                                                <option value="12">加盟(台灣房屋)</option>
                                                                <option value="13">加盟(優美地產)</option>
                                                                <option value="14">加盟(永春不動產)</option>
                                                                <option value="1">加盟</option>
                                                                <option value="2">直營</option>
                                                                <option value="3">非仲介成交</option>
                                                                <option value="4">其他(未指定)</option>

                                                            </select>
                                                        </td>
                                                        

                                                    </tr>
                                                    <tr>
                                                        <th>品牌:</th>
                                                        <td> <{html_options name=brand options=$menuBrand }></td>
                                                    </tr>
                                                   <tr>
                                                       <td colspan="2">&nbsp;</td>
                                                   </tr>
                                                    <tr>
                                                        <td colspan="2" align="center">

                                                            <input type="button" id="export" value="匯出EXCEL">
                                                            <input type="hidden" name="ck" value="1">
                                                        </td>

                                                    </tr>
                                                   
                                                </table>
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