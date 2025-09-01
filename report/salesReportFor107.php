<?php

if ($sales) {
	
	##確認是否滿一年##
	$sql = "SELECT pOnBoard FROM  tPeopleInfo WHERE pId = '".$sales."'";
	$rs = $conn->Execute($sql);

	$pOnBoard = date('Y-m-d',strtotime("+12 month",strtotime($rs->fields['pOnBoard'])));
	// $pOnBoard = $rs->fields['pOnBoard'];

	##

	//
	// $tmpD = ($yr+1911)."-".str_pad($mn,2,'0',STR_PAD_LEFT)."-31";
	// $check = ($pOnBoard > $tmpD)? false:true;

	// echo $pOnBoard."_".$tmpD."<br>";
	// if ($check) { //滿一年
	// 	echo '滿一年';
	// }
	

	// unset($tmpD);
	// $range_start = date('Y-m-d',strtotime("-12 month",strtotime(($yr+1911)."-".str_pad(1,2,'0',STR_PAD_LEFT))))." 00:00:00";
	
	
	//$range_start = date('Y-m-d',strtotime("-12 month",strtotime(($yr+1911)."-".str_pad($season_first_m,2,'0',STR_PAD_LEFT))))." 00:00:00";
	
	
	// $yr = 106;

	##
	
    //去年
    $last_start = ($yr + 1910).'-01-01' ;   //去年起始
    $last_end = ($yr + 1910).'-12-31' ;     //去年結束
    
    $i = 1 ;
    $sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$last_start."' AND sDate <= '".$last_end."' AND sSales ='".$sales."' ORDER BY sDate ASC";
    // echo $sql."<br>\n" ; exit;
    $rs = $conn->Execute($sql) ;
    
    $summary2 = array() ;
    //計算升降級考核評分
    $seasonLast = array() ;
	while (!$rs->EOF) {
		//簽約數 達成率
		$summary2[$i]['targetcount'] = $rs->fields['sSignQuantity'];    //簽約數
		##
        
		//進件量 成長率
		//20180419 立環跟欣怡因為第二季互相交換地區 第二季的計算如果有要抓第二季前的資料必須抓取對方的資料''
		if (($sales == 42 ) &&  $mn >3 && $mn <= 6 && $i < 4) { //未滿一年
			$otherData = getOtherSalesData($sales,($yr + 1910)."-".$i."-01",($yr + 1910)."-".$i."-31");
			$summary2[$i]['twcount'] = $otherData['twcount'];
			$summary2[$i]['othercount'] = $otherData['othercount'];
			unset($otherData);

		}elseif($sales == 38 &&  $mn >3 && $mn <= 6 &&  $i >3 && $i <= 6){ //立環滿一年
			$otherData = getOtherSalesData($sales,($yr + 1910)."-".$i."-01",($yr + 1910)."-".$i."-31");
			$summary2[$i]['twcount'] = $otherData['twcount'];
			$summary2[$i]['othercount'] = $otherData['othercount'];
			unset($otherData);
		}else{
			$summary2[$i]['twcount'] = $rs->fields['sCaseTwQuantity']; //台屋
			$summary2[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介(非台屋)
		}
		
		// $summary2[$i]['groupcount'] = $summary2[$i]['twcount']+$summary2[$i]['othercount'];
		##

		//季
		$sess = 0;
		if ($i <= 3) {  //第一季
			$sess = 1;
		}else if ($i > 3 && $i <= 6) {
			$sess = 2;
		}else if ($i > 6 && $i <= 9) {
			$sess = 3;
		}else if ($i >9 && $i <=12) {
			$sess = 4;
		}

		//簽約數/達成率
		$seasonLast[$sess]['targetcount'] +=  $summary2[$i]['targetcount'] ;    //簽約數
		// $seasonLast[1]['target'] += $summary2[$i]['target'] ;   //達成率
		##
			
		//進件量/成長率

		$seasonLast[$sess]['twcount'] += $summary2[$i]['twcount'] ;  //進件量(台屋)
		$seasonLast[$sess]['othercount'] += $summary2[$i]['othercount'] ;     //進件量(非台屋)
		##
        $i++;

		$rs->MoveNext() ;
	}
	unset($sess);
    // echo '<pre>' ;
    // print_r($summary2) ;
    ##
    
	//計算升降級考核評分
 //    $seasonLast = array() ;
	// for ($i = 1 ; $i <= count($summary2) ; $i ++) {
	// 	$sess = 0;
	// 	if ($i <= 3) {  //第一季
	// 		$sess = 1;
	// 	}else if ($i > 3 && $i <= 6) {
	// 		$sess = 2;
	// 	}else if ($i > 6 && $i <= 9) {
	// 		$sess = 3;
	// 	}else if ($i >9 && $i <=12) {
	// 		$sess = 4;
	// 	}

	// 	//簽約數/達成率
	// 	$seasonLast[$sess]['targetcount'] +=  $summary2[$i]['targetcount'] ;    //簽約數
	// 	// $seasonLast[1]['target'] += $summary2[$i]['target'] ;   //達成率
	// 	##
			
	// 	//進件量/成長率
	// 	$seasonLast[$sess]['twcount'] += $summary2[$i]['twcount'] ;  //進件量(台屋)
	// 	$seasonLast[$sess]['othercount'] += $summary2[$i]['othercount'] ;     //進件量(非台屋)
	// 	##
	// }
	unset($sess);
   // echo '<pre>' ;print_r($seasonLast) ; //exit ;
    ##
     
    //今年
	$date_start = ($yr + 1911).'-01-01' ;   //今年起始
	$date_end = ($yr + 1911).'-12-31' ;     //今年結束

	$i = 1;
	$sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$date_start."' AND sDate <= '".$date_end."' AND sSales ='".$sales."' ORDER BY sDate ASC";
	$rs = $conn->Execute($sql) ;
    
	while (!$rs->EOF) {
		//簽約數 達成率
		$summary1[$i]['targetcount'] = $rs->fields['sSignQuantity'];    //簽約數
		$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i);   //達成率
        ##
  		// if ($i <= 3) {  //第一季
		// 	$sess = 1;
			
		// }else if ($i > 3 && $i <= 6) {   //第二季
		// 	$sess = 2;
		// }
		// else if ($i > 6 && $i <= 9) {   //第三季
		// 	$sess = 3;

		// }else if ($i >9 && $i <=12) {    //第四季
		// 	$sess = 4;

		// }

		//進件量
		//20180419 立環跟欣怡因為第二季互相交換地區 第二季的計算如果有要抓第二季前的資料必須抓取對方的資料''
		if (($sales == 42 ) &&  $mn >3 && $mn <= 6 && $i < 4) { //未滿一年
			$otherData = getOtherSalesData($sales,($yr + 1911)."-".$i."-01",($yr + 1911)."-".$i."-31");
			$summary1[$i]['twcount'] = $otherData['twcount'];
			$summary1[$i]['othercount'] = $otherData['othercount'];

			unset($otherData);

		}
		// elseif($sales == 38 &&  $mn >3 && $mn <= 6 &&  $i >3 && $i <= 6){ //立環滿一年
		// 	$otherData = getOtherSalesData($sales,($yr + 1911)."-".$i."-01",($yr + 1911)."-".$i."-31");
		// 	// print_r($otherData);
		// 	$summary1[$i]['twcount'] = $otherData['twcount'];
		// 	$summary1[$i]['othercount'] = $otherData['othercount'];
		// 	unset($otherData);
		// }
		else{
			$summary1[$i]['twcount'] = $rs->fields['sCaseTwQuantity']; //台屋		
			$summary1[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介
		}
		

		// $summary1[$i]['groupcount'] = $summary1[$i]['twcount']+$summary1[$i]['othercount'];
		// $summary1[$i]['Untw'] = $rs->fields['sCaseOtherQuantity'];//他牌
		// $summary1[$i]['scrivener'] = $rs->fields['sCaseScrivenerQuantity']; //非仲介
		
		//檢查計算區間是否滿一年
		$tmpD = ($yr+1911)."-".str_pad($i,2,'0',STR_PAD_LEFT)."-31";
		$check = ($pOnBoard > $tmpD)? false:true;

		if ($check) { //滿一年
			$summary1[$i]['groupTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount'],$summary1[$i]['twcount'],'g',$check,'show');  //成長率
			$summary1[$i]['groupUnTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check,'show');  //成長率
			$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount'],$summary1[$i]['twcount'],'g',$check);  //成長率
			$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check);  //成長率
		
		}else{
			
			$summary1[$i]['groupTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['twcount'],$summary1[$i]['twcount'],'gTw',$check,'show');  //成長率
			
			$summary1[$i]['groupUnTWshow'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['othercount'],$summary1[$i]['othercount'],'gUnTw',$check,'show');  //成長率
			$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['twcount'],$summary1[$i]['twcount'],'gTw',$check);  //成長率
			$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['othercount'],$summary1[$i]['othercount'],'gUnTw',$check);  //成長率
		
		}

		
		
		##
        
	

		if ($i == $mn) { //店家/地政士明細(當月份資訊)
			$date_start = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-01 00:00:00';
			$date_end = ($yr+1911).'-'.str_pad($i,2,'0',STR_PAD_LEFT).'-31 23:59:59';
			
			$Branch = getOwnBranch($sales,$date_start,$date_end) ; //該月新進仲介店數
			$Scrivener = getOwnScrivener($sales,$date_start,$date_end);//該月新進地政士數
			
			$BranchCount = count($Branch);
			$ScrivenerCount = count($Scrivener);
			$summary1[$i]['class'] = "show";
			$target = $summary1[$i]['target'];//查詢月達成率
			$groupTW = $summary1[$i]['groupTW'];//查詢月成長率
			$groupUnTW = $summary1[$i]['groupUnTW'];//查詢月成長率
			
			$tmp_cut = getSameStore($sales,$Branch,$Scrivener);
			$tmp_cut2 = getUnApplyLine($sales,$Scrivener); //1/4有簽約地政士有加LINE才算
			
			
			$summary1[$i]['targetcount'] = $BranchCount+$ScrivenerCount-$tmp_cut-$tmp_cut2;
			$summary1[$i]['target'] = getOwnStoreTarget($summary1[$i]['targetcount'],$yr,$i);   //達成率
			unset($tmp_cut);
		}

		//季
		$sess = 0;
		
		//使用量有排除的問題，所以單獨拉出來算
		if ($i <= 3) {  //第一季
			$sess = 1;
			
		}else if ($i > 3 && $i <= 6) {   //第二季
			$sess = 2;
		}
		else if ($i > 6 && $i <= 9) {   //第三季
			$sess = 3;

		}else if ($i >9 && $i <=12) {    //第四季
			$sess = 4;

		}

		//簽約數/達成率
			$season1[$sess]['targetcount'] += $summary1[$i]['targetcount'] ;    //簽約數
			
			//進件量/成長率
			$season1[$sess]['twcount'] += $summary1[$i]['twcount'] ;  //進件量(台屋)
			$season1[$sess]['othercount'] += $summary1[$i]['othercount'] ;     //進件量(非台屋)
			##

		if (($sales == 42 ) &&  $mn >3 && $mn <= 6 && $i < 4) { //未滿一年
			$summary1[$i]['twcount'] = $rs->fields['sCaseTwQuantity'];
			$summary1[$i]['othercount'] = $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介

		}

		$i++;

		$rs->MoveNext();
	}
	unset($sess);
	

	//算出季的平均數字
	for ($i=1; $i <= 4; $i++) { 
		// echo $season1[$i]['targetcount']."_";
		$season1[$i]['target'] = round((($season1[$i]['targetcount']*10)/3));//達成率

		//檢查計算區間是否任職滿一年
		if ($i == 1) {
			$tmpD = ($yr+1911)."-03-01";
		}elseif($i == 2){
			$tmpD = ($yr+1911)."-06-01";
		}elseif($i == 3){
			$tmpD = ($yr+1911)."-09-01";
		}elseif($i == 4){
			$tmpD = ($yr+1911)."-12-01";
		}

		
		$check = ($pOnBoard > $tmpD)? false:true;
		// echo $pOnBoard.">".$tmpD;
		
		if ($check) { //滿一年(比較去年同期季)
			
			$season1[$i]['groupTW'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw',$check);
			$season1[$i]['groupUnTW'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check);
			
			$season1[$i]['groupTWshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['twcount'], $season1[$i]['twcount'], 'gTw',$check,'show');
			$season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr+1911), $i, $seasonLast[$i]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check,'show');
		
		}else{
			
			
			$season1[$i]['groupTW'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['twcount'], $season1[$i]['twcount'], 'gTw',$check);
			$season1[$i]['groupUnTW'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check);
			
			$season1[$i]['groupTWshow'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['twcount'], $season1[$i]['twcount'], 'gTw',$check,'show');
			$season1[$i]['groupUnTWshow'] = getPercent107($sales, ($yr+1911), $i, $season1[($i-1)]['othercount'], $season1[$i]['othercount'], 'gUnTw',$check,'show');
		
		}

		// if ($check) { //滿一年
		// 	$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['twcount'],$summary1[$i]['twcount'],'g',$check);  //成長率
		// 	$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary2[$i]['othercount'],$summary1[$i]['othercount'],'g',$check);  //成長率
		// }else{
			
		// 	$summary1[$i]['groupTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['twcount'],$summary1[$i]['twcount'],'gTw',$check);  //成長率
		// 	$summary1[$i]['groupUnTW'] = getPercentMonth107($sales,($yr+1911),$i,$summary1[($i-1)]['othercount'],$summary1[$i]['othercount'],'gUnTw',$check);  //成長率
		
		// }

		// $season1[$i]['group'] = getPercent107($sales,($yr+1911),$i,$season1[($i-1)]['g_count'],$season1[$i]['g_count'],'g');//成長率
		

		// $season1[$i]['use'] = getPercent107($sales,($yr+1911),$i,$season1[($i-1)]['usecount'],$season1[$i]['usecount'],'u');//使用率

		// echo $season1[$i]['use'].":";

		// $season1[$i]['contribution'] = getPercent107($sales,($yr+1911),$i,$season1[($i-1)]['realmoney'],$season1[$i]['realmoney'],'c');//貢獻率
		
		
		//季要顯示跟上一季相差多少
		// $season2[$i]['target'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['target'],$season1[$i]['target'],'t');
		// $season2[$i]['group'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['group'],$season1[$i]['group'],'g');//成長率
		// $season2[$i]['use'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['use'],$season1[$i]['use'],'u');//使用率
		// $season2[$i]['contribution'] = getPercent3($sales,($yr+1911),$i,$season1[($i-1)]['contribution'],$season1[$i]['contribution'],'c');//貢獻率
        ##
		

	}
	unset($tmpD);
	//本季考核
	if ($mn <= 3 ) {
		$sess = 1 ;
			
	}elseif ($mn > 3 && $mn <=6) {
	
		$sess = 2 ;
	}elseif ($mn >6 && $mn <=9) {
	
		$sess = 3 ;
	}elseif ($mn >9 && $mn <=12) {
	

		$sess = 4 ;
	}

	$season1[$sess]['class']  = "show";
	// $seasontarget = $season1[$sess]['target'];//達成率
	// $seasongroupTW = $season1[$sess]['groupTW'];
	// $seasongroupUnTW = $season1[$sess]['groupUnTW'];
			
	$showseason['target'] = $season1[$sess]['target'];
	$showseason['groupTW'] = $season1[$sess]['groupTW'];
	$showseason['groupUnTW'] = $season1[$sess]['groupUnTW'];
	##

		#############
	//有效率effective 
	// 用前一年度(由結算季度往前回推四季作為一整年度)所簽約代書及仲介通路之總和為分母，以分母的店家有進案之店家數為分子
	// 所得之比例數字以65%為基準 達此基準者，本指標所得之積分為20分，基準數字每增減一分，所得積分及增減一分，小於等於45% 就0分
	//(EX1:70%等於20分+5分=25分) (EX2:64%等於20分-1分=19分)
	//※已經停用的店 有效率分子 分母都要拉掉不計算 20170424
	//這季 $sess
