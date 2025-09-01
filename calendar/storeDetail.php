<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

$bId = $_REQUEST['s'] ;

$list = array() ;

if ($bId) {
	//
	$sql = '
		SELECT
			a.*,
			b.zCity as city,
			b.zArea as area,
			(SELECT bName FROM tBrand WHERE bId=a.bBrand) as brand,
			(SELECT pName FROM tPeopleInfo WHERE a.bCashierOrderPpl=pId) as staff
		FROM
			tBranch AS a
		LEFT JOIN
			tZipArea AS b ON a.bZip=b.zZip
		WHERE
			bId = "'.$bId.'"
	' ;
	$rs = $conn->Execute($sql) ;
	$list = $rs->fields ;
	$list['bZip'] = preg_replace("/[a-z]/","",$list['bZip']) ;
	if ($list['bRecall']) $list['bRecall'] .= '％' ;
	##
	
	//
	$sql = '
		SELECT
			a.bBank4_name as bankBranch,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.bBank3 AND bBank4="") as bankMain
		FROM
			tBank AS a
		WHERE
			bBank3="'.$list['bAccountNum1'].'"
			AND bBank4="'.$list['bAccountNum2'].'"
	;' ;
	$rs = $conn->Execute($sql) ;
	$bank = $rs->fields ;
	##
}
?>
<!DOCTYPE html>
<html>
<head>
<title>店家摘要</title>
<meta charset='utf-8' />
<link rel="stylesheet" href="/css/colorbox.css" />

<link rel='stylesheet' href='lib/cupertino/jquery-ui.min.css' />
<link href='fullcalendar.css' rel='stylesheet' />
<link href='fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='lib/moment.min.js'></script>
<script src='lib/jquery.min.js'></script>
<script src='fullcalendar.min.js'></script>

<link href="lib/jquery-ui.css" rel="stylesheet">
<script src="lib/jquery-ui.js"></script>

<script src='lang-all.js'></script>
<script src='gcal.js'></script>
<link rel="stylesheet" href="colorbox.css" />
<script src="jquery.colorbox-min.js"></script>

<script type='text/Javascript'>
$(document).ready(function() {
	$( "input[type=button], input[type=submit]" ).button();
});

function maps() {
	var url = 'maps.php?zips=<?=$list['bZip']?>&addr=<?=$list['bAddress']?>' ;
	
	$.colorbox({
		iframe:true,
		width:"500px",
		height:"620px",
		href:url,
		onClosed: function() {
			
		}
	}) ;
}
</script>
<style>
	.custom-combobox {
		position: relative;
		display: inline-block;
	}
	.custom-combobox-toggle {
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		padding: 0;
	}
	.custom-combobox-input {
		margin: 0;
		padding: 5px 10px;
	}
	body {
		margin: 40px 10px;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}
	.margin-div {
		margin-top:10px;
		margin-bottom: 10px;
	}
	.lineB {
		float: left;
		width: 160px;
	}
	.lineE {
		float: left;
	}
	td {
		border-bottom-width: 1px;
		border-bottom-style: solid;
		border-bottom-color: #CCC;
		margin: 0px;
		padding: 5px;
		min-width:120px;
	}
	.lineT {
		background-color: #AED0EA;
		text-align: right;
		min-width: 80px;
		border-color: #FFF;
		color: #FFFFFF;
	}
	.lineT:hover {
		color: #0000CC;
	}
</style>
</head>
<body>
<center>
<h2 style="margin-top: -25px;color:#550088">店家基本資料摘要</h2>
<div class="margin-div" style="clear:both;">
	<table cellspacing="0" width="400px;">
		<tr>
			<td class="lineT" nowrap>仲介品牌</td>
			<td><?=$list['brand']?>&nbsp;</td>
			<td class="lineT" nowrap>仲介店名</td>
			<td><?=$list['bStore']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>仲介公司</td>
			<td colspan="3"><?=$list['bName']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>仲介類型</td>
			<td><?php
			if ($list['bCategory'] == '1') echo '加盟' ;
			else echo '直營' ;
			?></td>
			<td class="lineT" nowrap>統一編號</td>
			<td><?=$list['bSerialnum']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>店東</td>
			<td><?=$list['bManager']?>&nbsp;</td>
			<td class="lineT" nowrap>行動電話</td>
			<td><?=$list['bMobileNum']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>聯絡電話</td>
			<td><?php
			if ($list['bTelArea'] && $list['bTelMain']) echo $list['bTelArea'].'-'.$list['bTelMain'] ;
			else {
				if ($list['bTelArea']) echo $list['bTelArea'] ;
				if ($list['bTelMain']) echo $list['bTelMain'] ;
			}
			?>&nbsp;</td>
			<td class="lineT" nowrap>傳真號碼</td>
			<td><?php
			if ($list['bFaxArea'] && $list['bFaxMain']) echo $list['bFaxArea'].'-'.$list['bFaxMain'] ;
			else {
				if ($list['bFaxArea']) echo $list['bFaxMain'] ;
			}
			?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>電子郵件</td>
			<td colspan="3"><?=$list['bEmail']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>地址</td>
			<td colspan="3"><?php
				$png = '' ;
				if ($list['bAddress']) $png = '<a href="#" onclick="maps()"><img src="images/2015033109362073_easyicon_net_21.8790697674.png" title="查看地圖"></a>' ;
				$addr = $list['bAddress'] ;
				$c = $list['city'] ;
				$a = $list['area'] ;
				$addr = preg_replace("/$c/isu","",$addr) ;
				$addr = preg_replace("/$a/isu","",$addr) ;
				echo $c.$a.$addr.'　'.$png ;
			?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>收票承辦人</td>
			<td><?=$list['staff']?>&nbsp;</td>
			<td class="lineT" nowrap>本票同意書</td>
			<td><?php
			if ($list['bCashierOrderHas'] == '1') echo '有' ;
			else echo '無' ;
			?></td>
		</tr>
		<tr>
			<td class="lineT" nowrap>本票票號</td>
			<td><?=$list['bCashierOrderNumber']?>&nbsp;</td>
			<td class="lineT" nowrap>本票金額</td>
			<td><?=number_format($list['bCashierOrderMoney'])?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>回饋比率</td>
			<td><?=$list['bRecall']?>&nbsp;</td>
			<td class="lineT" nowrap>保證費率</td>
			<td><?=$list['bCertified']?></td>
		</tr>
		<tr>
			<td class="lineT" nowrap>總行</td>
			<td><?=$bank['bankMain']?>&nbsp;</td>
			<td class="lineT" nowrap>分行</td>
			<td><?=$bank['bankBranch']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>帳號</td>
			<td><?=$list['bAccount3']?>&nbsp;</td>
			<td class="lineT" nowrap>戶名</td>
			<td><?=$list['bAccount4']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap valign="top">備註說明</td>
			<td colspan="3" style="min-height:150px;" valign="top">
				<?=$list['bCashierOrderMemo']?>
			</td>
		</tr>
	</table>
	<div class="margin-div" style="height:5px;"></div>
	<input type="button" value="Close" onclick="window.close()">
</div>
</center>
</body>
</html>
