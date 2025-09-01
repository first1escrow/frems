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
		function statusOK(id){
			if (confirm("確定要上架嗎?")) {
				$.ajax({
					url: 'status.php',
					type: 'POST',
					dataType: 'html',
					data: {cat: 'ok',id:"<{$id}>"},
				}).done(function(txt) {
					if (txt) {
						alert('上架成功');
					}
							
				});
						
			}
			
		}

		function statusNO(id){
			if (confirm("確定要下架嗎?")) {
				$.ajax({
					url: 'status.php',
					type: 'POST',
					dataType: 'html',
					data: {cat: 'no',id:"<{$id}>"},
				}).done(function(txt) {
					if (txt) {
						alert('下架成功');
					}
							
				});
						
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
	<h1>Banner編輯</h1>

	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<th>銀行</th>
				<td>
					<{html_options name=bank options=$menu_bank selected=$data.bBank class="sp"}>
				</td>
			</tr>
			
			<tr>
				<th>排序</th>
				<td>
					<{html_options name=sort options=$menu_sort selected=$data.bSort class="sp"}>
				<!-- <input type="text" name="sort" value="<{$data.bSort}>"></td> -->
			</tr>
			<tr>
				<th>官網</th>
				<td><{html_options name=ok options=$menu_publish selected=$data.bOk class="sp"}></td>
			</tr>
			<tr>
				<th>地政士後台</th>
				<td><{html_options name=ok2 options=$menu_publish selected=$data.bOk class="sp"}></td>
			</tr>
			<tr>
				<th>Banner</th>
				<td>

					<input type="file" name="upload_file">(限290X90px圖檔)
					<{if $data.bPic != ''}>
						<a href="<{$data.bPic}>" target="_blank">觀看</a>&nbsp;|&nbsp;
						<input type="checkbox" name="delbPic" id="" value="1">刪除
					<{/if}>
				</td>
			</tr>
			<tr>
				<th>文件DM</th>
				<td>
					<input type="file" name="upload_file2">
					<{if $data.bUrl != ''}>
						<a href="<{$data.bUrl}>" target="_blank">觀看</a>&nbsp;|&nbsp;
						<input type="checkbox" name="delbUrl" id="" value="1">刪除
					<{/if}>
				</td>
			</tr>
			<tr>
				<th>圖片</th>
				<td>
					<input type="file" name="upload_file3">
					<{if $data.bPicWindow != ''}>
						<a href="http://www.first1.com.tw/bank/upload/<{$data.bPicWindow}>" target="_blank">觀看</a> &nbsp;|&nbsp;

						<a href="https://escrow.first1.com.tw/images/ads/upload/<{$data.bPicWindow}>" target="_blank">觀看2</a>&nbsp;|&nbsp;

						<input type="checkbox" name="delbPicWindow" id="" value="1">刪除
					<{/if}>
				</td>
			</tr>
			<tr>
				<th>網址(直接連到對方網頁)</th>
				<td>
					<input type="text" name="link" value="<{$data.bLink}>" style="width:250px">

					<{if $data.bLink != ''}>
						<a href="<{$data.bLink}>" target="_blank">觀看</a>
					<{/if}>
				</td>
			</tr>
			<!-- <tr>
				<th>需要另開連結到對方網址</th>
				<td></td>
			</tr> -->
			<tr>
				<th>需要另開連結到對方網址</th>
				<td>
					<input type="text" name="link2" value="<{$data.bLink2}>" style="width:250px">
					<{if $data.bLink2 != ''}>
						<a href="<{$data.bLink2}>" target="_blank">觀看</a>
					<{/if}>
				</td>
			</tr>
			
			<tr>
				<th>彈跳視窗時間</th>
				<td>
					<input type="text" name="DateStart" class="datepickerROC" value="<{$data.bStart}>" >至
					<input type="text" name="DateEnd" class="datepickerROC" value="<{$data.bEnd}>" >
					(<input type="checkbox" name="window" value="1" <{$data.check}> >不彈跳)
				</td>
			</tr>
			
			
			
		</table>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="submit" value="送出" class="btn">
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
