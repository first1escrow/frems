<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;
header("Content-Type:text/html; charset=utf-8"); 
 
$data_case['cEscrowBankAccount'] = '60001090078074';//090125553 //090142659 //080422480
$cheque = array() ;
$income = array() ;
//取得銷帳檔入帳紀錄資料
$sql = '
	SELECT 
		*,
		(SELECT sName FROM tCategoryIncome WHERE sId = exp.eStatusRemark) eStatusRemarkName,
		(SELECT eId FROM tExpenseDetail WHERE eExpenseId=exp.id AND eCertifiedId="'.$id.'" LIMIT 1) as eId,
		(SELECT sName FROM tCategoryIncome WHERE sId=exp.eStatusRemark) object 
	FROM 
		tExpense AS exp 
	JOIN 
		tCategoryIncome AS inc ON exp.eStatusRemark = inc.sId 
	WHERE 
		eDepAccount = "00'.$data_case['cEscrowBankAccount'].'" AND ePayTitle <> "網路整批"
	ORDER BY 
		eTradeDate
	ASC ;' ;

$rs = $conn->Execute($sql)	;
$income_max = count($income) ;
$j = 0;
while (!$rs->EOF) {
	$income[$j] = $rs->fields;
	$income[$j]['match'] = 'x' ;
	$j++;
	$rs->MoveNext();
}


if (preg_match("/^60001/",$data_case['cEscrowBankAccount'])) {			//若為一銀的案件，則加入票據資料
	$sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount = "00'.$data_case['cEscrowBankAccount'].'" AND eTradeStatus IN(0,10,11,20,40,49) ORDER BY eTradeDate ASC; ' ;
			
	$rs =$conn->Execute($sql);
		
	$x = 0 ;
	while (!$rs->EOF) {
		$_cheque[$x] = $rs->fields;
		$_cheque[$x]['match'] = 'x' ;
		$x++;
		$rs->MoveNext();
	}

	if (is_array($_cheque)) {
		//檢核票據是否已兌現	 &&($income[$j]['eTradeStatus'] == '8')							//交易狀態為票據交易
		for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//票據交易(8)
			if ($_cheque[$x]['id'] == '29961') {
				echo "<pre>";
				// print_r($_cheque[$x]);
				// print_r($income);
			}
			for ($j = 0 ; $j < count($income) ; $j ++) {
				if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount'])		//保證號碼相同
					&&($income[$j]['eTradeCode'] == '1793')							//交易代碼為1793
					&&($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])				//支出金額相符
					&&($_cheque[$x]['eLender'] == $income[$j]['eLender'])			//收入金額相符
					&&($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])		//票據日期須小於銷帳日期
					&&($income[$j]['match'] == 'x' && $_cheque[$x]['match'] == 'x')) {								//銷帳紀錄須未被配對
				
					$income[$j]['match'] = '1' ;			//在銷帳紀錄中找到支票紀錄
					$_cheque[$x]['match'] = '1' ;			//在支票紀錄中找到銷帳紀錄
					$income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
					break ;
				}
			}
		}
	}

	// echo "<pre>";
	// print_r($_cheque);

	// die;

	
	if (is_array($_cheque)) {
		for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//正常交易(0)
			// echo "#############";
			// echo "<pre>";
			

			// if ($_cheque[$x]['id'] == '29961') {
			// 	echo "<pre>";
			// 	// print_r($_cheque[$x]);
			// 	print_r($income);
			// }

			for ($j = 0 ; $j < count($income) ; $j ++) {
				
				


				if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount'])		//保證號碼相同
					&&($income[$j]['eTradeCode'] == '1950')							//交易代碼為1950
					&&($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])				//支出金額相符
					&&($_cheque[$x]['eLender'] == $income[$j]['eLender'])			//收入金額相符
					&&($income[$j]['eTradeStatus'] == '0')							//交易狀態為票據交易
					&&($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])		//票據日期須小於銷帳日期
					&&($income[$j]['match'] == 'x' && $_cheque[$x]['match'] == 'x')) {								//銷帳紀錄須未被配對(重要)
						
					$income[$j]['match'] = '1' ;			//在銷帳紀錄中找到支票紀錄
					$_cheque[$x]['match'] = '1' ;			//在支票紀錄中找到銷帳紀錄
					$income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
					
					$income[$j]['match_id'] = $_cheque[$x]['id'];

					// echo $x."_".$j."<br>";
					// print_r(expression);
					// print_r(expression)
					break ;
				}
			}			
		}
	}
	
		
}
// echo "<pre>";
// print_r($_cheque);
// die;

