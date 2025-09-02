<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/bank/report/calTax.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$_POST                  = escapeStr($_POST);
$type                   = $_POST['type'];
$cid                    = $_POST['cid'];
$close                  = isset($_POST['close']) ? $_POST['close'] : '';
$branch                 = isset($_POST['branch']) ? $_POST['branch'] : '';
$branch1                = isset($_POST['branch1']) ? $_POST['branch1'] : '';
$branch2                = isset($_POST['branch2']) ? $_POST['branch2'] : '';
$jZip                   = isset($_POST['zip']) ? $_POST['zip'] : '';
$jAddr                  = isset($_POST['addr']) ? $_POST['addr'] : '';
$num                    = isset($_POST['num']) ? $_POST['num'] : '';
$sId                    = isset($_POST['sId']) ? $_POST['sId'] : '';
$feedBackScrivenerClose = isset($_POST['feedBackScrivenerClose']) ? $_POST['feedBackScrivenerClose'] : '';

switch ($type) {
    case 'others': //檢查其他買賣方資料
        $msg = tContractOther($conn, $cid);
        break; //檢查發票是否關閉
    case 'invoiceclose':
        $msg = InvoiceClose($cid, $type, $close);
        break;
    case 'scrivenerClose':
        $msg = scrivenerClose($cid, $feedBackScrivenerClose);
        break;
    case 'case3': //仲介店儲存案件低於三個的跳出提醒視窗
        $msg = checkCase3($cid, $branch, $branch1, $branch2);
        break;
    case 'AddedTaxMoney':
        $msg = calCase($cid);
        break;
    case 'checkaddr':

        $msg = checkAddr($jZip, $jAddr, $cid, $num);
        break;
    case 'ScrCaseCount':

        $msg = checkScirvnerCaseCount($sId);
        break;

    default:
        # code...
        break;
}
echo trim($msg);
die();

###################

function checkAddr($zip, $addr, $cid, $num)
{
    global $conn;

    $str = ""; // 初始化變數

    if ($cid != '') {
        $str .= " AND cc.cCertifiedId != '" . $cid . "'";
    }

    // if ($num != 0 && $num != 'new') {
    $str .= "  AND (cc.cCaseStatus = 2 OR cc.cCaseStatus = 6)";
    // }

    $msg = 'ok';
    if ($zip != '' && $addr != '') {
        $sql = "SELECT cc.cCertifiedId FROM tContractProperty AS cp LEFT JOIN tContractCase AS cc ON cp.cCertifiedId = cc.cCertifiedId WHERE  cp.cZip ='" . $zip . "' AND cp.cAddr ='" . $addr . "'" . $str . " ORDER BY cc.cSignDate DESC";

        $rs = $conn->Execute($sql);

        $total = $rs->RecordCount();
        if ($total > 0) {
            $msg = $rs->fields['cCertifiedId'];
        }
    }

    return $msg;

}

//1:不一樣 2:相同
function InvoiceClose($cid, $type, $close)
{
    global $conn;

    $sql = "SELECT cInvoiceClose FROM tContractCase WHERE cCertifiedId = '" . $cid . "'";
    // echo $sql;

    $rs = $conn->Execute($sql);

    // echo $close;

    if (($rs->fields['cInvoiceClose'] != $close) && $close == 'N') { //使用者畫面未關閉發票編輯權限，但會計已關閉發票編輯權限
        $msg = 'error';
    } else {
        $msg = 'ok';
    }
    return $msg;
}
function scrivenerClose($cid, $feedBackScrivenerClose)
{
    global $conn;

    $sql = "SELECT cFeedBackScrivenerClose FROM tContractCase WHERE cCertifiedId = '" . $cid . "'";
    $rs  = $conn->Execute($sql);

    if (($rs->fields['cFeedBackScrivenerClose'] != $feedBackScrivenerClose) && $feedBackScrivenerClose == 0) { //使用者畫面未關閉發票編輯權限，但會計已關閉發票編輯權限
        return 'error';
    }
    return 'ok';
}

