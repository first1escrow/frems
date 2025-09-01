<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php';
include_once 'class/contract.class.php';
include_once '../session_check.php' ;
include_once 'class/getAddress.php' ;

$_POST = escapeStr($_POST) ;
$contract = new Contract();
$advance = new Advance();
$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];

$zip = $_GET['zip'];
$addr = $_GET['addr'];
$limit = $_GET['limit'];
$edit = $_GET['edit'];
// if (!empty($_POST['cat'])) {
    
// }


##
?>
<!DOCTYPE html>
<html>
<head>  
    <meta charset="UTF-8">
    <title>匯入案件資料</title>
    <script src="/js/jquery-1.10.2.min.js"></script>
    <script src="/js/IDCheck.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // $("#copyBtn").attr('disabled','disabled');

        });


        function go(){
                // var cId = $('[name="certifiedid_view"]', window.parent.document).val();
                // var checkid = $('[name="certifiedid_view"]', window.parent.document).val();

                if (!confirm("確認否要匯入?")) {
                    return false;
                }

                if ("<?=$edit?>" == 0) {
                    var newId = $('[name="checkAcc"]', window.parent.document).val();
                }else{
                    var newId = $('[name="scrivener_bankaccount2"]', window.parent.document).val();
                }
                
                var reg = /.*\[]$/ ;
                var input = $('input');
                var arr_input = new Array();

                

                if (newId == '' || newId == undefined) {
                    alert("請先至合約書輸入保證號碼，再使用此功能");
                    return false;
                };
                $('[name="scrivener_bankaccount"]').val(newId);
                // $("[name='oCertifiedId']").val("");
                
                // $data_case
                $.each(input, function(key,item) {

                    if(reg.test($(item).attr("name"))){
                        if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                 if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }

                           
                        }else{
                             if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                            
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        }
                    }else if ($(item).is(':checkbox')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = '1';
                        }
                        else {
                            arr_input[$(item).attr("name")] = '0';
                        }
                    }else if ($(item).is(':radio')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = $(item).val();
                        }
                    }else {
                         // console.log($(item).attr("name"))
                        arr_input[$(item).attr("name")] = $(item).val();
                    }
                    
                });

                 var obj_input = $.extend({}, arr_input);
                 // console.log(obj_input);
                $.ajax({
                    url: 'copyCaseUpdate.php',
                    type: 'POST',
                    dataType: 'html',
                    data: obj_input,
                })
                .done(function(msg) {
                    
                    if (msg) {
                        // console.log(newId.substr(-9));//msg.trim()
                        $('form[name=reload] input[name=id]', window.parent.document).val(newId.substr(-9));
                        $('form[name=reload]', window.parent.document).attr('action', 'formbuyowneredit.php');
                        $('form[name=reload]', window.parent.document).submit();
                        
                        // parent.$.fn.colorbox.close();//關閉視窗
                    }
                });
                
                
        }
        function showCase(){
            var id = $("[name='oCertifiedId']").val();

            $("form[name=showC] input[name=id]").val(id);
            // console.log( $("form[name=showC] input[name=id]").val());
            $("form[name=showC]").submit();
        }

        function checkCertifiedId(){
            var url = 'id_conv_scr.php' ;
            var id = $('[name="oCertifiedId"]').val();
                    // console.log(id);
            $.post(url,{'cid':id},function(txt) {

                var obj = jQuery.parseJSON(txt);
                    // console.log(txt);
                if (obj.status == 'ng') { //
                        // var arr = txt.split('_') ;
                        $('#showcheckId').html(obj.statusMsg) ;
                        $("[name='case']").attr('style', 'display:none');
                        
                }else{
                    var arr = txt.split('_') ;
                        
                        if (obj.status == 'ok') {
                            $("[name='case']").attr('style', 'display:none');
                            $('#showcheckId').html('<span style="color:#000080;font-weight:bold;">'+obj.statusMsg+'</span>') ;
                    
                        }else{
                            $('#showcheckId').html('');
                            $("[name='case']").attr('style', '');
                        }
                    
                    // $('#showcheckId').html(arr[3]+'&nbsp;<span style="color:#000080;font-weight:bold;">'+arr[2]+'</span>&nbsp;<span style="color:#FF0000;font-weight:bold;">'+arr[4]+'</span>') ;
                    
                    
                    
                        
                }
                    // console.log(txt);
            }) ;
        }

  
    </script>
    <style>
        th{
            color: rgb(255, 255, 255);
            font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
            font-size: 1em;
            font-weight: bold;
            background-color: rgb(156, 40, 33);
            padding: 6px;
             border: 1px solid #CCCCCC;
        }
        td{
            color: rgb(51, 51, 51);
            font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
            font-size: 100%;
            padding: 6px;
            border: 1px solid #CCCCCC;
            /*text-align: left;*/
        }
    </style>