if ($_cheque) {
	for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//將未標記之票據紀錄取出
	
		if ($_cheque[$x]['eTipDate'] !='' || ($_cheque[$x]['eCheckDate'] != '0000000' && $_cheque[$x]['eCheckDate'] != '')) { //如果是託收票  以到期日加一日為兌現日
						
			$_expire_date = tDate_check($_cheque[$x]['eCheckDate'],'ymd','b','-',1,1) ;	 //

		}else{
			$_expire_date = tDate_check($_cheque[$x]['eTradeDate'],'ymd','b','-',3,1) ;		//票據(預計)兌現時間
			
		}

		// echo $_expire_date."<bR>";
		
		// if ($_expire_date <= date("Y-m-d")) {											//若今日超過兌現時間，則不顯示
		// 	$_cheque[$x]['match'] = '1' ;
		// }

		if ($_cheque[$x]['match']=='x') {
			$cheque[$j] = $_cheque[$x] ;
			$cheque[$j]['cheque'] = '1' ;
							
			$j ++ ;
		}
		
	}

	unset($_cheque) ;
}


//合併顯示
$income_arr = array_merge($income,$cheque) ;
//排序
for ($j = 0 ; $j < count($income_arr) ; $j ++) {
	$arr[$j]['date'] = (substr($income_arr[$j]['eTradeDate'],0,3)+1911).'-'.substr($income_arr[$j]['eTradeDate'],3,2).'-'.substr($income_arr[$j]['eTradeDate'],5);

	$arr[$j]['income'] = substr($income_arr[$j]['eLender'],0,-2) + 1 - 1 ;
	$arr[$j]['outgoing'] = substr($income_arr[$j]['eDebit'],0,-2) + 1 - 1 ;

	if ($income_arr[$j]['cheque'] == 1) {
		$arr[$j]['detail'] = '支票'.$income_arr[$j]['ePayTitle'];
	}else if ($income_arr[$j]['eStatusRemark']=='0') {
		$arr[$j]['detail'] = $income_arr[$j]['ePayTitle'] ;
	}else {
		$arr[$j]['detail'] = $income_arr[$j]['object'] ;
	}


	//$arr[$j]['outgoing'] = '' ;
	// $arr[$j]['outgoing'] = $rs->fields['eDebit'] ;
	$arr[$j]['remark'] =$income_arr[$j]['eRemarkContent'] ;
	$arr[$j]['obj'] = '1' ;	// 1 表示為收入
	$arr[$j]['expId'] = $income_arr[$j]['id'] ;//入帳ID
	$arr[$j]['eId'] = $income_arr[$j]['eId'] ;
	$arr[$j]['eTradeStatus'] = $income_arr[$j]['eTradeStatus'] ;
	$arr[$j]['show'] = $income_arr[$j]['eShow'];
	$arr[$j]['cheque'] = $income_arr[$j]['cheque'] ;
	
	if ($income_arr[$j]['cheque'] == 1) {
		
		if (($income_arr[$j]['eCheckDate'] != '') && ($income_arr[$j]['eCheckDate'] != '0000000')) {
			$_tDate = tDate_check($income_arr[$j]['eCheckDate'],'md','b','/',1,0) ;
			$arr[$j]['remark'] = '<font color="red">※未兌現、預計'.$_tDate.'兌現。(NT$'.number_format(($arr[$j]['income'] + 1 - 1)).' 不可動用)</font>';
		}else {
			$arr[$j]['remark'] = '<font color="red">※未兌現、預計二日後兌現。(NT$'.number_format(($arr[$j]['income'] + 1 - 1)).' 不可動用)</font>';
		}
		$minus_money += $arr[$j]['income'] + 1 - 1 ;		//票據金額加總
	}
	//remark

}
$max = ($arr)?count($arr):0 ;
	

for($i = 0 ; $i < $max ; $i ++) {
		for ($j = 0 ; $j < $max-1 ; $j ++) {
			if ($arr[$j]['date'] > $arr[$j+1]['date']) {
				$tmp = $arr[$j] ;
				$arr[$j] = $arr[$j+1] ;
				$arr[$j+1] = $tmp ;
				unset($tmp) ;
			}
			else if ($arr[$j]['date'] == $arr[$j+1]['date']) {
				if (($arr[$j]['obj']=='2')&&($arr[$j+1]['obj']=='1')) {
					$tmp = $arr[$j] ;
					$arr[$j] = $arr[$j+1] ;
					$arr[$j+1] = $tmp ;
					unset($tmp) ;
				}
			}
		}
	}