/*
	$eff1 = getEffective3($yr,$sess,$sales);

	if($yr == 106 && $sess == 1){ //第一季算法
		

		if ($eff1['effective'] > 45) {//低於45%就是0
			$tmp = $eff1['effective'] - 65;
			if ($tmp == 0) {
				$eff1['score'] = 20;
			}else{
				$eff1['score'] = 20+$tmp;
			}
		}else{
			$eff1['score'] = 0;
		}
	}else{
		//1.結算季度不算，往前推四個季度,所簽約代書及仲介通路之總合為分母，結算季度加上前四個季度為分子，  
          // 所得之比例數字以70%為30分，基準數字每增減1%所得積分即增減0.4分。
          // 

		
			$tmp = $eff1['effective'] - 70;
			$eff1['score'] = 30+($tmp * 0.4); 
			if ($eff1['effective'] == 0 ) { 
				$eff1['score'] = 0;
			}

		
	}
*/
    //判斷季別
    $tmp[0] = $yr + 1911 ;
    $tmp[1] = $mn ;
    if ($tmp[1] <= 3) {     //第一季
        //本季
        $sDate = $tmp[0].'-01-01 00:00:00' ;
        $eDate = $tmp[0].'-03-31 23:59:59' ;
        ##
        
        //去年同期
        $lsDate = ($tmp[0] - 1).'-01-01 00:00:00' ;
        $leDate = ($tmp[0] - 1).'-03-31 23:59:59' ;
        ##
    }
    else if (($tmp[1] >= 4) && ($tmp[1] <= 6)) {    //第二季
        //本季
        $sDate = $tmp[0].'-04-01 00:00:00' ;
        $eDate = $tmp[0].'-06-30 23:59:59' ;
        ##
        
        //去年同期
        $lsDate = ($tmp[0] - 1).'-04-01 00:00:00' ;
        $leDate = ($tmp[0] - 1).'-06-30 23:59:59' ;
        ##
    }
    else if (($tmp[1] >= 7) && ($tmp[1] <= 9)) {    //第三季
        //本季
        $sDate = $tmp[0].'-07-01 00:00:00' ;
        $eDate = $tmp[0].'-09-30 23:59:59' ;
        ##
        
        //去年同期
        $lsDate = ($tmp[0] - 1).'-07-01 00:00:00' ;
        $leDate = ($tmp[0] - 1).'-09-30 23:59:59' ;
        ##
    }
    else {      //第四季
        //本季
        $sDate = $tmp[0].'-10-01 00:00:00' ;
        $eDate = $tmp[0].'-12-31 23:59:59' ;
        ##
        
        //去年同期
        $lsDate = ($tmp[0] - 1).'-10-01 00:00:00' ;
        $leDate = ($tmp[0] - 1).'-12-31 23:59:59' ;
        ##
    }
    ##

    $eff1 = array() ;
    
    $eff1['range_start2'] = DateChange($sDate) ;
    $eff1['range_end2'] = DateChange($eDate) ;
    
    $eff1['range_start'] = DateChange($lsDate) ;
    $eff1['range_end'] = DateChange($leDate) ;
    
    
    $sql = 'SELECT * FROM tSalesReportStore WHERE sSales = "'.$sales.'" AND sDate >= "'.$sDate.'" AND sDate <= "'.$eDate.'";' ;

    // if ($_SESSION['member_id'] == 6) {
    // 	echo $sql;
    // }
  
    $rs = $conn->Execute($sql) ;
    // print_r($rs->fields) ;
    //分子 (從以前到前一季的店家)有進案的店家
    $A = $rs->fields['sScrivener'] + $rs->fields['sRealty'] ;
    $eff1['no'] = $A ;

     //分母 (從以前到前一季的店家)
    $B = $rs->fields['sScrTotal'] + $rs->fields['sRealTotal'] ;
    $eff1['total'] = $B ;
    // print_r($rs->fields) ;
    // print_r($rs->fields) ; exit ;
    $eff1['effective'] = round(($A / $B ), 2)* 100 ;


    $json = json_decode($rs->fields['sStore'],true);

    // if (condition) {
    // 	# code...
    // }

    $eff1['data']['scrcase'] = getScrivener107($json['sc_yes']);
    $eff1['data']['scrnocase'] = getScrivener107($json['sc_no']);

    $eff1['data']['branchcase'] = getBranch107($json['br_yes']);
    $eff1['data']['branchnocase'] = getBranch107($json['br_no']);
    
   
   
    
   
	// echo $sql;
 //    echo "<pre>";
	// print_r($eff);
	// echo "</pre>";
   //$eff1.data['scrcase']



    ##各績效分數計算##
	// $percentTarget = Round((25/100),2);
	// $percentGroupTw = Round((25/100),2);
	// $percentGroupUnTw = Round((25/100),2);
	// $percentUse = 25;
	// $EffectiveGoal = 65;//基準趴數
	// $EffectiveBaseScore = 20;//基準趴數的基準分
	// $EffectivePlus = 1;//變動分數(基準趴數增減多少?趴)
	// $EffectivePlus2 = 1;//變動分數(基準趴數增減多少$EffectivePlus趴就扣?分)

	$percentTarget = Round(($percent['pSign']/100),2);//通路簽約數占比
	$percentGroupTw = Round(($percent['pGroupTW']/100),2);//成長率(台)
	$percentGroupUnTw = Round(($percent['pGroupUnTW']/100),2);//成長率(非台)
	$percentUse = $percent['pPercentUse'];//有效使用率
	$EffectiveGoal = $percent['pEffectiveGoal'];//基準趴數
	$EffectiveBaseScore = $percent['pEffectiveBaseScore'];//基準趴數的基準分
	$EffectivePlus = $percent['pEffectivePlus'];//變動分數(基準趴數增減多少?趴)
	$EffectivePlus2 = $percent['pEffectivePlus2'];//變動分數(基準趴數增減多少$EffectivePlus趴就扣?分)


	$seasontarget = ($season1[$sess]['target']*$percentTarget);//達成率
	// $seasongroupTW = (($season1[$sess]['groupTW']*$percentGroupTw) > $percent['pGroupTW'])? $percent['pGroupTW']:($season1[$sess]['groupTW']*$percentGroupTw);//成長率(台)
	// $seasongroupUnTW = (($season1[$sess]['groupUnTW']*$percentGroupUnTw) > $percent['pGroupUnTW'])? $percent['pGroupUnTW']:($season1[$sess]['groupUnTW']*$percentGroupUnTw);//成長率(非台)
	
	$seasongroupTW = $season1[$sess]['groupTW']*$percentGroupTw;
	$seasongroupUnTW = $season1[$sess]['groupUnTW']*$percentGroupUnTw;

	  	if ($eff1['effective'] > ($EffectiveGoal-$EffectiveBaseScore)) {//扣到沒有分數就是0
			$tmpScore = $eff1['effective'] - $EffectiveGoal;//相減後取得差數
			
			$tmpScore = (($tmpScore*$EffectivePlus2)/$EffectivePlus);
			$eff1['score'] = $EffectiveBaseScore+$tmpScore;//
		
		}else{
			$eff1['score'] = 0;
		}

		
		// if ($eff1['score'] > $percentUse) {
		// 	$eff1['score'] = $percentUse;
		// }
	##
	//總分
	$grade = $seasontarget+$seasongroupTW+$seasongroupUnTW+$eff1['score'];
	
}else{
	$script = '$("[name=\'excel\']").hide();';
		
}




