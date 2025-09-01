<?php

include_once '../web_addr.php';
include_once '../openadodb.php';
include_once '../session_check.php';

//半形<=>全形
function n_to_w($strs, $types = '0')
{ // narrow to wide , or wide to narrow
    $nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " ",
    );
    $wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　",
    );

    if ($types == '0') { //半形轉全形
        // narrow to wide
        $strtmp = str_replace($nt, $wt, $strs);
    } else { //全形轉半形
        // wide to narrow
        $strtmp = str_replace($wt, $nt, $strs);
    }
    return $strtmp;
}
##

$id  = $tvr  = $_REQUEST["id"];
$dt  = $_REQUEST['dt'];
$cat = $_REQUEST['cat'];

// 媒體打包用參數
$_uid = uniqid();
##
//取得合約銀行資訊
$sql   = "select tBank_kind from tBankTrans where  tOk=1  AND tExport=2 AND tId = '" . $cat . "'";
$rs    = $conn->Execute($sql);
$_bank = $conBank['cBankName'];

$web_addr = preg_replace("/http\:\/\//", "", $web_addr);

if ($cat == 'all') {
    $str = ' AND
      IF( tBank_kind = "台新",tCode2 NOT IN("虛轉虛","大額繳稅","臨櫃開票","臨櫃領現","聯行代清償") AND (tObjKind2 != "01" AND tObjKind2 != "02" AND tObjKind2 != "04"),tCode2 NOT IN("虛轉虛","大額繳稅","臨櫃開票","臨櫃領現") AND tKind != "利息")';
} else if ($_bank == '台新') {

    $sql = "select tCode2,tObjKind2 from tBankTrans where tOk=1 AND tBank_kind='" . $_bank . "' AND tExport=2 AND tId = '" . $cat . "'";
    // echo $sql;
    $rs = $conn->Execute($sql);

    if ($rs->fields['tObjKind2'] != '' && $rs->fields['tObjKind2'] != '03') {
        $str = ' AND tObjKind2 = "' . $rs->fields['tObjKind2'] . '" ';
    } elseif ($rs->fields['tCode'] == '03') {
        $str = ' AND tCode2 = "' . $rs->fields['tCode2'] . '" ';
    } else {
        $str = 'AND tId = "' . $cat . '"';
    }

} else {
    $str = 'AND tId = "' . $cat . '"';

}

$sql = 'SELECT * FROM tBankTrans WHERE tVR_Code="' . $id . '" AND tOk="1" AND tExport="2" ' . $str . ' ORDER BY tId ASC;';
// echo $sql."<bR>";
$rs = $conn->Execute($sql);

$_date = date("Ymd_His");

if (preg_match("/^999850/", $id)) {
    $id   = 'sinopac_XM_' . $id;
    $bank = 4;
} else if (preg_match("/^999860/", $id)) {
    $id   = 'sinopac_CC_' . $id;
    $bank = 6;
} else if (preg_match("/^96988/", $id)) {
    $id   = 'taishin_' . $id;
    $bank = 5;
} else if (preg_match("/^55006/", $id)) {
    $id   = 'chengdong_' . $id;
    $bank = 7;
} else {
    $bank = 1; //一銀20160603++
}

$dt_tmp = preg_replace("/-/", "", $dt);
if (preg_match("/^$dt_tmp/", $_date)) {
    $_file = $id . '_' . $_date . '.txt';
}
//上傳日=銀行放款日
else {
    $_file = $id . '_' . $_date . '_' . $dt_tmp . '.txt';
}

$filename = '/home/httpd/html/' . $web_addr . '/bank/output/' . $_file;
$dl_file  = '/bank/output/' . $_file;

