<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#add').live('click', function () {
                    let val = 1;
                    $.ajax({
                        url: '/includes/maintain/checkMaintain.php',
                        type: 'POST',
                        dataType: 'html',
                        async: false, //同步處理
                        data: {"cat": 'checkB',"code":$("[name='code']").val()},
                    }).done(function(txt) {
                        if (txt != 1) {
                            val = 0;
                        }
                    });

                    if (val == 0) {
                        alert('已有相同的品牌代碼');
                        return false;
                    }

                    $('#add').hide();//禁止使用者多按
                    let password1 = $('[name=password1]').val();
                    let password2 = $('[name=password2]').val();

                    if (password1 != password2) {
                        alert('確認密碼必需一致!');
                        return;
                    }

                    let code = $('[name=code]').val();
                    if (! (/^[A-Z]+$/.test(code)) ) {
                        alert('仲介品牌代碼請輸入英文大寫');
                        return;
                    }

                    let input = $('input');
                    let textarea = $('textarea');
                    let select = $('select');
                    let arr_input = new Array();

                    $.each(select, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    $.each(textarea, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    $.each(input, function(key,item) {
                       if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }
                        } else {
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }
                    });

                    let obj_input = $.extend({}, arr_input);
                    let request = $.ajax({  
                        url: "/includes/maintain/brandadd.php",
                        type: "POST",
                        data: obj_input,
                        dataType: "html"
                    });

                    request.done( function( msg ) {
                        alert(msg);
                        $('#form_back').submit();
                    });
                });

                $('#save').live('click', function () {
                    let password1 = $('[name=password1]').val();
                    let password2 = $('[name=password2]').val();

                    if (password1 != password2) {
                        alert('確認密碼必需一致!');
                        return;
                    }

                    let code = $('[name=code]').val();
                    if (! (/^[A-Z]+$/.test(code)) ) {
                        alert('仲介品牌代碼請輸入英文大寫');
                        return;
                    }

                    let input = $('input');
                    let textarea = $('textarea');
                    let select = $('select');
                    let arr_input = new Array();

                    $.each(select, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    $.each(textarea, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    $.each(input, function(key,item) {
                        if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }
                        } else {
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }
                    });

                    let obj_input = $.extend({}, arr_input);
                    let request = $.ajax({  
                        url: "/includes/maintain/brandsave.php",
                        type: "POST",
                        data: obj_input,
                        dataType: "html"
                    });

                    request.done( function( msg ) {
                        alert(msg);
                        $('#form_back').submit();
                    });
                });

                $('#delete').live('click', function () {
                    if (confirm('是否刪除本品牌？') === true) {
                        let request = $.ajax({  
                            url: "/includes/maintain/branddelete.php",
                            type: "POST",
                            data: {'id': $('[name="id"]').val()},
                            dataType: "html"
                        });

                        request.done(function(msg) {
                            alert(msg);
                            $('#form_back').submit();
                        });
                    }
                });
                $( "#tabs" ).tabs({
                    selected: 0
                });
             
                $('#save, #delete, #add, #buyer_edit, #owner_edit').button({
                    icons:{
                        primary: "ui-icon-info"
                    }
                });
            });
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
        </style>
    </head>
    <body id="dt_example">
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753">
                            <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=100,width=650');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table>
                        </td>
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
                        <div id="menu-lv2"></div>
                        <br/> 
                        <div id="tab" class="tab_content">
                            <div id="tabs">
                                <ul>
                                    <li><a href="#tabs-contract">仲介品牌維護</a></li>
                                </ul>
                                <div id="tabs-contract">
                                    <table border="0" width="100%">
                                        <tr>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                        </tr>
                                        <tr>
                                            <th>仲介品牌代碼︰</th>
                                            <td>
                                            <input type="hidden" name="id" value="<{$data.bId}>">
                                            <input type="text" name="code" maxlength="2" class="input-text-big" value="<{$data.bCode}>"  />
                                            </td>
                                            <th>密碼輸入︰</th>
                                            <td>
                                                <input type="text" name="password1" maxlength="12" class="input-text-big" value="<{$data.bPassword}>"  /><br/>
                                                密碼長度6~12碼，密碼必同時包含大、小寫英文字母阿拉伯數字0-9英文小寫視為不同密碼
                                            </td>
                                            <th>再次確認密碼︰</th>
                                            <td>
                                                <input type="password" name="password2" maxlength="12" class="input-text-big" value="<{$data.bPassword}>"  />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>仲介品牌名稱︰</th>
                                            <td>
                                                <input type="text" name="name" maxlength="20" class="input-text-per" value="<{$data.bName}>"/>
                                            </td>
                                            <th>統一編號︰</th>
                                            <td>
                                                <input type="text" name="serialnum" maxlength="10" class="input-text-big" value="<{$data.bSerialnum}>"  />
                                            </td>
                                            <th>公司全名︰</th>
                                            <td>
                                                <input type="text" name="wholename" maxlength="15" class="input-text-per" value="<{$data.bWholeName}>"  />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>特約地政士限制︰</th>
                                            <td>
                                                <{html_checkboxes name=bScrivener options=$menu_scrivener selected=$data.bScrivener separator='<br/>'}>
                                                
                                            </td>
                                            <{if $smarty.session.pFeedBackModify !='0'}>
                                            
                                            <th>回饋其他對象</th>
                                            <td colspan="3">
                                                <{$data.bScrivenerFeed}>
                                            </td>
                                            <{else}>
                                                <td colspan="4">&nbsp;</td>
                                            <{/if}>
                                        
                                        </tr>
                                        <tr>
                                            <th><span class="sign-red">*</span>聯絡地址︰</th>
                                            <td colspan="5">
                                                <input type="hidden" name="zip" id="zip" />
                                                <input type="hidden" maxlength="6" name="zipF" id="zipF" class="input-text-sml text-center" readonly="readonly" />
                                                <select class="input-text-big" name="country" id="country" class="keyin2b" onchange="getArea('country','area','zip')">
                                                    <{$country}>
                                                </select>
                                                <span id="areaR">
                                                    <select class="input-text-big" name="area" id="area" onchange="getZip('area','zip')">
                                                    <{$area}>
                                                    </select>
                                                </span>
                                                <input class="input-text-per" name="address" value="<{$data.bAddress}>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>聯絡電話︰</th>
                                            <td>
                                                <input type="text" name="bTelArea" maxlength="3" class="input-text-sml" value="<{$data.bTelArea}>" /> -
                                                <input type="text" name="bTelMain" maxlength="10" class="input-text-mid" value="<{$data.bTelMain}>" />
                                            </td>
                                            <th>電子郵件︰</th>
                                            <td colspan="2">
                                                <input type="text" name="bEmail" maxlength="30" class="input-text-per" value="<{$data.bEmail}>" />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th>業務聯絡人︰</th>
                                            <td colspan="5">
                                                <input name="seller" type="text" class="input-text-per" value="<{$data.bSeller}>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>備註︰</th>
                                            <td colspan="5">
                                                <textarea name="remark" class="input-text-per" rows="3"><{$data.bRemark}></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>可用系統︰</th>
                                            <td>
                                                <{html_checkboxes name=bBank options=$menu_categorybank_twhg selected=$data.bBank separator='<br/>'}>
                                            </td>
                                            <th>保證費率︰</th>
                                            <td>
                                                <input type="text" name="certified" maxlength="10" class="input-text-big" value="<{$data.bCertified}>" />
                                            </td>
                                        </tr>
                                    <{if $smarty.session.member_pFeedBackModify!='0'}>
                                        <tr>
                                            <th>簽約日</th>
                                            <td><input type="text" name="signDate" class="datepickerROC" value="<{$data.bSignDate}>"></td>
                                            <th>回饋比率︰</th>
                                            <td>
                                                <input type="text" name="recall" maxlength="10" class="input-text-big" value="<{$data.bRecall}>" />%
                                            </td>
                                        </tr>
                                        <tr><th>回饋指定店家</th>
                                            <td colspan="5"><{html_options name=TargetBranch options=$menuBranch selected=$data.bBranch style="width:80%"}></td>
                                        </tr>
                                        <{/if}>
                                    </table>
                                </div>
                            </div>
                            <center>
                            <br/>
                            <{if $is_edit == 1}>
                            <button id="save">儲存</button>
                            <button id="delete">刪除</button>
                            <{else}>
                            <button id="add">儲存</button>
                            <{/if}>
                            </center>
                            <form name="form_back" id="form_back" method="POST"  action="listbrand.php"></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="footer">
            <p>2012 第一建築經理股份有限公司 版權所有</p>
        </div>
    </body>
</html>










