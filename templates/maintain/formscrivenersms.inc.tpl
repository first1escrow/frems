<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#dialog').dialog({
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        'OK': function() {
                            $(this).dialog('close') ;
                        }
                    }
                }) ;
                $('#save').live('click', function () {
                    if (checkNewSMS()) {

                        
                        if (confirm("要聯動至進行中的案件?")) {
                            $('[name="same"]').val(1);
                        }else{
                            $('[name="same"]').val('');
                        }

                        $('#form_sms').submit();
                    }
                });
                var row = 0;
                $("#addrow").on('click', function() {
                    var row2 = row+1;
                    // copy
                    // console.log('--');
                   
                    $("#copy:first").clone().insertAfter(".copy:last");
                    // $("#copy:last [name='sms_no[]']").val('sms_'+row);//sms_0
                    $(".copy:last [name='sms_"+row+"']").attr("name",'sms_'+row2);
                    $(".copy:last [name='sName[]']").val('');
                    $(".copy:last [name='sms_sMobile[]']").val('');
                    $(".copy:last [name='sms_sNID[]']").val('');
                    //sms_sNID[]
                     row++;
                    ///sms_sSend
                    // sms_no[]
                });
                 $('#cancel').live('click', function () {
                    $('#form_back').submit();
                });
                $('#save').button({
                    icons:{
                        primary: "ui-icon-check"
                    }
                });
                $('#cancel').button({
                    icons:{
                        primary: "ui-icon-close"
                    }
                });
                $('#addrow').button({
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('.del').button({
                    icons:{
                        primary: "ui-icon-info"
                    }
                });
                
            });

            function ck(id)
            {
                var phone = '0'+id;

                if ($("#default"+phone+':checked').val() == undefined) {

                     $("#send"+phone).removeAttr('checked');
                }else{
                     $("#send"+phone).attr('checked', 'checked');
                }

            }
            
            /* 檢查新增簡訊姓名電話格式 */
            function checkNewSMS() {
                var tte = $('#sms_sNID :selected').val() ;      //新增簡訊對象身分
                var nme = $('#sms_sName').val() ;               //新增簡訊對象姓名
                var mbe = $('#sms_sMobile').val() ;             //新增簡訊對象號碼
                
                /* 若無姓名輸入 */
                if ((nme == '')&&(mbe != '')) {
                    $('#dialog').html('請輸入姓名!!') ;
                    $('#dialog').dialog('open') ;
                    return false ;
                }
                ////
                
                /* 若手機號碼輸入格式不正確 */
                if ((!/^09\d{8}$/.test(mbe))&&(nme != '')) {
                    $('#dialog').html('輸入手機號碼格式錯誤或空白!!') ;
                    $('#dialog').dialog('open') ;
                    return false ;
                }
                ////
                
                return true ;
            }
            ////
            function del(id){
                if (confirm('是否要刪除?')) {
                    var ss = 0;
                    if (confirm("要聯動至進行中的案件?")) {
                        ss = 1;
                    }

                     $.ajax({
                        url: 'formscsmsdel.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {id: id,same:ss,sId:"<{$scid}>"},
                    })
                    .done(function(txt) {
                        
                        if (txt == 'OK') {
                            alert('刪除成功');
                            location.href = "formscrivenersms.php?scid=<{$scid}>";
                        }
                    });

                }
               
                
            }
            function lock(id,txt){
                if (confirm('是否要'+txt+'?')) {
                    var ss = 0;
                    if (confirm("要聯動至進行中的案件?")) {
                        ss = 1;
                    }

                     $.ajax({
                        url: 'formscsmslock.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {id: id,same:ss,sId:"<{$scid}>"},
                    })
                    .done(function(txt) {
                        console.log(txt);
                        if (txt == 'OK') {
                            alert(txt+'成功');
                            location.href = "formscrivenersms.php?scid=<{$scid}>";
                        }
                    });

                }
            }

            function copyToFeedBackSms(id,type){
                $.ajax({
                    url: 'copyToFeedBackSms.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: id,cat:1,type:type},
                })
                .done(function(msg) {
                    // console.log(msg);
                    alert(msg);
                });
                
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('http://first.twhg.com.tw/bank/create.php', '_blank', config='height=100,width=650');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                                <!-- <div><font color="red">※同步到回饋金對象:請儲存後再按同步。如果沒有資料會新增，有資料會更新，更新部分只有同步過去的資料才會更新</font></div> -->
                                <form id="form_sms" action="formscsmssave.php" method="POST" >
                                    <table border="0" width="100%">
                                        
                                        <tr>
                                            
                                             <td colspan="8">
                                                是否簡訊收到標的(EX:台北市松山區敦化北路)
                                                <{html_radios name=smsLocationMark options=$menu_choice selected=$dataScrivner.sSmsLocationMark}>
                                            </td>
                                        </tr>       
                                        <tr>
                                            <th colspan="8"><center>發送簡訊對象</center></th>
                                        </tr>
                                        <tr>
                                            <th width="5%"><center>預設</center></th>
                                            <th width="10%"><center>職稱</center></th>
                                            <th width="20%"><center>姓名</center></th>
                                            <th width="15%"><center>行動電話</center></th>
                                            <th width="10%"><center>服務費<br>簡訊通知</center></th>
                                            <th width="12%"><center>通知回饋簡訊</center></th>
                                            <th width="12%"><center>出款回饋簡訊</center></th>
                                            <th><center>&nbsp;</center><input type="hidden" name="scid" value="<{$scid}>"></th>
                                            
                                        </tr>
                                        <tbody id="item">
                                        
                                                <{foreach from=$data key=key item=item}>    
                                            <tr>
                                                <td align="center">
                                                    <input type="hidden" name="sSn[]" value="<{$item.sn}>">
                                                    <{if $item.sLock == 1}>
                                                        <{if $item.sDefault == 1}>
                                                        <input type="hidden" name="sDefault[]" value="<{$item.sMobile}>_<{$item.sName}>">


                                                        <input type="checkbox"  disabled="disabled" id="default<{$item.sMobile}>" checked <{$item.readonly}> >
                                                        <{else}>
                                                        <input type="checkbox"  disabled="disabled" id="default<{$item.sMobile}>"  <{$item.readonly}> >
                                                        <{/if}>
                                                    <{else}>
                                                        <{if $item.sDefault == 1}>
                                                        <input type="checkbox" name="sDefault[]" value="<{$item.sMobile}>_<{$item.sName}>" onclick="ck(<{$item.sMobile}>)" id="default<{$item.sMobile}>" style="<{$item.readonlystyle}>" checked <{$item.readonly}> >
                                                        <{else}>
                                                        <input type="checkbox" name="sDefault[]" value="<{$item.sMobile}>_<{$item.sName}>" onclick="ck(<{$item.sMobile}>)" id="default<{$item.sMobile}>" style="<{$item.readonlystyle}>" <{$item.readonly}>>
                                                        <{/if}>
                                                    <{/if}>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="sNID[]" value="<{$item.id}>">
                                                    <input type="text" class="input-text-per" value="<{$item.tTitle}>" disabled/>
                                                </td>
                                                <td><input type="text" class="input-text-per" style="<{$item.readonlystyle}>" name="sName[]" value="<{$item.sName}>"  <{$item.readonly}>/></td>
                                                <td><input type="text" class="input-text-per" style="<{$item.readonlystyle}>" name="sMobile[]" maxlength="10" value="<{$item.sMobile}>"  <{$item.readonly}>/></td>
                                                <td align="center">
                                                    <{if $item.sLock == 1}>
                                                        <{if $item.sSend == 1 }>
                                                         <input type="hidden" name="sSend[]" id="send<{$item.sMobile}>" value="<{$item.sMobile}>_<{$item.sName}>" checked>
                                                         <input type="checkbox" disabled="disabled" checked >
                                                        <{else}>
                                                         <input type="checkbox" disabled="disabled"  >
                                                        <{/if}>
                                                    <{else}>
                                                        <{if $item.sSend == 1 }>
                                                         <input type="checkbox" name="sSend[]" id="send<{$item.sMobile}>" value="<{$item.sMobile}>_<{$item.sName}>" style="<{$item.readonlystyle}>" checked <{$item.readonly}>>
                                                        <{else}>
                                                         <input type="checkbox" name="sSend[]" id="send<{$item.sMobile}>" value="<{$item.sMobile}>_<{$item.sName}>" style="<{$item.readonlystyle}>"  <{$item.readonly}>>
                                                        <{/if}>
                                                    <{/if}>
                                               
                                                </td>
                                                <td align="center"><input type="button" value="同步" onclick="copyToFeedBackSms(<{$item.sn}>,1)" <{$item.disabled}>></td>
                                                <td align="center"><input type="button" value="同步" onclick="copyToFeedBackSms(<{$item.sn}>,2)" <{$item.disabled}>></td>
                                                <td align="center">
                                                    <input type="button" class="del" value="刪除" onclick="del(<{$item.sn}>)">
                                                    <input type="button" value="<{$item.lock}>" class="xxx-button" onclick="lock(<{$item.sn}>,'<{$item.lock}>')">
                                                </td>
                                            </tr>
                                                <{/foreach}>
                                            <tr id="copy" class="copy">
                                                <td align="center">
                                                    <span>新增=></span>
                                                </td>
                                                <td>
                                                    <!-- <input type="hidden" name="sms_no[]" value="sms_0" > -->
                                                    <select style="width:100%;" id="sms_sNID" name="sms_sNID[]">
                                                    <{$sms_sNID}>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="input-text-per" id="sms_sName" name="sms_sName[]"></td>
                                                <td><input type="text" class="input-text-per" id="sms_sMobile" maxlength="10" name="sms_sMobile[]"></td>
                                                <td align="center"><input type="checkbox" name="sms_0" value="1" ></td>
                                                
                                            </tr>
                                            </tbody>
                                        
                                    </table>
                                    <input type="hidden" name="same" >
                                    </form>

                                </div>
                            </div>
                            <center>
                                <br/>
                                <button id="save">儲存</button>
                                <button id="cancel">取消</button>
                                <button id="addrow">新增一列</button>
                               
                            </center>
                            <form name="form_back" id="form_back" method="POST"  action="formscriveneredit.php">
                             <input type="hidden" name="id" value="<{$scid}>">
                            </form>
                        </div>
                    </div>
                </div></div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
            <div id="dialog"></div>
    </body>
</html>










