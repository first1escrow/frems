<?php 
include('../../openadodb.php') ;
include_once '../../session_check.php' ;

//半形 <=> 全形
Function n_to_w($strs, $types = '0') {
	$nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
	);
	$wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
	);
 
	if ($types == '0') {		//半形轉全形
		// narrow to wide
		$strtmp = str_replace($nt, $wt, $strs);
	}
	else {						//全形轉半形
		// wide to narrow
		$strtmp = str_replace($wt, $nt, $strs);
	}
	return $strtmp;
}
##

//關鍵字樣板
$keywords = array (
	'扣繳稅款'=>'稅',
	'仲介服務費'=>'服務費',
	'賣方先動撥'=>'動撥',
	'調帳'=>'轉入',
	'代清償'=>'代償',
	'點交(結案)'=>array('地政士'=>'代書費','仲介'=>'服務費','賣方'=>'','買方'=>'','保證費'=>'')
) ;
##

$_account_id = $_REQUEST["ac"];  //專屬帳號

$sql = 'SELECT * FROM tBankTrans WHERE tMemo="'.$_account_id.'";' ;
$rs = $conn->Execute($sql) ;

//合約銀行資料
$tmp = substr($rs->fields['tVR_Code'],0,5) ;
$branch = '' ;

$sql = 'SELECT * FROM tContractBank WHERE cBankVR LIKE "'.$tmp.'%" ;' ;
$rs = $conn->Execute($sql) ;
$_account_name = $rs->fields['cTrustAccountName'] ;
$_account_no = $rs->fields['cBankTrustAccount'] ;
if ($rs->fields['cBankMain']=='807') {
	$branch = $rs->fields['cBranchFullName'] ;
}
$main = $rs->fields['cBankFullName'] ;

unset($rs) ;
unset($tmp) ;
##

// 買賣人資料
$sql1 = 'SELECT  A.cCertifiedId, A.cName as owner, A.cIdentifyId as o_ID, B.cName as buyer, B.cIdentifyId as b_ID FROM tContractOwner AS A, tContractBuyer AS B WHERE A.cCertifiedId=B.cCertifiedId AND A.cCertifiedId="'.$_account_id.'" ; ' ;
$rs1 = $conn->Execute($sql1) ;
// 總價金
$sql2 = 'SELECT * FROM tContractIncome WHERE cCertifiedId="'.$_account_id.'" ;' ;
$rs2 = $conn->Execute($sql2) ;
// 地址
$sql3 = 'SELECT * FROM tContractProperty AS A, tZipArea AS B WHERE A.cCertifiedId="'.$_account_id.'" AND A.cZip=B.zZip ;' ;
$rs3 = $conn->Execute($sql3) ;
// 仲介
// $sql4 = 'SELECT B.bName as comp, bStore, C.bName as brand FROM tContractRealestate AS A, tBranch AS B, tBrand AS C WHERE A.cCertifyId="'.$_account_id.'" AND A.cBranchNum=B.bId AND A.cBrand=C.bId;' ;
// $rs4 = $conn->Execute($sql4) ;
$sql4 = 'SELECT
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cr.cBranchNum3,
			cr.cServiceTarget,
			cr.cServiceTarget1,
			cr.cServiceTarget2,
			cr.cServiceTarget3,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS brand,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum) AS comp,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS store,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS brand1,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum1) AS comp1,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS store1,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS brand2,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum2) AS comp2,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS store2,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand3) AS brand3,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum3) AS comp3,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum3) AS store3
		 FROM
		 	tContractRealestate AS cr
		 WHERE cr.cCertifyId="'.$_account_id.'"';
$rs4 = $conn->Execute($sql4) ;

if ($rs4->fields['cServiceTarget'] == 1) { //1.買賣方、2.賣方、3.買方
	$rs4->fields['cServiceTarget'] = '買賣方';
}elseif ($rs4->fields['cServiceTarget'] == 2) {
	$rs4->fields['cServiceTarget'] = '賣方';
}elseif ($rs4->fields['cServiceTarget'] == 3) {
	$rs4->fields['cServiceTarget'] = '買方';
}

