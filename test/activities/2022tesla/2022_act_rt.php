<?php
require_once dirname(dirname(dirname(__DIR__))) . '/openadodb.php';

/* 日期範圍 */
require_once __DIR__ . '/dateRange.php';
/***********/

$sql = 'SELECT
		cas.cCertifiedId as cCertifiedId,
		cas.cDealId as cDealId,
		cas.cSignDate as cSignDate,
		cas.cApplyDate as cApplyDate,
        cas.cFinishDate3 as cFinishDate3,
		rea.cBrand,
		rea.cBrand1,
		rea.cBrand2,
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum) AS bCategory,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum1) AS bCategory1,
		(SELECT bCategory FROM tBranch WHERE bId=rea.cBranchNum2) AS bCategory2,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brand,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brand1,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brand2,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS branch,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS branch1,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS branch2,
		rea.cServiceTarget as cServiceTarget,
		rea.cServiceTarget1 as cServiceTarget1,
		rea.cServiceTarget2 as cServiceTarget2,
		own.cIdentifyId as ownerId,
		own.cName as ownerName,
		buy.cIdentifyId as buyerId,
		buy.cMobileNum AS buymobile,
		own.cMobileNum AS ownmobile,
		buy.cName as buyerName,
        sts.sName as caseStatus
	FROM
		tContractCase AS cas
	JOIN
		tContractRealestate AS rea ON rea.cCertifyId = cas.cCertifiedId
	LEFT JOIN
		tContractOwner AS own ON own.cCertifiedId = cas.cCertifiedId
	LEFT JOIN
		tContractBuyer AS buy ON buy.cCertifiedId = cas.cCertifiedId
    JOIN
        tStatusCase AS sts ON cas.cCaseStatus = sts.sId
	WHERE
		cas.cSignDate >= "' . $fromDate . '"
		AND cas.cSignDate <= "' . $toDate . '"
		AND (rea.cBrand = 1 OR rea.cBrand1 = 1 OR rea.cBrand2 = 1)
        AND cas.cCaseStatus <> 2 AND cas.cCaseStatus <> 3 AND cas.cCaseStatus <> 10
	GROUP BY cas.cCertifiedId
	ORDER BY
		cas.cSignDate,cas.cDealId

	ASC;
';
##

$rs = $conn->Execute($sql);
$i  = 0;
//保證號碼+服務費>0+買方or賣方
//服務對象：1.買賣方、2.賣方、3.買方
while (!$rs->EOF) {
    $rs->fields['cSignDate']    = substr($rs->fields['cSignDate'], 0, 10);
    $rs->fields['cSignDate']    = DateChange($rs->fields['cSignDate']);
    $rs->fields['cApplyDate']   = substr($rs->fields['cApplyDate'], 0, 10);
    $rs->fields['cApplyDate']   = DateChange($rs->fields['cApplyDate']);
    $rs->fields['cFinishDate3'] = substr($rs->fields['cFinishDate3'], 0, 10);
    $rs->fields['cFinishDate3'] = DateChange($rs->fields['cFinishDate3']);

    //第一間店
    if ($rs->fields['cBrand'] == 1) {
        $category = verifyBranch($rs->fields['cBranchNum']);

        if ($rs->fields['cServiceTarget'] == 1) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum'], $rs->fields['cCertifiedId'], "買方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '買方';
            $data[$i]['Charge'] = '';

            $i++;
            $service_charge = null;unset($service_charge);

            $service_charge = checkServiecFee($rs->fields['cBranchNum'], $rs->fields['cCertifiedId'], "賣方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '賣方';
            $data[$i]['Charge'] = empty($service_charge) ? '' : $service_charge;

            $i++;
            $service_charge = null;unset($service_charge);

        } elseif ($rs->fields['cServiceTarget'] == 2) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum'], $rs->fields['cCertifiedId'], "賣方");

            if (!empty($service_charge)) { //是否有付服務費
                $data[$i]             = $rs->fields;
                $data[$i]['Category'] = $category;

                $data[$i]['Target'] = '賣方';
                $data[$i]['Charge'] = empty($service_charge) ? '' : $service_charge;

                $i++;
            }
            $service_charge = null;unset($service_charge);

        } elseif ($rs->fields['cServiceTarget'] == 3) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum'], $rs->fields['cCertifiedId'], "買方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '買方';
            $data[$i]['Charge'] = '';

            $i++;
            $service_charge = null;unset($service_charge);
        }
    }
    //第二間店
    if ($rs->fields['cBrand1'] == 1) {
        $category = verifyBranch($rs->fields['cBranchNum1']);

        if ($rs->fields['cServiceTarget1'] == 1) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum1'], $rs->fields['cCertifiedId'], "買方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '買方';
            $data[$i]['Charge'] = '';

            $i++;
            $service_charge = null;unset($service_charge);

            $service_charge = checkServiecFee($rs->fields['cBranchNum1'], $rs->fields['cCertifiedId'], "賣方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '賣方';
            $data[$i]['Charge'] = empty($service_charge) ? '' : $service_charge;

            $i++;
            $service_charge = null;unset($service_charge);

        } elseif ($rs->fields['cServiceTarget1'] == 2) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum1'], $rs->fields['cCertifiedId'], "賣方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '賣方';
            $data[$i]['Charge'] = empty($service_charge) ? '' : $service_charge;

            $i++;
            $service_charge = null;unset($service_charge);

        } elseif ($rs->fields['cServiceTarget1'] == 3) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum1'], $rs->fields['cCertifiedId'], "買方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '買方';
            $data[$i]['Charge'] = '';

            $i++;
            $service_charge = null;unset($service_charge);

        }
    }

    //第三間店
    if ($rs->fields['cBrand2'] == 1) {
        $category = verifyBranch($rs->fields['cBranchNum2']);

        if ($rs->fields['cServiceTarget2'] == 1) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum2'], $rs->fields['cCertifiedId'], "買方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '買方';
            $data[$i]['Charge'] = '';

            $i++;
            $service_charge = null;unset($service_charge);

            $service_charge = checkServiecFee($rs->fields['cBranchNum2'], $rs->fields['cCertifiedId'], "賣方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '賣方';
            $data[$i]['Charge'] = empty($service_charge) ? '' : $service_charge;

            $i++;
            $service_charge = null;unset($service_charge);

        } elseif ($rs->fields['cServiceTarget'] == 2) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum2'], $rs->fields['cCertifiedId'], "賣方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '賣方';
            $data[$i]['Charge'] = empty($service_charge) ? '' : $service_charge;

            $i++;
            $service_charge = null;unset($service_charge);

        } elseif ($rs->fields['cServiceTarget'] == 3) {
            $service_charge = checkServiecFee($rs->fields['cBranchNum2'], $rs->fields['cCertifiedId'], "買方");

            $data[$i]             = $rs->fields;
            $data[$i]['Category'] = $category;

            $data[$i]['Target'] = '買方';
            $data[$i]['Charge'] = '';

            $i++;
            $service_charge = null;unset($service_charge);

        }
    }

    $rs->MoveNext();
}

