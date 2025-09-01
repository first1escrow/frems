<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title></title>
	<link rel="stylesheet" type="text/css" href="../../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<script src="../../js/jquery-1.7.2.min.js"></script>

	<script type="text/javascript" src="../../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<script src="../../js/datepickerRoc.js"></script>
	<link rel="stylesheet" href="/css/datepickerROC.css" />
	<script type="text/javascript">
		$(document).ready(function() {
			
		});
		
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
		.sp{
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
	<h1>Banner銀行編輯</h1>

	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<th>銀行名稱</th>
				<td>
					<input type="text" name="BankName" id="" value="<{$data.bBankName}>">
				</td>
			</tr>
			
			<tr>
				<th>銀行代碼(英文)</th>
				<td>
					<input type="text" name="Bank" id="" value="<{$data.bBank}>">
				</td>
			</tr>
			<tr>
				<th>房貸名稱</th> 
				<td><input type="text" name="BankName2" id="" value="<{$data.bBankName2}>"></td>
			</tr>
			<tr>
				<th>顯示地區</th>
				<td>
					<{assign var='i' value='0'}> 
					<{foreach from=$menu_city key=key item=item}>
						
						<input type="checkbox" name="Area[]" id="<{$i++}>" value="<{$key}>" <{$item}> ><{$key}>

						<{if ($i%5) == 0}>
							<bR>
						<{/if}>
					<{/foreach}>
					
				</td>
			</tr>	
		</table>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="submit" value="送出" class="btn">
				<input type="button" value="返回" class="btn" onclick="javascript:history.back();">
				<!-- <{if $cat=='mod'}>
					<{if $data.bOk == 0}>
						<input type="button" value="官網上架" class="btn" onclick="statusOK(<{$id}>)">
					<{else}>
						<input type="button" value="官網下架" class="btn" onclick="statusNO(<{$id}>)">
					<{/if}>

					<{if $data.bOk == 0}>
						<input type="button" value="官網上架" class="btn" onclick="statusOK(<{$id}>)">
					<{else}>
						<input type="button" value="官網下架" class="btn" onclick="statusNO(<{$id}>)">
					<{/if}>
				<{/if}> -->
			</div>
			<div style="padding-left:30px;float:center;display:inline">
				<!-- <input type="button" value="刪除" onclick="Del()" class="btn"> -->
			</div>
			<input type="hidden" name="id" value="<{$id}>">

			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		
	</form>
</center>
	
</body>
</html>