//msg=1:買方資料不齊全,msg=2:賣方資料不齊全
function tContractOther($conn, $cid)
{

    $contract        = new Contract();
    $data_otherbuyer = $contract->GetOthers($cid, 1); //買
    $ck              = 0;                             //0:正確 1:買方錯誤 2:賣方錯誤

    for ($i = 0; $i < count($data_otherbuyer); $i++) {
        // $data_otherbuyer[$i]['cIdentifyId'] = 'AA20060243';

        if (! checkUID($data_otherbuyer[$i]['cIdentifyId'])) {
            $ck = 1;
        } elseif ($data_otherbuyer[$i]['cRegistZip'] == '') {
            $ck = 1;
        } elseif ($data_otherbuyer[$i]['cRegistAddr'] == '') {
            $ck = 1;
        } elseif ($data_otherbuyer[$i]['cBaseZip'] == '') {
            $ck = 1;
        } elseif ($data_otherbuyer[$i]['cBaseAddr'] == '') {
            $ck = 1;
        }

    }

    $data_otherowner = $contract->GetOthers($cid, 2); //賣

    for ($i = 0; $i < count($data_otherowner); $i++) {
        // $data_otherbuyer[$i]['cIdentifyId'] = 'AA20060243';

        if (! checkUID($data_otherowner[$i]['cIdentifyId'])) {
            $ck = 2;
        } elseif ($data_otherowner[$i]['cRegistZip'] == '') {
            $ck = 2;
        } elseif ($data_otherowner[$i]['cRegistAddr'] == '') {
            $ck = 2;
        } elseif ($data_otherowner[$i]['cBaseZip'] == '') {
            $ck = 2;
        } elseif ($data_otherowner[$i]['cBaseAddr'] == '') {
            $ck = 2;
        }

    }

    return $ck;
}

