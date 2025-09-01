<!DOCTYPE html>
<html>
<head>
	<script src="../js/jquery-1.7.2.min.js"></script>
	<meta charset="UTF-8">
	<title></title>
	<script type="text/javascript">
	$(document).ready(function() {
		$("[name='ok']").val('');
		$("[name='go']").on('click',function() {
			$("[name='ok']").val('1');
			alert('更新成功');
			$("[name='n']").submit();
		});

	});
	function del(id){
		$("[name='delid']").val(id);

		$('[name="delform"]').submit();
	}
	</script>
	<style>
		.btn {
			padding:10px 20px 10px 20px ;
			color:#212121 ;
			background-color:#F8ECE9 ;
			margin:2px ;
			border:1px outset #F8ECE0 ;
			cursor:pointer ;
		}
		.btn:hover {
			padding:10px 20px 10px 20px ;
			color:#212121 ;
			background-color:#EBD1C8 ;
			margin:2px;
			border:1px outset #F8ECE0;
			cursor:pointer;
		}
		.tb{
			border: 1px solid #CCC;
		}
		.tb th{
			border: 1px solid #CCC;
			background-color: #EBD1C8;
		}
		.tb td{
			border: 1px solid #CCC;
		}
	</style>
</head>
<body style="overflow-x:hidden">
<form action="" method="POST" name="delform">
	<input type="hidden" name="delid">

</form>

<form  method="POST" name="n">
<h1>備註</h1>
	
	<input type="text" name="Note" id="" value="" style="width:500px">
	<input type="button" value="送出" name="go" class="btn">
	<!-- <textarea name="Note" id="" cols="100" rows="10"><{$data.nContent}></textarea> -->
	<table id="go" class="tb" width="100%">
	<tr>
		<th width="70%">備註內容</th>
		<th width="20%">時間</th>
		<th>&nbsp;</th>
	</tr>
	<{foreach from=$data key=key item=item}>
		<tr>
			<td><{$item.nContent}></td>
			<td><{$item.nModifyTime}></td>
			<td><a href='#go' onclick="del(<{$item.nId}>)">刪除</a></td>
		</tr>
		
	<{/foreach}>
	</table>

	<input type="hidden" name="cat" value="<{$cat}>">
	<input type="hidden" name="ok">
	<br>
	
	
</form>
	
</body>
</html>