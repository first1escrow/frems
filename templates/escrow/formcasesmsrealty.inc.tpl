<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {

                if ("<{$cCaseStatus}>" == 3) {

                    var array = "input,select,textarea";
                    
                    $("#content").find(array).each(function() {
                        $(this).attr('disabled', true);
                        
                    }); 

                }
                
                var count=1;

                $('#cancel').live('click', function () {
                     $('#form_back').submit();
                 });
                $('#save').live('click', function () {


                    for (var i = 1; i <= count; i++) {
                        if ($('#phone1').val()!='') {
                             $('#phone'+i).css('background','#FFFFFF');
                             var phone = $('#phone'+i).val();
                             var msg = 0;
                             if ((!/^09\d{8}$/.test(phone))) {
                                 msg =1 ;
                                 $('#phone'+i).css('background','#E4BEB1');
                             }
                        };
                       
                    };
                    if(msg==1)
                    {
                        alert('手機號碼有誤');
                        return false;
                    }
                   
                    $("#form_sms").submit();
                });
                $('#count').live('click', function () {
                    $('tbody#item');
                });

                 $('[name="new_add"]').live('click', function () {
                     count++;
                   $("#new").clone().insertBefore("#lot");
                   $("[name='smsphone[]']:last").attr('id','phone'+count);
                   $("[name='add[]']:last").attr('value',count);
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
               $('[name="new_add"]').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('[name="delete"]').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                <{$dialog}>

                
            });
            function delete_phone(mol,id,branch)
                {
                    var url='branch_mobile_delete.php';
                    var txt='是否要刪除';

                        if(confirm(txt))
                        {
                            $.post(url,{'mol':mol,'id':id,'branch':branch},function(txt) {

                               location.href=txt;
                               // console.log(txt);

                                 }) ;
                        }
                        else
                        {
                            
                        }
                       
                   

                }
        </script>
        <style type="text/css">
            #tabs {
                width:450px;
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
                width:450px;
            }

            #detail {
                margin-left:auto; 
                margin-right:auto;
                width:450px;
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
            .split td{
                border-bottom:1px dotted #BBBBBB;
                padding-top:5px;
                
            }
        </style>
    </head>
    <body id="dt_example">
            <div id="content">
                <div id="tabs">
                    <div id="tabs-contract">
                        <form id="form_sms" action="formcasesmsrealtysave.php" method="POST" >
                                    <input type="hidden" name="certified_id" value="<{$certified_id}>">
                                    <input type="hidden" name="bBranch" value="<{$bBranch}>">
                                    <input type="hidden" name="index" value="<{$index}>">
                        <table border="0" width="450">
                            <tbody id="item">

                            <tr>
                                <th width="8%"><center>選擇</center></th>
                                <th width="20%"><center>職稱</center></th>
                                <th width="36%"><center>姓名</center></th>
                                <th width="36%"><center>手機</center></th>
                            </tr>                           
                               
                                        <{foreach from=$book key=key item=item}>
                                    <tr class="split">
                                        <td align="center">
                                            <{if $item.isSelect == 1}>
                                                <input type="checkbox" name="isSelect[]" value="<{$item.bMobile}>" checked />
                                            <{else}>
                                                <input type="checkbox" name="isSelect[]" value="<{$item.bMobile}>" />
                                            <{/if}>
                                        </td>
                                        <td><{$item.tTitle}></td>
                                        <td><{$item.bName}></td>
                                        <td>   
                                            <{$item.bMobile}>
                                        <{if $item.bCheck_id != 0}>
                                            <a href="#" onclick="delete_phone(<{$item.bMobile}>,<{$item.bId}>,<{$item.branch}>)" style="font-size:10px ">刪除</a>
                                            <{/if}> 
                                        </td>
                                        </tr>
                                            <{/foreach}>
                                         <tr class="split" id="new">
                                           <td align="center">
                                                <input type="checkbox" name="add[]" value="1">
                                            </td>
                                            <td>
                                               <{html_options name="title[]" options=$option_title}>
                                            </td>
                                            <td>
                                                <input type="text" name="smsname[]" size="8">
                                            </td>
                                            <td>
                                                <input type="text" name="smsphone[]" size="10" maxlength="10" id="phone1">
                                            </td>
                                        </tr>
                                        <tr id="lot">
                                            <td align="right" colspan="4">
                                            <input type="button" name="new_add" value="增加欄位">

                                            </td>
                                        </tr>
                                        <tr><td id="test"></td></tr>
                                
                            </tbody>
                           
                        </table>
                        </form>
                            <br/>
                            <center>
                             <{if $cSignCategory == 1}>
                                            <{if $cCaseStatus == 2 }>
                                            <button id="save">儲存</button>
                                            <{/if}>
                                        <{/if}>
                            </center>
                    </div>
                    <form name="form_back" id="form_back" method="POST"  action="formbuyowneredit.php">
                    <input type="hidden" name="id" value="<{$cCertifiedId}>">
                    </form>
                </div>
            </div>
        <div id="dialog"></div>
    </body>
</html>










