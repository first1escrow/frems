<?php
Function send_sms($mobile,$subjects,$txt){
	$api_kind = "APIRTRequest" ;
	$url = 'xsms.aptg.com.tw' ;
	$fp = fsockopen($url,80,$errno,$errstr,30) ;
	if (!$fp) {
		echo 'Could not open connection.';
		return 0 ;
	}
	else {
		$xmlpacket ='<soap-env:Envelope xmlns:soap-env=\'http://schemas.xmlsoap.org/soap/envelope/\'> 
			<soap-env:Header/> 
			<soap-env:Body> 
				<Request>
					<MDN>0980013768</MDN> 
					<UID>firstEng</UID> 
					<UPASS>87973222</UPASS> 
					<Subject>'.$subjects.'</Subject> 
					<Retry>Y</Retry>
					<AutoSplit>Y</AutoSplit><Message>'.$txt.'</Message> 
					<MDNList><MSISDN>'.$mobile.'</MSISDN></MDNList> 
				</Request> 
			</soap-env:Body> 
			</soap-env:Envelope>' ;
		
		$contentlength = strlen($xmlpacket);
			
		$out = "POST /XSMSAP/api/".$api_kind." HTTP/1.1\r\n";
		$out .= "Host: 210.200.219.138\r\n";
		$out .= "Connection: close\r\n";
		$out .= "Content-type: text/xml;charset=utf-8\r\n";
		$out .= "Content-length: $contentlength\r\n\r\n";
		$out .= "$xmlpacket";

		fwrite($fp, $out);
		$theOutput='';
		while (!feof($fp)) {
			$theOutput .= fgets($fp, 128);
		}
			
		fclose($fp);
		$res = $theOutput;
		
		preg_match("/<Reason>(.*)<\/Reason><Code>(.*)<\/Code><MDN>(.*)<\/MDN><TaskID>(.*)<\/TaskID><RtnDateTime>(.*)<\/RtnDateTime>/",$res,$_data) ;
		
		$reason = $_data[1] ;
		$code = $_data[2] ;
		$mdn = $_data[3] ;
		$tid = $_data[4] ;
		$RDT = $_data[5] ;

			
		if (trim($code)=="0") {
			//echo date("Y-m-d H:i:s")." SMS has been sent!!\n" ;
			return 1 ;
		}
		else {
			//echo date("Y-m-d H:i:s")." SMS falured...\n" ;
			return 2 ;
		}
	}
}
  
?>