<?php
//檢查排程是否已在執行中
Function checkPS($keys='', $cnt=3) {
	if ($keys) {
		$output = shell_exec('ps aux|grep "'.$keys.'"') ;
		//print_r($output) ;
		
		$matches = array() ;
		preg_match_all("/$keys/i",$output,$matches) ;

		if (count($matches[0]) > $cnt) return true ;
		else return false ;
	}
	else return false ;
}
##

//if (checkPS('hb.php')) echo "Founded!!\n" ;
//else echo "Not Found!!\n" ;
?>