if ($rs4->fields['cServiceTarget1'] == 1) { //1.買賣方、2.賣方、3.買方
	$rs4->fields['cServiceTarget1'] = '買賣方';
}elseif ($rs4->fields['cServiceTarget1'] == 2) {
	$rs4->fields['cServiceTarget1'] = '賣方';
}elseif ($rs4->fields['cServiceTarget1'] == 3) {
	$rs4->fields['cServiceTarget1'] = '買方';
}

if ($rs4->fields['cServiceTarget2'] == 1) { //1.買賣方、2.賣方、3.買方
	$rs4->fields['cServiceTarget2'] = '買賣方';
}elseif ($rs4->fields['cServiceTarget2'] == 2) {
	$rs4->fields['cServiceTarget2'] = '賣方';
}elseif ($rs4->fields['cServiceTarget2'] == 3) {
	$rs4->fields['cServiceTarget2'] = '買方';
}

if ($rs4->fields['cServiceTarget3'] == 1) { //1.買賣方、2.賣方、3.買方
	$rs4->fields['cServiceTarget3'] = '買賣方';
}elseif ($rs4->fields['cServiceTarget3'] == 2) {
	$rs4->fields['cServiceTarget3'] = '賣方';
}elseif ($rs4->fields['cServiceTarget3'] == 3) {
	$rs4->fields['cServiceTarget3'] = '買方';
}

// 代書
$sql5 = 'SELECT sName, sOffice FROM tContractScrivener, tScrivener WHERE cScrivener=sId AND cCertifiedId="'.$_account_id.'"; ' ;
$rs5 = $conn->Execute($sql5) ;
// 帳戶餘額
$sql6 = 'SELECT cCaseMoney,cEscrowBankAccount FROM tContractCase WHERE cCertifiedId="'.$_account_id.'"; ' ;
$rs6 = $conn->Execute($sql6) ;
//

// 20181005
// 如果當該案件沒有配帳過"買方服務費"
// 但是卻出款買方服務費
// 改筆買服出款的金額欄位後面


$tranBuyer = 0;
$checkBuyerMoney = true;
$sql = 'SELECT * FROM tBankTrans WHERE tMemo="'.$_account_id.'" AND tBuyer > 0 AND tPayOk="2" ;' ;
// echo $sql."<br>";
$rs = $conn->Execute($sql);
$tranBuyer = $rs->RecordCount();

