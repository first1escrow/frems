<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />
<script src="../js/jquery-1.7.2.min.js"></script>
<script src="../js/jquery.colorbox.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$( "#subTabs" ).tabs();

	var ef = "<{$effect_type}>";

	if (ef == 1) {
		$("#e1").show();
		$("#e2").hide();
	} else {
		$("#e2").show();
		$("#e1").hide();
	}
	
	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"300"});
	$(".inline").colorbox({inline:true, width:"700", height:"700"});
    $(".inline2").colorbox({inline:true, width:"700", height:"500"});

    <{$script}>
	
	// enter 輸入
	$(this).keypress(function(e) {
		if (e.keyCode == 13) {
			save() ;
		}
	}) ;

	$('[name="excel"]').on('click', function() {
		$("[name='ex']").attr('value', 'ok');
		$('[name="traceXls"]').val('') ;
		$("#salesForm").submit();
	});

	$('[name="su"]').on('click', function() {
		$("[name='ex']").attr('value', '');

		if ($("[name='sales']").val() == '0') {
			alert('請選擇業務對象!!');
			return false;
		}

		$('[name="traceXls"]').val('') ;
		$("[name='excel']").show();
		$("#salesForm").submit();
	});

	$('[name="excel"]').button( {
        icons:{
            primary: "ui-icon-document"
        }
    });

    $('[name="su"]').button( {
        icons:{
            primary: "ui-icon-document"
        }
    });
});

function tab2(v,name)
{
	$(".focus_page li").attr('class', '');

	if (v == 1) {
		$("#e1").show();
		$("#e2").hide();
	}else{
		$("#e1").hide();
		$("#e2").show();		
	}

	$("#"+name).attr('class', 'focus_end');
}

function colorbx(url) {
	$.colorbox({href:url});
}

function teaceXls() {
	var ss = $('[name="sales"]').val() ;
	var yy = $('[name="dateYear"]').val() ;
	var mm = $('[name="dateMonth"]').val() ;
	
	if (ss == '0') {
		$('[name="traceXls"]').val('') ;
		alert("請選擇業務對象!!") ;
		$('[name="sales"]').focus() ;
		return false ;
	}
	else {
		$('[name="traceXls"]').val('trace') ;
		$("#salesForm").submit();
	}
}

function chagePercent(){
    var pSalesId = $('[name="sales"]').val();
    var pYear = $('[name="dateYear"]').val();
    var pMonth = $('[name="dateMonth"]').val();
    var pSign = $("[name='percentSign']").val();
	var pGroupTW = $("[name='percentGroupTW']").val();
	var pGroupUnTW = $("[name='percentGroupUnTW']").val();
	//var pUse = $("[name='percentUse']").val();
	// var EffectiveGoal1 = $("[name='EffectiveGoal1']").val();
	// var EffectiveGoal2 = $("[name='EffectiveGoal2']").val();
	// var EffectiveGoal3 = $("[name='EffectiveGoal3']").val();
	 var EffectiveBaseScore = $("[name='EffectiveBaseScore']").val();
	// var EffectivePlus = $("[name='EffectivePlus']").val();
	// var EffectivePlus2 = $("[name='EffectivePlus2']").val();

	$.ajax({
		url: '../includes/sales/editSalesPercent.php',
		type: 'POST',
		dataType: 'html',
		data: {
            'SalesId': pSalesId,
            'Year': pYear,
            'Month': pMonth,
            'Sign': pSign,
            'GroupTW':pGroupTW,
            'GroupUnTW':pGroupUnTW,
            //'Use':pUse,
            // 'EffectiveGoal1':EffectiveGoal1,
            // 'EffectiveGoal2':EffectiveGoal2,
            // 'EffectiveGoal3':EffectiveGoal3,
             'EffectiveBaseScore':EffectiveBaseScore,
            // 'EffectivePlus':EffectivePlus,
            // 'EffectivePlus2':EffectivePlus2
			},
	})
	.done(function(msg) {
		msg = msg.trim();
		if ( msg == 'OK') {
			alert('更改成功');
			location.href='salesReport.php';
		}
	});
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

	.statistics table{
		width: 100%
		padding-bottom:20px;
	}

	.statistics th{
		width: 8%;
		padding: 5px;
		border: 1px solid #000;
	}

	.statistics td {
		width: 8%;
		padding: 5px;
		border: 1px solid #000;
	}

	.statistics_s table{
		width: 100%
		padding-bottom:20px;
	}

	.statistics_s th{
		width: 5%;
		padding: 5px;
		border: 1px solid #000;
	}

	.statistics_s td {
		width: 5%;
		padding: 5px;
		border: 1px solid #000;
	}

	.tb {
		padding:5px;
		margin-bottom: 20px;
		background-color:#FFFFFF;
	}

	.tb th{
		padding: 5px;
		border: 1px solid #CCC;
		background-color: #CFDEFF;
	}

	.tb td{
		text-align: center;
		padding: 5px;
		border: 1px solid #CCC;
	}

	.tb_cal th{
		padding: 5px;
		border: 1px solid #CCC;
		background-color: #CFDEFF;
	}

	.tb_cal td{
		text-align: left;
		padding: 5px;
		border: 1px solid #CCC;
		background-color:#FFFFFF;
	}

	.tag {
		margin-bottom: 20px;
		padding:5px;
		background-color:#FFFFFF;
	}

	.tag th{
		padding: 5px;
		border: 1px solid #CCC;
		background-color: #CFDEFF;
	}

	.tag td{
		text-align: center;
		padding: 5px;
		border: 1px solid #CCC;
	}

	.show{
		background-color:  #FFFF00;
	}

	#subTabs-1 div {
		margin-bottom:10px;
	}

	#subTab-8 div {
		margin-bottom:10px;
	}

	.focus_page {
		vertical-align: top;
		display:inline-block;
	}

	.focus_page li {
		float:left;
		margin: 0 3px 0 3px;
	}

	.focus_page li a {
		color: #b48400;
		font-family: Verdana;
		font-size: 16px;
		font-weight: bold;
		line-height: 20px;
		background-color: #FFFFFF;
		text-align:center;
		display:inline-block;
		padding: 8px 12px;
		border: 1px solid #DDDDDD;
	}

	.focus_page li a:hover {
		color: #FFFFFF;
		font-size:16px;
		background-color: #FFFF96;
		border: 1px solid #FFFF96;
	}

	.focus_page li.focus_end a {
		color: #FFFFFF;
		font-family: Verdana;
		font-size: 16px;
		font-weight: bold;
		line-height: 20px;
		background-color: #FFFF96;
		text-align:center;
		display:inline-block;
		padding: 8px 12px;
		border: 1px solid #FFFF96;
	}

	.category{
		font-size: 16px;
		font-weight: bold;
		line-height: 20px;
	}
	#open_weighted tr:nth-child(even) {
		background: #f8ece9
	}
	#open_weighted tr:nth-child(odd) {
		background: #e4beb1
	}
