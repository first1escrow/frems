<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Reader/Excel5.php';

$year   = $_POST['year'];
$season = $_POST['season'];

// 年度季別

if ($year && $season) {
    switch ($season) {
        case 'S1':
            $date_start = $year . "-01-01";

            break;
        case 'S2':
            $date_start = $year . "-04-01";

            break;
        case 'S3':
            $date_start = $year . "-07-01";

            break;
        case 'S4':
            $date_start = $year . "-10-01";

            break;
        default:
            $date_start = $year . "-" . $season . "-01";
            break;
    }
}

if ($year && $season) {
    switch ($season) {
        case 'S1':
            $date_end = $year . "-01-31";

            break;
        case 'S2':
            $date_end = $year . "-04-30";

            break;
        case 'S3':
            $date_end = $year . "-07-31";

            break;
        case 'S4':
            $date_end = $year . "-10-31";

            break;
        default:
            $date_end = $year . "-" . $season . "-" . date('t', $year . "-" . $season);
            break;
    }

}

# 設定檔案存放目錄位置
$uploaddir = __DIR__ . '/excel/';
if (!is_dir($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}
##

// # 設定檔案名稱
$uploadfile = $_FILES['upload_file']['name'];
$uploadfile = $uploaddir . $uploadfile;
// ##

if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile)) {
    $xls = $uploaddir . $_FILES["upload_file"]["name"];
} else {
    die("檔案上傳錯誤");
}
##

//讀取 excel 檔案

$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);

//檔案名稱
$objPHPExcel  = $objReader->load($xls);
$currentSheet = $objPHPExcel->getSheet(0); //讀取第一個工作表(編號從 0 開始)
$allLine      = $currentSheet->getHighestRow(); //取得總列數

//店編號    季別    收款日    回饋金    代扣二代健保    代扣所得稅    實收金額
//編號    戶名    店名稱    二代健保    收款日    回饋金    代扣二代健保    代扣所得稅    實收金額    季別

$i          = 0;
$repeatData = array();
$error      = array();
for ($excel_line = 2; $excel_line <= $allLine; $excel_line++) {

    $store = $currentSheet->getCell("A{$excel_line}")->getValue(); //編號

    $tmp['store']      = $store;
    $tmp['sStoreCode'] = substr($store, 0, 2);
    $tmp['sStoreId']   = substr($store, 2);
    $tmp['sType']      = ($tmp['sStoreCode'] == 'SC') ? 1 : 2;

    $tmp['sSeason'] = $currentSheet->getCell("J{$excel_line}")->getValue(); //季別

    $monthTxt = array('一' => '01', '二' => '02', '三' => '03', '四' => '04', '五' => '05', '六' => '06', '七' => '07', '八' => '08', '九' => '09', "十" => '10', '十一' => '11', '十二' => '12');

    $season = $tmp['sSeason'];

    //比對格式 xxx年1月 或 xxx年一月 統一 yyy年mm月
    if (preg_match("/月/", $season)) {
        foreach ($monthTxt as $k => $v) {
            $season = str_replace($k, $v, $season);
        }

        preg_match_all("/(.*)年(.*)月/", $season, $tmp2);

        $season         = $tmp2[1][0] . "年" . str_pad($tmp2[2][0], 2, '0', STR_PAD_LEFT) . '月'; //$tmp[1][1]
        $tmp['sSeason'] = $season;
        unset($tmp2);

    }

    if (preg_match("/季/", $season)) {
        foreach ($monthTxt as $k => $v) {
            $season = str_replace($k, $v, $season);
        }
        $tmp['sSeason'] = $season;
    }

    $tmp['sDate'] = $currentSheet->getCell("E{$excel_line}")->getValue(); //收款日
    $date         = $tmp['sDate']; //顯示訊息用

    $tmp['sFeedBackMoney'] = $currentSheet->getCell("F{$excel_line}")->getValue(); //回饋金

    $tmp['sNHITax'] = $currentSheet->getCell("G{$excel_line}")->getCalculatedValue(); //代扣二代健保
    $tmp['sTax']    = $currentSheet->getCell("H{$excel_line}")->getCalculatedValue(); //代扣所得稅

    $tmp['sAmountReceived'] = $currentSheet->getCell("I{$excel_line}")->getCalculatedValue(); //實收金額

    $check = 0;

    if (!preg_match("/^(\d{0,2})\/(\d{0,2})$/", $tmp['sDate'])) { //比對日期格式 月/日
        $check = 1;
        // echo "A";
    }

    if ($tmp['sAmountReceived'] == 0 || $tmp['sAmountReceived'] == '') {
        $check = 1;
        // echo "B";
    }

    if (!preg_match("/^(.*)年第(.*)季$/", $tmp['sSeason']) && !preg_match("/^(.*)年(.*)月$/", $tmp['sSeason'])) { //109年09月
        $check = 1;
        // echo "C";
    }

    $tmp['sDate'] = date('Y') . '-' . str_replace('/', '-', $tmp['sDate']);

    $sql = "SELECT
				*
			FROM
				tStoreFeedBackMoneyFrom_Record
			WHERE
				 sStoreCode = '" . $tmp['sStoreCode'] . "'
				AND sStoreId = '" . $tmp['sStoreId'] . "'
				AND sType = '" . $tmp['sType'] . "'
				AND sSeason = '" . $tmp['sSeason'] . "'
				AND sDate = '" . $tmp['sDate'] . "'
				AND sFeedBackMoney = '" . $tmp['sFeedBackMoney'] . "'
				AND sNHITax = '" . abs($tmp['sNHITax']) . "'
				AND sTax = '" . abs($tmp['sTax']) . "'
				AND sAmountReceived = '" . $tmp['sAmountReceived'] . "' AND sDel =0";
    // echo $sql."<br>";
    $rs = $conn->Execute($sql);

    if ($check == 1) {
        $error[] = $store . "季別:" . $tmp['sSeason'] . "收款日:" . $date . "實收金額:" . $tmp['sAmountReceived'];
    } else if ($rs->EOF) { //查不到
        $data[$i] = $tmp;
        $i++;
    } else {
        $repeatData[] = $tmp['store']; //有重複的
    }
}