if ($tranBuyer > 0) {
	$sql = 'SELECT  `eBuyerMoney` FROM  `tExpense` WHERE eDepAccount = "00'.$rs6->fields['cEscrowBankAccount'].'" AND eBuyerMoney > 0';
	// echo $sql."<br>";
	$rs = $conn->Execute($sql);

	$checkBuyerMoney = ($rs->fields['eBuyerMoney'] > 0)?true:false;
	// echo $checkBuyerMoney;
}
// echo $checkBuyerMoney;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="layout-2.css" rel="stylesheet" type="text/css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<body>
<table width="800" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
    <td align="center" id="header h1"><h3><strong>出款確認單 </strong>
      <input type="button" name="button" id="button" value="確認列印" onclick="window.print();" />
      <input type="submit" name="button2" id="button2" value="關閉" onclick="window.close();"/>
    </h3></td>
  </tr>
  <tr>
    <td align="right">
        <?=$main.$branch?>&nbsp;
	</td>
  </tr>
  <tr>
    <td align="right"><table width="80%" border="0" cellpadding="1" cellspacing="1">
      <tr>
        <td width="42%" align="right">&nbsp;</td>
        <td width="10%">&nbsp;</td>
        <td width="24%">&nbsp;</td>
        <td width="24%">單號：<?php echo $_account_id;?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td> <fieldset>
        <legend>案件資料 :
        <br />
        </legend>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" id="table2">
          <tr>
            <td colspan="8"><strong>買賣總價金額：<?php echo number_format($rs2->fields["cTotalMoney"]);?> 元</strong></td>
          </tr>
          <tr>
            <td width="113"><strong>買受人：</strong></td>
            <td width="78">
			<?php
			echo $rs1->fields["buyer"];
			
			$_sql = 'SELECT * FROM tContractOthers WHERE cIdentity="1" AND cCertifiedId="'.$_account_id.'"; ' ;
			$_rsB = $conn->Execute($_sql) ;
			$bNum = $_rsB->RecordCount() ;
			
			if ($bNum > 0) {
				echo '等'.($bNum + 1).'人' ;
			}
			unset($_rsB) ;
			?>
			</td>
            <td width="89"><strong>統一編號：</strong></td>
            <td width="103"><?php echo $rs1->fields["b_ID"];?></td>
            <td width="106"><strong>出賣人：</strong></td>
            <td width="85">
			<?php
			echo $rs1->fields["owner"];
			
			$_sql = 'SELECT * FROM tContractOthers WHERE cIdentity="2" AND cCertifiedId="'.$_account_id.'"; ' ;
			$_rsO = $conn->Execute($_sql) ;
			$oNum = $_rsO->RecordCount() ;
			
			$oTaxId = '' ;
			$oNHIId = '' ;
			if ($oNum > 0) {
				echo '等'.($oNum + 1).'人' ;
				
				while (!$_rsO->EOF) {
					$id = $_rsO->fields['cIdentifyId'] ;
					
					//若為法人身分，則將ID填入稅款旗標ID中
					if (preg_match("/^[0-9]{8}$/",$id)) {
						$oTaxId = $id ;
					}
					##
					
					//若為自然人身分，則將ID填入NHI旗標ID中
					if (preg_match("/^\w{10}$/",$id)) {
						$oNHIId = $id ;
					}
					##
					
					unset($id) ;
					$_rsO->MoveNext() ;
				}
			}
			unset($_rsO) ;
			
			$id = $rs1->fields['o_ID'] ;
			
			//若無其他賣方符合法人身分，則檢查主賣方身分證字號是否符合法人身分
			if (($oTaxId == '') && (preg_match("/^[0-9]{8}$/",$id))) {
				$oTaxId = $id ;
			}
			##
			
			//若無其他賣方符合自然人身分，則檢查主賣方身分證字號是否符合自然人身分
			if (($oNHIId == '') && (preg_match("/^\w{10}$/",$id))) {
				$oNHIId = $id ;
			}
			##
			?>
			</td>
            <td width="103"><strong>統一編號:</strong></td>
            <td width="123"><?php echo $rs1->fields["o_ID"];?></td>
          </tr>
          <?php
          	$i=0;

          	while(!$rs3->EOF)
          	{ 
          		if ($i==0) {
          			$ad_title= '標的物地址：';
          		}else
          		{
          			$ad_title='';
          		}
          		?>
          	<tr>
	           	<td><strong><?php echo $ad_title; ?></strong></td>
	           	<td colspan="7">
				<?php
	          		 echo $rs3->fields["zCity"].$rs3->fields["zArea"].$rs3->fields["cAddr"];

	          		$rs3->MoveNext();
	          		$i++;
	            ?>
	          	</td>
            </tr>
          <?php
      		}
          ?>
          <tr>
            <td><strong>仲介單位：<span style="font-size:10px;">(<?=$rs4->fields["cServiceTarget"]?>)</span></strong></td>
            <td colspan="4"><?php echo $rs4->fields["brand"];?> <?php echo $rs4->fields["comp"];?> <?php echo $rs4->fields["store"];?></td>
            <td>&nbsp;</td>
            <td><strong>承辦代書：</strong></td>
            <td><?php echo $rs5->fields["sName"];?></td>
          </tr>
          <?php
          	if ($rs4->fields['cBranchNum1'] > 0) { ?>
    			<tr>
		            <td><strong><span style="font-size:10px;">(<?=$rs4->fields["cServiceTarget1"]?>)</span></strong></td>
		            <td colspan="7"><?php echo $rs4->fields["brand1"];?> <?php echo $rs4->fields["comp1"];?> <?php echo $rs4->fields["store1"];?></td>
		            
		        </tr>      		
    <?php  	}

    		if ($rs4->fields['cBranchNum2'] > 0) { ?>
				<tr>
		            <td><strong><span style="font-size:10px;">(<?=$rs4->fields["cServiceTarget2"]?>)</span></strong></td>
		            <td colspan="7"><?php echo $rs4->fields["brand2"];?> <?php echo $rs4->fields["comp2"];?> <?php echo $rs4->fields["store2"];?></td>
		            
		        </tr>  
  <?php		}

  			if ($rs4->fields['cBranchNum3'] > 0) { ?>
				<tr>
		            <td><strong><span style="font-size:10px;">(<?=$rs4->fields["cServiceTarget3"]?>)</span></strong></td>
		            <td colspan="7"><?php echo $rs4->fields["brand3"];?> <?php echo $rs4->fields["comp3"];?> <?php echo $rs4->fields["store3"];?></td>
		            
		        </tr> 
  		<?php	}

          ?>
        </table>
    </fieldset></td>
  </tr>
  <tr>
    <td><form action="" method="post" name="form1" id="form1">
      
      <fieldset>
        <legend>取款戶名、帳號及金額：</legend>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" id="table2">
          <tr>
            <td colspan="5"><!--<h2><strong>本案點交完成，請結清款項，俟匯款完成即解除保證責任</strong></h2>--></td>
            </tr>
          <tr>
            <td width="259"><strong>取款戶名：</strong></td>
            <td colspan="4"><?=$_account_name?></td>
            </tr>
          <tr>
            <td><strong>取款帳號：</strong></td>
            <td colspan="4"><?=$_account_no?></td>
          </tr>
          <?php
		  $sqlx = "select sum(tMoney) as M,tMoney from tBankTrans where tMemo='$_account_id' and tPayOk='2'";
		  $rsx = $conn->Execute($sqlx);
		  ?>
          <tr>
            <td><strong>本指示單取款總金額新台幣：</strong></td>
            <td colspan="4">
            	<?php echo NumtoStr($rsx->fields["M"]);?> ( <?php echo number_format($rsx->fields["M"]);?>元 )
            	<?php
            			if (!$checkBuyerMoney) { ?>
            				<font color="red">[買服未入款]</font>
            	<?php	}
            	?>
            </td>
          </tr>
          <tr>
            <td><strong>本次出款後預計帳戶餘額新台幣：</strong></td>
            <td colspan="4"><?php echo NumtoStr($rs6->fields["cCaseMoney"] - $rsx->fields["M"]);?> ( <?php echo number_format($rs6->fields["cCaseMoney"]-$rsx->fields["M"]);?>元 )</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="101">&nbsp;</td>
            <td width="104">&nbsp;</td>
            <td width="120">&nbsp;</td>
          </tr>
      </table>
        </fieldset>
      
