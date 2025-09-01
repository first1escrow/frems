<?php
include_once '../../openadodb.php';
include_once '../../sms/sms_function_manually.php' ;


// $array = array('SC0002','SC0003','SC0004','SC0006','SC0007','SC0014','SC0016','SC0019','SC0020','SC0021','SC0023','SC0024','SC0028','SC0056','SC0058','SC0077','SC0088','SC0093','SC0107','SC0114','SC0119','SC0123','SC0124','SC0133','SC0138','SC0142','SC0144','SC0145','SC0158','SC0163','SC0166','SC0167','SC0170','SC0177','SC0180','SC0185','SC0197','SC0198','SC0200','SC0205','SC0206','SC0208','SC0209','SC0210','SC0221','SC0223','SC0225','SC0227','SC0229','SC0230','SC0232','SC0234','SC0249','SC0250','SC0254','SC0260','SC0261','SC0269','SC0271','SC0273','SC0276','SC0288','SC0293','SC0295','SC0298','SC0301','SC0302','SC0306','SC0309','SC0311','SC0314','SC0317','SC0322','SC0323','SC0324','SC0328','SC0335','SC0338','SC0339','SC0344','SC0347','SC0349','SC0350','SC0351','SC0353','SC0363','SC0365','SC0368','SC0369','SC0378','SC0380','SC0381','SC0389','SC0399','SC0401','SC0408','SC0409','SC0413','SC0420','SC0422','SC0423','SC0430','SC0432','SC0433','SC0435','SC0436','SC0438','SC0449','SC0452','SC0453','SC0459','SC0461','SC0463','SC0464','SC0465','SC0475','SC0476','SC0481','SC0487','SC0488','SC0489','SC0491','SC0493','SC0498','SC0501','SC0503','SC0509','SC0515','SC0516','SC0517','SC0526','SC0534','SC0537','SC0543','SC0549','SC0551','SC0574','SC0580','SC0595','SC0597','SC0602','SC0605','SC0618','SC0623','SC0628','SC0633','SC0634','SC0638','SC0640','SC0642','SC0645','SC0652','SC0653','SC0662','SC0668','SC0671','SC0672','SC0673','SC0675','SC0679','SC0681','SC0683','SC0684','SC0688','SC0689','SC0691','SC0693','SC0701','SC0711','SC0715','SC0716','SC0720','SC0729','SC0735','SC0738','SC0740','SC0746','SC0752','SC0753','SC0760','SC0762','SC0764','SC0766','SC0768','SC0770','SC0773','SC0775','SC0780','SC0783','SC0788','SC0790','SC0795','SC0800','SC0806','SC0810','SC0813','SC0822','SC0828','SC0829','SC0843','SC0846','SC0847','SC0853','SC0854','SC0855','SC0856','SC0861','SC0862','SC0868','SC0871','SC0872','SC0874','SC0877','SC0878','SC0886','SC0893','SC0897','SC0900','SC0902','SC0903','SC0906','SC0907','SC0908','SC0910','SC0922','SC0937','SC0946','SC0952','SC0956','SC0957','SC0958','SC0959','SC0963','SC0965','SC0979','SC0982','SC0987','SC0988','SC0999','SC1013','SC1015','SC1016','SC1020','SC1026','SC1027','SC1028','SC1029','SC1031','SC1036','SC1037','SC1043','SC1044','SC1048','SC1050','SC1056','SC1057','SC1059','SC1062','SC1065','SC1067','SC1068','SC1080','SC1084','SC1087','SC1090','SC1100','SC1101','SC1111','SC1113','SC1114','SC1117','SC1118','SC1120','SC1125','SC1132','SC1133','SC1134','SC1138','SC1141','SC1144','SC1147','SC1150','SC1152','SC1155','SC1157','SC1160','SC1173','SC1174','SC1177','SC1178','SC1188','SC1193','SC1204','SC1208','SC1211','SC1213','SC1214','SC1217','SC1218','SC1219','SC1221','SC1226','SC1230','SC1244','SC1246','SC1249','SC1257','SC1260','SC1261','SC1265','SC1267','SC1268','SC1272','SC1273','SC1274','SC1276','SC1277','SC1279','SC1280','SC1281','SC1284','SC1288','SC1292','SC1294','SC1295','SC1297','SC1299','SC1303','SC1304','SC1305','SC1309','SC1314','SC1319','SC1323','SC1354','SC1374','SC1382','SC1385','SC1397','SC1398','SC1400','SC1401','SC1402','SC1404','SC1409','SC1412','SC1413','SC1416','SC1422','SC1426','SC1429','SC1435','SC1441','SC1449','SC1454','SC1456','SC1460','SC1461','SC1463','SC1480','SC1487','SC1492','SC1493','SC1496','SC1497','SC1499','SC1503','SC1509','SC1517','SC1522','SC1526','SC1529','SC1530','SC1531','SC1532','SC1547','SC1551','SC1554','SC1557','SC1569','SC1571','SC1575','SC1582','SC1583','SC1587','SC1596','SC1604','SC1607','SC1620','SC1624','SC1626','SC1629','SC1632','SC1634','SC1639','SC1644','SC1645','SC1647','SC1648','SC1649','SC1662','SC1665','SC1680','SC1683','SC1693','SC1697','SC1706','SC1714','SC1716','SC1717','SC1720','SC1721','SC1722','SC1728','SC1743','SC1750','SC1751','SC1754','SC1759','SC1764','SC1766','SC1771','SC1783','SC1788','SC1793','SC1803','SC1806','SC1807','SC1811','SC1814','SC1815','SC1816','SC1819','SC1824','SC1826','SC1830','SC1833','SC1837','SC1841','SC1849','SC1852','SC1854','SC1857','SC1860','SC1861','SC1863','SC1865','SC1878','SC1888','SC1892','SC1899','SC1900','SC1901','SC1904','SC1908','SC1912','SC1917','SC1929','SC1953');