// 建立帳務明細表格
for ($i = 0 ; $i < $max ; $i ++) {
	if ($i % 2 == 0) { $color = $colorIndex ; }
	else { $color = $colorIndex1 ; }


	$total += (int)$arr[$i]['income'] + 1 - 1 ;
	$total -= (int)$arr[$i]['outgoing'] + 1 - 1 ;

	$income = (int)$arr[$i]['income'] + 1 - 1 ;
	$outgoing = $arr[$i]['outgoing'] + 1 - 1 ;
	$expId = $arr[$i]['expId'] ;
	$incomeTotal += (int)$arr[$i]['income'] + 1 - 1 ;
	$outgoingTotal += (int)$arr[$i]['outgoing'] + 1 - 1 ;
	$tbl .= '
	<tr style="'.$color.';">
		<td>'.$arr[$i]['date'].'&nbsp;</td>
	' ;
		
	if ($arr[$i]['obj'] == '1') {
		$aa = '' ;
		$bb = '' ;
		if ($arr[$i]['eId']) {
			$aa = 'class="incomeDetail" ' ;
			$bb = '<span style="width:100%;color:red;font-weight:bold;">&nbsp;*</span>' ;
		}
		
		$correct = '' ;

		if ($arr[$i]['eTradeStatus'] == '9') $correct = '<span style="font-size:9pt;color:red;">(被沖正)</span>' ;
		$tbl .= '<td>
					<span style="float:left;">
					'.$arr[$i]['detail'].$correct.'<span style="font-size:9pt;color:red;">'.$arr[$i]['taishinSp'].'</span>&nbsp;
					</span>';
		if ($arr[$i]['cheque'] != 1) {
			$tbl .='<span style="font-size:9pt;float:right;">
						<a href="../inquire/expenseDetail.php?cid='.$id.'&eid='.$expId.'" class="iframe">(編輯)</a>
					</span>
				' ;
		}
		
		$tbl .= '</td>' ;
		$tbl .= '<td '.$aa.'id="'.$expId.'" style="text-align:right;">'.$bb.number_format($income).'&nbsp;</td>' ;
	}
	else {
		$tbl .= '<td>'.$arr[$i]['detail'].'<span style="font-size:9pt;color:red;">'.$arr[$i]['taishinSp'].'</span>&nbsp;</td>' ;
		$tbl .= '<td style="text-align:right;">'.number_format($income).'&nbsp;</td>' ;
	}
	
	$tbl .= '
		<td style="text-align:right;">'.number_format($outgoing).'&nbsp;</td>
		<td style="text-align:right;">'.number_format($total).'&nbsp;</td>
		<td>'.$arr[$i]['remark'].'&nbsp;</td>';
	if ($arr[$i]['show'] == 0) {
		$ck = 'checked=checked';
		
	}else{
		$ck = '';
		
	}

	// if ($arr[$i]['obj'] == '1') {//入帳
	// 	$tbl .= '<td style="text-align:center;"><input type="checkbox" name="web_show[]" value="exp_'.$arr[$i]['expId'].'" '.$ck.'></td>';	
	// }else{//出款
	// 	$tbl .= '<td style="text-align:center;"><input type="checkbox" name="web_show[]" value="tra_'.$arr[$i]['tran_id'].'" '.$ck.'></td>';
	// }	
	
		


	$tbl .= '</tr>
	' ;
}
echo "<table>";
echo $tbl;
echo "</table>";

function tDate_check($_date,$_dateForm='ymd',$_dateType='r',$_delimiter='',$_minus=0,$_sat=0) {     
  $_aDate[0] = (substr($_date,0,3) + 1911) ;
  $_aDate[1] = substr($_date,3,2) ;
  $_aDate[2] = substr($_date,5) ;

  //$_cheque_date = implode('-',$_tDate) ;
  
  //是否遇六日要延後(六延兩天、日延一天)
  if ($_sat == '1') {
    $_ss = 0 ;
    $_ss = date("w",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;
    if ($_ss == '0') {      //如果是星期日的話，則延後一天
      // if ($_minus < 0) {
      //  $_minus =  $_minus + $_minus + $_minus ;
      // }
      // else {
      //  $_minus = $_minus + $_minus ;
      // }  
      $weekend = 1;     
    }
    else if ($_ss == '6') {   //如果是星期六的話，則延後兩天
      // if ($_minus < 0) {
      //  $_minus = $_minus + $_minus ;
      // }
      // else {
      //  $_minus =  $_minus + $_minus + $_minus ;
      // }
      $weekend = 2;
    }
  }
  ##
  $_minus = $_minus+$weekend;//傳進來的日期必須加上遇到加日延後的日期
  $_t = date("Y-m-d",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;    //設定日期為 t+1 天
  unset($_aDate) ;

  $_aDate = explode('-',$_t) ;
  
  if ($_dateType=='r') {    //若要回覆日期格式為"民國"
    $_aDate[0] = $_aDate[0] - 1911 ;
  }
  else {            //若要回覆日期格式為"西元"
  
  }

  //決定回覆日期格式
  switch ($_dateForm) {
    case 'y':       //年
          return $_aDate[0] ;
          break ;
    case 'm':       //月
          return $_aDate[1] ;
          break ;
    case 'd':       //日
          return $_aDate[2] ;
          break ;
    case 'ym':        //年月
          return $_aDate[0].$_delimiter.$_aDate[1] ;
          break ;
    case 'md':        //月日
          return $_aDate[1].$_delimiter.$_aDate[2] ;
          break ;
    case 'ymd':       //年月日
          return $_aDate[0].$_delimiter.$_aDate[1].$_delimiter.$_aDate[2] ;
          break ;
    default:
          break ;
  }
  ##
}
?>