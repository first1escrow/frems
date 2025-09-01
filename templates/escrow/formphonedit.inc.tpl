<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#save').live('click', function () {
                    let url = "formphonsave.php";
                    let cid = $('[name="certified_id"]').val();
                    let cateogry = $('[name="cateogry"]').val();                
                    let new_phone = $("[name='new_phone']").val();
                    let others_id = $('[name="others_id"]').val();

                    let phone = new Array();
                    let id = new Array();
                    $('input:[name="phone[]"]').each(function(i) {
                        phone[i] = this.value; 
                    });

                    $('input:[name="pid[]"]').each(function(i) {
                        id[i] = this.value; 
                    });

                    $.post(url, {'cid':cid, 'cateogry':cateogry, 'phone':phone, 'new_phone':new_phone, 'id':id, 'others_id': others_id}, function(txt) {
                        if (txt == 'ok') {
                            alert('儲存成功');
                            location.href='formphonedit.php?t=' + cateogry + '&cid=' + cid + '&cSignCategory=<{$cSignCategory}>&others_id=' + others_id;
                        }
                   });
                });

                $('#save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });
            });

            function delete_phone(id)
            {               
                let url = "formphondelete.php";
                let cateogry = $('[name="cateogry"]').val();
                let cid = $('[name="certified_id"]').val();
                let others_id = $('[name="others_id"]').val();
                
                $.post(url, {'id':id}, function(txt) {
                    if (txt=='ok') {
                        alert('刪除成功');
                        location.href='formphonedit.php?t=' + cateogry + '&cid=' + cid + '&cSignCategory=<{$cSignCategory}>&others_id=' + others_id;
                    }
                });
            }
        </script>
        <style type="text/css">
            #tabs {
                width:400px;
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

        </style>
    </head>
    <body id="dt_example">
        <div id="content">
            <div id="tabs">
                <div id="tabs-contract">
                    <form id="form_phone"  method="POST" >
                        <input type="hidden" name="certified_id" value="<{$cCertifiedId}>">
                        <input type="hidden" name="cateogry" value="<{$cateogry}>">
                        <input type="hidden" name="others_id" value="<{$others_id}>">
                    
                        <table border="0" width="450">
                            <tr>
                                <th colspan="2"><center>手機</center></th>
                            </tr>
                            <{foreach from=$data key=key item=item}>
                            <tr>
                                <td style="width: 360;">
                                    <input type="hidden" name="pid[]" value="<{$item.cId}>">
                                    <input type="text" style="width: 100%;" name="phone[]" id="" value="<{$item.cMobileNum}>" maxlength="10">
                                </td>
                                <td style="text-align: center;">
                                    <{if $cSignCategory == 1}>
                                    <input type="button" value="刪除" onClick="delete_phone(<{$item.cId}>)" class="ui-button ui-widget ui-state-default ui-corner-all">
                                    <{/if}>
                                </td>   
                                
                            </tr>
                            <{/foreach}>     
                            <tr>
                                <td><input type="text" style="width: 100%;" name="new_phone" id="" value="" maxlength="10"></td>
                                <td></td>
                            </tr>
                        </table>
                    </form>
                    <br/>
                    <center>
                        <{if $cSignCategory == 1}>
                        <button id="save">儲存</button>
                        <{/if}>
                    </center>
                </div>
                
            </div>
        </div>
        <div id="dialog"></div>
    </body>
</html>










