<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>

	<{include file='meta.inc.tpl'}> 
	<link rel="stylesheet" type="text/css" href="/js/checktree/checktree_styles.css" />	
	<script type="text/javascript" src="/js/checktree/jquery.checktree.js"></script>

	<script type="text/javascript">
	$(document).ready(function() {
		$( "#subTabs" ).tabs();

		$(".store1").hide();

		$(".city1").mouseleave(function() {
			closeCityMenu();
		});

		// $( "#branch_search" ).combobox() ;

		$( "#dialog" ).dialog({
			autoOpen: false,
			modal: true,
			minHeight:50,
			show: {
				effect: "blind",
				duration: 1000
			},
			hide: {
				effect: "explode",
				duration: 1000
			}
		});

		$(".ui-dialog-titlebar").hide() ;
		//
		
	});

	function areaDefault(ct, no) {
		var tag = '#salesArea' + no ;
		var sid = $(tag).val() ;
		//alert(ct + ',' + sid) ;
		
		$.ajax({
			url: 'salesBranchAreaDefault.php',
			type: 'POST',
			dataType: 'html',
			data: {'s': sid, 'c': ct},
		})
		.done(function(txt) {
			if (txt == 'T') alert("更新完成!!") ;
			else alert("更新失敗!!") ;
			//alert(txt) ;
		});
	}
	
	function areaDefaultT(ct, no) {
		var tag = '#TwhgSalesArea' + no ;
		var sid = $(tag).val() ;
		//alert(ct + ',' + sid) ;
		
		$.ajax({
			url: 'salesBranchAreaDefault.php',
			type: 'POST',
			dataType: 'html',
			data: {'s': sid, 'c': ct, 't': 1},
		})
		.done(function(txt) {
			if (txt == 'T') alert("更新完成!!") ;
			else alert("更新失敗!!") ;
			//alert(txt) ;
		});
	}
	function setArea(city){
		var url = "setAreaSales.php?cat=b&city="+city ;
		$.colorbox({iframe: true,width: "500px",height: "400px",href: url,onClosed: function() {
							//$('#reloadPage').submit() ;
							location.href="/sales/salesBranchArea.php";
					}}) ;
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
	            'background-color': 'rgba(0, 0, 0, 0.5)',
	            'border': '1px solid #CCC',
	            'padding':'5px',
	            'width':'350px',
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
			url: 'getMenuArea.php',
			type: 'POST',
			dataType: 'html',
			data: {'city': city},
		})
		.done(function(html) {
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
	function checkAllStore(){
		var check = $("[name='storeAll']").attr("checked");

		if (check == 'checked') {
			$(".ckStore").attr('checked', 'checked');
		}else{
			$(".ckStore").removeAttr('checked');
		}
		// setZip
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

	function setZip(){
		var html = $(".area").html();
		var txt = '';
		var count = 0;

		if ($("[name='all']").attr("checked") == 'checked') {
		
			txt += "<span class=\"btnC showZip\" id=\""+$("[name='all']").val()+"\" name=\""+$("[name='all']").val()+"\"><span onClick=\"delZip('"+$("[name='all']").val()+"')\" class=\"del\">X</span>"+$("[name='all']").val()+"</span>";
		}else{
			$("[name='all']").removeAttr('checked');
			$(".zip").each(function() {

				if ($(this).attr("checked") == 'checked') {
					txt += "<span class=\"btnC showZip\" id=\""+$(this).attr("id")+"\" name=\""+$(this).val()+"\"><span onClick=\"delZip('"+$(this).attr("id")+"')\" class=\"del\">X</span>"+$(this).attr("id")+"</span>";
					count++;
				}
				
			})

			if (count == $("[name='areaCount']").val()) {
				
				txt = "<span class=\"btnC showZip\" id=\""+$("[name='all']").val()+"\"><span onClick=\"delZip('"+$("[name='all']").val()+"')\" class=\"del\">X</span>"+$("[name='all']").val()+"</span>";
			}
		}
		
		$(".area").html(html+txt);

		closeCityMenu();
	}
	
	function searchB(){

		var sales = $("[name=sales]").val();
		var branch = $("[name='branch']").val();
		var str = new Array();
		var i = 0;
		$(".showZip").each(function() {
			str[i] = $(this).attr('name');
			i++;
		});
		
		$.ajax({
			url: 'salesBranchAreaAjax.php',
			type: 'POST',
			dataType: 'html',
			data: {"sales": sales,"branch":branch,"area":str},
		}).done(function(msg) {
			
			$("#store").html(msg);
			$(".store1").show();
		});
		
	}

	function delZip(c){
		$("#"+c).remove();
	}

	function setSales(){
		var sales = $("[name='sSales']").val();
		var act = $("[name='act']:checked").val();

		var str = new Array();
		var i = 0;
		$(".ckStore").each(function() {
			if ($(this).attr("checked") == 'checked') {
				str[i] = $(this).val();
				i++;
			}
			
		});

		if (sales == 0) {
			alert("請選擇業務");
		}

		if (i == 0) {
			alert("請選擇店家");
			return false;
		}

		$.ajax({
			url: 'setBranchSales.php',
			type: 'POST',
			dataType: 'html',
			data: {"sales": sales,"branch":str,"cat":act},
		})
		.done(function(msg) {
			
			
			alert(msg);
			searchB();
		});
		
	}
	function delSales(bId,sales){

		var str = new Array();
		str[0] = bId;

		if (confirm("確定是否要刪除?")) {
			$.ajax({
				url: 'setBranchSales.php',
				type: 'POST',
				dataType: 'html',
				data: {"branch":str,"sales":sales,"cat":'del'},
			})
			.done(function(msg) {
				
				alert(msg);
				searchB();
			});
		}

		
	}
	function export_xls() {
		$("[name='xls']").val('xls');
		$("[name='exp']").submit();
	}
	</script>
	<style>
		.tab-contract{
			background-color: #FFF;
		}
	</style>
<!-- 	<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
		.leftBlock{
			float:left;
			display:inline;
			/*border: 1px solid #999;*/
			width: 59%;
			height: auto;
		}
		.rightBlock{
			float:right;
			display:inline;
			/*border: 1px solid red;*/
			width: 40%;
			height: auto;

		}
		.rightBlock table{
			float:right;
		}
		.topBlock{
			
			/*border: 1px solid green;*/
			width: 100%;
			height: 1000px;
		}

		.bottomBlock{
			
			/*border: 1px solid orange;*/
			width: 100%;
			height: auto;
		}
		.city{
			border:1px solid #999;
			width:100px;
			
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
		
		.del{
			cursor: pointer;
		}

		.searchleft1{
			
			/*border: 1px solid blue;*/
			width: 350px;
			padding-bottom: 5px;
		}
		.searchleft2{
			/*border: 1px solid green;*/
			width: 350px;
		}

		.searchright{
			/*border:1px #CCC solid;*/
			width: 160px;
			float: left;
			margin-top:-50px;
			margin-left: 353px;
		}
		.store{
			overflow-y:scroll;
			overflow-x:hidden;
			height: 700px;
			margin-top: 10px;
			border:1px solid #999;
		}
		.store1{
			overflow-y:hidden;
			overflow-x:hidden;
			height: 50px;
			padding: 5px;
			border:1px solid #CCC;
		}
		.tb th{
			color: rgb(255, 255, 255);
			font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
			font-size: 1em;
			font-weight: bold;
			background-color: rgb(156, 40, 33);
			border: 1px solid #CCCCCC;
			padding: 6px;
		}
		.tb td{
			padding: 6px;
		    border: 1px solid #CCCCCC;
		    text-align: left;
		}
		

		.tb input[type="checkbox"]{
			display: inline-block;
		    width: 20px;
		    height: 20px;
		    margin: -3px 4px 0 0;
		    vertical-align: middle;
		}
		
		#branch_search{
			width:200px;
		}
		/*.ui-autocomplete-input {
			width:80px;
		}*/
		
	</style> -->
</head>
<body id="dt_example" >
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
<form name="exp" method="POST" action="salesBranchAreaExcel.php" target="_blank">
	<input type="hidden" name="xls" value="">
</form>
<table width="1000" border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td bgcolor="#DBDBDB">
			<table width="100%" border="0" cellpadding="4" cellspacing="1">
				<tr>
					<td height="17" bgcolor="#FFFFFF">
						<div id="menu-lv2"></div>
                        <br/>                        
						<h3>&nbsp;</h3>		
						<div id="container">	 
							<div id="dialog"></div>
							<h1>負責業務歸屬</h1>	
							<div id="subTabs">
								<ul>
									<li><a href="#subTabs-1">區域更改</a></li>
									<li><a href="#subTabs-2">單店更改</a></li>
									<li><a href="#subTabs-3">備註</a></li>							
								</ul>
									<div id="subTabs-1" class="tab-contract">
									<table >
										<tr>
											<th style="text-align:left;">
												縣市預設業務：
												<font color="red">點擊縣市可設定區域業務</font>
											</th>
										</tr>
										<tr>				
											<td id="cg">
												<ul class="tree" style="margin-left: 5px;">
													<{$i = 0}><{$j = 0}>
													<div style="float:left;width:80px;">縣市</div>			
													<div style="float:left;width:80px;">非台屋</div>
													<div style="float:left;width:80px;">台屋系統</div>
													<div style="clear:both;height:10px;border-top-width:1px;border-top-style:solid;"></div>
													<{$i = $i + 1}><{$j = $j + 1}>
													<{foreach from=$areaSales key=key item=item}>
													<div style="float:left;width:80px;">
														<a href="#" onclick="setArea('<{$key}>')" title="點擊可設定區域業務"><{$key}></a>
													</div>							
													<div style="float:left;width:80px;">
														<select id="salesArea<{$j}>" onchange="areaDefault('<{$key}>',<{$j}>)">
															<{$item["menu"]}>
														</select>
													</div>
													<div style="float:left;width:80px;">
														<select id="TwhgSalesArea<{$j}>" onchange="areaDefaultT('<{$key}>',<{$j}>)">
															<{$item["menuTwhg"]}>
														</select>
													</div>
														<{$i = $i + 1}><{$j = $j + 1}>
														<div style="clear:both;"></div>
													<{/foreach}>
												</ul>
											</td>
										</tr>
									</table>
								</div>
								<div id="subTabs-2" class="tab-contract">
									<div style="display:block;border:1px #000 solid; padding:5px;">
										※請先查詢後再設定業務
										
										<div class="searchleft1">
										業務:<{html_options name=sales options=$menu_sales}>
										店名:<{html_options name=branch options=$menu_Store id=branch_search}>
										</div>
										
										<div class="searchleft2">
											縣市:<input type="button" value="請選擇" class="city" onclick="showCity()" id="open">(預設是查詢全部)
												<div class="city1"></div>	
										</div>
										<div class="searchright">
											<input type="button" value="查詢" class="btnC btnC2" onclick="searchB()">
										</div>	
										<div class="area"></div>
									</div>
									
									
									<div class="store store1">
										
										<div>
											<input type="radio" name="act" id="" value="add" checked>新增
											<input type="radio" name="act" id="" value="rep">取代
											<input type="radio" name="act" id="" value="del">刪除
										</div>
										<div>

											業務:<{html_options name=sSales options=$menu_sales}>
											<input type="button" value="設定" class="btnC" onclick="setSales()">
										</div>
									
										

									</div>
									<div class="store" id="store">
										
									</div>	 	
										 
									
								</div>
							
								<div id="subTabs-3" class="tab-contract">
									<iframe src="salesNote.php?cat=2" frameborder="0"  width="100%" height="100%"></iframe>	
								</div>
							</div>
							<div id="footer" style="height:50px;">
								<p>2012 第一建築經理股份有限公司 版權所有</p>
							</div>
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