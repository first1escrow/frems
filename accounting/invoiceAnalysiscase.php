<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../includes/accounting/getInvoiceAnalysis.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

##
$yr = trim($_POST['dateYear']) ;
$mn = trim($_POST['dateMonth']) ;
$ok = trim($_POST['ex']) ;




if (!$yr) $yr = date("Y") - 1911 ;
// if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")-1))) ;
if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")))) ;

$today['Y'] = date("Y") - 1911;
$today['M'] = (int)date("m");


$search_m = str_pad($mn, 2,'0',STR_PAD_LEFT);
##

//年度顯示
$y = '' ;
for ($i = 0 ; $i < 100 ; $i ++) {
	$patt = $i + 100 ;

	if ($patt > 103) {
		if (($patt == $yr) ) { $sl = " selected='selected'" ; }
		else { $sl = '' ; }
		
		$y .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
	}

	
}
unset($patt);
//月份顯示
$m = '' ;
for ($i = 0 ; $i < 12 ; $i ++) {
	$patt = $i + 1 ;
	
	if ($patt==$mn) { $sl = " selected='selected'" ; $now_m = $patt;}
	else { $sl = '' ; }
	
	$m .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}
unset($patt);
##
if ($ok=='ok') {

	include_once 'invoiceAnalysiscaseExcel.php';
	die;
}
##
// $date_start = $yr.'/01/01';
// $date_end = $yr.'/12/31';
$tmp_month = array();
$tmp_year = array();
$start_day = ($yr+1911)."-01-01";
$start_end = ($yr+1911)."-12-31";
$sql = "SELECT * FROM tContractInvoiceCount WHERE cMonth >='".$start_day."' AND cMonth <= '".$start_end."'";

$rs = $conn->Execute($sql);
$month_count=$rs->RecordCount();

while (!$rs->EOF) {
	$tmp = explode('-', $rs->fields['cMonth']);
	$tmp[0] = $tmp[0]-1911;
	##月##
	$data[$tmp[0]][$tmp[1]]['total'] = $rs->fields['cInvoiceTotal'];//發票開立張數
	$data[$tmp[0]][$tmp[1]]['printY'] = $rs->fields['cInvoiceTotalPrint'];//列印紙本張數
	$data[$tmp[0]][$tmp[1]]['printN'] = $rs->fields['cInvoiceTotalPrintN'];//未列印紙本張數
	//二聯OR三聯
	
	$data[$tmp[0]][$tmp[1]]['二聯']['total'] = $rs->fields['cB2C']; 
	$data[$tmp[0]][$tmp[1]]['三聯']['total'] = $rs->fields['cB2B']; 

	$data[$tmp[0]][$tmp[1]]['二聯']['printY'] = $rs->fields['cB2Cprint'];
	$data[$tmp[0]][$tmp[1]]['三聯']['printY'] = $rs->fields['cB2Bprint'];

	$data[$tmp[0]][$tmp[1]]['二聯']['printN'] = $rs->fields['cB2CprintN'];
	$data[$tmp[0]][$tmp[1]]['三聯']['printN'] = $rs->fields['cB2BprintN'];
	##
	##年##
	$data2[$tmp[0]]['total'] += $rs->fields['cInvoiceTotal'];
	$data2[$tmp[0]]['printY'] += $rs->fields['cInvoiceTotalPrint'];
	$data2[$tmp[0]]['printN'] += $rs->fields['cInvoiceTotalPrintN'];
	$data2[$tmp[0]]['二聯']['total'] += $rs->fields['cB2C'];
	$data2[$tmp[0]]['三聯']['total'] += $rs->fields['cB2B'];
	$data2[$tmp[0]]['二聯']['printY'] += $rs->fields['cB2Cprint'];
	$data2[$tmp[0]]['三聯']['printY'] += $rs->fields['cB2Bprint'];
	$data2[$tmp[0]]['二聯']['printN'] += $rs->fields['cB2CprintN'];
	$data2[$tmp[0]]['三聯']['printN'] += $rs->fields['cB2BprintN'];

	if ( $tmp[0] == $yr && $tmp[1] == $mn) {
	
		$data[$tmp[0]][$tmp[1]]['css'] = "show";
		$data2[$tmp[0]]['css'] = "show";
		$sum2['css'] = "show";
	}

	unset($tmp);
	$rs->MoveNext();
}
// echo $sql;
// echo "<pre>";
// print_r($data2);
// echo "</pre>";
######################################
#################################計算##############################################
//開立總數

$month_data['total']['三聯'] = round($data2[$yr]['三聯']['total']/$month_count); //B2B 月平均

// echo $month_data['total']['三聯'];

