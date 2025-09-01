<?php


//字串編碼
Function enCrypt($str, $seed='first1app24602') {
	global $psiArr ;
	
	$encode = '' ;
	$rc = new Crypt_RC4 ;
	$rc->setKey($seed) ;
	$encode = $rc->encrypt($str) ;
	
	return $encode ;
}
##
//字串解碼
Function deCrypt($str, $seed='first1app24602') {
	global $psiArr ;
	
	$decode = '' ;
	$rc = new Crypt_RC4 ;
	$rc->setKey($seed) ;
	$decode = $rc->decrypt($str) ;
	
	return $decode ;
}
?>