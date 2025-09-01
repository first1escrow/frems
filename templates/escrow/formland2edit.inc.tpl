<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script src="/js/lib/lib.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#new_land").show();
                let item = parseInt(<{$data_land|@count + 1}>);
                $("[name='new']").text(item + 1);

                $('#save').on('click', function () {
                    $('#save').hide();//禁止使用者多按

                    $('[name="land_item[]"]').each(function(key, val) {
                        // land_price_movedate
                        let _movedate = $('#land_data_' + val.value + ' .price_movedate');
                        let data = [];
                        for (let i = 0; i < _movedate.length; i ++) {
                            data.push(_movedate[i].value);
                        }
                        $('#land_data_' + val.value + ' input[name="land_price_movedate[]"]').val(JSON.stringify(data));

                        // land_price_landprice
                        let price_landprice = $('#land_data_' + val.value + ' .price_landprice');
                        data = [];
                        for (let i = 0; i < price_landprice.length; i ++) {
                            data.push(price_landprice[i].value.replace(/[,]+/g, ""));
                        }
                        $('#land_data_' + val.value + ' input[name="land_price_landprice[]"]').val(JSON.stringify(data));

                        // land_price_power1
                        let price_power1 = $('#land_data_' + val.value + ' .price_power1');
                        data = [];
                        for (let i = 0; i < price_power1.length; i ++) {
                            data.push(price_power1[i].value);
                        }
                        $('#land_data_' + val.value + ' input[name="land_price_power1[]"]').val(JSON.stringify(data));

                        // land_price_power2
                        let price_power2 = $('#land_data_' + val.value + ' .price_power2');
                        data = [];
                        for (let i = 0; i < price_power2.length; i ++) {
                            data.push(price_power2[i].value);
                        }
                        $('#land_data_' + val.value + ' input[name="land_price_power2[]"]').val(JSON.stringify(data));
                    });

                    $('#form_land').submit();
                });

                $('#cancel').on('click', function () {
                    $('#form_back').submit();
                });

                $('[name="land_add"]').on('click', function () {
                    let item = parseInt($("[name='land_item[]']:last").val());
                    item += 1;

                    $("[name='new_land']:last").clone().insertAfter("[name='new_land']:last");

                    $("[name='new']:last").text(item);
                    $("[name='land_item[]']:last").val(item);

                    $("[name='land_zip[]']:last").attr('id', 'land_zip' + item);
                    $("[name='land_zipF']:last").attr('id', 'land_zip' + item + 'F');

                    $("[name='land_country']:last").attr('id', 'land_country' + item);
                    $("[name='land_country']:last").attr('onchange', "getArea2('land_country" + item + "','land_area" + item + "','land_zip" + item + "')");

                    $("[name='land_area']:last").attr('id', 'land_area' + item);
                    $("[name='land_area']:last").attr('onchange', "getZip2('land_area" + item + "','land_zip" + item + "')");

                    $('[name="new_land"]:last').attr('id', 'land_data_' + item);
                    $('.new_edit_btn:last').attr('onclick', 'land_price_edit(' + item + ')');

                    // 移除最後一個 land_category[] 的 combobox
                    $('[name="land_category[]"]:last').next('.ui-combobox').remove();
                    
                    // 重新觸發 setCombobox2 來初始化新增的 land_category[] 下拉選單
                    setCombobox2("land_category[]",'');
                });                
                setCombobox2("land_category[]",'');

                $('[name="land_add"]').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#cancel').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#add').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('.land_price').button( {
                    icons:{
                        primary: "ui-icon-transfer-e-w"
                    }
                });
            });

            function getArea2(ct,ar,zp) {
                var url = 'listArea.php' ;
                var ct = $('#' + ct + ' :selected').val() ;
                
                $('#' + zp + '').val('') ;
                $('#' + zp + 'F').val('') ;
                $('#' + ar + ' option').remove() ;
                
                $.post(url,{"city":ct},function(txt) {
                    var str = '' ;
                    str = str + txt  ;
                    $('#' + ar ).append(str) ;
                }) ;
            }
            
            function getZip2(ar,zp) {
                var zips = $('#' + ar + ' :selected').val() ;

                $('#' + zp + '').val(zips);
                $('#' + zp + 'F').val(zips.substr(0,3));
            }

            function checkCalTax(){
               $("[name='checkaddedtaxmoney']").val(1);
            }

            function land_price_edit(no) {
                let el = $('#land_data_' + no + ' tr:last').clone();
                $('#land_data_' + no).append(el);

                $('#land_data_' + no + ' tr:last .price_movedate').val('');
                $('#land_data_' + no + ' tr:last .price_landprice').val('');
                $('#land_data_' + no + ' tr:last .price_power1').val('');
                $('#land_data_' + no + ' tr:last .price_power2').val('');
                
                $('#land_data_' + no + ' .new_edit_btn').hide();
                $('#land_data_' + no + ' .new_edit_btn:last').show();
            }

        </script>
        <style type="text/css">
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

            #users {
                margin-left:auto; 
                margin-right:auto;
                width:750px;
            }

            #detail {
                margin-left:auto; 
                margin-right:auto;
                width:750px;
            }

            #ec_money{
                text-align:right;
            }

            #pay_income{
                text-align:right;
            }

            #pay_spend {
                text-align:right;
            }

            #pay_total {
                text-align:right;
            }
            
            .input-text-per{
                width:96%;
            }
           
            .input-text-big {
                width:120px;
            }
            
            .input-text-mid{
                width:80px;
            }
            
            .input-text-sml{
                width:36px;
            }
            
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            
            .no-border {
                border-top:0px ;
                border-left:0px ;
                border-right:0px ;
            }
            
            .tb-title {
                font-size: 18px;
                padding-left:15px; 
                padding-top:10px; 
                padding-bottom:10px; 
                background: #E4BEB1;
            }
            
            .th_title_sml {
                font-size: 10px;
            }
            
            .sign-red{
                color: red;
            }     

            .land_price  {
                color: #FFFFFF;
                background: #FFAC55;
            }
            .land_price:hover{
                background: #FF8F19 ;
            }

            .text-center {
                text-align: center;
            }

            .float-left {
                float: left;
            }
        </style>
    </head>
    <body id="dt_example">
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="id" id="id" value='3' />
        </form>
        <form name="form_add" id="form_add" method="POST">
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
                                    <form id="form_land" name='form_scrivener' method="POST" action="formland2save.php">
                                        <input type="hidden" name="certifiedid" id="certifiedid" value="<{$certifyid}>" />
                                        <input type="hidden" name="checkaddedtaxmoney">
                                        <input type="hidden" id="data_land_count" value="<{$data_land|@count + 1}>">

                                        <{foreach from=$data_land key=key item=item}>
                                        <div class="land-data">
                                            <table border="0" width="100%" id="land_data_<{$key + 1}>">
                                                <tr>
                                                    <th colspan="6">&nbsp;<input type="hidden" name="land_item[]" value="<{$key + 1}>" /></th>
                                                </tr>
                                                <tr>
                                                    <th>土地坐落(<{$key + 2}>)︰</th>
                                                    <td colspan="5">
                                                        <input type="hidden" name="land_zip[]" id="land_zip<{$key + 1}>" value="<{$item['cZip']}>" />
                                                        <input type="text" maxlength="6" name="land_zipF" id="land_zip<{$key + 1}>F" class="input-text-sml text-center" readonly="readonly"  value="<{$item['cZip']}>"/>

                                                        <select class="input-text-big" name="land_country" id="land_country<{$key + 1}>" onchange="getArea2('land_country<{$key + 1}>','land_area<{$key + 1}>','land_zip<{$key + 1}>')">
                                                        <{$item['land_city']}>
                                                        </select>
                                                        <span>
                                                            <select class="input-text-big" name="land_area" id="land_area<{$key + 1}>" onchange="getZip2('land_area<{$key + 1}>','land_zip<{$key + 1}>')">
                                                            <{$item['land_area']}>
                                                            </select>
                                                        </span>

                                                        <br/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>段︰</th>
                                                    <td>
                                                        <input type="text" name="land_land1[]" maxlength="16" class="input-text-big" value="<{$item['cLand1']}>"/>
                                                    </td>
                                                    <th>小段︰</th>
                                                    <td>
                                                        <input type="text" name="land_land2[]" maxlength="16" class="input-text-big" value="<{$item['cLand2']}>"/>
                                                    </td>
                                                    <th>地號︰</th>
                                                    <td>
                                                        <input type="text" name="land_land3[]" maxlength="12" class="input-text-big" value="<{$item['cLand3']}>"/> 
                                                    </td>
                                                </tr>
                                                <tr>   
                                                    <th>面積︰</th>
                                                    <td>
                                                        <input type="text" name="land_measure[]" maxlength="12" size="12" class="text-right" value="<{$item['cMeasure']}>" onKeyup="checkCalTax()"/>M<sup>2</sup>
                                                    </td>
                                                    <th>使用分區︰</th>
                                                    <td>
                                                        <{html_options name="land_category[]" options=$menu_categoryarea selected=$item['cCategory']}>
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                                <tr>
                                                    <th>公告土地現值︰</th>
                                                    <td>
                                                        <input type="text" name="land_money[]" maxlength="10" size="10" class="currency-money2 text-right" value="<{$item['cMoney']}>" onKeyup="checkCalTax()"/>元/M<sup>2</sup>
                                                    </td>
                                                    <th>權利範圍︰</th>
                                                    <td colspan="3">
                                                        <input type="text" name="land_power1[]" size="10" class="text-right" value="<{$item['cPower1']}>" onKeyup="checkCalTax()"/> / 
                                                        <input type="text" name="land_power2[]" size="10" class="text-right" value="<{$item['cPower2']}>" onKeyup="checkCalTax()"/>
                                                    </td>
                                                </tr>
                                                <{foreach from=$item.land_price.land_item key=k item=v}>
                                                <tr>
                                                    <th class="th_title_sml">前次移轉現值或原規定地價</th>
                                                    <td>
                                                        <input type="text" placeholder="000-00" size="4" maxlength="7" class="calender date-field text-right price_movedate" value="<{$item.land_price.move_date[$k]}>" onKeyup="checkCalTax()" onclick="showdate_m(this)"/>
                                                        <input type="text" maxlength="13" size="10" class="calender currency-money2 text-right price_landprice" value="<{$item.land_price.land_price[$k]}>" onKeyup="checkCalTax()" />元/M<sup>2</sup>
                                                    </td>
                                                    <th class="th_title_sml">前次移轉現值或原規定地價權利範圍</th>
                                                    <td colspan="2"><input type="text" class="text-right price_power1" value="<{$item.land_price.power1[$k]}>" size="10" onKeyup="checkCalTax()"/> / <input type="text" class="text-right price_power2" value="<{$item.land_price.power2[$k]}>" size="10" onKeyup="checkCalTax()"/></td>
                                                    <td>
                                                        <{if $k == ($item.land_price.land_item|@count - 1)}>
                                                        <input type="button" onclick="land_price_edit(<{$key + 1}>)" value="編輯多組前次移轉" class="new_edit_btn">
                                                        <{/if}>
                                                    </td>
                                                </tr>
                                                <{/foreach}>
                                                <input type="hidden" name="land_price_movedate[]">
                                                <input type="hidden" name="land_price_landprice[]">
                                                <input type="hidden" name="land_price_power1[]">
                                                <input type="hidden" name="land_price_power2[]">
                                            </table>
                                        </div>
                                        <{/foreach}>     

                                        <hr style="margin-top: 10px;">

                                        <div style="text-align: right;padding: 10px;">
                                            <input type="button" name="land_add" value="增加一筆">
                                        </div>
                                        
                                        <table border="0" width="100%" name="new_land" id="land_data_<{$data_land|@count + 1}>">
                                            <tr>
                                                <th colspan="6">&nbsp;<input type="hidden" name="land_item[]" value="<{$data_land|@count + 1}>" /></th>
                                            </tr>
                                            <tr>
                                                <th>土地坐落(<label name="new"></label>)︰</th>
                                                <td colspan="5">
                                                    <input type="hidden" name="land_zip[]" id="land_zip<{$data_land|@count + 1}>" value="<{$new_record_default['cZip']}>"/>
                                                    <input type="text" maxlength="6" name="land_zipF" id="land_zip<{$data_land|@count + 1}>F" class="input-text-sml text-center" readonly="readonly"  value="<{$new_record_default['cZip']}>" />

                                                    <select class="input-text-big" name="land_country" id="land_country<{$data_land|@count + 1}>" onchange="getArea2('land_country<{$data_land|@count + 1}>','land_area<{$data_land|@count + 1}>','land_zip<{$data_land|@count + 1}>')">
                                                        <{$new_record_default['land_city']}>
                                                    </select>
                                                    <span>
                                                        <select class="input-text-big" name="land_area" id="land_area<{$data_land|@count + 1}>" onchange="getZip2('land_area<{$data_land|@count + 1}>','land_zip<{$data_land|@count + 1}>')">
                                                        <{$new_record_default['land_area']}>
                                                        </select>
                                                    </span>
                                                    <br/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>段︰</th>
                                                <td>
                                                    <input type="text" name="land_land1[]" maxlength="16" class="input-text-big"  value="<{$new_record_default['cLand1']}>" />
                                                </td>
                                                <th>小段︰</th>
                                                <td>
                                                    <input type="text" name="land_land2[]" maxlength="16" class="input-text-big" value="<{$new_record_default['cLand2']}>"/>
                                                </td>
                                                <th>地號︰</th>
                                                <td>
                                                    <input type="text" name="land_land3[]" maxlength="12" class="input-text-big" value="" /> 
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>面積︰</th>
                                                <td>
                                                    <input type="text" name="land_measure[]" maxlength="12" size="12" class="text-right" onKeyup="checkCalTax()"/>M<sup>2</sup>
                                                </td>
                                                <th>使用分區︰</th>
                                                <td>
                                                    <{html_options name="land_category[]" options=$menu_categoryarea }>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <th>公告土地現值︰</th>
                                                <td>
                                                    <input type="text" name="land_money[]" maxlength="10" size="10" onKeyup="checkCalTax()" class="currency-money2 text-right" />元/M<sup>2</sup>
                                                </td>
                                                <th>權利範圍︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="land_power1[]" size="10" class="text-right" onKeyup="checkCalTax()" /> / 
                                                    <input type="text" name="land_power2[]" size="10" class="text-right" onKeyup="checkCalTax()"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="th_title_sml">前次移轉現值或原規定地價</th>
                                                <td>
                                                    <input type="text" placeholder="000-00" size="4" maxlength="7" class="calender date-field text-right price_movedate" value="" onKeyup="checkCalTax()" onclick="showdate_m(this)"/>
                                                    <input type="text" maxlength="13" size="10" class="calender currency-money2 text-right price_landprice" value="" onKeyup="checkCalTax()" />元/M<sup>2</sup>
                                                </td>
                                                <th class="th_title_sml">前次移轉現值或原規定地價權利範圍</th>
                                                <td colspan="3"><input type="text" class="text-right price_power1" value="" size="10" onKeyup="checkCalTax()"/> / <input type="text" class="text-right price_power2" value="" size="10" onKeyup="checkCalTax()"/></td>
                                            </tr>
                                            <tr>
                                                <th class="th_title_sml">前次移轉現值或原規定地價</th>
                                                <td>
                                                    <input type="text" placeholder="000-00" size="4" maxlength="7" class="calender date-field text-right price_movedate" value="" onKeyup="checkCalTax()" onclick="showdate_m(this)"/>
                                                    <input type="text" maxlength="13" size="10" class="calender currency-money2 text-right price_landprice" value="" onKeyup="checkCalTax()" />元/M<sup>2</sup>
                                                </td>
                                                <th class="th_title_sml">前次移轉現值或原規定地價權利範圍</th>
                                                <td colspan="2"><input type="text" class="text-right price_power1" value="" size="10" onKeyup="checkCalTax()"/> / <input type="text" class="text-right price_power2" value="" size="10" onKeyup="checkCalTax()"/></td>
                                                <td>
                                                    <input type="button" class="new_edit_btn" onclick="land_price_edit(<{$data_land|@count + 1}>)" value="編輯多組前次移轉" id="land_price">
                                                </td>
                                            </tr>
                                            <input type="hidden" name="land_price_movedate[]">
                                            <input type="hidden" name="land_price_landprice[]">
                                            <input type="hidden" name="land_price_power1[]">
                                            <input type="hidden" name="land_price_power2[]">
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
                            </center>

                            <form name="form_back" id="form_back" method="POST"  action="formbuyowneredit.php">
                                <input type="hidden" name="id" value="<{$certifyid}>">
                            </form>

                            <form name="form_land_price" id="form_land_price" method="POST"  action="">
                                <input type="hidden" name="id" value="<{$certifyid}>">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
        </div>

        <div id="dialog"></div>
    </body>
</html>










