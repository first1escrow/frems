<?php
include_once '../session_check.php' ;
include_once '../openadodb.php' ;
include_once '../class/myClass.php' ;

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
$lineName = array('進案案件','簽約案件','結案案件') ;
$totalNo = 0 ;
for ($i = 0 ; $i < count($lineName) ; $i ++) {
	$list[$i]['sName'] = $lineName[$i] ;
	
	for ($j = 0 ; $j < count($date_array) ; $j ++) {
		$tmp = array() ;
		$tmp = explode('.',$date_array[$j]) ;
		
		switch($list[$i]['sName']) {
			case '進案案件' :
					$sql = '
						SELECT 
							COUNT(cCertifiedId) as total 
						FROM 
							tContractCase 
						WHERE 
							cApplyDate >= "'.$tmp[0].'-'.$tmp[1].'-01 00:00:00"
							AND cApplyDate <= "'.$tmp[0].'-'.$tmp[1].'-31 23:59:59"
					' ;
					
					break ;
			case '簽約案件' :
					$sql = '
						SELECT 
							COUNT(cCertifiedId) as total 
						FROM 
							tContractCase 
						WHERE 
							cSignDate >= "'.$tmp[0].'-'.$tmp[1].'-01 00:00:00"
							AND cSignDate <= "'.$tmp[0].'-'.$tmp[1].'-31 23:59:59"
					' ;
					
					break ;
			case '結案案件' :
					$sql = '
						SELECT 
							COUNT(cCertifiedId) as total 
						FROM 
							tContractCase 
						WHERE 
							cCaseStatus = "3"
							AND cApplyDate >= "'.$tmp[0].'-'.$tmp[1].'-01 00:00:00"
							AND cApplyDate <= "'.$tmp[0].'-'.$tmp[1].'-31 23:59:59"
					' ;
					
					break ;
			default :
					
					break ;
		}
				
		$rs1 = $conn->Execute($sql) ;
		$list[$i]['total'] .= $rs1->fields['total'].',' ;
		
		unset($tmp) ;
	}
	$list[$i]['total'] = preg_replace("/,$/","",$list[$i]['total']) ;
}

$showOptions = new MyClass() ;
##
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>每月案件數統計</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../js/highcharts.js"></script>
<script src="../js/modules/exporting.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    window.resizeTo(600,550) ;
	
	$('#showChart').highcharts({
        title: {
            text: '第一建經每月案件數統計',
			x: -20
        },
		subtitle: {
			text: '(<?=$fromYear.'/'.$fromMonth.'~'.$toYear.'/'.$toMonth?>)',
			x: -20
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
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
        tooltip: {
			valueSuffix: '件'
        },
        plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0
			}
        },
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'middle',
			borderWidth: 0
		},
        series: [{
<?php
$arr = array() ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	$arr[$i] = 'name: "'.$list[$i]['sName'].'",'."\n" ;
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
//echo $showOptions->FromToYear($fromYear,2012) ;
echo MyClass::FromToYear($fromYear,2012) ;
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
<div id="showChart" style="min-width: 310px; margin: 0 auto"></div>
</form>
</body>
</html>
