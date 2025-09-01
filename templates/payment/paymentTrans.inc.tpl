<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>斡旋支付出款</title>
    <link rel="stylesheet" href="/css/colorbox.css" />
    <link rel="stylesheet" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css">
    <script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
    <script src="/js/jquery.colorbox.js"></script>
    <script src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script>
        $(document).ready(function() {
            // $(".tab a").click(function(){
            //     $(".tab a").css({"background-color":"#FF6600","border-color":"#FF6600",});
            //     $(this).css({"background-color":"#333333","border-color":"#333333"});

                
            // });
        });
        function auth(){
            var input = $('input');
            var arr_input = new Array();
            var reg = /.*\[]$/ ;

            $.each(input, function(key,item) {
                if ($(item).is(':checkbox')) {
                    if ($(item).is(':checked')) {
                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                    }
                } else if ($(item).is(':radio')) {
                    if ($(item).is(':checked')) {
                        arr_input[$(item).attr("name")] = $(item).val();
                    }

                } else {
                    if (reg.test($(item).attr("name"))) {

                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }
                                            
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();

                    }else{
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                                        
                    }    
                }
            });
            var obj_input = $.extend({}, arr_input);
            var request = $.ajax({  
                                    url: "export_all.php",
                                    type: "POST",
                                    data: obj_input,
                                    dataType: "html"
                                });
            request.done( function( msg ) {
                alert(msg);
                location.reload();
               // console.log(msg);
               // location.href="list.php";
                                    
            });
        }
        function checkALL(bId){
            // var cb = $("[name='bank"+bId+"']").prop('checked');
             if ($("[name='bank"+bId+"']").prop('checked') == true) {
                $(".cb"+bId).prop('checked', true);
            }else{
                $(".cb"+bId).prop('checked', false);
            }
        }
        function exportfile(bId){

            var loansdate = $('[name="datepicker_'+bId+'"]').val() ;


            var input = $('input');
            var arr_input = new Array();
            var reg = /.*\[]$/ ;

            $.each(input, function(key,item) {
                if ($(item).is(':checkbox')) {
                    if ($(item).is(':checked')) {
                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                    }
                } else if ($(item).is(':radio')) {
                    if ($(item).is(':checked')) {
                        arr_input[$(item).attr("name")] = $(item).val();
                    }

                } else {
                    if (reg.test($(item).attr("name"))) {

                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }
                                            
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();

                    }else{
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                                        
                    }    
                }
            });
            var obj_input = $.extend({}, arr_input);
            var request = $.ajax({  
                                    url: "export_all.php?x="+bId+"&l=" + loansdate,
                                    type: "POST",
                                    data: obj_input,
                                    dataType: "html"
                                });
            request.done( function( msg ) {
                // alert(msg);
                //
               // console.log(msg);
               window.open('download.php?path='+encodeURI(msg)," ",config='height=300,width=300');
               location.reload();
               // $('[name="output"]').attr('action',msg);
               // $('[name="path"]').val(msg);
               // $('[name="output"]').submit();
               // location.href="list.php";
                                    
            });
             
            // $.colorbox({    
            //     iframe:true, width:"1100", height:"500",                        
            //     href: "export_all.php?x="+bId+"&l=" + loansdate,
            //     onClosed:function(){ //reload_page();
            //     }
            // });
        }
    </script>
</head>
<style>
    
    th{
        background-color:#a63c38;
        border: 1px solid black;
        color: white;
        width: 10%
        padding:3px;
    }
    td{
        background-color:white;
        border: 1px solid black;
         padding:3px;      
    }
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
    


</style>
<body>
    <form action="download.php" name="output" target="_blank" method="POST">
        <input type="hidden" name="path" id="">
    </form>
    <{include file='paymentMenu.inc.tpl'}>
    <{foreach from=$contractBank key=key item=item}>
    <div style="font-weight:bold;color:#002060;padding: 5px;"><input type="checkbox" name="bank<{$item.cId}>" id="" onclick="checkALL(<{$item.cId}>)">～～～&nbsp;<{$item.cBankName}>案件&nbsp;～～～</div>
    
    <{foreach from=$BankTransData[<{$key}>] key=k item=data}>
        <table border="0"  width="70%" class="tb" cellpadding="0" cellspacing="0" >
            <tbody>
                <tr>
                    <td rowspan="3">
                        <input type="checkbox" name="check[]" value="<{$data.tId}>" class="cb<{$item.cId}>">
                    </td>
                    <th>類別</th>
                    <td><{$data.tKind}></td>
                   
                    <th>解匯行</th>
                    <td><{$data.bank}></td>
                   
                    <th>戶名</th>
                    <td><{$data.tAccountName}></td>
                    
                    <th>帳號</th>
                    <td><{$data.tAccount}></td>
                    <th>金額</th>
                    <td>NT$ <{$data.tMoney}>元(不含匯費)</td>
                </tr>
                <tr>
                     <th>交易類別</th>
                    <td>
                       <{$data.tCode2}>
                    </td>
                     <th>分行別</th>
                    <td><{$data.bankbranch}></td>
                    <th>證號</th>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <th>附言(備註)</th>
                    <td colspan="3"><{$data.tPayTxt}></td>

                    <td colspan="6"><{$data.tObjKind}></td>

                   
                </tr>

               
            </tbody>
        </table>
        <hr style="width: 70%;float: left;">
    <{/foreach}>
    <{if $cat == 1}>
        <div style="clear:both;"></div>
        <div><input type="button" value="產生媒體檔" onclick="exportfile(<{$item.cId}>)">銀行放款日：
                        <input type="text" name="datepicker_<{$item.cId}>" class="dt" style="width:100px;" value="<{$eDay}>" readonly /></div>
    <{/if}>
    <{/foreach}>
    <div style="clear:both;"></div>
<div>
    <{if $cat == 2}>
        <input type="button" value="審核通過" onclick="auth()" />    
    <{/if}>
</div>
</body>
</html>