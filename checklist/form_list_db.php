<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once __DIR__ . '/writelog.php';
require_once dirname(__DIR__) . '/class/traits/PayTax.traits.php';


class payTax
{
    use \First1\V1\Util\PayTax;
}
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

// 計算稅額
function calculate($_id, $_int = 0)
{
    $_len = strlen($_id); // 個人10碼 公司8碼

    if (preg_match("/[A-Za-z]{2}/", $_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/", $_id)) { //外國籍自然人(一般民眾)
        $_o   = 1; // 外國籍自然人(一般民眾)
        $_tax = 0.2; // 稅率：20%

    } else if ($_len == '10') { // 個人10碼
        if (preg_match("/[A-Za-z]{2}/", $_id)) { // 判別是否為外國人(兩碼英文字母者)
            $_o   = 1; // 外國籍自然人(一般民眾)
            $_tax = 0.2; // 稅率：20%
        } else {
            $_o   = 2; // 本國籍自然人(一般民眾)
            $_tax = 0.1; // 稅率：10%
        }
    } else if ($_len == '8') { // 公司8碼
        $_o   = 2; // 本國籍法人(公司)
        $_tax = 0.1; // 稅率：10%
    } else if ($_len == '7') {
        if (preg_match("/^9[0-9]{6}$/", $_id)) { // 判別是否為外國人
            $_o   = 1; // 外國籍自然人(一般民眾)
            $_tax = 0.2; // 稅率：20%
        }
    }

    if ($_o == "1") {
        $cTax = round($_int * $_tax);
    } else if ($_o == "2") {
        $cTax = 0;
        if ($_int > 20000) {
            $cTax = round($_int * $_tax);
        }
    }

    return $cTax;
}

//計算二代健保稅額 2016/01/15改1.91%(0.0191) //2021/01/01 調整為2.11%(0.0211)
function NHITax($_id, $_ide, $_int = 0)
{
    $NHI = 0;
    if (preg_match("/\w{10}/", $_id)) { // 若為自然人身分(10碼)則需要代扣 NHI2 稅額        20150303改為只要自然人就要代扣健保補充保費
        if ($_int >= 20000) { // 若餘額大於等於 5000, 105/01/01起額度改為20000
            $NHI = round($_int * 0.0211); // 則代扣 2% 保費 2016/01/15改1.91%(0.0191)
        }
    }
    return $NHI;
}

//取得仲介銀行資料
function get_realty($_link, $cId, $tg, $_no = 0)
{
    global $conn;
    if ($_no != '0') {
        $_realty = array();

        $_sql = '
			SELECT
				*
			FROM
				tBranch
			WHERE
				bId="' . $_no . '"
		';
        $rs = $conn->Execute($_sql);

        $bServiceOrderHas = ($rs->fields['bServiceOrderHas'] == 1) ? 0 : 1;
        $i                = 0;
        if ($tg == '2') {
            if ($rs->fields['bAccountUnused'] != 1) {
                $_realty[$i]['cCertifiedId']     = $cId;
                $_realty[$i]['cIdentity']        = '32';
                $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum1'];
                $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum2'];
                $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount3'];
                $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount4'];
                $_realty[$i]['cHide']            = $bServiceOrderHas;
                $_realty[$i]['cOrder']           = '8';
                $i++;
            }

            if ($rs->fields['bAccount31']) {
                if ($rs->fields['bAccountUnused1'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '32';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum11'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum21'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount31'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount41'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }

            if ($rs->fields['bAccount32']) {
                if ($rs->fields['bAccountUnused2'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '32';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum12'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum22'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount32'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount42'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }

            if ($rs->fields['bAccount33']) {
                if ($rs->fields['bAccountUnused3'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '32';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum13'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum23'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount33'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount43'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }
        } else if ($tg == '3') {
            if ($rs->fields['bAccountUnused'] != 1) {
                $_realty[$i]['cCertifiedId']     = $cId;
                $_realty[$i]['cIdentity']        = '33';
                $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum1'];
                $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum2'];
                $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount3'];
                $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount4'];
                $_realty[$i]['cHide']            = $bServiceOrderHas;
                $_realty[$i]['cOrder']           = '9';
                $i++;
            }

            if ($rs->fields['bAccount31']) {
                if ($rs->fields['bAccountUnused1'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '33';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum11'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum21'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount31'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount41'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '9';
                    $i++;
                }
            }

            if ($rs->fields['bAccount32']) {
                if ($rs->fields['bAccountUnused2'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '33';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum12'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum22'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount32'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount42'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }

            if ($rs->fields['bAccount33']) {
                if ($rs->fields['bAccountUnused3'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '33';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum13'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum23'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount33'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount43'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }
        } else {
            if ($rs->fields['bAccountUnused'] != 1) {
                $_realty[$i]['cCertifiedId']     = $cId;
                $_realty[$i]['cIdentity']        = '32';
                $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum1'];
                $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum2'];
                $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount3'];
                $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount4'];
                $_realty[$i]['cHide']            = $bServiceOrderHas;
                $_realty[$i]['cOrder']           = '8';
                $i++;
            }

            if ($rs->fields['bAccount31']) {
                if ($rs->fields['bAccountUnused1'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '32';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum11'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum21'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount31'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount41'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }

            if ($rs->fields['bAccount32']) {
                if ($rs->fields['bAccountUnused2'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '32';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum12'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum22'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount32'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount42'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }

            if ($rs->fields['bAccount33']) {
                if ($rs->fields['bAccountUnused3'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '32';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum13'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum23'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount33'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount43'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }

            #########
            if ($rs->fields['bAccountUnused'] != 1) {
                $_realty[$i]['cCertifiedId']     = $cId;
                $_realty[$i]['cIdentity']        = '33';
                $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum1'];
                $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum2'];
                $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount3'];
                $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount4'];
                $_realty[$i]['cHide']            = $bServiceOrderHas;
                $_realty[$i]['cOrder']           = '9';
                $i++;
            }

            if ($rs->fields['bAccount31']) {
                if ($rs->fields['bAccountUnused1'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '33';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum11'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum21'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount31'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount41'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '9';
                    $i++;
                }
            }

            if ($rs->fields['bAccount32']) {
                if ($rs->fields['bAccountUnused2'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '33';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum12'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum22'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount32'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount42'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }

            if ($rs->fields['bAccount33']) {
                if ($rs->fields['bAccountUnused3'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = '33';
                    $_realty[$i]['cBankMain']        = $rs->fields['bAccountNum13'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bAccountNum23'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bAccount33'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bAccount43'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = '8';
                    $i++;
                }
            }
        }

        $sql = "SELECT * FROM tBranchBank WHERE bBranch = '" . $_no . "' ";
        $rs  = $conn->Execute($sql);
        if ($rs->RecordCount() > 0) {
            if ($tg == '2') {
                $cIdentity = '32';
                $order     = '8';
            } else if ($tg == '3') {
                $cIdentity = '33';
                $order     = '9';
            } else {
                $cIdentity  = '32';
                $order      = '9';
                $cIdentity2 = '32';
                $order2     = '9';
            }

            while (!$rs->EOF) {
                if ($rs->fields['bUnUsed'] != 1) {
                    $_realty[$i]['cCertifiedId']     = $cId;
                    $_realty[$i]['cIdentity']        = $cIdentity;
                    $_realty[$i]['cBankMain']        = $rs->fields['bBankMain'];
                    $_realty[$i]['cBankBranch']      = $rs->fields['bBankBranch'];
                    $_realty[$i]['cBankAccountNo']   = $rs->fields['bBankAccountNo'];
                    $_realty[$i]['cBankAccountName'] = $rs->fields['bBankAccountName'];
                    $_realty[$i]['cHide']            = $bServiceOrderHas;
                    $_realty[$i]['cOrder']           = $order;
                    $i++;

                    if ($cIdentity2) {
                        $_realty[$i]['cCertifiedId']     = $cId;
                        $_realty[$i]['cIdentity']        = $cIdentity2;
                        $_realty[$i]['cBankMain']        = $rs->fields['bBankMain'];
                        $_realty[$i]['cBankBranch']      = $rs->fields['bBankBranch'];
                        $_realty[$i]['cBankAccountNo']   = $rs->fields['bBankAccountNo'];
                        $_realty[$i]['cBankAccountName'] = $rs->fields['bBankAccountName'];
                        $_realty[$i]['cHide']            = $bServiceOrderHas;
                        $_realty[$i]['cOrder']           = $order2;
                        $i++;
                    }
                }

                $rs->MoveNext();
            }
        }
    }

    return $_realty;
}
##

//更新 ChecklistBank 資料表
function updateBank($identer)
{
    global $conn;

    for ($i = 0; $i < count($identer); $i++) {
        if (empty($identer[$i]['cHide'])) {
            $identer[$i]['cHide'] = 0;
        }
        $_sql = '
			INSERT INTO
				tChecklistBank
				(
					cCertifiedId,
					cIdentity,
					cBankMain,
					cBankBranch,
					cBankAccountNo,
					cBankAccountName,
					cOrder,
					cMoney,
					cHide

				)
			VALUES
				(
					"' . $identer[$i]['cCertifiedId'] . '",
					"' . $identer[$i]['cIdentity'] . '",
					"' . $identer[$i]['cBankMain'] . '",
					"' . $identer[$i]['cBankBranch'] . '",
					"' . $identer[$i]['cBankAccountNo'] . '",
					"' . $identer[$i]['cBankAccountName'] . '",
					"' . $identer[$i]['cOrder'] . '",
					"' . $identer[$i]['cBankMoney'] . '",
					"' . $identer[$i]['cHide'] . '"
				) ;
		';
        $conn->Execute($_sql);
    }
}
##

$cCertifiedId = $_REQUEST['cCertifiedId'];

//依據保證號碼找到14碼完整保證號碼
$sql      = 'SELECT cEscrowBankAccount FROM tContractCase WHERE cCertifiedId="' . $cCertifiedId . '";';
$rs       = $conn->Execute($sql);
$tVR_Code = $rs->fields['cEscrowBankAccount'];
unset($tmp);
unset($rel);
##

$last_modify = date("Ymd.His");

// ======================= 買賣方 ===================================
$sql = 'SELECT * FROM tChecklist WHERE cCertifiedId="' . $cCertifiedId . '";';
$rs  = $conn->Execute($sql);
$max = $rs->RecordCount();

$ck = $max;

if (!$max) {
    $sql = '
	SELECT
		cas.cCertifiedId as cCertifiedId,
		cas.cInvoiceClose as cInvoiceClose,
		(SELECT sName FROM tScrivener WHERE sId=csc.cScrivener) as cScrivener,
		buy.cName as cBuyer,
		buy.cIdentifyId as cBuyerId,
		buy.cNHITax as cBuyerNHITax,
		buy.cResidentLimit as cBuyerResidentLimit,
		buy.cChecklistBank AS buyerCB,
		own.cName as cOwner,
		own.cIdentifyId as cOwnerId,
		own.cNHITax as cOwnerNHITax,
		own.cResidentLimit as cOwnerResidentLimit,
		own.cChecklistBank AS ownerCB,
		rea.cBrand AS BrandId,
		rea.cBrand1 AS BrandId1,
		rea.cBrand2 AS BrandId2,
		rea.cBrand3 AS BrandId3,
		rea.cBranchNum,
		rea.cBranchNum1,
		rea.cBranchNum2,
		rea.cBranchNum3,
		(SELECT bName FROM tBrand WHERE bId=rea.cBrand) as cBrand,
		(SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum AND bBrand=rea.cBrand) as cStore,
		(SELECT bName FROM tBrand WHERE bId=rea.cBrand1) as cBrand1,
		(SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum1 AND bBrand=rea.cBrand1) as cStore1,
		(SELECT bName FROM tBrand WHERE bId=rea.cBrand2) as cBrand2,
		(SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum2 AND bBrand=rea.cBrand2) as cStore2,
		(SELECT bName FROM tBrand WHERE bId=rea.cBrand3) as cBrand3,
		(SELECT bStore FROM tBranch WHERE bId=rea.cBranchNum3 AND bBrand=rea.cBrand3) as cStore3,
		rea.cServiceTarget,
		rea.cServiceTarget1,
		rea.cServiceTarget2,
		rea.cServiceTarget3,
		inc.cTotalMoney as cTotalMoney,
		inc.cNotIntoMoney as cNotIntoMoney,
		inc.cReasonCategory,
		own.cMoney2 as cCompensation,
		own.cMoney3 as cCompensation2,
		own.cMoney4 as cCompensation3,
		own.cMoney5 as cCompensation4,
		pro.cAddr as cProperty,
		(SELECT zCity FROM tZipArea WHERE zZip=pro.cZip) as cCity,
		(SELECT zArea FROM tZipArea WHERE zZip=pro.cZip) as cArea,
		inc.cCertifiedMoney as cCertifiedMoney,
		exp.cScrivenerMoney as cScrivenerMoney,
		exp.cScrivenerMoneyBuyer as cScrivenerMoneyBuyer,
		exp.cRealestateMoney as cRealestateMoney,
		exp.cRealestateMoneyBuyer as cRealestateMoneyBuyer,
		exp.cAdvanceMoney as cAdvanceMoney,
		exp.cAdvanceMoneyBuyer as cAdvanceMoneyBuyer,
		exp.cDealMoney as cRealestateBalance,
		exp.cDealMoneyBuyer as cRealestateBalanceBuyer,
		inv.cTaxReceiptTarget as cTaxReceiptTarget
	FROM
		tContractCase AS cas
	LEFT JOIN
		tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
	LEFT JOIN
		tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
	LEFT JOIN
		tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId
	LEFT JOIN
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
	LEFT JOIN
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId
	LEFT JOIN
		tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId
	LEFT JOIN
		tContractExpenditure AS exp ON exp.cCertifiedId=cas.cCertifiedId
	LEFT JOIN
		tContractInvoice AS inv ON inv.cCertifiedId=cas.cCertifiedId
	LEFT JOIN
		tBranch AS bra ON bra.bId=rea.cBranchNum AND bra.bBrand=rea.cBrand
	WHERE
		cas.cCertifiedId="' . $cCertifiedId . '"
	';

    // 處理相關資料明細
    $rs         = $conn->Execute($sql);
    $detail     = $rs->fields;
    $store      = 0;
    $buyerBrand = '';
    $buyerStore = '';
    $ownerBrand = '';
    $ownerStore = '';

    $detail['cNote'] = ($detail['cReasonCategory'] == 1) ? 1 : 0;
    $detail['bNote'] = ($detail['cReasonCategory'] == 1) ? 1 : 0;

    //仲介店服務對象
    //服務對象：1.買賣方、2.賣方、3.買方
    //買賣方都要顯示在買OR賣上面 20181108
    if ($detail['cBranchNum']) {
        if ($detail['cServiceTarget'] == 1) {
            $buyerBrand       = $detail["cBrand"];
            $buyerBranch      = $detail["cStore"];
            $ownerBrand       = $detail["cBrand"];
            $ownerBranch      = $detail["cStore"];
            $tmpBuyerBranch[] = $detail["cBrand"] . "/" . str_replace('(待停用)', '', $detail["cStore"]);
            $tmpOwnerBranch[] = $detail["cBrand"] . "/" . str_replace('(待停用)', '', $detail["cStore"]);
        } else if ($detail['cServiceTarget'] == 2) {
            $ownerBrand       = $detail["cBrand"];
            $ownerBranch      = $detail["cStore"];
            $tmpOwnerBranch[] = $detail["cBrand"] . "/" . str_replace('(待停用)', '', $detail["cStore"]);
        } else if ($detail['cServiceTarget'] == 3) {
            $buyerBrand       = $detail["cBrand"];
            $buyerBranch      = $detail["cStore"];
            $tmpBuyerBranch[] = $detail["cBrand"] . "/" . str_replace('(待停用)', '', $detail["cStore"]);
        }
    }

    if ($detail['cBranchNum1'] > 0) {
        if ($detail['cServiceTarget1'] == 1) {
            $buyerBrand       = $detail["cBrand1"];
            $buyerBranch      = $detail["cStore1"];
            $ownerBrand       = $detail["cBrand1"];
            $ownerBranch      = $detail["cStore1"];
            $tmpBuyerBranch[] = $detail["cBrand1"] . "/" . str_replace('(待停用)', '', $detail["cStore1"]);
            $tmpOwnerBranch[] = $detail["cBrand1"] . "/" . str_replace('(待停用)', '', $detail["cStore1"]);
        } else if ($detail['cServiceTarget1'] == 2) {
            $ownerBrand       = $detail["cBrand1"];
            $ownerBranch      = $detail["cStore1"];
            $tmpOwnerBranch[] = $detail["cBrand1"] . "/" . str_replace('(待停用)', '', $detail["cStore1"]);
        } else if ($detail['cServiceTarget1'] == 3) {
            $buyerBrand       = $detail["cBrand1"];
            $buyerBranch      = $detail["cStore1"];
            $tmpBuyerBranch[] = $detail["cBrand1"] . "/" . str_replace('(待停用)', '', $detail["cStore1"]);
        }
    }

    if ($detail['cBranchNum2'] > 0) {
        if ($detail['cServiceTarget2'] == 1) {
            $buyerBrand       = $detail["cBrand2"];
            $buyerBranch      = $detail["cStore2"];
            $ownerBrand       = $detail["cBrand2"];
            $ownerBranch      = $detail["cStore2"];
            $tmpBuyerBranch[] = $detail["cBrand2"] . "/" . str_replace('(待停用)', '', $detail["cStore2"]);
            $tmpOwnerBranch[] = $detail["cBrand2"] . "/" . str_replace('(待停用)', '', $detail["cStore2"]);
        } else if ($detail['cServiceTarget2'] == 2) {
            $ownerBrand       = $detail["cBrand2"];
            $ownerBranch      = $detail["cStore2"];
            $tmpOwnerBranch[] = $detail["cBrand2"] . "/" . str_replace('(待停用)', '', $detail["cStore2"]);
        } else if ($detail['cServiceTarget2'] == 3) {
            $buyerBrand       = $detail["cBrand2"];
            $buyerBranch      = $detail["cStore2"];
            $tmpBuyerBranch[] = $detail["cBrand2"] . "/" . str_replace('(待停用)', '', $detail["cStore2"]);
        }
    }

    if ($detail['cBranchNum3'] > 0) {
        if ($detail['cFeedbackTarget3'] == 1) {
            $buyerBrand       = $detail["cBrand3"];
            $buyerBranch      = $detail["cStore3"];
            $ownerBrand       = $detail["cBrand3"];
            $ownerBranch      = $detail["cStore3"];
            $tmpBuyerBranch[] = $detail["cBrand3"] . "/" . str_replace('(待停用)', '', $detail["cStore3"]);
            $tmpOwnerBranch[] = $detail["cBrand3"] . "/" . str_replace('(待停用)', '', $detail["cStore3"]);
        } else if ($detail['cServiceTarget3'] == 2) {
            $ownerBrand       = $detail["cBrand3"];
            $ownerBranch      = $detail["cStore3"];
            $tmpOwnerBranch[] = $detail["cBrand3"] . "/" . str_replace('(待停用)', '', $detail["cStore3"]);
        } else if ($detail['cServiceTarget3'] == 3) {
            $buyerBrand       = $detail["cBrand3"];
            $buyerBranch      = $detail["cStore3"];
            $tmpBuyerBranch[] = $detail["cBrand3"] . "/" . str_replace('(待停用)', '', $detail["cStore3"]);
        }
    }

    if ($detail['cCity']) {
        $cc                  = $detail['cCity'];
        $detail['cProperty'] = preg_replace("/$cc/", '', $detail['cProperty']);
    }

    if ($detail['cArea']) {
        $cc                  = $detail['cArea'];
        $detail['cProperty'] = preg_replace("/$cc/", '', $detail['cProperty']);
    }

    $detail['cProperty'] = $detail['cCity'] . $detail['cArea'] . $detail['cProperty'];

    // 利息
    $sql = '
		SELECT
			tAccount,
			sum(tInterest) as INTEREST
		FROM
			tBankInterest
		WHERE
			tAccount="' . $tVR_Code . '"
	';
    $rs = $conn->Execute($sql);

    $cInterest = round($rs->fields['INTEREST']);
    unset($tmp);
    ##

    //重新計算利息
    $detail['bInterest'] = $bInterest;
    $detail['cInterest'] = $cInterest;
    ##

    $oTaxId = ''; //其他賣方所得稅樣板ID
    $oNHIId = ''; //賣方代扣保費樣板ID

    //確認是否有多組買賣方
    $Obuyer = 0;
    $sql    = 'SELECT * FROM tContractOthers WHERE	cCertifiedId="' . $cCertifiedId . '" AND cIdentity="1" ;';
    $rs     = $conn->Execute($sql);
    $Obuyer = $rs->RecordCount(); //其他買方

    if ($Obuyer > 0) {
        $Obuyer += 1;
        $detail['cBuyer'] .= '等' . $Obuyer . '人';
    }

    $Oowner = 0;
    $sql    = 'SELECT * FROM tContractOthers WHERE	cCertifiedId="' . $cCertifiedId . '" AND cIdentity="2" ;';
    $rs     = $conn->Execute($sql);
    $Oowner = $rs->RecordCount();

    if ($Oowner > 0) {
        $Oowner += 1;
        $detail['cOwner'] .= '等' . $Oowner . '人';

        while (!$rs->EOF) {
            $id = $rs->fields['cIdentifyId']; //取出賣方 ID

            //若賣方 ID 有法人身分，則所得稅旗標加 1
            if (preg_match("/^[0-9]{8}$/", $id)) {
                $bTaxId = $id;
            }
            ##
            //若賣方 ID 有外國人身分，則所得稅旗標加 1
            if (preg_match("/[A-Za-z]{2}/", $id)) { //
                $bTaxId = $id;
            }

            //若賣方 ID 有自然人身分，則代扣保費旗標加 1
            if (preg_match("/^\w{10}$/", $id)) {
                $bNHIId = $id;
            }
            ##

            unset($id);unset($tmp);
            $rs->MoveNext();
        }
    }
    ##


    //預設所得稅與補充保費均歸給賣方(20130715)、當賣方身分當中有法人時則代扣稅款(20140321)
    $bInterest = 0;

    $detail['bTax'] = 0;
    $detail['cTax'] = 0;
    if ($bTaxId == '') { //若查無法人身分，則將主要代表賣方身分證字號進行計算
        $bTaxId = $detail['cOwnerId'];
    }
    $payTax = new payTax();
    $detail['cTax'] = $payTax->incomeTax($cInterest, $bTaxId);

    $detail['bNHITax'] = 0;
    $detail['cNHITax'] = 0;
    if ($bNHIId == '') { //若查無自然人身分，則將主要代表賣方身分證字號進行計算
        $bNHIId = $detail['cOwnerId'];
    }

    $detail['cNHITax'] = $payTax->NHITax($cInterest, $bNHIId);
    ##

    //賣方履保費要分兩個項目，但如果有買方履保費則
    $sql  = "SELECT eId,eMoney FROM tExpenseDetail WHERE eTarget =3 AND eItem = 9 AND eCertifiedId='" . $cCertifiedId . "' ";
    $rs   = $conn->Execute($sql);
    $list = $rs->fields;

    if ($list['eId'] == '') {
        $m = $detail['cCertifiedMoney'] % 2; //餘數
        $n = floor($detail['cCertifiedMoney'] / 2); //商數
        $m = $m + $n;

        $detail['cCertifiedMoney']  = $m; //賣方履保費
        $detail['cCertifiedMoney2'] = $n; //代扣買方履保費
        $detail['bCertifiedMoney']  = $n;
        $detail['bcertify_remark']  = '點交時由買方找補賣方';
    } else {
        $detail['cCertifiedMoney']  = $detail['cCertifiedMoney'] - $list['eMoney']; //賣方履保費
        $detail['bCertifiedMoney']  = $list['eMoney']; //買方履保費
        $detail['cCertifiedMoney2'] = 0;
        $detail['bcertify_remark']  = '買方應付履約保證費';
    }
    unset($list);
    ##

    $ownerBranch = str_replace('(待停用)', '', $ownerBranch);
    $buyerBranch = str_replace('(待停用)', '', $buyerBranch);
    // 寫入資料庫
    ## 寫入 tChecklist
    $sql = '
		INSERT INTO	tChecklist
		(
			cCertifiedId,
			cScrivener,
			bScrivener,
			cBuyer,
			bBuyer,
			cBuyerId,
			bBuyerId,
			cOwner,
			bOwner,
			cOwnerId,
			bOwnerId,
			cBrand,
			bBrand,
			cStore,
			bStore,
			cTotalMoney,
			bTotalMoney,
			cNotIntoMoney,
			bNotIntoMoney,
			cCompensation2,
			bCompensation2,
			cCompensation3,
			bCompensation3,
			cCompensation4,
			bCompensation4,
			cProperty,
			bProperty,
			cInterest,
			bInterest,
			cRealestateBalance,
			bRealestateBalance,
			cCertifiedMoney,
			cCertifiedMoney2,
			bCertifiedMoney,
			cScrivenerMoney,
			bScrivenerMoney,
			cTax,
			bTax,
			cNHITax,
			bNHITax,
			balance_remark,
			realty_remark,
			certify_remark,
			certify_remark2,
			bcertify_remark,
			scrivener_remark,
			cTaxTitle,
			bTaxTitle,
			cTaxRemark,
			bTaxRemark,
			InterestDate,
			last_modify,
			bNote,
			cNote

		)
		VALUES
		(
			"' . $cCertifiedId . '",
			"' . $detail['cScrivener'] . '",
			"' . $detail['cScrivener'] . '",
			"' . $detail['cBuyer'] . '",
			"' . $detail['cBuyer'] . '",
			"' . $detail['cBuyerId'] . '",
			"' . $detail['cBuyerId'] . '",
			"' . $detail['cOwner'] . '",
			"' . $detail['cOwner'] . '",
			"' . $detail['cOwnerId'] . '",
			"' . $detail['cOwnerId'] . '",
			"' . $ownerBrand . '",
			"' . $buyerBrand . '",
			"' . $ownerBranch . '",
			"' . $buyerBranch . '",
			"' . $detail['cTotalMoney'] . '",
			"' . $detail['cTotalMoney'] . '",
			"' . $detail['cNotIntoMoney'] . '",
			"' . $detail['cNotIntoMoney'] . '",
			"' . $detail['cCompensation2'] . '",
			"' . $detail['cCompensation2'] . '",
			"' . $detail['cCompensation3'] . '",
			"' . $detail['cCompensation3'] . '",
			"' . $detail['cCompensation4'] . '",
			"' . $detail['cCompensation4'] . '",
			"' . n_to_w($detail['cProperty']) . '",
			"' . n_to_w($detail['cProperty']) . '",
			"' . $detail['cInterest'] . '",
			"' . $detail['bInterest'] . '",
			"' . $detail['cRealestateBalance'] . '",
			"' . $detail['cRealestateBalanceBuyer'] . '",
			"' . $detail['cCertifiedMoney'] . '",
			"' . $detail['cCertifiedMoney2'] . '",
			"' . $detail['bCertifiedMoney'] . '",
			"' . $detail['cScrivenerMoney'] . '",
			"' . $detail['cScrivenerMoneyBuyer'] . '",
			"' . $detail['cTax'] . '",
			"' . $detail['bTax'] . '",
			"' . $detail['cNHITax'] . '",
			"' . $detail['bNHITax'] . '",
			"即專收款扣除專戶出款",
			"賣方應付仲介服務費",
			"",
			"代扣買方履約保證費",
			"' . $detail['bcertify_remark'] . '",
			"",
			"代扣利息所得稅",
			"代扣利息所得稅",
			"代賣方扣繳利息所得稅",
			"代買方扣繳利息所得稅",
			"' . date("Y-m-d") . '",
			"' . $last_modify . str_pad($_SESSION['member_id'], 4, '0', STR_PAD_LEFT) . '",
			"' . $detail['bNote'] . '",
			"' . $detail['cNote'] . '"
		) ;
	';
    //先由賣方價款中扣除，點交時再由買賣雙方找補
    $conn->Execute($sql);
    ##

    //20181108 買賣方有多店狀況
    if (count($tmpBuyerBranch) > 1) {
        $sql = "UPDATE tChecklist SET bMoreStore = '" . @implode(',', $tmpBuyerBranch) . "' WHERE cCertifiedId= '" . $cCertifiedId . "'";
        $conn->Execute($sql);
    }

    if (count($tmpOwnerBranch) > 1) {
        $sql = "UPDATE tChecklist SET cMoreStore = '" . @implode(',', $tmpOwnerBranch) . "' WHERE cCertifiedId= '" . $cCertifiedId . "'";
        $conn->Execute($sql);
    }

    //預設分配利息給合約書第一位賣方
    if ($detail['cInvoiceClose'] != 'Y') {
        //改為預設不分配利息
//        $sql = 'UPDATE tContractOwner SET cInterestMoney="' . $cInterest . '" WHERE cCertifiedId="' . $cCertifiedId . '" ;';
//        $conn->Execute($sql);
    }
    ##

    if ($detail['cNotIntoMoney'] > 0) { //未入專戶
        $remark = '*價款' . number_format($detail['cNotIntoMoney']) . '元由買方逕行支付予賣方,此筆款項不入專戶不在第一建經保證範圍。';
        $sql    = "INSERT INTO tChecklistRemark (cCertifiedId,cIdentity,cRemark)  VALUES ('" . $cCertifiedId . "',1,'" . $remark . "')";
        $conn->Execute($sql);

        $sql = "INSERT INTO tChecklistRemark (cCertifiedId,cIdentity,cRemark)  VALUES ('" . $cCertifiedId . "',2,'" . $remark . "')";
        $conn->Execute($sql);

        unset($remark);
    }
    ##

    // 點交單所有身分銀行清單
    /* 主買方 */
    $i   = 0;
    $sql = '
		SELECT
			*
		FROM
			tContractBuyer
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
	';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        if ($detail['buyerCB'] == 0) {
            $buyer[$i]['cCertifiedId']     = $rs->fields['cCertifiedId'];
            $buyer[$i]['cIdentity']        = '1';
            $buyer[$i]['cBankMain']        = $rs->fields['cBankKey2'];
            $buyer[$i]['cBankBranch']      = $rs->fields['cBankBranch2'];
            $buyer[$i]['cBankAccountNo']   = $rs->fields['cBankAccNumber'];
            $buyer[$i]['cBankAccountName'] = $rs->fields['cBankAccName'];
            $buyer[$i]['cBankMoney']       = $rs->fields['cBankMoney'];
            $buyer[$i]['cOrder']           = '2';
            $i++;

            $buyer[$i]['cCertifiedId']     = $rs->fields['cCertifiedId'];
            $buyer[$i]['cIdentity']        = '31';
            $buyer[$i]['cBankMain']        = $rs->fields['cBankKey2'];
            $buyer[$i]['cBankBranch']      = $rs->fields['cBankBranch2'];
            $buyer[$i]['cBankAccountNo']   = $rs->fields['cBankAccNumber'];
            $buyer[$i]['cBankAccountName'] = $rs->fields['cBankAccName'];
            $buyer[$i]['cBankMoney']       = $rs->fields['cBankMoney'];
            $buyer[$i]['cOrder']           = '3';
            $i++;
        }

        $rs->MoveNext();
    }
    ////

    /* 其他買方 */
    $sql = '
		SELECT
			*
		FROM
			tContractOthers
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
			AND cIdentity="1"
	';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        if ($rs->fields['cChecklistBank'] == 0) {
            $buyer[$i]['cCertifiedId']     = $rs->fields['cCertifiedId'];
            $buyer[$i]['cIdentity']        = $rs->fields['cIdentity'];
            $buyer[$i]['cBankMain']        = $rs->fields['cBankMain'];
            $buyer[$i]['cBankBranch']      = $rs->fields['cBankBranch'];
            $buyer[$i]['cBankAccountNo']   = $rs->fields['cBankAccNum'];
            $buyer[$i]['cBankAccountName'] = $rs->fields['cBankAccName'];
            $buyer[$i]['cBankMoney']       = $rs->fields['cBankMoney'];
            $buyer[$i]['cOrder']           = '2';
            $i++;

            $buyer[$i]['cCertifiedId']     = $rs->fields['cCertifiedId'];
            $buyer[$i]['cIdentity']        = '31';
            $buyer[$i]['cBankMain']        = $rs->fields['cBankMain'];
            $buyer[$i]['cBankBranch']      = $rs->fields['cBankBranch'];
            $buyer[$i]['cBankAccountNo']   = $rs->fields['cBankAccNum'];
            $buyer[$i]['cBankAccountName'] = $rs->fields['cBankAccName'];
            $buyer[$i]['cBankMoney']       = $rs->fields['cBankMoney'];
            $buyer[$i]['cOrder']           = '3';
            $i++;
        }

        $rs->MoveNext();
    }
    ////

    /* 主賣方 */
    $i   = 0;
    $sql = '
		SELECT
			*
		FROM
			tContractOwner
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
	';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        if ($detail['ownerCB'] == 0) {
            $owner[$i]['cCertifiedId']     = $rs->fields['cCertifiedId'];
            $owner[$i]['cIdentity']        = '2';
            $owner[$i]['cBankMain']        = $rs->fields['cBankKey2'];
            $owner[$i]['cBankBranch']      = $rs->fields['cBankBranch2'];
            $owner[$i]['cBankAccountNo']   = $rs->fields['cBankAccNumber'];
            $owner[$i]['cBankAccountName'] = $rs->fields['cBankAccName'];
            $owner[$i]['cBankMoney']       = $rs->fields['cBankMoney'];
            $owner[$i]['cOrder']           = '1';
            $i++;
        }

        $rs->MoveNext();
    }
    ////

    /* 其他賣方 */
    $sql = '
		SELECT
			*
		FROM
			tContractOthers
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
			AND cIdentity="2"
	';
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        if ($rs->fields['cChecklistBank'] == 0) {
            $owner[$i]['cCertifiedId']     = $rs->fields['cCertifiedId'];
            $owner[$i]['cIdentity']        = $rs->fields['cIdentity'];
            $owner[$i]['cBankMain']        = $rs->fields['cBankMain'];
            $owner[$i]['cBankBranch']      = $rs->fields['cBankBranch'];
            $owner[$i]['cBankAccountNo']   = $rs->fields['cBankAccNum'];
            $owner[$i]['cBankAccountName'] = $rs->fields['cBankAccName'];
            $owner[$i]['cBankMoney']       = $rs->fields['cBankMoney'];
            $owner[$i]['cOrder']           = '1';
            $i++;
        }

        $rs->MoveNext();
    }
    ////

    /* 仲介方 */
    $i   = 0;
    $sql = '
		SELECT
			*
		FROM
			tContractRealestate
		WHERE
			cCertifyId="' . $cCertifiedId . '"
	';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        //第一組買方
        if ($rs->fields['cBranchNum'] != '0') {
            $arrTmp = get_realty($link, $cCertifiedId, $rs->fields['cServiceTarget'], $rs->fields['cBranchNum']);
            foreach ($arrTmp as $k => $v) {
                $realty[$i++] = $v;
            }
            unset($arrTmp);
        }
        ##

        //第二組買方
        if ($rs->fields['cBranchNum1'] != '0') {
            $arrTmp = get_realty($link, $cCertifiedId, $rs->fields['cServiceTarget1'], $rs->fields['cBranchNum1']);
            foreach ($arrTmp as $k => $v) {
                $realty[$i++] = $v;
            }
            unset($arrTmp);
        }
        ##

        //第三組買方
        if ($rs->fields['cBranchNum2'] != '0') {
            $arrTmp = get_realty($link, $cCertifiedId, $rs->fields['cServiceTarget2'], $rs->fields['cBranchNum2']);
            foreach ($arrTmp as $k => $v) {
                $realty[$i++] = $v;
            }
            unset($arrTmp);
        }
        ##

        //第4組買方
        if ($rs->fields['cBranchNum3'] != '0') {
            $arrTmp = get_realty($link, $cCertifiedId, $rs->fields['cServiceTarget3'], $rs->fields['cBranchNum3']);
            foreach ($arrTmp as $k => $v) {
                $realty[$i++] = $v;
            }
            unset($arrTmp);
        }
        ##

        $rs->MoveNext();
    }
    ////

    /* 地政士 */
    $i   = 0;
    $sql = '
		SELECT
			scr.*
		FROM
			tContractScrivener AS csc
		JOIN
			tScrivener AS scr ON scr.sId=csc.cScrivener
		WHERE
			csc.cCertifiedId="' . $cCertifiedId . '"
	';
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $sId = $rs->fields['sId'];
        if ($rs->fields['sAccountUnused'] != 1) {
            $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
            $scrivener[$i]['cIdentity']        = '42'; //賣方顯示
            $scrivener[$i]['cBankMain']        = $rs->fields['sAccountNum1'];
            $scrivener[$i]['cBankBranch']      = $rs->fields['sAccountNum2'];
            $scrivener[$i]['cBankAccountNo']   = $rs->fields['sAccount3'];
            $scrivener[$i]['cBankAccountName'] = $rs->fields['sAccount4'];
            $scrivener[$i]['cOrder']           = '5';
            $i++;
            $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
            $scrivener[$i]['cIdentity']        = '43'; //買方顯示
            $scrivener[$i]['cBankMain']        = $rs->fields['sAccountNum1'];
            $scrivener[$i]['cBankBranch']      = $rs->fields['sAccountNum2'];
            $scrivener[$i]['cBankAccountNo']   = $rs->fields['sAccount3'];
            $scrivener[$i]['cBankAccountName'] = $rs->fields['sAccount4'];
            $scrivener[$i]['cOrder']           = '6';
            $i++;
        }

        if ($rs->fields['sAccountUnused1'] != 1 && $rs->fields['sAccountNum11']) {
            $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
            $scrivener[$i]['cIdentity']        = '42'; //賣方顯示
            $scrivener[$i]['cBankMain']        = $rs->fields['sAccountNum11'];
            $scrivener[$i]['cBankBranch']      = $rs->fields['sAccountNum21'];
            $scrivener[$i]['cBankAccountNo']   = $rs->fields['sAccount31'];
            $scrivener[$i]['cBankAccountName'] = $rs->fields['sAccount41'];
            $scrivener[$i]['cOrder']           = '5';
            $i++;
            $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
            $scrivener[$i]['cIdentity']        = '43'; //買方顯示
            $scrivener[$i]['cBankMain']        = $rs->fields['sAccountNum11'];
            $scrivener[$i]['cBankBranch']      = $rs->fields['sAccountNum21'];
            $scrivener[$i]['cBankAccountNo']   = $rs->fields['sAccount31'];
            $scrivener[$i]['cBankAccountName'] = $rs->fields['sAccount41'];
            $scrivener[$i]['cOrder']           = '6';
            $i++;
        }

        if ($rs->fields['sAccountUnused2'] != 1 && $rs->fields['sAccountNum12']) {
            $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
            $scrivener[$i]['cIdentity']        = '42'; //賣方顯示
            $scrivener[$i]['cBankMain']        = $rs->fields['sAccountNum12'];
            $scrivener[$i]['cBankBranch']      = $rs->fields['sAccountNum22'];
            $scrivener[$i]['cBankAccountNo']   = $rs->fields['sAccount32'];
            $scrivener[$i]['cBankAccountName'] = $rs->fields['sAccount42'];
            $scrivener[$i]['cOrder']           = '5';
            $i++;
            $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
            $scrivener[$i]['cIdentity']        = '43'; //買方顯示
            $scrivener[$i]['cBankMain']        = $rs->fields['sAccountNum12'];
            $scrivener[$i]['cBankBranch']      = $rs->fields['sAccountNum22'];
            $scrivener[$i]['cBankAccountNo']   = $rs->fields['sAccount32'];
            $scrivener[$i]['cBankAccountName'] = $rs->fields['sAccount42'];
            $scrivener[$i]['cOrder']           = '6';
            $i++;
        }

        $rs->MoveNext();
    }

    $sql = "SELECT * FROM tScrivenerBank WHERE sScrivener ='" . $sId . "' AND sUnUsed = 0";

    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
        $scrivener[$i]['cIdentity']        = '42'; //賣方顯示
        $scrivener[$i]['cBankMain']        = $rs->fields['sBankMain'];
        $scrivener[$i]['cBankBranch']      = $rs->fields['sBankBranch'];
        $scrivener[$i]['cBankAccountNo']   = $rs->fields['sBankAccountNo'];
        $scrivener[$i]['cBankAccountName'] = $rs->fields['sBankAccountName'];
        $scrivener[$i]['cOrder']           = '5';
        $i++;
        $scrivener[$i]['cCertifiedId']     = $cCertifiedId;
        $scrivener[$i]['cIdentity']        = '43'; //買方顯示
        $scrivener[$i]['cBankMain']        = $rs->fields['sBankMain'];
        $scrivener[$i]['cBankBranch']      = $rs->fields['sBankBranch'];
        $scrivener[$i]['cBankAccountNo']   = $rs->fields['sBankAccountNo'];
        $scrivener[$i]['cBankAccountName'] = $rs->fields['sBankAccountName'];
        $scrivener[$i]['cOrder']           = '6';
        $i++;

        $rs->MoveNext();
    }
    ////

    /* 捐創世基金會 */

    /* 寫入 tChecklistBank 資料表中(買方) */
    updateBank($buyer);

    /* 寫入 tChecklistBank 資料表中(賣方) */
    updateBank($owner);

    /* 寫入 tChecklistBank 資料表中(仲介方) */
    updateBank($realty);

    /* 寫入 tChecklistBank 資料表中(代書) */
    updateBank($scrivener);

    function ChecklistBankOther($id)
    {
        global $conn;
        $sql = "SELECT cChecklistBank FROM tContractOthers WHERE cId = '" . $id . "'";
        $rs  = $conn->Execute($sql);

        if ($rs->fields['cChecklistBank'] == 0) {
            return ture;
        } else {
            return false;
        }
    }
    $i   = 0;
    $sql = '
		SELECT
			*
		FROM
			tContractCustomerBank
		WHERE
			cCertifiedId="' . $cCertifiedId . '" AND cChecklistBank != 1 ORDER BY cId ASC';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $tmp = $rs->fields;
        if ($tmp['cBankMain'] != 0) {
            if ($tmp['cIdentity'] == 52) { //sell
                $tmp['cIdentity'] = 2;
            } else if ($tmp['cIdentity'] == 53) {
                $tmp['cIdentity'] = 1;
            } else if ($tmp['cIdentity'] == 3) {
                $tmp['cIdentity'] = 32; //33
            }

            $tmp2[$i]['cCertifiedId']     = $tmp['cCertifiedId'];
            $tmp2[$i]['cIdentity']        = $tmp['cIdentity'];
            $tmp2[$i]['cBankMain']        = $tmp['cBankMain'];
            $tmp2[$i]['cBankBranch']      = $tmp['cBankBranch'];
            $tmp2[$i]['cBankAccountNo']   = $tmp['cBankAccountNo'];
            $tmp2[$i]['cBankAccountName'] = $tmp['cBankAccountName'];
            $tmp2[$i]['cBankMoney']       = $tmp['cBankMoney'];

            switch ($tmp['cIdentity']) {
                case '1':
                    $tmp2[$i]['cOrder'] = '2';
                    break;
                case '2':
                    $tmp2[$i]['cOrder'] = '1';
                    break;
                case '31':
                    $tmp2[$i]['cOrder'] = '3';
                    break;
                case '32':
                    $tmp2[$i]['cOrder'] = '8';
                    break;
                case '33':
                    $tmp2[$i]['cOrder'] = '9';
                    break;
                case '42':
                    $tmp2[$i]['cOrder'] = '5';
                    break;
                case '43':
                    $tmp2[$i]['cOrder'] = '6';
                    break;
                case '52':
                    $tmp2[$i]['cOrder'] = '11';
                    break;
                case '53':
                    $tmp2[$i]['cOrder'] = '12';
                    break;
            }

            $i++;

            if ($tmp['cIdentity'] == 1) {
                $tmp2[$i]['cCertifiedId']     = $tmp['cCertifiedId'];
                $tmp2[$i]['cIdentity']        = '31';
                $tmp2[$i]['cBankMain']        = $tmp['cBankMain'];
                $tmp2[$i]['cBankBranch']      = $tmp['cBankBranch'];
                $tmp2[$i]['cBankAccountNo']   = $tmp['cBankAccountNo'];
                $tmp2[$i]['cBankAccountName'] = $tmp['cBankAccountName'];
                $tmp2[$i]['cBankMoney']       = $tmp['cBankMoney'];
                $tmp2[$i]['cOrder']           = '3';
                $i++;
            }

            if ($tmp['cIdentity'] == 32) {
                $tmp2[$i]['cCertifiedId']     = $tmp['cCertifiedId'];
                $tmp2[$i]['cIdentity']        = '33';
                $tmp2[$i]['cBankMain']        = $tmp['cBankMain'];
                $tmp2[$i]['cBankBranch']      = $tmp['cBankBranch'];
                $tmp2[$i]['cBankAccountNo']   = $tmp['cBankAccountNo'];
                $tmp2[$i]['cBankAccountName'] = $tmp['cBankAccountName'];
                $tmp2[$i]['cBankMoney']       = $tmp['cBankMoney'];
                $tmp2[$i]['cOrder']           = '9';
                $i++;
            }
        }

        $rs->MoveNext();
    }

    updateBank($tmp2);
    unset($tmp2);
    ##
}

// 買方出入明細
$sql = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '";';
$rs  = $conn->Execute($sql);

$max = $rs->RecordCount();

if (!$ck) {
    $sql = '
		SELECT
			id,
			eTradeDate,
			eDebit,
			eLender,
			eDepAccount,
			(SELECT sName FROM tCategoryIncome WHERE sId=a.eStatusRemark) as sName,
			eStatusIncome,
			eBuyerMoney,
			eRemarkContent
		FROM
			tExpense AS a
		WHERE
			eDepAccount="00' . $tVR_Code . '"
			AND eTradeStatus="0"
			AND ePayTitle<>"網路整批"
		ORDER BY
			eLastTime
		ASC;
	';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $_money1 = (int) substr($rs->fields["eLender"], 0, 13); // 存入
        $_money2 = (int) substr($rs->fields["eDebit"], 0, 13); // 支出
        $_y      = substr($rs->fields["eTradeDate"], 0, 3) + 1911;
        $_m      = substr($rs->fields["eTradeDate"], 3, 2);
        $_d      = substr($rs->fields["eTradeDate"], 5, 2);
        $_date   = $_y . "/" . $_m . "/" . $_d;

        //
        if ($rs->fields["eStatusIncome"] != "3" && $rs->fields["eStatusIncome"] != "4") { // 調帳交易不顯示
            $arr[] = array(
                'date'        => $_date,
                'money1'      => $_money1,
                'money2'      => $_money2,
                'kind'        => $rs->fields['sName'],
                'txt'         => $rs->fields['eRemarkContent'],
                'eBuyerMoney' => $rs->fields['eBuyerMoney'],
                'expId'       => $rs->fields['id'],
            );
        }
        $rs->MoveNext();
    }

    //設定變更出款日期
    $sql      = 'SELECT tBankLoansDate AS tExport_time FROM tBankTrans WHERE tVR_Code="' . $tVR_Code . '" AND tObjKind="扣繳稅款";';
    $rs       = $conn->Execute($sql);
    $tmp      = $rs->fields;
    $tmp_date = explode("-", substr($tmp['tExport_time'], 0, 10));
    if (count($tmp_date) > 0) {
        $exp_date = implode('/', $tmp_date);
    }
    unset($tmp_date);
    ##

    //
    foreach ($arr as $k => $v) {
        if (!$exp_date) {
            $exp_date = $v['date'];
        }

        //取出款項明細
        $sql = '
			SELECT
				*,
				(SELECT tBankLoansDate FROM  tBankTrans WHERE tId=a.eOK) AS tBankLoansDate,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as kind
			FROM
				tExpenseDetail AS a
			WHERE
				eExpenseId="' . $v['expId'] . '"
				AND eTarget="3";
		';
        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $tmp_date                     = explode("-", substr($rs->fields['tBankLoansDate'], 0, 10));
            $rs->fields['tBankLoansDate'] = $tmp_date[0] . "/" . $tmp_date[1] . "/" . $tmp_date[2];

            unset($tmp_date);

            $c[] = array(
                'date'   => $rs->fields['tBankLoansDate'],
                'money1' => 0,
                'money2' => $rs->fields['eMoney'],
                'kind'   => $rs->fields['kind'],
                'expId'  => $v['eExpenseId'],
            );

            $rs->MoveNext();
        }
        ##

        //主要入款紀錄
        $sql      = "SELECT * FROM tExpenseDetailSms WHERE eExpenseId = '" . $v['expId'] . "'";
        $rs       = $conn->Execute($sql);
        $tmp      = $rs->fields;
        $in_check = 0;
        if ($tmp['eSignMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eSignMoney'],
                'money2' => 0,
                'kind'   => '簽約款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eAffixMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eAffixMoney'],
                'money2' => 0,
                'kind'   => '用印款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eDutyMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eDutyMoney'],
                'money2' => 0,
                'kind'   => '完稅款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eEstimatedMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eEstimatedMoney'],
                'money2' => 0,
                'kind'   => '尾款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eEstimatedMoney2'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eEstimatedMoney2'],
                'money2' => 0,
                'kind'   => '尾款差額',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eCompensationMoney'] > 0) { //
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eCompensationMoney'],
                'money2' => 0,
                'kind'   => '代償後餘額',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eExtraMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eExtraMoney'],
                'money2' => 0,
                'kind'   => '買方溢入款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eExchangeMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eExchangeMoney'],
                'money2' => 0,
                'kind'   => '換約款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($tmp['eServiceFee'] > 0) {
            $in_check = 1; //有輸入金額
            $c[]      = array(
                'date'   => $v['date'],
                'money1' => $tmp['eServiceFee'],
                'money2' => 0,
                'kind'   => '買方仲介服務費',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        $sql = "SELECT * FROM tExpenseDetailSmsOther WHERE eExpenseId = '" . $v['expId'] . "' AND eDel = 0";
        $rs  = $conn->Execute($sql);
        while (!$rs->EOF) {
            if ($rs->fields['eMoney'] > 0) {
                $in_check = 1; //有輸入金額
                $c[]      = array(
                    'date'   => $v['date'],
                    'money1' => $rs->fields['eMoney'],
                    'money2' => 0,
                    'kind'   => $rs->fields['eTitle'],
                    'txt'    => '',
                    'expId'  => $v['expId'],
                );
            }

            $rs->MoveNext();
        }
        unset($tmp, $tmp2);

        if ($in_check == 0) {
            //eTitle
            $c[] = array(
                'date'   => $v['date'],
                'money1' => $v['money1'],
                'money2' => $v['money2'],
                'kind'   => $v['kind'],
                'txt'    => $v['txt'],
                'expId'  => $v['expId'],
            );
        }
        unset($in_check);
        ##
    }
    unset($arr);
    ##

    //
    $sql = 'SELECT * FROM tBankTrans WHERE tVR_Code="' . $tVR_Code . '" AND tObjKind="仲介服務費" AND tBuyer<>"" AND tPayOk="1";';
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $rs->fields['tBankLoansDate'] = implode('/', explode('-', substr($rs->fields['tBankLoansDate'], 0, 10)));

        $c[] = array(
            'date'   => $rs->fields['tBankLoansDate'],
            'money1' => 0,
            'money2' => $rs->fields['tBuyer'],
            'kind'   => $rs->fields['tObjKind'],
            'txt'    => '',
            'expId'  => '',
        );

        $rs->MoveNext();
    }
    ##

    // 寫入資料庫
    ## 寫入 tChecklistBlist
    if (!empty($c)) {
        foreach ($c as $k => $v) {
            $sql = '
				INSERT INTO	tChecklistBlist
				(
					bCertifiedId,
					bDate,
					bKind,
					bIncome,
					bExpense,
					bRemark
				)
				VALUES
				(
					"' . $cCertifiedId . '",
					"' . $v['date'] . '",
					"' . $v['kind'] . '",
					"' . $v['money1'] . '",
					"' . $v['money2'] . '",
					"' . n_to_w($v['txt']) . '"
				) ;
			';
            $conn->Execute($sql);
        }
    }
    ##
}

// 賣方出明細(支出)
$sql = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oExpense<>"0";';
$rs  = $conn->Execute($sql);
$max = $rs->RecordCount();
if (!$ck) {
    $b   = array();
    $sql = '
		SELECT
			tVR_Code,
			tObjKind,
			tTxt,
			tMoney,
			tSeller,
			tExport_time,
			tBankLoansDate
		FROM
			tBankTrans
		WHERE
			tVR_Code="' . $tVR_Code . '" ;
	';
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $tmpArray[] = $rs->fields;
        $rs->MoveNext();
    }

    for ($i = 0; $i < count($tmpArray); $i++) {
        $_money2 = (int) $tmpArray[$i]["tMoney"];
        $_total  = $_money2;
        $_name   = $tmpArray[$i]["tTxt"];

        $_y    = substr($tmpArray[$i]["tBankLoansDate"], 0, 4);
        $_m    = substr($tmpArray[$i]["tBankLoansDate"], 5, 2);
        $_d    = substr($tmpArray[$i]["tBankLoansDate"], 8, 2);
        $_date = $_y . "/" . $_m . "/" . $_d;

        if ($tmpArray[$i]["tObjKind"] == '仲介服務費') {
            $_name   = '';
            $_money2 = (int) $tmpArray[$i]['tSeller'];
        }

        if ($tmpArray[$i]["tObjKind"] == '扣繳稅款') {
            $sql = 'SELECT * FROM tExpenseDetail WHERE eCertifiedId="' . substr($tVR_Code, 5, 9) . '";';
            $rs  = $conn->Execute($sql);

            if ($rs->RecordCount() < 1) {
                $b[] = array(
                    'date'   => $_date,
                    'money1' => '0',
                    'money2' => $_money2,
                    'kind'   => $tmpArray[$i]["tObjKind"],
                    'txt'    => $_name,
                    'expId'  => $v['expId'],
                );
            }
        } else if ($tmpArray[$i]["tObjKind"] != '調帳') {
            //點交單名稱修正
            if($tmpArray[$i]["tObjKind"] == '履保費先收(結案回饋)') $tmpArray[$i]["tObjKind"] = '履保費';
            //主要入款紀錄
            $b[] = array(
                'date'   => $_date,
                'money1' => '0',
                'money2' => $_money2,
                'kind'   => $tmpArray[$i]["tObjKind"],
                'txt'    => $_name,
                'expId'  => $v['expId'],
            );
            ##
        }
    }

    // 寫入資料庫
    ## 寫入 tCheckOlist (出款)
    if (!empty($b)) {
        foreach ($b as $k => $v) {
            $sql = '
				INSERT INTO	tChecklistOlist
				(
					oCertifiedId,
					oDate,
					oKind,
					oIncome,
					oExpense,
					oRemark
				)
				VALUES
				(
					"' . $cCertifiedId . '",
					"' . $v['date'] . '",
					"' . $v['kind'] . '",
					"' . $v['money1'] . '",
					"' . $v['money2'] . '",
					"' . n_to_w($v['txt']) . '"
				) ;
			';
            $conn->Execute($sql);
        }
    }
    ##
}

// 賣方出明細(存入)
$sql = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" AND oIncome<>"0";';
$rs  = $conn->Execute($sql);
$max = $rs->RecordCount();

if (!$ck) {
    $sql = '
		SELECT
			id,
			eTradeDate,
			eDebit,
			eLender,
			eChangeMoney,
			eStatusIncome,
			eBuyerMoney,
			eExtraMoney,
			eDepAccount,
			(SELECT sName FROM tCategoryIncome WHERE sId=a.eStatusRemark) as sName,
			eRemarkContent
		FROM
			tExpense AS a
		WHERE
			eDepAccount="00' . $tVR_Code . '"
			AND eTradeStatus="0"
			AND ePayTitle<>"網路整批"
		ORDER BY
			eLastTime
		ASC;
	';

    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $_money1   = (int) substr($rs->fields["eLender"], 0, 13); // 存入
        $_money2   = (int) substr($rs->fields["eDebit"], 0, 13); // 支出
        $_buyer    = (int) substr($rs->fields["eBuyerMoney"], 0, 13); // 扣除買方服務費
        $_buyer2   = (int) $rs->fields['eExtraMoney'];
        $tmp_check = 0; //1 買方服務費  2買方溢入款
        if ($_buyer > 0) {$_money1 = $_money1 - $_buyer;
            $tmp_check += 1;} //
        if ($_buyer2 > 0) {$_money1 = $_money1 - $_buyer2;
            $tmp_check += 2;}

        $_total = $_money1 - $_money2;
        $_y     = substr($rs->fields["eTradeDate"], 0, 3) + 1911;
        $_m     = substr($rs->fields["eTradeDate"], 3, 2);
        $_d     = substr($rs->fields["eTradeDate"], 5, 2);
        $_date  = $_y . "/" . $_m . "/" . $_d;

        if ($rs->fields["eStatusIncome"] != "3" && $rs->fields["eStatusIncome"] != "4") { // 調帳交易不顯示
            $arr[] = array(
                'date'   => $_date,
                'money1' => $_money1,
                'money2' => $_money2,
                'total'  => $_total,
                'kind'   => $rs->fields['sName'],
                'txt'    => $rs->fields['eRemarkContent'],
                'expId'  => $rs->fields['id'],
                'check'  => $tmp_check,
            );
        }

        $rs->MoveNext();
    }

    //設定 tExpenseDetail 變更出款日期
    $sql = 'SELECT tExport_time FROM tBankTrans WHERE tVR_Code="' . $tVR_Code . '" AND tObjKind="扣繳稅款";';
    $rs  = $conn->Execute($sql);

    $tmp_date = explode("-", substr($rs->fields['tExport_time'], 0, 10));
    if (count($tmp_date) > 0) {
        $exp_date = implode('/', $tmp_date);
    }
    unset($tmp_date);
    ##

    foreach ($arr as $k => $v) {
        //取得明細部分買方分配總金額並將賣方入帳金額扣除買方支出
        $sql = 'SELECT SUM(eMoney) as M FROM tExpenseDetail WHERE eExpenseId="' . $v['expId'] . '" AND eTarget="3"; ';
        $rs  = $conn->Execute($sql);
        $v['money1'] -= (int) $rs->fields['M']; //扣除買方明細加總金額
        unset($tmp);
        ##

        //取出賣方明細部分出款
        $sql = '
			SELECT
				*,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as kind,
				(SELECT tBankLoansDate FROM  tBankTrans WHERE tId=a.eOK) AS tBankLoansDate
			FROM
				tExpenseDetail AS a
			WHERE
				eExpenseId="' . $v['expId'] . '"
				AND eTarget="2";
		';
        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $money2 = (int) $rs->fields['eMoney'];
            if (!$exp_date) {
                $exp_date = $v['date'];
            }

            $tmp_date                     = explode("-", substr($rs->fields['tBankLoansDate'], 0, 10));
            $rs->fields['tBankLoansDate'] = $tmp_date[0] . "/" . $tmp_date[1] . "/" . $tmp_date[2];
            unset($tmp_date);

            $a[] = array(
                'date'   => $rs->fields['tBankLoansDate'],
                'money1' => 0,
                'money2' => $money2,
                'kind'   => $rs->fields['kind'],
                'expId'  => $v['eExpenseId'],
            );

            $rs->MoveNext();
        }
        ##

        //主要入款紀錄
        $sql = "SELECT * FROM tExpenseDetailSms WHERE eExpenseId = '" . $v['expId'] . "'";
        $rs  = $conn->Execute($sql);

        $in_check = 0;
        if ($rs->fields['eSignMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'   => $v['date'],
                'money1' => $rs->fields['eSignMoney'],
                'money2' => 0,
                'kind'   => '簽約款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($rs->fields['eAffixMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'   => $v['date'],
                'money1' => $rs->fields['eAffixMoney'],
                'money2' => 0,
                'kind'   => '用印款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($rs->fields['eDutyMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'   => $v['date'],
                'money1' => $rs->fields['eDutyMoney'],
                'money2' => 0,
                'kind'   => '完稅款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($rs->fields['eEstimatedMoney'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'   => $v['date'],
                'money1' => $rs->fields['eEstimatedMoney'],
                'money2' => 0,
                'kind'   => '尾款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($rs->fields['eEstimatedMoney2'] > 0) {
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'   => $v['date'],
                'money1' => $rs->fields['eEstimatedMoney2'],
                'money2' => 0,
                'kind'   => '尾款差額',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($rs->fields['eCompensationMoney'] > 0) { //
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'   => $v['date'],
                'money1' => $rs->fields['eCompensationMoney'],
                'money2' => 0,
                'kind'   => '代償後餘額',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        if ($rs->fields['eExchangeMoney'] > 0) { //
            $in_check = 1; //有輸入金額
            $a[]      = array(
                'date'   => $v['date'],
                'money1' => $rs->fields['eExchangeMoney'],
                'money2' => 0,
                'kind'   => '換約款',
                'txt'    => '',
                'expId'  => $v['expId'],
            );
        }

        $sql = "SELECT * FROM tExpenseDetailSmsOther WHERE eExpenseId = '" . $v['expId'] . "' AND eDel = 0";
        $rs  = $conn->Execute($sql);
        while (!$rs->EOF) {
            if ($rs->fields['eMoney'] > 0) {
                $in_check = 1; //有輸入金額
                if (!preg_match("/買方應付款項/", $rs->fields['eTitle']) && !preg_match("/買方預收款項/", $rs->fields['eTitle']) && !preg_match("/買方履保費/", $rs->fields['eTitle']) && !preg_match("/契稅/", $rs->fields['eTitle']) && !preg_match("/印花稅/", $rs->fields['eTitle'])) {
                    $a[] = array(
                        'date'   => $v['date'],
                        'money1' => $rs->fields['eMoney'],
                        'money2' => 0,
                        'kind'   => $rs->fields['eTitle'],
                        'txt'    => '',
                        'expId'  => $v['expId'],
                    );
                }
            }
            $rs->MoveNext();
        }
        unset($tmp, $tmp2);

        if ($in_check == 0) {
            $tmp = explode('+', $v['txt']);

            for ($i = 0; $i < count($tmp); $i++) {
                if ($v['check'] == 1) { //1 買方服務費  2買方溢入款
                    if (preg_match("/買方/", $tmp[$i]) && preg_match("/服務費/", $tmp[$i])) {
                        unset($tmp[$i]);
                    }
                } else if ($v['check'] == 2) {
                    if (preg_match("/買方溢入款/", $tmp[$i])) {
                        unset($tmp[$i]);
                    }
                } else if ($v['check'] == 3) {
                    if (preg_match("/買方/", $tmp[$i]) && preg_match("/服務費/", $tmp[$i])) {
                        unset($tmp[$i]);
                    } else if (preg_match("/買方溢入款/", $tmp[$i])) {
                        unset($tmp[$i]);
                    }
                }
            }

            if ($v['txt'] != '') {
                $v['txt'] = @implode('+', $tmp);
            }
            unset($tmp);

            $a[] = array(
                'date'   => $v['date'],
                'money1' => $v['money1'],
                'money2' => $v['money2'],
                'kind'   => $v['kind'],
                'txt'    => $v['txt'],
                'expId'  => $v['expId'],
            );
        }
        unset($in_check);
        ##
    }
    unset($arr);
    ##

    // 寫入資料庫
    ## 寫入 tCheckOlist (收款)
    if (!empty($a)) {
        foreach ($a as $k => $v) {
            $sql = '
				INSERT INTO	tChecklistOlist
				(
					oCertifiedId,
					oDate,
					oKind,
					oIncome,
					oExpense,
					oRemark
				)
				VALUES
				(
					"' . $cCertifiedId . '",
					"' . $v['date'] . '",
					"' . $v['kind'] . '",
					"' . $v['money1'] . '",
					"' . $v['money2'] . '",
					"' . n_to_w($v['txt']) . '"
				) ;
			';

            $conn->Execute($sql);
        }
    }
    ##
}

//埋log紀錄
if (!$ck) {
    checklist_log('資料寫入點交表(保證號碼:' . $cCertifiedId . ')');
} else {
    checklist_log('從合約書點開(保證號碼:' . $cCertifiedId . ')');
}

// 進入編輯畫面
header('location:checklist.php?cCertifiedId=' . $cCertifiedId);