$month_data['total']['二聯'] = round($data2[$yr]['二聯']['total']/$month_count);//B2C 月平均
$month_data['total']['average'] = round($data2[$yr]['total']/$month_count); //全部月平均

// $year_data['total']['三聯'] = $month_data['total']['三聯'] * 12; //B2B平均年張數
$year_data['total']['三聯'] = round(($data2[$yr]['三聯']['total']/$month_count)*12); //B2B 月平均
$year_data['total']['二聯'] = round(($data2[$yr]['二聯']['total']/$month_count)*12); //B2B平均年張數
$year_data['total']['average'] = round(($data2[$yr]['total']/$month_count)*12);

//百分比
if ($data[$yr][$search_m]['total'] == '' || $data[$yr][$search_m]['total'] == 0) {
	$month_per['total']['三聯']['total'] = 0;
	$month_per['total']['三聯']['total'] = 0;
}else{
	$month_per['total']['三聯']['total'] = round($data[$yr][$search_m]['三聯']['total']/$data[$yr][$search_m]['total'],2)*100 ; //本月B2B%
	$month_per['total']['二聯']['total'] = round($data[$yr][$search_m]['二聯']['total']/$data[$yr][$search_m]['total'],2)*100 ;	//本月B2C%

}
 //判斷是否為100%
$month_per['total']['二聯']['total'] = checkper($month_per['total']['三聯']['total'],$month_per['total']['二聯']['total']);


##
$month_per['total']['三聯']['average'] = round(round($data2[$yr]['三聯']['total']/$month_count)/$month_data['total']['average'],2)*100; //B2B月平均%
$month_per['total']['二聯']['average'] = round(round($data2[$yr]['二聯']['total']/$month_count)/$month_data['total']['average'],2)*100; //B2B月平均%

 //判斷是否為100%
$month_per['total']['二聯']['average'] = checkper($month_per['total']['三聯']['average'],$month_per['total']['二聯']['average']);

##

$year_per['total']['三聯'] = round($year_data['total']['三聯']/$year_data['total']['average'],2)*100;
$year_per['total']['二聯'] = round($year_data['total']['二聯']/$year_data['total']['average'],2)*100;

//判斷是否為100%
$year_per['total']['二聯'] = checkper($year_per['total']['三聯'],$year_per['total']['二聯']);


###########################################################################
//列印紙本
$month_data['printY']['三聯']  = round($data2[$yr]['三聯']['printY']/$month_count);//B2B月平均
$month_data['printY']['二聯']  = round($data2[$yr]['二聯']['printY']/$month_count);//B2C月平均
$month_data['printY']['average'] = round($data2[$yr]['printY']/$month_count);//月平均總計

$year_data['printY']['三聯'] = round(($data2[$yr]['三聯']['printY']/$month_count)*12);//B2B平均年張數
$year_data['printY']['二聯']  = round(($data2[$yr]['二聯']['printY']/$month_count)*12);//B2C平均年張數
$year_data['printY']['average'] = round(($data2[$yr]['printY']/$month_count)*12);


if ($data[$yr][$search_m]['printY'] !=0 || $data[$yr][$search_m]['printY'] !='') {
	$month_per['printY']['三聯']['total'] = round($data[$yr][$search_m]['三聯']['printY']/$data[$yr][$search_m]['printY'],2)*100 ; //本月B2B%
	$month_per['printY']['二聯']['total'] = round($data[$yr][$search_m]['二聯']['printY']/$data[$yr][$search_m]['printY'],2)*100 ;	//本月B2C%
}else{
	$month_per['printY']['三聯']['total'] = 0;
	$month_per['printY']['二聯']['total'] = 0;
}

//判斷是否為100%
$month_per['printY']['二聯']['total'] = checkper($month_per['printY']['三聯']['total'],$month_per['printY']['二聯']['total']);

###
$month_per['printY']['三聯']['average'] = round(round($data2[$yr]['三聯']['printY']/$month_count)/$month_data['printY']['average'],2)*100; //B2B月平均%
$month_per['printY']['二聯']['average'] = round(round($data2[$yr]['二聯']['printY']/$month_count)/$month_data['printY']['average'],2)*100; //B2C月平均%

//判斷是否為100%
$month_per['printY']['二聯']['average'] = checkper($month_per['printY']['三聯']['average'],$month_per['printY']['二聯']['average']);

##
$year_per['printY']['三聯'] = round($year_data['printY']['三聯']/$year_data['printY']['average'],2)*100; //B2B平均年張數%
$year_per['printY']['二聯'] = round($year_data['printY']['二聯']/$year_data['printY']['average'],2)*100; //B2C平均年張數%

//判斷是否為100%
$year_per['printY']['二聯'] = checkper($year_per['printY']['三聯'],$year_per['printY']['二聯']);
################################


