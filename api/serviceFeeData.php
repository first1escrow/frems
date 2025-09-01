<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once $GLOBALS['webssl_upload'] . '/api/api_function.php';

$company = json_decode(file_get_contents(dirname(dirname(dirname(__FILE__))) . '/lib/company.json'), true);
$_GET    = escapeStr($_GET);
$cId     = $_GET['cId'];
$sql     = "
		SELECT
			cc.cCertifiedId,
			cc.cSignDate AS cSignDate,
			cb.cName AS buyer,
			cb.cIdentifyId AS buyerId,
			cb.cMobileNum AS buyerphone,
			cb.sAgentName1 AS buyersale,
			cb.sAgentName2 AS buyersale1,
			cb.sAgentMobile1 AS buyersalephone,
			cb.sAgentMobile2 AS buyersalephone1,
			co.cName AS owner,
			co.cIdentifyId AS ownerId,
			co.cMobileNum AS ownerphone,
			co.sAgentName1 AS ownersale,
			co.sAgentMobile1 AS ownersalephone,
			co.sAgentName2 AS ownersale1,
			co.sAgentMobile2 AS ownersalephone1,
			(SELECT sName FROM tScrivener AS s WHERE s.sId=cs.cScrivener) AS Scrivener,
			(SELECT sName FROM tScrivenerSms AS s WHERE s.sId=cs.cManage2) AS Scrivener2,
			(SELECT sCategory FROM tScrivener WHERE sId = cs.cScrivener) AS sCategory,
			ci.cTotalMoney AS cTotalMoney,
			ci.cSignMoney AS cSignMoney,
			ci.cAffixMoney AS cAffixMoney,
			ci.cDutyMoney AS cDutyMoney,
			ci.cEstimatedMoney AS cEstimatedMoney,
			cr.cServiceTarget,
			cr.cServiceTarget1,
			cr.cServiceTarget2,
			cr.cBrand,
			cr.cBrand1,
			cr.cBrand2,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS Brand,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS Brand1,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS Brand2,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS Store,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS Store1,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS Store2,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum) AS StoreName,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum1) AS StoreName1,
			(SELECT bName FROM tBranch WHERE bId = cr.cBranchNum2) AS StoreName2
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractBuyer AS cb ON cb.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractOwner AS co ON co.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
		WHERE
			cc.cCertifiedId = '" . $cId . "'
	   ";

$rs = $conn->Execute($sql);

$data = $rs->fields;

if ($data["cSignDate"] != '') {
    $tmp               = explode('-', substr($data["cSignDate"], 0, 10));
    $tmp[0]            = $tmp[0] - 1911;
    $data['cSignDate'] = $tmp[0] . "年" . $tmp[1] . "月" . $tmp[2] . "日";
    unset($tmp);
}
//
//服務對象：1.買賣方、2.賣方、3.買方
if ($data['cBranchNum'] > 0) {
    if ($data['cServiceTarget'] == 1) {
        $buyerBrand[]  = $data["Brand"];
        $buyerBranch[] = $data["Store"] . "-" . $data['StoreName'];
        $ownerBrand[]  = $data["Brand"];
        $ownerBranch[] = $data["Store"] . "-" . $data['StoreName'];
    } elseif ($data['cServiceTarget'] == 2) {
        $ownerBrand[]  = $data["Brand"];
        $ownerBranch[] = $data["Store"] . "-" . $data['StoreName'];
    } elseif ($data['cServiceTarget'] == 3) {
        $buyerBrand[]  = $data["Brand"];
        $buyerBranch[] = $data["Store"] . "-" . $data['StoreName'];
    }
}

if ($data['cBranchNum1'] > 0) {
    if ($data['cServiceTarget1'] == 1) {
        $buyerBrand[]  = $data["Brand1"];
        $buyerBranch[] = $data["Store1"] . "-" . $data['StoreName1'];
        $ownerBrand[]  = $data["Brand1"];
        $ownerBranch[] = $data["Store1"] . "-" . $data['StoreName1'];
    } elseif ($data['cServiceTarget1'] == 2) {
        $ownerBrand[]  = $data["Brand1"];
        $ownerBranch[] = $data["Store1"] . "-" . $data['StoreName1'];
    } elseif ($data['cServiceTarget1'] == 3) {
        $buyerBrand[]  = $data["Brand1"];
        $buyerBranch[] = $data["Store1"] . "-" . $data['StoreName1'];
    }
}