##//成長率、有效使用率((該季)/去年該季)*100%[BY季] (本季與去年同季相比)
function getPercent107($sales, $year, $s, $last, $now, $type,$check,$show='') {
	$val = 0;
	// echo $sales.",".$year.",".$s.",".$last.",".$now.",".$type."<br>";
	// echo $year;
	if ($s == 1 && !$check) { //第一季要求出上一季
		$date_s = ($year-1).'-10-01'; 
		$date_e = ($year-1).'-12-31'; 
		// echo ($year-1);
		

		//上一年的第三季
		// $date_s2 = ($year-1).'-07-01 00:00:00'; 
		// $date_e2 = ($year-1).'-09-30 23:59:59'; 
		
		// $tmp = getOwnCase($sales,$date_s,$date_e);
		// $tmp2 = getOwnCase($sales,$date_s2,$date_e2);
		$tmp = getLastData($sales,$date_s,$date_e,$type);

		if($type == 'gTw'){
			$last = $tmp['tw'];//成長率

			
		}elseif($type == 'gUnTw'){
			$last = $tmp['other'];

			
		}

		
	}

	if ($last == 0) {
		$val = 0;

	}else{
		if ($show) {
			$val = round((($now/$last)-1)*100);
		}else{
			$val = round(($now/$last)*100);
		}
	}

	
	
	


	
	
	
	return $val;
}
##//成長率、有效使用率((該月)/去年該月)*100%[BY月] (本季與去年同季相比)
function getPercentMonth107($sales,$year,$month,$last,$now,$type,$check,$show='') {
	
	// echo $month;
	// echo $check;
	
	if ($month == 1 && !$check) {
		
		$date_s = ($year-1).'-12-01'; 
		$date_e = ($year-1).'-12-31'; 
		
		// $tmp = getOwnCase($sales,$date_s,$date_e);
		$tmp = getLastData($sales,$date_s,$date_e,$type);


		if($type == 'gTw'){
			$last = $tmp['tw'];//成長率

			
		}elseif($type == 'gUnTw'){
			$last = $tmp['other'];

			
		}


	}
	
	

	if ($last == 0) {
		$val = 0;

	}else{
		if ($show) {
			$val = round((($now/$last)-1)*100);
		}else{
			$val = round((($now/$last))*100);
		}
		
	}

	
	return $val;
}
//未滿一年撈取查詢年前一季用(限查詢年第一季使用)
function getLastData($sales,$sDate,$eDate,$type){

	global $conn;

	$sql = "SELECT SUM(sCaseTwQuantity) AS sCaseTwQuantity,SUM(sCaseUnTwQuantity) AS sCaseUnTwQuantity FROM tSalesReport WHERE sSales = '".$sales."' AND sDate >= '".$sDate."' AND sDate <= '".$eDate."'";
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);

	$tmp['tw'] = $rs->fields['sCaseTwQuantity'];
	$tmp['other'] = $rs->fields['sCaseUnTwQuantity'];

	return $tmp;

}

