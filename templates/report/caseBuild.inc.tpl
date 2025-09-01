<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                showChildMenu('objkind');
                
                $(".city1").mouseleave(function() {
                        closeCityMenu();
                });

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
                
                $('#citys').change(function() {
                    cityChange() ;
                }) ;
                
                $('#areas').change(function() {
                    areaChange() ;
                }) ;
               

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
            
            function showChildMenu(cat){
                $("#"+cat).show();
                if (cat == 'objkind') {
                    $('#money').hide();
                }else if(cat == 'money'){
                    $('#objkind').hide();
                }else{
                   $('#money').hide();
                   $('#objkind').hide();
                }
                
            }
            /* 取得縣市區域資料 */
            function cityChange() {
                var url = 'zipArea.php' ;
                var _city = $('#citys :selected').val() ;
                $.post(url,{'c':_city,'op':'1'},function(txt) {
                    $('#areas').html(txt) ;
                }) ;
            }
            function addArea(){
                var html = $(".area").html();
                var city = $("[name='country']").val();
                var area = $("[name='area']").val();
                var area2 = $("[name='area']").find(":selected").text();
                var txt = '';
                var val = '';
                

                if (area == '') { //only city
                    txt += "<span onClick=\"delArea('"+city+"')\" class=\"delArea\" id=\""+city+"\">"+city+"<input type=\"hidden\" name=\"zipC[]\" class=\"btC\" value=\""+city+"\" ></span> ";
                    val = city;
                }else{
                    txt += "<spna onClick=\"delArea('"+area+"')\" class=\"delArea\" id=\""+area+"\">"+city+area2+"<input type=\"hidden\" name=\"zipA[]\" class=\"btC\" value=\""+area+"\" ></span>";
                     val = area;
                }

                if (checkArea(val) == false) {
                    alert("該區已新增");
                    return false;
                }
                $(".area").html(html+txt);

                // console.log(area);
            }
            function checkArea(val){
                var check = 0;
                $(".btC").each(function() {
                    if ($(this).val() == val) {
                        // console.log();
                        check++;
                    }
                });

                if (check > 0) {
                    return false;
                }else{
                    return true;
                }
            }
            function delArea(name){
               
                $(".delArea").each(function() {
                    if ($(this).attr('id') == name) {
                        // console.log();
                        $(this).remove();
                    }
                });
               
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
            width: 60%;

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
        .city{
            border:1px solid #999;
            width:100px;
            
        }
        .searchleft2{
            /*border: 1px solid green;*/
            width: 350px;
        }
        .delArea{
            color: rgb(156, 40, 33);
            font-family: Verdana;
            font-size: 12px;
            font-weight: bold;
            line-height: 14px;
            background-color: #FFF;
            text-align:center;
            display:inline-block;
            padding: 4px 6px;
            border: 1px solid #DDDDDD;
            margin-top: 5px;
            margin-right: 5px;
        }

        .delArea:hover {
            color: #FFF;
            font-size:12px;
            background-color: rgb(156, 40, 33);
            border: 1px solid #DDDDDD;
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
                                        <h1>區域案件建物統計</h1>
                                        <div id="container">
                                            

                                            <center>
                                            <div style="text-align:left">
                                                <font color="red">

                                               

                                                </font>
                                            
                                            </div>
                                            <br>
                                            <form name="form_search" method="post" action="caseBuildExcel.php">

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
                                                        <th>品牌</th>
                                                        <td><input type="checkbox" class="s_check[]" value="yc" checked="checked">&nbsp;永慶
                                                            <input type="checkbox" class="s_check[]" value="sy" checked="checked">&nbsp;信義
                                                            <input type="checkbox" class="s_check[]" value="first" checked="checked">&nbsp;第一建經</td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <th>定義：</th>
                                                        <td>
                                                            <input type="radio" name="cat" id="" value="objkind" onclick="showChildMenu('objkind')" checked>物件類型
                                                            <input type="radio" name="cat" id="" value="money" onclick="showChildMenu('money')">價金

                                                            <input type="radio" name="cat" id="" value="count" onclick="showChildMenu('count')">移轉棟數
                                                        </td>
                                                    </tr>
                                                    <tr id="objkind">
                                                        <td></td>
                                                        <td >
                                                            <input type="checkbox" name="obj[]" id="" value="4" checked>大樓
                                                            <input type="checkbox" name="obj[]" id="" value="8" checked>華廈
                                                            <input type="checkbox" name="obj[]" id="" value="3" checked>公寓
                                                            <input type="checkbox" name="obj[]" id="" value="2" checked>透天
                                                            <input type="checkbox" name="obj[]" id="" value="1" checked>土地/廠辦
                                                            <input type="checkbox" name="obj[]" id="" value="9" checked>套房
                                                            <input type="checkbox" name="obj[]" id="" value="10" checked>店面



                                                        </td>
                                                    </tr>
                                                    <tr id="money">
                                                        <td></td>
                                                        <td>
                                                           <input type="checkbox" name="obj2[]" id="" value="1" checked>500萬(含)以下
                                                           <input type="checkbox" name="obj2[]" id="" value="2" checked>500~1000萬(含)
                                                           <input type="checkbox" name="obj2[]" id="" value="3" checked>1000~1500萬(含) <br>
                                                           <input type="checkbox" name="obj2[]" id="" value="4" checked>1500~2000萬(含)
                                                           <input type="checkbox" name="obj2[]" id="" value="5" checked>2000萬以上
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <th>地區：</th>
                                                        <td>
                                                            
                                                            <select name="country" id="citys" class="keyin2b"><{$citys}></select>
                                                            <select name="area" id="areas" class="keyin2b">
                                                                <option value="">全部</option>
                                                            </select>
                                                            <input type="button" value="新增" onclick="addArea()">
                                                            <input type="hidden" name="zip" id="zip" readonly="readonly" />
                                                            ※如要刪除區域請點擊該區域
                                                        </td>
                                                    </tr>
                                                     
                                                    
                                                    <tr>
                                                        <td></td>
                                                        <td><div class="area"></div></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" align="center">

                                                            <input type="submit" id="export" value="匯出EXCEL">
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