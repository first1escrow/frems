<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
	$( "#subTabs" ).tabs();

	if ($('[name="pDep"]').val() == 7) { //業務部
		$("#salesArea").show();
	} else {
		$("#salesArea").css('display', 'none');
	}

    $('body').on('change', '.trainee', function() {
        if(true == $(this).prop('checked')) {
            let area = this.value;
            $('.trainee').each(function() {
                if(this.value == area) {
                    $(this).prop("checked",true) ;
                } else {
                    $(this).prop("checked",false) ;
                }
            }) ;
        }

        if(false == $(this).prop('checked')) {
            $('.trainee').each(function() {
                $(this).prop("checked",false) ;
            }) ;
        }
    });
});

/* 重置帳戶密碼 */
function newMemberPWD() {
	$('#confirm').html("<div>一旦執行後，系統將重新產生一組新密碼<br>並於用戶下次登入時生效!!</div><br><h3 style='font-weight:bold;color:red;'>確認是否仍要執行??</h3>") ;
	$('#confirm').prop("title","您正要重新製作此帳戶的密碼!!") ;
	$('#confirm').dialog({
		modal: true,
		buttons: {
			"確認": function() {
				$(this).dialog("close") ;
				var url = 'memberPWD.php' ;
				$.post(url,{'id':'<?=$id?>'},function(txt) {
					$('#dialog').html('新密碼:'+txt) ;
					$('#dialog').dialog("open") ;
				}) ;
			},
			"取消": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
}
////

/* 檢核字串是否符合格式 */
function checkFormat(str) {
	let patt = /^[a-zA-Z0-9]+$/ ;
	
	if (!patt.test(str)) {
		return true ;
	}
	else {
		return false ;
	}
}
////

/* 檢核字串長度 */
function checkLength(str) {
	let re = /^.{4,12}$/;
	
    return re.test(str) ? false : true;
}
////

/* 確認總部案件選項 */
function HQS(no) {
	if (no == 0) {
		$('[name="pRealtyCaseListAdd"]').prop("checked",false) ;
		$('[name="pRealtyCaseListAdd"]').prop("disabled",true) ;
	} else {
		$('[name="pRealtyCaseListAdd"]').prop("disabled",false) ;
	}
}
////

/* 返回權限列表 */
function cancel() {
	location.href = 'memberTable.php';
}
////

/* 新增帳戶 */
function save() {
	let pn = $('[name="pName"]').val() ;
	let pa = $('[name="pAccount"]').val() ;
	let pp = $('[name="pwd"]').val() ;
	
	if (pn == '') {
		alert('請確認輸入使用者名稱!!') ;
		focusOn('pName') ;
		return false ;
	}
	
	if (checkFormat(pa)) {
		alert('請確認輸入帳號須為英文字母或數字!!') ;
		focusOn('pAccount') ;
		return false ;
	}
	
	if (checkLength(pa)) {
		alert('請確認輸入帳號長度需 4~12 碼!!') ;
		focusOn('pAccount') ;
		return false ;
	}
	
	if (pp != '') {
		if (checkFormat(pp)) {
			alert('請確認輸入密碼須為英文字母或數字!!') ;
			focusOn('pwd') ;
			return false ;
		}
		
		if (checkLength(pp)) {
			alert('請確認輸入密碼長度需 4~12 碼!!') ;
			focusOn('pwd') ;
			return false ;
		}
	}

	let input = $('input');
    let textarea = $('textarea');
    let select = $('select');
    let arr_input = new Array();                
    let reg = /.*\[]$/ ;               

    $.each(select, function(key,item) {
        if (reg.test($(item).attr("name"))) {
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();
            }
            
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
        } else {
            arr_input[$(item).attr("name")] = $(item).val();
        }
    });

    $.each(textarea, function(key,item) {
        arr_input[$(item).attr("name")] = $(item).attr("value");
    });

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
            } else {
            	 arr_input[$(item).attr("name")] = $(item).val();
            }    
        }
    });

  	let obj_input = $.extend({}, arr_input);
    let request = $.ajax({  
                        url: 'memberEditSave.php',
                        type: "POST",
                        data: obj_input,
                        dataType: "html"
                    });

    request.done(function(msg) {
        location.href = 'memberTable.php';
    });
}
////

/* 選取欄位 */
function focusOn(fds) {
	let fields = $('[name="'+fds+'"]') ;

	fields.select() ;
	fields.focus() ;
}
////