</head>
<body>
<center></center>
    
    <form action="formbuyowneredit.php" method="POST" target="_blank" name="showC">
        <input type="hidden" name="id" value="">
<!--        <table cellspacing="0" cellpadding="0" border="0" width="70%" align="center">
        <tr>
            <td colspan="6">建物對應案件</td>
        </tr>
        <tr>
            <th width="20%">保證號碼:</th>
            <td width="30%">    
                <?=$id?>
                <input type="hidden" name="id" value="<?=$id?>">
                
            </td>
            <th width="20%">案件狀態:</th>
            <td width="30%">
                <?=$status?>
            </td>
        </tr>
        <tr>
            <th>買方姓名:</th>
            <td><?=$data_buyer['cName']?></td>
            <th>賣方姓名:</th>
            <td><?=$data_owner['cName']?></td>
        </tr>
        <tr>
            <th>仲介店:</th>
            <td colspan="3"><?=$branch?></td>
        </tr>
        <?php
        if ($branch1) {
        ?>
        <tr>
            <th>仲介店:</th>
            <td colspan="3"><?=$branch1?></td>
        </tr>
        <?php
        }
        if ($branch2) {
        ?>
        <tr>
            <th>仲介店:</th>
            <td colspan="3"><?=$branch2?></td>
        </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="4" align="center"></td>
        </tr>
        </table> -->
    </form>
    <br>
    <form action="" method="POST" name="form">
        

        <table cellspacing="0" cellpadding="0" border="0" width="70%" align="center">
            
            <tr>
                <th width="20%">被複製的保證號碼：</th>
                <td>
                    <input type="text" name="oCertifiedId"  maxlength="9" onkeyup="checkCertifiedId()">
                    <input type="button" value="觀看案件" name="case" onclick="showCase()" style="display:none">
                    <input type="hidden" name="scrivener_bankaccount" >
                    <input type="hidden" name="edit" value="<?=$edit?>">
                    <span id="showcheckId"></span>
                    <!-- <input type="text" name="certifyId" maxlength="9" onkeyup="checkCertifiedId()">
                    <input type="hidden" name="scrivener_bankaccount">
                    <br>
                     -->
                </td>
            </tr>
            <tr>
                <th width="20%">匯入頁籤：</th>
                <td>
                    
                    
                <!--     -->

                    <!-- <input type="checkbox" name="cat[]" id="" value="1">案件明細表(只複製客服內容) -->
                    <input type="checkbox" name="cat[]" id="" value="2">合約書
                    <input type="checkbox" name="cat[]" id="" value="3">土地
                    <input type="checkbox" name="cat[]" id="" value="4">建物
                    <!-- <input type="checkbox" name="cat[]" id="" value="5">地政士(保證號碼會自動對應地政士) -->
                    <input type="checkbox" name="cat[]" id="" value="6">仲介
                    <input type="checkbox" name="cat[]" id="" value="7">買方
                    <input type="checkbox" name="cat[]" id="" value="8">賣方
                    
                    <input type="button" value="複製" id="copyBtn" onclick="go()">
                    
                </td>
                
            </tr>
            
        </table>
        </form>
        
    <!--  -->
</body>
</html>