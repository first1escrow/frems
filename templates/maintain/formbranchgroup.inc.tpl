<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
              
             
                $('#add').live('click', function () {
                    $('#add').hide();//禁止使用者多按
                   
                   
                    var input = $('input');
                    var textarea = $('textarea');
                    var select = $('select');
                    var arr_input = new Array();
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
                    var obj_input = $.extend({}, arr_input);
                    var request = $.ajax({  
                            url: "/includes/maintain/branchgroupadd.php",
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

                    // $('#save').hide();//禁止使用者多按
                   
                    
                    var input = $('input');
                    var textarea = $('textarea');
                    var select = $('select');
                    var arr_input = new Array();
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
                    var obj_input = $.extend({}, arr_input);
                    var request = $.ajax({  
                            url: "/includes/maintain/branchgroupsave.php",
                            type: "POST",
                            data: obj_input,
                            dataType: "html"
                        });
                        request.done( function( msg ) {
                           
                                alert(msg);
                                $('#form_back').submit();
                            
                        });
                });

                $('#del').live('click', function (){
                    if (confirm('是否要刪除?')) {
                        $.ajax({
                            url: '/includes/maintain/branchgroupdel.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {'id': "<{$data.bId}>"},
                        })
                        .done(function(txt) {
                           alert(txt);
                           $('#form_back').submit();
                        });
                    }
                    

                    // 
                });
                
                $( "#tabs" ).tabs({
                    selected: 0
                });
                
                
               
                $('#save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#del').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#buyer_edit').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#owner_edit').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                
            });
        </script>
        <style type="text/css">
           
            .tb1 {
               width:980px;
               margin-left:auto; 
               margin-right:auto;
            }

            .tb1 th {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
            }
            
           
            .tb2 {
               width:950px;
               margin-left:auto; 
               margin-right:auto;
            }

            .tb2 th {
                text-align:center;
                background: #E4BEB1;
                padding-top:8px;
                padding-bottom:8px;
                border:solid 1px #CCC;
            }
            .tb2 td {
                border:solid 1px #CCC;
                background: #FFFFFF;
                padding-top:5px;
                padding-bottom:5px;
                padding-left: 8px;
            }
            
        </style>
    </head>
    <body id="dt_example">
        <form action=""></form>
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
            <ul>
                <li><a href="#tabs-contract">仲介群組維護</a></li>
            </ul>
            <div id="tabs-contract">
                <table border="0" width="100%" class="tb1">
                    <tr>
                        <td width="20%"></td>
                        <td width="30%"></td>
                        <td width="20%"></td>
                        <td width="30%"></td>
                        
                    </tr>
                    <tr>
                        <th>仲介群組名稱︰</th>
                        <td>
                          <input type="text" name="name" id="" value="<{$data.bName}>">
                          <input type="hidden" name="id" value="<{$data.bId}>">
                        </td>
                         <th>仲介群組公司名稱︰</th>
                        <td>
                           <input type="text" name="store" id="" value="<{$data.bStore}>">
                        </td>
                       
                    </tr>
                    <tr>
                        <th>帳號:</th>
                        <td><input type="text" name="account" value="<{$data.bAccount}>"></td>
                        <th>密碼</th>
                        <td><input type="password" name="password" value="<{$data.bPassword}>"></td>
                    </tr>
                    <!-- <tr>
                        <th>官網預設顯示店家</th>
                        <td colspan="3"><{html_options name=webBranch options=$menuBranch2 selected=$data.bWebBranch style="width:50%"}></td>
                    </tr> -->
                    <{if $smarty.session.member_pFeedBackModify!='0'}>
                    <tr>
                        <th>回饋比例</th>
                        <td ><input type="text" name="bRecall" value="<{$data.bRecall}>">%</td>
                        <th>回饋指定店家</th>
                        <td><{html_options name=TargetBranch options=$menuBranch selected=$data.bBranch style="width:80%"}></td>
                    </tr>
                    <tr>
                        <th>簽約日期</th>
                        <td><input type="text" name="signDate" class="datepickerROC" id="" value="<{$data.bSignDate}>"></td>
                    </tr>
                    <{/if}>
                   
                </table>
               
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tb2">
                    <tr>
                        <th>編號</th>
                        <th>仲介店名</th>
                        <th>公司名稱(法人名稱)</th>
                    </tr>
                    <{foreach from=$list key=key item=item}>
                        <tr >
                            <td align="center" style="background-color: <{$item.close}>"><{$item.bCode}></td>
                            <td style="background-color: <{$item.close}>"><{$item.brand}><{$item.bStore}></td>
                            <td style="background-color: <{$item.close}>"><{$item.bName}></td>
                        </tr>
                    <{/foreach}>
                </table>
            </div>
        </div>
    <center>
        <br/>
        <{if $is_edit == 1}>
        <button id="save">儲存</button>
        &nbsp;&nbsp;
        <button id="del">刪除</button>
        <{else}>
        <button id="add">儲存</button>
        <{/if}>
    </center>
    <form name="form_back" id="form_back" method="POST"  action="listbranchgroup.php">
    </form>
                            </div>
                        </div>
                    </div></div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>










