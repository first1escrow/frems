<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once '../openadodb.php' ;


$id = trim($_POST['id']);
// $id = '020001139';

$contract = new Contract();
$data_case = $contract->GetContract($id);



//表格顏色區分
if ((($cindex + 1) % 2) == 1) {
	$colorIndex = 'background-color:#FFFFFF;' ;
	$colorIndex1 = '' ;
}
else {
	$colorIndex = '' ;
	$colorIndex1 = 'background-color:#FFFFFF;' ;
}
##
##票據部分##
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


//取得保證號碼之所有交換票據資料
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
	//檢核票據是否已兌現	
	for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//票據交易(8)
		for ($j = 0 ; $j < count($income) ; $j ++) {
			if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount'])		//保證號碼相同
				&&($income[$j]['eTradeCode'] == '1950')							//交易代碼為1950
				&&($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])				//支出金額相符
				&&($_cheque[$x]['eLender'] == $income[$j]['eLender'])			//收入金額相符
				&&($income[$j]['eTradeStatus'] == '8')							//交易狀態為票據交易
				&&($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])		//票據日期須小於銷帳日期
				&&($income[$j]['match'] == 'x')) {								//銷帳紀錄須未被配對
			
				$income[$j]['match'] = '1' ;			//在銷帳紀錄中找到支票紀錄
				$_cheque[$x]['match'] = '1' ;			//在支票紀錄中找到銷帳紀錄
				$income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
				break ;
			}
		}
	}
	
	for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//正常交易(0)
		for ($j = 0 ; $j < count($income) ; $j ++) {
			if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount'])		//保證號碼相同
				&&($income[$j]['eTradeCode'] == '1950')							//交易代碼為1950
				&&($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])				//支出金額相符
				&&($_cheque[$x]['eLender'] == $income[$j]['eLender'])			//收入金額相符
				&&($income[$j]['eTradeStatus'] == '0')							//交易狀態為票據交易
				&&($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])		//票據日期須小於銷帳日期
				&&($income[$j]['match'] == 'x')) {								//銷帳紀錄須未被配對(重要)
					
				$income[$j]['match'] = '1' ;			//在銷帳紀錄中找到支票紀錄
				$_cheque[$x]['match'] = '1' ;			//在支票紀錄中找到銷帳紀錄
				$income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
				break ;
			}
		}			
	}
		
}else if (preg_match("/^9998[56]0/",$data_case['cEscrowBankAccount'])) {	//若為永豐的案件，則加入票據資料
	//取得次交票紀錄(Time to Pay tickets)
	$Time2Pay = array() ;
			
	$sql = '
		SELECT 
			DISTINCT eCheckNo
		FROM 
			tExpense_cheque 
		WHERE 
			eDepAccount = "00'.$data_case['cEscrowBankAccount'].'" 
			AND eTradeStatus = "0" 
			AND eCheckDate = "0000000"
		ORDER BY 
			eDepAccount
		ASC; 
	' ;

	$rs =$conn->Execute($sql);
	while (!$rs->EOF){
		$tmp[] = $rs->fields;

		$rs->MoveNext();
	}

			
	$y = 0 ;
	for ($x = 0 ; $x < count($tmp) ; $x ++) {

				//依據支票號碼，取得最後一日次交票的保證號碼支票紀錄
				
				$sql = '
					SELECT 
						* 
					FROM 
						tExpense_cheque 
					WHERE 
						eDepAccount = "00'.$data_case['cEscrowBankAccount'].'" 
						AND eTradeStatus = "0"
						AND eCheckDate = "0000000"						
						AND eCheckNo = "'.$tmp[$x]['eCheckNo'].'"
					ORDER BY 
						eTradeDate
					DESC 
					LIMIT 1
				' ;
				// echo $sql;
				$rs =$conn->Execute($sql);

				$tmp2 = $rs->fields;
				
				
				if ($tmp2['eDepAccount'] !='') {
					$Time2Pay[$y] = $tmp2 ;
					$Time2Pay[$y]['match'] = 'x' ;
					$Time2Pay[$y++]['Time2Pay'] = '1' ;		//保留、顯示
				}
				##
		
		
	}

	unset($tmp) ;
			
	//取得託收票紀錄(Bills for Collection)
	$B4C = array() ;
			
	$sql = '
		SELECT 
			* 
		FROM 
			tExpense_cheque 
		WHERE 
			eDepAccount = "00'.$data_case['cEscrowBankAccount'].'"
			AND eTradeStatus = "0" 
			AND eCheckDate <> "0000000"
		ORDER BY 
			eDepAccount,eCheckDate 
		ASC
		; 
	' ;
	$rs = $conn->Execute($sql);

	$x = 0 ;
	while (!$rs->EOF) {
		$B4C[$x] = $rs->fields;
		$B4C[$x]['match'] = 'x' ;
		$B4C[$x]['Time2Pay'] = '1' ;		//保留、顯示
		$x++;
		$rs->MoveNext();
	}
			
			
	##

	//比對當相同紀錄出現時，剔除託收票紀錄
	for ($x = 0 ; $x < count($Time2Pay) ; $x ++) {
		for ($y = 0 ; $y < count($B4C) ; $y ++) {
			if ($Time2Pay[$x]['eDepAccount'] == $B4C[$y]['eDepAccount']
					&& $Time2Pay[$x]['eCheckNo'] == $B4C[$y]['eCheckNo']
					&& $Time2Pay[$x]['eDebit'] == $B4C[$y]['eDebit']
					&& $Time2Pay[$x]['eLender'] == $B4C[$y]['eLender']) {
				$B4C[$y]['Time2Pay'] = '2' ;	//剔除、不顯示
			}
		}
	}
	$B4C = array_merge($B4C,$Time2Pay) ;
	unset($Time2Pay) ;
			
	$y = 0 ;
	for ($x = 0 ; $x < count($B4C) ; $x ++) {
		if ($B4C[$x]['Time2Pay'] == '1') {		//僅取出保留的票據資料
			$_cheque[$y++] = $B4C[$x] ;
		}
	}
	unset($B4C) ;

	//檢核票據是否已兌現
	for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//票據交易
		for ($j = 0 ; $j < count($income) ; $j ++) {

			

			if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount'])		//保證號碼相同
				&&($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])				//支出金額相符
				&&($_cheque[$x]['eLender'] == $income[$j]['eLender'])			//收入金額相符
				&&($income[$j]['eSummary'] == '票據轉入')						//交易摘要為票據轉入
				&&($_cheque[$x]['eCheckNo'] == $income[$j]['eCheckNo'])			//支票號碼相同
				&&($income[$j]['match'] == 'x')) {								//銷帳紀錄須未被配對
					
				$income[$j]['match'] = '1' ;			//在銷帳紀錄中找到支票紀錄
				$_cheque[$x]['match'] = '1' ;			//在支票紀錄中找到銷帳紀錄
				$income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
				break ;
			}
		}
	}
			##
}else if (preg_match("/^96988/",$data_case['cEscrowBankAccount'])) {		//若為台新的案件，則加入票據資料
	$sql = 'SELECT * FROM tExpense_cheque WHERE eDepAccount = "00'.$data_case['cEscrowBankAccount'].'" AND eTradeStatus = "0" ORDER BY eTradeDate ASC; ' ;

	$rs = $conn->Execute($sql);
	$x = 0 ;
	while (!$rs->EOF) {
		$_cheque[$x] = $tmp;
		$_cheque[$x]['match'] = 'x' ;
		$x++;

		$rs->MoveNext();
	}	
	//檢核票據是否已兌現
	for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//正常交易(0)
		for ($j = 0 ; $j < count($income) ; $j ++) {
			if (($_cheque[$x]['eDepAccount'] == $income[$j]['eDepAccount'])		//保證號碼相同
				&&($income[$j]['eTradeCode'] == 'PDC')							//交易代碼為 PDC 票據交易
				&&($_cheque[$x]['eDebit'] == $income[$j]['eDebit'])				//支出金額相符
				&&($_cheque[$x]['eLender'] == $income[$j]['eLender'])			//收入金額相符
				&&($income[$j]['eTradeStatus'] == '0')							//交易狀態為票據交易
				&&($_cheque[$x]['eTradeDate'] < $income[$j]['eTradeDate'])		//票據日期須小於銷帳日期
				&&($income[$j]['match'] == 'x')) {								//銷帳紀錄須未被配對(重要)
					
				$income[$j]['match'] = '1' ;			//在銷帳紀錄中找到支票紀錄
				$_cheque[$x]['match'] = '1' ;			//在支票紀錄中找到銷帳紀錄
				$income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
				break ;
			}
		}			
	}
}