/* 凍結帳戶所有權限 */
function lockAll(tre) {
	if (tre) {
		$('.lock').each(function() {
			$(this).prop("disabled",true) ;
		}) ;

		$('[name="pRealtyCaseListAdd"]').prop("disabled",true) ;
	} else {
		$('.lock').each(function() {
			$(this).prop("disabled",false) ;
		}) ;
		
		let cs = $('[name="pRealtyCaseList"]:checked').val() ;
		
        if (cs == '1') {
			$('[name="pRealtyCaseListAdd"]').prop("disabled",false) ;
		} else {
			$('[name="pRealtyCaseListAdd"]').prop("disabled",true) ;
		}
	}
}
////

function changeDepAuth(){
	let dep = $('[name="pDep"]').val();

	$.ajax({
		url: 'getDepAuthority.php',
		type: 'POST',
		dataType: 'html',
		data: {dep: dep},
	})
	.done(function(msg) {
		let obj = JSON.parse(msg);
		
		if (obj.code == '200') {
			$.each(obj.data, function(index, val) {
				$('input[name="'+index+'"][value="'+val+'"]').attr('checked',true);
			});
		}
	});

	if (dep == 7) {
		$("#salesArea").show();
	}
}

function changeValue() {
	$("[name='chageVal']").val(1);
}

function getUse() {
	if ($("[name='use']:checked").val() == 1) {
		$("[name='city[]']").each(function(index, val) {
            $("[name='city[]']").attr('disabled', false);
		});
	} else {
		$("[name='city[]']").each(function(index, val) {
			$("[name='city[]']").attr('disabled', 'disabled');
		});
	}
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

#subTabs-1,#subTabs-2{
    background-color: #FFF;
}

.memberTB {
	border: 1px solid #ccc;
	padding: 5px;
	font-size: 10pt;
	font-weight: bold;
	text-align: center;
	background-color: #EEE0E5 ;
}

.memberCell {
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
}

#table tbody td{
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
}

#tt table tbody tr:nth-child(even) {
    background-color: #FFF0F5;
}

#tt table tbody tr:nth-child(odd) {
    background-color: #FFFAFA;
}

#salesArea table tbody tr:nth-child(even) {
    background-color: #FFF0F5;
}

