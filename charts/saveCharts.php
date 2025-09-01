<?php
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
	
	$i ++ ;
	$rs->MoveNext() ;
}

for ($i = 0 ; $i < count($list) ; $i ++) {
	$list[$i]['percent'] = round(($list[$i]['total'] / $totalNo) * 100, 2) ;
	$list[$i]['percentThis'] = round(($list[$i]['ThisMonth'] / $thisMonth) * 100, 2) ;
}
##

$contents = "
{
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
" ;

$str = '' ;

for ($i = 0 ; $i < count($list) ; $i ++) {
	$str .= '["'.$list[$i]['cBankFullName'].'", '.$list[$i]['percentThis']."],\n" ;
}

$str = preg_replace("/,$/","",$str) ;
$contents .= $str ;

$contents .= "
            ]
        }]
};
" ;

//Server 端產生圖片檔案
$url = 'http://export.highcharts.com/';
//$data = array('filename' => 'chart' , 'type' => 'image/jpeg' , 'svg' => '<svg xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" xmlns="http://www.w3.org/2000/svg" width="600" height="400"><desc>Created with Highcharts 3.0.4</desc><defs><linearGradient x1="0" y1="0" x2="1" y2="1" id="highcharts-13"><stop offset="0" stop-color="rgb(255, 255, 255)" stop-opacity="1"></stop><stop offset="1" stop-color="rgb(240, 240, 255)" stop-opacity="1"></stop></linearGradient><clipPath id="highcharts-14"><rect fill="none" x="1" y="1" width="550" height="304"></rect></clipPath></defs><rect rx="0" ry="0" fill="url(#highcharts-13)" x="1" y="1" width="598" height="398" stroke="#4572A7" stroke-width="2"></rect><rect rx="0" ry="0" fill="none" x="38" y="40" width="552" height="306" fill-opacity="0.9"  stroke="black" stroke-opacity="0.049999999999999996" stroke-width="5" transform="translate(1, 1)"></rect><rect rx="0" ry="0" fill="none" x="38" y="40" width="552" height="306" fill-opacity="0.9"  stroke="black" stroke-opacity="0.09999999999999999" stroke-width="3" transform="translate(1, 1)"></rect><rect rx="0" ry="0" fill="none" x="38" y="40" width="552" height="306" fill-opacity="0.9"  stroke="black" stroke-opacity="0.15" stroke-width="1" transform="translate(1, 1)"></rect><rect rx="0" ry="0" fill="rgb(255,255,255)" x="38" y="40" width="552" height="306" fill-opacity="0.9"></rect><g class="highcharts-grid" ></g><g class="highcharts-grid" ></g><rect rx="0" ry="0" fill="none" x="38.5" y="40.5" width="551" height="305" stroke="#C0C0C0" stroke-width="1" ></rect><g class="highcharts-axis" ><path fill="none" d="M 38 345.5 L 590 345.5" stroke="#000" stroke-width="1"  visibility="visible"></path></g><g class="highcharts-axis" ><text x="28" y="193" style="font-family:trebuchet ms, verdana, sans-serif;font-size:12px;color:#333;font-weight:bold;fill:#333;"  text-anchor="middle" transform="translate(0,0) rotate(270 28 193)" visibility="visible"><tspan x="28">Temperatura °C</tspan></text><path fill="none" d="M 38.5 40 L 38.5 346" stroke="#000" stroke-width="1"  visibility="visible"></path></g><g class="highcharts-series-group" ><g class="highcharts-series" visibility="visible"  transform="translate(38,40) scale(1 1)" clip-path="url(#highcharts-14)"></g><g class="highcharts-markers" visibility="visible"  transform="translate(38,40) scale(1 1)" clip-path="none"><path fill="none" d="M 0 0" class="highcharts-tracker" stroke-linejoin="round" visibility="visible" stroke-opacity="0.0001" stroke="rgb(192,192,192)" stroke-width="22"  style=""></path></g><g class="highcharts-series" visibility="visible"  transform="translate(38,40) scale(1 1)" clip-path="url(#highcharts-14)"></g><g class="highcharts-markers" visibility="visible"  transform="translate(38,40) scale(1 1)" clip-path="none"><path fill="none" d="M 0 0" class="highcharts-tracker" stroke-linejoin="round" visibility="visible" stroke-opacity="0.0001" stroke="rgb(192,192,192)" stroke-width="22"  style=""></path></g><g class="highcharts-series" visibility="visible"  transform="translate(38,40) scale(1 1)" clip-path="url(#highcharts-14)"></g><g class="highcharts-markers" visibility="visible"  transform="translate(38,40) scale(1 1)" clip-path="none"><path fill="none" d="M 0 0" class="highcharts-tracker" stroke-linejoin="round" visibility="visible" stroke-opacity="0.0001" stroke="rgb(192,192,192)" stroke-width="22"  style=""></path></g></g><text x="300" y="25" style="font-family:\'lucida grande\', \'lucida sans unicode\', verdana, arial, helvetica, sans-serif;font-size:16px;color:#000;font:bold 16px \'trebuchet ms\', verdana, sans-serif;fill:#000;width:536px;" text-anchor="middle" class="highcharts-title" ><tspan x="300">Grafica en tiempo real del equipo "Equipo 1" de la última hora</tspan></text><g class="highcharts-legend"  transform="translate(182,356)"><rect rx="5" ry="5" fill="none" x="0.5" y="0.5" width="235" height="28" stroke="#909090" stroke-width="1" visibility="visible"></rect><g ><g><g class="highcharts-legend-item"  transform="translate(8,3)"><path fill="none" d="M 0 11 L 16 11" stroke="#058DC7" stroke-width="2"></path><path fill="#058DC7" d="M 8 7 C 13.328 7 13.328 15 8 15 C 2.6719999999999997 15 2.6719999999999997 7 8 7 Z"></path><text x="21" y="15" style="font-family:\'lucida grande\', \'lucida sans unicode\', verdana, arial, helvetica, sans-serif;font-size:12px;cursor:pointer;color:black;font:9pt trebuchet ms, verdana, sans-serif;fill:black;" text-anchor="start" ><tspan x="21">Sensor 1</tspan></text></g><g class="highcharts-legend-item"  transform="translate(84,3)"><path fill="none" d="M 0 11 L 16 11" stroke="#50B432" stroke-width="2"></path><path fill="#50B432" d="M 8 7 L 12 11 8 15 4 11 Z"></path><text x="21" y="15" style="font-family:\'lucida grande\', \'lucida sans unicode\', verdana, arial, helvetica, sans-serif;font-size:12px;cursor:pointer;color:black;font:9pt trebuchet ms, verdana, sans-serif;fill:black;" text-anchor="start" ><tspan x="21">Sensor 2</tspan></text></g><g class="highcharts-legend-item"  transform="translate(160,3)"><path fill="none" d="M 0 11 L 16 11" stroke="#ED561B" stroke-width="2"></path><path fill="#ED561B" d="M 4 7 L 12 7 12 15 4 15 Z"></path><text x="21" y="15" style="font-family:\'lucida grande\', \'lucida sans unicode\', verdana, arial, helvetica, sans-serif;font-size:12px;cursor:pointer;color:black;font:9pt trebuchet ms, verdana, sans-serif;fill:black;" text-anchor="start" ><tspan x="21">Sensor 3</tspan></text></g></g></g></g><g class="highcharts-axis-labels" ></g><g class="highcharts-axis-labels" ></g><g class="highcharts-tooltip"  style="cursor:default;padding:0;white-space:nowrap;" visibility="hidden" transform="translate(0,0)"><rect rx="3" ry="3" fill="none" x="0.5" y="0.5" width="16" height="16" fill-opacity="0.85"  stroke="black" stroke-opacity="0.049999999999999996" stroke-width="5" transform="translate(1, 1)"></rect><rect rx="3" ry="3" fill="none" x="0.5" y="0.5" width="16" height="16" fill-opacity="0.85"  stroke="black" stroke-opacity="0.09999999999999999" stroke-width="3" transform="translate(1, 1)"></rect><rect rx="3" ry="3" fill="none" x="0.5" y="0.5" width="16" height="16" fill-opacity="0.85"  stroke="black" stroke-opacity="0.15" stroke-width="1" transform="translate(1, 1)"></rect><rect rx="3" ry="3" fill="rgb(255,255,255)" x="0.5" y="0.5" width="16" height="16" fill-opacity="0.85"></rect><text x="8" y="21" style="font-family:\'lucida grande\', \'lucida sans unicode\', verdana, arial, helvetica, sans-serif;font-size:12px;color:#333333;fill:#333333;" ></text></g><text x="590" y="395" style="font-family:\'lucida grande\', \'lucida sans unicode\', verdana, arial, helvetica, sans-serif;font-size:9px;cursor:pointer;color:#909090;fill:#909090;" text-anchor="end" ><tspan x="590">Highcharts.com</tspan></text></svg>');
//$data = array('filename' => 'chart' , 'type' => 'image/jpeg' , 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="551" height="400" version="1.1"><desc>Created with Highcharts 3.0.4</desc><defs><clipPath id="highcharts-1"><rect fill="none" x="0" y="0" width="476" height="265" /></clipPath></defs><rect fill="#ffffff" x="0" y="0" width="551" height="400" rx="5" ry="5" /><g title="Chart context menu" class="highcharts-button" style="cursor: default;" stroke-linecap="round" transform="translate(517 10)"><title>Chart context menu</title><rect fill="white" stroke="none" stroke-width="1" x="0.5" y="0.5" width="24" height="22" rx="2" ry="2" /><path fill="#e0e0e0" stroke="#666" stroke-width="3" d="M 6 6.5 L 20 6.5 M 6 11.5 L 20 11.5 M 6 16.5 L 20 16.5" zIndex="1" /><text style="color: black; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; fill: black;" x="0" y="13" zIndex="1" /></g><g class="highcharts-grid" zIndex="1" /><g class="highcharts-grid" zIndex="1"><path opacity="1" fill="none" stroke="#c0c0c0" stroke-width="1" d="M 65 259.5 L 541 259.5" zIndex="1" /><path opacity="1" fill="none" stroke="#c0c0c0" stroke-width="1" d="M 65 192.5 L 541 192.5" zIndex="1" /><path opacity="1" fill="none" stroke="#c0c0c0" stroke-width="1" d="M 65 126.5 L 541 126.5" zIndex="1" /><path opacity="1" fill="none" stroke="#c0c0c0" stroke-width="1" d="M 65 59.5 L 541 59.5" zIndex="1" /><path opacity="1" fill="none" stroke="#c0c0c0" stroke-width="1" d="M 65 324.5 L 541 324.5" zIndex="1" /></g><g class="highcharts-axis" zIndex="2"><path opacity="1" fill="none" stroke="#c0d0e0" stroke-width="1" d="M 223.5 325 L 223.5 330" /><path opacity="1" fill="none" stroke="#c0d0e0" stroke-width="1" d="M 302.5 325 L 302.5 330" /><path opacity="1" fill="none" stroke="#c0d0e0" stroke-width="1" d="M 381.5 325 L 381.5 330" /><path opacity="1" fill="none" stroke="#c0d0e0" stroke-width="1" d="M 461.5 325 L 461.5 330" /><path opacity="1" fill="none" stroke="#c0d0e0" stroke-width="1" d="M 540.5 325 L 540.5 330" /><path opacity="1" fill="none" stroke="#c0d0e0" stroke-width="1" d="M 143.5 325 L 143.5 330" /><path fill="none" stroke="#c0d0e0" stroke-width="1" d="M 65.5 325 L 65.5 330" /><path visibility="visible" fill="none" stroke="#c0d0e0" stroke-width="1" d="M 65 324.5 L 541 324.5" zIndex="7" /></g><g class="highcharts-axis" zIndex="2"><text visibility="visible" style="color: rgb(77, 117, 158); font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; fill: #4d759e;" text-anchor="middle" transform="translate(0) rotate(270 26.12 192)" x="26.12" y="192" zIndex="7"><tspan x="26.12">案件數</tspan></text></g><g class="highcharts-series-group" zIndex="3"><g class="highcharts-series highcharts-tracker" visibility="visible" clip-path="url(&quot;#highcharts-1&quot;)" transform="translate(65 59) scale(1)" zIndex="0.1"><rect fill="#2f7ed8" x="19" y="156" width="10" height="110" rx="0" ry="0" /><rect fill="#2f7ed8" x="98" y="144" width="10" height="122" rx="0" ry="0" /><rect fill="#2f7ed8" x="177" y="159" width="10" height="107" rx="0" ry="0" /><rect fill="#2f7ed8" x="257" y="160" width="10" height="106" rx="0" ry="0" /><rect fill="#2f7ed8" x="336" y="188" width="10" height="78" rx="0" ry="0" /><rect fill="#2f7ed8" x="415" y="261" width="10" height="5" rx="0" ry="0" /></g><g class="highcharts-markers" visibility="visible" transform="translate(65 59) scale(1)" zIndex="0.1" /><g class="highcharts-series highcharts-tracker" visibility="visible" clip-path="url(&quot;#highcharts-1&quot;)" transform="translate(65 59) scale(1)" zIndex="0.1"><rect fill="#0d233a" x="34" y="66" width="10" height="200" rx="0" ry="0" /><rect fill="#0d233a" x="114" y="44" width="10" height="222" rx="0" ry="0" /><rect fill="#0d233a" x="193" y="86" width="10" height="180" rx="0" ry="0" /><rect fill="#0d233a" x="272" y="47" width="10" height="219" rx="0" ry="0" /><rect fill="#0d233a" x="352" y="116" width="10" height="150" rx="0" ry="0" /><rect fill="#0d233a" x="431" y="261" width="10" height="5" rx="0" ry="0" /></g><g class="highcharts-markers" visibility="visible" transform="translate(65 59) scale(1)" zIndex="0.1" /><g class="highcharts-series highcharts-tracker" visibility="visible" clip-path="url(&quot;#highcharts-1&quot;)" transform="translate(65 59) scale(1)" zIndex="0.1"><rect fill="#8bbc21" x="50" y="266" width="10" height="0" rx="0" ry="0" /><rect fill="#8bbc21" x="130" y="266" width="10" height="0" rx="0" ry="0" /><rect fill="#8bbc21" x="209" y="266" width="10" height="0" rx="0" ry="0" /><rect fill="#8bbc21" x="288" y="263" width="10" height="3" rx="0" ry="0" /><rect fill="#8bbc21" x="368" y="247" width="10" height="19" rx="0" ry="0" /><rect fill="#8bbc21" x="447" y="266" width="10" height="0" rx="0" ry="0" /></g><g class="highcharts-markers" visibility="visible" transform="translate(65 59) scale(1)" zIndex="0.1" /></g><text class="highcharts-title" style="width: 487px; color: rgb(39, 75, 109); font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 16px; fill: #274b6d;" text-anchor="middle" x="276" y="25" zIndex="4"><tspan x="276">第一建經進案件數統計</tspan></text><text class="highcharts-subtitle" style="width: 487px; color: rgb(77, 117, 158); font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; fill: #4d759e;" text-anchor="middle" x="276" y="40" zIndex="4"><tspan x="276">(2013/9~2014/2)</tspan></text><g class="highcharts-legend" transform="translate(180 358)" zIndex="7"><rect visibility="visible" fill="none" stroke="#909090" stroke-width="1" x="0.5" y="0.5" width="190" height="25" rx="5" ry="5" /><g zIndex="1"><g><g class="highcharts-legend-item" transform="translate(8 3)" zIndex="1"><text style="color: rgb(39, 75, 109); font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; cursor: pointer; fill: #274b6d;" text-anchor="start" x="21" y="15" zIndex="2"><tspan x="21">一銀</tspan></text><rect fill="#2f7ed8" x="0" y="4" width="16" height="12" rx="2" ry="2" zIndex="3" /></g><g class="highcharts-legend-item" transform="translate(61 3)" zIndex="1"><text style="color: rgb(39, 75, 109); font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; cursor: pointer; fill: #274b6d;" text-anchor="start" x="21" y="15" zIndex="2"><tspan x="21">永豐西門</tspan></text><rect fill="#0d233a" x="0" y="4" width="16" height="12" rx="2" ry="2" zIndex="3" /></g><g class="highcharts-legend-item" transform="translate(138 3)" zIndex="1"><text style="color: rgb(39, 75, 109); font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 12px; cursor: pointer; fill: #274b6d;" text-anchor="start" x="21" y="15" zIndex="2"><tspan x="21">台新</tspan></text><rect fill="#8bbc21" x="0" y="4" width="16" height="12" rx="2" ry="2" zIndex="3" /></g></g></g></g><g class="highcharts-axis-labels" zIndex="7"><text style="width: 59px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="middle" x="104.6667" y="339"><tspan x="104.6667">2013.09</tspan></text><text style="width: 59px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="middle" x="184" y="339"><tspan x="184">2013.1</tspan></text><text style="width: 59px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="middle" x="263.3334" y="339"><tspan x="263.3334">2013.11</tspan></text><text style="width: 59px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="middle" x="342.6667" y="339"><tspan x="342.6667">2013.12</tspan></text><text style="width: 59px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="middle" x="422" y="339"><tspan x="422">2014.01</tspan></text><text style="width: 59px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="middle" x="501.3334" y="339"><tspan x="501.3334">2014.02</tspan></text></g><g class="highcharts-axis-labels" zIndex="7"><text style="width: 162px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="end" x="57" y="325.65"><tspan x="57">0</tspan></text><text style="width: 162px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="end" x="57" y="259.15"><tspan x="57">200</tspan></text><text style="width: 162px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="end" x="57" y="192.65"><tspan x="57">400</tspan></text><text style="width: 162px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="end" x="57" y="126.15"><tspan x="57">600</tspan></text><text style="width: 162px; color: rgb(102, 102, 102); line-height: 14px; font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 11px; cursor: default; fill: #666;" opacity="1" text-anchor="end" x="57" y="59.65"><tspan x="57">800</tspan></text></g><g class="highcharts-tooltip" visibility="hidden" style="padding: 0px; white-space: nowrap; cursor: default;" opacity="0" transform="translate(296 215)" zIndex="8"><rect fill="none" fill-opacity="0.85" stroke="black" stroke-opacity="0.05" stroke-width="5" transform="translate(1 1)" x="0.5" y="0.5" width="113" height="79" rx="3" ry="3" isShadow="true" /><rect fill="none" fill-opacity="0.85" stroke="black" stroke-opacity="0.1" stroke-width="3" transform="translate(1 1)" x="0.5" y="0.5" width="113" height="79" rx="3" ry="3" isShadow="true" /><rect fill="none" fill-opacity="0.85" stroke="black" stroke-opacity="0.15" stroke-width="1" transform="translate(1 1)" x="0.5" y="0.5" width="113" height="79" rx="3" ry="3" isShadow="true" /><rect fill="rgb(255, 255, 255)" fill-opacity="0.85" stroke="#2f7ed8" stroke-width="1" x="0.5" y="0.5" width="113" height="79" rx="3" ry="3" anchorX="125.61708062502595" anchorY="64.00311279296875" /></g><text style="color: rgb(144, 144, 144); font-family: &quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, Verdana, Arial, Helvetica, sans-serif; font-size: 9px; cursor: pointer; fill: #909090;" text-anchor="end" x="541" y="395" zIndex="8"><tspan x="541">Highcharts.com</tspan></text></svg>');
$data = array('filename' => 'chart' , 'type' => 'image/jpeg' , 'options' => $contents) ;
$options = array(
      'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
));

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

//var_dump($result);
$fp = fopen('pdfChart.jpg', 'w');
fwrite($fp, $result);
fclose($fp);
##
?>