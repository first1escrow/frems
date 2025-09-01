<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <style>
         
        </style>
        <{include file='meta2.inc.tpl'}>

       
       <script type="text/javascript">
            $(document).ready(function() {
                $( "#subTabs" ).tabs();
                // setComboboxNormal("branch","id");
                // setComboboxNormal("scrivener","id");
                <{if $msg != ''}>
                    alert("<{$msg}>");
                <{/if}>
                
                $("#import").on('click', function() {
                  
                    $("#myform").submit();
                });

                $('#import').button({
                    icons:{
                        primary: "ui-icon-folder-open"
                    }
                }) ;
                
               
            } );
            
            function searchData(){
                $.ajax({
                    url: 'storeFeedBackUpload_result.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {branch: $("[name='branch']").val(),scrivener:$("[name='scrivener']").val(),sDate:$("[name='sDate']").val(),sDate2:$("[name='sDate2']").val()},
                }).done(function(html) {
                    // console.log(html);
                    $("#result").html(html);
                });
                
            }
            function deleteUpload(){
                var cbx = new Array();
                $('input:checkbox:checked[name="allForm[]"]').each(function(i) { cbx[i] = this.value; });

                $.ajax({
                    url: 'storeFeedBackUploadDelete.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: cbx},
                })
                .done(function(msg) {
                    // console.log(msg);
                    alert(msg);
                    searchData();
                });
                
            }
            function checkALL(){
                var all = $('[name="all"]').prop('checked');

                // console.log(all);
                if (all == true) {
                    $('[name="allForm[]"]').prop('checked', true);
                }else{
                    $('[name="allForm[]"]').prop('checked', false);
                }
                // allForm
                showcount();
            }
        </script>
        <style>
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
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }
        .tb td{
           padding-bottom: 10px;
          

        }

        .tb2 td{
           padding-bottom: 10px;
           font-size: 10px;

        }
        .tb2 th{
            font-size: 10px;
        }
        #subTabs-1,#subTabs-2{
            background-color: #FFF;
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

            width:100px;
            height:35px;
            font-size:16px;
        }
        .xxx-button:hover {
            background-color:#333333;
            border:1px solid #333333;
        }
        #result th{
            border:1px solid #999;
            padding:5px;
        }
        #result td{
            border:1px solid #999;
            padding:5px;
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
            <ul id="menu">
            <div id="dialog"></div>
            </ul>
                <table width="1000" border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td bgcolor="#DBDBDB">
                            <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                <tr>
                                    <td height="17" bgcolor="#FFFFFF">
                                        <div id="menu-lv2">
                                                        
                                        </div>
                                        <br/> 
                                        <h3>&nbsp;</h3>
                                        <div id="container">
                                            <center>
                                                <h1>回饋金匯款紀錄匯入/查詢</h1>
                                                <br>
                                                <!-- <div style="paddin:20px;text-align:right;"><a href="excel/example/feedbackExample.xlsx">範例檔格式下載</a></div> -->
                                                <br>
                                                <div id="tt" class="easyui-tabs" style="">
                                                    <{if $smarty.session.member_pDep == 10 ||  $smarty.session.member_pDep == 9 || $smarty.session.member_id == 6}>
                                                       
                                                    <div title="上傳檔案" style="padding:20px;display:none;">
                                                        <form name="myform" id="myform" method="POST" enctype="multipart/form-data" >
                                                            <table  align="center" class="tb_main" cellpadding="10" cellspacing="10">
                                                               
                                                                <tr>
                                                                    <th align="center">上傳檔案<input type="hidden" value='1' name='check'></th>
                                                                    <td align="center"><input name="upload_file" type="file"  /></td>
                                                                    <td align="center"> <input type="button" id="import" value="匯入"></td>    
                                                                    <td>※限EXCEL2007以上格式(.xlsx)</td>
                                                                </tr>
                                                               
                                                            </table>
                                                        </form>
                                                    </div>
                                                    <{/if}>
                                                   <div title="匯款紀錄查詢" style="padding:20px;display:none;">
                                                       <form action="" name="form" method="POST">
                                                            <table cellspacing="0" cellpadding="0" width="70%" class="tb">
                                                                <tr>
                                                                    <td>仲介: </td>
                                                                    <td>
                                                                        <select id="branch" name="branch" class="easyui-combobox" data-options="
                                                                                    valueField: 'id',
                                                                                    textField: 'text'
                                                                                    " style="width:300px;">
                                                                        <{foreach from=$menuBranch key=key item=item}>
                                                                            <option value="<{$key}>"><{$item}></option>
                                                                        <{/foreach}>
                                                                        </select> 

                                                                      
                                                                    </td>
                                                                    <td rowspan="3">
                                                                        <input type="button" value="查詢" class="xxx-button" onclick="searchData()">

                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>地政士 : </td>
                                                                    <td>
                                                                        <select name="scrivener" id="scrivener"  class="easyui-combobox" style="width:300px;">
                                                                            <{foreach from=$menuScrivener key=key item=item}>
                                                                            <option value="<{$key}>"><{$item}></option>
                                                                            <{/foreach}>
                                                                        </select>
        
                                                                        <!-- <{html_options name=scrivener id=scrivener options=$menuScrivener}> -->
                                                                            
                                                                    </td>
                                                                    
                                                                </tr>
                                                                <tr>
                                                                    <td>收款日</td>
                                                                    <td>
                                                                        <input type="text" name="sDate" class="datepickerROC" style="width:100px;">~
                                                                        <input type="text" name="sDate2" class="datepickerROC" style="width:100px;">
                                                                    </td>
                                                                </tr>
                                                               
                                                            </table>
                                                            <hr>
                                                            <div id="result">
                                                                
                                                            </div>
                                                           
                                                            
                                                        </form>
                                                    </div>
                                                   
                                                </div>
            


                                              
                                                
                                               
                                                <br>

                                                <div id="show">
                                                    <{$show}>
                                                </div>
                                                
                                           </center>
                                           
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>