function getScrivener107($id){
	global $conn;

	// print_r($id);
	if ($id) {
		$sql = "SELECT
				s.sName,
				s.sOffice,
				s.sCreat_time,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = s.sZip1) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = s.sZip1) AS area,
				s.sSales AS preSales,
				s.sId
			FROM
				tScrivener AS s
			WHERE
				s.sId IN(".@implode(',', $id).")
				
				ORDER BY s.sId ASC";
	// echo $sql."<bR>";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$rs->fields['sCreat_time'] = DateChange($rs->fields['sCreat_time']);
			$tmp[] = $rs->fields;

			$rs->MoveNext();
		}
	}

	

	return $tmp;
}

function getBranch107($id){
	global $conn;

	if ($id) {
		$sql = "SELECT
				
				(SELECT bName FROM tBrand AS br WHERE br.bId = b.bBrand) AS brand,
				b.bOldStoreID,
				b.bStore,
				bName,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip = b.bZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip = b.bZip) AS area,
				b.bCashierOrderMemo,
				b.bId,
				b.bCreat_time,
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode
			FROM
				tBranch AS b 
			WHERE
				b.bId IN(".@implode(',', $id).")
			ORDER BY b.bId ASC";

		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			$rs->fields['bCreat_time'] = DateChange($rs->fields['bCreat_time']);
			$tmp[] = $rs->fields;


			$rs->MoveNext();
		}
	}
	
	
	return $tmp;			
}

