<?php
require_once dirname(dirname(dirname(__DIR__))) . '/openadodb.php';

/* 日期範圍 */
$fromDate = '2022-10-01 00:00:00';
$toDate   = '2023-02-28 18:00:00';

$creatingFrom = '2022-10-01 00:00:00';
// $creatingTo   = '2023-02-28 18:00:00';
$creatingTo = '2022-12-05 23:59:59';
/***********/

//確認加盟店類型
function checkCategory($bId, $con)
{
    $ct = false;

    if ($bId > 0) {
        $sql = 'SELECT * FROM tBranch WHERE bId="' . $bId . '";';
        $rel = $con->Execute($sql);

        //確認案件為台灣房屋加盟店
        if ($rel->fields['bBrand'] == '1') { //台灣房屋
            $ct = true;
        }
        ##
    }

    return $ct;
}
##

$sql = 'SELECT
		cas.cCertifiedId as cCertifiedId,
		cas.cDealId as cDealId,
		cas.cSignDate as cSignDate,
		cas.cApplyDate as cApplyDate,
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
		AND cas.cApplyDate >= "' . $creatingFrom . '"
		AND cas.cApplyDate <= "' . $creatingTo . '"
		AND (rea.cBrand = 1 OR rea.cBrand1 = 1 OR rea.cBrand2 = 1)
	GROUP BY cas.cCertifiedId
	ORDER BY
		cas.cSignDate,cas.cDealId

	ASC;
';
##

//取得主要資料
$rs = $conn->Execute($sql);
$i  = 0;

function DateChange($val)
{
    $tmp = explode('-', $val);
    $val = $tmp[0] . "/" . (int) $tmp[1] . "/" . $tmp[2];

    return $val;
}

