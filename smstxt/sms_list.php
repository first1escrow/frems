<?php
require_once dirname(__DIR__).'/configs/config.class.php';
require_once dirname(__DIR__).'/class/SmartyMain.class.php';

Function file_get_contents_curl($url) {
	$ch = curl_init();
		
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
	curl_setopt($ch, CURLOPT_URL, $url);
		
	$data = curl_exec($ch);
	curl_close($ch);
		
	return $data;
}

if ($_SESSION['member_job'] != '1') {
    header('Location: http://' . $GLOBALS['DOMAIN']);
} 

// 指定日期範圍
$today = date("Ymd") ;
$yesterday = date("Ymd",mktime(0,0,0,date("m"),(date("d")-1),date("Y"))) ;

$start_date = $today ;
$end_date = $today ;

$dir = '../sms/log/' ;
$i = 0 ;
$arr[] = '' ;

// 搜尋符合日期之每個 Log 檔案並讀取
if ($dh = opendir($dir)) {
	while (($file = readdir($dh)) !== false) {
		if($file != '.' && $file != '..') {
			if (($file >= 'sms_'.$start_date.'.log') && ($file <= 'sms_'.$end_date.'.log')) {
				$fh = fopen($dir.$file,"r") ;
				$arr[$i] = fread($fh,filesize($dir.$file))."<br><br>" ;
				fclose($fh) ;
				$i ++ ;
			}
		}
	}
	closedir($dh) ;
}

// 從每個 Log 檔(每日)中分離出每通簡訊紀錄
$max = count($arr) ;
for ($i = 0 ; $i < $max ; $i ++) {
	$list[$i] = explode('===============================================================================================================',$arr[$i]) ;
}
unset($arr) ;

// 從每通簡訊中分離出需要的資訊(發訊日期、簡訊對象、簡訊手機號碼、保證號碼、msgid、狀態、經辦人員)
$max = count($list) ;
$count = 0 ;
$k = 0 ;
$msgid[] = '' ;
for ($i = 0 ; $i < $max ; $i ++) {
	for ($j = 0 ; $j < count($list[$i]) ; $j ++) {
		$tmp = explode('[',$list[$i][$j]) ;
		
		// 發訊日期
		$arr[$i][$j]['smsdate'] = ereg_replace(']=','',$tmp[3]) ;
		
		// 簡訊手機號碼
		$temp = explode(' ',$tmp[4]) ;
		$arr[$i][$j]['mobile'] = sprintf("%010d",$temp[0]) ;
		
		// 簡訊對象
		$arr[$i][$j]['smsobject'] = ereg_replace(']==','',$temp[1]) ;
		unset($temp) ;
		
		// 保證號碼
		$arr[$i][$j]['certifiedid'] = ereg_replace(']========','',$tmp[2]) ;	
		
		// msgid
		$temp = explode("\n",$tmp[6]) ;
		$arr[$i][$j]['msgid'] = substr($temp[1],6,10) ;
		unset($tmp) ; unset($temp) ;
		
		
		// 分批取出簡訊 msgid 以備反查
		
		if ($count < 100) {
			if ($msgid[$k]) { $msgid[$k] .= ',' ; }
			$msgid[$k] .= $arr[$i][$j]['msgid'] ;
			$count ++ ;
		}
		else {
			$k ++ ;
			$count = 0 ;
		}
	}
}
unset($list) ;

$k = 0 ;
for ($i = 0 ; $i < count($msgid) ; $i ++) {
	
	$msgid[$i] = ereg_replace("^,","",$msgid[$i]) ;
	$msgid[$i] = ereg_replace(",$","",$msgid[$i]) ;
	
	// 從三竹搜尋查出狀態與時間
	$url = 'http://smexpress.mitake.com.tw/SmQueryGet.asp?username=0921946427&password=first168&msgid='.$msgid[$i] ;
	
	$data = file_get_contents_curl($url) ;
	$data = iconv("big5","utf-8",$data);
	if (!$data) {
		$i -- ;
		goto skip ;
	}
	
	$tmp = explode("\n",$data) ;
	unset($data) ;
	
	for ($j = 0 ; $j < count($tmp) ; $j ++) {
		
		$temp = explode("\t",$tmp[$j]) ;
		switch ($temp[1]) {
			case '5' : $list[$k]['msgid'] = $temp[0] ;
						$list[$k]['datetime'] = $temp[2] ;
						$list[$k]['status'] = '內容有錯誤' ;
						$k ++ ;
						break ;
			case '6' : $list[$k]['msgid'] = $temp[0] ;
						$list[$k]['datetime'] = $temp[2] ;
						$list[$k]['status'] = '門號有錯誤' ;
						$k ++ ;
						break ;
			case '7' : $list[$k]['msgid'] = $temp[0] ;
						$list[$k]['datetime'] = $temp[2] ;
						$list[$k]['status'] = '簡訊已停用' ;
						$k ++ ;
						break ;
			case '8' : $list[$k]['msgid'] = $temp[0] ;
						$list[$k]['datetime'] = $temp[2] ;
						$list[$k]['status'] = '逾時無送達' ;
						$k ++ ;
						break ;
			case '9' : $list[$k]['msgid'] = $temp[0] ;
						$list[$k]['datetime'] = $temp[2] ;
						$list[$k]['status'] = '預約已取消' ;
						$k ++ ;
						break ;
			default : ;
		}
		unset($temp) ;
	}
	unset($tmp) ;
	skip:
}