// foreach ($array as $key => $value) {
// 	$sql = "INSERT INTO tFeedBackSmsLog SET fCode = '".$value."',fDate = '2020-10-15 18:00'";
// 	$conn->Execute($sql);
// 	echo $sql.";\r\n";
// }


// die;


$str = "fCategory = 1 AND fDate >= '2020-10-15 18:00' ";

$sql = "SELECT fCode FROM tFeedBackSmsLog WHERE ".$str;
// echo $sql;

// die;

$rs = $conn->Execute($sql);
$Data = array();
$noData = array();
	$i = 0;
while (!$rs->EOF) {
	
	$code = substr($rs->fields['fCode'], 0,2);

	$id = (int)substr($rs->fields['fCode'], 2);


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
			$Data[$i]['code'] = $rs->fields['fCode'];
			$i++;

			$rs2->MoveNext();
		}

	}else{
		
		$noData[] = $rs->fields;

	}
	
	$rs->MoveNext();
	
}
$Data2 = array();
foreach ($Data as $key => $value) {
	
	// $sql = "SELECT * FROM tSMS_Log WHERE tKind = '回饋金2' AND sSend_Time >= '2020-10-14 00:00' AND tTo = '".$value['mMobile']."'";
	
	// $rs = $conn->Execute($sql);

	// if ($rs->EOF) {
		
	// 	$noData2[] = $value;

	// }else{
	// 	$Data2[] = $value;
	// }

	$noData2[] = $value;

}

echo "總數".count($Data)."\r\n";

echo "發送數量".count($Data2)."\r\n";

echo "簡訊對象有缺".count($noData)."\r\n";

echo "未發出簡訊".count($noData2)."\r\n";
print_r($Data2);

$sms = new SMS_Gateway();
if (count($noData2) > 0) {
	// print_r($noData2);
	foreach ($noData2 as $k => $v) {
		

		if ($v['mMobile'] != '') {
			$jsonArr['mobile'] = $v['mMobile'];
			$jsonArr['code'] = $v['code'];
			$jsonArr['Time'] = date('Ymd');
			$url =  getShortUrl('https://escrow.first1.com.tw/login/page-price1.php?v='.enCrypt(json_encode($jsonArr)),enCrypt(json_encode($jsonArr)));

			$jsonArr['txt'] = '第一建經通知：109年第3季回饋金已結算,請點下列網址至第一建經官網確認,並依辦法請款;新E化回饋金結算操作手冊,可於登入官網後下載,謝謝。'.$url."\r\n";
			$jsonArr['name'] = $v['mName'];
			// $jsonArr['mobile'] ='0937185661';
			// $A = $sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);

			// $jsonArr['mobile'] ='0922591797';
			// $A = $sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);

			// $jsonArr['mobile'] ='0928590425';
			// $A = $sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);

			// $jsonArr['mobile'] ='0919200247';
			$A = $sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);

			print_r($jsonArr);


			// print_r($A);
			
			// die;
		}else{
			$noData3[] = $v;
		}
		
		unset($jsonArr);
	}
}


// print_r($noData3);
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
			echo 'A';
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