$tmp = check($data[$yr],$data2[$yr]['三聯']['total'],'三聯','total');
$tmp2 = check($data[$yr],$data2[$yr]['三聯']['printY'],'三聯','printY');
$tmp3 = check($data[$yr],$data2[$yr]['二聯']['total'],'二聯','total');
$tmp4 = check($data[$yr],$data2[$yr]['二聯']['printY'],'二聯','printY');
$tmp5 = check2($data[$yr],$data2[$yr]['total'],'total');
$tmp6 = check2($data[$yr],$data2[$yr]['printY'],'printY');

for ($i=0; $i < count($tmp); $i++) { 
	//理論上陣列數一樣(都月份)
	$b2b['total'][$i]['count']=$tmp[$i];
	$b2b['printY'][$i]['count']=$tmp2[$i];

	$b2c['total'][$i]['count']=$tmp3[$i];
	$b2c['printY'][$i]['count']=$tmp4[$i];

	$all['total'][$i]['count']=$tmp5[$i];
	$all['printY'][$i]['count']=$tmp6[$i];

	if (($i+1) == $mn) {
		$b2b['total'][$i]['css'] = 'show' ;
		$b2b['printY'][$i]['css'] = 'show' ;
		$b2c['total'][$i]['css'] = 'show' ;
		$b2c['printY'][$i]['css'] = 'show' ;
		$all['total'][$i]['css'] = 'show' ;
		$all['printY'][$i]['css'] = 'show' ;
	}
}

unset($tmp);unset($tmp2);
unset($tmp3);unset($tmp4);
unset($tmp5);unset($tmp6);
#######列印紙本總張數
$arr['A'] = round($data[$yr][$search_m]['printY'] / $data[$yr][$search_m]['total'],3)*100;

$arr['B'] = round($month_data['printY']['average'] / $month_data['total']['average'],3)*100;

$arr['C'] = round($year_data['printY']['average'] / $year_data['total']['average'],3)*100;

// $data[$search_y][$search_m]['printY'] / $data[$search_y][$search_m]['total']



function checkper($val1,$val2)
{
	$total = $val1 + $val2;
	if ($total > 100) {
		$tmp = $tmp_ck-100;
		$val2 = $val2 - $tmp;
	}

	return $val2;
}

function check($arr,$total,$type1,$type2)
{
	$i = 0;

	foreach ($arr as $k => $v) {

		if ($total > 0) {
			$tmp[$i] = round($v[$type1][$type2]/$total,2)*100;
			$tmp_total = $tmp_total +$tmp[$i];
		}
	
		$i++;
	}

	

	if ($tmp_total >100) {
		$tmp_val = $tmp_total - 100;
		$tmp[(count($tmp)-1)] = $tmp[(count($tmp)-1)] - $tmp_val;
	}elseif ($tmp_total < 100) {
		$tmp_val = 100 - $tmp_total;
		$tmp[(count($tmp)-1)] = $tmp[(count($tmp)-1)] + $tmp_val;
	}


	return $tmp;
}

function check2($arr,$total,$type1)
{
	$i = 0;

	foreach ($arr as $k => $v) {

		if ($total > 0) {
			$tmp[$i] = round($v[$type1]/$total,2)*100;
			$tmp_total = $tmp_total +$tmp[$i];
		}
	
		$i++;
	}

	if ($tmp_total >100) {
		$tmp_val = $tmp_total - 100;
		$tmp[(count($tmp)-1)] = $tmp[(count($tmp)-1)] - $tmp_val;
	}elseif ($tmp_total < 100) {
		$tmp_val = 100 - $tmp_total;
		$tmp[(count($tmp)-1)] = $tmp[(count($tmp)-1)] + $tmp_val;
	}

	return $tmp;
}
##

if ($ok=='ok2') {

	
	include_once 'invoiceAnalysiscaseExcel2.php';
	die;
}




##
$smarty->assign('b2b',$b2b);
$smarty->assign('b2c',$b2c);
$smarty->assign('all',$all);
$smarty->assign("month_data",$month_data) ;
$smarty->assign("year_data",$year_data) ;
$smarty->assign("month_per",$month_per) ;
$smarty->assign("year_per",$year_per) ;
$smarty->assign("month",$month) ;
$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign('search_m',$search_m);
$smarty->assign('search_y',$yr);
$smarty->assign('data',$data);
$smarty->assign('data2',$data2);
$smarty->assign('msg',$msg);
$smarty->assign('sum2',$sum2);
$smarty->assign('month_count',$month_count);
$smarty->assign('arr',$arr);
$smarty->display('invoiceAnalysiscase.inc.tpl', '', 'accounting') ;
?>