<?php
Function rec_str($str) {
	$str = trim(addslashes($str)) ;
	return $str ;
}

Function gaps() {
	//$gap = array() ;
	$gap = array(0=>'2',1=>'5',2=>'7',3=>'3') ;
	return $gap ;
}

Function encode_no($no) {
	$no += 2 ;
	$no = $no * 4 / 2 ;
	
	return $no ;
}

Function decode_no($no) {
	$no = $no * 2 / 4 ;
	$no -= 2 ;
	
	return $no ;
}

Function encode_str($str,$no=4) {
	$string = '1234567890abcdefghijklmnopqrstuvwxyz=ABCDEFGHIJKLNNOPQRSTUVWXYZ' ;
	$str_encode = '' ;
	
	$mx = strlen($string) - 1 ;
	for ($z = 0 ; $z < $no ; $z ++) {
		$str_encode .= $string[rand(1,$mx)] ;
	}
	
	$str = base64_encode($str) ;
	$str_encode .= $str ;
	
	return $str_encode ;
}

Function decode_str($str,$no=4) {
	$str = substr($str,$no) ;
	$str = base64_decode($str) ;
	
	return $str ;
}

Function show_range($max,$record_limit,$current_page,$total_page) {
	# 設定目前頁數顯示範圍
	if ($current_page) {
		if ($current_page >= ($max / $record_limit)) {
			if ($max % $record_limit == 0) {
				$current_page = floor($max / $record_limit) ;
			}
			else {
				$current_page = floor($max / $record_limit) + 1 ;
			}
		}
		$i_end = $current_page * $record_limit ;
		$i_begin = $i_end - $record_limit ;
		if ($i_end > $max) {
			$i_end = $max ;
		}
		if($i_end > $max) { $i_end = $max ; }
	}
	else {
		$i_end = $record_limit ;
		if($i_end > $max) { $i_end = $max ; }
		$i_begin = 0 ;
		$current_page = 1 ;
	}
	##	
	
	# 計算總頁數
	if (!$total_page) {
		if (($max % $record_limit) == 0) {
			$total_page = $max / $record_limit ;
		}
		else {
			$total_page = floor($max / $record_limit) + 1 ;
		}
	}
	##
	return array($i_begin,$i_end,$current_page,$total_page) ;
}

?>