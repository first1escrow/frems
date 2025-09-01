<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#cancel').on('click', function () {
                     $('#form_back').submit();
                 });
                $('#save').on('click', function () {
                    $('#form_land').submit();
                });
                $('#count').on('click', function () {
                    $('tbody#item');
                });

                $('.currency-money2').on('blur',function(event) {
                   $('.currency-money2').formatCurrency({roundToDecimalPlace:2, symbol:''});
                });

               
                $('#save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#count').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
               $('#cancel').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );


            });
            function AddRow(){
                var count = $('.row').length+1;
                var cloneRow = $('.row:first').clone();
                cloneRow.find('input[name*="new_land_movedate[]"]').val("");
                cloneRow.find('input[name*="new_land_landprice[]"]').val("");
                cloneRow.find('input[name*="new_land_power1[]"]').val("");
                cloneRow.find('input[name*="new_land_power2[]"]').val("");
                cloneRow.insertAfter('.row:first');
            }

            function del(id){
               
                $("[name='delId']").val(id);
                $("#form").submit();
            }
        </script>
        <style type="text/css">
            .add {
                padding:5px 10px 5px 10px ;
                color:#212121 ;
                background-color:#FFD78C ;
                margin:2px ;
                border:1px outset #F8ECE0 ;
                cursor:pointer ;
                font-size: 15px;
            }
            .add:hover {
                padding:5px 10px 5px 10px ;
                color:#212121 ;
                background-color:orange;
                margin:2px;
                border:1px outset #F8ECE0;
                cursor:pointer;
                font-size: 15px;
            }
            #tabs {
                width:980px;
                margin-left:auto; 
                margin-right:auto;
            }

            #tabs table th {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
            }

            #tabs table th .sml {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                font-size: 10px;
            }
            #tabs table td {
               /*border: 1px solid #CCC;*/
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=100,width=650');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <div id="mainNav">
                <table width="1000" border="0" cellpadding="0" cellspacing="0">
                    <tr>

                    </tr>
                </table>
            </div>
            <div id="content">
                <div class="abgne_tab">
                        <{include file='menu1.inc.tpl'}>
                    <div class="tab_container">
                        <div id="menu-lv2">
                                                        
                            </div>
                            <br/>
                        <div id="tab" class="tab_content">
                            <div id="tabs">
                                <div id="tabs-contract">
                                    <div>前次移轉現值或原規定地價 </div>
                                    <form id="form" method="POST">
                                        <input type="hidden" name="delId">
                                        <input type="hidden" name="id" value="<{$certifiedid}>">
                                    </form>
                                    <form id="form_land"  method="POST" >
                                        
                                        <input type="hidden" name="id" value="<{$certifiedid}>">
                                         <input type="hidden" name="ok" value="1">  

                                    <table border="0" width="100%">
                                        <tr>
                                            <th width="20%"><center>時間</center></th>
                                            <th width="25%"><center>前次移轉現值或原規定地價</center></th>
                                            <th width="20%"><center>權利分子</center></th>
                                            <th width="20%"><center>權利分母</center></th>
                                            <th width=""></th>
                                            
                                        </tr>
                                        <{foreach from=$data key=key item=item}>
                                        <tr>
                                            <td>
                                                <input type="hidden" name="land_id[]" value="<{$item.cId}>">
                                                <input type="text" name="land_movedate[]" placeholder="000-00" onclick="showdate_m(this)" maxlength="7" class="calender date-field text-right" value="<{$item.cMoveDate}>" onKeyup="checkCalTax()" width="100%"/>
                                            </td>
                                            <td>
                                                <input type="text" name="land_landprice[]" maxlength="13" class="calender currency-money2 text-right" value="<{$item.cLandPrice}>" onKeyup="checkCalTax()" width="80%"/>元/M<sup>2</sup>
                                                <input type="hidden" name="land_landprice_check">
                                            </td>
                                            <td><input type="text" class="input-text-per" name="land_power1[]" value="<{$item.cPower1}>" width="100%"/></td>
                                            <td><input type="text" class="input-text-per" name="land_power2[]" value="<{$item.cPower2}>" width="100%"/></td>
                                            <td align="center"><input type="button" value="刪除" onclick="del(<{$item.cId}>)"></td>
                                        </tr>
                                        <{/foreach}>
                                        <tr class="row" id="row">
                                            <td>
                                                <input type="text" name="new_land_movedate[]" placeholder="000-00" onclick="showdate_m(this)" maxlength="7" width="100%" class="calender date-field text-right" onKeyup="checkCalTax()"/>
                                            </td>
                                            <td>
                                                <input type="text" name="new_land_landprice[]" maxlength="13" width="80%" class="calender currency-money2 text-right"  onKeyup="checkCalTax()"/>元/M<sup>2</sup>
                                            </td>
                                            <td><input type="text" class="input-text-per" name="new_land_power1[]" value="" width="100%"/></td>
                                            <td><input type="text" class="input-text-per "name="new_land_power2[]" value="" width="100%"/></td>
                                        </tr>
                                    </table>
                                   
                                    </form>
                                </div>
                            </div>
                            <center>
                                <br/>
                                <{if $cSignCategory == 1}>
                                <button id="save">儲存</button>
                                <{/if}>
                                <button id="cancel">取消</button>

                                <input type="button" value="增加一列" onclick="AddRow()" name="add" class="add">
                            </center>
                            <form name="form_back" id="form_back" method="POST"  action="<{$file}>">
                             <input type="hidden" name="id" value="<{$certifiedid}>">
                              <input type="hidden" name="cSignCategory" value="<{$cSignCategory}>" />
                            </form>
                        </div>
                    </div>
                </div></div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>










