<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

$sId = $_REQUEST['s'] ;

$list = array() ;

if ($sId) {
	$sql = '
		SELECT
			a.*,
			b.zCity as city,
			b.zArea as area,
			c.zCity as cpcity,
			c.zArea as cparea,
			(SELECT pName FROM tPeopleInfo WHERE pId=a.sUndertaker1) as staff,
			(SELECT cName FROM tCategoryGuild WHERE cId=a.sGuild) as guild
		FROM
			tScrivener AS a
		LEFT JOIN
			tZipArea AS b ON a.sZip1=b.zZip
		LEFT JOIN
			tZipArea AS c ON a.sCpZip1=c.zZip
		WHERE
			sId = "'.$sId.'"
	' ;
	$rs = $conn->Execute($sql) ;
	$list = $rs->fields ;
	if ($list['sRecall'] == '0') $list['sRecall'] = '' ;
	if ($list['sRecall']) $list['sRecall'] .= '％' ;
	if ($list['sSpRecall'] == '0') $list['sSpRecall'] = '' ;
	if ($list['sSpRecall']) $list['sSpRecall'] .= '％' ;
	##
	
	//
	$sql = '
		SELECT
			a.bBank4_name as bankBranch,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.bBank3 AND bBank4="") as bankMain
		FROM
			tBank AS a
		WHERE
			bBank3="'.$list['sAccountNum1'].'"
			AND bBank4="'.$list['sAccountNum2'].'"
	;' ;
	$rs = $conn->Execute($sql) ;
	$bank = $rs->fields ;
	##
}
?>
<!DOCTYPE html>
<html>
<head>
<title>地政士摘要</title>
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

function maps(zip, adr) {
	var url = 'maps.php?zips=' + zip + '&addr=' + adr ;
	
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
<h2 style="margin-top: -25px;color:#550088">地政士基本資料摘要</h2>
<div class="margin-div" style="clear:both;">
	<table cellspacing="0" width="400px;">
		<tr>
			<td class="lineT" nowrap>地政士</td>
			<td><?=$list['sName']?>&nbsp;</td>
			<td class="lineT" nowrap>事務所名稱</td>
			<td><?=$list['sOffice']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>地政士執照</td>
			<td><?=$list['sLicense']?>&nbsp;</td>
			<td class="lineT" nowrap>所屬公會</td>
			<td><?=$list['guild']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>統一編號</td>
			<td><?=$list['bSerialnum']?>&nbsp;</td>
			<td class="lineT" nowrap>身分證號碼</td>
			<td><?=strtoupper($list['sIdentifyId'])?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>行動電話</td>
			<td><?=$list['sMobileNum']?>&nbsp;</td>
			<td class="lineT" nowrap>聯絡電話</td>
			<td><?php
			if ($list['sTelArea'] && $list['sTelMain']) echo $list['sTelArea'].'-'.$list['sTelMain'] ;
			else {
				if ($list['sTelArea']) echo $list['sTelArea'] ;
				if ($list['sTelMain']) echo $list['sTelMain'] ;
			}
			?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>聯絡電話(二)</td>
			<td><?php
			if ($list['sTelArea2'] && $list['sTelMain2']) echo $list['sTelArea2'].'-'.$list['sTelMain2'] ;
			else {
				if ($list['sTelArea2']) echo $list['sTelMain2'] ;
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
			<td class="lineT" nowrap>身分類型</td>
			<td><?php
			if ($list['sCategory'] == '1') echo '加盟' ;
			else echo '直營' ;
			?></td>
			<td class="lineT" nowrap>承辦人</td>
			<td><?=$list['staff']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>回饋比率</td>
			<td><?=$list['sRecall']?>&nbsp;</td>
			<td class="lineT" nowrap>特殊回饋比率</td>
			<td><?=$list['sSpRecall']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>聯絡地址</td>
			<td colspan="3"><?php
				$png = '' ;
				if ($list['sAddress']) $png = '<a href="#" onclick="maps(\''.$list['sZip1'].'\',\''.$list['sAddress'].'\')"><img src="images/2015033109362073_easyicon_net_21.8790697674.png" title="查看地圖"></a>' ;
				$addr = $list['sAddress'] ;
				$c = $list['city'] ;
				$a = $list['area'] ;
				$addr = preg_replace("/$c/isu","",$addr) ;
				$addr = preg_replace("/$a/isu","",$addr) ;
				echo $c.$a.$addr.'　'.$png ;
			?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>公司地址</td>
			<td colspan="3"><?php
				$png = '' ;
				if ($list['sCpAddress']) $png = '<a href="#" onclick="maps(\''.$list['sCpZip1'].'\',\''.$list['sCpAddress'].'\')"><img src="images/2015033109362073_easyicon_net_21.8790697674.png" title="查看地圖"></a>' ;
				$addr = $list['sCpAddress'] ;
				$c = $list['cpcity'] ;
				$a = $list['cparea'] ;
				$addr = preg_replace("/$c/isu","",$addr) ;
				$addr = preg_replace("/$a/isu","",$addr) ;
				echo $c.$a.$addr.'　'.$png ;
			?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>電子郵件</td>
			<td colspan="3"><?=$list['sEmail']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>總行</td>
			<td><?=$bank['bankMain']?>&nbsp;</td>
			<td class="lineT" nowrap>分行</td>
			<td><?=$bank['bankBranch']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap>帳號</td>
			<td><?=$list['sAccount3']?>&nbsp;</td>
			<td class="lineT" nowrap>戶名</td>
			<td><?=$list['sAccount4']?>&nbsp;</td>
		</tr>
		<tr>
			<td class="lineT" nowrap valign="top">備註說明</td>
			<td colspan="3" style="min-height:150px;" valign="top">
				<?=nl2br($list['sRemark4'])?>&nbsp;
			</td>
		</tr>
	</table>
	<div class="margin-div" style="height:5px;"></div>
	<input type="button" value="Close" onclick="window.close()">
</div>
</center>
</body>
</html>