while (!$rs->EOF) {
    $rs->fields['cSignDate']  = substr($rs->fields['cSignDate'], 0, 10);
    $rs->fields['cSignDate']  = DateChange($rs->fields['cSignDate']);
    $rs->fields['cApplyDate'] = substr($rs->fields['cApplyDate'], 0, 10);
    $rs->fields['cApplyDate'] = DateChange($rs->fields['cApplyDate']);

    $rs->fields['ownerId']   = strtoupper($rs->fields['ownerId']);
    $rs->fields['ownerName'] = strtoupper($rs->fields['ownerName']);
    $rs->fields['buyerId']   = strtoupper($rs->fields['buyerId']);
    $rs->fields['buyerName'] = strtoupper($rs->fields['buyerName']);

    $rs->fields['cAddr'] = str_replace(',', '，', getAddr($rs->fields['cCertifiedId']));

    $fg = 0;
    if ($rs->fields['cBrand'] == '1') {$fg++;}

    if ($rs->fields['cBrand1'] == '1') {$fg++;}

    if ($rs->fields['cBrand2'] == '1') {$fg++;}

    if ($fg > 0) {
        $list[$i]         = $rs->fields;
        $list[$i]['type'] = checkType($conn, $rs->fields);
        $list[$i]['fg']   = $fg;

        if ($rs->fields['cBrand'] == '1') {
            if ($rs->fields['bCategory'] == 1) {
                $tmp[] = '加盟';
            } elseif ($rs->fields['bCategory'] == 2) {
                $tmp[] = '直營';
            }
        }

        if ($rs->fields['cServiceTarget'] == 3) {
            $list[$i]['buyStore'] = $list[$i]['brand'] . $list[$i]['branch']; //1.買賣方、2.賣方、3.買方
        } elseif ($rs->fields['cServiceTarget'] == 2) {
            $list[$i]['ownStore'] = $list[$i]['brand'] . $list[$i]['branch'];
        } else {
            $list[$i]['buyStore'] = $list[$i]['brand'] . $list[$i]['branch']; //1.買賣方、2.賣方、3.買方
            $list[$i]['ownStore'] = $list[$i]['brand'] . $list[$i]['branch'];
        }

        if ($rs->fields['cBranchNum1'] > 0) {
            if ($rs->fields['cBrand1'] == '1') {
                if ($rs->fields['bCategory1'] == 1) {
                    $tmp[] = '加盟';
                } elseif ($rs->fields['bCategory1'] == 2) {
                    $tmp[] = '直營';
                }
            }

            if ($rs->fields['cServiceTarget1'] == 3) {
                $list[$i]['buyStore'] = $list[$i]['brand1'] . $list[$i]['branch1']; //1.買賣方、2.賣方、3.買方
            } elseif ($rs->fields['cServiceTarget1'] == 2) {
                $list[$i]['ownStore'] = $list[$i]['brand1'] . $list[$i]['branch1'];
            } else {
                $list[$i]['buyStore'] = $list[$i]['brand1'] . $list[$i]['branch1']; //1.買賣方、2.賣方、3.買方
                $list[$i]['ownStore'] = $list[$i]['brand1'] . $list[$i]['branch1'];
            }
        }

        $list[$i]['cate'] = @implode('_', $tmp);
        unset($tmp);
        $i++;
    }

    $rs->MoveNext();
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

function checkType($conn, $arr)
{
    $type = 0;
    if ($arr['cBrand'] == 1 && $arr['cBranchNum'] > 0) {
        if ($arr['cServiceTarget'] == 1) {
            return 5; //5 2+3(賣+買)
        } else {
            $type = $arr['cServiceTarget'];
        }
    }

    if ($arr['cBrand1'] == 1 && $arr['cBranchNum1'] > 0) {
        if ($arr['cServiceTarget1'] == 1) {
            return 5; //5 2+3(賣+買)
        } else {
            $type = $arr['cServiceTarget1'];
        }
    }

    $type = $arr['cServiceTarget'] + $arr['cServiceTarget1'];

    return $type;
}
##

//查詢多組買賣方身分證字號
$max = count($list);
for ($i = 0; $i < $max; $i++) {
    $sql = 'SELECT cIdentity,cIdentifyId,cName,cMobileNum FROM tContractOthers WHERE cCertifiedId="' . $list[$i]['cCertifiedId'] . '" ORDER BY cId ASC;';

    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        if ($rs->fields['cIdentity'] == '1' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 3)) {
            $list[$i]['buyerId'] .= '_' . strtoupper($rs->fields['cIdentifyId']);
            $list[$i]['buyerName'] .= '_' . $rs->fields['cName'];
            $list[$i]['buymobile'] .= '_' . $rs->fields['cMobileNum'];
        } else if ($rs->fields['cIdentity'] == '2' && ($list[$i]['type'] == 5 || $list[$i]['type'] == 2)) {
            $list[$i]['ownerId'] .= '_' . strtoupper($rs->fields['cIdentifyId']);
            $list[$i]['ownerName'] .= '_' . $rs->fields['cName'];
            $list[$i]['ownmobile'] .= '_' . $rs->fields['cMobileNum'];
        }
        $rs->MoveNext();
    }
}
##

$fh = fopen('A_all.csv', 'w');
fwrite($fh, "\xEF\xBB\xBF");
fwrite($fh, '保證號碼,簽約日期,建檔日期,案件狀態,買方身份證字號,賣方身份證字號,物件地址,類型,買方姓名,賣方姓名,買方電話,賣方電話,買方仲介店,賣方仲介店' . "\r\n");
for ($i = 0; $i < count($list); $i++) {
    if (preg_match("/直營/", $list[$i]['cate'])) {
        fwrite($fh, $list[$i]['cCertifiedId'] . '_,' . $list[$i]['cSignDate'] . ',' . $list[$i]['cApplyDate'] . ',' . $list[$i]['caseStatus'] . ',' . $list[$i]['buyerId'] . ',' . $list[$i]['ownerId'] . ',' . $list[$i]['cAddr'] . ',' . $list[$i]['cate'] . ',' . $list[$i]['buyerName'] . ',' . $list[$i]['ownerName'] . ',' . $list[$i]['buymobile'] . ',' . $list[$i]['ownmobile'] . ',' . $list[$i]['buyStore'] . ',' . $list[$i]['ownStore'] . "\r\n");
    }
}
fclose($fh);

echo 'Done!!(' . date("Y-m-d G:i:s") . ')';