//仲介店儲存案件低於三個的跳出提醒視窗
//20180615 仲介店儲存案件低於三個且該案件服務費50萬以上要提醒(該案件出過服務費不用提醒)
function checkServiceMoney($cid, $branch, $target)
{
    global $conn;
    $check = 0; //過 //tBuyer // tBuyer

    if ($target == 2) { //1.買賣方、2.賣方、3.買方
        $str = " AND tSeller > 0";
    } elseif ($target == 3) {
        $str = " AND tBuyer > 0";
    } else {
        $str = " AND (tSeller > 0 OR tBuyer > 0)";
    }
    $sql = "SELECT tObjKind FROM tBankTrans WHERE tMemo = '" . $cid . "' AND tObjKind = '仲介服務費'" . $str;
    $rs  = $conn->Execute($sql);

    $total = $rs->RecordCount();
                      // echo $total;
    if ($total > 0) { // 有出過服務費不用提醒
        return false;
    }

    $sql = "SELECT cRealestateMoneyBuyer,cRealestateMoney FROM tContractExpenditure WHERE cCertifiedId = '" . $cid . "'";
    $rs  = $conn->Execute($sql);

    if ($target == 2) { //1.買賣方、2.賣方、3.買方
        if ($rs->fields['cRealestateMoney'] >= 500000) {
            return true;
        }
    } elseif ($target == 3) {
        if ($rs->fields['cRealestateMoneyBuyer'] >= 500000) {
            return true;
        }
    } else {
        if ($rs->fields['cRealestateMoney'] >= 500000 || $rs->fields['cRealestateMoneyBuyer'] >= 500000) {
            return true;
        }
    }

    return false;
}
function checkCase3($cid, $bId, $bId1, $bId2)
{
    global $conn;

    // echo $bId."_".$bId1."_".$bId2."<br>";

    if ($bId > 0) {
        $sql = "SELECT
					COUNT(cId) AS total,
					(SELECT bStore FROM tBranch AS b WHERE b.bId='" . $bId . "') AS Name,
					(SELECT (SELECT bName FROM tBrand AS c WHERE c.bId=b.bBrand) AS brand FROM tBranch AS b WHERE b.bId='" . $bId . "') AS brand,
					(SELECT b.bBrand FROM tBranch AS b WHERE b.bId='" . $bId . "') AS brandId,
					cServiceTarget AS cServiceTarget
				FROM
					tContractRealestate WHERE cBranchNum = '" . $bId . "' OR cBranchNum1 = '" . $bId . "' OR cBranchNum2 = '" . $bId . "'";
        $rs     = $conn->Execute($sql);
        $branch = $rs->fields;
        if ($branch['total'] <= 3 && ($branch['brandId'] != 1 && $branch['brandId'] != 49)) {

            if (checkServiceMoney($cid, $bId, $branch['cServiceTarget'])) { // 如果是true 就要顯示
                $msg .= $branch['brand'] . $branch['Name'] . " 辦理案件尚未超過三件且應付仲介費服務費大於50萬,服務費出款需依照服務費確認單辦理出款(1)\r\n";
            }

        }

    }

    if ($bId1 > 0) {
        $sql = "SELECT
					COUNT(cId) AS total,
					(SELECT bStore FROM tBranch AS b WHERE b.bId='" . $bId1 . "') AS Name,
					(SELECT (SELECT bName FROM tBrand AS c WHERE c.bId=b.bBrand) AS brand FROM tBranch AS b WHERE b.bId='" . $bId1 . "') AS brand,
					(SELECT b.bBrand FROM tBranch AS b WHERE b.bId='" . $bId1 . "') AS brandId,
					cServiceTarget1 AS cServiceTarget
				FROM tContractRealestate WHERE cBranchNum = '" . $bId1 . "' OR cBranchNum1 = '" . $bId1 . "' OR cBranchNum2 = '" . $bId1 . "'";
        $rs      = $conn->Execute($sql);
        $branch1 = $rs->fields;
        if ($branch1['total'] <= 3 && ($branch1['brandId'] != 1 && $branch1['brandId'] != 49)) {
            $msg .= $branch1['brand'] . $branch1['Name'] . " 辦理案件尚未超過三件且應付仲介費服務費大於50萬,服務費出款需依照服務費確認單辦理出款(2)\r\n";
        }
    }

    if ($bId2 > 0) {
        $sql = "SELECT
					COUNT(cId) AS total,
					(SELECT bStore FROM tBranch AS b WHERE b.bId='" . $bId2 . "') AS Name,
					(SELECT (SELECT bName FROM tBrand AS c WHERE c.bId=b.bBrand) AS brand FROM tBranch AS b WHERE b.bId='" . $bId2 . "') AS brand,
					(SELECT b.bBrand FROM tBranch AS b WHERE b.bId='" . $bId2 . "') AS brandId,
					cServiceTarget2 AS cServiceTarget
				FROM tContractRealestate WHERE cBranchNum = '" . $bId2 . "' OR cBranchNum1 = '" . $bId2 . "' OR cBranchNum2 = '" . $bId2 . "'";
        $rs      = $conn->Execute($sql);
        $branch2 = $rs->fields;
        if ($branch2['total'] <= 3 && ($branch2['brandId'] != 1 && $branch2['brandId'] != 49)) {
            $msg .= $branch2['brand'] . $branch2['Name'] . " 辦理案件尚未超過三件且應付仲介費服務費大於50萬,服務費出款需依照服務費確認單辦理出款(3)\r\n";
        }
    }

    // echo $count."_".$count2."_".$count3;
    // "仲介品牌+仲介店名 辦理案件尚未超過三件,服務費出款需依照服務費確認單辦理出款"

    return $msg;
}

//近三個月申請剩餘份數(土/00份 建物/00份)
function checkScirvnerCaseCount($sId)
{
    global $conn;

    $last = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1)) . " 00:00:00";

    $sql = "SELECT
				s.sName,
				CONCAT('SC',LPAD(s.sId,4,'0')) as Code,
				Count(case when (bc.bApplication = 1) then 1 else null end) as land,
				Count(case when (bc.bApplication = 2) then 1 else null end) as build,
				Count(case when (bc.bApplication = 3) then 1 else null end) as building

			FROM
				tBankCode AS bc
			LEFT JOIN
				tScrivener AS s ON s.sId = bc.bSID
			WHERE
				s.sId ='" . $sId . "' AND bc.bUsed = 0 AND bc.bDel = 'n' AND bc.bCreateDate >= '" . $last . "'";

    $rs = $conn->Execute($sql);

    $txt = $rs->fields['sName'] . "(" . $rs->fields['Code'] . ")近一年申請剩餘份數(土/" . $rs->fields['land'] . "份 建物/" . $rs->fields['build'] . "份)";

    // $fw = fopen('/home/httpd/html/first.twhg.com.tw/log2/showCaseCount.log', 'a+');
    // $fw = fopen('/home/httpd/html/first2.twhg.com.tw/log2/showCaseCount.log', 'a+');

    // fwrite($fw,$_SESSION['member_id'].'_'.$txt);
    // fclose($fw);
    return $txt;
}

