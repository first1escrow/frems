<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php';
include_once '../session_check.php' ;
include_once '../report/getBranchType.php';
include_once '../includes/sales/getSalesInfo.php'; //function 都在這
include_once '../includes/maintain/feedBackData.php';
##

$sales = $_SESSION['member_id'];

if ($sales == 6) {
	 $sales = 25;
}
//時間下拉
$yr = trim(addslashes($_POST['dateYear'])) ;
$mn = trim(addslashes($_POST['dateMonth'])) ;
$ok = trim(addslashes($_POST['ex'])) ;

if (!$yr) $yr = date("Y") - 1911 ;
// if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")-1))) ;
if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")))) ;


##

//年度顯示
$y = '' ;
for ($i = 0 ; $i < 100 ; $i ++) {
	$patt = $i + 100 ;
	
	// if (($patt == $yr) && ($mn != '12')) { $sl = " selected='selected'" ; echo 'a'; }
	// else if ((($patt+1)==$yr)&&($mn=='12')) { $sl = " selected='selected'" ; echo 'b';}
	if (($patt == $yr) ) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$y .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

//月份顯示
$m = '' ;
for ($i = 0 ; $i < 12 ; $i ++) {
	$patt = $i + 1 ;
	
	if ($patt==$mn) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$m .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

##
	//使用率
	$tmp_use = array();
	if ($sales == 25) {
		$tmp = getUseMonth($sales,($yr+1911).'-01-01 00:00:00',($yr+1911).'-03-31 23:59:59');
	}else{
		$tmp = getUseMonth($sales,($yr+1911).'-01-01 00:00:00',($yr+1911).'-03-31 23:59:59');
	}
	
	if (is_array($tmp)) {
		foreach ($tmp as $k => $v) {
			$tmp_use[$k] = $v;
		}
	}
	unset($tmp);

	if ($sales == 25) {
		$tmp = getUseMonth($sales,($yr+1911).'-04-01 00:00:00',($yr+1911).'-06-31 23:59:59');
	}else{
		$tmp = getUseMonth($sales,($yr+1911).'-04-01 00:00:00',($yr+1911).'-06-31 23:59:59');
	}

	if (is_array($tmp)) {
		foreach ($tmp as $k => $v) {
			$tmp_use[$k] = $v;
		}
	}
	unset($tmp);
	
	if ($sales == 25) {
		$tmp = getUseMonth($sales,($yr+1911).'-07-01 00:00:00',($yr+1911).'-09-31 23:59:59');
	}else{
		$tmp = getUseMonth($sales,($yr+1911).'-07-01 00:00:00',($yr+1911).'-09-31 23:59:59');
	}

	if (is_array($tmp)) {
		foreach ($tmp as $k => $v) {
			$tmp_use[$k] = $v;
		}
	}
	unset($tmp);

	if ($sales == 25) {
		$tmp = getUseMonth($sales,($yr+1911).'-10-01 00:00:00',($yr+1911).'-12-31 23:59:59');
	}else{
		$tmp = getUseMonth($sales,($yr+1911).'-10-01 00:00:00',($yr+1911).'-12-31 23:59:59');
	}

	if (is_array($tmp)) {
		foreach ($tmp as $k => $v) {
			$tmp_use[$k] = $v;
		}
	}
	unset($tmp);


	for ($i=1; $i <= 12; $i++) { 
		//店家簽約數/達成率
		$date_start = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-01 00:00:00';
		$date_end = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-31 23:59:59';
		
		$data_b = getOwnBranch($sales,$date_start,$date_end) ; //該月新進仲介店數
		$data_s = getOwnScrivener($sales,$date_start,$date_end );//該月新進地政士數

		$summary1[$i]['targetcount'] = count($data_b) + count($data_s);//新進店總數 (該月新進仲介店數+該月新進地政士數)
		if ($i==2 && $yr < 106)//106年前二月份的要*2
		{
			$summary1[$i]['targetcount'] = $summary1[$i]['targetcount'] *2;
		}else{
			$summary1[$i]['targetcount'] = $summary1[$i]['targetcount'];
		}
		$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i);
		##
		
		//進件量/成長率

		if ($sales == 25) {
			$tmp = getOwnCase($sales,$date_start,$date_end);
		}else{
			$tmp = getOwnCase($sales,$date_start,$date_end);
		}


		$summary1[$i]['twcount'] = $tmp['tw'];
		$summary1[$i]['othercount'] = $tmp['other'];
		$summary1[$i]['Untw'] = $tmp['unTW'];
		$summary1[$i]['scrivener'] = $tmp['Scrivener'];
		// if ($i==2) { //二月份的要*2
		// 		$summary1[$i]['twcount'] = $summary1[$i]['twcount'] *2;
		// 		$summary1[$i]['othercount'] = $summary1[$i]['othercount'] *2;
		// 	}
		$summary1[$i]['groupcount'] = $summary1[$i]['twcount']+$summary1[$i]['othercount'];
		$summary1[$i]['group'] = getPercent($sales,($yr+1911),$i,$summary1[($i-1)]['groupcount'],$summary1[$i]['groupcount'],'g');
		##
		
		// //使用率
		$summary1[$i]['usecount'] = $tmp_use[($yr+1911).str_pad($i,2,'0',STR_PAD_LEFT)];
		// echo $i."月".$summary1[$i]['usecount']."<bR>" ;
		// if ($i==2) { //二月份的要*2
		// 		$summary1[$i]['usecount'] = $summary1[$i]['usecount'] *2;
		// 	}
		$summary1[$i]['use'] = getPercent($sales,($yr+1911),$i,$summary1[($i-1)]['usecount'],$summary1[$i]['usecount'],'u');

		##
		
		//貢獻率contribution
		$summary1[$i]['crtifiedMoney'] = $tmp['cCertifiedMoney']; 
		$summary1[$i]['feedBackMoney'] = $tmp['feedBackMoney']; 
		// if ($i==2) { //二月份的要*2
		// 		$summary1[$i]['crtifiedMoney'] = $summary1[$i]['crtifiedMoney'] *2;
		// 		$summary1[$i]['feedBackMoney'] = $tmp['feedBackMoney'] *2;
		// 	}
		$cmoney = $summary1[$i]['crtifiedMoney']-$summary1[$i]['feedBackMoney'];
		$lastcmoney = $summary1[($i-1)]['crtifiedMoney']-$summary1[($i-1)]['feedBackMoney'];

		$summary1[$i]['contribution'] = getPercent($sales,($yr+1911),$i,$lastcmoney,$cmoney,'c');
		##
		
		//店家簽約數/達成率(季)season1  進件量/成長率(季)season1
		if ($i == $mn) { //店家/地政士明細

			$Branch = $data_b;
			
			$BranchCount = count($Branch);
			$Scrivener = $data_s;
			$ScrivenerCount = count($Scrivener);
			$summary1[$i]['class'] = "show";
			$target = $summary1[$i]['target'];//查詢月達成率
			$group = $summary1[$i]['group'];//查詢月成長率
			$use = $summary1[$i]['use'];//查詢月成長率
			$contribution = $summary1[$i]['contribution'];//查詢月貢獻率
		}
		unset($data_b);unset($data_s); unset($total);unset($tmp);

	}

// echo "<pre>";
// print_r($summary1);
// echo "</pre>";


//計算升降級考核評分
	//計算升降級考核評分
	for ($i=1; $i <= count($summary1); $i++) { 

		$t = $summary1[$i]['targetcount'];
		$g = $summary1[$i]['group'];
		$u = $summary1[$i]['use'];
		$c = $summary1[$i]['contribution'];
		#簽約數/達成率
		//後面大於10 則把多出來的加到前一個去(超過季度的月份不加3
		//[4不算在同一個季度] 6[7不算在同一個季度] 9[10不算在同一個季度] 12[1不算在同一個季度] )
		if (($i%3) != 0 && $summary1[$i]['targetcount'] < 10 && $summary1[($i+1)]['targetcount'] >10) { 
			$t = getMoreTargetCount($summary1[$i]['targetcount'],$summary1[($i+1)]['targetcount']);
			// echo $t."-";
		}
			// $season1[1]['targetcount'] = $season1[1]['targetcount']+$summary1[$i]['targetcount'];
			// $season1[1]['target'] = $season1[1]['target']+$summary1[$i]['target'];
		
		//大於100%的算100%

		if ($t > 10) {$t = 10;} //數量
		// if ($g > 100) {$g = 100;}
		// if ($u > 100) {$u = 100;}
		// if ($c > 100) {$c = 100;}
			
		
		//使用量有排除的問題，所以單獨拉出來算
		if ($i <= 3) {
			//簽約數/達成率
			$season1[1]['targetcount'] = $season1[1]['targetcount']+$summary1[$i]['targetcount'];
			$season1[1]['target'] = $season1[1]['target']+($t*10);
			
			// echo $t."-";
			##
			
			//進件量/成長率
			$season1[1]['twcount'] = $season1[1]['twcount']+$summary1[$i]['twcount'];
			$season1[1]['othercount'] = $season1[1]['othercount']+$summary1[$i]['othercount'];
			$season1[1]['g_count'] = $season1[1]['twcount'] + $season1[1]['othercount'];
			##
			
			//使用率
			$season1[1]['usecount'] = $season1[1]['usecount']+$summary1[$i]['usecount'];
			// $season1[1]['use'] = $season1[1]['use']+$u;
			##
			
			//貢獻率
			$season1[1]['crtifiedMoney'] = $season1[1]['crtifiedMoney']+$summary1[$i]['crtifiedMoney'];
			$season1[1]['feedBackMoney'] = $season1[1]['feedBackMoney']+$summary1[$i]['feedBackMoney'];
			$season1[1]['realmoney'] = $season1[1]['crtifiedMoney'] - $season1[1]['feedBackMoney'];
			##

		}
		else if ($i > 3 && $i <= 6) {
			$season1[2]['targetcount'] = $season1[2]['targetcount']+$summary1[$i]['targetcount'];
			$season1[2]['target'] = $season1[2]['target']+($t*10);

			$season1[2]['twcount'] = $season1[2]['twcount']+$summary1[$i]['twcount'];
			$season1[2]['othercount'] = $season1[2]['othercount']+$summary1[$i]['othercount'];
			$season1[2]['g_count'] = $season1[2]['twcount'] + $season1[2]['othercount'];
			

			$season1[2]['usecount'] = $season1[2]['usecount']+$summary1[$i]['usecount'];
			

			$season1[2]['crtifiedMoney'] = $season1[2]['crtifiedMoney']+$summary1[$i]['crtifiedMoney'];
			$season1[2]['feedBackMoney'] = $season1[2]['feedBackMoney']+$summary1[$i]['feedBackMoney'];
			$season1[2]['realmoney'] = $season1[2]['crtifiedMoney'] - $season1[2]['feedBackMoney'];
		}
		else if ($i >6 && $i <=9) {
			$season1[3]['targetcount'] = $season1[3]['targetcount']+$summary1[$i]['targetcount'];
			$season1[3]['target'] = $season1[3]['target']+($t*10);

			$season1[3]['twcount'] = $season1[3]['twcount']+$summary1[$i]['twcount'];
			$season1[3]['othercount'] = $season1[3]['othercount']+$summary1[$i]['othercount'];
			$season1[3]['g_count'] = $season1[3]['twcount'] + $season1[3]['othercount'];
			

			$season1[3]['usecount'] = $season1[3]['usecount']+$summary1[$i]['usecount'];
			

			$season1[3]['crtifiedMoney'] = $season1[3]['crtifiedMoney']+$summary1[$i]['crtifiedMoney'];
			$season1[3]['feedBackMoney'] = $season1[3]['feedBackMoney']+$summary1[$i]['feedBackMoney'];
			$season1[3]['realmoney'] = $season1[3]['crtifiedMoney'] - $season1[3]['feedBackMoney'];
			
		}
		else if ($i >9 && $i <=12) {
			$season1[4]['targetcount'] = $season1[4]['targetcount']+$summary1[$i]['targetcount'];
			$season1[4]['target'] = $season1[4]['target']+($t*10);
			
			$season1[4]['twcount'] = $season1[4]['twcount']+$summary1[$i]['twcount'];
			$season1[4]['othercount'] = $season1[4]['othercount']+$summary1[$i]['othercount'];
			$season1[4]['g_count'] = $season1[4]['twcount'] + $season1[4]['othercount'];
			

			$season1[4]['usecount'] = $season1[4]['usecount']+$summary1[$i]['usecount'];
			

			$season1[4]['crtifiedMoney'] = $season1[4]['crtifiedMoney']+$summary1[$i]['crtifiedMoney'];
			$season1[4]['feedBackMoney'] = $season1[4]['feedBackMoney']+$summary1[$i]['feedBackMoney'];
			$season1[4]['realmoney'] = $season1[4]['crtifiedMoney'] - $season1[4]['feedBackMoney'];
		}
	}

//算出季的平均數字
	for ($i=1; $i <= 4; $i++) { 
		
		$season1[$i]['target'] = round(($season1[$i]['target']/3));//達成率

		$season1[$i]['group'] = getPercent2($sales,($yr+1911),$i,$season1[($i-1)]['g_count'],$season1[$i]['g_count'],'g');//成長率
		$season1[$i]['use'] = getPercent2($sales,($yr+1911),$i,$season1[($i-1)]['usecount'],$season1[$i]['usecount'],'u');//使用率

		// echo $season1[$i]['use'].":";

		$season1[$i]['contribution'] = getPercent2($sales,($yr+1911),$i,$season1[($i-1)]['realmoney'],$season1[$i]['realmoney'],'c');//貢獻率
		
		
		//季要顯示跟上一季相差多少
		$season2[$i]['target'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['target'],$season1[$i]['target'],'t');

		$season2[$i]['group'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['group'],$season1[$i]['group'],'g');//成長率
		
		// echo "<br>".$season1[($i-1)]['use']."ggggg".$season1[$i]['use']."-";
		// die;

		$season2[$i]['use'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['use'],$season1[$i]['use'],'u');//使用率
		$season2[$i]['contribution'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['contribution'],$season1[$i]['contribution'],'c');//貢獻率
	

	}

if ($mn <= 3 ) {
		$season1[1]['class']  = "show";
		$seasontarget = $season1[1]['target'];//達成率
		$seasongroup = $season1[1]['group'];//維持率
		$seasonuse = $season1[1]['use'];//使用率
		$seasoncontribution = $season1[1]['contribution']; //貢獻率

		$showseason['target'] = $season2[1]['target'];
		$showseason['group'] = $season2[1]['group'];
		$showseason['use'] = $season2[1]['use'];
		$showseason['contribution'] = $season2[1]['contribution'];
		$sess = 1 ;
}elseif ($mn > 3 && $mn <=6) {
		$season1[2]['class']  = "show";
		$seasontarget = $season1[2]['target'];
		$seasongroup = $season1[2]['group'];
		$seasonuse = $season1[2]['use'];
		$seasoncontribution = $season1[2]['contribution'];

		$showseason['target'] = $season2[2]['target'];
		$showseason['group'] = $season2[2]['group'];
		$showseason['use'] = $season2[2]['use'];
		$showseason['contribution'] = $season2[2]['contribution'];
		$sess = 2 ;
}elseif ($mn >6 && $mn <=9) {
		$season1[3]['class']  = "show";
		$seasontarget = $season1[3]['target'];
		$seasongroup = $season1[3]['group'];
		$seasonuse = $season1[3]['use'];
		$seasoncontribution = $season1[3]['contribution'];

		$showseason['target'] = $season2[3]['target'];
		$showseason['group'] = $season2[3]['group'];
		$showseason['use'] = $season2[3]['use'];
		$showseason['contribution'] = $season2[3]['contribution'];
		$sess = 3 ;
}elseif ($mn >9 && $mn <=12) {
		$season1[4]['class']  = "show";
		$seasontarget = $season1[4]['target'];
		$seasongroup = $season1[4]['group'];
		$seasonuse = $season1[4]['use'];
		$seasoncontribution = $season1[4]['contribution'];
		$sess = 4 ;

		$showseason['target'] = $season2[4]['target'];
		$showseason['group'] = $season2[4]['group'];
		$showseason['use'] = $season2[4]['use'];
		$showseason['contribution'] = $season2[4]['contribution'];
}


$now_month = sprintf("%d",date('m')) ;
// $grade = ($seasontarget * 0.4)+($seasongroup*0.3)+($seasonuse*0.2)+($seasoncontribution*0.1);//績效考核成績
$grade = getGrade($seasontarget,$seasongroup,$seasonuse,$seasoncontribution);

if ($grade > 100) {
	$grade = 100;
}
// echo $yr;
if (sprintf("%d",date('Y')) > ($yr+1911) ) {
	$now_check = '1';
}
$now_month = sprintf("%d",date('m')) ;
##
if ($ok=='ok') {

	$sql= "SELECT pName FROM tPeopleInfo WHERE pId = '".$sales."'";
	$rs = $conn->Execute($sql);
	$sales_name = $rs->fields['pName'];
	// echo $sales_name;
	include_once 'salesSummaryExcel.php';

}
##
$smarty->assign('now_check',$now_check);
$smarty->assign("now_month",$now_month);
$smarty->assign('sess',$sess);
$smarty->assign('script',$script);
$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign('season1',$season1);
$smarty->assign('now_year',($yr));
$smarty->assign('summary1Table', $summary1Table) ;
$smarty->assign('summary1Table', $summary1Table) ;
$smarty->assign('BranchCount',$BranchCount);
$smarty->assign('Branch',$Branch);
$smarty->assign('ScrivenerCount',$ScrivenerCount);
$smarty->assign('Scrivener',$Scrivener);
$smarty->assign('target',$target);
$smarty->assign('seasontarget',$seasontarget);
$smarty->assign('seasongroup',$seasongroup);
$smarty->assign('seasonuse',$seasonuse);
$smarty->assign('group',$group);
$smarty->assign('use',$use);
$smarty->assign('grade',$grade);
$smarty->assign('summary1',$summary1);
$smarty->assign('contribution',$contribution);
$smarty->assign('seasoncontribution',$seasoncontribution);
$smarty->assign('season2',$season2);
$smarty->assign('showseason',$showseason);
//$contribution = $season1[$i]['contribution'];
$smarty->display('salesSummary.inc.tpl', '', 'sales') ;
?>