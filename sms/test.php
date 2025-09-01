<?php
include_once '../../openadodb.php';
include_once '../../sms/sms_function_manually.php' ;


$array = array('CH00756',
'TH00776',
'JS00790',
'JF00802',
'ER00812',
'TH00813',
'TH00820',
'TH00822',
'ED00826',
'SN00827',
'AA00830',
'TH00857',
'TH00859',
'NR00862',
'JF00872',
'JF00893',
'CT00897',
'JS00900',
'TH00902',
'TH00908',
'YC00924',
'TH00927',
'YC00928',
'PO00948',
'PO00953',
'JS00976',
'ER00982',
'ER00985',
'ER00986',
'ER00988',
'ER00989',
'CH00990',
'TH00991',
'JS00998',
'JS00999',
'ED01015',
'TH01018',
'CH01022',
'PO01041',
'TR01047',
'JS01051',
'CH01052',
'JS01059',
'TH01060',
'YC01068',
'TH01070',
'JF01071',
'TH01077',
'JS01078',
'AA01092',
'TH01101',
'AA01114',
'UV01115',
'ED01118',
'UM01126',
'AA01131',
'CT01139',
'CH01143',
'ER01145',
'CT01152',
'TH01157',
'CH01158',
'TH01162',
'TR01171',
'CT01194',
'NR01201',
'YC01206',
'ER01209',
'CH01232',
'CH01236',
'PO01241',
'AA01249',
'ER01254',
'JS01263',
'FS01267',
'TH01283',
'AA01290',
'TH01292',
'CH01297',
'CT01302',
'FS01306',
'TH01315',
'CT01318',
'JS01319',
'UV01328',
'CT01330',
'AA01336',
'FS01346',
'JS01347',
'UM01357',
'CT01364',
'ER01373',
'TH01375',
'CT01377',
'ER01384',
'TH01387',
'JS01397',
'CH01406',
'TR01430',
'TH01444',
'YC01451',
'PO01456',
'AA01457',
'TR01485',
'TH01489',
'CT01500',
'AA01503',
'TH01509',
'TH01531',
'TH01540',
'PO01570',
'UV01572',
'EC01575',
'YC01592',
'CI01604',
'TR01620',
'AA01629',
'YC01630',
'JS01632',
'AA01647',
'AA01661',
'CH01662',
'CH01663',
'AA01667',
'CH01677',
'TH01681',
'YC01684',
'AA01690',
'AA01693',
'AA01707',
'CH01715',
'YC01732',
'AA01733',
'SN01740',
'AA01757',
'TH01764',
'YC01765',
'AA01767',
'SN01771',
'TH01786',
'TH01790',
'AA01806',
'TH01809',
'TH01811',
'JF01822',
'GM01827',
'UM01829',
'TR01830',
'TH01836',
'UM01843',
'TH01844',
'TH01848',
'TR01874',
'AA01882',
'NR01891',
'CT01904',
'CH01910',
'PO01914',
'TH01917',
'AA01931',
'AA01937',
'AA01939',
'CH01952',
'NR01954',
'AA01961',
'UV01975',
'AA01976',
'TH01980',
'AA01984',
'JF01990',
'YC01993',
'UV02002',
'TH02004',
'CI02006',
'AA02009',
'TH02042',
'HS02047',
'AA02057',
'TH02063',
'TH02064',
'TH02071',
'AA02074',
'UM02077',
'JS02081',
'JS02085',
'TH02091',
'GH02099',
'CI02101',
'TH02108',
'AA02118',
'TH02130',
'UV02148',
'JS02157',
'TR02166',
'TH02173',
'CH02177',
'JS02180',
'HS02181',
'TH02183',
'TH02189',
'PO02206',
'ER02214',
'FE02220',
'TH02231',
'AA02236',
'CI02254',
'AA02261',
'AA02269',
'AA02274',
'AA02278',
'AA02286',
'AA02296',
'CI02298',
'CI02299',
'JS02308',
'TH02309',
'JS02315',
'CT02318',
'CI02321',
'ED02322',
'YC02334',
'TH02336',
'TH02338',
'TH02339',
'CH02343',
'AA02344',
'JS02345',
'AA02372',
'AA02375',
'TH02378',
'TH02380',
'AA02385',
'AA02388',
'FS02396',
'JS02398',
'NR02403',
'UV02423',
'AA02424',
'AA02432',
'CH02433',
'AA02438',
'NR02459',
'AA02461',
'ER02464',
'ER02470',
'PO02476',
'UM02477',
'AA02478',
'AA02489',
'AA02499',
'AA02502',
'AA02524',
'TH02526',
'TH02531',
'PO02536',
'UV02544',
'AA02545',
'NR02566',
'HS02571',
'CI02590',
'UM02603',
'NR02612',
'TH02619',
'TH02623',
'TH02631',
'TH02632',
'TH02634',
'TH02635',
'TH02641',
'AA02649',
'UV02650',
'HS02671',
'AA02672',
'TR02673',
'TH02675',
'YC02676',
'YC02683',
'YC02685',
'CT02690',
'TH02691',
'TH02698',
'FE02709',
'FE02712',
'FE02714',
'FE02715',
'FE02716',
'FE02717',
'UV02725',
'AA02729',
'TH02737',
'AA02743',
'ED02745',
'FS02749',
'CI02759',
'AA02761',
'UV02763',
'TH02768',
'TR02769',
'PO02783',
'UV02799',
'FE02807',
'AA02808',
'TH02810',
'FS02821',
'JF02823',
'NR02825',
'AA02830',
'TH02831',
'AA02835',
'CI02838',
'TH02839',
'CI02851',
'CI02858',
'TH02863',
'TH02865',
'TH02874',
'TH02876',
'AA02878',
'CI02881',
'TH02886',
'TH02889',
'NR02893',
'CI02894',
'CI02896',
'CI02906',
'UV02911',
'YC02928',
'ER02931',
'CI02940',
'CI02942',
'CI02948',
'AA02954',
'CI02956',
'AA02962',
'CT02970',
'AA02975',
'TH02980',
'TR02983',
'PO02986',
'ER02987',
'CI02991',
'AA02993',
'TH02996',
'UV02997',
'TH02999',
'CI03002',
'GH03004',
'FS03029',
'TH03035',
'TH03043',
'CI03049',
'CI03051',
'HS03056',
'AA03060',
'CI03061',
'CI03062',
'MS03074',
'TH03075',
'CI03079',
'PO03088',
'PO03089',
'CI03090',
'NR03092',
'TH03094',
'TR03098',
'FE03103',
'PO03108',
'TH03113',
'TH03116',
'TH03117',
'TH03118',
'CI03121',
'CI03128',
'TH03129',
'AA03132',
'FE03134',
'TH03136',
'TH03138',
'CI03148',
'FE03156',
'FS03161',
'CI03162',
'FE03165',
'JS03179',
'ED03180',
'TH03193',
'AA03203',
'AA03204',
'TH03208',
'FS03215',
'HS03216',
'SN03217',
'AA03219',
'JS03225',
'TR03229',
'PO03239',
'HS03252',
'AA03253',
'TH03262',
'CI03272',
'TH03273',
'CH03277',
'PO03278',
'TH03280',
'TH03281',
'GH03284',
'TH03287',
'AA03302',
'CH03311',
'CI03312',
'NR03316',
'GH03318',
'CI03328',
'AA03332',
'FS03335',
'AA03342',
'CH03344',
'CI03347',
'FE03350',
'CH03355',
'TH03358',
'TR03374',
'AA03378',
'UV03379',
'TH03383',
'TH03394',
'AA03396',
'CH03398',
'TH03401',
'ER03403',
'JS03408',
'AA03409',
'AA03421',
'TH03424',
'TH03427',
'JS03441',
'AA03453',
'ER03454',
'AA03458',
'DF03467',
'AA03469',
'AA03470',
'ER03472',
'AA03473',
'HS03480',
'UV03486',
'TH03490',
'CI03491',
'TH03493',
'CI03497',
'JS03501',
'UV03502',
'TH03503',
'TH03507',
'PO03508',
'CT03509',
'GH03512',
'AA03513',
'TH03514',
'AA03523',
'UV03528',
'GH03529',
'TH03530',
'TH03531',
'AA03534',
'AA03541',
'CI03544',
'CH03546',
'TR03553',
'NR03555',
'AA03558',
'CI03565',
'JS03571',
'GH03574',
'UV03577',
'JS03578',
'AA03579',
'ER03582',
'ER03584',
'PO03591',
'AA03593',
'CH03598',
'TH03599',
'UV03603',
'GH03613',
'YC03614',
'TH03615',
'UV03616',
'AA03619',
'TR03624',
'AA03631',
'AA03632',
'CT03638',
'TH03640',
'SN03641',
'TH03642',
'JS03655',
'TH03661',
'YC03664',
'AA03667',
'TH03670',
'TR03698',
'CH03701'
);

// foreach ($array as $key => $value) {
// 	$sql = "INSERT INTO tFeedBackSmsLog SET fCode = '".$value."',fDate = '2020-10-14 17:20'";
// 	$conn->Execute($sql);
// 	echo $sql.";\r\n";
// }


// die;
$str = "fCategory = 1 AND fDate >= '2020-10-14 17:00:00' ";

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

									// $_all[$a]['smsTxt'] = '親愛的客戶您好:109年第2季之回饋金報表已結算完成,請點下列網址至第一建經官方網站確認報表,並依請款辦法作業,謝謝。新E化回饋金結算流程之操作手冊,請至第一建經官網下載。'.$url."";
			$jsonArr['txt'] = '*更正*第一建經通知：109年第3季回饋金已結算,請點下列網址至第一建經官網確認,並依辦法請款;新E化回饋金結算操作手冊,可於登入官網後下載,謝謝。'.$url."\r\n";
			$jsonArr['name'] = $v['mName'];
			$jsonArr['mobile'] ='0937185661';
			$A = $sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);

			$jsonArr['mobile'] ='0922591797';
			$A = $sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);
			// print_r($A);
			print_r($jsonArr);
			die;
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