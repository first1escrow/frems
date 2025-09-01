<?php

//計算總額並規畫表格
$max = count($arr) ;
$con = '序號,保證號碼,仲介店編號,仲介店名,賣方,買方,總價金,合約保證費,出款保證費,案件狀態日期,進案日期,實際點交日期,地政士姓名,標的物座落,狀態,業務'."\n" ;
$con = iconv('utf-8','big5',$con) ;
for ($i = 0 ; $i < $max ; $i ++) {
	$totalMoney += $arr[$i]['cTotalMoney'] ;
	$certifiedMoney += $arr[$i]['cCertifiedMoney'] ;
	$transMoney += $arr[$i]['tMoney'] ;
	
	$con .= ($i+1).',_'.$arr[$i]['cCertifiedId'].',' ;
	//$con .= $arr[$i]['bCode'].str_pad($arr[$i]['bId'],5,'0',STR_PAD_LEFT).',' ;
	$con .= $arr[$i]['bCode'].',' ;
	$con .= iconv('utf-8','big5',$arr[$i]['bStore']).',' ;
	$con .= iconv('utf-8','big5',$arr[$i]['owner']).',' ;
	$con .= iconv('utf-8','big5',$arr[$i]['buyer']).',' ;
	$con .= $arr[$i]['cTotalMoney'].',' ;

	$tmp = round($arr[$i]['cTotalMoney']*0.0006); //萬分之六
	$tmp2 = round($arr[$i]['cTotalMoney']*0.0006)*0.1;

	if(($tmp-$tmp2)>$arr[$i]['cCertifiedMoney']) //合約保證費 如果未達6/10000的合約保證費  在合約保證費的金額位置 加註星星 
	{
		$con .= '*'.$arr[$i]['cCertifiedMoney'].',' ;
	}else
	{
		$con .= $arr[$i]['cCertifiedMoney'].',' ;
	}

	
	$con .= $arr[$i]['tMoney'].',' ;
		
	if ($status=='3') {
		$con .= $arr[$i]['cEndDate'].',' ;
	}
	else {
		$con .= $arr[$i]['cSignDate'].',' ;
	}
	
	$con .= $arr[$i]['cApplyDate'].',' ;
	$con .= $arr[$i]['cFinishDate'].',' ;
	$con .= iconv('utf-8','big5',$arr[$i]['scrivener']).',' ;
	
	$zc = $arr[$i]['zCity'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/","",$arr[$i]['cAddr']) ;
	$zc = $arr[$i]['zArea'] ;
	$arr[$i]['cAddr'] = preg_replace("/$zc/","",$arr[$i]['cAddr']) ;
	$arr[$i]['cAddr'] = $arr[$i]['zCity'].$arr[$i]['zArea'].$arr[$i]['cAddr'] ;
	
	$arr[$i]['cAddr'] = str_replace(',', '、', $arr[$i]['cAddr']);

	$con .= iconv('utf-8','big5',$arr[$i]['cAddr']).',' ;
	$con .= iconv('utf-8','big5',$arr[$i]['status']).',' ;
	$con .= iconv('utf-8','big5',$arr[$i]['sales'])."\n" ;
}

// $con = $max.','.$totalMoney.','.$certifiedMoney.','.$transMoney."\n\n".$con ;
// $con = iconv('utf-8','big5','案件總筆數,買賣總價金額,合約總保證費金額,出款總保證費金額')."\n".$con ;

//CSV版	
header("Content-type: text/csv") ;
header("Content-Disposition: attachment; filename=SalesCase.csv") ;
header("Pragma: no-cache") ;
header("Expires: 0") ;

$fh = fopen("php://output","w") ;
fwrite($fh,$con) ;
fclose($fh) ;
exit ;
##
?>
