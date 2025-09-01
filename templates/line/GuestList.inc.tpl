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
		function detail(id){
			$("[name=id]").val(id);
			$('[name="form"]').attr('action', 'GuestEdit.php');
			$('[name="form"]').submit();
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
	<form action="" method="POST" name="form">
		<input type="hidden" value="" name="id" >
	</form>
	<h1>訪客紀錄</h1>
	<table cellpadding="0" cellspacing="0" border="0" width="100%"> 
		<tr>
			<th>暱稱</th>
			<th>加入時間</th>
			<th></th>
		</tr>
		<{foreach from=$list key=key item=item}>
		<tr>
			<td><{$item.lNickName}></td>
			<td><{$item.lFollowTime}></td>
			<td><input type="button" value="編輯" onclick="detail(<{$item.lId}>)"></td>
		</tr>
		<{/foreach}>
	</table>
</center>
	
</body>
</html>