$successful  = 0;
$failStoreId = array();
for ($i = 0; $i < count($data); $i++) {

    if ($data[$i]['sStoreCode'] && $data[$i]['sStoreId'] && $data[$i]['sFeedBackMoney']) {
        # code...

        $sql = "INSERT INTO
					tStoreFeedBackMoneyFrom_Record
				SET
					sStoreCode = '" . $data[$i]['sStoreCode'] . "',
					sStoreId = '" . $data[$i]['sStoreId'] . "',
					sType = '" . $data[$i]['sType'] . "',
					sSeason = '" . $data[$i]['sSeason'] . "',
					sDate = '" . $data[$i]['sDate'] . "',
					sFeedBackMoney = '" . $data[$i]['sFeedBackMoney'] . "',
					sNHITax = '" . abs($data[$i]['sNHITax']) . "',
					sTax = '" . abs($data[$i]['sTax']) . "',
					sAmountReceived = '" . $data[$i]['sAmountReceived'] . "'
		";
        if ($conn->Execute($sql)) {
            $successful++;
        } else {
            $failStoreId[] = $data[$i]['store'];
        }
        $id = $conn->Insert_ID();

        //出款回寫至tStoreFeedBackMoneyFrom
        $sql = "UPDATE
					tStoreFeedBackMoneyFrom
				SET
					sRecord = '" . $id . "',
					sStatus = 3
				WHERE
					sType = '" . $data[$i]['sType'] . "'
					AND sStoreId = '" . $data[$i]['sStoreId'] . "'
					AND sSeason = '" . $data[$i]['sSeason'] . "'";
        // echo $sql."<bR>";
        $conn->Execute($sql);
    }
}

if (!empty($error)) {
    $msg = "資料有誤請檢查" . @implode('\r\n', $error);
} elseif (!empty($repeatData)) {
    $msg = "有重複資料" . @implode(',', $repeatData);
} elseif ($failStoreId) {
    $msg = '部分上傳更新失敗' . @implode(',', $failStoreId);
} elseif ($successful == count($data)) {
    $msg = '成功';
}

function month($txt)
{

}
