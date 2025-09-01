<?php
include_once dirname(dirname(dirname(__FILE__))).'/SFTP/Net/SFTP.php' ;
$username = "cu079004";
$password = "53549920michael";
$ip = '210.59.232.109';


define('NET_SFTP_LOGGING', NET_SFTP_LOG_COMPLEX);
		
$sftp = new NET_SFTP($ip) ; //210.59.232.109 //mft.firstbank.com.tw
		// $sftp = new NET_SFTP('218.32.3.95') ;
if (!$sftp->login($username, $password)) {
	// echo '一銀銷帳檔網路連線失敗!!('.date("Y-m-d/H:i:s").')';
	 linePushMsg('一銀銷帳檔網路連線失敗!!('.date("Y-m-d/H:i:s").')read_first') ;
	// echo 'QQQQ';
	exit;
}

$sftp->chdir('./Inbox/') ;
$sftp->get($_file, $local_file);

?>