//取出未兌現支票據資料  (再這處李天數)
$j = 0 ;
for ($x = 0 ; $x < count($_cheque) ; $x ++) {		//將未標記之票據紀錄取出
	
	if ($_cheque[$x]['eTipDate'] !='' || $_cheque[$x]['eCheckDate'] != '0000000') { //如果是託收票  以到期日加一日為兌現日
					
		$_expire_date = tDate_check($_cheque[$x]['eCheckDate'],'ymd','b','-',1,1) ;	 //

	}else{
		$_expire_date = tDate_check($_cheque[$x]['eTradeDate'],'ymd','b','-',3,1) ;		//票據(預計)兌現時間
		
	}
	
	if ($_expire_date <= date("Y-m-d")) {											//若今日超過兌現時間，則不顯示
		$_cheque[$x]['match'] = '1' ;
	}

	if ($_cheque[$x]['match']=='x') {
		$cheque[$j] = $_cheque[$x] ;
		$cheque[$j]['cheque'] = '1' ;
						
		$j ++ ;
	}
	
}
unset($_cheque) ;
##

//合併顯示
$income_arr = array_merge($income,$cheque) ;
unset($income) ; unset($cheque) ;
##

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


##


// 支出部分
$sql_tra = '
SELECT 
	tBankLoansDate as tExport_time, 
	tObjKind,
	tKind, 
	tMoney, 
	tTxt,
	tId,
	tShow,
	tObjKind2Item,
	tBank_kind,
	tObjKind2
