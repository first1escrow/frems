<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<{include file='meta.inc.tpl'}>	
<!-- <script src="../js/highcharts.js"></script> -->

<script type="text/javascript" src="/js/hightChartMap/highmaps.js"></script>
<!-- <script type="text/javascript" src="/js/hightChartMap/exporting.js"></script> -->
<script type="text/javascript" src="/js/hightChartMap/tw-all.js"></script>



<script type="text/javascript">
$(document).ready(function() {

//
var data= [
	//北
	{
	    "value": <{$area['tw-tw']['part']}>,
	    "value2": <{$area['tw-tw']['count']}>,
	    "hc-key": "tw-tw",
	    "color":"#FF7878"
	},{
	    "value": <{$area['tw-tp']['part']}>,
	    "value2": <{$area['tw-tp']['count']}>,
	    "hc-key": "tw-tp",
	    "color":"#FF7878"
	},{
	    "value": <{$area['tw-cl']['part']}>,
	    "value2": <{$area['tw-cl']['count']}>,
	    "hc-key": "tw-cl",
	    "color":"#FF7878"
	},{
	    "value": <{$area['tw-ty']['part']}>,
	    "value2": <{$area['tw-ty']['count']}>,
	    "hc-key": "tw-ty",
	    "color":"#FF7878"
	},{
	    "value": <{$area['tw-hh']['part']}>,
	    "value2": <{$area['tw-hh']['count']}>,
	    "hc-key": "tw-hh",
	    "color":"#FF7878"
	},{
	    "value": <{$area['tw-ml']['part']}>,
	    "value2": <{$area['tw-ml']['count']}>,
	    "hc-key": "tw-ml",
	    "color":"#FF7878"
	},
	//東
	{
	    "value": <{$area['tw-il']['part']}>,
	    "value2": <{$area['tw-il']['count']}>,
	    "hc-key": "tw-il",
	    "color":"#00E600"
	},
	{
	    "value": <{$area['tw-tt']['part']}>,
	    "value2": <{$area['tw-tt']['count']}>,
	    "hc-key": "tw-tt",
	    "color":"#00E600"
	},{
	    "value": <{$area['tw-hl']['part']}>,
	    "value2": <{$area['tw-hl']['count']}>,
	    "hc-key": "tw-hl",
	    "color":"#00E600"
	},
	//中部 tw-cs
	
	{
	    "value": <{$area['tw-th']['part']}>,
	    "value2": <{$area['tw-th']['count']}>,
	    "hc-key": "tw-th",
	    "color":"#FFB326"
	},{
	    "value": <{$area['tw-nt']['part']}>,
	    "value2": <{$area['tw-nt']['count']}>,
	    "hc-key": "tw-nt",
	    "color":"#FFB326"
	},{
	    "value": <{$area['tw-cg']['part']}>,
	    "value2": <{$area['tw-cg']['count']}>,
	    "hc-key": "tw-cg",
	    "color":"#FFB326"
	},
	{
	    "value": <{$area['tw-yl']['part']}>,
	    "value2": <{$area['tw-yl']['count']}>,
	    "hc-key": "tw-yl",
	    "color":"#FFB326"
	},{
	    "value": <{$area['tw-ch']['part']}>,
	    "value2": <{$area['tw-ch']['count']}>,
	    "hc-key": "tw-ch",
	    "color":"#FFB326"
	},
	,
	//南部
	{
	    "value": <{$area['tw-kh']['part']}>,
	    "value2": <{$area['tw-kh']['count']}>,
	    "hc-key": "tw-kh",
	    "color":"#55CCFF"
	},{
	    "value": <{$area['tw-tn']['part']}>,
	    "value2": <{$area['tw-tn']['count']}>,
	    "hc-key": "tw-tn",
	    "color":"#55CCFF"
	},{
	    "value": <{$area['tw-pt']['part']}>,
	    "value2": <{$area['tw-pt']['count']}>,
	    "hc-key": "tw-pt",
	    "color":"#55CCFF"
	},
	//外島
	{
	    "value": <{$area['tw-ph']['part']}>,
	    "value2": <{$area['tw-ph']['count']}>,
	    "hc-key": "tw-ph",
	    "color":"#BABABA"
	},{
	    "value": <{$area['tw-km']['part']}>,
	    "value2": <{$area['tw-km']['count']}>,
	    "hc-key": "tw-km",
	    "color":"#BABABA"
	}
	// ,{
	//     "value": <{$area['tw-lk']['part']}>,
	//     "value2": <{$area['tw-lk']['count']}>,
	//     "hc-key": "tw-lk",
	//     "color":"#BABABA"
	// }
	];


	Highcharts.mapChart('chart', {
	    chart: {
	        map: 'countries/tw/tw-all',
		   	mapData: Highcharts.maps['countries/tw/tw-all'],
	    },

	    title: {
	    	
			useHTML:true,
	        text: '<span style="color:black;font-size:30px;font-weight:solid;">經辦區域統計表</span>'
	    },

	    subtitle: {
	        text: ''
	    },

	    mapNavigation: {
	        enabled: false,
	        buttonOptions: {
	            verticalAlign: 'bottom'
	        }
	    },
	    legend:{
	    	enabled: false,
	    },

	    colorAxis: {
	        min: 0
	    },
	    credits:{
		        enabled:true,
		        href:'',
		        mapText:"",
		        mapTextFull:'',
		       
		        text:'',
		},
	    tooltip:{
			animation:true,
			backgroundColor:"rgba(247,247,247,0.85)",
			
			headerFormat:"{point.key}",
			
			pointFormat:"{point.value}%",
			footerFormat:"",
			useHTML:true,
			
		},
		 
	    series: [{
	    	type: "map",
	        data: data,
	        name: '',
	        states: {

	            hover: {
	                color: '#FFF'
	            }
	        },
	        dataLabels: {
	            enabled: true,
	            useHTML:true,
	            format: '<div class="label" >{point.name}{point.value2}件{point.value}%</div>',
	            x:0,
	            y:0,


	        },
	        
	    }]
	});




// $('#chart').highcharts().mapZoom(0.7,10,10);
});



