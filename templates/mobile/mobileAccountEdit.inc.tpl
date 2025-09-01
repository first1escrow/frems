<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>最新消息修改</title>
	<script src="../js/ckeditor/adapters/jquery.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("[name='image']").change(function(){
			    if (this.files && this.files[0]) {
		                var reader = new FileReader();
		                
		                reader.onload = function (e) {
		                        $('[name="show"]').attr('src', e.target.result);
		                }
		                
		                reader.readAsDataURL(this.files[0]);
		        }
			});
		});
		function Del(){
			if (confirm("確定要刪除嗎?")) {
				$('[name="cat"]').val(3);
				$("#NewsForm").submit();
			}
			
		}
		function check(){

			if ($("[name='password']").val() != $("[name='password2']").val()) {
				alert('密碼錯誤，請再重新輸入');
				$("[name='password']").val('');
				$("[name='password2']").val('');
				return false;
			}
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
	</style>
</head>
<body>
<center>
	<h1>帳號編輯</h1>
	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<th>姓名</th>
				<td><input type="text" name="name" value="<{$data.aName}>"></td>
			</tr>
			<tr>
				<th>帳號<input type="hidden" name="id" value="<{$data.aId}>"></th>
				<td colspan="2">
				<{if $cat == 1}>
					<input type="text" style="width:90%" value="<{$data.aAccount}>" name='account'>
				<{else}>
					<input type="text" style="width:90%" value="<{$data.aAccount}>" disabled>
				<{/if}>
				</td>
			</tr>
			<tr>
				<th>密碼</th>
				<td  colspan="2">
					<input type="text"  value="<{$data.aPassword}>" disabled>
				</td>
			</tr>
			<!-- <tr>
				<th>再次確認密碼</th>
				<td colspan="2">
					<input type="text"  value="<{$data.aPassword}>">	
				</td>
			</tr> -->
			<tr>
				<th>帳號是否有效</th>
				<td colspan="2"><{html_radios name="status" options=$menu_status selected=$data.aStatus}></td>
			</tr>
			<tr>
				<th>事務所/公司代碼</th>
				<td colspan="2"><input type="text" name="ParentId" style="width:90%" value="<{$data.aParentId}>"></td>
			</tr>
			<tr>
				<th>案件分類手機號碼</th>
				<td colspan="2"><input type="text" name="ParentPhone" style="width:90%" maxlength="10" value="<{$data.aParentPhone}>"></td>
			</tr>
			<tr>
				<th>身分別</th>
				<td colspan="2"><{html_radios name="identity" selected=$data.aIdentity options=$menu_identity}></td>
			</tr>
			<{if $cat != 1}>
			<tr>
				<th>裝置ID</th>
				<td colspan="2"><input type="text" name="DeviceId" style="width:90%" value="<{$data.aDeviceId}>"></td>
				
			</tr>
			<tr>
				<th>手機型號</th>
				<td colspan="2"><input type="text" name="Model" style="width:90%" value="<{$data.aModel}>">	</td>
			</tr>
			<{/if}>
			<tr>
				<th>授權碼</th>
				<td colspan="2"><{$data.aAuthCode}></td>
			</tr>
			<tr>
				<th>授權碼到期日</th>
				<td colspan="2"><{$data.aAuthExpire}></td>
			</tr>
			<tr>
				<th>已完成認證</th> 
				<td><{html_radios name="ideOk" selected=$data.aOK options=$menu_OK}></td>
			</tr>
			<!-- <tr>
				<th>SLACKID</th>
				<td><input type="text" name="slackId" value="<{$data.aSlackId}>"></td>
			</tr> -->
			
			<tr>
				<th>預設簡訊發送方式</th>
				<td><{html_radios name="sms" selected=$data.aSmsOption options=$menu_sms}></td> 
			</tr>
			<tr>
				<th>是否可以看仲介群組</th>
				<td><{html_radios name="group" selected=$data.aGroup options=$menu}></td>
			</tr>
			<tr>
				<th>備註</th>
				<td><textarea name="memo" cols="30" rows="10"><{$data.aMemo}></textarea></td>
			</tr>
			<tr>
				<th>登入IP</th>
				<td><{$data.aRemoteIP}></td>
			</tr>
			
			<tr>
				<th>最後登入時間</th>
				<td><{$data.aLoginTime}></td>
			</tr>
			<tr>
				<th>建立時間</th>
				<td><{$data.aCreateTime}></td>
			</tr>
			<tr>
				<th>修改時間</th>
				<td><{$data.aLastModify}></td>
			</tr>

		</table>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="submit" value="送出" class="btn">
			</div>
			<div style="padding-left:30px;float:center;display:inline">
				<!-- <input type="button" value="刪除" onclick="Del()" class="btn"> -->
			</div>
			
			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		
	</form>
</center>
	
</body>
</html>
