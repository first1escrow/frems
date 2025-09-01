<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta.inc.tpl'}>
<script type="text/javascript">
    $(document).ready(function() {
	    $( "#dialog" ).dialog({
		  autoOpen: false,
		  modal: true,
		  minHeight:50,
		  show: {
			 effect: "blind",
			 duration: 1000
		  },
		  hide: {
			 effect: "explode",
			 duration: 1000
		  }
	    });
		$(".ui-dialog-titlebar").hide() ;
				
		 $('[name="search"]').on('click',function() {
            $('[name="form_search"]').submit();
         });
       

        $('[name="search"]').button( {
            icons:{
                primary: "ui-icon-document"
            }
        });
        $('.save').button( {
            icons:{
                primary: "ui-icon-document"
            }
        });
    });
			
	function dia(op) {
		$( "#dialog" ).dialog(op) ;
	}

    function save(){
        var year = $('[name="year"]').val();
        var month = $('[name="month"]').val();

        $('[name="ok"]').val('ok');
        $('[name="s_year"]').val(year);
        $('[name="m_year"]').val(month);
        $('[name="form_save"]').submit();
    }

</script>
<style>
    #dialog {
    	background-image:url("../images/animated-overlay.gif") ;
    	background-repeat: repeat-x;
    	margin: 0px auto;
    }

    .tb1 {
        margin-bottom: 20px;
        padding:5px;
        background-color:#FFFFFF;
    }
    .tb1 th{
        padding: 5px;
        border: 1px solid #CCC;
        background-color: #CFDEFF;
    }
    .tb1 td{
        text-align: left;
        padding: 5px;
        border: 1px solid #CCC;
    }

    .tb {
        margin-bottom: 20px;
        padding:5px;
        background-color:#FFFFFF;
    }
    .tb th{
        padding: 5px;
        border: 1px solid #CCC;
        background-color: #E4BEB1;
    }
    .tb td{
        text-align: left;
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
        <td width="753">
            <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                <tr>
                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                </tr>
                <tr>
                    <td width="81%" align="right"></td>
                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                </tr>
            </table>
        </td>
    </tr>
</table> 
</div>
<{include file='menu1.inc.tpl'}>
<ul id="menu"><div id="dialog"></div></ul>
<table width="1000" border="0" cellpadding="4" cellspacing="0">
<tr>
    <td bgcolor="#DBDBDB">
        <table width="100%" border="0" cellpadding="4" cellspacing="1">
            <tr>
                <td height="17" bgcolor="#FFFFFF">
                    <div id="menu-lv2"></div><br/>
                    <h1>出款錯誤紀錄</h1>
                    <div id="container">
                    <form name="form_search" method="POST">
                        年份:
                        <{html_options name=year options=$menu_y selected=$s_year}>
                       
                        月份:
                        <{html_options name=month options=$menu_m selected=$s_month}>
                       
                        <input type="button" value="查詢" name="search" id="search">
                        <input type="button" value="儲存" class="save" onclick="save()">                    
                    </form>
                    <form method="POST" name="form_save">
                    <div id="subTabs">
                    <input type="hidden" name="total" value="<{$data_t}>">
                    <input type="hidden" name="s_year" value="">
                    <input type="hidden" name="m_year" value="">
                    <div style="padding-top:20px;padding-bottom:20px;">
                        <{foreach from=$list_people key=key item=item}>
                          &nbsp;|&nbsp;<a href="#go<{$item.pId}>"><{$item.pName}></a>
                        <{/foreach}>
                        &nbsp;|&nbsp;
                        <br>
                    </div>
                    
                    <{foreach from=$list_people key=key item=item}>
                    <table class="<{$item.class}>" cellpadding="2" cellspacing="0">
                        <tr>
                            <th colspan="8"  id="go<{$item.pId}>">
                                <{$item.pName}>
                                <input type="hidden" name="mId[]" value="<{$item.pId}>">  
                            </th>
                        </tr>
                        <tr>
                            <th>基本檔(0.5)<input type="hidden" name="pId[]" value="<{$data[$item.pId]['pId']}>"></th>
                            <td>
                                <input type="text" name="basic[]" style="width:50px;" value="<{$data[$item.pId]['pBasic']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="basic_msg[]" style="width:150px;" value="<{$data[$item.pId]['pBasicMsg']}>">
                            </td>
                            <th>出錯款(GG)</th>
                            <td>
                                <input type="text" name="banktran[]" style="width:50px;" value="<{$data[$item.pId]['pBanktran']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="banktran_msg[]" style="width:150px;" value="<{$data[$item.pId]['pBanktranMsg']}>">
                            </td>
                        </tr>
                        <tr>
                            <th>金額(1)</th>
                            <td>
                                <input type="text" name="money[]" style="width:50px;" value="<{$data[$item.pId]['pMoney']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="money_msg[]" style="width:150px;" value="<{$data[$item.pId]['pMoneyMsg']}>">
                            </td>
                            <th>分行(1)</td>
                            <td>
                                <input type="text" name="bankBranch[]" style="width:50px;" value="<{$data[$item.pId]['pBankBranch']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="bankBranch_msg[]" style="width:150px;" value="<{$data[$item.pId]['pBankBranchMsg']}>">
                            </td>
                        </tr>
                        <tr>
                            <th>附言(1)</th>
                            <td>
                                <input type="text" name="txt[]" style="width:50px;" value="<{$data[$item.pId]['pTxt']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="txt_msg[]" style="width:150px;" value="<{$data[$item.pId]['pTxtMsg']}>">
                            </td>

                            <th>帳號(1)</th>
                            <td>
                                <input type="text" name="account[]" style="width:50px;" value="<{$data[$item.pId]['pAccount']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="account_msg[]" style="width:150px;" value="<{$data[$item.pId]['pAccountMsg']}>">
                            </td>
                        </tr>
                        <tr>    
                            <th>戶名(1)</th>
                            <td>
                                <input type="text" name="accountName[]" style="width:50px;" value="<{$data[$item.pId]['pAccountName']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="accountName_msg[]" style="width:150px;" value="<{$data[$item.pId]['pAccountNameMsg']}>">
                            </td>
                            <th>其他(0)</th>
                            <td><input type="text" name="other[]" style="width:50px;" value="<{$data[$item.pId]['pOther']}>"></td>
                            <th>錯誤說明</th>
                            <td>
                                <input type="text" name="other_msg[]" style="width:150px;" value="<{$data[$item.pId]['pOtherMsg']}>">
                            </td>
                        </tr>
                        <tr>
                            <th>結案(0.5)</th>
                            <td>
                                <input type="text" name="end[]" style="width:50px;" value="<{$data[$item.pId]['pEnd']}>">
                            </td>
                            <th>錯誤說明</th>
                            <td ><input type="text" name="end_msg[]" style="width:150px;" value="<{$data[$item.pId]['pEndMsg']}>"></td>
                            <th>結案筆數小計</th>
                            <td colspan="3"><input type="text" name="end_total[]" style="width:100px;" value="<{$data[$item.pId]['pEndTotal']}>"></td>
                        </tr>
                        <tr>
                            <th>特殊備註</th>
                            <td colspan="7">
                                <textarea name="sp_msg[]" id="" cols="80" rows="5" ><{$data[$item.pId]['pSp_msg']}></textarea>
                                <a href="#container">TOP</a>
                            </td>

                        </tr>
                    </table>
                    <{/foreach}>
                    <center>
                        <div id="msg"></div>
                        <input type="hidden" name="ok">
                        <input type="button" value="儲存" class="save" style="width:80px;height:50px;font-size:15px;" onclick="save()">
                    </center>
                    
                    </div>
                    </form>
                                            <!-- <button id="export">匯出Excel</button> -->
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