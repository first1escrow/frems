<?php
require_once dirname(__FILE__).'/rc4.php' ;

Class crypt extends Crypt_RC4 {
	var $link = 0 ;
    var $result = 0 ;
	
	public function __construct() {
		parent::__construct() ;
	}
	
	//取得金鑰
	private function getRC4Key() {
		$keys = file_get_contents(dirname(__FILE__).'/rc4keys.txt') ;
		return $keys ;
	}
	##
	
	//加密
	public function EnCode($str) {
		$this->setKey($this->getRC4Key()) ;
		
		return $this->encrypt($str) ;
	}
	##
	
	//解密
	public function DeCode($str) {
		$this->setKey($this->getRC4Key()) ;
		
		return $this->decrypt($str) ;
	}
	##
}

?>