</form></td>
  </tr>
  <tr>
    <td>
	<fieldset>
      <legend>出款項目及明細：</legend>
      <?php
	  $cmp = '' ;
	  $msg = '' ;
	  $checkB = 0;
	  $checkS = 0;

	  $sql = 'SELECT * FROM tBankTrans WHERE tMemo="'.$_account_id.'" AND tPayOk="2" ;' ;
	  $rs = $conn->Execute($sql);
	  while (!$rs->EOF) {
	  	if ($rs->fields['tKind'] == '仲介') {
	  		$checkB++;//檢查仲介數量
	  	}elseif($rs->fields['tKind'] == '地政士'){
	  		$checkS++;//檢查地政士數量
	  	}
	  	
	  	
	  	$rs->MoveNext();
	  }


	  $sql = 'SELECT * FROM tBankTrans WHERE tMemo="'.$_account_id.'" AND tPayOk="2" ;' ;
	  //echo $sql;
	  $rs = $conn->Execute($sql) ;
	   while( !$rs->EOF ) {
		    $bank3 = substr($rs->fields["tBankCode"],0,3) ;
			$bank4 = substr($rs->fields["tBankCode"],3,4) ;
			
			$sql6 = 'SELECT * FROM tBank WHERE bBank3="'.$bank3.'" AND bBank4="" LIMIT 1 ;' ;
			$rs6 = $conn->Execute($sql6) ;	
			$_bank_title = str_replace("　","",$rs6->fields["bBank4_name"]) ;
			$sql7 = 'SELECT * FROM tBank WHERE bBank3="'.$bank3.'" AND bBank4="'.$bank4.'" LIMIT 1 ;' ;	
			$rs7 = $conn->Execute($sql7) ;
			$_bank_cotitle = str_replace("　","",$rs7->fields["bBank4_name"]) ;
			//
			switch ($rs->fields["tCode"]){
				  case "01":
					// $_title = "聯行轉帳";
				  $_title = $rs->fields['tCode2'];
				  break;
				  case "02":
					$_title = "跨行代清償";
				  break;
				  case "03":
					$_title = "聯行代清償";
				  break;
				  case "04":
					$_title = "大額繳稅";
				  break;
				  case "05":
				  	$_title = $rs->fields['tCode2'];
					// $_title = "臨櫃開票";
				  break;
				  case "06":
					$_title = "利息";
				  break;
			  }
			//
			
			//比對關鍵字是否正確
			$_target = '出款項目' ;
			$patt = '' ;
			$fg = 0 ;
			$flag = 0 ;
			$_tObjKind = $rs->fields['tObjKind'] ;		//出款項目(選項)
			$_tTxt = $rs->fields['tTxt'] ;				//附言
			$_tKind = $rs->fields['tKind'] ;			//角色

			if ($_tObjKind=='點交(結案)') {
				$_target = '點交角色' ;
				$cmp = $_tObjKind ;
				if ((preg_match("/賣方/",$_tKind))||(preg_match("/買方/",$_tKind))) {		//買賣方
					if ($_tTxt!='') {
						$fg ++ ;
					}
				}
				else if (preg_match("/保證費/",$_tKind)) {		//保證費
					$_tTxt = n_to_w($_tTxt,1) ;
					if (!preg_match("/\d{9}/",$_tTxt)) {
						$fg ++ ;
					}
				}
				else {		//其他(地政士、仲介服務費)
					$patt = $keywords[$_tObjKind][$_tKind] ;
					if (!preg_match("/$patt/",$_tTxt)) {
						$fg ++ ;
					}
				}
			}
			else {
				$patt = $keywords[$_tObjKind] ;
	
				if (!preg_match("/$patt/",$_tTxt)) {
					$fg ++ ;
				}
			}

			if ($fg > 0) {
				$msg .= '\"'.$rs->fields['tObjKind'].'\" '.$_target.'與附言內容有差異!!\n' ;
				$msg .= '出款對象：'.$_tKind.'\n' ;
				$msg .= '出款金額：'.$rs->fields['tMoney'].'\n' ;
				$msg .= '出帳建檔日期：'.$rs->fields['tDate'].'\n' ;
				$msg .= '附言：'.$rs->fields['tTxt'].'\n\n' ;
			}
			##
			
			if ($checkB > 1 && $rs->fields['tKind'] == '仲介') {
				$font = 'font-weight:900;';
			}elseif ($checkS > 1  && $rs->fields['tKind'] == '地政士') {
				$font = 'font-weight:900;';
			}else{
				$font = '';
			}
			
	  ?>
		<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2" id="table2">
        <tr> 
          <td colspan="3" align="left">
          	<strong>
          		項目：<?php echo $rs->fields["tObjKind"];?>
          		<?php if ($rs->fields["tObjKind"] == '扣繳稅款') {?>
          				<font color="red">(增值稅：<?php echo number_format($rs2->fields['cAddedTaxMoney']);?>)</font>
          		<?php } ?><br />
          	</strong>
          </td>
        </tr>
        <tr>
          <td width="265" align="center"><strong>賣方解匯行</strong></td>
          <td width="181" align="center"><strong>金 額</strong></td>
          <td width="328" align="center"><strong>出帳建檔日期</strong></td>
        </tr>
        <tr>
          <td align="center" style="<?=$font?>"><?php echo $rs->fields["tBankCode"];?> / <?php echo trim($_bank_title)." ".$_bank_cotitle;?></td>
          <td align="center" style="<?=$font?>">NT$ <?php echo number_format($rs->fields["tMoney"]);?> 元</td>
          <td align="center" style="<?=$font?>"><?php echo $rs->fields["tDate"];?>&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><strong>戶 名</strong></td>
          <td align="center"><strong>帳 號</strong></td>
          <td align="center"><strong>附言</strong></td>
        </tr>
        <tr>
          <td align="center" style="<?=$font?>"><?php echo $rs->fields["tAccountName"];?>&nbsp;</td>
          <td align="center" style="<?=$font?>"><?php echo $rs->fields["tAccount"];?>&nbsp;</td>
          <td align="center" style="<?=$font?>"><?php echo $rs->fields["tTxt"];?>&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><strong>電郵</strong></td>
          <td align="center"><strong>傳真</strong></td>
          <td align="center"><strong>交易類別</strong></td>
        </tr>
        <tr>
          <td align="center" style="<?=$font?>"><?php echo $rs->fields["tEmail"];?>&nbsp;</td>
          <td align="center" style="<?=$font?>"><?php echo $rs->fields["tFax"];?>&nbsp;</td>
          <td align="center" style="<?=$font?>"><?php echo $_title;?>&nbsp;</td>
        </tr> 
        </table>
      <hr />
      <?php	    
	   $rs->MoveNext();
	  }
	  if ($cmp) {
			//當利息大於20000時之處置
			$sql = 'SELECT cInterest, bInterest, cTax, bTax, cNHITax, bNHITax FROM tChecklist WHERE cCertifiedId="'.$_account_id.'" ;' ;
			$_rs = $conn->Execute($sql) ;
			$interest = 0 ;
			$tax = 0 ;
			$NHItax = 0 ;
			$str = '' ;
			
			$interest = (int)$_rs->fields['cInterest'] + (int)$_rs->fields['bInterest'] + 1 - 1 ;
			$tax = (int)$_rs->fields['cTax'] + (int)$_rs->fields['bTax'] + 1 - 1 ;
			$NHItax = (int)$_rs->fields['cNHITax'] + (int)$_rs->fields['bNHITax'] + 1 - 1 ;
			
			//利息>=20,000時，出現提醒
			if ($interest >= 20000) {
				//賣方具有自然人身分時，出現提醒
				if ($oNHIId) {
					$str = '(賣方具有自然人身分!!)' ;
				}
				##
				
				$msg .= '注意!!\n利息 >= 20,000 元, 請確認 \"二代健保代扣\" 對象是否正確!!\n'.$str.'\n\n' ;
				$str = '' ;
			}
			##
			
			//當賣方具有法人身分時，出現提醒
			if (($tax > 0) && ($oTaxId)) {
				$msg .= '注意!!\n請確認 \"代扣利息所得\" 之扣款對象及金額是否正確!!\n(賣方具有法人身分!!)\n\n' ;
			}
			##
	  }
	  ?>
      
      
    </fieldset></td>
  </tr>
  <tr>
    <td></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right"><table width="100%" border="0">
      <tr>
        <td width="10%">核定</td>
        <td width="20%">&nbsp;</td>
        <td width="10%">審核</td>
        <td width="20%"><p>&nbsp;</p>
          <p>&nbsp;</p></td>
        <td width="10%">經辦</td>
        <td width="20%"><p>&nbsp;</p>
          <p>&nbsp;</p>
          <p>&nbsp;</p></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