FROM 
	tBankTrans 
WHERE 
	tVR_Code="'.$data_case['cEscrowBankAccount'].'" 
ORDER BY 
	tExport_time 
ASC ;
' ;

$rs= $conn->Execute($sql_tra);
// $arr_tra[] = '' ;
while (!$rs->EOF) {
	//$arr[$j]['date'] = $arr_tra[$i]['tExport_time'] ;
	$arr[$j]['date'] = substr($rs->fields['tExport_time'],0,10) ;
	$arr[$j]['detail'] = $rs->fields['tObjKind'] ;
	$arr[$j]['income'] = '' ;
	$arr[$j]['outgoing'] = $rs->fields['tMoney'] ;
	$arr[$j]['remark'] = $rs->fields['tTxt'] ;
	$arr[$j]['obj'] = '2' ;	// 2 表示為支出
	$arr[$j]['tran_id'] = $rs->fields['tId'];//出款ID
	$arr[$j]['show'] = $rs->fields['tShow'];

	if ($rs->fields['tKind']=='保證費') {
		if ($data_case['cEscrowBankAccount']=='99985003081297') $cCertifyDate = '&nbsp;' ;	//2015-09-08 惠婷要求遮掉此案件的履保費出款日
		else $cCertifyDate = substr($rs->fields['tExport_time'],0,10) ;
	}else{
		$tmp = explode('-',$data_case['cBankList']) ;
		if ($data_case['cBankList'] != '') {
			$cCertifyDate = ($tmp[0] - 1911).'-'.str_pad($tmp[1],2,'0',STR_PAD_LEFT).'-'.str_pad($tmp[2],2,'0',STR_PAD_LEFT) ;
		}

		


		unset($tmp) ;
		
	}

	if ($rs->fields['tBank_kind'] == '台新' && $rs->fields['tObjKind']== '扣繳稅款' && $rs->fields['tObjKind2'] == '01') {
	
		$arr[$j]['taishinSp'] = ( $rs->fields['tObjKind2Item'] != '')?'已返還代墊款':'未返還代墊款';
		// echo $arr[$j]['taishinSp']	;
	}

	$j ++ ;

	$rs->MoveNext();
}
// echo "<pre>";
// print_r($arr);

// 氣泡排序
$max = count($arr) ;
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

	$total += $arr[$i]['income'] + 1 - 1 ;
	$total -= $arr[$i]['outgoing'] + 1 - 1 ;
	$income = $arr[$i]['income'] + 1 - 1 ;
	$outgoing = $arr[$i]['outgoing'] + 1 - 1 ;
	$expId = $arr[$i]['expId'] ;
	
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
		// if ($arr[$i]['eTradeStatus'] == '9') $correct = '<span style="font-size:9pt;color:red;">(被沖正)</span>' ;
		// $tbl .= '<td>
		// 			<span style="float:left;">
		// 			'.$arr[$i]['detail'].$correct.'&nbsp;
		// 			</span>
		// 			<span style="font-size:9pt;float:right;">
		// 				<a href="../inquire/expenseDetail.php?cid='.$id.'&eid='.$expId.'" class="iframe">(編輯)</a>
		// 			</span>
		// 		</td>' ;
		// $tbl .= '<td '.$aa.'id="'.$expId.'" style="text-align:right;">'.$bb.number_format($income).'&nbsp;</td>' ;
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
        if($arr[$i]['detail'] == '賣方先動撥'){
            $tbl .= '<td>' . $arr[$i]['detail'] . '<span style="font-size:9pt;color:red;">' . $arr[$i]['taishinSp'] . '<a href="javascript:open_confirm_call('.$id.','.$arr[$i]['tran_id'].')" style="font-size:9pt;">(照會)</a></span>&nbsp;</td>';
        } else {
            $tbl .= '<td>'.$arr[$i]['detail'].'<span style="font-size:9pt;color:red;">'.$arr[$i]['taishinSp'].'</span>&nbsp;</td>' ;
        }
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

if ($tbl == '') {
	$tbl = '
	<tr style="background-color:'.$colorIndex.';">
		<td colspan="6">尚無出入款紀錄!!</td>
	</tr>
	' ;
}

###
$smarty->assign('tbl',$tbl) ;
$smarty->assign('is_edit', 1);
$smarty->assign('total',number_format($total)) ;
$smarty->assign('data_case',$data_case);
$smarty->display('tran_table.inc.tpl', '', 'escrow');
?>