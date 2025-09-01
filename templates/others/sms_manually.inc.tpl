<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".cheque").hide();
               
                $("[name='send']").on('click',  function() {

                    var cat = $("[name='cat']:checked").val();

                    if ($('[name="txt"]').val() == '') {
                        alert("簡訊內容不能為空");
                        return false;
                    }

                    var check = '';

                    if (cat == 'cheque') {
                        $("[name='mobile2[]']:checked").each(function() {
                            check = $(this).val();
                        });

                        if (check =='') {
                            alert("請勾選寄送號碼");
                            return false;
                        }
                        
                        
                    }else if(cat == 'manually'){
                        if ($('[name="mobile"]').val() == '') {
                            alert("手機號碼欄位不能為空");
                            return false;
                        }
                    }   
                    $("[name='send']").hide();
                    $("[name='save']").val('ok');

                    $("[name='form_search']").submit();


                });

                $("[name='cat']").on('click',  function() {

                    if ($("[name='cat']:checked").val() == 'cheque') {
                         $(".cheque").show();
                         $(".manually").hide();
                    }else if($("[name='cat']:checked").val() == 'manually'){
                        $("[name='cId']").val('');
                        $(".cheque").hide();
                        $(".manually").show();
                    }   

                  
                   
                });

                /*-----------------------------------------*/

                $("[name='send']").button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
            } );
			function getSms(cat){
                var val = $("[name='cId']").val();
                $.ajax({
                    url: 'sms_function_manuallyC.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {"cat": cat,"cId":val},
                })
                .done(function(txt) {
                    $(".row").remove();
                    $('[name="txt"]').text('');
                    if (txt != '') {
                       var obj = jQuery.parseJSON(txt);
                        // console.log(obj);
                        if (obj.Code == '202') {
                            alert(obj.errorMsg);
                        }else if(obj.Code == '200'){
                            $('[name="txt"]').text(obj.txt);

                            $.each(obj.sms, function(index, val) {
                                // console.log(val.mName);
                                $("<tr class=\"row\"><td width=\"20%\"><input type=\"checkbox\" name=\"mobile2[]\" value=\""+val.mMobile+"\">"+val.mName+"</td><td>"+val.mMobile+"</td></tr>").insertAfter('#show');
                                
                            }); 
                        }
                        
                    }

                });
            }

            function getList(cat){
                var url = 'sms_manually_list.php?cat='+cat ;
                $.colorbox({iframe:true, width:"1000px", height:"100%", href:url}) ;
              
                
            }

            function checkAll() {
                let _checked = $('#selectAll').prop('checked');

                let _tf = false;
                if (_checked) {
                    _tf = true;
                }

                $("[name='mobile2[]']").each(function() {
                    $(this).prop('checked', _tf);
                });
            }
        </script>
		<style>
		#container table{
            border: solid #CCC 1px;
        }
        #container  th{
            background-color:#E4BEB1;
             /*border: solid #CCC 1px;*/
            padding: 10px;
        }
       
        #container td{
            padding: 10px;
             border: solid #CCC 1px;
        }

        .ui-autocomplete-input {
                width:250px;
                height: 20px;
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
                                        <h1>手動發送簡訊</h1>
                                        <div id="container">
                                            <center>
                                                <{if $post_txt !=''}>
                                                <table>
                                                    <tr>
                                                        <th colspan="2">簡訊已發送，發送內容及對象如下</th>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>簡訊內容</td>
                                                        <td><{$post_txt}></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td>寄送號碼</td>
                                                        <td>
                                                            <{$mobile}>
                                                        </td>
                                                      
                                                    </tr>
                                                   
                                                </table>
                                                <br>
                                                <{/if}>
                                                <form name="form_search" method="POST">
                                                <table width="80%">
                                                    <tr>
                                                        <th>簡訊類別</th>
                                                        <td>
                                                            <input type="radio" name="cat" id="" value="manually" checked>手動
                                                            <input type="radio" name="cat" id="" value="cheque">票據簡訊
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr class="cheque">
                                                        <th>保證號碼</th>
                                                         <td><input type="text" name="cId" id="" maxlength="9" onkeyup="getSms('cheque')"></td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <th colspan="2">簡訊內容</th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" align="center">
                                                            <textarea name="txt" id="" cols="30" rows="10"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr class="manually">
                                                        <th colspan="2">寄送號碼<font color="red">*</font>每個門號請用,(逗號)區分</th>
                                                    </tr>
                                                    <{if $smarty.session.member_id == 6 || $smarty.session.member_id == 1}>
                                                    <tr class="manually">
                                                        <td colspan="2">
                                                            <input type="button" value="匯入地政士名單" onclick="getList('s')">
                                                            &nbsp;&nbsp;
                                                            <input type="button" value="匯入仲介店名單" onclick="getList('b')">
                                                            &nbsp;&nbsp;
                                                           
                                                             <input type="button" value="匯入地政士名單(非直營)" onclick="getList('s1')">
                                                             
                                                            &nbsp;&nbsp;
                                                            ※地政士跟仲介請分開寄送
                                                        </td>
                                                    </tr>
                                                    <{/if}>
        
                                                    <tr class="manually">
                                                        <td colspan="2" align="center">
                                                            <textarea name="mobile" id="" cols="30" rows="10"></textarea>
                                                            
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table width="80%" class="cheque">
                                                    <tr  id="show">
                                                        <th><span id="selectAllName" style="margin-right:5px;">全選</span><input type="checkbox" id="selectAll" onclick="checkAll()"></th>
                                                        <th>寄送號碼</th>
                                                    </tr>
                                                   <!--  <tr><td width="20%"><input type="checkbox" name="mobile2[]" id="" value=""></td></tr> -->
                                                        
                                                    
                                                </table>
                                                <div style="padding:10px;">
                                                    <input type="hidden" name="save">
                                                    <input type="button" name="send" value="寄送簡訊">
                                                </div>
                                                
                                                </form>
                                                <!-- <div id="show"></div> -->
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