function getUnApplyLine($sales,$scrivener){ 
	global $conn;
 	

	if (is_array($scrivener)) {
		foreach ($scrivener as $k => $v) {

			
			if ($v['sSignDate2'] >= '2018-01-04') { //20180109 佩琪說1/4號簽進來的才要算LINE的部分  1/4前是以之前的算法
				$sql = "SELECT * FROM tLineAccount WHERE lStatus = 'Y' AND lTargetCode = 'SC".str_pad($v['sId'], 4,'0',STR_PAD_LEFT)."'";
				
				$rs = $conn->Execute($sql);
				

				if ($rs->RecordCount() == 0) {
					$score ++ ;
				}
			}
			// $sql= "SELECT sStore,COUNT(sStore) AS storeCount FROM tSalesSign WHERE sType = 2 AND sStore = '".$v['bId']."'";
			// $rs = $conn->Execute($sql);

			// if ($rs->fields['storeCount'] > 1) {
			// 	
			// }
			
		}
	}

	
	return $score;
}
//因為換地區所以互換
function getOtherSalesData($sales,$sDate,$eDate){

	global $conn;

	if ($sales == 38) {
		$sales = 42;
	}else if($sales == 42) {
		$sales = 38;
	}else{
		return false;
	}


	$sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$sDate."' AND sDate <= '".$eDate."' AND sSales ='".$sales."' ORDER BY sDate ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$data['twcount'] +=  $rs->fields['sCaseTwQuantity']; //台屋
		$data['othercount'] += $rs->fields['sCaseUnTwQuantity']; // 他牌+非仲介(非台屋)



		$rs->MoveNext();
	}


	return $data;

    
}
?>