$fh = __DIR__ . '/RT' . date("Ymd") . '.csv';
file_put_contents($fh, "\xEF\xBB\xBF");
file_put_contents($fh, '保證號碼,簽約日期,建檔日期,案件狀態,狀態時間' . "\r\n", FILE_APPEND);

if (!empty($data)) {
    foreach ($data as $v) {
        $txt = $v['cCertifiedId'] . '_,' . $v['cSignDate'] . ',' . $v['cApplyDate'] . ',' . $v['caseStatus'] . ',' . $v['cFinishDate3'];
        file_put_contents($fh, $txt . "\r\n", FILE_APPEND);
    }
}

exit('Done!!(' . date("Y-m-d G:i:s") . ')');

//確認加盟店類型
function checkCategory($bId)
{
    global $conn;
    $ct = false;

    if ($bId > 0) {
        $sql = 'SELECT * FROM tBranch WHERE bId="' . $bId . '";';
        $rel = $conn->Execute($sql);

        //確認案件為台灣房屋加盟店
        if ($rel->fields['bBrand'] == '1' && $rel->fields['bCategory'] == 1) { //台灣房屋
            $ct = true;
        }
        ##
    }

    return $ct;
}

//確認仲介店類型
function verifyBranch($bId)
{
    global $conn;

    if ($bId > 0) {
        $sql = 'SELECT * FROM tBranch WHERE bId="' . $bId . '";';
        $rel = $conn->Execute($sql);

        //確認案件為台灣房屋加盟店
        if ($rel->fields['bBrand'] == 1) { //台灣房屋
            if ($rel->fields['bCategory'] == 1) {
                return '加盟';
            } else if ($rel->fields['bCategory'] == 2) {
                return '直營';
            }
        }
        ##
    }

    return '';
}

function checkServiecFee($branch, $cId, $target)
{ //
    global $conn;

    $check = false;

    $sql = "SELECT tMoney, tTxt FROM tBankTrans WHERE tMemo = '" . $cId . "' AND tStoreId = '" . $branch . "'";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        if (preg_match("/服務費/", $rs->fields['tTxt']) && preg_match("/" . $target . "/", $rs->fields['tTxt'])) {
            $check = $rs->fields['tMoney'];
        }

        $rs->MoveNext();
    }

    return $check;
}

function DateChange($val)
{
    $tmp = explode('-', $val);
    $val = $tmp[0] . "/" . (int) $tmp[1] . "/" . $tmp[2];

    return $val;
}

function getAddr($cId)
{
    global $conn;

    $sql = "SELECT
				(SELECT CONCAT(zCity,zArea) AS country FROM tZipArea WHERE zZip =cZip) AS country,
				cAddr,
				cLevelNow
			FROM
				tContractProperty
			WHERE
				cCertifiedId ='" . $cId . "'";
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $floor = '';
        if ($cId == '005077128') {
            // echo $cId;
            $floor = $rs->fields['cLevelNow'] . "樓";
        }

        $arr[] = $rs->fields['country'] . $rs->fields['cAddr'] . $floor;

        $rs->MoveNext();
    }

    if (is_array($arr)) {
        return implode('_', $arr);
    } else {
        return false;
    }
}
##