#################################################################
function checkUID($sn)
{
    $result = false;

    if (mb_strlen($sn) == 8) { //檢查統一編號
        $result = UNID($sn);

    } else if (mb_strlen($sn) == 10) {

        $sn = strtoupper($sn); //將英文字母設定為大寫

        $reg  = "/^[A-Z]{1}[A-D]{1}[0-9]{8}$/";
        $reg1 = "/^[0-9]{8}[A-Z]{1}[A-Z]{1}$/";

        if (preg_match($reg, $sn)) { //檢查居留證字號

            $result = RID($sn);
                                                 // $result = true;
        } else if (preg_match($reg1, $sn)) { //未住滿183天的非大陸民眾(第一~八碼採護照西元年出生日;第九~十碼彩護照內英文姓名第一個字之前2個字母)

            $result = true;
        } else { //檢查身分證字號
            $result = PID($sn);
        }
    } else if (mb_strlen($sn) == 7) { //未住滿183天的大陸民眾(第一碼為9;第二到七碼為西元出生年之後2位及月日各2位)
        $reg2 = "/^[0-9]{7}$/";
        if (preg_match($reg2, $sn)) {

            $result = true;
        }
    }

    return $result;
}

/* 統一編號檢核 */
function UNID($sn)
{
    $cx  = [1, 2, 1, 2, 1, 2, 4, 1]; //驗算基數
    $sum = 0;
    if (mb_strlen($sn) != 8) {
        // echo "統編錯誤，要有 8 個數字";
        return false;
    }

    $cnum[0] = substr($sn, 0, 1);
    $cnum[1] = substr($sn, 1, 1);
    $cnum[2] = substr($sn, 2, 1);
    $cnum[3] = substr($sn, 3, 1);
    $cnum[4] = substr($sn, 4, 1);
    $cnum[5] = substr($sn, 5, 1);
    $cnum[6] = substr($sn, 6, 1);
    $cnum[7] = substr($sn, 7, 1);
    for ($i = 0; $i <= 7; $i++) {
        if (ord($cnum[$i]) < 48 || ord($cnum[$i]) > 57) {

            // echo "統編錯誤，要有 8 個 0-9 數字組合";
            return false;
        }
        $sum += cc($cnum[$i] * $cx[$i]); //加總運算碼結果
    }

    if ($sum % 10 == 0) {

        // echo "統一編號：".$sn." 正確!";
        return true;
    } else if ($cnum[6] == 7 && ($sum + 1) % 10 == 0) {
        // echo "統一編號：".$sn." 正確!";
        return true;
    } else {
        // echo "統一編號：".$sn." 錯誤!";
        return false;
    }
}
////
/* 計算數字大於 10 之處理 */
function cc($n)
{
    if ($n > 9) {
        $s = $n+"";

        $n1 = substr($s, 0, 1) * 1;
        $n2 = substr($s, 1, 2) * 1;
        $n  = $n1 + $n2;
    }
    return $n;
}

