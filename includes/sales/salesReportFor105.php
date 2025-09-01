<?php

if ($sales) {


	$date_start = ($yr+1911).'-01-01';
	$date_end = ($yr+1911).'-12-31';
	$i = 1;
	$sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$date_start."' AND sDate <= '".$date_end."' AND sSales ='".$sales."' ORDER BY sDate ASC";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		//簽約數
		$summary1[$i]['targetcount'] = $rs->fields['sSignQuantity'];

		if ($i == 2) {
			$summary1[$i]['targetcount'] = $summary1[$i]['targetcount'] * 2 ;
		}
		//達成率
		$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i);

		//進件量
		$summary1[$i]['twcount'] = $rs->fields['sCaseTwQuantity']; //台屋
		$summary1[$i]['Untw'] = $rs->fields['sCaseOtherQuantity'];//他牌
		$summary1[$i]['scrivener'] = $rs->fields['sCaseScrivenerQuantity']; //非仲介
		$summary1[$i]['othercount'] = $rs->fields['sCaseOtherQuantity']+$rs->fields['sCaseScrivenerQuantity']; // 他牌+非仲介
		$summary1[$i]['groupcount'] = $summary1[$i]['twcount']+$summary1[$i]['othercount'];
		$summary1[$i]['group'] = getPercent($sales,($yr+1911),$i,$summary1[($i-1)]['groupcount'],$summary1[$i]['groupcount'],'g');
		
		// //使用率
		$summary1[$i]['usecount'] = $rs->fields['sUseQuantity'];
		$summary1[$i]['use'] = getPercent($sales,($yr+1911),$i,$summary1[($i-1)]['usecount'],$summary1[$i]['usecount'],'u');


		//貢獻率contribution
		$summary1[$i]['crtifiedMoney'] = $rs->fields['sCertifiedMoney']; 
		$summary1[$i]['feedBackMoney'] = $rs->fields['sCaseFeedBackMoney'];

		$cmoney = $summary1[$i]['crtifiedMoney']-$summary1[$i]['feedBackMoney'];
		$lastcmoney = $summary1[($i-1)]['crtifiedMoney']-$summary1[($i-1)]['feedBackMoney'];

		$summary1[$i]['contribution'] = getPercent($sales,($yr+1911),$i,$lastcmoney,$cmoney,'c');


		if ($i == $mn) { //店家/地政士明細

			$date_start = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-01 00:00:00';
			$date_end = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-31 23:59:59';
			
			$Branch = getOwnBranch($sales,$date_start,$date_end) ; //該月新進仲介店數
			$Scrivener = getOwnScrivener($sales,$date_start,$date_end);//該月新進地政士數
			$BranchCount = count($Branch);
			$ScrivenerCount = count($Scrivener);
			$summary1[$i]['class'] = "show";
			$target = $summary1[$i]['target'];//查詢月達成率
			$group = $summary1[$i]['group'];//查詢月成長率
			$use = $summary1[$i]['use'];//查詢月成長率
			$contribution = $summary1[$i]['contribution'];//查詢月貢獻率
		}

		$i++;

		$rs->MoveNext();
	}

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
	

	//本季考核
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
			$showseason['target'] = $season2[$i]['target'];

			$showseason['target'] = $season2[4]['target'];
			$showseason['group'] = $season2[4]['group'];
			$showseason['use'] = $season2[4]['use'];
			$showseason['contribution'] = $season2[4]['contribution'];
			$sess = 4 ;
	}
	##
	
	// $grade = ($seasontarget * 0.4)+($seasongroup*0.3)+($seasonuse*0.2)+($seasoncontribution*0.1);//績效考核成績
	$grade = getGrade($seasontarget,$seasongroup,$seasonuse,$seasoncontribution,$yr);

	if ($grade > 100) {
		$grade = 100;
	}
	if ($seasontarget > 100) {$seasontarget = 100;}//超過100只能是100
	if ($seasongroup > 100) {$seasongroup = 100;}//超過100只能是100
	if ($seasonuse > 100) {$seasonuse = 100;}//超過100只能是100
	if ($seasoncontribution > 100) {$seasoncontribution = 100;}//超過100只能是100

	$oseasontarget = $seasontarget;
	$oseasongroup = $seasongroup;
	$oseasonuse = $seasonuse;
	$oseasoncontribution = $seasoncontribution;


	$seasontarget = $seasontarget*0.4;
	$seasongroup = $seasongroup*0.3;
	$seasonuse = $seasonuse*0.2;
	$seasoncontribution = $seasoncontribution*0.1;

	
}else{
	$script = '$("[name=\'excel\']").hide();';
		
}




?>