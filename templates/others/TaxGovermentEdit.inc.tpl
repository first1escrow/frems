<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>地政事務所轄區編輯</title>
	<script src="../js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
	<script src="/js/combobox.js"></script>
	<link href="/css/combobox.css" rel="stylesheet">
	<script type="text/javascript">
		$(document).ready(function() {
			var check = "<{$msgCode}>";

			if (check) {
				alert("<{$msg}>");
			}
			if (check == 3) {

				parent.$.fn.colorbox.close();
			}

			$('[name="code"]').combobox();
		});
		function Del(){
			if (confirm("確定要刪除嗎?")) {
				$('[name="cat"]').val(3);
				$("#NewsForm").submit();
			}
			
		}
		function checkIDE(){
			
			if ($("[name='name']").val() == '' ) {
				alert("名稱不可為空");
				return false;
			}

			

			$("#NewsForm").submit();
			// $("#NewsForm").submit();

			// if ($("[name='password']").val() != $("[name='password2']").val()) {
			// 	alert('密碼錯誤，請再重新輸入');
			// 	$("[name='password']").val('');
			// 	$("[name='password2']").val('');
			// 	return false;
			// }
		}
		function getArea2(ct,ar,zp) {
            var url = '../escrow/listArea.php' ;
            var ct = $('#' + ct + ' :selected').val() ;
                
            $('#' + zp + '').val('') ;
            $('#' + zp + 'F').val('') ;
            $('#' + ar + ' option').remove() ;
                
            $.post(url,{"city":ct},function(txt) {
                var str = '' ;
                str = str + txt  ;
                $('#' + ar ).append(str) ;
            }) ;
        }
            
        function getZip2(ar,zp) {
            var zips = $('#' + ar + ' :selected').val() ;

            $('#' + zp + '').val(zips);
            $('#' + zp + 'F').val(zips.substr(0,3));
        }
        function showCity(){
			var type = $(".city").attr('id');
			var position = $(".city").position();  
	        var x = (position.left)+20;  
	         var y = (position.top);  
	      	if (type == 'open') {
	      		getMenuArea('city');
	      		$(".city").attr('id', 'close');
	      		$(".city1").css({
	      			'color':"#FFF",
		            'background-color': 'rgba(0, 0, 0, 0.5)',
		            'border': '1px solid #CCC',
		            'padding':'5px',
		            'width':'400px',
		            'float': 'right',
		            'z-index':'1',
		            'position':'absolute',
		            'left':x,
		            'top':y,
		            'display':'block',
		            'height':'auto'
		        });
	      	}else{
	      		closeCityMenu();
	      	}
	        
	        
		}
		function closeCityMenu(){
			$(".city").attr('id', 'open');
			$(".city1").hide();
		}

		function getMenuArea(city){

			$.ajax({
				url: '/sales/getMenuArea.php',
				type: 'POST',
				dataType: 'html',
				data: {'city': city},
			}).done(function(html) {
				$(".city1").html(html);
			});
			
		}
		function clickAll(){
			var check = $("[name='all']").attr("checked");

			if (check == 'checked') {
				$(".btnC input").attr('checked', 'checked');
			}else{
				$(".btnC input").removeAttr('checked');
			}
			// setZip($("[name='all']").val(),'all');
		}
		function delZip(c){
			$("#"+c).remove();
		}
		function setZip(){
			var html = $(".manager").html();
			var txt = '';
			var count = 0;

			if ($("[name='all']").attr("checked") == 'checked') {
			
				txt += "<span class=\"btnC showZip\" id=\""+$("[name='all']").val()+"\" name=\""+$("[name='all']").val()+"\"><span onClick=\"delZip('"+$("[name='all']").val()+"')\" class=\"del\">X</span>"+$("[name='all']").val()+"<input type=\"hidden\" name=\"managercity[]\" value=\""+$("[name='all']").val()+"\"></span>";
			}else{
				$("[name='all']").removeAttr('checked');
				$(".zip").each(function() {

					if ($(this).attr("checked") == 'checked') {
						txt += "<span class=\"btnC showZip\" id=\""+$(this).attr("id")+"\" name=\""+$(this).val()+"\"><span onClick=\"delZip('"+$(this).attr("id")+"')\" class=\"del\">X</span>"+$(this).attr("id")+"<input type=\"hidden\" name=\"managerzip[]\" value=\""+$(this).val()+"\"></span>";
						count++;
					}
					
				})

				if (count == $("[name='areaCount']").val()) {
					
					txt = "<span class=\"btnC showZip\" id=\""+$("[name='all']").val()+"\"><span onClick=\"delZip('"+$("[name='all']").val()+"')\" class=\"del\">X</span>"+$("[name='all']").val()+"</span>";
				}
			}
			
			$(".manager").html(html+txt);

			closeCityMenu();
		}
		function checkClick(){
			var count = 0;

			$("[name='all']").removeAttr('checked');

			$(".zip").each(function() {
				if ($(this).attr("checked") == 'checked') {
					count++;
				}
				if (count == $("[name='areaCount']").val()) {
					$("[name='all']").attr('checked', 'checked');
				}
			});
		}
        // function addArea(){
        	
        // 	var clone = $("[name='managerzip']").clone();
        // 	var city = $("[name='managercountry']").val();
        // 	var area = $("[name='managerarea']").find("option:selected").text();

        // 	clone.attr('name', 'managerzip[]');
        // 		console.log(area);
        // 	// $("#manager").append(clone);
        // 	clone.appendTo('#manager');
        // 	$("#manager").append(city);


        // }
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
		input,select,textarea {
			padding:5px;
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
        .btnC{
			color: #b48400;
			font-family: Verdana;
			font-size: 12px;
			font-weight: bold;
			line-height: 14px;
			background-color: #FFFFFF;
			text-align:center;
			display:inline-block;
			padding: 4px 6px;
			border: 1px solid #DDDDDD;
			margin-top: 5px;
			margin-right: 5px;
			cursor: pointer;
		}

		.btnC:hover {
			color: #b48400;
			font-size:12px;
			background-color: #000;
			border: 1px solid #FFFF96;
		}

		.btnC2{
			
			width:60px;
			height: 40px;
			
		}
		.btnC3{
			
			width:80px;
			height: 20px;
			
		}
	</style>
</head>
<body>
<center>
	<h1>稅捐稽徵處</h1>
	<form action="" method="POST" id="NewsForm" >
		<font color="red">*必填</font>
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr>
				<th><font color="red">*</font>名稱</th>
				<td><input type="text" name="name" value="<{$data.cName}>"></td>
			</tr>
			
			<tr>
				<th>地址</th>
				<td colspan="2">
					<input type="hidden" name="zip" id="zip" value="<{$data.cZip}>" />
                    <input type="text" maxlength="6" name="zipF" id="zipF" class="input-text-sml text-center" readonly="readonly" size="5" value="<{$data.cZip}>" />
                    <select class="input-text-big" name="country" id="country" onchange="getArea2('country','area','zip')">
                        <{$menuCity}>
                    </select>
                    <span id="areaR">
                    <select class="input-text-big" name="area" id="area" onchange="getZip2('area','zip')">
                        <{$menuArea}>
                    </select>
                    </span>
                    <input style="width:500px;" name="addr" value="<{$data.cAddress}>" />
				</td>
			</tr>
			<tr>
				<th>電話</th>
				<td colspan="2"><input type="text" name="phone" id="" value="<{$data.cTel}>"></td>
			</tr>
			<tr>
				<th>管轄區域</th>
				<td colspan="2">
					<input type="button" value="請選擇" class="city btn" onclick="showCity()" id="open">
					<div class="city1"></div>
					<div class="manager"><{$data.managerArea}></div>
					
				</td>
			</tr>
			
		</table>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<input type="button" value="送出" class="btn" onclick="checkIDE()">
			</div>
			<{if $cat > 1}>
				<div style="padding-left:30px;float:center;display:inline">
					<input type="button" value="刪除" onclick="Del()" class="btn">
				</div>
			<{/if}>
			
			<input type="hidden" name="id" value="<{$data.cId}>">
			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		
	</form>
</center>
	
</body>
</html>
