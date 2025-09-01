<?php
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

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
				COUNT(cCertifiedId) as total 
			FROM 
				tContractCase 
			WHERE 
				cBank="'.$rs->fields['cBankCode'].'" 
				AND cApplyDate >= "'.$tmp[0].'-'.$tmp[1].'-01 00:00:00"
				AND cApplyDate <= "'.$tmp[0].'-'.$tmp[1].'-31 23:59:59"
				AND cCaseStatus = "3"
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
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>結案件數統計</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../js/highcharts.js"></script>
<script src="../js/modules/exporting.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    window.resizeTo(600,550) ;
	
	$('#showChart').highcharts({
        chart: {
			type: 'column'
        },
        title: {
            text: '第一建經結案件數統計'
        },
		subtitle: {
			text: '(<?=$fromYear.'/'.$fromMonth.'~'.$toYear.'/'.$toMonth?>)'
		},
		xAxis: {
			categories: [
<?php
$str = '' ;
for ($i = 0 ; $i < count($date_array) ; $i ++) {
	$str .= "\t\t\t".$date_array[$i].",\n" ;
}
echo preg_replace("/,$/","",$str) ;
?>
			]
		},
		yAxis: {
			min: 0,
			title: {
				text: '案件數'
			}
		},
        tooltip: {
			headerFormat: '<table>',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.0f} 件</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
        },
        plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0
			}
        },
        series: [{
<?php
$arr = array() ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	if ($list[$i]['cBankName'] == '永豐') {
		$list[$i]['cBankName'] .= $list[$i]['cBranchName'] ;
	}
	
	$arr[$i] = 'name: "'.$list[$i]['cBankName'].'",'."\n" ;
	$arr[$i] .= 'data: ['.$list[$i]['total'].']'."\n" ;
}

echo implode('}, {',$arr) ;
?>
        }]
    });
});
    

</script>
</head>
<body>
<form method="POST" name="myform">
<div>
	日期範圍：
	<select name="fromYear">
<?php
$max = (int)date("Y") ;
for ($i = 2012 ; $i <= $max ; $i ++) {
	echo '<option value="'.$i.'"' ;
	if ($fromYear == $i) {
		echo ' selected="selected"' ;
	}
	echo '>'.$i."</option>\n" ;
}
?>
	</select>
	年度
	<select name="fromMonth">
<?php
for ($i = 0 ; $i < 12 ; $i ++) {
	echo '<option value="'.($i+1).'"' ;
	if ($fromMonth == ($i+1)) {
		echo ' selected="selected"' ;
	}
	echo '>'.str_pad(($i+1),2,'0',STR_PAD_LEFT)."</option>\n" ;
}
?>
	</select>
	月份
	~
	<select name="toYear">
<?php
$max = (int)date("Y") ;
for ($i = 2012 ; $i <= $max ; $i ++) {
	echo '<option value="'.$i.'"' ;
	if ($toYear == $i) {
		echo ' selected="selected"' ;
	}
	echo '>'.$i."</option>\n" ;
}
?>
	</select>
	年度
	<select name="toMonth">
<?php
for ($i = 0 ; $i < 12 ; $i ++) {
	echo '<option value="'.($i+1).'"' ;
	if ($toMonth == ($i+1)) {
		echo ' selected="selected"' ;
	}
	echo '>'.str_pad(($i+1),2,'0',STR_PAD_LEFT)."</option>\n" ;
}
?>
	</select>
	月份
	<input type="submit" style="margin-left:20px;" value="查詢">
</div>
<div id="showChart" style="min-width: 310px; margin: 0 auto"></div>
</form>
</body>
</html>
