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

                $('#cancel').live('click', function () {
                     $('#form_back').submit();
                 });
                $('#save').live('click', function () {
                    $('#form_sms').submit();
                });
                $('#count').live('click', function () {
                    $('tbody#item');
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
				<{$dialog}>
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

            function addRow(){
                var cloneRow = $(".newRow:first").clone();
                var rowCount = $(".newRow").length;
                // console.log(rowCount);
                cloneRow.find('[name*="isManage"]').prop({
                    checked: false,
                    value: 'new_'+rowCount,
                });        
                
                cloneRow.find('[name*="isManage2"]').prop({
                    checked: false,
                    value: 'new_'+rowCount,
                });        

                cloneRow.find('[name*="newName[]"]').prop({
                    value: ''
                });        
                
                 cloneRow.find('[name*="newMobile[]"]').prop({
                    value: ''
                });  
                //

                cloneRow.insertAfter('.newRow:last');

            }
        </script>
        <style type="text/css">
            #tabs {
                width:600px;
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
            .content td{
                padding-top:5px;
                font-size: 12px;
            }
        </style>
    </head>
    <body id="dt_example">
            <div id="content">
                            <div id="tabs">
                                <div id="tabs-contract">
                                    <form id="form_sms" action="formcasesmssave.php" method="POST" >
                                    <table border="0" width="600px">
                                        <tr>
                                            <th width="8%"><center>選擇</center></th>
                                            <th width="20%"><center>職稱</center></th>
                                            <th width="20%"><center>姓名</center></th>
                                            <th width="28%"><center>手機</center></th>
                                            <th width="8%"><center>承辦代書</center></th>
                                            <th width="8%"><center>承辦代書助理</center></th>
                                             <th width="8%"><center>服務費簡訊通知</center></th>
                                        </tr>
                                        <tbody id="item">
                                        
                                            <input type="hidden" name="certified_id" value="<{$certified_id}>">
											<input type="hidden" name="sScrivener" value="<{$sScrivener}>">
                                                <{foreach from=$book key=key item=item}>
                                            <tr class="split">
                                                <td align="center">
                                                    <{if $item.isSelect == 1}>
                                                        <input type="checkbox" name="isSelect[]" value="<{$item.sName}>_<{$item.sMobile}>" checked onclick="ck(<{$item.sMobile}>)" id="default<{$item.sMobile}>"/>
                                                    <{else}>
                                                        <input type="checkbox" name="isSelect[]" value="<{$item.sName}>_<{$item.sMobile}>" onClick="ck()" id="default<{$item.sMobile}>"/>
                                                    <{/if}>
                                                </td>
                                                <td><{$item.tTitle}></td>
                                                <td><{$item.sName}></td>
                                                <td><{$item.sMobile}></td>
                                                <td align="center">
                                                    <{if $item.isManage == 1}>
                                                        <input type="radio" name="isManage" id="isManage" value="<{$item.sId}>" checked />
                                                    <{else}>
                                                        <input type="radio" name="isManage" id="isManage" value="<{$item.sId}>" />
                                                    <{/if}>
                                                </td>
                                                <td  align="center">
                                                     <{if $item.isManage2 == 1}>
                                                        <input type="radio" name="isManage2" id="isManage2" value="<{$item.sId}>" checked />
                                                    <{else}>
                                                        <input type="radio" name="isManage2" id="isManage2" value="<{$item.sId}>" />
                                                    <{/if}>
                                                </td>
                                                 <td align="center">
                                                  
                                                    <{if $item.sSend2 == 1 }>
                                                     <input type="checkbox" name="sSend2[]" id="send<{$item.sMobile}>" value="<{$item.sName}>_<{$item.sMobile}>" checked>
                                                    <{else}>
                                                     <input type="checkbox" name="sSend2[]" id="send<{$item.sMobile}>" value="<{$item.sName}>_<{$item.sMobile}>" >
                                                    <{/if}>
                                               
                                                </td>
                                            </tr>
                                                <{/foreach}>
                                           <{foreach from=$otherSms key=key item=item}>
                                            
                                           <{/foreach}>
                                            
                                            <tr class="split newRow">
                                                <td width="8%"><center>&nbsp;</center></td>
                                                <td width="20%">地政士<input type="hidden" name="newTitle[]" id="" value="1"></td>
                                                <td width="20%"><input type="text" name="newName[]" id="" size="8"></td>
                                                <td width="28%"><input type="text" name="newMobile[]" id="" size="10" maxlength="10"></td>
                                                <td width="8%"><center><input type="radio" name="isManage" id="isManage" value="new_0" /></center></td>
                                                <td width="8%"><center><input type="radio" name="isManage2" id="isManage2" value="new_0" /></center></td>
                                                <td width="8%"><center><input type="checkbox" name="newSend2[]" value="1" ></center></td>
                                            </tr>
                                            <tr>
                                                <td colspan="7" align="right"><input type="button" value="新增一列" onclick="addRow()"></td>
                                            </tr>
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










