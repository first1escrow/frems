<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/class/tax.class.php';

//取得仲介店基本資料
function getRealty($_conn, $no = 0)
{
    if ($no > 0) {
        $_sql = 'SELECT * FROM tBranch WHERE bId="' . $no . '" ;';
        $_rs  = $_conn->Execute($_sql);
        $arr  = $_rs->fields;
    }
    return $arr;
}
##

function getInterestTax($_conn, $iCertifiedId, $iIdentifyId)
{
    $sql = "SELECT * FROM `tInterestTax` WHERE  iCertifiedId='" . $iCertifiedId . "' AND iIdentifyId='" . $iIdentifyId . "'";
    $rs  = $_conn->Execute($sql);
    return $rs->fields;
}

// 計算稅額
function calculate($_id, $_int = 0)
{
    $_len = strlen($_id); // 個人10碼 公司8碼

    if (preg_match("/[A-Za-z]{2}/", $_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/", $_id)) { //外國籍自然人(一般民眾)
        $_o   = 1;                                                                                    // 外國籍自然人(一般民眾)
        $_tax = 0.2;                                                                                  // 稅率：20%
    } else if ($_len == '10') {                                                                   // 個人10碼
        return 0;
    } else if ($_len == '8') { // 公司8碼
        return 0;
    } else if ($_len == '7') {
        if (preg_match("/^9[0-9]{6}$/", $_id)) { // 判別是否為外國人
            $_o   = 1;                               // 外國籍自然人(一般民眾)
            $_tax = 0.2;                             // 稅率：20%
        } else {
            return 0;
        }
    }

    return (($_o == 1) && ! empty($_tax)) ? round($_int * $_tax) : 0;
}
##

$tlog = new TraceLog();

$cId  = isset($_REQUEST['cCertifiedId']) ? $_REQUEST['cCertifiedId'] : '';
$save = isset($_REQUEST['save']) ? $_REQUEST['save'] : '';

$cCertifiedId = substr($cId, 5);
$_res         = '';
$records      = '';

//調整利息分配更新
if ($save == 'ok') {
    //取得更新資料
    $owner_cId            = $_POST['owner_cId'];
    $owner_cInterestMoney = $_POST['owner_cInterestMoney'] + 1 - 1;
    $owner_mail           = $_POST['owner_mail'];

    $buyer_cId            = $_POST['buyer_cId'];
    $buyer_cInterestMoney = $_POST['buyer_cInterestMoney'] + 1 - 1;
    $buyer_mail           = $_POST['buyer_mail'];

    $int_cId  = $_POST['int_cId'];
    $int_arr  = $_POST['cInterestMoney'];
    $int_mail = $_POST['int_mail'];

    $realty_cId   = $_POST['realty_cId'];
    $realty       = $_POST['realty_cInterestMoney0'];
    $realty1      = $_POST['realty_cInterestMoney1'];
    $realty2      = $_POST['realty_cInterestMoney2'];
    $realty3      = $_POST['realty_cInterestMoney3'];
    $realty_mail  = $_POST['realty_email0'];
    $realty1_mail = $_POST['realty_email1'];
    $realty2_mail = $_POST['realty_email2'];

    $scrivener_cId  = $_POST['scrivener_cId'];
    $scrivener      = $_POST['scrivener_cInterestMoney'];
    $scrivener_mail = $_POST['scrivener_mail'];

    $BankCheckList = $_POST['BankCheckList'];
    ##

    $tax = new tax();
    //更新主要賣方利息資料
    $sql = '
		UPDATE
			tContractOwner
		SET
			cInterestMoney="' . $owner_cInterestMoney . '",
			cEmail = "' . $owner_mail . '",
			cInterestEdit = "Y"
		WHERE
			cId="' . $owner_cId . '"
	;';
    $conn->Execute($sql);
    ##

    //取得主要賣方
    $sql = '
        SELECT
            cIdentifyId, cCategoryIdentify
        FROM
            tContractOwner
        WHERE
            cId="' . $owner_cId . '";
    ';
    $owner = $conn->Execute($sql);

    $_o      = $tax->citizenship($owner->fields['cIdentifyId']);
    $iTax    = $tax->interestTax($_o, $owner_cInterestMoney);
    $iNHITax = 0;
    if ($_o == 2 and $owner->fields['cCategoryIdentify'] == 1) {
        $iNHITax = $tax->NHITax($owner_cInterestMoney);
    }
    ##

    //新增已分利息稅額
    $sql = 'UPDATE tInterestTax
            SET `iTax` = "' . $iTax . '", `iNHITax` = "' . $iNHITax . '", updatedAt = NOW()
            WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $owner->fields['cIdentifyId'] . '"';
    $rs = $conn->Execute($sql);
    //計算數量
    $sql = 'SELECT * FROM tInterestTax  WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $owner->fields['cIdentifyId'] . '"';
    $res = $conn->Execute($sql);
    if ($res->RecordCount() == 0) {
        $sql = '
        INSERT INTO tInterestTax (iCertifiedId, iCategoryTarget, iCategoryIdentify, iIdentifyId, iTax, iNHITax)
        VALUES ("' . $cCertifiedId . '", "1", "' . $owner->fields['cCategoryIdentify'] . '", "' . $owner->fields['cIdentifyId'] . '", "' . $iTax . '", "' . $iNHITax . '");
        ';
        $conn->Execute($sql);
    }
    ##

    //更新主要買方利息資料
    $sql = '
		UPDATE
			tContractBuyer
		SET
			cInterestMoney="' . $buyer_cInterestMoney . '",
			cEmail = "' . $buyer_mail . '"
		WHERE
			cId="' . $buyer_cId . '"
	;';
    $conn->Execute($sql);
    ##
    //取得主要賣方
    $sql = '
        SELECT
            cIdentifyId, cCategoryIdentify
        FROM
            tContractBuyer
        WHERE
            cId="' . $buyer_cId . '";
    ';
    $buyer = $conn->Execute($sql);
    ##

    $_o      = $tax->citizenship($buyer->fields['cIdentifyId']);
    $iTax    = $tax->interestTax($_o, $buyer_cInterestMoney);
    $iNHITax = 0;
    if ($_o == 2 and $buyer->fields['cCategoryIdentify'] == 1) {
        $iNHITax = $tax->NHITax($buyer_cInterestMoney);
    }
    ##
    //新增已分利息稅額
    $sql = 'UPDATE tInterestTax
            SET `iTax` = "' . $iTax . '", `iNHITax` = "' . $iNHITax . '", updatedAt = NOW()
            WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $buyer->fields['cIdentifyId'] . '"';
    $rs = $conn->Execute($sql);
    //計算數量
    $sql = 'SELECT * FROM tInterestTax  WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $buyer->fields['cIdentifyId'] . '"';
    $res = $conn->Execute($sql);
    if ($res->RecordCount() == 0) {
        $sql = '
        INSERT INTO tInterestTax (iCertifiedId, iCategoryTarget, iCategoryIdentify, iIdentifyId, iTax, iNHITax)
        VALUES ("' . $cCertifiedId . '", "2", "' . $buyer->fields['cCategoryIdentify'] . '", "' . $buyer->fields['cIdentifyId'] . '", "' . $iTax . '", "' . $iNHITax . '");
    ';
        $conn->Execute($sql);
    }
    ##

    //更新其他利息對象資料
    $index = 0;
    if (count($int_cId) > 0) {
        foreach ($int_cId as $k => $v) {
            $sql = '
				UPDATE
					tContractOthers
				SET
					cInterestMoney="' . ($int_arr[$k] + 1 - 1) . '",
					cEmail = "' . addslashes($int_mail[$k]) . '"
				WHERE
					cId="' . $v . '"
			;';
            $conn->Execute($sql);

            //取得主要賣方
            $sql = '
                SELECT
                    cIdentity, cIdentifyId, cInterestMoney
                FROM
                    tContractOthers
                WHERE
                    cId="' . $v . '";
            ';
            $other = $conn->Execute($sql);

            $_o       = $tax->citizenship($other->fields['cIdentifyId']);
            $iTax     = $tax->interestTax($_o, ($int_arr[$k] + 1 - 1));
            $iNHITax  = 0;
            $category = 2;
            if (preg_match("/\w{10}/", $other->fields['cIdentifyId'])) {
                $category = 1;
            }

            if ($_o == 2 and $category == 1) {
                $iNHITax = $tax->NHITax(($int_arr[$k] + 1 - 1));
            }
            ##
            if ($other->fields['cIdentity'] == 1) {
                $categoryTarget = 2;
            }

            if ($other->fields['cIdentity'] == 2) {
                $categoryTarget = 1;
            }

            //新增已分利息稅額
            $sql = 'UPDATE tInterestTax
                    SET `iTax` = "' . $iTax . '", `iNHITax` = "' . $iNHITax . '", updatedAt = NOW()
                    WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $other->fields['cIdentifyId'] . '"';
            $rs = $conn->Execute($sql);
            //計算數量
            $sql = 'SELECT * FROM tInterestTax  WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $other->fields['cIdentifyId'] . '"';
            $res = $conn->Execute($sql);
            if ($res->RecordCount() == 0) {
                $sql = '
                    INSERT INTO tInterestTax (iCertifiedId, iCategoryTarget, iCategoryIdentify, iIdentifyId, iTax, iNHITax)
                    VALUES ("' . $cCertifiedId . '", "' . $categoryTarget . '", "' . $category . '", "' . $other->fields['cIdentifyId'] . '", "' . $iTax . '", "' . $iNHITax . '");
                    ';
                $conn->Execute($sql);
            }
            ##
        }
    }
    ##

    //更新仲介利息對象資料
    $sql = '
		UPDATE
			tContractRealestate
		SET
			cInterestMoney="' . $realty . '",
			cInterestMoney1="' . $realty1 . '",
			cInterestMoney2="' . $realty2 . '",
			cInterestMoney3="' . $realty3 . '",
			cEmail = "' . $realty_mail . '",
			cEmail1 = "' . $realty_mail1 . '",
			cEmail2 = "' . $realty_mail2 . '"
		WHERE
			cId="' . $realty_cId . '"
	;';

    $conn->Execute($sql);
    ##

    //取得仲介
    $sql = '
        SELECT cCertifyId, cBranchNum, cBranchNum1, cBranchNum2, cBranchNum3, cSerialNumber, cSerialNumber1, cSerialNumber2, cSerialNumber3
        FROM tContractRealestate
        WHERE
            cId="' . $realty_cId . '"
            ';
    $realestate = $conn->Execute($sql);
    $_o         = 2;
    $iNHITax    = 0;
    ##

    //新增已分利息稅額
    for ($i = 0; $i < 4; $i++) {
        if ($i == 0) {$money = $realty;
            $iTax                             = $tax->interestTax($_o, $realty);
            $iIdentifyId                      = $realestate->fields['cSerialNumber'];
            $branchNum                        = $realestate->fields['cBranchNum'];}
        if ($i == 1) {$money = $realty1;
            $iTax                             = $tax->interestTax($_o, $realty1);
            $iIdentifyId                      = $realestate->fields['cSerialNumber1'];
            $branchNum                        = $realestate->fields['cBranchNum1'];}
        if ($i == 2) {$money = $realty2;
            $iTax                             = $tax->interestTax($_o, $realty2);
            $iIdentifyId                      = $realestate->fields['cSerialNumber2'];
            $branchNum                        = $realestate->fields['cBranchNum2'];}
        if ($i == 3) {$money = $realty3;
            $iTax                             = $tax->interestTax($_o, $realty3);
            $iIdentifyId                      = $realestate->fields['cSerialNumber3'];
            $branchNum                        = $realestate->fields['cBranchNum3'];}

        if ($branchNum != 0) {
            $sql = 'UPDATE tInterestTax
                SET `iTax` = "' . $iTax . '", `iNHITax` = "' . $iNHITax . '", updatedAt = NOW()
                WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $iIdentifyId . '" AND iBranchNum = "' . $branchNum . '"';
            $rs = $conn->Execute($sql);
            //計算數量
            $sql = 'SELECT * FROM tInterestTax WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $iIdentifyId . '" AND iBranchNum = "' . $branchNum . '"';
            $res = $conn->Execute($sql);
            if ($res->RecordCount() == 0) {
                $sql = '
            INSERT INTO tInterestTax (iCertifiedId, iCategoryTarget, iCategoryIdentify, iIdentifyId, iTax, iNHITax, iBranchNum)
            VALUES ("' . $cCertifiedId . '", "3", "2", "' . $iIdentifyId . '", "' . $iTax . '", "' . $iNHITax . '", "' . $branchNum . '");
            ';
                $conn->Execute($sql);
            }
        }
    }
    ##

    //更新代書利息對象資料
    $sql = '
		UPDATE
			tContractScrivener
		SET
			cInterestMoney="' . $scrivener . '",
			cEmail = "' . $scrivener_mail . '"
		WHERE
			cId="' . $scrivener_cId . '"
	;';

    $conn->Execute($sql);
    ##

    //取得代書
    $sql = '
        SELECT cs.cCertifiedId, s.sIdentifyId, cs.cInterestMoney
        FROM `tContractScrivener` AS cs
            LEFT JOIN `tScrivener` AS s ON cs.cScrivener = s.sId
        WHERE
            cId="' . $scrivener_cId . '"
        ';

    $scrivenerInfo = $conn->Execute($sql);
    $_o            = 2; //本國人
    $iTax          = $tax->interestTax($_o, $scrivener);
    $iNHITax       = 0;

    $iNHITax = $tax->NHITax($scrivener);
    ##

    //新增已分利息稅額
    $sql = 'UPDATE tInterestTax
            SET `iTax` = "' . $iTax . '", `iNHITax` = "' . $iNHITax . '", updatedAt = NOW()
            WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $scrivenerInfo->fields['sIdentifyId'] . '"';
    $rs = $conn->Execute($sql);
    //計算數量
    $sql = 'SELECT * FROM tInterestTax WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $scrivenerInfo->fields['sIdentifyId'] . '"';
    $res = $conn->Execute($sql);
    if ($res->RecordCount() == 0) {
        $sql = '
        INSERT INTO tInterestTax (iCertifiedId, iCategoryTarget, iCategoryIdentify, iIdentifyId, iTax, iNHITax)
        VALUES ("' . $cCertifiedId . '", "4", "1", "' . $scrivenerInfo->fields['sIdentifyId'] . '", "' . $iTax . '", "' . $iNHITax . '");
    ';
        $conn->Execute($sql);
    }
    ##

    //指定對象
    for ($i = 0; $i < count($_POST['another_cId']); $i++) {

        $sql = "
		UPDATE
			 tContractInterestExt
		SET
			cInterestMoney  ='" . $_POST['another_cInterestMoney'][$i] . "',
			cEmail = '" . $_POST['another_mail'][$i] . "'
		WHERE
			cId = '" . $_POST['another_cId'][$i] . "'
		";

        // echo $sql."<br>";
        $conn->Execute($sql);

        //取得指定對象
        $sql = '
                SELECT
                    cDBName, cIdentifyId, cInterestMoney
                FROM
                    tContractInterestExt
                WHERE
                    cId="' . $_POST['another_cId'][$i] . '";
            ';
        $ext = $conn->Execute($sql);

        $_o       = $tax->citizenship($ext->fields['cIdentifyId']);
        $iTax     = $tax->interestTax($_o, $ext->fields['cInterestMoney']);
        $iNHITax  = 0;
        $category = 2;
        if (preg_match("/\w{10}/", $ext->fields['cIdentifyId'])) {
            $category = 1;
        }

        if ($_o == 2 and $category == 1) {
            $iNHITax = $tax->NHITax($ext->fields['cInterestMoney']);
        }
        ##

        //指定對象 身分別1:賣 2:買 3:仲 4:地
        if (in_array($ext->fields['cDBName'], ['tContractOwner', 'tContractOthersO'])) {$categoryTarget = 1;}
        if (in_array($ext->fields['cDBName'], ['tContractBuyer', 'tContractOthersB'])) {$categoryTarget = 2;}
        if (in_array($ext->fields['cDBName'], ['tContractRealestate', 'tContractRealestate1', 'tContractRealestate2', 'tContractRealestate3'])) {$categoryTarget = 3;}
        if ($ext->fields['cDBName'] == 'tContractScrivener') {$categoryTarget = 4;}
        ##

        //新增已分利息稅額
        $sql = 'UPDATE tInterestTax
                SET `iTax` = "' . $iTax . '", `iNHITax` = "' . $iNHITax . '", updatedAt = NOW()
                WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $ext->fields['cIdentifyId'] . '"';
        $rs = $conn->Execute($sql);

        //計算數量
        $sql = 'SELECT * FROM tInterestTax WHERE iCertifiedId = "' . $cCertifiedId . '" AND iIdentifyId = "' . $ext->fields['cIdentifyId'] . '"';
        $res = $conn->Execute($sql);
        if ($res->RecordCount() == 0) {
            $sql = '
                    INSERT INTO tInterestTax (iCertifiedId, iCategoryTarget, iCategoryIdentify, iIdentifyId, iTax, iNHITax)
                    VALUES ("' . $cCertifiedId . '", "' . $categoryTarget . '", "' . $category . '", "' . $ext->fields['cIdentifyId'] . '", "' . $iTax . '", "' . $iNHITax . '");
                    ';
            $conn->Execute($sql);
        }
        ##
    }

               //更新點交表利息欄位
    $bInt = 0; //買方總利息
    $oInt = 0; //賣方總利息

    /* 主買方利息 get */
    $sql = '
		SELECT
			cInterestMoney
		FROM
			tContractBuyer
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
	';
    $rs = $conn->Execute($sql);
    $bInt += $rs->fields['cInterestMoney'];

    /* 其他買方利息 get */
    $sql = '
		SELECT
			cInterestMoney
		FROM
			tContractOthers
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
			AND cIdentity="1"
	';
    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $bInt += $rs->fields['cInterestMoney'];
        $rs->MoveNext();
    }

    /* 主賣方利息 get */
    $sql = '
		SELECT
			cInterestMoney
		FROM
			tContractOwner
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
	';
    $rs = $conn->Execute($sql);
    $oInt += $rs->fields['cInterestMoney'];

    /* 其他賣方利息 get */
    $sql = '
		SELECT
			cInterestMoney
		FROM
			tContractOthers
		WHERE
			cCertifiedId="' . $cCertifiedId . '"
			AND cIdentity="2"
	';
    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $oInt += $rs->fields['cInterestMoney'];
        $rs->MoveNext();
    }

    //指定對象
    $sql = "SELECT cDBName,cInterestMoney  FROM tContractInterestExt WHERE cCertifiedId='" . $cCertifiedId . "'";

    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {

        if ($rs->fields['cDBName'] == 'tContractOwner' || $rs->fields['cDBName'] == 'tContractOthersO') {
            $oInt += $rs->fields['cInterestMoney'];
        } elseif ($rs->fields['cDBName'] == 'tContractBuyer' || $rs->fields['cDBName'] == 'tContractOthersB') {
            $bInt += $rs->fields['cInterestMoney'];
        } else {
            $oInt += $rs->fields['cInterestMoney'];
        }

        $rs->MoveNext();
    }

    /* 利息歸仲介或代書時，利息算在賣方點交單上 */
    $oInt += $realty + $realty1 + $realty2 + $realty3 + $scrivener;
    ////

    //賣家是否為國外賣家，確認是否需收稅費
    $cTax = 0;

    $sql = 'SELECT cIdentifyId, cInterestMoney FROM tContractOwner WHERE cCertifiedId = "' . $cCertifiedId . '";'; //主賣方
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        $_id  = $rs->fields['cIdentifyId'];
        $_int = (int) $rs->fields['cInterestMoney'];

        $cTax += calculate($_id, $_int);

        $_id = $_int = null;
        unset($_id, $_int);

        $rs->MoveNext();
    }

    $sql = 'SELECT cIdentifyId, cInterestMoney FROM tContractOthers WHERE cCertifiedId = "' . $cCertifiedId . '" AND cIdentity = "2";'; //其他賣方
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        $_id  = $rs->fields['cIdentifyId'];
        $_int = (int) $rs->fields['cInterestMoney'];

        $cTax += calculate($_id, $_int);

        $_id = $_int = null;
        unset($_id, $_int);

        $rs->MoveNext();
    }

    $sql_ext = ($cTax > 0) ? ', cTax = "' . $cTax . '" ' : '';
    ##

    /* 更新點交單資料表欄位 */
    $sql = 'UPDATE tChecklist SET cInterest="' . $oInt . '", bInterest="' . $bInt . '"' . $sql_ext . ' WHERE cCertifiedId="' . $cCertifiedId . '";';

    $conn->Execute($sql);
    ##

    //更新無履保但須出利息扣繳憑單與點交單之出款日期
    if ($BankCheckList) {
        $tmp           = explode('-', $BankCheckList);
        $BankCheckList = ($tmp[0] + 1911) . '-' . str_pad($tmp[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($tmp[2], 2, '0', STR_PAD_LEFT);
        unset($tmp);
    }

    $sql = 'UPDATE tContractCase SET cBankList="' . $BankCheckList . '" WHERE cCertifiedId="' . $cCertifiedId . '";';
    $conn->Execute($sql);
    ##

    $sql = "UPDATE tContractCase SET `cLastEditor` =  '" . $_SESSION['member_id'] . "', `cLastTime` =  now() WHERE cCertifiedId ='" . $cCertifiedId . "'";
    $conn->Execute($sql);

    $tlog->updateWrite($_SESSION['member_id'], '', '案件利息作業編修');

}
##

//顯示保證號碼相關資料
$sql = '
	SELECT
		a.*,
		b.cBankList,
		b.cSignCategory,
		b.cEscrowBankAccount
	FROM
		tChecklist AS a
	JOIN
		tContractCase AS b ON a.cCertifiedId=b.cCertifiedId
	WHERE
		a.cCertifiedId="' . $cCertifiedId . '";
';
$rs = $conn->Execute($sql);

$cEscrowBankAccount = isset($rs->fields['cEscrowBankAccount']) ? $rs->fields['cEscrowBankAccount'] : '';

$o_no   = 0;
$b_no   = 0;
$_index = 0;

//若有搜尋到資料
if (isset($rs->fields['cCertifiedId']) && $rs->fields['cCertifiedId']) {

    $cSignCategory = isset($rs->fields['cSignCategory']) ? $rs->fields['cSignCategory'] : ''; //判斷合約書位置
                                                                                              //取得利息
    $_bal           = 0;
    $_b             = $rs->fields['bInterest'] + 1 - 1;
    $_c             = $rs->fields['cInterest'] + 1 - 1;
    $interest_total = $_b + $_c;
    unset($_b);unset($_c);

    //是否強制出現會計點交表中
    $BankListChk = '';
    $bankList    = '';
    if ($rs->fields['cBankList']) {
        $BankListChk = ' checked="checked"';
        $tmp         = explode('-', $rs->fields['cBankList']);
        $bankList    = ($tmp[0] - 1911) . '-' . $tmp[1] . '-' . $tmp[2];
        unset($tmp);
    }
    ##

    //合約書中第一位賣方
    $i   = 0;
    $sql = '
        SELECT *
        FROM tContractOwner AS o
        LEFT JOIN tInterestTax AS t
        ON (o.cCertifiedId = t.iCertifiedId AND o.cIdentifyId = t.iIdentifyId)
        WHERE o.cCertifiedId="' . $cCertifiedId . '" ;';
    $rsO = $conn->Execute($sql);

    while (! $rsO->EOF) {

        $data_o[$i]        = $rsO->fields;
        $data_o[$i]['tbl'] = 'tContractOwner';

        if (preg_match("/^\d{8}$/", $data_o[$i]['cIdentifyId'])) { //8
            $data_o[$i]['checkIden'] = 2;
        }

        $_bal += $rsO->fields['cInterestMoney'] + 1 - 1; //計算已分配利息
        $i++;
        $rsO->MoveNext();
    }
    //
    ##

    //指定對象$_bal +=
    $owner_another = another($conn, $cCertifiedId, '指定1', $data_o[0]['tbl'], $data_o[0]['cId']);

    //其他賣方
    $i   = 0;
    $j   = 2;
    $sql = '
            SELECT *
            FROM tContractOthers AS o
            LEFT JOIN tInterestTax AS t
            ON (o.cCertifiedId = t.iCertifiedId AND o.cIdentifyId=t.iIdentifyId)
            WHERE cCertifiedId="' . $cCertifiedId . '"
            AND o.cIdentity="2" ORDER BY cId ASC;
            ';
    $rsA = $conn->Execute($sql);

    while (! $rsA->EOF) {

        $data_o2[$i]        = $rsA->fields;
        $data_o2[$i]['tbl'] = 'tContractOthersO';

        if (preg_match("/^\d{8}$/", $data_o2[$i]['cIdentifyId'])) { //8
            $data_o2[$i]['checkIden'] = 2;
        }

        $_bal += $rsA->fields['cInterestMoney'] + 1 - 1; //計算已分配利息

        //指定對象
        $arr = another($conn, $cCertifiedId, '指定' . $j, $data_o2[$i]['tbl'], $data_o2[$i]['cId']);
        if (is_array($arr)) {
            if (is_array($owner_another)) {
                $owner_another = array_merge($owner_another, $arr);
            } else {
                $owner_another = $arr;
            }

        }

        unset($arr);
        $i++;
        $j++;

        $rsA->MoveNext();
    }

    //指定計算已分配利息(owner)
    for ($i = 0; $i < count($owner_another); $i++) {
        $_bal += $owner_another[$i]['cInterestMoney'];
    }

    ##

    //合約書中第一位買方
    $i   = 0;
    $sql = 'SELECT *
            FROM tContractBuyer AS b
            LEFT JOIN tInterestTax AS t
            ON (b.cCertifiedId = t.iCertifiedId AND b.cIdentifyId = t.iIdentifyId)
            WHERE b.cCertifiedId="' . $cCertifiedId . '";';
    $rsB = $conn->Execute($sql);

    while (! $rsB->EOF) {

        $data_b[$i]        = $rsB->fields;
        $data_b[$i]['tbl'] = 'tContractBuyer';
        if (preg_match("/^\d{8}$/", $data_b[$i]['cIdentifyId'])) { //8
            $data_b[$i]['checkIden'] = 2;
        }
        $_bal += $rsB->fields['cInterestMoney'] + 1 - 1; //計算已分配利息
        $i++;
        $rsB->MoveNext();
    }
    ##
    //指定對象
    $buyer_another = another($conn, $cCertifiedId, '指定1', $data_b[0]['tbl'], $data_b[0]['cId']);

    //其他買方
    $i   = 0;
    $j   = 2;
    $sql = '
            SELECT *
            FROM tContractOthers AS o
            LEFT JOIN tInterestTax AS t
            ON (o.cCertifiedId = t.iCertifiedId AND o.cIdentifyId = t.iIdentifyId)
            WHERE cCertifiedId="' . $cCertifiedId . '"
            AND cIdentity="1"
            ORDER BY cId ASC;';
    $rsA = $conn->Execute($sql);

    while (! $rsA->EOF) {

        $data_b2[$i]        = $rsA->fields;
        $data_b2[$i]['tbl'] = 'tContractOthersB';
        if (preg_match("/^\d{8}$/", $data_b2[$i]['cIdentifyId'])) { //8
            $data_b2[$i]['checkIden'] = 2;
        }
        $_bal += $rsA->fields['cInterestMoney'] + 1 - 1; //計算已分配利息

        //指定對象
        $arr = another($conn, $cCertifiedId, '指定' . $j, $data_b2[$i]['tbl'], $data_b2[$i]['cId']);
        if (is_array($arr)) {
            if (is_array($buyer_another)) {
                $buyer_another = array_merge($buyer_another, $arr);
            } else {
                $buyer_another = $arr;
            }

        }

        unset($arr);

        $i++;
        $j++;
        $rsA->MoveNext();
    }

    ##

    //指定計算已分配利息(buyer)
    for ($i = 0; $i < count($buyer_another); $i++) {
        $_bal += $buyer_another[$i]['cInterestMoney'];

    }

    //仲介
    $r_no           = 0;
    $realty_another = [];
    $sql            = 'SELECT * FROM tContractRealestate WHERE cCertifyId="' . $cCertifiedId . '" ;';
    $rsR            = $conn->Execute($sql);
    if ($rsR->RecordCount() > 0) {
        //第一組仲介

        if ($rsR->fields['cBranchNum'] != '0') {

            $data_r[$r_no]           = $rsR->fields;
            $tmp                     = getRealty($conn, $rsR->fields['cBranchNum']);
            $data_r[$r_no]['bStore'] = $tmp['bStore'];
            if ($rsR->fields['cInterestMoney'] != '0') {
                $data_r[$r_no]['ck'] = 'checked=checked';
            }
            $interestTax = getInterestTax($conn, $cCertifiedId, $rsR->fields['cSerialNumber']);
            if ($interestTax) {
                $data_r[$r_no]['iTax']    = $interestTax['iTax'];
                $data_r[$r_no]['iNHITax'] = $interestTax['iNHITax'];
            }

            $data_r[$r_no]['tbl'] = 'tContractRealestate';

            $_bal += $rsR->fields['cInterestMoney'] + 1 - 1; //計算已分配利息

            $data_r[$r_no]['checkIden'] = 2;

            $arr = another($conn, $cCertifiedId, '指定1', $data_r[$r_no]['tbl'], $data_r[$r_no]['cId']);
            if (is_array($arr)) {

                $realty_another = $arr;
            }
            $r_no++;

        }
        ##

        //第二組仲介
        if ($rsR->fields['cBranchNum1'] != '0') {

            $data_r[$r_no]           = $rsR->fields;
            $tmp                     = getRealty($conn, $rsR->fields['cBranchNum1']);
            $data_r[$r_no]['bStore'] = $tmp['bStore'];
            if ($rsR->fields['cInterestMoney1'] != '0') {
                $data_r[$r_no]['ck'] = 'checked=checked';
            }
            $interestTax = getInterestTax($conn, $cCertifiedId, $rsR->fields['cSerialNumber1']);
            if ($interestTax) {
                $data_r[$r_no]['iTax']    = $interestTax['iTax'];
                $data_r[$r_no]['iNHITax'] = $interestTax['iNHITax'];
            }
            $data_r[$r_no]['tbl']       = 'tContractRealestate1';
            $data_r[$r_no]['checkIden'] = 2;

            $_bal += $rsR->fields['cInterestMoney1'] + 1 - 1; //計算已分配利息

            //指定對象
            $arr = another($conn, $cCertifiedId, '指定2', $data_r[$r_no]['tbl'], $data_r[$r_no]['cId']);
            if (is_array($arr)) {
                if (is_array($realty_another)) {
                    $realty_another = array_merge($realty_another, $arr);
                } else {
                    $realty_another = $arr;
                }

            }

            unset($arr);

            $r_no++;
            unset($tmp);
        }
        ##

        //第三組仲介
        if ($rsR->fields['cBranchNum2'] != '0') {
            $_index++;
            $cked = '';

            $data_r[$r_no]           = $rsR->fields;
            $tmp                     = getRealty($conn, $rsR->fields['cBranchNum2']);
            $data_r[$r_no]['bStore'] = $tmp['bStore'];
            if ($rsR->fields['cInterestMoney2'] != '0') {
                $data_r[$r_no]['ck'] = 'checked=checked';
            }
            $interestTax = getInterestTax($conn, $cCertifiedId, $rsR->fields['cSerialNumber2']);
            if ($interestTax) {
                $data_r[$r_no]['iTax']    = $interestTax['iTax'];
                $data_r[$r_no]['iNHITax'] = $interestTax['iNHITax'];
            }
            $data_r[$r_no]['tbl']       = 'tContractRealestate2';
            $data_r[$r_no]['checkIden'] = 2;

            $_bal += $rsR->fields['cInterestMoney2'] + 1 - 1; //計算已分配利息

            //指定對象
            $arr = another($conn, $cCertifiedId, '指定3', $data_r[$r_no]['tbl'], $data_r[$r_no]['cId']);
            if (is_array($arr)) {
                if (is_array($realty_another)) {
                    $realty_another = array_merge($realty_another, $arr);
                } else {
                    $realty_another = $arr;
                }

            }

            unset($arr);
            $r_no++;
            unset($tmp);
        }
        ##

        //第四組仲介
        if ($rsR->fields['cBranchNum3'] != '0') {
            $_index++;
            $cked = '';

            $data_r[$r_no]           = $rsR->fields;
            $tmp                     = getRealty($conn, $rsR->fields['cBranchNum3']);
            $data_r[$r_no]['bStore'] = $tmp['bStore'];
            if ($rsR->fields['cInterestMoney3'] != '0') {
                $data_r[$r_no]['ck'] = 'checked=checked';
            }
            $interestTax = getInterestTax($conn, $cCertifiedId, $rsR->fields['cSerialNumber3']);
            if ($interestTax) {
                $data_r[$r_no]['iTax']    = $interestTax['iTax'];
                $data_r[$r_no]['iNHITax'] = $interestTax['iNHITax'];
            }
            $data_r[$r_no]['tbl']       = 'tContractRealestate3';
            $data_r[$r_no]['checkIden'] = 2;

            $_bal += $rsR->fields['cInterestMoney3'] + 1 - 1; //計算已分配利息

            //指定對象
            $arr = another($conn, $cCertifiedId, '指定4', $data_r[$r_no]['tbl'], $data_r[$r_no]['cId']);
            if (is_array($arr)) {
                if (is_array($realty_another)) {
                    $realty_another = array_merge($realty_another, $arr);
                } else {
                    $realty_another = $arr;
                }

            }

            unset($arr);
            unset($tmp);
        }
        ##
    }
    ##
    //指定計算已分配利息(realty)
    for ($i = 0; $i < count($realty_another); $i++) {
        $_bal += $realty_another[$i]['cInterestMoney'];

    }
    // echo "<pre>";
    // print_r($data_r);
    // echo "</pre>";
    //合約書地政士

    $s_no = 0;
    $sql  = 'SELECT * FROM tContractScrivener WHERE cCertifiedId="' . $cCertifiedId . '" ;';
    $rsS  = $conn->Execute($sql);
    if ($rsS->RecordCount() > 0) {
        $_index++;
        $cked = '';

        $data_s[$s_no]                   = $rsS->fields;
        $data_s[$s_no]['cInterestMoney'] = $rsS->fields['cInterestMoney'];
        if ($rsS->fields['cInterestMoney'] != '0') {
            $data_s[$s_no]['ck'] = 'checked=checked';
        }

        $data_s[$s_no]['tbl'] = 'tContractScrivener';

        //取得地政士基本資料
        $sql = 'SELECT * FROM tScrivener WHERE sId="' . $rsS->fields['cScrivener'] . '" ;';
        $_rs = $conn->Execute($sql);

        $data_s[$s_no]['sName'] = $_rs->fields['sName'];
        //計算利息稅 二代健保
        $interestTax = getInterestTax($conn, $cCertifiedId, $_rs->fields['sIdentifyId']);
        if ($interestTax) {
            $data_s[$s_no]['iTax']    = $interestTax['iTax'];
            $data_s[$s_no]['iNHITax'] = $interestTax['iNHITax'];
        }

        if (preg_match("/^\d{8}$/", $_rs->fields['sIdentifyId'])) { //8
            $data_s[$s_no]['checkIden'] = 2;
        }

        unset($_rs);
        ##

        $_bal += $rsS->fields['cInterestMoney'] + 1 - 1; //計算已分配利息
        unset($tmp);
        ##

        $scr_another = another($conn, $cCertifiedId, '指定1', $data_s[$s_no]['tbl'], $data_s[$s_no]['cId']);

        //指定計算已分配利息(scr)
        for ($i = 0; $i < count($scr_another); $i++) {
            $_bal += $scr_another[$i]['cInterestMoney'];

        }
    }

    ##

    //$_res = '已分利息配金額：<input id="int_already" style="width:60px;font-weight:bold;color:#000080;" value="'.$_bal.'" disabled="disabled">元、剩餘未分配利息金額：<span id="int_notyet" style="font-weight:bold;color:red;">'.($interest_total - $_bal).'</span>元' ;
    $_show = '1';
} else {

    $_show = '';
}
##
function another($conn, $cCertifiedId, $type, $tbl, $id)
{

    $sql = "SELECT
                *
            FROM
                tContractInterestExt AS e
                LEFT JOIN tInterestTax AS t
                ON (e.cCertifiedId = t.iCertifiedId AND e.cIdentifyId = t.iIdentifyId)
            WHERE
                cCertifiedId='" . $cCertifiedId . "'
                AND cDBName ='" . $tbl . "'
                AND cTBId='" . $id . "'";

    $rs = $conn->Execute($sql);

    $i = 0;
    while (! $rs->EOF) {

        $arr[$i] = $rs->fields;

        $arr[$i]['type'] = $type;
        $arr[$i]['tbl']  = $tbl;

        //cIdentifyId

        if (preg_match("/^\d{8}$/", $rs->fields['cIdentifyId'])) { //8
            $arr[$i]['checkIden'] = 2;
        }

        $i++;
        $rs->MoveNext();
    }

    return $arr;
}

function bankdate($conn, $cEscrowBankAccount)
{
    // 支出部分
    $sql_tra = '
	SELECT
		tBankLoansDate,
		tObjKind,
		tKind,
		tMoney,
		tTxt
	FROM
		tBankTrans
	WHERE
		tVR_Code="' . $cEscrowBankAccount . '"
		AND tKind ="保證費"
	ORDER BY
		tExport_time
	ASC ;
	';

    $rs = $conn->Execute($sql_tra);
                        // $arr_tra[] = '' ;
    $cCertifyDate = ''; // 初始化變數
    while (! $rs->EOF) {

        $cCertifyDate = $rs->fields['tBankLoansDate'];

        $rs->MoveNext();
    }

    return $cCertifyDate;
}

// echo 'cSignCategory='.$cSignCategory;
// exit;
//是否匯出進銷檔，匯出就不能改 20150918
$sql = "SELECT cInvoiceClose FROM  tContractCase WHERE cCertifiedId = '" . $cCertifiedId . "'";

$rs = $conn->Execute($sql);

$close = $rs->fields['cInvoiceClose']; //
##

// 初始化變數，避免 Undefined variable 警告
$data_o         = isset($data_o) ? $data_o : [];
$interest_total = isset($interest_total) ? $interest_total : 0;
$_bal           = isset($_bal) ? $_bal : 0;
$BankListChk    = isset($BankListChk) ? $BankListChk : '';
$bankList       = isset($bankList) ? $bankList : '';
$cCertifyDate   = isset($cCertifyDate) ? $cCertifyDate : '';
$data_o2        = isset($data_o2) ? $data_o2 : [];
$data_b         = isset($data_b) ? $data_b : [];
$data_b2        = isset($data_b2) ? $data_b2 : [];
$data_r         = isset($data_r) ? $data_r : [];
$data_s         = isset($data_s) ? $data_s : [];
$owner_another  = isset($owner_another) ? $owner_another : [];
$buyer_another  = isset($buyer_another) ? $buyer_another : [];
$realty_another = isset($realty_another) ? $realty_another : [];
$scr_another    = isset($scr_another) ? $scr_another : [];
$_show          = isset($_show) ? $_show : '';

if (isset($data_o[0]['cInterestEdit']) && 'N' == $data_o[0]['cInterestEdit']) {
    $_bal = 0;
}

$smarty->assign('close', $close);
$smarty->assign('interest_total', $interest_total);
$smarty->assign('_bal', $_bal);
$smarty->assign('cCertifiedId', $cCertifiedId);
$smarty->assign('_res', $_res);
$smarty->assign('BankListChk', $BankListChk);
$smarty->assign('bankList', $bankList);
$smarty->assign('cId', $cId);
$smarty->assign('_show', $_show);
$smarty->assign('records', $records);
$smarty->assign('save', $save);
$smarty->assign('date', bankdate($conn, $cEscrowBankAccount));
$smarty->assign('data_o', $data_o);
$smarty->assign('data_o2', $data_o2);
$smarty->assign('data_b', $data_b);
$smarty->assign('data_b2', $data_b2);
$smarty->assign('data_r', $data_r);
$smarty->assign('data_s', $data_s);
//指定對象
$smarty->assign('owner_another', $owner_another);
$smarty->assign('buyer_another', $buyer_another);
$smarty->assign('realty_another', $realty_another);
$smarty->assign('scr_another', $scr_another);
// echo 'cSignCategory='.$cSignCategory;
// exit;
##
// 確保 $cSignCategory 已定義
$cSignCategory = isset($cSignCategory) ? $cSignCategory : '';
$smarty->assign('cSignCategory', $cSignCategory);

$smarty->display('int_dealing.inc.tpl', '', 'escrow');
