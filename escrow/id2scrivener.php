<?php
include_once("../openadodb.php") ;
include_once '../session_check.php' ;

// 取得變數值
$scr = $_REQUEST['scr'] ;
$bank = $_REQUEST['bank'] ;
$ver = $_REQUEST['ver'] ;

##
$str='';

if(!empty($bank)){
	$str .= ' AND bAccount LIKE "'.$bank.'%"';
}

if(!empty($ver)){
	$str .= ' AND bBrand LIKE "'.$ver.'%"';
}

if (!empty($_REQUEST['date'])) {
	$tmp = explode('-', $_REQUEST['date']);
	$sDate = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2]." 00:00:00";
	$eDate = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2]." 23:59:59";
	$dateStr = ' AND bCreateDate >="'.$sDate.'" AND bCreateDate <="'.$eDate.'"';
}

//剩餘保證號碼總數
$sql = '
	SELECT 
		* 
	FROM 
		tBankCode 
	WHERE 
		bSID="'.$scr.'" 
		AND bDel="n"
		AND bUsed="0"
		'.$str.$dateStr.'
		 ;
' ;

$rs = $conn->Execute($sql) ;

$b1L = 0 ;		//加盟土地計數
$b1B = 0 ; 		//加盟建物計數
$b1S = 0 ;		//加盟預售
$b2L = 0 ;		//直營土地計數
$b2B = 0 ;		//直營建物計數
$b2S = 0 ;		//直營預售
$b3L = 0 ;		//非仲介成交土地計數
$b3B = 0 ;		//非仲介成交建物計數
$b3S = 0 ;		//加盟預售建物計數

while (!$rs->EOF) {
	if ($rs->fields['bCategory']=='1') {				//加盟保證號碼
		if ($rs->fields['bApplication']=='1') {				//土地
			$b1L ++ ;
		}
		else if ($rs->fields['bApplication']=='2') {		//建物
			$b1B ++ ;
		}elseif ($rs->fields['bApplication']=='3') { //預售屋
			$b1S ++ ;
		}
	}
	else if ($rs->fields['bCategory']=='2') {			//直營保證號碼
		if ($rs->fields['bApplication']=='1') {				//土地
			$b2L ++ ;
		}
		else if ($rs->fields['bApplication']=='2') {		//建物
			$b2B ++ ;
		}elseif ($rs->fields['bApplication']=='3') { //預售屋
			$b2S ++ ;
		}
	}
	else if ($rs->fields['bCategory']=='3') {	//非仲介成交保證號碼
		if ($rs->fields['bApplication']=='1') {				//土地
			$b3L ++ ;
		}
		else if ($rs->fields['bApplication']=='2') {		//建物
			$b3B ++ ;
		}elseif ($rs->fields['bApplication']=='3') { //預售屋
			$b3S ++ ;
		}
	}
	
	$rs->MoveNext() ;
}
unset($rs) ;
##

//舊版無法辨識版本保證號碼餘額
$sql = '
	SELECT 
		COUNT(bAccount) as unknow_no 
	FROM 
		tBankCode 
	WHERE 
		bSID="'.$scr.'" 
		AND (bBrand="" OR bCategory="" OR bApplication="")
		AND bDel="n"
		AND bUsed="0"
		AND bAccount LIKE "'.$bank.'%"
		'.$dateStr.' 
		;' ;
$rs = $conn->Execute($sql) ;
$unknow_no = $rs->fields['unknow_no'] + 1 - 1 ;
##

//取得代書姓名
$sql = 'SELECT sName FROM tScrivener WHERE sId="'.$scr.'";' ;
$rs = $conn->Execute($sql) ;
$scr_name = $rs->fields['sName'] ;
unset($rs) ;
##

//整理顯示內容
$scr_name = '
<div style="font-weight:bold;text-align:center;"><span style="color:blue;">'.$scr_name.'</span> 未使用保證號碼數量明細如下：</div>
<div style="height:20px;"></div>
<table border="0" width="95%">
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;width:60%;">加盟</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;width:20%;">土地</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;width:20%;">
			<a href="#" id="show" onClick="show_msg(1,1,1);">'.number_format($b1L).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">&nbsp;</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">建物</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(1,2,1);">'.number_format($b1B).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">&nbsp;</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">預售屋</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(1,3,1);">'.number_format($b1S).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">直營</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">土地</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(2,1,1);">'.number_format($b2L).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">&nbsp;</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">建物</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(2,2,1);">'.number_format($b2B).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">&nbsp;</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">預售屋</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(2,3,1);">'.number_format($b2S).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">非仲介成交</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">土地</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(3,1,1);">'.number_format($b3L).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">&nbsp;</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">建物</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(3,2,1);">'.number_format($b3B).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">&nbsp;</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">預售屋</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(3,3,1);">'.number_format($b3S).'</a>
		</td>		
	</tr>
	<tr>
		<td style="text-align:center;">&nbsp;</td>
		<td style="text-align:center;">&nbsp;</td>
		<td style="text-align:center;">&nbsp;</td>		
	</tr>
	<tr>
		<td style="text-align:center;">&nbsp;</td>
		<td style="text-align:center;">&nbsp;</td>
		<td style="text-align:center;">&nbsp;</td>		
	</tr>
	<tr>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">其他無法判別保證號碼數量</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">&nbsp;</td>
		<td style="text-align:center;border-width:1px;border-bottom-style:dotted;border-bottom-color:#CCCCCC;">
			<a href="#" id="show" onClick="show_msg(0,0,2);">'.number_format($unknow_no).'</a>
		</td>		
	</tr>
</table>
<div style="height:20px;"></div>
' ;
##

echo $scr_name ;
?>