</style>
</head>
<body id="dt_example">
    <form action="/calendar/calendar.php" target="_blank"></form>
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
        <table width="1000" border="0" cellpadding="4" cellspacing="0">
            <tr>
                <td bgcolor="#DBDBDB">
                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                        <tr>
                            <td height="17" bgcolor="#FFFFFF">
                                <div id="menu-lv2">
                                </div>
                                <br/> 
                                <h3></h3>
                                <div id="container">

                                <div style="padding-bottom:20px;">
                                <h1>績效一覽表</h1>
                                
                                    <form method="POST" id="salesForm" >
                                        <{if $smarty.session.member_id|in_array:[2, 3, 6, 48] || $smarty.session.member_pDep == 7}>
                                        <a href="/report/salesAchievement.php">業務績效目標與達成率</a>
                                        <{/if}>
                                        <{if $smarty.session.member_id|in_array:[1, 2, 3, 6, 48] || $smarty.session.member_pDep == 7}>
                                        <a href="/report/salesPerformance.php" target="salesPerformance">成長趨勢表</a>
                                            <{if $smarty.session.member_pDep == 7}>
                                            <br><br>
                                            <{/if}>
                                        <{/if}>

                                        <{if $smarty.session.member_pDep != 7}>
                                        <a href="../report2/salesSignReport.php" target="_blank">業務簽約數表</a>
                                        <{if $smarty.session.member_id|in_array:[1, 2, 3, 6, 48]}>
                                        <a href="../report2/salesReportPromo.php">課程推廣登記</a>
                                        <{/if}>

                                        <{if $smarty.session.member_id|in_array:[2, 3, 6]}>
                                        <a href="/report/salesTwhgNoCaseReport.php">台屋未進案季報表</a>
                                        <{/if}>
                                        <br><br>
                                        <span style="padding-right:20px;">
                                            業務： <{html_options name=sales options=$menu_sales selected=$sales}>
                                        </span>
                                        <{/if}>
                                        <span style="padding-right:20px;">
                                            年度：<select name="dateYear" style="width:50px;"><{$y}></select>
                                        </span>
                                        <span style="padding-right:40px;">
                                            月份：<select name="dateMonth" style="width:50px;"><{$m}></select>
                                        </span>
                                        <input type="button" name="su" value="查詢" >
                                        <input type="hidden" name="ex">
                                        <input type="hidden" name="traceXls">
                                        
                                            <input type="button" value="匯出Excel" name="excel">
                                        
                                    </form>
                                    <form method="POST" id="xls"> 
                                        
                                    </form>
                                
                                </div>	

                                <div id="subTabs">
                                    <ul>
                                        <li><a href="#subTabs-1">業績一覽表</a></li>
                                        
                                        <li><a href="#subTabs-2">通路新簽約數/達成率</a></li>
                                        <{if $smarty.session.member_id != 48}> 
                                            <li><a href="#subTabs-3">進案件數/成長率</a></li>
                                            <{if $sales|in_array: [38, 72] && $yr >= 110 }>

                                            <li><a href="#subTabs-11">進案件數/成長率(全部)</a></li>
                                            <{/if}>
                                            <li><a href="#subTabs-7">有效使用率</a></li>
                                            <{if $smarty.session.member_pDep != 7}>		
                                                <li><a href="#subTabs-6">未進案統計</a></li>
                                                <li><a href="#subTab-8">設定比率</a></li>
                                            <{/if}>
                                            <{if $salesGroupListShow == 1}>
                                                <li><a href="#subTabs-9">同區業務簽約名單</a></li>
                                                <li><a href="#subTabs-10">同區業務行程</a></li>
                                            <{/if}>
                                        <{/if}>
                                    </ul>
                                    
                                    <div id="subTabs-1">
                                        <h1>
                                            <table>
                                                <tr>
                                                    <td style="vertical-align: top;">
                                                        本季考核評分 <span style="font-weight:bold;color:<{$gradecolor}>;font-size:24pt;"><{$grade}></span> 分
                                                    </td>
                                                    <{if $gradeNotice != ''}>
                                                    <td style="padding-left: 50px;"><span style="font-weight:bold;color:<{$gradecolor}>;font-size:14pt;"><{$gradeNotice}></span></td>
                                                    <{/if}>
                                                </tr>
                                            </table>
                                        </h1>
                                        <div>
                                            達成率：
                                            <span style="font-weight:bold;color:#000088;"><{$showseason.target}></span>
                                            %
                                            、
                                            得分：
                                            <span style="font-weight:bold;color:#000088;"><{$seasontarget}></span>
                                            分
                                        </div>
                                        <{if (($sales == 57) && (($yr == 109 && $mn > 3) || $yr >= 110)) || ($sales == 68 && $yr >= 109 ) || ($sales == 97 && $yr >= 114 )}>
                                        <div>
                                            成長率：
                                            
                                            <span style="font-weight:bold;color:#000088;"><{$showseason.groupAll}></span>
                                            %
                                            、
                                            得分：
                                            <span style="font-weight:bold;color:#000088;"><{$seasongroupALL}></span>
                                            分
                                        </div>
                                        <{else}>
                                        <div>
                                            台屋成長率：
                                            
                                            <span style="font-weight:bold;color:#000088;"><{$showseason.groupTW}></span>
                                            %
                                            、
                                            得分：
                                            <span style="font-weight:bold;color:#000088;"><{$seasongroupTW}></span>
                                            分
                                        </div>
                                        <div>
                                            非台屋成長率：
                                            
                                            <span style="font-weight:bold;color:#000088;"><{$showseason.groupUnTW}></span>
                                            %
                                            、
                                            得分：
                                            <span style="font-weight:bold;color:#000088;"><{$seasongroupUnTW}></span>
                                            分
                                        </div>
                                        <{/if}>
                                        
                                        <div>
                                            <{if $yr > 109 && $eff1.score < 10}>
                                                <{assign var='eff_color' value='red'}>
                                            <{else}>
                                                <{assign var='eff_color' value='#000088'}> 
                                            <{/if}>
                                                有效使用率：
                                                <span style="font-weight:bold;color:<{$eff_color}>;"><{$eff1.effective}></span>
                                                %
                                                、
                                                得分：
                                                    <span style="font-weight:bold;color:<{$eff_color}>;"><{$eff1.score}></span>
                                                分
                                        </div>
                                        
                                        <{if ($yr >= 112)}>
                                        <div>
                                            <{assign var='weight_color_add' value='#000088'}> 
                                            <{assign var='weight_color_minus' value='red'}> 

                                            <div>
                                                通路加權：
                                                得分：
                                                <span style="font-weight:bold;color:<{$weight_color_add}>;"><{$sales_weight.add}></span>
                                                分
                                                、
                                                扣分：
                                                <span style="font-weight:bold;color:<{$weight_color_minus}>;"><{$sales_weight.minus}></span>
                                                分
                                                <{if ($sales != 'a' and $sales != NULL) }>
                                                    <a class='inline' href="#open_weighted">(明細)</a>
                                                <{/if}>
                                            </div>
                                        </div>
                                        <{/if}>

                                        <div>
                                            <{assign var='weight_color_promo' value='#000088'}> 

                                            <div>
                                                課程推廣加權：
                                                得分：
                                                <span style="font-weight:bold;color:<{$weight_color_promo}>;"><{$sales_weight.promo}></span>
                                                分
                                            </div>
                                        </div>

                                        <{if (isset($calendar_score))}>
                                        <div>
                                            <div>
                                                行程記錄加權：
                                                得分：
                                                <span style="font-weight:bold;color:#000088;"><{$calendar_score.score}></span>
                                                分
                                                <a class='inline2' href="#open_weighted_calendar">(說明)</a>
                                            </div>
                                        </div>
                                        <{/if}>

                                        <{if (isset($econtract))}>
                                        <div>
                                            <div>
                                                電子契約書加權：
                                                得分：
                                                <span style="font-weight:bold;color:#000088;"><{$econtract.score}></span>
                                                分
                                                <a class='inline2' href="#open_weighted_econtract">(明細)</a>
                                            </div>
                                        </div>
                                        <{/if}>

                                        <div style="clear:both;"></div>
                                    </div>
                                    
                                    <div id="subTabs-2">
                                            <div class="tb">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <th width="8%">序號</th>
                                                        <th width="16%">建檔日</th>
                                                        <th width="15%">地政士</th>
                                                        <th width="30%">事務所名稱</th>
                                                        <th width="12%">區域</th>
                                                        <th>備註</th>
                                                    </tr>
                                                    <{foreach from=$Scrivener key=key item=item}>
                                                    <tr>
                                                        <td><{$item.no}></td>
                                                        <td><{$item.sSignDate}></td>
                                                        <td><{$item.sName}></td>
                                                        <td><{$item.sOffice}></td>
                                                        <td><{$item.city}><{$item.area}></td>
                                                        <td><{$item.Line}></td>
                                                    </tr>
                                                    <{/foreach}>
                                                    <{if $ScrivenerCount == 0}>
                                                    <tr>
                                                        <td colspan="6">無資料</td>
                                                    </tr>
                                                    <{/if}>
                                                </table>
                                            </div>
                                            
                                            <div class="tb">
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                    <tr>
                                                        <th width="5%">序號</th>
                                                        <th width="16%">建檔日</th>
                                                        <th width="15%">品牌</th>
                                                        <th width="15%">加盟店</th>
                                                        <th width="30%">仲介店名稱</th>
                                                        <th width="12%">區域</th>
                                                        <th>備註</th>
                                                    </tr>
                                                    <{assign var='no' value='1'}> 
                                                    <{foreach from=$Branch key=key item=item}>
                                                    <tr>
                                                        <td><{$no++}></td>
                                                        <td><{$item.sSignDate}></td>
                                                        <td><{$item.brand}></td>
                                                        <td><{$item.bStore}></td>
                                                        <td><{$item.bName}></td>
                                                        <td><{$item.city}><{$item.area}></td>
                                                        <td><{$item.oldStore}></td>
                                                    </tr>
                                                    <{/foreach}>
                                                    <{if $BranchCount == 0}>
                                                    <tr>
                                                        <td colspan="7">無資料</td>
                                                    </tr>
                                                    <{/if}>
                                                </table>
                                            </div>
                                            <div class="tag">
                                                <h2><{$now_year}>年度各月份簽約數/達成率(<font color="red">本月份達成率：<{$target}>%</font>)</h2>
                                                <table cellspacing="0" cellpadding="0" class="statistics">
                                                    <thead>
                                                        <tr>
                                                            <th>月份</th>
                                                            <th>一</th>
                                                            <th>二</th>
                                                            <th>三</th>
                                                            <th>四</th>
                                                            <th>五</th>
                                                            <th>六</th>
                                                            <th>七</th>
                                                            <th>八</th>
                                                            <th>九</th>
                                                            <th>十</th>
                                                            <th>十一</th>
                                                            <th>十二</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>簽約數</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>"><{$item.targetcount}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <tr style="background-color:#F0FFF0;">
                                                            <td>達成率</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>">
                                                                <{$item.target}>%
                                                            </td>
                                                            <{/foreach}>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <div style="height:20px;"></div>
                                            </div>
                                            <div class="tag">
                                                <h2><{$now_year}>年度各季簽約數/達成率(<font color="red">本季達成率：<{$showseason.target}>%</font>)</h2>
                                                <table cellspacing="0" cellpadding="0"  class="statistics_s">
                                                    <thead>
                                                        <tr>
                                                            <th>季</th>
                                                            <th>一</th>
                                                            <th>二</th>
                                                            <th>三</th>
                                                            <th>四</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr style="background-color:#F0FFF0;">
                                                            <td>達成率</td>
                                                            <{foreach from=$season1 key=key item=item}>
                                                            <td class="<{$item.class}>">
                                                            <{$item.target}>%
                                                            </td>
                                                            <{/foreach}>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div style="height:20px;"></div>
                                            </div>		
                                        </div>
                                    <{if $smarty.session.member_id != 48}> 
                                        
                                        <div id="subTabs-3">
                                            <{if (($sales == 57) && (($yr == 109 && $mn > 3)
                                            || $yr >= 110))
                                            || ($sales == 68 && $yr >= 109 )
                                            || ($sales == 97 && $yr >= 114 )}>
                                                    <div class="tag">
                                                        <h2><{$now_year}>年度各月份進件量/成長率(<font color="red">本月份成長率：<{$group}>%</font>)</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics">
                                                            <thead>
                                                                <tr>
                                                                    <th>月份</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                    <th>五</th>
                                                                    <th>六</th>
                                                                    <th>七</th>
                                                                    <th>八</th>
                                                                    <th>九</th>
                                                                    <th>十</th>
                                                                    <th>十一</th>
                                                                    <th>十二</th>
                                                                </tr>
                                                            </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td style="font-size:10pt;">進件量</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.groupcount}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <tr style="background-color:#F0FFF0;">
                                                                    <td>成長率</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>" >
                                                                        <{if $now_month < $key && $now_check != 1}>
                                                                            0%
                                                                        <{else}>
                                                                            <{$item.groupAllshow}>%
                                                                        <{/if}>
                                                                    </td>
                                                                    <{/foreach}>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <div style="height:20px;"></div>
                                                    </div>
                                                    
                                                    <div class="tag">
                                                        <h2><{$now_year}>年度各季進件量/成長率(<font color="red">本季成長率：<{$showseason.groupAll}>%</font>)</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics_s">
                                                            <thead>
                                                                <tr>
                                                                    <th>季</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                    
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr style="background-color:#F0FFF0;">
                                                                    <td>成長率</td>
                                                                    <{foreach from=$season1 key=key item=item}>
                                                                    <td class="<{$item.class}>">
                                                                        <{$item.groupAllshow}>%
                                                                    </td>
                                                                    <{/foreach}>
                                                                </tr>	
                                                            </tbody>
                                                        </table>

                                                        <div style="height:20px;"></div>
                                                    </div>
                                            <{else}>
                                                    <div class="tag">
                                                        <h2><{$now_year}>年度各月份台屋進件量/成長率(<font color="red">本月份成長率：<{$groupTW}>%</font>)</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics">
                                                            <thead>
                                                                <tr>
                                                                    <th>月份</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                    <th>五</th>
                                                                    <th>六</th>
                                                                    <th>七</th>
                                                                    <th>八</th>
                                                                    <th>九</th>
                                                                    <th>十</th>
                                                                    <th>十一</th>
                                                                    <th>十二</th>
                                                                    
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="font-size:10pt;">進件量(台屋)</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.twcount}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <{if ($sales == 38 || $sales == 72) && ($yr == 109 && $mn >= 10)}>
                                                                <tr>
                                                                    <td style="font-size:10pt;">進件量(台中)</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.twcountTaichung}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:10pt;">進件量(南投)</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.twcountNantou}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:10pt;">進件量(彰化)</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.twcountChanghua}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <{/if}>
                                                                <tr style="background-color:#F0FFF0;">
                                                                    <td>成長率</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>" >
                                                                        <{if $now_month < $key && $now_check != 1}>
                                                                            0%
                                                                        <{else}>
                                                                            <{$item.groupTWshow}>%
                                                                        <{/if}>
                                                                    </td>
                                                                    <{/foreach}>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <div style="height:20px;"></div>
                                                    </div>
                                                    <div class="tag">
                                                        <h2><{$now_year}>年度各月份非台屋進件量/成長率(<font color="red">本月份成長率：<{$groupUnTW}>%</font>)</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics">
                                                            <thead>
                                                                <tr>
                                                                    <th>月份</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                    <th>五</th>
                                                                    <th>六</th>
                                                                    <th>七</th>
                                                                    <th>八</th>
                                                                    <th>九</th>
                                                                    <th>十</th>
                                                                    <th>十一</th>
                                                                    <th>十二</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td style="font-size:10pt;">進件量(非台屋)</td>
                                                                <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>"><{$item.othercount}></td>
                                                                <{/foreach}>
                                                            </tr>
                                                            <{if ($sales == 38 || $sales == 72) && ($yr == 109 && $mn >= 10)}>
                                                            <tr>
                                                                <td style="font-size:10pt;">進件量(台中)</td>
                                                                <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>"><{$item.othercountTaichung}></td>
                                                                <{/foreach}>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size:10pt;">進件量(南投)</td>
                                                                <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>"><{$item.othercountNantou}></td>
                                                                <{/foreach}>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size:10pt;">進件量(彰化)</td>
                                                                <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>"><{$item.othercountChanghua}></td>
                                                                <{/foreach}>
                                                            </tr>
                                                            <{/if}>
                                                            <tr style="background-color:#F0FFF0;">
                                                                <td>成長率</td>
                                                                <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>" >
                                                                    <{if $now_month < $key && $now_check != 1}>
                                                                        0%
                                                                    <{else}>
                                                                        <{$item.groupUnTWshow}>%
                                                                    <{/if}>
                                                                </td>
                                                                <{/foreach}>
                                                            </tr>	
                                                            </tbody>
                                                        </table>

                                                        <div style="height:20px;"></div>
                                                    </div>
                                                    <div class="tag">
                                                        <h2><{$now_year}>年度各季台屋進件量/成長率(<font color="red">本季成長率：<{$showseason.groupTW}>%</font>)</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics_s">
                                                            <thead>
                                                                <tr>
                                                                    <th>季</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr style="background-color:#F0FFF0;">
                                                                    <td>成長率</td>
                                                                    <{foreach from=$season1 key=key item=item}>
                                                                    <td class="<{$item.class}>">
                                                                        <{$item.groupTWshow}>%
                                                                    </td>
                                                                    <{/foreach}>
                                                                </tr>	
                                                            </tbody>
                                                        </table>
                                                        <div style="height:20px;"></div>
                                                    </div>
                                                    <div class="tag">
                                                        <h2><{$now_year}>年度各季非台屋進件量/成長率(<font color="red">本季成長率：<{$showseason.groupUnTW}>%</font>)</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics_s">
                                                            <thead>
                                                                <tr>
                                                                    <th>季</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr style="background-color:#F0FFF0;">
                                                                <td>成長率</td>
                                                                <{foreach from=$season1 key=key item=item}>
                                                                <td class="<{$item.class}>">
                                                                    <{$item.groupUnTWshow}>%
                                                                </td>
                                                                <{/foreach}>
                                                            </tr>	
                                                            </tbody>
                                                        </table>
                                                        <div style="height:20px;"></div>
                                                    </div>
                                            <{/if}>
                                            <{if ($smarty.session.member_pDep == 1 || $smarty.session.member_pDep == 4)}>
                                                <div class="tag">
                                                        <h2><{$now_year}>年度台屋案件各月份統計</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics">
                                                            <thead>
                                                                <tr>
                                                                    <th>月份</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                    <th>五</th>
                                                                    <th>六</th>
                                                                    <th>七</th>
                                                                    <th>八</th>
                                                                    <th>九</th>
                                                                    <th>十</th>
                                                                    <th>十一</th>
                                                                    <th>十二</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="font-size:10pt;">保證費</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.certifiedMoneyTw|number_format}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:10pt;">回饋費</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.caseFeedBackMoneyTw|number_format}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:10pt;">淨收</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.caseIncomeTw|number_format}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <div style="height:20px;"></div>
                                                </div>
                                                <div class="tag">
                                                        <h2><{$now_year}>年度非台屋案件各月份統計</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics">
                                                            <thead>
                                                                <tr>
                                                                    <th>月份</th>
                                                                    <th>一</th>
                                                                    <th>二</th>
                                                                    <th>三</th>
                                                                    <th>四</th>
                                                                    <th>五</th>
                                                                    <th>六</th>
                                                                    <th>七</th>
                                                                    <th>八</th>
                                                                    <th>九</th>
                                                                    <th>十</th>
                                                                    <th>十一</th>
                                                                    <th>十二</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="font-size:10pt;">保證費</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.certifiedMoneyOther|number_format}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:10pt;">回饋費</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.caseFeedBackMoneyOther|number_format}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size:10pt;">淨收</td>
                                                                    <{foreach from=$summary1 key=key item=item}>
                                                                    <td class="<{$item.class}>"><{$item.caseIncomeOther|number_format}></td>
                                                                    <{/foreach}>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <div style="height:20px;"></div>
                                                </div>
                                                <div class="tag">
                                                    <h2><{$now_year}>年度統計</h2>
                                                        <table cellspacing="0" cellpadding="0"  class="statistics">
                                                            <thead>
                                                                <tr>
                                                                    <th>&nbsp;</th>
                                                                    <th>進件量</th>
                                                                    <th>保證費</th>
                                                                    <th>回饋費</th>
                                                                    <th>淨收</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>台屋</td>
                                                                    <td><{$totalData.twcount|number_format}></td>
                                                                    <td><{$totalData.certifiedMoneyTw|number_format}></td>
                                                                    <td><{$totalData.caseFeedBackMoneyTw|number_format}></td>
                                                                    <td><{$totalData.caseIncomeTw|number_format}></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>非台屋</td>
                                                                    <td><{$totalData.othercount|number_format}></td>
                                                                    <td><{$totalData.certifiedMoneyOther|number_format}></td>
                                                                    <td><{$totalData.caseFeedBackMoneyOther|number_format}></td>
                                                                    <td><{$totalData.caseIncomeOther|number_format}></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>總計</td>
                                                                    <td><{($totalData.twcount+$totalData.othercount)|number_format}></td>
                                                                    <td><{($totalData.certifiedMoneyTw+$totalData.certifiedMoneyOther)|number_format}></td>
                                                                    <td><{($totalData.caseFeedBackMoneyTw+$totalData.caseFeedBackMoneyOther)|number_format}></td>
                                                                    <td><{($totalData.caseIncomeTw+$totalData.caseIncomeOther)|number_format}></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <div style="height:20px;"></div>
                                                </div>
                                            <{/if}>
                                        </div>

                                        <{if $smarty.session.member_pDep != 7}>
                                        <div id="subTabs-6">
                                            <div id="xlsdownload" style="cursor:pointer;" onclick="teaceXls()"><img src="/images/Excel_2013.png" title="excel 報表下載">
                                            </div>
                                        </div>
                                        <{/if}>
                                        
                                        <div id="subTabs-7">
                                            <div><font color="red">(A)所有通路數在本季有進案且不重複通路數[<{$eff1.range_start2}>～<{$eff1.range_end2}>]：<{$eff1.no}></font></div>
                                            <div><font color="red">(B)所有通路數[<{$eff1.range_start}>～<{$eff1.range_end}>]：<{$eff1.total}></font></div>
                                            <div>
                                                <h1>有效率 <span style="font-weight:bold;color:#FF0000;font-size:24pt;"><{$eff1.effective}></span> % (有效率 = A / B%)</h1>
                                                <div class="tag"></div>
                                                    <div class="tag">
                                                        <h2>有進案</h2>
                                                        <div class="tb">
                                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                                <tr>
                                                                    <th width="8%">序號</th>
                                                                    <th width="16%">建檔日</th>
                                                                    <th width="15%">地政士</th>
                                                                    <th width="30%">事務所名稱</th>
                                                                    <th width="12%">區域</th>
                                                                </tr>
                                                                <{assign var='cc' value=1}> 
                                                                <{foreach from=$eff1.data['scrcase'] key=key item=item}>
                                                                <tr>
                                                                    <td><{$cc++}></td>
                                                                    <td><{$item.sCreat_time}></td>
                                                                    <td><{$item.sName}></td>
                                                                    <td><{$item.sOffice}></td>
                                                                    <td><{$item.city}><{$item.area}></td>
                                                                </tr>
                                                                <{/foreach}>
                                                                <{if $cc == 1}>
                                                                <tr><td colspan="5">無資料</td></tr>
                                                                <{/if}>
                                                            </table>
                                                        </div>
                                                        <div class="tb">
                                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                                <tr>
                                                                    <th width="8%">序號</th>
                                                                    <th width="16%">建檔日</th>
                                                                    <th width="15%">品牌</th>
                                                                    <th width="15%">加盟店</th>
                                                                    <th width="30%">仲介店名稱</th>
                                                                    <th width="12%">區域</th>
                                                                </tr>
                                                                <{assign var='cc' value=1}>
                                                                <{foreach from=$eff1.data['branchcase'] key=key item=item}>
                                                                <tr>
                                                                    <td><{$cc++}></td>
                                                                    <td><{$item.bCreat_time}></td>
                                                                    <td><{$item.brand}></td>
                                                                    <td><{$item.bStore}></td>
                                                                    <td><{$item.bName}></td>
                                                                    <td><{$item.city}><{$item.area}></td>
                                                                </tr>
                                                                <{/foreach}>
                                                                <{if $cc == 1}>
                                                                    <tr><td colspan="6">無資料</td></tr>
                                                                <{/if}>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="tag">
                                                        <h2>未進案</h2>
                                                        <div class="tb">
                                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                                <tr>
                                                                    <th width="8%">序號</th>
                                                                    <th width="16%">建檔日</th>
                                                                    <th width="15%">地政士</th>
                                                                    <th width="30%">事務所名稱</th>
                                                                    <th width="12%">區域</th>
                                                                </tr>
                                                                <{assign var='cc' value=1}>
                                                                <{foreach from=$eff1.data['scrnocase'] key=key item=item}>
                                                                <tr>
                                                                    <td><{$cc++}></td>
                                                                    <td><{$item.sCreat_time}></td>
                                                                    <td><{$item.sName}></td>
                                                                    <td><{$item.sOffice}></td>
                                                                    <td><{$item.city}><{$item.area}></td>
                                                                </tr>
                                                                <{/foreach}>
                                                                <{if $cc == 1}>
                                                                <tr><td colspan="5">無資料</td></tr>
                                                                <{/if}>
                                                            </table>
                                                        </div>
                                                        <div class="tb">
                                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                                <tr>
                                                                    <th width="8%">序號</th>
                                                                    <th width="16%">建檔日</th>
                                                                    <th width="15%">品牌</th>
                                                                    <th width="15%">加盟店</th>
                                                                    <th width="30%">仲介店名稱</th>
                                                                    <th width="12%">區域</th>
                                                                </tr>
                                                                <{assign var='cc' value=1}>
                                                                <{foreach from=$eff1.data['branchnocase'] key=key item=item}>
                                                                <tr>
                                                                    <td><{$cc++}></td>
                                                                    <td><{$item.bCreat_time}></td>
                                                                    <td><{$item.brand}></td>
                                                                    <td><{$item.bStore}></td>
                                                                    <td><{$item.bName}></td>
                                                                    <td><{$item.city}><{$item.area}></td>
                                                                </tr>
                                                                <{/foreach}>
                                                                <{if $cc == 1}>
                                                                    <tr><td colspan="6">無資料</td></tr>
                                                                <{/if}>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                        </div>
                                        <{if $smarty.session.member_pDep != 7}>	
                                        <div id="subTab-8">
                                            <div><h2><font color="red">2025年後適用</font></h2></div>
                                            <div>通路新簽約數，占比<input type="text" name="percentSign" id="" size="5" value="<{$percent.pSign}>"></div>
                                            <div>進案件成長率，台屋占比<input type="text" name="percentGroupTW" size="5" value="<{$percent.pGroupTW}>"></div>
                                            <div>進案件成長率，非台屋占比<input type="text" name="percentGroupUnTW" size="5" value="<{$percent.pGroupUnTW}>"></div>
                                            <!--<div>有效使用率，占比<input type="text" name="percentUse" size="5" value="<{$percent.pPercentUse}>"></div>-->
                                            <div>有效率指標積分<input type="text" name="EffectiveBaseScore" size="5" value="<{$percent.pEffectiveBaseScore}>"></div>
                                            <!--<div>
                                                <fieldset style="border:1px solid #000; padding:5px;width:80%;">
                                                    <legend align="left">
                                                        <div>有效使用率，占比<input type="text" name="percentUse" size="5" value="<{$percent.pPercentUse}>"></div>
                                                    </legend>
                                                    <div>所得比例數字以<input type="text" name="EffectiveGoal1" size="5" value="<{$percent.pEffectiveGoal1}>">為基準(區域涵蓋率為 40% 以下)</div>
                                                    <div>所得比例數字以<input type="text" name="EffectiveGoal2" size="5" value="<{$percent.pEffectiveGoal2}>">為基準(區域涵蓋率為 40% ~ 50%)</div>
                                                    <div>所得比例數字以<input type="text" name="EffectiveGoal3" size="5" value="<{$percent.pEffectiveGoal3}>">為基準(區域涵蓋率為 50% 以上)</div>
                                                    <div>指標積分<input type="text" name="EffectiveBaseScore" size="5" value="<{$percent.pEffectiveBaseScore}>"></div>
                                                    <div>基準數字每增減<input type="text" name="EffectivePlus" id="" size="5" value="<{$percent.pEffectivePlus}>">
                                                        ，所得積分即增減<input type="text" name="EffectivePlus2" id="" size="5" value="<{$percent.pEffectivePlus2}>">
                                                    </div>
                                                </fieldset>
                                            </div>-->
                                            <div>
                                                <input type="button" value="更改" onclick="chagePercent()">
                                            </div>
                                        </div>
                                        <{/if}>
                                        <{if $salesGroupListShow == 1}>
                                            <div id="subTabs-9">
                                                    <{foreach from=$salesGroupList key=key item=item}>
                                                    <{assign var='no' value='1'}>
                                                    <h1><{$item.name}>(<font color="red">簽約數:<{$item.targetcount}></font>)</h1>
                                                    <div></div>
                                                    <div class="tb">
                                                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                                <tr>
                                                                    <th width="8%">序號</th>
                                                                    <th width="16%">建檔日</th>
                                                                    <th width="15%">地政士</th>
                                                                    <th width="30%">事務所名稱</th>
                                                                    <th width="12%">區域</th>

                                                                    <th>備註</th>
                                                                </tr>
                                                                <{foreach from=$item['scrivener'] key=key2 item=data}>
                                                                <tr>
                                                                    <td><{$no++}></td>
                                                                    <td><{$data.sSignDate}></td>
                                                                    <td><{$data.sName}></td>
                                                                    <td><{$data.sOffice}></td>
                                                                    <td><{$data.city}><{$data.area}></td>
                                                                    <td><{$data.Line}></td>
                                                                </tr>
                                                                <{/foreach}>
                                                            </table>
                                                    </div>
                                                    <div class="tb">
                                                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                                            <tr>
                                                                <th width="5%">序號</th>
                                                                <th width="16%">建檔日</th>
                                                                <th width="15%">品牌</th>
                                                                <th width="15%">加盟店</th>
                                                                <th width="30%">仲介店名稱</th>
                                                                <th width="12%">區域</th>
                                                                <th>備註</th>

                                                            </tr>
                                                            <{assign var='no' value='1'}>
                                                            <{foreach from=$item['branch'] key=key2 item=data}>
                                                            <tr>
                                                                <td><{$no++}></td>
                                                                <td><{$data.sSignDate}></td>
                                                                <td><{$data.brand}></td>
                                                                <td><{$data.bStore}></td>
                                                                <td><{$data.bName}></td>
                                                                <td><{$data.city}><{$data.area}></td>
                                                                <td><{$data.oldStore}></td>
                                                            </tr>
                                                            <{/foreach}>
                                                        </table>
                                                    </div>

                                                    <{/foreach}>

                                            </div>
                                            <div id="subTabs-10">
                                                <{foreach from=$salesGroupList key=key item=item}>
                                                <h1><{$item.name}></h1>
                                                <table cellpadding="0" cellspacing="0" border="0" width="100%" class="tb_cal">
                                                    <tr>
                                                        <th width="15%">期間</th>
                                                        <th width="8%">地區</th>
                                                        <th width="10%">分類</th>
                                                        <th width="10%">目的</th>
                                                        <th width="22%">對象</th>
                                                        <th width="30%">內容</th>
                                                    </tr>
                                                    <{foreach from=$item['calendar'] key=key2 item=data}>
                                                    <tr>
                                                        <td><{$data.from}><br><{$data.to}></td>
                                                        <td><{$data.city}></td>
                                                        <td><{$data.class}></td>
                                                        <td><{$data.subject}></td>
                                                        <td><{$data.target}></td>
                                                        <td><{$data.desc}></td>
                                                    </tr>
                                                    <{/foreach}>
                                                </table>
                                                <{/foreach}>
                                            </div>
                                        <{/if}>
                                        <{if $sales|in_array: [38, 72] && $yr >= 110}>
                                        <div id="subTabs-11">
                                            <div class="tag">
                                                <h2><{$now_year}>年度各月份台屋進件量/成長率(<font color="red">本月份成長率：<{$groupTW38}>%</font>)</h2>
                                                    <table cellspacing="0" cellpadding="0"  class="statistics">
                                                        <thead>
                                                            <tr>
                                                                <th>月份</th>
                                                                <th>一</th>
                                                                <th>二</th>
                                                                <th>三</th>
                                                                <th>四</th>
                                                                <th>五</th>
                                                                <th>六</th>
                                                                <th>七</th>
                                                                <th>八</th>
                                                                <th>九</th>
                                                                <th>十</th>
                                                                <th>十一</th>
                                                                <th>十二</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(台屋)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>"><{$item.twcount38}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(台中)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>"><{$item.twcountTaichung}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <{if $sales|in_array: [72] && $yr >= 110}>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(南投)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>"><{$item.twcountNantou}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <{/if}>
                                                        <{if $sales|in_array: [38] && $yr >= 110}>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(彰化)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>"><{$item.twcountChanghua}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <{/if}>
                                                        <tr style="background-color:#F0FFF0;">
                                                            <td>成長率</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>" >
                                                                <{if $now_month < $key && $now_check != 1}>
                                                                0%
                                                                <{else}>
                                                                <{$item.groupTWshow38}>%
                                                                <{/if}>
                                                            </td>
                                                            <{/foreach}>
                                                        </tr>	
                                                        </tbody>
                                                    </table>

                                                    <div style="height:20px;"></div>
                                                </div>
                                                <div class="tag">
                                                    <h2><{$now_year}>年度各月份非台屋進件量/成長率(<font color="red">本月份成長率：<{$groupUnTW38}>%</font>)</h2>
                                                    <table cellspacing="0" cellpadding="0"  class="statistics">
                                                        <thead>
                                                            <tr>
                                                                <th>月份</th>
                                                                <th>一</th>
                                                                <th>二</th>
                                                                <th>三</th>
                                                                <th>四</th>
                                                                <th>五</th>
                                                                <th>六</th>
                                                                <th>七</th>
                                                                <th>八</th>
                                                                <th>九</th>
                                                                <th>十</th>
                                                                <th>十一</th>
                                                                <th>十二</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(非台屋)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            <td class="<{$item.class}>"><{$item.othercount38}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(台中)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>"><{$item.othercountTaichung}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(南投)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>"><{$item.othercountNantou}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size:10pt;">進件量(彰化)</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                                <td class="<{$item.class}>"><{$item.othercountChanghua}></td>
                                                            <{/foreach}>
                                                        </tr>
                                                        <tr style="background-color:#F0FFF0;">
                                                            <td>成長率</td>
                                                            <{foreach from=$summary1 key=key item=item}>
                                                            
                                                                <td class="<{$item.class}>" >
                                                                    <{if $now_month < $key && $now_check != 1}>
                                                                        0%
                                                                    <{else}>
                                                                        <{$item.groupUnTWshow38}>%
                                                                    <{/if}>
                                                                </td>
                                                            <{/foreach}>
                                                        </tr>	
                                                        </tbody>
                                                    </table>

                                                    <div style="height:20px;"></div>
                                                </div>
                                                <div class="tag">
                                                    <h2><{$now_year}>年度各季台屋進件量/成長率(<font color="red">本季成長率：<{$showseason.groupTW38}>%</font>)</h2>
                                                    <table cellspacing="0" cellpadding="0"  class="statistics_s">
                                                        <thead>
                                                            <tr>
                                                                <th>季</th>
                                                                <th>一</th>
                                                                <th>二</th>
                                                                <th>三</th>
                                                                <th>四</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        
                                                        <tr style="background-color:#F0FFF0;">
                                                            <td>成長率</td>
                                                            <{foreach from=$season1 key=key item=item}>
                                                            
                                                                <td class="<{$item.class}>">
                                                                    <{$item.groupTWshow38}>%
                                                                </td>
                                                            
                                                            <{/foreach}>
                                                        </tr>	
                                                        </tbody>
                                                    </table>
                                                    <div style="height:20px;"></div>
                                                </div>
                                                <div class="tag">
                                                    <h2><{$now_year}>年度各季非台屋進件量/成長率(<font color="red">本季成長率：<{$showseason.groupUnTW38}>%</font>)</h2>
                                                    <table cellspacing="0" cellpadding="0"  class="statistics_s">
                                                        <thead>
                                                            <tr>
                                                                <th>季</th>
                                                                <th>一</th>
                                                                <th>二</th>
                                                                <th>三</th>
                                                                <th>四</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        
                                                        <tr style="background-color:#F0FFF0;">
                                                            <td>成長率</td>
                                                            <{foreach from=$season1 key=key item=item}>
                                                            
                                                                <td class="<{$item.class}>">
                                                                    <{$item.groupUnTWshow38}>%
                                                                </td>
                                                            
                                                            <{/foreach}>
                                                        </tr>	
                                                        </tbody>
                                                    </table>
                                                    <div style="height:20px;"></div>
                                                </div>
                                        </div>
                                        <{/if}>
                                    <{/if}>
                                <div id="footer" style="height:50px;">
                                    <p>2012 第一建築經理股份有限公司 版權所有</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- This contains the hidden content for inline calls -->
    <div style='display:none'>
        <div id='open_weighted' style='padding:10px; background:#fff;margin: 0px auto;'>
            <table bgcolor="#f8ece9" width="640px">
                <tr >
                    <td width="120" align="center" style="padding-bottom: 20px; padding-top:20px;text-align:center;">月份</td>
                    <td width="519" align="center" style="font-weight:bold;text-align:center;">加分</td>
                </tr>
                <{foreach from=$sales_weight.bId key=month item=types}>
                    <tr>
                        <td align="center" style="font-weight:bold;padding: 5px;"><{$month}></td>
                        <td valign="top" style="padding: 5px;">
                        <{foreach from=$types key=title item=storeName}>
                            
                            <{if $title == '加分'}>
                                <{$storeName}>
                            <{/if}>
                        <{/foreach}>
                        </td>
                    </tr>
                <{/foreach}>
            </table>
            <div style="height: 20px;"></div>
            <table  bgcolor="#f8ece9" width="640px">
                <tr >
                    <td width="519" align="center" style="font-weight:bold;padding: 5px;">扣分(未進案店家)</td>
                </tr>
                <tr>
                    <td valign="top" style="padding: 5px;">
                    <{foreach from=$sales_weight.bId key=month item=types}>
                        <{foreach from=$types key=title item=storeName}>
                            <{if $title == '扣分'}>
                                <{$storeName}>
                            <{/if}>
                        <{/foreach}>
                        <br><br>
                    <{/foreach}>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!--行程分數-->
    <div style='display:none'>
        <div id='open_weighted_calendar' style='padding:10px; background:#fff;margin: 0px auto;'>
            <table  bgcolor="#f8ece9" width="640px">
                <tr bgcolor="#E4BEB1">
                    <td width="519" align="center" style="font-weight:bold;padding: 5px;"><h3>行程紀錄填寫規範(每季結算)：</h3></td>
                </tr>
                <tr>
                    <td valign="top" style="padding: 5px;">
                        <div style="padding: 8px;">1. 基本規定：</div>
                        <div style="padding: 5px;padding-left: 20px;">- 填寫時間：每個工作日的上午 9:00 至下午 5:00</div>
                        <div style="padding: 5px;padding-left: 20px;">- 午休時段（12:00-13:00）無需填寫</div>
                        <div style="padding: 5px;padding-left: 20px;">- 例假日和休假日無需填寫，系統會自動與假勤系統核對</div>
                        <div>&nbsp;</div>
                        <div style="padding: 8px;">2. 計分方式：</div>
                        <div style="padding: 5px;padding-left: 20px;">- 當季工作日確實填寫：獲得 10 分</div>
                        <div style="padding: 5px;padding-left: 20px;">- 當季有任何工作日未填寫或填寫不完整：0 分</div>
                        <div style="padding: 5px;padding-left: 20px;">- 補填過去日期視為填寫不完整</div>
                        <div>&nbsp;</div>
                        <div style="padding: 8px;">3. 注意事項：</div>
                        <div style="padding: 5px;padding-left: 20px;">- 行程正常上班時間：上午 9:00 至下午 6:00</div>
                    </td>
                </tr>
            </table>
            <{if count($calendar_score.error) > 0}>
            <div style="height: 20px;"></div>
            <table  bgcolor="#f8ece9" width="640px">
                <tr bgcolor="#E4BEB1">
                    <td width="519" align="center" style="font-weight:bold;padding: 5px;">扣分說明</td>
                </tr>
                <{foreach from=$calendar_score.error key=k item=v}>
                <tr>
                    <td valign="top" style="padding: 10px;">
                        <{$k}> 未確實填寫行程
                    </td>
                </tr>
                <{/foreach}>
            </table>
            <{/if}>
        </div>
    </div>
    <!-- 電子契約書加權明細 -->
    <div style='display:none'>
        <div id='open_weighted_econtract' style='padding:10px; background:#fff;margin: 0px auto;'>
            <table bgcolor="#f8ece9" width="640px">
                <tr bgcolor="#E4BEB1">
                    <td align="center" style="padding:10px;font-weight:bold;text-align:center;">第一筆入款時間</td>
                    <td align="center" style="padding:10px;font-weight:bold;text-align:center;">地政士</td>
                    <td align="center" style="padding:10px;font-weight:bold;text-align:center;">履保帳號</td>
                </tr>
                <{foreach from=$econtract.detail key=k item=v}>
                    <tr>
                        <td align="center" style="padding:10px;text-align:center;"><{$v.eTradeDate}></td>
                        <td align="center" style="padding:10px;text-align:center;"><{$v.sName}></td>
                        <td align="center" style="padding:10px;text-align:center;"><{$v.eDepAccount}></td>
                    </tr>
                <{/foreach}>
            </table>
        </div>
    </div>
</body>
</html>