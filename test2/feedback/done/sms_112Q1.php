<?php
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL ^ E_WARNING);

require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/sms/sms_function_manually.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(dirname(__DIR__)) . '/bank/Classes/PHPExcel/Reader/Excel5.php';
require_once dirname(dirname(__DIR__)) . '/lineNotify.php';

//簡訊發送起始時間
$time = '2023-04-12 00:00:00';

//設定檔案存放目錄位置
$xls = __DIR__ . '/excel/112Q1.xlsx';

$sheet = 0; //季結
// $sheet = 1; //月結

//讀取 excel 檔案
$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);

//檔案名稱
$objPHPExcel  = $objReader->load($xls);
$currentSheet = $objPHPExcel->getSheet($sheet); //讀取第一個工作表(編號從 0 開始)
$allLine      = $currentSheet->getHighestRow(); //取得總列數

$noData = array();
$list   = array();
for ($excel_line = 2; $excel_line <= $allLine; $excel_line++) {
    array_push($list, trim($currentSheet->getCell("A{$excel_line}")->getValue()));
}

// print_r($list);
// exit;

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

// $Data = array_slice($Data, 0, 10);
// print_r($Data);
// exit;

$Data2 = $noData2 = [];
foreach ($Data as $key => $value) {
    $sql = "SELECT `id` as exist FROM tSMS_Log WHERE tKind = '回饋金2' AND sSend_Time >= '" . $time . "' AND tTo = '" . $value['mMobile'] . "'";
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

// echo "<pre>";
// print_r($noData);
print_r($noData2);
// exit;

$no_short_url_count = [];
if (count($noData2) > 0) {
    // $sms = new SMS_Gateway();

    foreach ($noData2 as $k => $v) {
        if ($v['mMobile'] != '') {
            $jsonArr['code'] = $v['code'];

            //取得短網址
            $url = getShortUrl('https://escrow.first1.com.tw/login/page-price1.php?v=' . enCrypt(json_encode($jsonArr)), enCrypt(json_encode($jsonArr)), $v['code']);
            // print_r($url);
            // echo "\n\n";
            // continue;

            if ($url == 'error') {
                lineNotify('回饋金簡訊通知用短網址產製失敗(' . $v['mMobile'] . '、' . $v['code'] . ')');
                $no_short_url_count[] = $v['code'];
                continue;
            }
            ##

            //濾除(待停用)字樣
            if (isset($v['brand'])) {
                $v['brand'] = preg_replace("/\(待停用\)/iu", '', $v['brand']);
            }

            if (isset($v['bStore'])) {
                $v['bStore'] = preg_replace("/\(待停用\)/iu", '', $v['bStore']);
            }
            ##

            //組成發送文案
            $txt = ($sheet == 0) ? '第一建經通知：112年第1季' : '第一建經通知：112年3月';

            $txt .= preg_match("/^SC[0-9]{4}$/iu", $v['code']) ? $v['bStore'] : $v['brand'] . $v['bStore'];
            $txt .= '回饋金已結算,請點下列網址至第一建經官網確認,並依辦法請款,謝謝。' . $url . "\r\n";
            ##

            //發送簡訊
            $jsonArr['txt']    = $txt;
            $jsonArr['name']   = $v['mName'];
            $jsonArr['mobile'] = $v['mMobile'];

            // if ($jsonArr['code'] == 'SC0063') {//|| $jsonArr['code'] == 'YC03664'
            // $sms->manual_send("0928590425",$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);//家津
            // $sms->manual_send("0922591797", $jsonArr['txt'], "y", '', '回饋金2', $jsonArr['name']); //品彣
            // $sms->manual_send("0919200247",$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);//佩琦
            // $sms->manual_send('0922785490', $jsonArr['txt'], "y", '', '回饋金2', $jsonArr['name']); //Jason

            // $sms->manual_send($jsonArr['mobile'],$jsonArr['txt'],"y",'','回饋金2',$jsonArr['name']);
            // print_r($jsonArr);
            // exit;
            // }

            //正式發送程式
            // $sms->manual_send($jsonArr['mobile'], $jsonArr['txt'], "y", '', '回饋金2', $jsonArr['name']);
            ##

            echo 'No. ' . ($k + 1) . "\n";
            print_r($jsonArr);
            echo "\n";
            ##

            $jsonArr = null;
            unset($jsonArr);
            exit;
            // sleep(1); //緩衝發送
        }
    }
}

// echo '缺短網址總數：' . count($no_short_url_count) . "\n";
// echo '缺短網址總數(不重複)：' . count(array_unique($no_short_url_count)) . "\n";

//加密參數
function enCrypt($str, $seed = 'firstfeedSms')
{
    $encode = '';
    $rc     = new Crypt_RC4;
    $rc->setKey($seed);
    $encode = $rc->encrypt($str);

    return $encode;
}

//取得(DB)或產製(picsee.io)短網址
function getShortUrl($url, $key, $code = null)
{
    global $conn;

    $sql          = "SELECT * FROM tShortUrl WHERE sCategory = '0' AND sKey = '" . $key . "'";
    $rs           = $conn->Execute($sql);
    $ShortUrlData = $rs->fields;

    if ($ShortUrlData['sShortUrl'] != '') {
        return $ShortUrlData['sShortUrl'];
    } else {
        // exit($url);
        // file_put_contents(__DIR__ . '/no_short_url.log', $code . "\n", FILE_APPEND);
        return 'error';

        // return "https://www.first1.com.tw";
        // echo $sql . "\n";
        // exit('No Short URL: ' . $code . "\n");

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
            $sql = "INSERT INTO tShortUrl SET sCategory = '0', storeId = '" . $code . "', sKey = '" . $key . "', sUrl ='" . $url . "', sShortUrl = '" . $data['url'] . "'";
            $conn->Execute($sql);

            return $data['url'];
        } else { //失敗就走原本的
            return 'error';
        }
    }
}