function PID($sn)
{
    // echo $sn;
    /* 定義字母對應的數字 */
    $a        = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $b        = ['10', '11', '12', '13', '14', '15', '16', '17', '34', '18', '19', '20', '21', '22', '35', '23', '24', '25', '26', '27', '28', '29', '32', '30', '31', '33'];
    $max      = count($a);
    $alphabet = [];
    for ($i = 0; $i < $max; $i++) {
        $alphabet[$i] = [$a[$i], $b[$i]];
    }
    ////

    $sn    = strtoupper($sn); //將英文字母設定為大寫;
    $snLen = mb_strlen($sn);  //計算字數長度

    /* 若號碼長度不等於10，代表輸入長度不合格式 */
    if ($snLen != 10) {
        //alert('輸入字號長度不正確!!') ;
        return false;
    }
    ////

    /* 取出第一個英文字母 */
    $ch    = substr($sn, 0, 1);
    $chVal = '';
    for ($i = 0; $i < $max; $i++) {
        if ($alphabet[$i][0] == $ch) {
            $chVal = $alphabet[$i][1];
            break;
        }
    }
    ////

    /* 取出檢查碼 */
    $lastch = substr($sn, -1, 1);
    ////

    $ch1 = substr($chVal, 0, 1); //十位數
    $ch2 = substr($chVal, 1, 1); //個位數

    $_val = ($ch2 * 9) + $ch1; //個位數 x 9 再加上十位數
    $_val = $_val % 10;        //除以10取餘數

    /* 計算檢核碼 */
    $t1 = $_val * 1;
    $t2 = substr($sn, 1, 1) * 8;
    $t3 = substr($sn, 2, 1) * 7;
    $t4 = substr($sn, 3, 1) * 6;
    $t5 = substr($sn, 4, 1) * 5;
    $t6 = substr($sn, 5, 1) * 4;
    $t7 = substr($sn, 6, 1) * 3;
    $t8 = substr($sn, 7, 1) * 2;
    $t9 = substr($sn, 8, 1) * 1;

    $checkCode = ($t1 + $t2 + $t3 + $t4 + $t5 + $t6 + $t7 + $t8 + $t9) % 10;
    if ($checkCode == 0) {
        $checkCode = 0;
    } else {
        $checkCode = 10 - $checkCode; //檢查碼
    }
    ////
    // echo $checkCode."-".$lastch."\r\n";
    /* 比對檢核碼是否相符 */
    if ($checkCode == $lastch) {
        //alert('checkCode=' + checkCode) ;
        return true;
    } else {
        //alert('checkCode<>' + checkCode) ;
        return false;
    }
    ////
}

/* 居留證字號檢核 */
function RID($sn)
{
    /* 定義字母對應的數字 */
    $a        = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $b        = ['10', '11', '12', '13', '14', '15', '16', '17', '34', '18', '19', '20', '21', '22', '35', '23', '24', '25', '26', '27', '28', '29', '32', '30', '31', '33'];
    $max      = count($a);
    $alphabet = [];
    for ($i = 0; $i < $max; $i++) {
        $alphabet[$i] = [$a[$i], $b[$i]];
    }
    ////

    $sn    = strtoupper($sn); //將英文字母設定為大寫
    $snLen = mb_strlen($sn);  //計算字數長度

    /* 若號碼長度不等於10，代表輸入長度不合格式 */
    if ($snLen != 10) {
        //alert('輸入字號長度不正確!!') ;
        return false;
    }
    ////

    /* 取出英文字母 */
    $ch1    = substr($sn, 0, 1); //
    $ch2    = substr($sn, 1, 1); //
    $chVal1 = '';
    $chVal2 = '';
    for ($i = 0; $i < $max; $i++) {
        /* 取出第一個英文字母對應的數值 */
        if ($alphabet[$i][0] == $ch1) {
            $chVal1 = $alphabet[$i][1];
            //break ;
        }
        ////

        /* 取出第二個英文字母對應的數值 */
        if ($alphabet[$i][0] == $ch2) {
            $chVal2 = $alphabet[$i][1];
            //break ;
        }
        ////
    }
    ////

    /* 取出檢查碼 */
    $lastch = substr($sn, -1, 1);
    ////

    /* 第一碼英文字的轉換 */
    $ch1 = substr($chVal1, 0, 1); //十位數
    $ch2 = substr($chVal1, 1, 1); //個位數
    $t0  = ($ch2 * 9 + $ch1) % 10;
    ////

    /* 第二碼英文字的轉換 */
    $_val = substr($chVal2, -1, 1) * 1; //個位數
                                        ////

    /* 計算檢核碼 */
    $t1 = $_val * 8;
    $t2 = substr($sn, 2, 1) * 7;
    $t3 = substr($sn, 3, 1) * 6;
    $t4 = substr($sn, 4, 1) * 5;
    $t5 = substr($sn, 5, 1) * 4;
    $t6 = substr($sn, 6, 1) * 3;
    $t7 = substr($sn, 7, 1) * 2;
    $t8 = substr($sn, 8, 1) * 1;

    $checkCode = $t0 + $t1 + $t2 + $t3 + $t4 + $t5 + $t6 + $t7 + $t8 + 1 - 1;

    $checkCode = 10 - ($checkCode % 10);

    if (($checkCode % 10) == 0) {
        $checkCode = 0;
    }

    /* 比對檢核碼是否相符 */
    if ($checkCode == $lastch) {
        return true;
    } else {
        return false;
    }
    ////
}
###################################################################3
