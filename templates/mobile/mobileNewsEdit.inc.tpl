<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>最新消息修改</title>
	<script src="../js/jquery-1.10.2.min.js"></script>
	<script src="../js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			CKEDITOR.replace( 'content',
				{
					// filebrowserUploadUrl: "",
					filebrowserImageUploadUrl : 'upload.php'

				}
			);
			$("[name='image']").change(function(){
				preview(this);	
			   
			});
			
		});
		function Del(){
			if (confirm("確定要刪除嗎?")) {
				$('[name="cat"]').val(3);
				$("#NewsForm").submit();
			}
			
		}
		function preview(input) {
	        if (input.files && input.files[0]) {
	            var reader = new FileReader();
	            // alert(input.files) ;
	            reader.onload = function (e) {
	                $('[name="show"]').attr('src', e.target.result);
	                var KB = format_float(e.total / 1024, 2);
	                $('.size').text("檔案大小：" + KB + " KB");
	            }
	 
	            reader.readAsDataURL(input.files[0]);
	       }
	    }
 		function checkValid() {
			var f = $('[name="image"]').val()
			if (f != '') {
				var re = /\.(jpg|png)$/i;  //允許的圖片副檔名 
				if (!re.test(f)) { 
					alert("只允許上傳 JPG 或 PNG 影像檔") ;
					event.returnValue = false ;
				}
			}
			
		}
    	 function format_float(num, pos) {
	        var size = Math.pow(10, pos) ;
	        return Math.round(num * size) / size ;
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
		/*textarea{
			padding:10px;
			border:1px solid #CCC;
		}*/
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
	<h1>最新消息編輯</h1>
	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" onsubmit="checkValid()">
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<th>文章標題<input type="hidden" name="id" value="<{$data.aId}>"></th>
				<td colspan="2"><input type="text" name="title" style="width:90%" value="<{$data.aTitle}>"></td>
			</tr>
			<tr>
				<th>文章內容</th>
				<td  colspan="2">
					<textarea name="content" id="" cols="100" rows="20"><{$data.aContent}></textarea>
				</td>
			</tr>
			<tr>
				<th>列表圖片手機(<br><span style='font-size:10px;color:red'>(限200x200大小)</span></th>
				<td><input type="file" name="image"></td>
				<td>
					<{if $cat == 1}>
						<img src="" style="width:67px;height:67px;" name="show">
						<div class="size"></div>
					<{else if $data.aPic != ''}>

						<img src="showPic.php?a=<{$data.aId}>" style="width:67px;height:67px;" name="show">
						<div class="size"></div>
					
						
					<{/if}>
				</td>
			</tr>
			<tr>
				<th>列表圖片平板(<br><span style='font-size:10px;color:red'>(限???x???大小)</span></th>
				<td><input type="file" name="image"></td>
				<td>
					<{if $cat == 1}>
						<img src="" style="width:67px;height:67px;" name="show">
						<div class="size"></div>
					<{else if $data.aPic != ''}>

						<img src="showPic.php?a=<{$data.aId}>" style="width:67px;height:67px;" name="show">
						<div class="size"></div>
					
						
					<{/if}>
				</td>
			</tr>
		</table>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="submit" value="送出" class="btn">
			</div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="button" value="刪除" onclick="Del()" class="btn">
			</div>
			
			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		
	</form>
</center>
	
</body>
</html>
