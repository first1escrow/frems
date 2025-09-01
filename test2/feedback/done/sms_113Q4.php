<?php
error_reporting(E_ALL ^ E_WARNING);

require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/sms/sms_function_manually.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Reader/Excel5.php';
require_once dirname(dirname(__DIR__)) . '/lineNotify.php';

//簡訊發送起始時間
$time = '2025-01-13 00:00:00';
//設定檔案存放目錄位置
$xls = __DIR__ . '/excel/113Q4.xlsx';
$sheet = 0; //季結
//讀取 excel 檔案
$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);
//檔案名稱
$objPHPExcel  = $objReader->load($xls);
$currentSheet = $objPHPExcel->getSheet($sheet); //讀取第一個工作表(編號從 0 開始)
$totalRows    = $currentSheet->getHighestRow(); //取得總列數

$Data = array();
$noData = array();
$list   = array();
for ($excelRows = 2; $excelRows <= $totalRows; $excelRows++ ) {
    array_push($list, trim($currentSheet->getCell("A{$excelRows}")->getValue()));
}

$i = 0;
foreach ($list as $k => $v) {
    $v = trim($v);
    if (empty($v)) {
        continue;
    }

    $code = substr($v, 0, 2);
    $id   = (int) substr($v, 2);

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
					fs.fType = 1 AND fs.fStoreId = '" . $id . "' AND fs.fDelete = 0";
    } else {
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
					fs.fType = 2 AND fs.fStoreId = '" . $id . "' AND fs.fDelete = 0";
    }

    $rs2 = $conn->Execute($sql);
    if (!$rs2->EOF) {
        while (!$rs2->EOF) {
            if ($rs2->fields['mMobile'] != '') {
                $Data[$i]         = $rs2->fields;
                $Data[$i]['code'] = $v;
                $i++;
            }
            $rs2->MoveNext();
        }
    } else {
        $noData[] = $v;
    }
}

$Data2   = array();
$noData2 = array();
foreach ($Data as $key => $value) {
    $sql = "SELECT 
                `id` as exist 
            FROM 
                tSMS_Log 
            WHERE 
                tKind = '回饋金2' 
              AND 
                sSend_Time >= '" . $time . "' 
              AND 
                tTo = '" . $value['mMobile'] . "'";
    $rs  = $conn->Execute($sql);

    if ($rs->EOF) {
        $noData2[] = $value;
    } else {
        $Data2[] = $value;
    }
}

echo "總數" . count($Data) . "\r\n";
echo "發送數量" . count($Data2) . "\r\n";
echo "簡訊對象有缺" . count($noData) . "\r\n";
echo "未發出簡訊" . count($noData2) . "\r\n";

$no_short_url_count = [];
if (count($noData2) > 0) {
//     $sms = new SMS_Gateway();

    foreach ($noData2 as $k => $v) {
        if ($v['mMobile'] != '') {
            $jsonArr['code'] = $v['code'];

            if(substr($v['code'], 0, 2) == 'SC') { //地政士
                $sUrl = 'https://escrow2.first1.com.tw/login/scrivener/' . enCrypt(json_encode($jsonArr));
            } else { //仲介
                $sUrl = 'https://escrow2.first1.com.tw/login/realty/' . enCrypt(json_encode($jsonArr));
            }

            //取得短網址
            $url = getShortUrl($sUrl, enCrypt(json_encode($jsonArr)), $v['code']);

            if ($url == 'error') {
                lineNotify('回饋金簡訊通知用短網址產製失敗(' . $v['mMobile'] . '、' . $v['code'] . ')');
                $no_short_url_count[] = $v['code'];
                continue;
            }


            //濾除(待停用)字樣
            if (isset($v['brand'])) {
                $v['brand'] = preg_replace("/\(待停用\)/iu", '', $v['brand']);
            }

            if (isset($v['bStore'])) {
                $v['bStore'] = preg_replace("/\(待停用\)/iu", '', $v['bStore']);
            }

            //組成發送文案
            //$txt = ($sheet == 0) ? '第一建經通知：113年第2季' : '第一建經通知：113年01月';
            $txt = '第一建經通知：113年第4季';

            $txt .= preg_match("/^SC[0-9]{4}$/iu", $v['code']) ? $v['bStore'] : $v['brand'] . $v['bStore'];
            $txt .= '回饋金已結算,請點下列網址 '. $url .' 至第一建經官網確認,並依辦法請款,謝謝。';
            ##

            //發送簡訊
            $jsonArr['txt']    = $txt;
            $jsonArr['name']   = $v['mName'];
            $jsonArr['mobile'] = $v['mMobile'];

            //測試簡訊
//            if(in_array($v['code'], ['TH00112', 'TH00174', 'SC1084'])) {
//                $sms->manual_send('0906870079', $jsonArr['txt'], "y", '', '回饋金2', $jsonArr['name']); //淑婷
//                $sms->manual_send("0922591797", $jsonArr['txt'], "y", '', '回饋金2', $jsonArr['name']); //品彣
//                print_r($jsonArr);

//             }

            //正式發送程式
//            $sms->manual_send($jsonArr['mobile'], $jsonArr['txt'], "y", '', '回饋金2', $jsonArr['name']);

            echo 'No. ' . ($k + 1) . "\n";
            print_r($jsonArr);
            echo "\n";

            $jsonArr = null;
            unset($jsonArr);

            sleep(1); //緩衝發送
        }
    }
}

echo '缺少短網址總數：' . count($no_short_url_count) . "\n";
echo '缺少短網址總數(不重複)：' . count(array_unique($no_short_url_count)) . "\n";

//加密參數
function enCrypt($str, $seed = 'firstfeedSms')
{
    $encode = '';
    $rc     = new Crypt_RC4;
    $rc->setKey($seed);
    $encode = $rc->encrypt($str);

    return $encode;
}

//縮址
#20250110之後的網址改https://escrow2.first1.com.tw/
function getShortUrl($url, $key, $code = null)
{
    global $conn;

    $sql          = "SELECT * FROM tShortUrl WHERE sCategory = '0' AND sDomain = '2' AND storeId = '" . $code . "'";
    $rs           = $conn->Execute($sql);
    $ShortUrlData = $rs->fields;

    if ($ShortUrlData['sShortUrl'] != '') {
        return $ShortUrlData['sShortUrl'];
    } else {
        file_put_contents(__DIR__ . '/no_short_url.log', $code . "\n", FILE_APPEND);
        //return 'error';

        $target = "https://escrow.first1.com.tw/url/url.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("url" => $url)));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);

        if ($data['code'] == 200) { //成功
            $sql = "INSERT INTO tShortUrl SET sCategory = '0', sDomain = '2', storeId = '" . $code . "', sKey = '" . $key . "', sUrl ='" . $url . "', sShortUrl = '" . $data['url'] . "'";
            $conn->Execute($sql);

            return $data['url'];
        } else { //失敗就走原本的
            return 'error';
        }
    }
}