<?php
//產生亂數英數字密碼 $ln = 密碼長度
Function genPwd($ln=8) {
	$pwd = '' ;
	for ($i = 0 ; $i < $ln ; $i ++) {
		srand() ;
		$ch = rand(0,9) ;
		$n = $ch % 7 ;
		
		switch ($n) {
			case '1':
					srand() ;
					$ch = strtoupper(chr(rand(65,90))) ;
					break ;
			case '3':
					srand() ;
					$ch = strtolower(chr(rand(97,122))) ;
					break ;
			default:
					srand() ;
					$ch = rand(0,9) ;
					break ;
		}
		
		$pwd .= $ch ;
	}
	
	return $pwd ;
}
##
?>