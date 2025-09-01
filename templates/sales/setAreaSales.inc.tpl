<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>

	<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>


	<script type="text/javascript">
	$(document).ready(function() {
		var cat = "<{$cat}>";
		if ( cat== 'b') {
			$(".scrivener").hide();
		}else{
			$(".branch").hide();
		}

	});

	
	</script>
	<style>
		body{
			background-color: #F8ECE9;
		}
		div{
			padding: 5px;
		}
		.default_sales{
			border: 1px solid #999;
			padding: 3px;

		}
		
	</style>
</head>
<body>
<center>
<form action="" method="POST">
<div class="branch">
	<div style="float:left;width:80px;">區域</div>
	<div style="float:left;width:140px;">非台屋</div>
	<div style="float:left;width:140px;">台屋系統</div>
	<div style="clear:both;height:10px;border-top-width:1px;border-top-style:solid;"></div>
 	<{foreach from=$areaSales key=key item=item}>
 	<div style="float:left;width:80px;"><{$key}></div>
	<div style="float:left;width:140px;">
		<!-- <{html_checkboxes name="Sales_<{$item.zZip}>" options=$menuSales selected=$item.zSales}> -->
		<!-- <select id="salesArea<{$j}>" onchange="areaDefault('<{$key}>',<{$j}>)">
			<{$areaSales[$key]["menu"]}>
		</select> -->
		<{foreach from=$item['sales'] key=k item=v}>
				<span class="default_sales"><{$v}></span> &nbsp;
        <{/foreach}>
	</div>
	<div style="float:left;width:140px;">
		<{foreach from=$item['salesTW'] key=k item=v}>
			<span class="default_sales"><{$v}></span> &nbsp;
       <{/foreach}>
		<!-- <{html_checkboxes name="SalesTwhg_<{$item.zZip}>" options=$menuSales selected=$item.zSalesTwhg}> -->
		<!-- <select id="TwhgSalesArea<{$j}>" onchange="areaDefaultT('<{$key}>',<{$j}>)">
			<{$areaSales[$key]["menuTwhg"]}>
		</select> -->
	</div>
	<div style="clear:both;"></div>

 	<{/foreach}>
</div>
<div class="scrivener">
	<div style="float:left;width:80px;">區域</div>
	<div style="float:left;width:140px;">業務</div>
	<div style="clear:both;height:10px;border-top-width:1px;border-top-style:solid;"></div>
 	<{foreach from=$areaSales key=key item=item}>
 	<div style="float:left;width:80px;"><{$key}></div>
	<div style="float:left;width:140px;">
		<!-- <{html_options name="ScrivenerSales_<{$item.zZip}>" options=$menuSales selected=$item.zScrivenerSales}>		 -->
		<{foreach from=$item['zScrivenerSales'] key=k item=v}>
				<span class="default_sales"><{$v}></span> &nbsp;
        <{/foreach}>
	</div>
	
	
	<div style="clear:both;"></div>

 	<{/foreach}>
</div>

<!-- <div> <input type="submit" value="送出"><input type="hidden" name="cat" value="<{$cat}>"></div> -->

</form>
</center>
</body>
</html>