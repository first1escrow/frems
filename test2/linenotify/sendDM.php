<?php
#顯示錯誤
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php' ;
include_once '../../sms/sms_function_manually.php';
header("Content-Type:text/html; charset=utf-8"); 

$sms = new SMS_Gateway();

$sql = "SELECT zZip FROM tZipArea WHERE zCity IN('新北市','台北市')";
$rs = $conn->Execute($sql);
$zip = array();
while (!$rs->EOF) {
	
	array_push($zip, $rs->fields['zZip']);
	$rs->MoveNext();
}

$sql = "SELECT
			s.sId,
			s.sBrand,
			ss.sMobile,
			ss.sName
		FROM
			tScrivener AS s
		LEFT JOIN
			tScrivenerSms AS ss ON ss.sScrivener=s.sId
		WHERE
			s.sCpZip1 IN(".implode(',', $zip).") AND s.sStatus = 1 AND ss.sDel = 0 AND ss.sLock = 0 AND ss.sMobile != '' AND ss.sName NOT LIKE '%離職%' GROUP BY sMobile ORDER BY sId ASC";
// echo $sql;
$rs = $conn->Execute($sql);
$list = array();
while (!$rs->EOF) {

	$data = array();
	$data['code'] = 'SC'.str_pad($rs->fields['sId'], 4,0,STR_PAD_LEFT);
	$data['name'] = $rs->fields['sName'];
	$data['mobile'] = $rs->fields['sMobile'];
	$data['line'] = checkLine('S',$data['code'],$data['mobile']);
	$exp = explode(',', $rs->fields['sBrand']);

	if (in_array(1, $exp)) {
		$data['url'] = 'https://escrow.first1.com.tw/images/ads/fubon/fubon_20211228_1.png';
	}
	$data['url2'] = 'https://escrow.first1.com.tw/images/ads/fubon/fubon_20211228_2.png';

	if ($data['line'] == '0') {
		
		if (in_array(1, $exp)) {
			$data['short_url'] = 'https://first.pse.is/3urt6g';
		}
		$data['short_url2'] = 'https://first.pse.is/3va9wv';
		
	}


	// print_r($data);
	unset($exp);
	array_push($list, $data);

	$rs->MoveNext();
}

$sql = "SELECT
			b.bId,
			(SELECT bCode FROM tBrand WHERE bId =b.bBrand) AS code,
			bs.bName,
			bs.bMobile
		FROM
			tBranch AS b
		LEFT JOIN
			tBranchSms AS bs ON bs.bBranch=b.bId
		WHERE
			b.bZip IN (".implode(',', $zip).") AND b.bStatus = 1 AND bs.bDel = 0 AND bs.bNID IN(12,13) AND bs.bMobile != '' AND b.bBrand = 1 AND bs.bName NOT LIKE '%秘書%'
		";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {

	$data = array();
	$data['code'] = $rs->fields['code'].str_pad($rs->fields['bId'], 5,0,STR_PAD_LEFT);
	$data['name'] = $rs->fields['bName'];
	$data['mobile'] = $rs->fields['bMobile'];
	$data['line'] = checkLine('R',$data['code'],$data['mobile']);
	
	
	$data['url'] = 'https://escrow.first1.com.tw/images/ads/fubon/fubon_20211228_1.png';
	
	$data['url2'] = 'https://escrow.first1.com.tw/images/ads/fubon/fubon_20211228_2.png';

	if ($data['line'] == '0') {
		
		
		$data['short_url'] = 'https://first.pse.is/3urt6g';
		
		$data['short_url2'] = 'https://first.pse.is/3va9wv';
		
	}
	
	
	
	array_push($list, $data);

	$rs->MoveNext();
}

$i = 0;
$fw = fopen('/var/www/html/first.twhg.com.tw/test2/log/sendDM.txt', 'a+');
$countLine = 0;
$countSms = 0;
foreach ($list as $value) {
	$txt = '第一建經服務通知:台北富邦優惠房貸專案';

	print_r($value);
	fwrite($fw, "##########################\r\n");
	fwrite($fw, json_encode($value)."\r\n");
	
	if ($value['line'] == '0') {
		
		$txt2 = $txt."\r\n";
		if ($value['short_url']) {
			$txt2 .= $value['short_url']."\r\n";
			
		}

		if ($value['short_url2']) {
			$txt2 .= $value['short_url2']."\r\n";
			

		}

		if ($txt2) {
			echo $txt2."\r\n";
			$sms->manual_send($value['mobile'],$txt2,"y",'','富邦DM',$value['name']);
			fwrite($fw, $txt2."\r\n");
			$countSms++;
		}
		
		unset($txt2);
		
	}else{
		// $value['line'] = 'U4b14569b842b0d5d4613b77b94af02b6';
		
	
		$url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=".$value['line']."&txt=".urlencode($txt); //文字
		file_get_contents($url);
		echo 'urlTxt:'.$url."\r\n";
		fwrite($fw, $url."\r\n");
		$countLine++;
		if ($value['url']) {
			$url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=".$value['line']."&img=".$value['url']; //圖片
			echo 'urlImg:'.$url."\r\n";
			file_get_contents($url);
			$countLine++;
		}
		
		if ($value['url2']) {
			$url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=".$value['line']."&img=".$value['url2']; //圖片
			echo 'urlImg2:'.$url."\r\n";
			file_get_contents($url);
			$countLine++;

		}

		

		
	}

	fwrite($fw, "##########################\r\n");
	

}
fclose($fw);
echo '對象:'.count($list)."_Line:".$countLine."_簡訊:".$countSms;

// print_r($list);

function checkLine($cat,$target,$mobile){
	global $conn;

	$sql=  "SELECT lLineId FROM tLineAccount WHERE lIdentity = '".$cat."' AND lTargetCode = '".$target."' AND lCaseMobile = '".$mobile."'";

	$rs = $conn->Execute($sql);

	if (!$rs->EOF) {
		return $rs->fields['lLineId'];
	}else{
		return '0';
	}
	

}
function enCrypt($str, $seed='dm') {
		$encode = '' ;
		$rc = new Crypt_RC4 ;
		$rc->setKey($seed) ;
		$encode = $rc->encrypt($str) ;
		
		return $encode ;
}
function getShortUrl($url,$key){
	global $conn;
		$sql = "SELECT * FROM tShortUrl WHERE sCategory = '0' AND sKey = '".$key."'";
		$rs = $conn->Execute($sql);
		
		$ShortUrlData = $rs->fields ;

		if ($ShortUrlData['sShortUrl'] != '') {
			// echo 'A';
			return $ShortUrlData['sShortUrl'];
		}else{
			$target = "https://escrow.first1.com.tw/url/url.php";
			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $target);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("url"=>$url))); 
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch); 
				curl_close($ch);
				$data = json_decode($result,true);
				
				if ($data['code'] == 200) { //成功
					$sql = "INSERT INTO tShortUrl SET sCategory = '0',sKey = '".$key."',sUrl ='".$url."',sShortUrl = '".$data['url']."'";
					$conn->Execute($sql);

					return $data['url'];
				}else{ //失敗就走原本的
					return 'error';
				}

		}	

}
?>