<?php
include_once '../../openadodb.php';
include_once '../../sms/sms_function_manually.php';
require_once('../../bank/Classes/PHPExcel.php');
require_once('../../bank/Classes/PHPExcel/Writer/Excel2007.php');
require_once("../../bank/Classes/PHPExcel/IOFactory.php");
require_once("../../bank/Classes/PHPExcel/Reader/Excel5.php");

$sms = new SMS_Gateway();
# 設定檔案存放目錄位置
$xls = '../log/branch.xlsx';
##
//讀取 excel 檔案

$objReader = new PHPExcel_Reader_Excel2007(); 
$objReader->setReadDataOnly(true); 

//檔案名稱
$objPHPExcel = $objReader->load($xls); 
$currentSheet = $objPHPExcel->getSheet(0);//讀取第一個工作表(編號從 0 開始) 
$allLine = $currentSheet->getHighestRow() ;//取得總列數


$noData = array();
$list = array();
for($excel_line = 2;$excel_line<=$allLine;$excel_line++) {
	
	array_push($list, trim($currentSheet->getCell("A{$excel_line}")->getValue()));

}

$i=0;
foreach ($list as $k => $v) {
	$code = substr($v, 0,2) ;

	$id = (int)substr($v, 2);

	

	if ($code == 'SC') {
		$sql = "SELECT
		 					fs.fName AS mName,
		 					fs.fMobile AS mMobile,
		 					(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=fs.fTitle ) AS title,
		 					s.sOffice AS bStore,
		 					s.sName
	 					FROM
	 						tFeedBackStoreSms AS fs
	 					LEFT JOIN
	 						tScrivener AS s ON s.sId=fs.fStoreId
	 					WHERE
	 						fs.fType = 1 AND fs.fStoreId = '".$id."' AND fs.fDelete = 0";


	}else{
		$sql = "SELECT 
		 					fs.fName AS mName,
		 					fs.fMobile AS mMobile,
		 					(SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) AS brand,
		 					(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=fs.fTitle ) AS title,
				            b.bStore
	 					FROM
	 						tFeedBackStoreSms AS fs
	 					LEFT JOIN
	 						tBranch AS b ON b.bId = fs.fStoreId
	 					WHERE
	 						fs.fType = 2 AND fs.fStoreId = '".$id."' AND fs.fDelete = 0";
						
	}


	$rs2 = $conn->Execute($sql);

	if (!$rs2->EOF) {
		while (!$rs2->EOF) {
			$Data[$i] = $rs2->fields;
			$Data[$i]['code'] = $v;
			$i++;

			$rs2->MoveNext();
		}

	}else{
		
		$noData[] = $v;
	}
	
}



$Data2 = array();
foreach ($Data as $key => $value) {
	
	$sql = "SELECT * FROM tSMS_Log WHERE tKind = '回饋金2' AND sSend_Time >= '2021-04-14 00:00' AND tTo = '".$value['mMobile']."'";
	
	$rs = $conn->Execute($sql);

	if ($rs->EOF) {
		
		$noData2[] = $value;

	}else{
		$Data2[] = $value;
	}

}

echo "總數".count($Data)."\r\n";

echo "發送數量".count($Data2)."\r\n";

echo "簡訊對象有缺".count($noData)."\r\n";

echo "未發出簡訊".count($noData2)."\r\n";
print_r($noData2);

// foreach ($noData as $key => $value) {
// 	// print_r($value);
// 	echo $value."\r\n";
// 	// echo $value['code']."_".$value['mName']."\r\n";

// }
die;
// foreach ($noData2 as $key => $value) {
	
// 	echo $value['code']."_".$value['sName']."\r\n";

// }
// echo "<pre>";
// print_r($noData);
// print_r($noData2);
die;

if (count($noData2) > 0) {
	// print_r($noData2);
	foreach ($noData2 as $k => $v) {
		

		if ($v['mMobile'] != '') {
			$jsonArr['mobile'] = $v['mMobile'];
			$jsonArr['code'] = $v['code'];
			$jsonArr['Time'] = date('Ymd');
			$url =  getShortUrl('https://escrow.first1.com.tw/login/page-price1.php?v='.enCrypt(json_encode($jsonArr)),enCrypt(json_encode($jsonArr)));

			$jsonArr['txt'] = '第一建經通知：110年第1季回饋金已結算,請點下列網址至第一建經官網確認,並依辦法請款;新E化回饋金結算操作手冊,可於登入官網後下載,謝謝。'.$url."\r\n";
			$jsonArr['name'] = $v['mName'];
			print_r($v);

			if ($jsonArr['code'] == 'SC1793' || $jsonArr['code'] == 'YC03664') {
				// $sms->manual_send("0928590425",$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);
				// $sms->manual_send("0922591797",$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);
				// $sms->manual_send("0919200247",$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);
				// $sms->manual_send("0937185661",$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);
				// print_r($jsonArr);
				// die;
			}
			

			//$sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);
			// print_r($jsonArr);
			
			
		}
		
		unset($jsonArr);
	}
}

die;


// 
function enCrypt($str, $seed='firstfeedSms') {
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
					return $url;
				}

			

		}


		
		

		
	}
?>