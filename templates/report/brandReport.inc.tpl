<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=11"/>

        <{include file='meta2.inc.tpl'}>

        <script type="text/javascript">
        $(document).ready(function() {
            $('#dialog').dialog('close');
            $(".ajax").colorbox({width:"400",height:"100"});

            $('#citys').combobox({
                onChange: function (newValue, oldValue) {
                    if (newValue == 0) {
                    }

                    var url = '../includes/getZip.php?city='+newValue+'&type=json';
                    
                    $('#areas').combobox('reload', url);
                    $('#areas').combobox('setValue', '0');
                }
            });

            $('#areas').combobox({
                onChange: function (newValue, oldValue) {
                    $('#zip').val(newValue) ;
                }
            });
        });

        function downloadXls(){
            $("[name='xls']").val(1);
            $('[name="form"]').attr('action', 'brandReport_result.php');
            $('[name="form"]').submit();
        }

        function search() {
            $( "#dialog" ).dialog("open") ;
            $("[name='xls']").val('');
            var reg = /.*\[]$/ ; //reg.test($(item).attr("name"))
            var input = $('input');
            var textarea = $('textarea');
            var select = $('select');
            var arr_input = new Array();

            $.each(select, function(key,item) {
                if (reg.test($(item).attr("name"))) {                        
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();            
                    }                           
                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();                    
                } else {
                    arr_input[$(item).attr("name")] = $(item).val();
                }
            });
            
            $.each(textarea, function(key,item) {
                arr_input[$(item).attr("name")] = $(item).attr("value");
            });

            $.each(input, function(key,item) {
                if(reg.test($(item).attr("name"))){
                    if ($(item).is(':checkbox')) {
                        if ($(item).is(':checked')) {
                            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                            
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        }
                    } else {
                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }

                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                    }
                } else if ($(item).is(':checkbox')) {
                    if ($(item).is(':checked')) {
                        arr_input[$(item).attr("name")] = '1';
                    } else {
                        arr_input[$(item).attr("name")] = '0';
                    }
                } else if ($(item).is(':radio')) {
                    if ($(item).is(':checked')) {
                        arr_input[$(item).attr("name")] = $(item).val();
                    }
                } else {
                    arr_input[$(item).attr("name")] = $(item).attr("value");
                }
            });

            var obj_input = $.extend({}, arr_input);
            $.ajax({
                url: 'brandReport_result.php',
                type: 'POST',
                dataType: 'html',
                data: obj_input
            })
            .done(function(msg) {
                $("#searchList").html(msg);
                $('#dialog').dialog('close');
            });
        }

        function showTimeArea(val) {
            $(".timeArea").each(function() {
                $(this).hide();
            });

            if (val == 's') {
                $("[name='showSeason']").show();
            } else if (val == 'm') {
                $("[name='showMonth']").show();
            }
        }

        function add(cat) {
            var val = $('[name="'+cat+'Menu"]').val();

            if (val != 0) {
                var text = $('#'+cat+'Menu option[value="'+val+'"]').text(); 
                $("#"+cat+"Show").append('<div id="'+cat+val+'" class="addStore bStore"><input type="hidden" name="'+cat+'[]" value="'+val+'"><a href="#" onClick="del(\''+cat+'\','+val+')" >(刪除)</a>'+text+'</div>');
            }
        }

        function del(cat,id) {
            $("#"+cat+id).remove();
        }
        </script>
        
        <style>
        .small_font {
            font-size: 9pt;
            line-height:1;
        }
        input.bt4 {
            padding:4px 4px 1px 4px;
            vertical-align: middle;
            background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
        }
        input.bt4:hover {
            padding:4px 4px 1px 4px;
            vertical-align: middle;
            background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
        }
        
        #dialog {
            background-image:url("/images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
            width: 300px; 
            height: 30px;
        }
        .easyui-combobox{
            width: 300px;
        }
        .block{
            border: 1px solid #CCC;
            /*background-color: #999;*/
        }
        .row_title{
            background-color: #8F0000;
            color:white;
            padding: 5px;
        }
        .row_contant{
            padding: 5px;
            border: 1px solid  #999;
        }
        .row_contant_left{
            padding: 0px;
            float: left;
            display:inline;
            width: 100%;
            border: 1px solid  #999;
        }
        .row_contant_right{
            float: left;
            display:inline;
            width: 49%;
            border: 1px solid  #999;
            padding: 0px;
        }

        /*button*/
        .xxx-button {
            color:#FFFFFF;
            font-size:12px;
            font-weight:normal;
            
            text-align: center;
            white-space:nowrap;
            height:20px;
            
            background-color: #a63c38;
            border: 1px solid #a63c38;
            border-radius: 0.35em;
            font-weight: bold;
            padding: 0 20px;
            margin: 5px auto 5px auto;
        }
        .xxx-button:hover {
            background-color:#333333;
            border:1px solid #333333;
        }
        ul.tabs {
            width: 100%;
            height: auto;
            border-left: 0px solid #999;
            border-bottom: 1px solid #D99888;
            
        }  
        ul.tabs li {
                margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
            height: auto;
        }

        .tb{
            /*padding: 10px;*/
            border:solid #CCC 1px;
        }

        .tb th{
            color: #FFF;
            background-color: #8F0000;
            padding: 5px;
            border: 1px solid #fff;
        }

        .tb td{
            color: #000;
            background-color: #FFF;
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
                                    <div id="menu-lv2"></div>
                                    <br/> 
                                    <h3>&nbsp;</h3>
                                    <div id="container">

                                        <div>
                                            <form name="form" method="POST">
                                                <h1>各品牌店家名單</h1>
                                                <div id="dialog" class="easyui-dialog" style="display:none"></div>
                                                
                                                <div class="block">
                                                    <div class="row_title">搜尋條件</div>
                                                    <div></div>
                                                    <div class="row_contant">
                                                        地區
                                                            <select name="city" id="citys"  style="width:100px;" >
                                                                <{foreach from=$menu_city key=key item=item}>
                                                                <option value="<{$key}>"><{$item}></option>
                                                                <{/foreach}> 
                                                            </select>
                                                            <select name="area" id="areas"  style="width:100px;" data-options="valueField:'id',textField:'text'">
                                                                <option value="">全部</option>
                                                            </select>
                                                            <input type="hidden" name="zip">
                                                        
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                    <div class="row_contant">
                                                        品牌
                                                            <select name="brandMenu" id="brandMenu" class="easyui-combobox" data-options="
                                                                    valueField: 'id',
                                                                    textField: 'text'
                                                                    " style="width:300px;">
                                                                <{foreach from=$menu_brand key=key item=item}>
                                                                <option value="<{$key}>"><{$item}></option>
                                                                <{/foreach}> 
                                                            </select> 
                                                            <input type="button" value="增加" onclick="add('brand')" class="xxx-button"><br>
                                                            <font color="red">※查詢請務必按下增加</font>
                                                            <div id="brandShow"></div>
                                                    </div>
                                                    <div class="row_contant">
                                                        店家
                                                            <select name="branchMenu" id="branchMenu" class="easyui-combobox" style="width:300px;">
                                                                <{foreach from=$menu_branch key=key item=item}>
                                                                <option value="<{$key}>"><{$item}></option>
                                                                <{/foreach}> 
                                                            </select> 
                                                            <input type="button" value="增加" onclick="add('branch')" class="xxx-button"><br>
                                                            <font color="red">※查詢請務必按下增加</font>
                                                            <div id="branchShow"></div>
                                                    </div>
                                                </div>
                                                
                                                <center>
                                                    <div >
                                                        <input type="button" value="查詢" onclick="search()" class="xxx-button">
                                                        <input type="button" value="下載EXCEL" onclick="downloadXls()" class="xxx-button">
                                                        <input type="hidden" name="xls">
                                                    </div>
                                                </center>

                                                <div class="block" id="searchList">
                                                </div>
                                                <hr>
                                                <div id="showData">
                                                </div>
                                            </form>
                                        </div>

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
        </div>
    </body>
</html>