//台新每日用戶自訂序號
// $last_sn = '' ;
// $sql = 'SELECT * FROM tTaishinSN WHERE tDate="'.date("Ymd").'";' ;
// $tmp = $conn->Execute($sql) ;
// if ($tmp->RecordCount() > 0) {
//     $last_sn = $tmp->fields['tSN'] ;
// }
// else {
//     $last_sn = 0 ;
// }
// $last_sn += 1 - 1 ;
// unset($tmp) ;
##
$book_check = 1;
$bCategory  = 1;
while (!$rs->EOF) {

    switch ($rs->fields["tCode"]) {
        case "01":
            if ($rs->fields["tCode2"] == '虛轉虛') {
                if ($bank != 5) {
                    $book_check = 0; //非一般指示書
                }

            }
            break;
        case '03':
            if ($bank == 5) {
                $bCategory = 10;
            }
            break;
        case "04":
            // if ($bank != 5) {
            //     $book_check = 0; //非一般指示書
            // }
            $book_check = 0; //非一般指示書
            break;
        case "05":
            // if ($bank != 5) {
            //     $book_check = 0; //非一般指示書
            // }
            $book_check = 0; //非一般指示書
            break;

    }

    if ($rs->fields['tObjKind2'] == '01' || $rs->fields['tObjKind2'] == '05') { //01申請公司代墊不用指示書
        $book_check = 2;
    }

    if (preg_match("/^9998[56]0/", $tvr)) {
        // No.1 交易類別(2)
        $aCode = $rs->fields["tCode"];

        // No.2 收款銀行(7)
        //if ($aCode=='06') {                                        // 若交易類別為利息轉出
        //    $rs->fields["tBankCode"] = '8070391' ;                    // 則將"收款銀行"指定為"永豐帳戶"
        //}
        $aBank = str_pad($rs->fields["tBankCode"], 7);

        // No.3 收款帳號(14)
        if (($aCode == '04') || ($aCode == '05')) { // 當交易類別為"大額繳稅"、"臨櫃開票"
            $rs->fields["tAccount"] = '00000000000000'; // 則將"收款帳號"改為"00000000000000"
        }
        $aAccount = str_pad($rs->fields["tAccount"], 14, STR_PAD_LEFT);

        // No.4 收款人戶名(80)
        if (($aCode == '04') || ($aCode == '05')) { // 當交易類別為"大額繳稅"、"臨櫃開票"
            $rs->fields["tAccountName"] = ''; // 則本欄位顯示全空白
        }
        //$_t4 = mb_strlen($rs->fields["tAccountName"],"utf-8");
        $_aName = mb_str_pad(n_to_w($rs->fields["tAccountName"]), 80); //2015-05-20 半形轉全形

        // No.5 收款人身分證、統一編號(10)
        if (($aCode == '04') || ($aCode == '05')) { // 當交易類別為"大額繳稅"、"臨櫃開票"
            $rs->fields["tAccountId"] = ''; // 則本欄位顯示全空白
        }
        //$_t5 = mb_strlen($rs->fields["tAccountId"],"utf-8");
        $_aId = str_pad($rs->fields["tAccountId"], 10);

        // No.6 匯款金額(13+2=15)
        //$_t6 = mb_strlen($rs->fields["tMoney"],"utf-8");
        $_money  = $rs->fields["tMoney"] . "00";
        $_aMoney = str_pad($_money, 15, "0", STR_PAD_LEFT);

        // No.7 備註(10)
        //$_t7 = mb_strlen($rs->fields["tMemo"],"utf-8");
        $_aMemo = mb_str_pad($rs->fields["tMemo"], 10); // 保證號碼

        // No.8 附言(60)
        //$_t8 = mb_strlen($rs->fields["tTxt"],"utf-8");
        $_aTxt = mb_str_pad(n_to_w($rs->fields["tTxt"]), 60);

        // No.9 收款人傳真號碼(15)
        //$_t9 = mb_strlen($rs->fields["tFax"],"utf-8");
        $_aFax = str_pad($rs->fields["tFax"], 15);

        // No.10 收款人 e-mail 位址(35)
        //$_t10 = mb_strlen($rs->fields["tEmail"],"utf-8");
        $_aEmail = str_pad($rs->fields["tEmail"], 35);

        //No.11 存摺備註欄 (12)
        // $aCode
        if ($aCode == '01' || $aCode == '02') {
            $_bankShow = mb_str_pad(n_to_w($rs->fields["tBankShowTxt"]), 12);
        } else {
            $_bankShow = mb_str_pad(n_to_w(''), 12);
        }

        $_full_line .= $aCode . $aBank . $aAccount . $_aName . $_aId . $_aMoney . $_aMemo . $_aTxt . $_aFax . $_aEmail . $_bankShow . "\n";
    } else if (preg_match("/^96988/", $tvr)) {
        $aCode = $rs->fields["tCode"];
        //台新每日用戶自訂序號
        $last_sn  = 0;
        $dd       = preg_replace("/-/", "", $dt);
        $sql      = 'SELECT * FROM tTaishinSN WHERE tDate="' . $dd . '";';
        $tmp      = $conn->Execute($sql);
        $tmpCount = $tmp->RecordCount();
        if ($tmpCount > 0) {
            $last_sn = $tmp->fields['tSN'] + 1 - 1;
        }
        // $last_sn += 1 - 1 ;
        unset($tmp);
        // No.1 用戶自訂序號(7)
        $last_sn++; //每筆紀錄流水號累加
        $_sn = substr($dd, -2) . str_pad($last_sn, 5, '0', STR_PAD_LEFT); //每日不重複之 7 碼流水序號
        setTaishinNum($last_sn, $dd); //更新台新每日流水序號
        ##

        // No.2 付款日期(8)
        //$payDate = date("Ymd") ;                                    //預設當日即進行付款
        $payDate = preg_replace("/-/", "", $dt); //付款日期
        ##

        // No.3 付款金額(18)
        $_money = $rs->fields["tMoney"] . "00"; //右側捕兩位小數點
        $_money = str_pad($_money, 18, "0", STR_PAD_LEFT); //左補零補滿18碼
        ##
        //付款人帳號
        if ($rs->fields['tObjKind2'] == '01' || $rs->fields['tObjKind2'] == '05') { //申請代墊 付款對象為代墊用帳戶
            $showPayBank['Account']     = $conBank['cBankAccount2'];
            $showPayBank['AccountName'] = $conBank['cAccountName2'];
        } else {
            $showPayBank['Account']     = $conBank['cBankTrustAccount'];
            $showPayBank['AccountName'] = $conBank['cTrustAccountName'];
        }

        // No.4 付款人帳號(17)
        $_payAccNo = $showPayBank['Account']; //第一建經台新信託帳號
        $_payAccNo = str_pad($_payAccNo, 17); //左靠右補空白
        ##

        // No.5 付款戶名(60)
        //$_payAccName = '台新國際商業銀行受託信託財產專戶' ;        //第一建經台新信託戶名
        //$_payAccName = '台新國際商業銀行受託信託財產專' ;            //第一建經台新信託戶名
        if (($aCode == '02') && $rs->fields["tBankShowTxt"] != '') {
            $tBankShowTxt = $rs->fields["tBankShowTxt"];

            $_payAccName = $tBankShowTxt . $showPayBank['AccountName']; //第一建經台新信託戶名(台新銀行－第一建經履保專戶)
        } else {
            $_payAccName = $showPayBank['AccountName']; //第一建經台新信託戶名(台新銀行－第一建經履保專戶)
        }
        // $_payAccName = $tBankShowTxt.'第一建經' ;//第一建經台新信託戶名(台新銀行－第一建經履保專戶)
        $_payAccName = mb_str_pad(n_to_w($_payAccName), 60); //左靠右補空白
        ##

        // No.6 收款帳號(17)
        $_accountNo = $rs->fields['tAccount']; //收款人帳號
        $_accountNo = str_pad($_accountNo, 17); //左靠右補空白
        ##

        // No.7 收款戶名(60)
        $_accName = n_to_w($rs->fields['tAccountName']); //收款人戶名  2015-05-20 加上半形轉全形
        $_accName = mb_str_pad($_accName, 60); //左靠右補空白
        ##

        // No.8 付款總行(3)
        $_payBankMain = '812'; //第一建經台新總行
        ##

        // No.9 付款分行(4)
        $_payBankBranch = '0687'; //第一建經台新分行
        ##

        // No.10 收款總行(3)
        $_accBankMain = substr($rs->fields['tBankCode'], 0, 3); //收款人總行
        ##

        // No.11 收款人分行(4)
        $_accBankBranch = substr($rs->fields['tBankCode'], 3); //收款人分行
        ##

        // No.12 附言(100)
        // $memo_txt = $rs->fields['tTxt']." 付款人:第一建經/保證碼:".$rs->fields['tMemo'];
        // $_memo = mb_str_pad(n_to_w($memo_txt),100) ;
        $memo_txt = $rs->fields['tTxt'];
        $_memo    = mb_str_pad(n_to_w($memo_txt), 60); // 56碼 = 100 - 44
        $memo_txt = "[付款人第一建經/" . $rs->fields['tMemo'] . "]";
        $_memo .= mb_str_pad(n_to_w($memo_txt), 40);
        ##

        // No.13 收款人識別碼(17)
        $_IdCode = str_pad('00000000', 17); //收款人識別碼、左靠右補空白
        ##

        // No.14 收款人代碼識別(3)
        $_Id = '53 '; //53:虛擬帳號、左靠右補空白
        ##

        // No.15 付款人識別碼(3)
        //$_payIdCode = str_pad('53549920',17) ;                    //第一建經統一編號、左靠右補空白
        // $_payIdCode = str_pad('99360890',17) ;                        //台新國際商業銀行受託信託財產專戶統一編號、左靠右補空白
        if ($rs->fields['tObjKind2'] == '01' || $rs->fields['tObjKind2'] == '05') {
            $_payIdCode = str_pad('53549920', 17); //台新國際商業銀行受託信託財產專戶統一編號、左靠右補空白
        } else {
            $_payIdCode = str_pad('99360890', 17); //台新國際商業銀行受託信託財產專戶統一編號、左靠右補空白
        }
        ##

        // No.16 付款人代碼識別(3)
        $_payId = '58 '; //58:統一編號、左靠右補空白
        ##

        // No.17 手續費負擔別(3)
        $_charge = '15 '; //15:付款人、左靠右補空白
        ##

        // No.18 對帳單 Key 值(30)
        if ($rs->fields['tObjKind2'] == '01' || $rs->fields['tObjKind2'] == '05') {
            $_billCheck = str_pad('98828' . substr($rs->fields["tVR_Code"], -9), 30);
        } else {
            $_billCheck = str_pad($rs->fields["tVR_Code"], 30); //對帳單 Key 值
        }
        ##

        // No.19 付款聯絡人(35)
        $_payContact = str_pad('', 35);
        ##

        // No.20 付款連絡電話(25)
        $_payTel = str_pad('', 25);
        ##

        // No.21 付款傳真號碼(25)
        $_payFax = str_pad('', 25);
        ##

        // No.22 收款聯絡人(35)
        //$_contact = $rs->fields['tAccountName'] ;
        $_contact = mb_substr($rs->fields['tAccountName'], 0, 17, 'UTF-8'); //取17個中文字
        $_contact = mb_str_pad($_contact, 35); //收款方姓名、左靠右補空白
        ##

        // No.23 收款連絡電話(25)
        $_conTel = str_pad('', 25);
        ##

        // No.24 收款傳真號碼(25)
        $_conFax = str_pad($rs->fields['tFax'], 25);
        ##

        // No.25 收款通知 email(50)
        $_conEmail = str_pad($rs->fields['tEmail'], 50);
        ##

        // 組成單行資料紀錄
        $_full_line .= $_sn . $payDate . $_money . $_payAccNo . $_payAccName . $_accountNo . $_accName . $_payBankMain . $_payBankBranch;
        $_full_line .= $_accBankMain . $_accBankBranch . $_memo . $_IdCode . $_Id . $_payIdCode . $_payId . $_charge . $_billCheck;
        $_full_line .= $_payContact . $_payTel . $_payFax . $_contact . $_conTel . $_conFax . $_conEmail . "\n";

        //echo $_full_line."<br>\n" ;
        ##
    } else {
        // $aCode = $rs->fields["tCode"];
        // $aBank = str_pad($rs->fields["tBankCode"],7);
        // $aAccount = str_pad($rs->fields["tAccount"],14,"0",STR_PAD_LEFT);
        //echo strlen(trim($rs->fields["tAccountName"])) ."-". mb_strlen($rs->fields["tAccountName"],"utf-8")."<br>";

        $aCode    = $rs->fields["tCode"];
        $aBank    = ($aCode == '06') ? '0070000' : str_pad($rs->fields["tBankCode"], 7);
        $aAccount = in_array($aCode, ['04', '05']) ? '00000000000000' : str_pad($rs->fields["tAccount"], 14);

        $_t4    = mb_strlen($rs->fields["tAccountName"], "utf-8");
        $_aName = mb_str_pad(n_to_w($rs->fields["tAccountName"]), 80); //2015-05-20 半形轉全形

        $_t5  = mb_strlen($rs->fields["tAccountId"], "utf-8");
        $_aId = str_pad($rs->fields["tAccountId"], 10);

        //$_t6 = mb_strlen($rs->fields["tMoney"],"utf-8");
        $_money  = $rs->fields["tMoney"] . "00";
        $_aMoney = str_pad($_money, 15, "0", STR_PAD_LEFT);

        $_t7    = mb_strlen($rs->fields["tMemo"], "utf-8");
        $_aMemo = mb_str_pad($rs->fields["tMemo"], 10);

        $_t8   = mb_strlen($rs->fields["tTxt"], "utf-8");
        $_aTxt = mb_str_pad(n_to_w($rs->fields["tTxt"]), 80); //20221021 要求一銀加長附言長度至 80 bytes
        // $_aTxt = mb_str_pad(n_to_w($rs->fields["tTxt"]), 40);
        // $_aTxt = mb_substr(n_to_w($rs->fields["tTxt"]), 0, 20, 'utf-8');
        // $_aTxt = mb_str_pad($_aTxt, 40);

        $_t9   = mb_strlen($rs->fields["tFax"], "utf-8");
        $_aFax = str_pad($rs->fields["tFax"], 15);

        $_t10    = mb_strlen($rs->fields["tEmail"], "utf-8");
        $_aEmail = str_pad($rs->fields["tEmail"], 35);

        $_full_line .= $aCode . $aBank . $aAccount . $_aName . $_aId . $_aMoney . $_aMemo . $_aTxt . $_aFax . $_aEmail . "\n";
    }

    //echo $_full_line."\n";

    // 更新資料庫出款狀態
    $_date = date("Y-m-d H:i:s");
    //------------------------------------------
    $update = '
		UPDATE
			tBankTrans
		SET
			tExport="1",
			tExport_time="' . $_date . '",
			tExport_nu="' . $_uid . '",
			tBankLoansDate="' . $dt . '"
		WHERE
			tId="' . $rs->fields['tId'] . '" ;
	';
    // echo $update."<bR>";
    $conn->Execute($update);
    //------------------------------------------
    if ($book_check == 0) { //非一般指示書
        $sql = "UPDATE tBankTrankBook SET bExport_nu ='" . $_uid . "',bMoney ='" . $rs->fields["tMoney"] . "' WHERE bBankTranId = '" . $rs->fields['tId'] . "'";
        //$rs->fields["tMoney"]
        $conn->Execute($sql);

    }
    $rs->MoveNext();
}
// echo $book_check;
// die;
//新增一般指示書
if ($book_check == 1) {

    $sql = 'SELECT SUM(tMoney) AS totalmoney,tVR_Code,COUNT(tId) AS cou FROM tBankTrans WHERE tExport_nu="' . $_uid . '" ORDER BY tId ASC;';
    $rs  = $conn->Execute($sql);

    $sql = "INSERT INTO tBankTrankBook (bCertifiedId,bMoney,bCount,bExport_nu,bCategory,bBank,bCreatorId,bCreatName,bCreatTime) VALUES('" . $rs->fields['tVR_Code'] . "','" . $rs->fields['totalmoney'] . "','" . $rs->fields['cou'] . "','" . $_uid . "','" . $bCategory . "','" . $bank . "','" . $_SESSION['member_id'] . "','" . $_SESSION['member_name'] . "','" . date('Y-m-d H:i:s') . "')";

    $conn->Execute($sql);
}