#salesArea table tbody tr:nth-child(odd) {
    background-color: #FFFAFA;
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
							<td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
							<td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
						</tr>
					</table>
				</td>
			</tr>
		</table> 
	</div>
	<{include file='menu1.inc.tpl'}>
	<table width="1000" border="0" cellpadding="4" cellspacing="0">
		<tr>
			<td bgcolor="#DBDBDB">
			<table width="100%" border="0" cellpadding="4" cellspacing="1">
				<tr>
					<td height="17" bgcolor="#FFFFFF">
						<div id="menu-lv2">
                        
						</div>
						<br/> 
						<h3></h3>
						<div id="container">
							<h1>編輯資料</h1>
							<br>

							<form name="membersNew" method="POST">
								<div id="tt" class="easyui-tabs" style="">
									<div title="基本資料" style="padding:20px;display:none;">
										<input type="hidden" name="cat" value="<{$cat}>">
										<input type="hidden" name="id" value="<{$id}>">
										<table cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="memberTB" style="width:100px;">&nbsp;</th>
                                                    <th class="memberTB" style="width:100px;">權限／資訊</th>
                                                    <th class="memberTB" style="width:600px;">說明</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        使用者
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <input type="text" name="pName" class="lock" maxlength="20" style="width:90px;" value="<{$data.pName}>">
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        帳戶擁有者姓名。
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        性別
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <{html_radios name=pGender options=$menuGender checked=$data.pGender}>
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        使用者性別、後台系統自動帶入稱謂用。
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        帳號
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <input type="text" name="pAccount" maxlength="12" style="width:90px;" value="<{$data.pAccount}>">
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        後台登入帳號；請填入"數字、字母" 4~12 碼，英文字母大小寫視為相異。
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        密碼
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <input type="password" name="pwd" style="width:90px;" maxlength="12" value="<{$data.pPassword}>">
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        後台登入密碼；請填入"數字、字母" 4~12 碼，英文字母大小寫視為相異。若未輸入密碼、則系統將自動產生一組密碼。
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        帳號管理
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <{html_radios name=pJob options=$menuAct checked=$data.pJob}>
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        啟用或停止帳號登入。
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        部門
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <select name="pDep" onchange="changeDepAuth()">
                                                        <{$depMenu}>
                                                        </select>
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        使用者部門單位。
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        分機
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <input type="text" name="pExt" class="lock" maxlength="3" style="width:100px;" value="<{$data.pExt}>">
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        使用者電話分機號碼。
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">
                                                        傳真號碼
                                                    </td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <input type="text" name="pFaxNumArea" class="lock" maxlength="2" style="width:20px;" value="<{$data.pFaxNumArea}>">-
                                                        <input type="text" name="pFaxNum" class="lock" maxlength="8" style="width:70px;" value="<{$data.pFaxNum}>">
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        使用者傳真號碼。(xx-xxxxxxxx)
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="memberCell" style="text-align:center;">到職日</td>
                                                    <td class="memberCell" style="text-align:center;">
                                                        <input type="text" name="pOnBoard" class="datepickerROC" style="width:100px;" value="<{$data.pOnBoard}>">
                                                    </td>
                                                    <td class="memberCell" style="text-align:left;">
                                                        <font color="red">(會影響績效一覽表的計算規則)</font>
                                                    </td>
                                                </tr>
                                            </tbody>
										</table>

										<div id="salesArea">
											<table cellspacing="0" border="0"  width="100%" style="margin-top:20px;">
                                                <thead>
                                                    <tr>
                                                        <th class="memberTB">業務專區</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="memberCell">業務行事曆色碼定義：<input type="color" name="pCalenderClass" value="<{$data.pCalenderClass}>"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="memberCell">業務測試區域：<{html_radios name=use options=$menuUse selected=$use onclick="getUse()"}></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="memberCell">
                                                            <div>縣市：</div>
                                                            <div>
                                                                <{foreach from=$menuCity key=key item=item }>
                                                                    <{if $key%3 == 0 && $key != 0}>
                                                                        <br>
                                                                    <{/if}>

                                                                    <{if $use == 0}>
                                                                        <input type="checkbox" name="city[]" class="trainee"  value="<{$item.trainee}>" <{$cityChecked[$item.city]}> disabled> <{$item.city}>
                                                                    <{else}>
                                                                        <input type="checkbox" name="city[]" class="trainee"  value="<{$item.trainee}>" <{$cityChecked[$item.city]}>> <{$item.city}>
                                                                    <{/if}>
                                                                <{/foreach}> 
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
											</table>
										</div>
									</div>
									<div title="權限" style="padding:20px;display:none;">
										<form name="formList" method="POST">
											<table cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <td class="memberTB" width="30%">名稱</td>
                                                        <td class="memberTB" width="70%">權限</td>	
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <{foreach from=$list_main key=key item=item}>
                                                    <tr style="background-color:#FFF0F5;">
                                                        <td class="memberCell" style="text-align:left;">
                                                            <{$item.pTxt}>
                                                        </td>
                                                        <td class="memberCell" style="text-align:left;"> &nbsp;
                                                            <{html_radios name="authority_<{$item.pId}>" options=$item.pAuthority2 selected="<{$auth["authority_<{$item.pId}>"]}>" class="" onclick="changeValue()"}>
                                                        </td>
                                                    </tr>
                                                        <{foreach from=$list_branch[$item.pId] key=k item=row}>
                                                        <tr style="background-color:#FFF;">
                                                            <td class="memberCell" style="text-align:left;">
                                                            <{$row.pTxt}>(<{$row.pName}>)
                                                            </td>
                                                            <td class="memberCell" style="text-align:left;">
                                                            <{html_radios name="authority_<{$row.pId}>" options=$row.pAuthority2 selected="<{$auth["authority_<{$row.pId}>"]}>" class="child_<{$item.pId}>" onclick="changeValue()"}>
                                                            </td>
                                                        </tr>
                                                        <{/foreach}>
                                                    <{/foreach}>
                                                </tbody>
											</table>
											<input type="hidden" name="chageVal">	
										</form>
									</div>
								</div>

							    <div>&nbsp;</div>

                                <div style="text-align: center;width: 100%">
                                    <input type="button" style="width:100px;" value="儲存" onclick="save()">
                                    <input type="button" style="width:100px;" value="返回" onclick="cancel()">
                                </div>
							</form>
						</div>
						<div id="footer" style="height:50px;">
							<p>2012 第一建築經理股份有限公司 版權所有</p>
						</div>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
</div>
</body>
</html>