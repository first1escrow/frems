<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>最新消息修改</title>
	<script src="../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<script src="/js/combobox.js"></script>
	<link href="/css/combobox.css" rel="stylesheet">
	<script type="text/javascript">
		$(document).ready(function() {
			$('[name="code"]').combobox();
		});
		function Del(){
			if (confirm("確定要刪除嗎?")) {
				$('[name="cat"]').val(3);
				$("#NewsForm").submit();
			}
			
		}
		function checkIDE(){
			var ide = $("[name='lIdentity']:checked").val();

			if ($("[name='ParentPhone2']").val() == '' && ide != 'R') {
				alert("案件手機號碼不可為空");
				return false;
			}

			if ($("[name='code']").val() == '' && ide != 'B') {
				alert("事務所/公司代碼不可為空");
				return false;
			}

			$("#NewsForm").submit();

			// if ($("[name='password']").val() != $("[name='password2']").val()) {
			// 	alert('密碼錯誤，請再重新輸入');
			// 	$("[name='password']").val('');
			// 	$("[name='password2']").val('');
			// 	return false;
			// }
		}
	</script>
	<style>
		body{
			background-color: #F8ECE9
		}
		table th{
			width: 20%;
			text-align: right;
			padding: 5px;
			border: 1px solid #999;
		}
		table td{
			
			text-align: left;
			padding: 5px;
			border: 1px solid #999;
		}
		input {
			padding:10px;
			border:1px solid #CCC;
		}
		textarea{
			padding:10px;
			border:1px solid #CCC;
		}
		.btn {
		    color: #000;
		    font-family: Verdana;
		    font-size: 14px;
		    font-weight: bold;
		    line-height: 14px;
		    background-color: #CCCCCC;
		    text-align:center;
		    display:inline-block;
		    padding: 8px 12px;
		    border: 1px solid #DDDDDD;
		    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
		}
		.btn:hover {
		    color: #000;
		    font-size:12px;
		    background-color: #999999;
		    border: 1px solid #CCCCCC;
		}
		.btn.focus_end{
		    color: #000;
		    font-family: Verdana;
		    font-size: 14px;
		    font-weight: bold;
		    line-height: 14px;
		    background-color: #CCCCCC;
		    text-align:center;
		    display:inline-block;
		    padding: 8px 12px;
		    border: 1px solid #FFFF96;
		    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
		}
		.ui-combobox {
            position: relative;
            display: inline-block;
        }
        .ui-combobox-toggle {
            position: absolute;
            top: 0;
            bottom: 0;
            margin-left: -1px;
            padding: 0;
            /* adjust styles for IE 6/7 */
            *height: 1.5em;
            *top: 0.1em;
        }
        .ui-combobox-input {
            margin: 0;
            padding: 0.1em;
            width:160px;
        }
        .ui-autocomplete {
            width:160px;
            max-height: 300px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            /* add padding to account for vertical scrollbar */
            padding-right: 20px;
        }

        .ui-autocomplete-input {
            width:300px;
        }

	</style>
</head>
<body>
<center>
	<h1>帳號編輯</h1>
	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<th>姓名</th>
				<td><input type="text" name="name" value="<{$data.lNickName}>"></td>
			</tr>
			<tr>
				<th>第一階段認證</th>
				<td colspan="2"><{$data.lStage1Auth}></td>
			</tr>
			<tr>
				<th>第二階段認證</th>
				<td colspan="2"><{$data.lStage2Auth}></td>
			</tr>
			<tr>
				<th>帳號是否有效</th>
				<td colspan="2"><{html_radios name="status" options=$menu_status selected=$data.lStatus}></td>
			</tr>
			
			<tr class="s r">
				<th>事務所/公司代碼<br><span style="font-size:12px;color:red">(※身份為經紀人，可略過)</span></th>
				<td colspan="2">
					<select name="code" id="">
						<option value=""></option>
						<{foreach from=$menu_store key=key item=item}>
							<{if $key == $data.lTargetCode}>
								 <{assign var='ck' value='selected=selected'}> 
							<{else}>
								 <{assign var='ck' value=''}> 
							<{/if}>
							<option value="<{$key}>" <{$ck}>><{$item}></option>

						<{/foreach}>
					</select>
					
				</td>
			</tr>
			
			
			<tr class="s b">
				<th>手機號碼</th>
				<td colspan="2"><input type="text" name="ParentPhone" style="width:90%" maxlength="10" value="<{$data.lCaseMobile}>"></td>
			</tr>
			
			<tr class="s">
				<th>案件手機號碼<br><span style="font-size:12px;color:red">(※身份為仲介店，可略過)</span></th>
				<td colspan="2"><input type="text" name="ParentPhone2" style="width:90%" maxlength="10" value="<{$data.lCaseMobile2}>"></td>
			</tr>
			
			<tr>
				<th>身分別</th>
				<td colspan="2">
					<{html_radios name="lIdentity" selected=$data.lIdentity options=$menu_identity }>
					
				</td>
			</tr>
			<tr>
				<th>官網跟LINE同樣條件查詢<br><span style="font-size:12px;color:red">(※身份為仲介店，可略過)</span></th>
				<td colspan="2">
					<{html_radios name="web" selected=$data.lWeb options=$menu1 }>
				</td>
			</tr>
			<tr>
				<th>禁止看分店資料<br><span style="font-size:12px;color:red">(※身份為地政士，可略過)</span></th>
				<td><{html_radios name="branch" selected=$data.lbranch options=$menu1 }></td>
			</tr>
			<tr>
				<th>可看自己店裡的案件<br><span style="font-size:12px;color:red">代書是以號碼查詢有可能大型事務所不是每個案件都有設定到要查詢的手機號碼</span></th>
				<td colspan="2"><{html_radios name="scrivener" selected=$data.lScrivener options=$menu1 }></td>
			</tr>
			<tr>
				<th>複數店<br><span style="font-size:12px;color:red">(※請輸入地政士代碼，中間區隔以";"為主)</span></th>
				<td colspan="2">
					<input type="text"  name="childStore" style="width:90%" value="<{$data.lChildStore}>">
				</td>
			</tr>
			
			<tr>
				<th>建立時間</th>
				<td><{$data.lCreateTime}></td>
			</tr>
			<tr>
				<th>修改時間</th>
				<td><{$data.lModifyTime}></td>
			</tr>

		</table>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="button" value="送出" class="btn" onclick="checkIDE()">
			</div>
			<div style="padding-left:30px;float:center;display:inline">
				<!-- <input type="button" value="刪除" onclick="Del()" class="btn"> -->
			</div>
			<input type="hidden" name="id" value="<{$data.lId}>">
			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		
	</form>
</center>
	
</body>
</html>