<script type="text/javascript">
<?php
if ($msg) {
	echo 'alert("'.$msg.'") ;'."\n" ;
}
?>
</script>
<?php
function NumtoStr($num){
	$numc	="零,壹,貳,參,肆,伍,陸,柒,捌,玖";
	$unic	=",拾,佰,仟";
	$unic1	=" 元整,萬,億,兆,京";
	
	//$numc_arr	=split("," , $numc);
	$numc_arr	= explode("," , $numc);
	//$unic_arr	=split("," , $unic);
	$unic_arr	= explode("," , $unic);
	//$unic1_arr	=split("," , $unic1);
	$unic1_arr	= explode("," , $unic1);
	
	$i = str_replace(',','',$num);#取代逗號
	$c0 = 0;
	$str=array();
	do{
		$aa = 0;
		$c1 = 0;
		$s = "";
		#取最右邊四位數跑迴圈,不足四位就全取
		$lan=(strlen($i)>=4)?4:strlen($i);
		$j = substr($i, -$lan);
		while($j>0){
			$k = $j % 10;#取餘數
			if($k > 0){
				$aa = 1;
				$s = $numc_arr[$k] . $unic_arr[$c1] . $s ;
			}elseif ($k == 0){
				if($aa == 1)	$s = "0" . $s;
			}
			$j = intval($j / 10);#只取整數(商)
			$c1 += 1;
		}
		#轉成中文後丟入陣列,全部為零不加單位
		$str[$c0]=($s=='')?'':$s.$unic1_arr[$c0];
		#計算剩餘字串長度
		$count_len=strlen($i) - 4;
		$i=($count_len > 0 )?substr($i, 0, $count_len):'';

		$c0 += 1;
	}while($i!='');
	
	#組合陣列
	foreach($str as $v)	$string .= array_pop($str);

	#取代重複0->零
	$string=preg_replace('/0+/','零',$string);

	return $string;
}
?>