for ($i = 0 ; $i < count($list) ; $i ++) {
	for($j = 0 ; $j < count($arr) ; $j ++) {
		for ($k = 0 ; $k < count($arr[$j]) ; $k ++) {
			if (ereg($list[$i]['msgid'],$arr[$j][$k]['msgid'])) {
				$list[$i]['smsdate'] = $arr[$j][$k]['smsdate'] ;
				$list[$i]['mobile'] = $arr[$j][$k]['mobile'] ;
				$list[$i]['smsobject'] = $arr[$j][$k]['smsobject'] ;
				$list[$i]['certifiedid'] = $arr[$j][$k]['certifiedid'] ;
			}
		}
	}
}
unset($arr) ;

require_once dirname(__DIR__).'/first1DB.php';

$conn = new first1DB;

$tbl = '' ;
for ($i = 0 ; $i < count($list) ; $i ++) {
	$sql = 'SELECT peo.pId pId, peo.pName pName FROM tContractCase AS cas JOIN tPeopleInfo AS peo ON cas.cUndertakerId=peo.pId WHERE cas.cCertifiedId="'.$list[$i]['certifiedid'].'";' ;
	$under = $conn->one($sql);

	$list[$i]['undertaker'] = $under['pName'] ;
	unset($under) ;
	
	$tmp[0] = substr($list[$i]['datetime'],0,4) ;
	$tmp[1] = substr($list[$i]['datetime'],4,2) ;
	$tmp[2] = substr($list[$i]['datetime'],6,2) ;
	$tmp[3] = substr($list[$i]['datetime'],8,2) ;
	$tmp[4] = substr($list[$i]['datetime'],10,2) ;
	$tmp[5] = substr($list[$i]['datetime'],12,2) ;
	
	$list[$i]['datetime'] = $tmp[0].'-'.$tmp[1].'-'.$tmp[2].' '.$tmp[3].':'.$tmp[4].':'.$tmp[5] ;
	unset($tmp) ;
	
	if ($i % 2 == 0) { $color_index = '#FFFFFF' ; }
	else { $color_index = '#F8ECE9' ; }
	
	$tbl .= '
	<tr>
		<td style="background-color='.$color_index.';">'.$list[$i]['certifiedid'].'</td>
		<td style="background-color='.$color_index.';">'.$list[$i]['msgid'].'</td>
		<td style="background-color='.$color_index.';">'.$list[$i]['smsdate'].'</td>
		<td style="background-color='.$color_index.';">'.$list[$i]['datetime'].'</td>
		<td style="background-color='.$color_index.';">'.$list[$i]['smsobject'].'</td>
		<td style="background-color='.$color_index.';">'.$list[$i]['mobile'].'</td>
		<td style="background-color='.$color_index.';">'.$list[$i]['status'].'</td>
		<td style="background-color='.$color_index.';">'.$list[$i]['undertaker'].'&nbsp;</td>
	</tr>
	' ;
}

$start_date = substr($start_date,0,4).'-'.substr($start_date,4,2).'-'.substr($start_date,6,2) ;
$end_date = substr($end_date,0,4).'-'.substr($end_date,4,2).'-'.substr($end_date,6,2) ;

$smarty->assign('start_date', $start_date);
$smarty->assign('end_date', $end_date);
$smarty->assign('tbl', $tbl);

$smarty->display('sms_list.inc.tpl', '', 'smstxt');
?> 
