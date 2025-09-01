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

//
function regular_w($_str, $_max_char = 20, $_str_type = "utf-8")
{
    $_str = preg_replace("/ /", "　", $_str);
    $_max = mb_strlen($_str, $_str_type);
    $_max = $_max_char - $_max;

    for ($index = 0; $index < $_max; $index++) {
        $_str .= "　";
    }
    return $_str;
}
##

//
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
##

$x   = $_REQUEST['x']; // bank
$y   = $_REQUEST['y'];
$dt  = $_REQUEST['l'];
$cat = $_REQUEST['cat'];
//取得合約銀行資訊
$sql     = 'SELECT * FROM tContractBank WHERE cShow="1" AND cId="' . $x . '" ORDER BY cId ASC;';
$rs      = $conn->Execute($sql);
$conBank = $rs->fields;
$_bank   = $conBank['cBankName'];
unset($rs);
##

// 媒體打包用參數 **極重要**
$_uid = uniqid();
##

if ($cat == 'all') {
    $str = ' AND
      IF( tBank_kind = "台新",tCode2 NOT IN("一銀內轉","大額繳稅","臨櫃開票","臨櫃領現","聯行代清償") AND (tObjKind2 != "01" AND tObjKind2 != "02" AND tObjKind2 != "04" AND tObjKind2 != "05")  AND tKind != "利息",tCode2 NOT IN("一銀內轉","大額繳稅","臨櫃開票","臨櫃領現") AND tKind != "利息")';
} elseif ($_bank == '台新') {

    $sql = "select tCode2,tObjKind2,tCode from tBankTrans where tVR_Code LIKE '" . $conBank['cBankVR'] . "%' AND tOk=1 AND tBank_kind='" . $_bank . "' AND tExport=2 AND tId = '" . $cat . "'";

    // if ($_SESSION['member_id'] == 6) {
    //     echo $sql;
    //     // die;
    // } else {
    //     # code...
    // }

    // echo $sql;
    $rs = $conn->Execute($sql);

    // if ($rs->fields['tObjKind2'] != '') {
    //     $str = ' AND tObjKind2 = "'.$rs->fields['tObjKind2'].'" ';
    // }elseif($rs->fields['tCode'] == '03'){
    //     $str = ' AND tCode2 = "'.$rs->fields['tCode2'].'" ';
    // }else{
    //     $str = 'AND tId = "'.$cat.'"';
    // }
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
$sql = 'SELECT * FROM tBankTrans WHERE tOk="1" AND tExport="2" AND tBank_kind="' . $_bank . '" AND tVR_Code LIKE "' . $conBank['cBankVR'] . '%" ' . $str . ' ORDER BY tId ASC;';
// if ($_SESSION['member_id'] == 6) {
//         echo $sql;
//         die;
//     } else {
//         # code...
//     }
$rs = $conn->Execute($sql);

$_full_line = '';

$_total = $rs->RecordCount();

echo "<p>媒體檔明細  共 $_total 筆</p>";
echo "<ul>";
$book_check = 1; //確認是否為一般指示書
$bCategory  = 1;
$i          = 0;
while (!$rs->EOF) {
    //
    switch ($rs->fields["tCode"]) {
        case "01":
            $_title = "聯行轉帳";
            if ($rs->fields["tCode2"] == '一銀內轉') {
                $_title = "一銀內轉";
                $bCategory = 14;
            }

            break;
        case "02":
            $_title = "跨行代清償";
            break;
        case "03":
            $_title = "聯行代清償";
            if ($x == 5) { //台新聯行代清償資料都可從出款資料取得所以不用讓經辦KEY，故當成一般指示書
                // $book_check = 0; //非一般指示書
                $bCategory = 10;
            }
            break;
        case "04":
            $_title = "大額繳稅";
            // $book_check = 0; //非一般指示書
            // if ($x != 5) {
            //     $book_check = 0; //非一般指示書
            // }
            $book_check = 0; //非一般指示書
            break;
        case "05":
            $_title = "臨櫃開票";
            if ($rs->fields["tCode2"] == '臨櫃領現') {
                $_title = "臨櫃領現";
            }
            // $book_check = 0; //非一般指示書
            // if ($x != 5) {
            //     $book_check = 0; //非一般指示書
            // }
            $book_check = 0; //非一般指示書
            break;
        case "06":
            $_title = "利息";
            break;
    }

    if ($rs->fields['tObjKind2'] == '01' || $rs->fields['tObjKind2'] == '05') { //01申請公司代墊不用指示書
        $book_check = 2;
    }

    //

    // 永豐銀行媒體檔
    if (($x == 4) || ($x == 6)) {
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
        $aAccount = str_pad($rs->fields["tAccount"], 14, "0", STR_PAD_LEFT);

        // No.4 收款人戶名(80)
        if (($aCode == '04') || ($aCode == '05')) { // 當交易類別為"大額繳稅"、"臨櫃開票"
            $rs->fields["tAccountName"] = ''; // 則本欄位顯示全空白
        }
        //$_t4 = mb_strlen($rs->fields["tAccountName"],"utf-8");
        $_aName = mb_str_pad(n_to_w($rs->fields["tAccountName"]), 80); // 2015-05-20 半形轉全形

        // No.5 收款人身分證、統一編號(10)
        if (($aCode == '04') || ($aCode == '05')) { // 當交易類別為"大額繳稅"、"臨櫃開票"
            $rs->fields["tAccountId"] = ''; // 則本欄位顯示全空白
        }
        //$_t5 = mb_strlen($rs->fields["tAccountId"],"utf-8") ;
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

        echo "<li>$_title -銀行代碼: $aBank -銀行帳號: $aAccount -戶名: $_aName - $_aId -匯出款: $_money -備註: $_aMemo -附言: $_aTxt -傳真: $_aFax -電郵: $_aEmail - 存摺備註欄: $_bankShow </li>";
        $_full_line .= $aCode . $aBank . $aAccount . $_aName . $_aId . $_aMoney . $_aMemo . $_aTxt . $_aFax . $_aEmail . $_bankSho . $_bankShow . "\n";
    }
    ##

    // 台新銀行媒體檔
    else if ($x == 5) {
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
        unset($tmp);

        ##

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
        ##

        // No.4 付款人帳號(17)
        //$_payAccNo = '20680100135997' ;                            //第一建經台新信託帳號
        $_payAccNo = $showPayBank['Account']; //第一建經台新信託帳號
        $_payAccNo = str_pad($_payAccNo, 17); //左靠右補空白
        ##

        // No.5 付款戶名(60)
        //$_payAccName = '台新第一建經' ;                                            //第一建經台新信託戶名
        //$_payAccName = mb_substr($conBank['cTrustAccountName'],0,-1,'utf-8') ;    //第一建經台新信託戶名
        //欄位5”付款人戶名”，可在信託專戶戶名前，加上6個中文字，讓收款人可以在存摺上看到這6個中文字
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
        $_accName = n_to_w($rs->fields['tAccountName']); //收款人戶名  2015-05-20 半形轉全形
        $_accName = mb_str_pad($_accName, 60); //左靠右補空白
        ##

        // No.8 付款總行(3)
        //$_payBankMain = '812' ;                                    //第一建經台新總行
        $_payBankMain = $conBank['cBankMain']; //第一建經台新總行
        ##

        // No.9 付款分行(4)
        //$_payBankBranch = '0687' ;                                //第一建經台新分行
        $_payBankBranch = $conBank['cBankBranch']; //第一建經台新分行
        ##

        // No.10 收款總行(3)
        $_accBankMain = substr($rs->fields['tBankCode'], 0, 3); //收款人總行
        ##

        // No.11 收款人分行(4)
        $_accBankBranch = substr($rs->fields['tBankCode'], 3); //收款人分行
        ##

        // No.12 附言(100)
        // $memo_txt = $rs->fields['tTxt']." 付款人:第一建經/保證碼:".$rs->fields['tMemo'];
        // $_memo = mb_str_pad(n_to_w($memo_txt),100) ;        // 56碼 = 100 - 44
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
    }
    ##

    // 一銀之媒體檔
    else {
        // $aCode = $rs->fields["tCode"];
        // $aBank = str_pad($rs->fields["tBankCode"],7);
        // $aAccount = str_pad($rs->fields["tAccount"],14,"0",STR_PAD_LEFT);

        $aCode    = $rs->fields["tCode"];
        $aBank    = ($aCode == '06') ? '0070000' : str_pad($rs->fields["tBankCode"], 7);
        $aAccount = in_array($aCode, ['04', '05']) ? '00000000000000' : str_pad($rs->fields["tAccount"], 14);

        echo "==!!> " . $aAccount . " <br>";
        //echo strlen(trim($rs->fields["tAccountName"])) ."-". mb_strlen($rs->fields["tAccountName"],"utf-8")."<br>";
        $_t4    = mb_strlen($rs->fields["tAccountName"], "utf-8");
        $_aName = mb_str_pad(n_to_w($rs->fields["tAccountName"]), 80); // 2015-05-20 半形轉全形

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
        echo "<li>$_title -銀行代碼: $aBank -銀行帳號: $aAccount -戶名: $_aName - $_aId -匯出款: $_money -備註: $_aMemo -附言: $_aTxt -傳真: $_aFax -電郵: $_aEmail </li>";
        $_full_line .= $aCode . $aBank . $aAccount . $_aName . $_aId . $_aMoney . $_aMemo . $_aTxt . $_aFax . $_aEmail . "\n";
        //echo $_full_line."\n";
    }

    // 更新資料庫出款狀態
    $_date = date("Y-m-d H:i:s");
    //------------------------------------------
    //$update = "update tBankTrans set tExport='1' , tExport_time='$_date' , tExport_nu='$_uid' where tId='".$rs->fields["tId"]."'";
    $update = '
		UPDATE
			tBankTrans
		SET
			tExport="1",
			tExport_time="' . $_date . '",
			tExport_nu="' . $_uid . '",
			tBankLoansDate="' . $dt . '"
		WHERE
			tId="' . $rs->fields['tId'] . '";
	';
    $conn->Execute($update);
    //------------------------------------------
    if ($book_check == 0) { //非一般指示書
        $sql = "UPDATE tBankTrankBook SET bExport_nu ='" . $_uid . "',bMoney ='" . $rs->fields["tMoney"] . "' WHERE bBankTranId = '" . $rs->fields['tId'] . "'";

        $conn->Execute($sql);

    }

    //有出款保證費
    if($rs->fields['tKind'] == '保證費' and $rs->fields['tObjKind'] != '履保費先收(結案回饋)' ) {
        $sql = 'SELECT c.cFeedbackDate, c.cBankRelay, p.fDetail 
                FROM `tContractCase` AS c LEFT JOIN tFeedBackMoneyPayByCase AS p ON c.cCertifiedId = p.fCertifiedId 
                WHERE cCertifiedId = "' . $rs->fields['tMemo'] . '"';
        $res  = $conn->Execute($sql);
        //正常案件 沒有壓回饋日期
        if($res->fields["cBankRelay"] == 'N') {
            //沒有回饋給代書 或 回饋0元
            if($res->fields["fDetail"] == null or json_decode($res->fields["fDetail"])->total == 0) {
                $sql = "UPDATE `tContractCase` SET cFeedbackDate = '".$dt."' WHERE cCertifiedId = '".$rs->fields['tMemo']."' AND cFeedbackDate IS NULL";
                $conn->Execute($sql);
            }
        }
    }

    //點交結案 有選擇 [結案]
    if($rs->fields['tInvoice'] != null) {
        $sql = 'SELECT c.cFeedbackDate, c.cBankRelay, c.cBankList, p.fDetail 
                FROM `tContractCase` AS c LEFT JOIN tFeedBackMoneyPayByCase AS p ON c.cCertifiedId = p.fCertifiedId 
                WHERE cCertifiedId = "' . $rs->fields['tMemo'] . '"';
        $res  = $conn->Execute($sql);
        //正常案件 沒有壓回饋日期
        if(in_array($res->fields["cBankRelay"], ['Y', 'C'])) {
            //沒有回饋給代書 或 回饋0元
            if($res->fields["fDetail"] == null or json_decode($res->fields["fDetail"])->total == 0) {
                $sql = "UPDATE `tContractCase` SET cFeedbackDate = '".$dt."' WHERE cCertifiedId = '".$rs->fields['tMemo']."' AND cFeedbackDate IS NULL";
                $conn->Execute($sql);

                if($res->fields["cBankList"] == '') {
                    $sql  = "UPDATE tContractCase SET cBankList = '" . $dt . "' WHERE cBankRelay = 'Y' AND cCertifiedId = '".$rs->fields['tMemo']."'";
                    $conn->Execute($sql);
                }
            }
        }
    }
    $rs->MoveNext();
}
if ($book_check == 1) {
    $sql = 'SELECT SUM(tMoney) AS totalmoney,COUNT(tId) AS cou FROM tBankTrans WHERE tExport_nu="' . $_uid . '" ORDER BY tId ASC;';
    $rs  = $conn->Execute($sql);

    $sql = "INSERT INTO tBankTrankBook (bExport_nu,bMoney,bCount,bCategory,bBank,bCreatorId,bCreatName,bCreatTime) VALUES('" . $_uid . "','" . $rs->fields["totalmoney"] . "','" . $rs->fields['cou'] . "','" . $bCategory . "','" . $x . "','" . $_SESSION['member_id'] . "','" . $_SESSION['member_name'] . "','" . date('Y-m-d H:i:s') . "')";
    // echo $sql;
    $conn->Execute($sql);
}

//一般

echo "</ul>";

//編碼檔案名稱
$bank_ide = '';

if ($x == 1) { //一銀
    $bank_ide = '';
} else if ($x == 4) { //永豐西門
    $bank_ide = "sinopac_XM_";
} else if ($x == 5) { //台新
    //更新台新每日流水序號
    // $update = 'SELECT * FROM tTaishinSN WHERE tDate="'.date("Ymd").'";' ;
    // $update_rs = $conn->Execute($update) ;

    // if ($update_rs->RecordCount() > 0) {
    //     $update = 'UPDATE tTaishinSN SET tSN="'.$last_sn.'" WHERE tDate="'.date("Ymd").'";' ;
    // }
    // else {
    //     $update = 'INSERT INTO tTaishinSN (tDate,tSN) VALUES ("'.date("Ymd").'","'.$last_sn.'") ;' ;
    // }
    // $conn->Execute($update) ;
    ##

    //修改台新媒體檔名
    $bank_ide = "taishin_";
    ##
} else if ($x == 6) { //永豐城中
    $bank_ide = "sinopac_CC_";
} else { //其他銀行
    $bank_ide = $conBank['cBankAlias'] . '_';
}

$id    = $bank_ide . "export";
$_date = date("Ymd_His");

$dt_tmp = preg_replace("/-/", "", $dt);
if (preg_match("/^$dt_tmp/", $_date)) {
    $_file = $id . '_' . $_date . '.txt';
}
//上傳日=銀行放款日
else {
    $_file = $id . '_' . $_date . '_' . $dt_tmp . '.txt';
}

##

//寫入檔案
$web_addr = preg_replace("/http\:\/\//", "", $web_addr);

// $filename = '/home/httpd/html/'.$web_addr.'/bank/output/'.$_file ;
$filename = 'output/' . $_file;
$dl_file  = 'output/' . $_file;

$txt = iconv("utf-8", "big5", $_full_line);
//$txt = preg_replace("/\n/","\r\n",$txt) ;

$handle = fopen($filename, 'w+');
fwrite($handle, $txt);
fclose($handle);
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>媒體檔匯出</title>
</head>
<body>
<a href="<?php echo $dl_file; ?>">下載媒體檔 - <?php echo $_file; ?></a>
</body>
</html>