if ($data['cBranchNum2'] > 0) {
    if ($data['cServiceTarget2'] == 1) {
        $buyerBrand[]  = $data["Brand2"];
        $buyerBranch[] = $data["Store2"] . "-" . $data['StoreName2'];
        $ownerBrand[]  = $data["Brand2"];
        $ownerBranch[] = $data["Store2"] . "-" . $data['StoreName2'];
    } elseif ($data['cServiceTarget2'] == 2) {
        $ownerBrand[]  = $data["Brand2"];
        $ownerBranch[] = $data["Store2"] . "-" . $data['StoreName2'];
    } elseif ($data['cServiceTarget2'] == 3) {
        $buyerBrand[]  = $data["Brand2"];
        $buyerBranch[] = $data["Store2"] . "-" . $data['StoreName2'];
    }
}
//建物
$sql = "
			SELECT
				cAddr,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=cZip) AS city,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=cZip) AS area
			FROM
				 tContractProperty
			WHERE
				cCertifiedId = '" . $cId . "'
				 ORDER BY cItem ASC";
$rs = $conn->Execute($sql);

while (!$rs->EOF) {

    $tmp2[] = $rs->fields['city'] . $rs->fields['area'] . $rs->fields['cAddr'];

    $rs->MoveNext();
}
$data['addr'] = implode(';', $tmp2);

unset($tmp2);

$sql                    = "SELECT cName,cMobileNum FROM  tContractPhone WHERE  cIdentity = 3 AND cCertifiedId = '" . $cId . "' ORDER BY cId ASC LIMIT 1";
$rs                     = $conn->Execute($sql);
$data['buyersale']      = $rs->fields['cName'];
$data['buyersalephone'] = $rs->fields['cMobileNum'];

$sql                    = "SELECT cName,cMobileNum FROM  tContractPhone WHERE  cIdentity = 4 AND cCertifiedId = '" . $cId . "' ORDER BY cId ASC LIMIT 1";
$rs                     = $conn->Execute($sql);
$data['ownersale']      = $rs->fields['cName'];
$data['ownersalephone'] = $rs->fields['cMobileNum'];
##

//經辦
$sql = "SELECT
			p.pName,
			p.pExt,
			p.pFaxNum,
			p.pGender
		FROM
			tContractScrivener AS cs
		LEFT JOIN
			tScrivener AS s ON s.sId = cs.cScrivener
		LEFT JOIN
			tPeopleInfo AS p ON p.pId=s.sUndertaker1
		WHERE
			cs.cCertifiedId = '" . $cId . "'
		";

$rs = $conn->Execute($sql);

$undertaker = $rs->fields;

if ($undertaker['pGender'] == 'M') {
    $undertaker['undertaker'] = mb_substr($undertaker['pName'], 0, 1, "UTF-8") . '先生';
} else {
    $undertaker['undertaker'] = mb_substr($undertaker['pName'], 0, 1, "UTF-8") . '小姐';
}

$tel = ($undertaker['pExt']) ? $company['tel'] . "(" . $undertaker['undertaker'] . "*" . $undertaker['pExt'] . ")" : $company['tel'];

$data["buyerId"] = substr_replace($data["buyerId"], '****', 5,4);
$data["ownerId"] = substr_replace($data["ownerId"], '****', 5,4);
$data['buyerphone'] = substr_replace($data['buyerphone'], '****',5, 4 );
$data['ownerphone'] = substr_replace($data['ownerphone'], '****',5, 4 );
$data['buyersalephone'] = substr_replace($data['buyersalephone'], '****',5, 4 );
$data['ownersalephone'] = substr_replace($data['ownersalephone'], '****',5, 4 );