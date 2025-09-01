<?php
include_once '../session_check.php' ;
include_once '../openadodb.php' ;
include_once '../class/myClass.php' ;

$bId = trim(addslashes($_POST['bId'])) ;

$fromYear = (int)trim(addslashes($_POST['fromYear'])) ;
if (!$fromYear) {
	$fromYear = (int)date("Y",mktime(0,0,0,(date("m")-5),1,date("Y"))) ;
}

$fromMonth = (int)trim(addslashes($_POST['fromMonth'])) ;
if (!$fromMonth) {
	$fromMonth = (int)date("m",mktime(0,0,0,(date("m")-5),1,date("Y"))) ;
}

$toYear = (int)trim(addslashes($_POST['toYear'])) ;
if (!$toYear) {
	$toYear = (int)date("Y") ;
}

$toMonth = (int)trim(addslashes($_POST['toMonth'])) ;
if (!$toMonth) {
	$toMonth = (int)date("m") ;
}

if ($fromYear && $fromMonth && $toYear && $toMonth) {
	$totalMonths = ($toYear - $fromYear) * 12 + $toMonth - $fromMonth + 1 ;		//計算期間總月份
	
	$date_array = array() ;
	for ($i = 0 ; $i < $totalMonths ; $i ++) {
		$mm = date("Y.m",mktime(0,0,0,($fromMonth + $i),1,$fromYear)) ;
		$date_array[$i] = $mm ;
	}
}

if ((!$bId) || ($bId == 505) || ($bId == 0)) {
	$bId = 33 ;		//預設仲介為中壢直營店
}

//取得仲介店基本資料
$sql = '
	SELECT 
		bra.bStore as bStore,
		bra.bId as bId,
		brd.bName as bName
	FROM
		 tBranch AS bra
	JOIN
		tBrand AS brd ON bra.bBrand=brd.bId
	WHERE
		bra.bId NOT IN ("0","505")
	ORDER BY 
		brd.bName,bra.bId
	ASC;
' ;
$rs = $conn->Execute($sql) ;
$i = 0 ;
while (!$rs->EOF) {
	$realty[$i++] = $rs->fields ;
	
	$rs->MoveNext() ;
}
##

//取得時間範圍內之資料
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;

$i = 0 ;
$totalNo = 0 ;
while (!$rs->EOF) {
	$list[$i] = $rs->fields ;
	
	for ($j = 0 ; $j < count($date_array) ; $j ++) {
		$tmp = array() ;
		$tmp = explode('.',$date_array[$j]) ;
		
		$sql = '
			SELECT 
				COUNT(cas.cCertifiedId) as total
			FROM 
				tContractCase AS cas
			JOIN
				tContractRealestate AS cre ON cas.cCertifiedId=cre.cCertifyId
			WHERE 
				cas.cBank="'.$rs->fields['cBankCode'].'" 
				AND cas.cApplyDate >= "'.$tmp[0].'-'.$tmp[1].'-01 00:00:00"
				AND cas.cApplyDate <= "'.$tmp[0].'-'.$tmp[1].'-31 23:59:59"
				AND (cre.cBranchNum="'.$bId.'" OR cre.cBranchNum1="'.$bId.'" OR cre.cBranchNum2="'.$bId.'")
		' ;
		
		$rs1 = $conn->Execute($sql) ;
		$list[$i]['total'] .= $rs1->fields['total'].',' ;
		unset($tmp) ;
	}
	$tmp = preg_replace("/,$/","",$list[$i]['total']) ;
	$list[$i]['total'] = $tmp ;
	
	unset($tmp) ;
	
	$i ++ ;
	$rs->MoveNext() ;
}
##

$showOptions = new MyClass() ;

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>仲介店合約書使用狀態</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../js/highcharts.js"></script>
<script src="../js/modules/exporting.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    window.resizeTo(800,600) ;
	
	$('#showChart').highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: '仲介店案件合約書使用狀態'
		},
		xAxis: {
			title: {
				text: '日期'
			},
			categories: ['<?=implode("','",$date_array)?>']
		},
		yAxis: {
			min: 0,
			title: {
				text: '案件數量'
			},
			stackLabels: {
				enabled: true,
				style: {
					fontWeight: 'bold',
					color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
				}
			}
		},
		legend: {
			align: 'right',
			x: -70,
			verticalAlign: 'top',
			y: 20,
			floating: true,
			backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
			borderColor: '#CCC',
			borderWidth: 1,
			shadow: false
		},
		tooltip: {
				formatter: function() {
					return '<b>'+ this.x +'</b><br/>'+
						this.series.name +': '+ this.y +'<br/>'+
						'總件數: '+ this.point.stackTotal + '件' ;
				}
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				dataLabels: {
					enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
				}
			}
		},
		series: [
<?php
$str = '' ;
for ($i = 0 ; $i < count($list) ; $i ++) {
	if ($list[$i]['cBankName'] == '永豐') {
		$list[$i]['cBankName'] .= $list[$i]['cBranchName'] ;
	}
	
	$str .= '{ name: "'.$list[$i]['cBankName'].'", data: ['.$list[$i]['total'].']},'."\n" ;
}
$str = preg_replace("/,$/","",$str) ;
echo $str ;
?>
		]
	});
});

/* 更換仲介店 */ 
function chgRealty() {
	$('[name="myform"]').submit() ;
}
////
</script>
<style>

</style>
</head>
<body>
<form name="myform" method="POST">
<center>
<div id="scrvener" style="padding-bottom:10px;">
	仲介店名：
	<select name="bId" onchange="chgRealty()">
	<?php
	for ($i = 0 ; $i < count($realty) ; $i ++) {
		echo '<option value="'.$realty[$i]['bId'].'"' ;
		if ($realty[$i]['bId'] == $bId) {
			echo ' selected="selected"' ;
		}
		echo '>'.$realty[$i]['bName'].$realty[$i]['bStore']."</option>\n" ;
	}
	?>
	</select>　
</div>
<div>
	日期範圍：
	<select name="fromYear">
<?php
echo $showOptions->FromToYear($fromYear,2012) ;
?>
	</select>
	年度
	<select name="fromMonth">
<?php
echo $showOptions->FromToMonth($fromMonth) ;
?>
	</select>
	月份
	~
	<select name="toYear">
<?php
echo $showOptions->FromToYear($toYear,2012) ;
?>
	</select>
	年度
	<select name="toMonth">
<?php
echo $showOptions->FromToMonth($toMonth) ;
?>
	</select>
	月份
	<input type="submit" style="margin-left:20px;" value="查詢">
</div>
<div style="height:20px;border-top-style:dashed;border-top-color:#CCC;border-top-width:1px;">　</div>
<div id="showChart" style="min-width: 310px; margin: 0 auto"></div>
</center>
</form>
</body>
</html>