//更新台新每日流水序號
if (preg_match("/^96988/", $tvr)) {
    // $update = 'SELECT * FROM tTaishinSN WHERE tDate="'.date("Ymd").'";' ;
    // $update_rs = $conn->Execute($update) ;

    // if ($update_rs->RecordCount() > 0) {
    //     $update = 'UPDATE tTaishinSN SET tSN="'.$last_sn.'" WHERE tDate="'.date("Ymd").'";' ;
    // }
    // else {
    //     $update = 'INSERT INTO tTaishinSN (tDate,tSN) VALUES ("'.date("Ymd").'","'.$last_sn.'") ;' ;
    // }
    // $conn->Execute($update) ;
}

##
function setTaishinNum($last_sn, $date)
{
    global $conn;

    if ($last_sn > 1) {
        $update = 'UPDATE tTaishinSN SET tSN="' . $last_sn . '" WHERE tDate="' . $date . '";';
    } else {
        $update = 'INSERT INTO tTaishinSN (tDate,tSN) VALUES ("' . $date . '","' . $last_sn . '") ;';
    }
    $conn->Execute($update);
}

$txt    = iconv("utf-8", "big5", $_full_line);
$handle = fopen($filename, 'w+');
fwrite($handle, $txt);
fclose($handle);

function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
{
    //$diff = strlen($input) - mb_strlen($input,"utf-8");
    if (strlen($input) == mb_strlen($input, "utf-8")) {
        $diff = 0;
    } else {
        $diff = mb_strlen($input, "utf-8");
    }
    return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>媒體檔匯出</title>
</head>
<body>
<a href="<?php echo $dl_file; ?>">下載媒體檔</a>
</body>
</html>