////translate(293,938)
</script>
<style>
.label{
	font-size:14px;
	/*line-height: 20px;*/
	background-color: snow;
	color: black;
	border:1px solid black;
	z-index: 0;
	padding:2px; 

}


#chart {
    height: 700px; 
    min-width: 310px; 
    max-width: 800px; 
    margin: 0 auto; 

}
.btn {
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #CCC;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #DDDDDD;
	/*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn:hover {
	color: #000;
	font-size:14px;
	background-color: #FFF;
	border: 1px solid #CCCCCC;
}
.btn.focus_end{
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #CCC;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #FFFF96;
	  /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
</style>
</head>
<body id="dt_example">
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
							<td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
							<td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
						</tr>
					</table>
				</td>
			</tr>
		</table> 
	</div>
	<{include file='menu1.inc.tpl'}>
	<table width="1000" border="0" cellpadding="4" cellspacing="0">
		<tr>
			<td bgcolor="#DBDBDB">
				<table width="100%" border="0" cellpadding="4" cellspacing="1">
					<tr>
						<td height="17" bgcolor="#FFFFFF">
							<div id="menu-lv2">
                                                        
							</div>
							<br/> 
							<h3>&nbsp;</h3>
							<div id="container">
								<div id="dialog">
								
								</div>
								<h1>經辦區域統計表</h1>
								<div>
									<form action="" method="POST">
										<input type="submit" value="產生EXCEL表" class="btn">
										<input type="hidden" name="ok" value="1">
									</form>
								</div>
								
								<br><br>
								<div id="chart"></div>
								<cneter>
								<div style="text-align:center;font-size:20px;line-heigh:22px;">
									總進行件數:<{$total}>
									
								</div>
								</cneter>
								<div id="footer" style="height:50px;clear:both;">
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