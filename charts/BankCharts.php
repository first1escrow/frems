<?php
include_once '../session_check.php' ;
include_once '../openadodb.php' ;

//取得金流系統類別與百分比
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;

$i = 0 ;
$totalNo = 0 ;
while (!$rs->EOF) {
	$list[$i] = $rs->fields ;
	
	//歷史保證號碼統計
	$sql = 'SELECT COUNT(cBank) as total FROM tContractCase WHERE cBank="'.$rs->fields['cBankCode'].'" ;' ;
	$rsBank = $conn->Execute($sql) ;
	$list[$i]['total'] = $rsBank->fields['total'] ;
	$totalNo += $list[$i]['total'] + 1 - 1 ;
	##
	
	//本月保證號碼統計
	$sql = '
		SELECT 
			COUNT(cBank) as total 
		FROM 
			tContractCase 
		WHERE 
			cBank="'.$rs->fields['cBankCode'].'" 
			AND cApplyDate>="'.date("Y-m").'-01 00:00:00" 
			AND cApplyDate<="'.date("Y-m").'-31 23:59:59"
	;' ;
	$rsThis = $conn->Execute($sql) ;
	$list[$i]['ThisMonth'] = $rsThis->fields['total'] ;
	$thisMonth += $list[$i]['ThisMonth'] + 1 - 1 ;
	##
	
	//三個月內保證號碼統計
	$fDate = date("Y-m",mktime(0,0,0,(date("m")-3),1,date("Y"))) ;
	$sql = '
		SELECT 
			COUNT(cBank) as total 
		FROM 
			tContractCase 
		WHERE 
			cBank="'.$rs->fields['cBankCode'].'" 
			AND cApplyDate>="'.$fDate.'-01 00:00:00" 
			AND cApplyDate<="'.date("Y-m").'-31 23:59:59"
	;' ;
	$rsThis = $conn->Execute($sql) ;
	$list[$i]['ThreeMonth'] = $rsThis->fields['total'] ;
	$threeMonth += $list[$i]['ThreeMonth'] + 1 - 1 ;
	##
	
	//六個月內保證號碼統計
	$fDate = date("Y-m",mktime(0,0,0,(date("m")-6),1,date("Y"))) ;
	$sql = '
		SELECT 
			COUNT(cBank) as total 
		FROM 
			tContractCase 
		WHERE 
			cBank="'.$rs->fields['cBankCode'].'" 
			AND cApplyDate>="'.$fDate.'-01 00:00:00" 
			AND cApplyDate<="'.date("Y-m").'-31 23:59:59"
	;' ;
	$rsThis = $conn->Execute($sql) ;
	$list[$i]['SixMonth'] = $rsThis->fields['total'] ;
	$sixMonth += $list[$i]['SixMonth'] + 1 - 1 ;
	##
	
	$i ++ ;
	$rs->MoveNext() ;
}

for ($i = 0 ; $i < count($list) ; $i ++) {
	$list[$i]['percent'] = round(($list[$i]['total'] / $totalNo) * 100, 2) ;
	$list[$i]['percentThis'] = round(($list[$i]['ThisMonth'] / $thisMonth) * 100, 2) ;
	$list[$i]['percentThree'] = round(($list[$i]['ThreeMonth'] / $threeMonth) * 100, 2) ;
	$list[$i]['percentSix'] = round(($list[$i]['SixMonth'] / $sixMonth) * 100, 2) ;
}
##

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<title>銀行系統</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../js/highcharts.js"></script>
<script src="../js/modules/exporting.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	window.resizeTo(900,900) ;
	
	$('#showChart').highcharts({
        chart: {
			plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '第一建經銀行系統使用比例'
        },
        subtitle: {
            text: '(全部年度)'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '百分比',
            data: [
<?php
$str = '' ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	if ($list[$i]['cBankName'] == '永豐') {
		$list[$i]['cBankFullName'] .= $list[$i]['cBranchFullName'] ;
	}
	$str .= '["'.$list[$i]['cBankFullName'].'", '.$list[$i]['percent']."],\n" ;
}

$str = preg_replace("/,$/","",$str) ;
echo $str ;
?>
            ]
        }]
    });
	
	$('#showChart1').highcharts({
        chart: {
			plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '第一建經銀行系統使用比例'
        },
        subtitle: {
            text: '(本月份)'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '百分比',
            data: [
<?php
$str = '' ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	$str .= '["'.$list[$i]['cBankFullName'].'", '.$list[$i]['percentThis']."],\n" ;
}

$str = preg_replace("/,$/","",$str) ;
echo $str ;
?>
            ]
        }]
    });
	
	$('#showChart2').highcharts({
        chart: {
			plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '第一建經銀行系統使用比例'
        },
        subtitle: {
            text: '(三個月內)'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '百分比',
            data: [
<?php
$str = '' ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	$str .= '["'.$list[$i]['cBankFullName'].'", '.$list[$i]['percentThree']."],\n" ;
}

$str = preg_replace("/,$/","",$str) ;
echo $str ;
?>
            ]
        }]
    });
	
	$('#showChart3').highcharts({
        chart: {
			plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '第一建經銀行系統使用比例'
        },
        subtitle: {
            text: '(六個月內)'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '百分比',
            data: [
<?php
$str = '' ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	$str .= '["'.$list[$i]['cBankFullName'].'", '.$list[$i]['percentSix']."],\n" ;
}

$str = preg_replace("/,$/","",$str) ;
echo $str ;
?>
            ]
        }]
    });
});
    

</script>
</head>
<body style="width: 850px;">
<center>
<div id="showChart" style="float:left;min-width: 400px;border:1px solid #CCC;"></div>
<div id="showChart1" style="margin-left:10px;float:left;min-width: 400px;border:1px solid #CCC;"></div>
<div style="clear:both;margin-bottom:10px;"></div>
<div id="showChart3" style="float:left;min-width: 400px;border:1px solid #CCC;"></div>
<div id="showChart2" style="margin-left:10px;float:left;min-width: 400px;border:1px solid #CCC;"></div>
</center>
</body>
</html>
