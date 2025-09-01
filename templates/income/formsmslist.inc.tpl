<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <link rel="stylesheet" href="/css/colorbox.css" />
        <script src="/js/jquery.colorbox.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
               
                $('#save').on('click', function () {
                    var input = $('input');
                    var textarea = $('textarea');
                    var arr_input = new Array();
                    var reg = /.*\[]$/ ;

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
                                    // console.log($(item).attr("name"));
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
                            }else {
                                arr_input[$(item).attr("name")] = '0';
                            }
                        }else if ($(item).is(':radio')) {
                            if ($(item).is(':checked')) {
                                arr_input[$(item).attr("name")] = $(item).val();
                            }
                        }else {
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }
                    });

                    var obj_input = $.extend({}, arr_input);
                    var check = false;
                    if (arr_input['normal_sms[]'] || arr_input['owner_sms[]']) {
                        check = true;
                    
                    }

                    if (!check) {
                        alert('請選擇對象');
                        return false;
                    }

                    if (confirm("確定要發送簡訊通知？")==true) {
                        $.ajax({
                            url: 'formsms.php',
                            type: 'POST',
                            dataType: 'html',
                            data: obj_input,
                        }).done(function(msg) {
                            // console.log(msg);
                        });

                         alert('簡訊已依序發送中');
                                parent.$.colorbox.close();
                    }
                    


                    // var normal = new Array();
                    // $('input:checkbox:checked[name="normal_sms[]"]').each(function(i) { cbxVehicle[i] = this.value; });
                    // var owner


                    // owner_sms
                    // var txt = $("[name='sms_txt']").val();

                    // if (cbxVehicle[0]==undefined) {
                    //     alert('請選擇對象');
                    //     return false;
                    // }else{
                    //     if (confirm("確定要發送簡訊通知？")==true) {

                            

                    //         $.ajax({
                    //             url: 'formsms.php',
                    //             type: 'POST',
                    //             dataType: 'html',
                    //             data: {'id':"<{$id}>",'cid':"<{$cid}>",'mail':'1','all':cbxVehicle,'txt':txt},
                    //         })
                    //         .done(function(msg) {
                    //             console.log(msg)
                               
                    //         });

                            
                            
                           
                          
                    //     }
                    // }

                    

                     
                            
                });
                $('#cancel').live('click', function () {
                    parent.$.colorbox.close();

                });
                $('#save').button( {
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
            function clickAll(cat){
                if (cat == 1) {
                    var dom = $("[name='normal_sms[]']");
                }else{
                    var dom = $("[name='owner_sms[]']");
                }
               
             
                if ($("[name=all"+cat+"]").prop("checked") == true) {
                    dom.prop('checked', true);
                }else{
                    dom.prop('checked', false);
                }
            }
         
        </script>
        <style type="text/css">
            body{
                text-align: center;
                font-size: 16px;
            }
            #tabs {
                width:80%;
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
                font-size: 12px;
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

           
            .content td{
                padding-top:5px;
                font-size: 14px;
            }
            .sms_block{
                border: 1px #CCC solid;
            }
            .sms_block_title{
                font-family: "微軟正黑體","新細明體";
                font-size: 1.2em;
                font-weight: normal;
                line-height: 1.6em;
                color: red;
            }
        </style>
    </head>
    <body >
        <form action="sms_list.php" method="POST" name="form" id="form">
            <div id="content">
                <div id="tabs">
                    <div id="tabs-contract">
                        <input type="hidden" name="id" value="<{$id}>">
                        <input type="hidden" name="cid" value="<{$cid}>">
                        <input type="hidden" name="mail" value="1">
                        <div class="sms_block">
                        <div class="sms_block_title">一般簡訊內容</div>
                        <table border="0" width="100%">
                            <tr>
                                <td colspan="4"><textarea name="normal_sms_txt" id="" cols="90" rows="3"><{$list['normal_sms_txt']}></textarea></td>
                            </tr>
                            <tr>
                                <th width="10%"><center>選擇<input type="checkbox" name="all1" onclick="clickAll(1)" checked></center></th>
                                <th width="10%"><center>身份</center></th>
                                <th width="30%"><center>姓名</center></th>
                                <th width="30%"><center>手機</center></th>
                            </tr>
                            <{foreach from=$list['normal'] key=key item=item}>
                                <tr>
                                    <td align="center"><input type="checkbox" name="normal_sms[]" value="<{$item.mMobile}>" checked></td>
                                    <td align="center"><{$item.tTitle}></td>                                                   
                                    <td align="center"><{$item.mName}></td>
                                    <td align="center"> <{$item.mMobile}></td>
                                </tr>
                                                
                            <{/foreach}>
                        </table>
                        </div>
                        <br/>
                        <div class="sms_block">
                            <div class="sms_block_title">賣方簡訊內容</div>
                            
                        <table border="0">
                            
                            <tr>
                                <td colspan="4"><textarea name="owner_sms_txt" id="" cols="90" rows="3"><{$list['owner_sms_txt']}></textarea></td>
                            </tr>
                            <tr>
                                <th width="10%"><center>選擇<input type="checkbox" name="all2" onclick="clickAll(2)" checked></center></th>
                                <th width="10%"><center>身份</center></th>
                                <th width="30%"><center>姓名</center></th>
                                <th width="30%"><center>手機</center></th>
                            </tr>
                            <{foreach from=$list['owner'] key=key item=item}>
                                    <tr>
                                        <td align="center"><input type="checkbox" name="owner_sms[]" value="<{$item.mMobile}>" checked></td>
                                        <td align="center"><{$item.tTitle}></td>                                                   
                                        <td align="center"><{$item.mName}></td>
                                        <td align="center"> <{$item.mMobile}></td>
                                    </tr>
                                                    
                            <{/foreach}>
                        </table>
                        
                       
                    </div>
                    <{if $list['owner_sms_txt'] == ''}>
                        <div style="color:red">因款項只有買方的部分，所以賣方不發送</div>
                    <{/if}>
                    <div style="margin-top:30px;">
                         <center>
                            <button id="save">送出</button>
                            <button id="cancel">取消</button>
                       </center>
                    </div>

                   
                </div>
            </div>
        </form>
    </body>
</html>










