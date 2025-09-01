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
		function Del(id){
			if (confirm("確定要刪除嗎?")) {
				$('[name="id"]').val(id);
				$("#formDel").submit();
			}
			
		}
		function check(){
			

			if ($("[name='name']").val() == '') {
				alert("姓名不可為空");
				return false;
			}

			if ($("[name='mobile']").val() == '') {
				alert("手機不可為空");
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
			text-align: center;
			padding: 5px;
			border: 1px solid #999;
		}
		table td{
			
			text-align: center;
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
	<h1>業務帳號</h1>
	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
		<div>
			姓名：<input type="text" name="name" value="">
			手機：<input type="text" name="mobile" value="" maxlength="10">
			<input type="button" value="新增" class="btn" onclick="check()">
		</div>
	</form>
		<hr>
		<div>
		<form action="" method="POST" id="formDel">
			<input type="hidden" name="id">
			<table cellpadding="0" cellspacing="0" border="0" width="80%">
				<tr>
					<th>姓名</th>
					<th>手機</th>
					<th></th>
				</tr>
				<{foreach from=$data key=key item=item}>
				<tr>
					<td><{$item.cName}></td>
					<td><{$item.cMobile}></td>
					<td><input type="button" value="刪除" onclick="Del(<{$item.cId}>)"></td>
				</tr>
				<{/foreach}>
			</table>
		</form>
		</div>
		<br>
		
		
	
</center>
	
</body>
</html>
