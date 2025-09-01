<?php

//取得被紀錄的延伸選項
function getActivityRecordExt($aId, $identity, $storeId)
{
    $conn = new first1DB;

    $sql = 'SELECT aContent as json FROM tActivityRecordsExt WHERE aActivityId = :aId AND aIdentity = :iDn AND aStoreId = :sId;';
    $rs  = $conn->one($sql, ['aId' => $aId, 'iDn' => $identity, 'sId' => $storeId]);

    return empty($rs) ? [] : json_decode($rs['json'], true);
}
##

function FeedBackData($id, $type)
{
    global $conn;

    $i = 1;

    $sql = "SELECT * FROM tFeedBackData WHERE fType ='" . $type . "' AND fStoreId ='" . $id . "' AND fStatus = 0";
    $rs  = $conn->Execute($sql);

    while (!$rs->EOF) {
        $data[$i]                = $rs->fields;
        $data[$i]['no']          = $i;
        $data[$i]['stop']        = ($rs->fields['fStop'] == 1) ? 'checked=checked' : '';
        $data[$i]['disabled']    = ($rs->fields['fStop'] == 1) ? 'disabled=disabled' : '';
        $data[$i]['countryC']    = listCity($conn, $rs->fields['fZipC']);
        $data[$i]['areaC']       = listArea($conn, $rs->fields['fZipC']);
        $data[$i]['countryR']    = listCity($conn, $rs->fields['fZipR']);
        $data[$i]['areaR']       = listArea($conn, $rs->fields['fZipR']);
        $data[$i]['bank_branch'] = getBankBranch($conn, $rs->fields['fAccountNum'], $rs->fields['fAccountNumB']);
        $i++;

        $rs->MoveNext();
    }

    return $data;
}

###############其他回饋對象##################
//合約書用
function getFeedBackMoney($cid)
{
    global $conn;

    $data = array();

    $sql = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='" . $cid . "' AND fDelete = 0 AND (fType = 1 OR fType = 2)";
    $rs  = $conn->Execute($sql);

    $i = 0;
    while (!$rs->EOF) {
        $data[$i]          = $rs->fields;
        $data[$i]['store'] = getStore($data[$i]['fType']);
        $i++;

        $rs->MoveNext();
    }

    return $data;
}
//個案回饋
function getIndividualFeedBack($cid)
{
    global $conn;

    $data = array();
    $sql  = "SELECT *,(SELECT bStore FROM `tBranch` WHERE bId = f.fIndividualId) AS name
            FROM tFeedBackMoney AS f WHERE fCertifiedId ='" . $cid . "' AND fDelete = 0 AND fType = 3";
    $rs = $conn->Execute($sql);

    $i = 0;
    while (!$rs->EOF) {
        $data[$rs->fields['fStoreId']][] = $rs->fields;
        $i++;

        $rs->MoveNext();
    }
    return $data;
}

function getStore($type) //合約書其他回饋對象店家option

{
    global $conn;

    if ($type == 1) {
        $sql = "SELECT sName AS Name,sOffice AS Name2,sId AS ID,CONCAT('SC',LPAD(sId,4,'0')) as Code FROM tScrivener ORDER BY sName ASC";
    } elseif ($type == 2) {
        $sql = "SELECT (SELECT bName FROM tBrand AS b WHERE b.bId=bBrand) AS Name,bStore AS Name2 ,bId AS ID,CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as Code FROM tBranch ORDER BY bBrand ASC";
    }

    $rs        = $conn->Execute($sql);
    $option[0] = '請選擇';
    while (!$rs->EOF) {
        $option[$rs->fields['ID']] = $rs->fields['Code'] . $rs->fields['Name'] . '(' . $rs->fields['Name2'] . ')';
        $rs->MoveNext();
    }

    return $option;
}

function updateFeedBackMoney($arr)
{
    global $conn;

    if (is_array($arr['otherFeedId'])) {
        for ($i = 0; $i < count($arr['otherFeedId']); $i++) {
            if ($arr['otherFeedCheck'][$i] == 1) { //有更改才更新
                $sales = getFeedBackOtherSales($arr['otherFeedType' . $arr['otherFeedId'][$i]], $arr['otherFeedstoreId' . $arr['otherFeedId'][$i]]);

                $fw  = fopen(dirname(dirname(__DIR__)) . '/log2/otherFeed/' . $arr['certifiedid'] . '.log', 'a+');
                $sql = "UPDATE tFeedBackMoney SET
                            fType ='" . $arr['otherFeedType' . $arr['otherFeedId'][$i]] . "',
                            fStoreId ='" . $arr['otherFeedstoreId' . $arr['otherFeedId'][$i]] . "',
                            fMoney ='" . $arr['otherFeedMoney'][$i] . "',
                            fSales = '" . $sales . "',
                            fLastEditor ='" . $_SESSION['member_name'] . "'
                        WHERE
                            fId = '" . $arr['otherFeedId'][$i] . "'";
                $conn->Execute($sql);

                fwrite($fw, date('Y-m-d H:i:s') . $sql . "\r\n");
                fclose($fw);
                unset($sales);
            }
        }
    }

    if($arr['cCaseFeedBackModifier'] == '') { //如果有觸發到回饋金重算機制 其他回饋對象的地政士一律刪除 讓業務重新申請
        $fw  = fopen(dirname(dirname(__DIR__)) . '/log2/otherFeed/' . $arr['certifiedid'] . '.log', 'a+');
        $sql = "UPDATE tFeedBackMoney SET fDelete = 1 WHERE fType = 1 AND fCertifiedId = '" . $arr['id'] . "'";
        $conn->Execute($sql);
        fwrite($fw, date('Y-m-d H:i:s') . $sql . "\r\n");
        fclose($fw);
    }
}

function insertFeedBackMoney($arr)
{
    global $conn;

    if (is_array($arr['newotherFeedMoney'])) {
        for ($i = 0; $i < count($arr['newotherFeedMoney']); $i++) {
            if ($_POST['newotherFeedCheck'][$i] == 1) {
                $sales = getFeedBackOtherSales($_POST['newotherFeedType' . $i], $_POST['newotherFeedstoreId' . $i]);

                $fw  = fopen(dirname(dirname(__DIR__)) . '/log2/otherFeed/' . $arr['certifiedid'] . '.log', 'a+');
                $sql = "INSERT INTO tFeedBackMoney (
							fType,
							fCertifiedId,
							fStoreId,
							fMoney,
							fSales,
							fCreatEditor,
							fCreatTime
						) VALUES (
							'" . $_POST['newotherFeedType' . $i] . "',
							'" . $_POST['certifiedid'] . "',
							'" . $_POST['newotherFeedstoreId' . $i] . "',
							'" . $_POST['newotherFeedMoney'][$i] . "',
							'" . $sales . "',
							'" . $_SESSION['member_name'] . "',
							'" . date('Y-m-d H:i:s') . "'
						)";
                $conn->Execute($sql);

                fwrite($fw, date('Y-m-d H:i:s') . $sql . "\r\n");
                fclose($fw);
                unset($sales);
            }
        }
    }
}

function getFeedBackOtherSales($type, $id)
{
    global $conn;

    if ($type == 1) { //scrivener
        $sql = "SELECT sSales FROM  tScrivenerSales  WHERE sScrivener = '" . $id . "'";
    } else {
        $sql = "SELECT bSales AS sSales FROM tBranchSales WHERE bBranch = '" . $id . "'";
    }
    $rs = $conn->Execute($sql);

    $sales = array();
    while (!$rs->EOF) {
        $sales[] = $rs->fields['sSales'];
        $rs->MoveNext();
    }

    return @implode(',', $sales);
}

//回饋案件表
function getOtherFeed_case($cid, $arr, $bId = '', $sId = '', $brand = '')
{ //回饋案件表
    global $conn;

    $str = '';
    if (($sId != 0 && $sId != '') && $bId) { //兩個都查
        $str = "AND (fb.fType in (2,3) AND fStoreId IN(" . $bId . ") OR fb.fType = 1 AND fStoreId IN(" . $sId . "))";
    } else if ($bId) {
        $str = "AND fb.fType in (2,3) AND fStoreId IN(" . $bId . ")";
    } elseif ($sId != 0 && $sId != '') {
        $str .= "AND fb.fType = 1 AND fStoreId IN(" . $sId . ")";
    }

    $sql = "SELECT
                *,
                (SELECT bCategory FROM tBranch WHERE bId =cr.cBranchNum) AS bCategory,
                (SELECT SUBSTR(relay.bExport_time, 1, 10) FROM tBankTransRelay AS relay WHERE relay.bCertifiedId = fb.fCertifiedId AND relay.bKind= '地政士回饋金' Limit 1) AS bExport_time
            FROM
                tFeedBackMoney AS fb,tContractRealestate AS cr
            WHERE
                fb.fCertifiedId=cr.cCertifyId  AND fb.fDelete = 0 AND fb.fCertifiedId ='" . $cid . "'" . $str;

    $rs = $conn->Execute($sql);

    $data = [];
    $i    = 0;
    while (!$rs->EOF) {
        if ($rs->fields['fType'] == 1) { //地政士先以第一間店為主..
            $data[$i]['buyer']              = $arr['buyer'];
            $data[$i]['owner']              = $arr['owner'];
            $data[$i]['cBranchNum']         = '地政士';
            $data[$i]['cTotalMoney']        = $arr['cTotalMoney'];
            $data[$i]['cCaseFeedback']      = 0;
            $data[$i]['cCaseFeedBackMoney'] = $rs->fields['fMoney'];

            $data[$i]['cEndDate']        = $arr['cEndDate'];
            $data[$i]['cSignDate']       = $arr['cSignDate'];
            $data[$i]['cFeedbackTarget'] = '地政士';
            $data[$i]['cBank']           = $arr['cBank'];
            $data[$i]['cCertifiedId']    = $cid;
            $data[$i]['bApplication']    = $arr['bApplication'];
            $data[$i]['cCertifiedMoney'] = $arr['cCertifiedMoney'];
            $tmp                         = getFeedBackStore($rs->fields['fType'], $rs->fields['fStoreId']);

            $data[$i]['bBrand']     = 'SC';
            $data[$i]['bId']        = $rs->fields['fStoreId'];
            $data[$i]['bCategory2'] = '特殊回饋(地政士)';
            $data[$i]['bCategory']  = $data[$i]['bCategory2'] . '(回饋)';
            $data[$i]['bFBTarget']  = $tmp['Code']; //
            $data[$i]['cScrivener'] = $tmp['Name'];
            $data[$i]['bFeedback']  = '回饋';

            $data[$i]['bStoreClass'] = '單店';
            $data[$i]['bStore']      = $tmp['Store'];

            $data[$i]['bFeedDateCat'] = $tmp['FeedDateCat'];
            //隨案結
            if ($tmp['FeedDateCat'] == 2) {
                $data[$i]['exportTime'] = $rs->fields['bExport_time'];
            }
        } elseif ($rs->fields['fType'] == 2) { //仲介
            $data[$i]['buyer']              = $arr['buyer'];
            $data[$i]['owner']              = $arr['owner'];
            $data[$i]['cBranchNum']         = $rs->fields['fStoreId'];
            $data[$i]['cTotalMoney']        = $arr['cTotalMoney'];
            $data[$i]['cCaseFeedback']      = 0;
            $data[$i]['cCaseFeedBackMoney'] = $rs->fields['fMoney'];
            $data[$i]['cEndDate']           = $arr['cEndDate'];
            $data[$i]['cSignDate']          = $arr['cSignDate'];
            $data[$i]['cFeedbackTarget']    = '';
            $data[$i]['cBank']              = $arr['cBank'];
            $data[$i]['cCertifiedId']       = $cid;
            $data[$i]['cCertifiedMoney']    = $arr['cCertifiedMoney'];

            $data[$i]['cScrivener']   = '';
            $data[$i]['bId']          = $rs->fields['fStoreId'];
            $data[$i]['bApplication'] = $arr['bApplication'];

            $tmp = getFeedBackStore($rs->fields['fType'], $rs->fields['fStoreId']);

            $data[$i]['bBrand']     = $tmp['brand'];
            $data[$i]['bFeedback']  = '回饋';
            $data[$i]['bCategory2'] = category_convert2($tmp['bCategory'], $tmp['brand']);

            $data[$i]['bFBTarget'] = $tmp['Code'];

            $data[$i]['bCategory']      = $data[$i]['bCategory2'] . '(回饋)';
            $data[$i]['bStoreClass']    = $tmp['bStoreClass'];
            $data[$i]['bStore']         = $tmp['Store'];
            $data[$i]['bFeedDateCat']   = $tmp['FeedDateCat'];
            $data[$i]['bFeedbackMark2'] = $tmp['FeedbackMark2'];
        } elseif ($rs->fields['fType'] == 3) { //個案
            $data[$i]['buyer']              = $arr['buyer'];
            $data[$i]['owner']              = $arr['owner'];
            $data[$i]['cBranchNum']         = '個案';
            $data[$i]['cTotalMoney']        = $arr['cTotalMoney'];
            $data[$i]['cCaseFeedback']      = 0;
            $data[$i]['cCaseFeedBackMoney'] = $rs->fields['fMoney'];

            $data[$i]['cEndDate']        = $arr['cEndDate'];
            $data[$i]['cSignDate']       = $arr['cSignDate'];
            $data[$i]['cFeedbackTarget'] = '個案';
            $data[$i]['cBank']           = $arr['cBank'];
            $data[$i]['cCertifiedId']    = $cid;
            $data[$i]['bApplication']    = $arr['bApplication'];
            $data[$i]['cCertifiedMoney'] = $arr['cCertifiedMoney'];
            $tmp                         = getIndividualStore($rs->fields['fIndividualId']);

            $data[$i]['bBrand']     = 'BM';
            $data[$i]['bId']        = $rs->fields['fIndividualId'];
            $data[$i]['bCategory2'] = '個案回饋';
            $data[$i]['bCategory']  = $data[$i]['bCategory2'] . '(回饋)';
            $data[$i]['bFBTarget']  = 'BM' . str_pad($tmp['bId'], 5, '0', STR_PAD_LEFT); //
            $data[$i]['Name']       = $tmp['bName'];
            $data[$i]['bFeedback']  = '回饋';

            $data[$i]['bStoreClass'] = '單店';
            $data[$i]['bStore']      = $tmp['bStore'];

            $data[$i]['bFeedDateCat'] = '';
        }

        if ($brand) {
            if ($tmp['brandId'] == $brand) {
                $i++;
            } else {
                unset($data[$i]);
            }
        } else {
            $i++;
        }

        unset($tmp);
        $rs->MoveNext();
    }

    return $data;
}

function category_convert2($str = '0', $code = '')
{
    switch ($str) {
        case '1':
            if ($code == 'TH') {
                $str = '特殊回饋(台屋)';
            } elseif ($code == 'UM') {
                $str = '特殊回饋(優美)';
            } else {
                $str = '特殊回饋(其他)';
            }

            break;
        case '2':
            $str = '特殊回饋(直營)';
            break;
        case '3':
            $str = '非仲介成交';
            break;
        default:
            $str = '未知';
            break;
    }

    return $str;
}

function getFeedBackStore($type, $id)
{ //回饋案件表
    global $conn;

    if ($type == 1) {
        $sql = "SELECT
					sName AS Name,
					CONCAT('SC',LPAD(sId,4,'0')) as Code,
					sOffice AS Store,
					sFeedDateCat AS FeedDateCat
				FROM
					tScrivener WHERE sId ='" . $id . "' ORDER BY sId ASC";
    } elseif ($type == 2) {
        $sql = "SELECT
					bStore AS Name ,
					bBrand AS brandId,
					(Select bCode From `tBrand` c Where c.bId = bBrand ) AS brand,
					CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as Code,
					bCategory AS bCategory,
					bStoreClass AS bStoreClass,
					bStore AS Store,
					bFeedDateCat AS FeedDateCat,
					bFeedbackMark2 AS FeedbackMark2,
					bFeedbackAllCase AS bFeedbackAllCase
				FROM
					tBranch WHERE bId ='" . $id . "'";
    }
    $rs = $conn->Execute($sql);

    if ($rs->fields['bStoreClass'] == 1) {
        $rs->fields['bStoreClass'] = '總店';
    } elseif ($rs->fields['bStoreClass'] == 2) {
        $rs->fields['bStoreClass'] = '單店';
    }

    return $rs->fields;
}

function getIndividualStore($id)
{
    global $conn;

    $sql = "SELECT bBrand AS brandId, bId, bName, bStore  FROM `tBranch` WHERE bId ='" . $id . "'";
    $rs  = $conn->Execute($sql);
    return $rs->fields;
}
//業績統計表
function getOtherFeedSales($sales)
{
    global $conn;

    $sql = "SELECT pName FROM tPeopleInfo WHERE pId ='" . $sales . "'";
    $rs  = $conn->Execute($sql);

    return $rs->fields['pName'];
}

function getOtherFeed2($data) //把部分欄位取代

{
    global $conn;

    if ($data['sid'] > 0) {
        $str .= "AND fSales = '" . $data['sid'] . "'";
    }

    $sql   = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='" . $data['cCertifiedId'] . "' " . $str . "  AND fDelete = 0";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    $i = 0;
    if ($total == 0) {
        return false;
    } else {
        while (!$rs->EOF) {
            $arr[$i] = $data;
            $tmp     = getOtherFeed($rs->fields['fType'], $rs->fields['fStoreId']);
            $sales   = getOtherFeedSales($rs->fields['fSales']);

            if ($rs->fields['fType'] == 2) {
                $arr[$i]['bid']        = $rs->fields['fStoreId'];
                $arr[$i]['store']      = $tmp['Store'];
                $arr[$i]['branch']     = $tmp['Name'];
                $arr[$i]['cBranchNum'] = $rs->fields['fStoreId'];
                $arr[$i]['cBrand']     = $tmp['brandCode'];
                $arr[$i]['bCategory']  = $tmp['bCategory'];
                $arr[$i]['brand']      = $tmp['brand'];
            } elseif ($rs->fields['fType'] == 1) {
                $arr[$i]['scrivener']  = $tmp['Name'];
                $arr[$i]['cScrivener'] = $rs->fields['fStoreId'];

                if ($arr[$i]['cBrand'] == 69) {
                    $arr[$i]['brand']     = $arr[$i]['brand'];
                    $arr[$i]['store']     = $arr[$i]['store'];
                    $arr[$i]['bCategory'] = $arr[$i]['bCategory'];
                    $arr[$i]['branch']    = $arr[$i]['branch'];
                } elseif ($arr[$i]['cBrand1'] == 69) {
                    $arr[$i]['brand']     = $arr[$i]['brand1'];
                    $arr[$i]['store']     = $arr[$i]['store1'];
                    $arr[$i]['bCategory'] = $arr[$i]['bCategory1'];
                    $arr[$i]['branch']    = $arr[$i]['branch1'];
                } elseif ($arr[$i]['cBrand2'] == 69) {
                    $arr[$i]['brand']     = $arr[$i]['brand2'];
                    $arr[$i]['store']     = $arr[$i]['store2'];
                    $arr[$i]['bCategory'] = $arr[$i]['bCategory2'];
                    $arr[$i]['branch']    = $arr[$i]['branch2'];
                }
            }

            $arr[$i]['sid']       = $rs->fields['fSales'];
            $arr[$i]['SalesName'] = $sales;
            $arr[$i]['fType']     = $rs->fields['fType'];

            $i++;
            unset($tmp, $sales);

            $rs->MoveNext();
        }
    }

    return $arr;
}

function getOtherFeed($type, $id, $individualId = null)
{
    global $conn;

    if ($type == 2) {
        $sql = "SELECT
					b.bStore AS Store ,
					b.bName AS Name,
					(Select bCode From `tBrand` c Where c.bId = b.bBrand ) AS brandCode,
					(Select bName From `tBrand` c Where c.bId = b.bBrand ) AS brand,
					CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as Code,
					b.bCategory
				FROM
					tBranch AS b
				WHERE
					b.bId ='" . $id . "'";
    } elseif ($type == 1) {
        $sql = "SELECT
					s.sName AS Name,
					CONCAT('SC',LPAD(s.sId,4,'0')) as Code,
					s.sOffice AS Store
				FROM
					tScrivener AS s
				WHERE s.sId ='" . $id . "'";
    } elseif ($type == 3) {
        $sql = "SELECT
					b.bStore AS Store ,
					b.bName AS Name,
					(Select bCode From `tBrand` c Where c.bId = b.bBrand ) AS brandCode,
					(Select bName From `tBrand` c Where c.bId = b.bBrand ) AS brand,
					CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as Code,
					b.bCategory
				FROM
					tBranch AS b
				WHERE
					b.bId ='" . $individualId . "'";
    }
    $rs = $conn->Execute($sql);

    return $rs->fields;
}

function getOtherFeedMoney($id)
{
    global $conn;

    $sql = "SELECT COUNT(`fId`) AS fCount,SUM(`fMoney`) AS fMoney FROM tFeedBackMoney WHERE fCertifiedId ='" . $id . "' AND fDelete = 0 AND (fType = 1 OR fType = 2)";
    $rs  = $conn->Execute($sql);

    return $rs->fields;
}

####################回饋寄信##########################33

function getScrivenerData($sId)
{
    global $conn;
    //已填（地政士）XXX地政士事務所  OOO-小姐收（稱謂會再調整）
    //未填寫（地政士）XXX地政士事務所  地政士姓名-小姐收

    ##有KEY稱謂
    //XXX地政士事務所-OOO 台啟（稱謂會再調整）
    ##沒有KEY稱謂
    //XXX地政士事務所-地政士姓名(帶回饋資料填的姓名) 台啟
    ##回饋資料沒有KEY的
    //XXX地政士事務所-地政士姓名(基本資料) 台啟

    $sql = "SELECT
				s.sName,
				s.sOffice,
				CONCAT('SC', LPAD(s.sId,4,'0')) AS sCode
		   	FROM
				tScrivener AS s
			WHERE
				s.sId ='" . $sId . "' ";
    $rs = $conn->Execute($sql);

    $i             = 0;
    $data_feedData = FeedBackData($sId, 1);

    if (is_array($data_feedData)) {
        foreach ($data_feedData as $key => $value) {
            $data[$i]['zip']  = $value['fZipC'];
            $data[$i]['addr'] = trim($value['fAddrC']);
            $data[$i]['code'] = $rs->fields['sCode'];
            $data[$i]['note'] = $value['fNote'];
            $tmp[]            = $rs->fields['sOffice'];

            if ($value['fRtitle']) {
                $value['fRtitle'] = str_replace("收", '', $value['fRtitle']);
                $value['fRtitle'] = str_replace("先生", '', $value['fRtitle']);
                $value['fRtitle'] = str_replace("小姐", '', $value['fRtitle']);

                $data[$i]['fRtitle'] = $value['fRtitle'];

                $tmp[] = $value['fRtitle'] . "　台啟";
            } else {
                $tmp[] = $value['fTitle'] . "　台啟";
            }

            $data[$i]['title'] = implode('-', $tmp);

            unset($tmp);
            $i++;
        }
    } else {
        $data[$i]['title'] = $rs->fields['sOffice'] . "-" . $rs->fields['sName'] . "　台啟";
        $data[$i]['code']  = $rs->fields['sCode'];
        $i++;
    }

    return $data;
}

function getBranchData($bId)
{
    global $conn;

    ##有KEY稱謂
    //稱謂內容 台啟

    ##沒有KEY稱謂
    //店東 台啟

    ##回饋資料沒有KEY的
    //XXX仲介店-店東 台啟

    $sql = "SELECT
				b.bfnote,
				b.bStore,
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode
			FROM
				tBranch  AS b

			WHERE
				b.bId = '" . $bId . "'";
    $rs = $conn->Execute($sql);

    $i = 0;

    $data_feedData = FeedBackData($bId, 2);
    if (is_array($data_feedData)) {
        foreach ($data_feedData as $key => $value) {
            $data[$i]['code'] = $rs->fields['bCode'];
            $data[$i]['zip']  = $value['fZipC'];
            $data[$i]['addr'] = trim($value['fAddrC']);
            $data[$i]['note'] = $value['fNote'];

            if ($value['fRtitle']) {
                $value['fRtitle'] = str_replace("收", '', $value['fRtitle']);
                $value['fRtitle'] = str_replace("先生", '', $value['fRtitle']);
                $value['fRtitle'] = str_replace("小姐", '', $value['fRtitle']);

                $data[$i]['fRtitle'] = $value['fRtitle'];

                $data[$i]['title'] = $value['fRtitle'] . "　台啟";
            } else {
                $data[$i]['title'] = $rs->fields['bStore'] . "店東　台啟";
            }

            unset($tmp);
            $i++;
        }
    } else {
        $data[$i]['title'] = $rs->fields['bStore'] . "店東　台啟";
        $data[$i]['code']  = $rs->fields['bCode'];
        $i++;
    }

    $rs->MoveNext();

    return $data;
}

function getOtherFeed3($cid)
{ //案件數量統計表也有用
    global $conn;

    $sql = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId = '" . $cid . "'  AND fDelete = 0 AND (fType = 1 OR fType = 2)";
    $rs  = $conn->Execute($sql);

    $i    = 0;
    $list = [];
    while (!$rs->EOF) {
        $list[$i] = $rs->fields;
        $tmp      = getStoreData($list[$i]['fType'], $list[$i]['fStoreId']);

        $exp = explode(',', $rs->fields['fSales']);
        for ($j = 0; $j < count($exp); $j++) {
            $exp[$j] = getOtherFeedSales($exp[$j]);
        }
        $sales = implode(',', $exp);

        $list[$i]['store']     = $tmp['Name'];
        $list[$i]['storeType'] = $tmp['brandId']; //仲介品牌
        $list[$i]['reNote']    = $tmp['bRenote']; //回饋金的備註
        $list[$i]['sales']     = $sales;
        $list[$i]['salesId']   = $rs->fields['fSales'];

        unset($tmp);unset($sales);

        $i++;
        $rs->MoveNext();
    }

    return $list;
}

function getStoreData($type, $id)
{

    global $conn;

    if ($type == 2) {
        $sql = "SELECT
					b.bStore AS Store ,
					b.bBrand AS brandId,
                    b.bRenote AS bRenote,
					(Select bName From `tBrand` c Where c.bId = b.bBrand ) AS brand,
					CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as Code,
					b.bManager,
                    b.bGroup,
                    bRecall,
                    bScrRecall
				FROM
					tBranch AS b
				WHERE b.bId ='" . $id . "'";
        $rs = $conn->Execute($sql);

        $list['Name']    = $rs->fields['Code'] . $rs->fields['brand'] . $rs->fields['Store'];
        $list['brandId'] = $rs->fields['brandId'];
        $list['bRenote'] = $rs->fields['bRenote'];
    } elseif ($type == 1) {
        $sql = "SELECT
					s.sName AS Name,
					CONCAT('SC',LPAD(s.sId,4,'0')) as Code,
					s.sOffice AS Store,
                    s.sRenote AS sRenote,
                    sRecall,
                    sSpRecall
				FROM
					tScrivener AS s
				WHERE  s.sId ='" . $id . "'";
        $rs = $conn->Execute($sql);

        $list['Name']    = $rs->fields['Code'] . $rs->fields['Name'] . "(" . $rs->fields['Store'] . ")";
        $list['bRenote'] = $rs->fields['sRenote'];
    }

    return $list;
}

###############重新計算回饋金###################
function getFeedMoney($type, $id, $id2 = '', $FeedDateCat = '')
{
    global $conn;

    $cCertifiedId = array();

    $nowMonth = date('m');
    if ($FeedDateCat == 1) { //FeedDateCat 0:季1:月
        $sDate = date('Y-m') . "-01";
        $eDate = date('Y-m') . "-31";
    } else {
        if ($nowMonth >= 1 && $nowMonth <= 3) {
            $sDate = date('Y') . "-01-01";
            $eDate = date('Y') . "-03-31";
        } elseif ($nowMonth >= 4 && $nowMonth <= 6) {
            $sDate = date('Y') . "-04-01";
            $eDate = date('Y') . "-06-30";
        } elseif ($nowMonth >= 7 && $nowMonth <= 9) {
            $sDate = date('Y') . "-07-01";
            $eDate = date('Y') . "-09-30";
        } else {
            $sDate = date('Y') . "-10-01";
            $eDate = date('Y') . "-12-31";
        }
    }

    if ($type == 's') {
        $str = "AND cs.cScrivener='" . $id . "'";
    } elseif ($type == 'b') {
        $str = "AND (cr.cBranchNum = '" . $id . "' OR cr.cBranchNum1 = '" . $id . "' OR cr.cBranchNum2 = '" . $id . "')";
    } elseif ($type == 'c') {
        $str = "AND cc.cCertifiedId ='" . $id . "'";
    } elseif ($type == 'bs') { //品牌回饋代書
        $str = " AND (cr.cBrand = '" . $id . "' OR cr.cBrand1 = '" . $id . "' OR cr.cBrand2 = '" . $id . "') AND cs.cScrivener = '" . $id2 . "'";
    }
    $str .= " AND (cc.cCaseStatus = 2 OR cc.cEndDate >= '" . $sDate . "' AND cc.cEndDate <= '" . $eDate . "')";

    $sql = "SELECT
            cc.cCertifiedId AS cCertifiedId,
            ci.cTotalMoney AS cTotalMoney,
            ci.cCertifiedMoney as cerifiedmoney,
            ci.cFirstMoney as cFirstMoney,
            cr.cBranchNum AS branch,
            cr.cBranchNum1 AS branch1,
            cr.cBranchNum2 AS branch2,
            cr.cBranchNum3 AS branch3,
            cr.cBrand AS brand,
            cr.cBrand1 AS brand1,
            cr.cBrand2 AS brand2,
            cr.cBrand3 AS brand3,
            cr.cServiceTarget AS cServiceTarget,
            cr.cServiceTarget1 AS cServiceTarget1,
            cr.cServiceTarget2 AS cServiceTarget2,
            cr.cServiceTarget3 AS cServiceTarget3,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS bRecall,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS bRecall1,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS bRecall2,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS bRecall3,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS scrRecall,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS scrRecall1,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS scrRecall2,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum3)  AS scrRecall3,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum)  AS bFeedbackMoney,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum1)  AS bFeedbackMoney1,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum2)  AS bFeedbackMoney2,
            (SELECT bFeedbackMoney FROM tBranch WHERE bId=cr.cBranchNum3)  AS bFeedbackMoney3,
            (SELECT sRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall,
            (SELECT sSpRecall2 FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall2,
            (SELECT sFeedbackMoney FROM tScrivener WHERE sId=cs.cScrivener) AS sFeedbackMoney,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandScrRecall,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandScrRecall1,
            (SELECT sRecall FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandScrRecall2,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand AND sDel = 0) AS brandRecall,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand1 AND sDel = 0) AS brandRecall1,
            (SELECT sReacllBrand FROM tScrivenerFeedSp WHERE sScrivener=cs.cScrivener AND sBrand =cr.cBrand2 AND sDel = 0) AS brandRecall2,
            cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
            cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
            cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
            cc.cCaseFeedBackMoney3 AS cCaseFeedBackMoney3,
            cc.cCaseFeedback AS cCaseFeedback,
            cc.cCaseFeedback1 AS cCaseFeedback1,
            cc.cCaseFeedback2 AS cCaseFeedback2,
            cc.cCaseFeedback3 AS cCaseFeedback3,
            cc.cFeedbackTarget AS cFeedbackTarget,
            cc.cFeedbackTarget1 AS cFeedbackTarget1,
            cc.cFeedbackTarget2 AS cFeedbackTarget2,
            cc.cFeedbackTarget3 AS cFeedbackTarget3,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum)  AS branchbook,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum1)  AS branchbook1,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum2)  AS branchbook2,
            (SELECT bCooperationHas FROM tBranch WHERE bId=cr.cBranchNum3)  AS branchbook3,
            cr.cAffixBranch,
            cr.cAffixBranch1,
            cr.cAffixBranch2,
            cr.cAffixBranch3
        FROM
            tContractCase AS cc
        JOIN tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
        JOIN tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
        JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
        WHERE
             ci.cTotalMoney !=0 AND cc.cCaseFeedBackModifier ='' AND ci.cCertifiedMoney !=0 AND cc.cFeedBackClose = 0 AND cc.cFeedBackScrivenerClose = 0  " . $str . "
        ORDER BY cc.cEndDate ASC";
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $list[] = $rs->fields;
        $rs->MoveNext();
    }

    if (is_array($list)) {
        for ($i = 0; $i < count($list); $i++) {
            $cerifiedMoney = ($list[$i]['cTotalMoney'] - $list[$i]['cFirstMoney']) * 0.0006; //應收保證費
            $uSql          = array(
                'cBranchRecall'        => '',
                'cBranchScrRecall'     => '',
                'cScrivenerRecall'     => '',
                'cScrivenerSpRecall'   => '',
                'cBranchRecall1'       => '',
                'cCaseFeedback'        => 0,
                'cCaseFeedback1'       => 0,
                'cCaseFeedback2'       => 0,
                'cCaseFeedback3'       => 0,
                'cCaseFeedBackMoney'   => 0,
                'cCaseFeedBackMoney1'  => 0,
                'cCaseFeedBackMoney2'  => 0,
                'cCaseFeedBackMoney3'  => 0,
                'cFeedbackTarget'      => 1,
                'cFeedbackTarget1'     => 1,
                'cFeedbackTarget2'     => 1,
                'cFeedbackTarget3'     => 1,
                'cBranchRecall2'       => '',
                'cBranchRecall3'       => '',
                'cBrandRecall'         => '',
                'cBrandRecall1'        => '',
                'cBrandRecall2'        => '',
                'cBrandRecall3'        => '',
                'cSpCaseFeedBackMoney' => 0);
            $brecall   = array();
            $scrrecall = array();
            $scrpartsp = array();
            $bcount    = 0;
            $scrpart   = '';

            //確認店家數及地政回饋比率casecheck
            if ($list[$i]['branch'] > 0) {
                if ($list[$i]['cFeedbackTarget'] == 2) { //scrivener
                    $brecall[0] = $list[$i]['sRecall'] / 100; //計算用
                } else {
                    $brecall[0] = $list[$i]['bRecall'] / 100; //計算用
                }
                $uSql['cBranchRecall'] = $list[$i]['bRecall'];
                if ($list[$i]['scrRecall'] != '' && $list[$i]['scrRecall'] != '0') {
                    $scrrecall[0]             = $list[$i]['scrRecall'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall'] != '') {
                    $brecall[0]   = $list[$i]['brandRecall'] / 100;
                    $scrpartsp[0] = $list[$i]['brandScrRecall'] / 100; //地政士部

                    $uSql['cBrandRecall'] = $list[$i]['brandRecall'];
                }

                $bcount++;
            }

            if ($list[$i]['branch1'] > 0) {
                if ($list[$i]['cFeedbackTarget1'] == 2) { //scrivener
                    $brecall[1] = $list[$i]['sRecall'] / 100; //計算用
                } else {
                    $brecall[1] = $list[$i]['bRecall1'] / 100; //計算用
                }

                $uSql['cBranchRecall1'] = $list[$i]['bRecall1'];

                if ($list[$i]['scrRecall1'] != '' && $list[$i]['scrRecall1'] != '0') {
                    $scrrecall[1]             = $list[$i]['scrRecall1'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall1'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall1'] != '') {
                    $brecall[1]            = $list[$i]['brandRecall1'] / 100;
                    $scrpartsp[1]          = $list[$i]['brandScrRecall1'] / 100; //地政士部
                    $uSql['cBrandRecall1'] = $list[$i]['brandRecall1'];
                }

                $bcount++;
            }

            if ($list[$i]['branch2'] > 0) {
                if ($list[$i]['cFeedbackTarget2'] == 2) { //scrivener
                    $brecall[2] = $list[$i]['sRecall'] / 100; //計算用
                } else {
                    $brecall[2] = $list[$i]['bRecall2'] / 100; //計算用
                }

                $uSql['cBranchRecall2'] = $list[$i]['bRecall2'];

                if ($list[$i]['scrRecall2'] != '' && $list[$i]['scrRecall2'] != '0') {
                    $scrrecall[2]             = $list[$i]['scrRecall2'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall2'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall2'] != '') {
                    $brecall[2]            = $list[$i]['brandRecall2'] / 100;
                    $scrpartsp[2]          = $list[$i]['brandScrRecall2'] / 100; //地政士部
                    $uSql['cBrandRecall2'] = $list[$i]['brandRecall2'];
                }

                $bcount++;
            }

            if ($list[$i]['branch3'] > 0) {

                if ($list[$i]['cFeedbackTarget3'] == 2) { //scrivener
                    $brecall[3] = $list[$i]['sRecall'] / 100; //計算用
                } else {
                    $brecall[3] = $list[$i]['bRecall3'] / 100; //計算用
                }

                $uSql['cBranchRecall3'] = $list[$i]['bRecall3'];

                if ($list[$i]['scrRecall2'] != '' && $list[$i]['scrRecall3'] != '0') {
                    $scrrecall[3]             = $list[$i]['scrRecall3'] / 100; //仲介回饋地政士(仲)
                    $uSql['cBranchScrRecall'] = $list[$i]['scrRecall3'];
                }

                //品牌回饋代書
                if ($list[$i]['brandRecall3'] != '') {
                    $brecall[3]            = $list[$i]['brandRecall3'] / 100;
                    $scrpartsp[3]          = $list[$i]['brandScrRecall3'] / 100; //地政士部
                    $uSql['cBrandRecall3'] = $list[$i]['scrRecall3'];
                }

                $bcount++;
            }

            //地政士特殊回饋
            if (count($scrrecall) > 0) {
                rsort($scrrecall); //取一個就好
                $scrpart = $scrrecall[0];
            }

            if (count($scrpartsp) > 0) {

                rsort($scrpartsp); //取一個就好
                $scrpart = $scrpartsp[0];
            }
            unset($scrrecall);unset($scrpartsp);

            $uSql['cScrivenerRecall']   = $list[$i]['sRecall'];
            $uSql['cScrivenerSpRecall'] = $list[$i]['sSpRecall'];

            if (($list[$i]['cerifiedmoney'] + 10) < $cerifiedMoney) {
                $uSql['cCaseFeedback']  = 0;
                $uSql['cCaseFeedback1'] = 0;
                $uSql['cCaseFeedback2'] = 0;
                $uSql['cCaseFeedback3'] = 0;

                if ($bcount == 1) {
                    //第一間無合作契約書給代書
                    if (($list[$i]['branchbook'] == '' || $list[$i]['branchbook'] == 0) && $list[$i]['branch'] > 0 && $list[$i]['brand'] != 1 && $list[$i]['brand'] != 69) {
                        $uSql['cFeedbackTarget'] = 2;
                        if ($list[$i]['sFeedbackMoney'] == 1) { //地政士未收足也要回饋
                            $uSql['cCaseFeedback']      = 0;
                            $uSql['cCaseFeedBackMoney'] = round(($brecall[0] * $list[$i]['cerifiedmoney']));
                        }
                    } else { //
                        if ($list[$i]['bFeedbackMoney'] == 1) {
                            $uSql['cCaseFeedback']      = 0;
                            $uSql['cCaseFeedBackMoney'] = round(($brecall[0] * $list[$i]['cerifiedmoney']));
                        }
                    }
                } else {
                    $branchbookCount = $list[$i]['branchbook'] + $list[$i]['branchbook1'] + $list[$i]['branchbook2'] + $list[$i]['branchbook3'];
                    if ($list[$i]['bFeedbackMoney'] == 1) { //未收足回饋
                        //有合契
                        if (($list[$i]['branchbook'] == '1') || ($list[$i]['brand'] == 1 || $list[$i]['brand'] == 69)) {
                            $uSql['cCaseFeedback']      = 0;
                            $uSql['cCaseFeedBackMoney'] = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback']      = 1;
                            $uSql['cCaseFeedBackMoney'] = 0;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback']   = 0; //要回饋
                            }
                        }
                    } else {
                        //有合契
                        if (($list[$i]['branchbook'] == '1') || ($list[$i]['brand'] == 1 || $list[$i]['brand'] == 69)) {
                            $uSql['cCaseFeedback'] = 0;
                        } else {
                            $uSql['cCaseFeedback'] = 1;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback']   = 0; //要回饋
                            }
                        }
                    }

                    if ($list[$i]['bFeedbackMoney1'] == 1) {
                        //有合契
                        if (($list[$i]['branchbook1'] == '1') || ($list[$i]['brand1'] == 1 || $list[$i]['brand1'] == 69)) {
                            $uSql['cCaseFeedback1']      = 0;
                            $uSql['cCaseFeedBackMoney1'] = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback1']      = 1;
                            $uSql['cCaseFeedBackMoney1'] = 0;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget1'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback1']   = 0; //要回饋
                            }
                        }
                    } else {
                        //有合契
                        if (($list[$i]['branchbook1'] == '1') || ($list[$i]['brand1'] == 1 || $list[$i]['brand1'] == 69)) {
                            $uSql['cCaseFeedback1'] = 0;
                        } else {
                            $uSql['cCaseFeedback1'] = 1;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget1'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback1']   = 0; //要回饋
                            }
                        }
                    }

                    if ($list[$i]['bFeedbackMoney2'] == 1) {
                        //有合契
                        if (($list[$i]['branchbook2'] == '1') || ($list[$i]['brand2'] == 1 || $list[$i]['brand2'] == 69)) {
                            $uSql['cCaseFeedback2']      = 0;
                            $uSql['cCaseFeedBackMoney2'] = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback2']      = 1;
                            $uSql['cCaseFeedBackMoney2'] = 0;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget2'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback2']   = 0; //要回饋
                            }
                        }

                    } else {
                        //有合契
                        if (($list[$i]['branchbook2'] == '1') || ($list[$i]['brand2'] == 1 || $list[$i]['brand2'] == 69)) {
                            $uSql['cCaseFeedback2'] = 0;
                        } else {
                            $uSql['cCaseFeedback2'] = 1;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget2'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback2']   = 0; //要回饋
                            }
                        }
                    }

                    if ($list[$i]['bFeedbackMoney3'] == 1) {
                        //有合契
                        if (($list[$i]['branchbook3'] == '1') || ($list[$i]['brand3'] == 1 || $list[$i]['brand3'] == 69)) {
                            $uSql['cCaseFeedback3']      = 0;
                            $uSql['cCaseFeedBackMoney3'] = round(($brecall[3] * $list[$i]['cerifiedmoney']) / $bcount);
                        } else {
                            $uSql['cCaseFeedback3']      = 0;
                            $uSql['cCaseFeedBackMoney3'] = 0;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget3'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback3']   = 0; //要回饋
                            }
                        }
                    } else {
                        //有合契
                        if (($list[$i]['branchbook3'] == '1') || ($list[$i]['brand3'] == 1 || $list[$i]['brand3'] == 69)) {
                            $uSql['cCaseFeedback3'] = 0;
                        } else {
                            $uSql['cCaseFeedback3'] = 1;
                            if($branchbookCount == 0) {
                                $uSql['cFeedbackTarget3'] = 2; //回饋對象: 地政士
                                $uSql['cCaseFeedback3']   = 0; //要回饋
                            }
                        }
                    }
                }

                $str = array();
                foreach ($uSql as $key => $value) {
                    $str[] = $key . "='" . $value . "'";
                }

                $sql = "UPDATE tContractCase SET " . @implode(',', $str) . " WHERE cCertifiedId ='" . $list[$i]['cCertifiedId'] . "'";
                $conn->Execute($sql);

                continue;
            }

            $brand69 = 0;
            if ($brand > 0 && $brand == 69) {$brand69++;}
            if ($brand1 > 0 && $brand1 == 69) {$brand69++;}
            if ($brand2 > 0 && $brand2 == 69) {$brand69++;}
            if ($brand3 > 0 && $brand3 == 69) {$brand69++;}

            //幸福家
            if ($bcount > 1 && $brand69 == $bcount) {
                //配件只有幸福家
                $o = 0;
                if ($list[$i]['cAffixBranch'] == 1) {
                    $ownerbrand  = $list[$i]['brand'];
                    $ownercol    = 'cCaseFeedBackMoney';
                    $ownerRecall = $brecall[0];
                    $ownercheck  = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['bFeedbackMoney'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $ownerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                    $o++;
                } else {
                    $buyerbrand  = $list[$i]['brand'];
                    $buyercol    = 'cCaseFeedBackMoney';
                    $buyerRecall = $brecall[0];
                    $buyercheck  = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $buyerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                }

                if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                    if ($list[$i]['cAffixBranch1'] == 1) {
                        $ownerbrand  = $list[$i]['brand1'];
                        $ownercol    = 'cCaseFeedBackMoney1';
                        $ownerRecall = $brecall[1];
                        $ownercheck  = $list[$i]['branchbook1'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand1'];
                        $buyercol    = 'cCaseFeedBackMoney1';
                        $buyerRecall = $brecall[1];
                        $buyercheck  = $list[$i]['branchbook1'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                    if ($list[$i]['cAffixBranch2'] == 1) {
                        $ownerbrand  = $list[$i]['brand2'];
                        $ownercol    = 'cCaseFeedBackMoney2';
                        $ownerRecall = $brecall[2];
                        $ownercheck  = $list[$i]['branchbook2'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand2'];
                        $buyercol    = 'cCaseFeedBackMoney2';
                        $buyerRecall = $brecall[2];
                        $buyercheck  = $list[$i]['branchbook2'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                    if ($list[$i]['cAffixBranch3'] == 1) {
                        $ownerbrand  = $list[$i]['brand3'];
                        $ownercol    = 'cCaseFeedBackMoney3';
                        $ownerRecall = $brecall[3];
                        $ownercheck  = $list[$i]['branchbook3'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand3'];
                        $buyercol    = 'cCaseFeedBackMoney3';
                        $buyerRecall = $brecall[3];
                        $buyercheck  = $list[$i]['branchbook3'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                    }
                }

                //以防沒選到契約書用印店(用舊的方法 只回饋給賣方)
                if ($o == 0) {
                    if ($list[$i]['cFeedbackTarget'] == 2) {
                        $ownerbrand  = $list[$i]['brand'];
                        $ownercol    = 'cCaseFeedBackMoney';
                        $ownerRecall = $brecall[0];
                        $ownercheck  = $list[$i]['branchbook'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand'];
                        $buyercol    = 'cCaseFeedBackMoney';
                        $buyerRecall = $brecall[0];
                        $buyercheck  = $list[$i]['branchbook'];
                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyrfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                    }

                    if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                        if ($list[$i]['cFeedbackTarget1'] == 2) {
                            $ownerbrand  = $list[$i]['brand1'];
                            $ownercol    = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck  = $list[$i]['branchbook1'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand  = $list[$i]['brand1'];
                            $buyercol    = 'cCaseFeedBackMoney1';
                            $buyerRecall = $brecall[1];
                            $buyercheck  = $list[$i]['branchbook1'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                        if ($list[$i]['cFeedbackTarget2'] == 2) {
                            $ownerbrand  = $list[$i]['brand2'];
                            $ownercol    = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck  = $list[$i]['branchbook2'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand  = $list[$i]['brand2'];
                            $buyercol    = 'cCaseFeedBackMoney2';
                            $buyerRecall = $brecall[2];
                            $buyercheck  = $list[$i]['branchbook2'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                        if ($list[$i]['cFeedbackTarget3'] == 2) {
                            $ownerbrand  = $list[$i]['brand3'];
                            $ownercol    = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck  = $list[$i]['branchbook3'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand  = $list[$i]['brand3'];
                            $buyercol    = 'cCaseFeedBackMoney3';
                            $buyerRecall = $brecall[3];
                            $buyercheck  = $list[$i]['branchbook3'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($o == 0) { //沒有選定賣方則從買賣方選一個
                        if ($list[$i]['cFeedbackTarget'] == 1) {
                            $ownerbrand  = $list[$i]['brand'];
                            $ownercol    = 'cCaseFeedBackMoney';
                            $ownerRecall = $brecall[0];
                            $ownercheck  = $list[$i]['branchbook'];
                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['brand1'] > 0) {
                            $ownerbrand  = $list[$i]['brand1'];
                            $ownercol    = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck  = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['branchbook1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['brand2'] > 0) {
                            $ownerbrand  = $list[$i]['brand2'];
                            $ownercol    = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck  = $list[$i]['branchbook2'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['branchbook2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['brand3'] > 0) {
                            $ownerbrand  = $list[$i]['brand3'];
                            $ownercol    = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck  = $list[$i]['branchbook3'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['branchbook3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }
                }

                if ($ownerbrand == 69) {
                    if ($ownerfeed == '') {
                        $_feedbackMoney  = round($ownerRecall * $list[$i]['cerifiedmoney']);
                        $uSql[$ownercol] = $_feedbackMoney;
                        $uSql[$buyercol] = 0;
                    } else {
                        $uSql[$ownercol] = 0;
                        $uSql[$buyercol] = 0;
                        $uSql[$ownercol] = 1;
                        $uSql[$buyercol] = 1;
                    }
                } else if ($ownerbrand != 69) {
                    if ($ownercheck > 0) { //他牌是契約用印店且有合作契約書 各店:保證費*回饋趴/回饋數
                        if ($feed == 1) { //  只有一間有勾選未收足，只算給那一間店
                            $bcount = 0;
                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch2'] == 1 || $list[$i]['brand2'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                //是契約書用印店才回饋 &&
                                $_feedbackMoney             = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney'] = 0;
                                $uSql['cCaseFeedback']      = 1;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $_feedbackMoney              = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney1'] = 0;
                                $uSql['cCaseFeedback1']      = 1;
                            }

                            if ($bcount == 3) {
                                if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand2'] == 69)) { //是契約書用印店才回饋 && $("[name='cAffixBranch']:checked").val() == 'b2'
                                    $_feedbackMoney              = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                    $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney;
                                } else {
                                    $uSql['cCaseFeedBackMoney2'] = 0;
                                    $uSql['cCaseFeedback2']      = 1;
                                }
                            }
                        } else {
                            $_feedbackMoney             = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;

                            $_feedbackMoney              = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;

                            if ($bcount == 3) {
                                $_feedbackMoney             = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            }
                        }
                    } else {
                        //沒合作契約書回饋給幸福家(買)
                        if ($buyerfeed == '') {
                            $_feedbackMoney  = round($ownerRecall * $list[$i]['cerifiedmoney']);
                            $uSql[$buyercol] = $_feedbackMoney;
                            $uSql[$ownercol] = 0;
                        } else {
                            $uSql[$ownercol]  = 0;
                            $uSql[$buyercol]  = 0;
                            $uSql[$ownerfeed] = 1;
                            $uSql[$buyerfeed] = 1;
                        }
                    }
                }
            } else if ($bcount > 1 && ($list[$i]['brand'] == 69 || $list[$i]['brand1'] == 69 || $list[$i]['brand2'] == 69)) {
                //幸福他排配(含台屋)
                $o = 0;
                if ($list[$i]['cServiceTarget'] == 2) {
                    $ownerbrand  = $list[$i]['brand'];
                    $ownercol    = 'cCaseFeedBackMoney';
                    $ownerRecall = $brecall[0];
                    $ownercheck  = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $ownerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                    $o++;
                } else {
                    $buyerbrand  = $list[$i]['brand'];
                    $buyercol    = 'cCaseFeedBackMoney';
                    $buyerRecall = $brecall[0];
                    $buyercheck  = $list[$i]['branchbook'];
                    if ($feed == 1) {
                        if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                            $buyerfeed = 'cCaseFeedback'; //1不回饋
                        }
                    }
                }

                if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                    if ($list[$i]['cServiceTarget1'] == 2) {
                        $ownerbrand  = $list[$i]['brand1'];
                        $ownercol    = 'cCaseFeedBackMoney1';
                        $ownerRecall = $brecall[1];
                        $ownercheck  = $list[$i]['branchbook1'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand1'];
                        $buyercol    = 'cCaseFeedBackMoney1';
                        $buyerRecall = $brecall[1];
                        $buyercheck  = $list[$i]['branchbook1'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback1'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                    if ($list[$i]['cServiceTarget2'] == 2) {
                        $ownerbrand  = $list[$i]['brand2'];
                        $ownercol    = 'cCaseFeedBackMoney2';
                        $ownerRecall = $brecall[2];
                        $ownercheck  = $list[$i]['branchbook2'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand2'];
                        $buyercol    = 'cCaseFeedBackMoney2';
                        $buyerRecall = $brecall[2];
                        $buyercheck  = $list[$i]['branchbook2'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback2'; //1不回饋
                            }
                        }
                    }
                }

                if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                    if ($list[$i]['cServiceTarget3'] == 2) {
                        $ownerbrand  = $list[$i]['brand3'];
                        $ownercol    = 'cCaseFeedBackMoney3';
                        $ownerRecall = $brecall[3];
                        $ownercheck  = $list[$i]['branchbook3'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand3'];
                        $buyercol    = 'cCaseFeedBackMoney3';
                        $buyerRecall = $brecall[3];
                        $buyercheck  = $list[$i]['branchbook3'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyerfeed = 'cCaseFeedback3'; //1不回饋
                            }
                        }
                    }
                }

                //以防沒選到契約書用印店(用舊的方法 只回饋給賣方)
                if ($o == 0) {
                    if ($list[$i]['cFeedbackTarget'] == 2) {
                        $ownerbrand  = $list[$i]['brand'];
                        $ownercol    = 'cCaseFeedBackMoney';
                        $ownerRecall = $brecall[0];
                        $ownercheck  = $list[$i]['branchbook'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $ownerfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                        $o++;
                    } else {
                        $buyerbrand  = $list[$i]['brand'];
                        $buyercol    = 'cCaseFeedBackMoney';
                        $buyerRecall = $brecall[0];
                        $buyercheck  = $list[$i]['branchbook'];

                        //未收足不回饋
                        if ($feed == 1) {
                            if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                $buyrfeed = 'cCaseFeedback'; //1不回饋
                            }
                        }
                    }

                    if ($list[$i]['brand1'] > 0 && $list[$i]['branch1'] > 0) {
                        if ($list[$i]['cFeedbackTarget1'] == 2) {
                            $ownerbrand  = $list[$i]['brand1'];
                            $ownercol    = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck  = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand  = $list[$i]['brand1'];
                            $buyercol    = 'cCaseFeedBackMoney1';
                            $buyerRecall = $brecall[1];
                            $buyercheck  = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand2'] > 0 && $list[$i]['branch2'] > 0) {
                        if ($list[$i]['cFeedbackTarget2'] == 2) {
                            $ownerbrand  = $list[$i]['brand2'];
                            $ownercol    = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck  = $list[$i]['branchbook2'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand  = $list[$i]['brand2'];
                            $buyercol    = 'cCaseFeedBackMoney2';
                            $buyerRecall = $brecall[2];
                            $buyercheck  = $list[$i]['branchbook2'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($list[$i]['brand3'] > 0 && $list[$i]['branch3'] > 0) {
                        if ($list[$i]['cFeedbackTarget3'] == 2) {
                            $ownerbrand  = $list[$i]['brand3'];
                            $ownercol    = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck  = $list[$i]['branchbook3'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                            $o++;
                        } else {
                            $buyerbrand  = $list[$i]['brand3'];
                            $buyercol    = 'cCaseFeedBackMoney3';
                            $buyerRecall = $brecall[3];
                            $buyercheck  = $list[$i]['branchbook3'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['bFeedbackMoney3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $buyerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }

                    if ($o == 0) { //沒有選定賣方則從買賣方選一個
                        if ($list[$i]['cFeedbackTarget'] == 1) {
                            $ownerbrand  = $list[$i]['brand'];
                            $ownercol    = 'cCaseFeedBackMoney';
                            $ownerRecall = $brecall[0];
                            $ownercheck  = $list[$i]['branchbook'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 0) || ($list[$i]['cFeedbackTarget'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['brand1'] > 0) {
                            $ownerbrand  = $list[$i]['brand1'];
                            $ownercol    = 'cCaseFeedBackMoney1';
                            $ownerRecall = $brecall[1];
                            $ownercheck  = $list[$i]['branchbook1'];

                            //未收足不回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['branchbook1'] == 0) || ($list[$i]['cFeedbackTarget1'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback1'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['brand2'] > 0) {
                            $ownerbrand  = $list[$i]['brand2'];
                            $ownercol    = 'cCaseFeedBackMoney2';
                            $ownerRecall = $brecall[2];
                            $ownercheck  = $list[$i]['branchbook2'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['branchbook2'] == 0) || ($list[$i]['cFeedbackTarget2'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback2'; //1不回饋
                                }
                            }
                        } else if ($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['brand3'] > 0) {
                            $ownerbrand  = $list[$i]['brand3'];
                            $ownercol    = 'cCaseFeedBackMoney3';
                            $ownerRecall = $brecall[3];
                            $ownercheck  = $list[$i]['branchbook3'];

                            //未收足回饋
                            if ($feed == 1) {
                                if (($list[$i]['cFeedbackTarget3'] == 1 && $list[$i]['branchbook3'] == 0) || ($list[$i]['cFeedbackTarget3'] == 2 && $list[$i]['sFeedbackMoney'] == 0)) {
                                    $ownerfeed = 'cCaseFeedback3'; //1不回饋
                                }
                            }
                        }
                    }
                }

                if ($ownerbrand == 69) {
                    if ($ownerfeed == '') {
                        $_feedbackMoney  = round($ownerRecall * $list[$i]['cerifiedmoney']);
                        $uSql[$ownercol] = $_feedbackMoney;
                        $uSql[$buyercol] = 0;
                    } else {
                        $uSql[$ownercol]  = 0;
                        $uSql[$buyercol]  = 0;
                        $uSql[$ownerfeed] = 1;
                        $uSql[$buyerfeed] = 1;
                    }
                } else if ($ownerbrand != 69) {
                    if ($ownercheck > 0) { //他牌是契約用印店且有合作契約書 各店:保證費*回饋趴/回饋數
                        if ($feed == 1) { //  只有一間有勾選未收足，只算給那一間店
                            $bcount = 0;
                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch2'] == 1 || $list[$i]['brand2'] == 69)) {
                                $bcount++;
                            }

                            if ($list[$i]['cFeedbackTarget'] == 1 && $list[$i]['bFeedbackMoney'] == 1 && ($list[$i]['cAffixBranch'] == 1 || $list[$i]['brand'] == 69)) {
                                //是契約書用印店才回饋 &&
                                $_feedbackMoney             = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney'] = 0;
                                $uSql['cCaseFeedback']      = 1;
                            }

                            if ($list[$i]['cFeedbackTarget1'] == 1 && $list[$i]['bFeedbackMoney1'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand1'] == 69)) {
                                //是契約書用印店才回饋
                                $_feedbackMoney              = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;
                            } else {
                                $uSql['cCaseFeedBackMoney1'] = 0;
                                $uSql['cCaseFeedback1']      = 1;
                            }

                            if ($bcount == 3) {
                                if ($list[$i]['cFeedbackTarget2'] == 1 && $list[$i]['bFeedbackMoney2'] == 1 && ($list[$i]['cAffixBranch1'] == 1 || $list[$i]['brand2'] == 69)) { //是契約書用印店才回饋 && $("[name='cAffixBranch']:checked").val() == 'b2'
                                    $_feedbackMoney              = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                    $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney;
                                } else {
                                    $uSql['cCaseFeedBackMoney2'] = 0;
                                    $uSql['cCaseFeedback2']      = 1;
                                }
                            }
                        } else {
                            $_feedbackMoney              = round(($brecall[0] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney']  = $_feedbackMoney;
                            $_feedbackMoney              = round(($brecall[1] * $list[$i]['cerifiedmoney']) / $bcount);
                            $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney;

                            if ($bcount == 3) {
                                $_feedbackMoney             = round(($brecall[2] * $list[$i]['cerifiedmoney']) / $bcount);
                                $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                            }
                        }
                    } else {
                        //沒合作契約書回饋給幸福家(買)
                        if ($buyerfeed == '') {
                            $_feedbackMoney  = round($buyerRecall * $list[$i]['cerifiedmoney']);
                            $uSql[$buyercol] = $_feedbackMoney;
                            $uSql[$ownercol] = 0;
                        } else {
                            $uSql[$ownercol]  = 0;
                            $uSql[$buyercol]  = 0;
                            $uSql[$ownerfeed] = 0;
                            $uSql[$buyerfeed] = 0;
                        }
                    }
                }
            } else {
                if ($bcount == 1) { //只有一間店
                    $_feedbackMoney = round($brecall[0] * $list[$i]['cerifiedmoney']);

                    $uSql['cCaseFeedBackMoney']  = $_feedbackMoney;
                    $uSql['cCaseFeedBackMoney1'] = 0;
                    $uSql['cCaseFeedBackMoney2'] = 0;
                    $uSql['cCaseFeedBackMoney3'] = 0;

                    //無合作契約書給代書
                    if ($list[$i]['branchbook'] != 1 && $list[$i]['branch'] > 0 && $list[$i]['brand'] != 1 && $list[$i]['brand'] != 69) {
                        $uSql['cFeedbackTarget'] = 2;
                    }

                    //如有回饋給地政士另有地政士特殊回饋
                    if (($list[$i]['cFeedbackTarget'] == 2 || $list[$i]['cFeedbackTarget1'] == 2 || $list[$i]['cFeedbackTarget2'] == 2) && ($list[$i]['brand'] != 69 || $list[$i]['brand'] != 1 || $list[$i]['brand'] != 49) && ($list[$i]['sSpRecall'] != '' || $list[$i]['sSpRecall'] != 0)) {
                        $list[$i]['sSpRecall'] = $list[$i]['sSpRecall'] / 100;

                        if ($list[$i]['sSpRecall'] > $brecall[0]) {
                            $_feedbackMoney = round($list[$i]['sSpRecall'] * $list[$i]['cerifiedmoney']);
                        } else {
                            $_feedbackMoney = round($brecall[0] * $list[$i]['cerifiedmoney']);
                        }

                        $uSql['cCaseFeedBackMoney']  = $_feedbackMoney;
                        $uSql['cCaseFeedBackMoney1'] = 0;
                        $uSql['cCaseFeedBackMoney2'] = 0;
                        $uSql['cCaseFeedBackMoney3'] = 0;
                    }
                } else if ($bcount > 1) {
                    $tmp_c = 0;

                    //計算回饋
                    if ($list[$i]['branch'] > 0) {
                        $_feedbackMoney             = round($brecall[0] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                    }

                    if ($list[$i]['branch1'] > 0) {
                        $_feedbackMoney1             = round($brecall[1] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney1;
                    }

                    if ($list[$i]['branch2'] > 0) {
                        $_feedbackMoney2             = round($brecall[2] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney2;
                    }

                    if ($list[$i]['branch3'] > 0) {
                        $_feedbackMoney3             = round($brecall[3] * $list[$i]['cerifiedmoney'] / $bcount);
                        $uSql['cCaseFeedBackMoney3'] = $_feedbackMoney3;
                    }

                    //是否為台屋優美或有合作契約書
                    if (($list[$i]['brand'] == 1 || $list[$i]['brand'] == 49 || $list[$i]['branchbook'] > 0)) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback']      = 1;
                        $uSql['cCaseFeedBackMoney'] = 0;
                    }

                    if (($list[$i]['brand1'] == 1 || $list[$i]['brand1'] == 49 || $list[$i]['branchbook1'] > 0) && $list[$i]['branch1'] > 0) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback1']      = 1;
                        $uSql['cCaseFeedBackMoney1'] = 0;
                    }

                    if (($list[$i]['brand2'] == 1 || $list[$i]['brand2'] == 49 || $list[$i]['branchbook2'] > 0) && $list[$i]['branch2'] > 0) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback2']      = 1;
                        $uSql['cCaseFeedBackMoney2'] = 0;
                    }

                    if (($list[$i]['brand3'] == 1 || $list[$i]['brand3'] == 49 || $list[$i]['branchbook3'] > 0) && $list[$i]['branch3'] > 0) {
                        $tmp_c++;
                    } else {
                        //無合契
                        $uSql['cCaseFeedback3']      = 1;
                        $uSql['cCaseFeedBackMoney3'] = 0;
                    }
                    //配件都沒有合作契約書，回饋給代書
                    if ($tmp_c == 0) {
                        if ($list[$i]['branch'] > 0) {
                            $uSql['cCaseFeedback']      = 0;
                            $uSql['cFeedbackTarget']    = 2;
                            $uSql['cCaseFeedBackMoney'] = $_feedbackMoney;
                        }

                        if ($list[$i]['branch1'] > 0) {
                            $uSql['cCaseFeedback1']      = 0;
                            $uSql['cFeedbackTarget1']    = 2;
                            $uSql['cCaseFeedBackMoney1'] = $_feedbackMoney1;
                        }

                        if ($list[$i]['branch2'] > 0) {
                            $uSql['cCaseFeedback2']      = 0;
                            $uSql['cFeedbackTarget2']    = 2;
                            $uSql['cCaseFeedBackMoney2'] = $_feedbackMoney2;
                        }

                        if ($list[$i]['branch3'] > 0) {
                            $uSql['cCaseFeedback3']      = 0;
                            $uSql['cFeedbackTarget3']    = 2;
                            $uSql['cCaseFeedBackMoney3'] = $_feedbackMoney3;
                        }
                    }
                }
            }

            if ($scrpart != 0 && $scrpart != '') {
                $scrFeedMoney                 = round($scrpart * $list[$i]['cerifiedmoney']);
                $uSql['cSpCaseFeedBackMoney'] = $scrFeedMoney;
            } else {
                $uSql['cSpCaseFeedBackMoney'] = 0;
            }

            $str = array();
            foreach ($uSql as $key => $value) {
                $str[] = $key . "='" . $value . "'";
            }

            $sql = "UPDATE tContractCase SET " . @implode(',', $str) . " WHERE cCertifiedId ='" . $list[$i]['cCertifiedId'] . "'";
            $conn->Execute($sql);

            //如果有回饋給地政士 特殊回饋不回饋
            if (($scrpart == 0 || $scrpart == '') && ($uSql['cFeedbackTarget'] != 2 && $uSql['cFeedbackTarget1'] != 2 && $uSql['cFeedbackTarget2'] != 2 && $uSql['cFeedbackTarget3'] != 2)) { //如果仲介品牌有回饋給地政士 特殊回饋不回饋
                if ($feed == 1) {
                    if ($list[$i]['sFeedbackMoney'] == 1) {
                        SpRecall($list[$i]);
                    }
                } else {
                    SpRecall($list[$i]);
                }
            }

            write_log($id . ":" . $sql . "\r\n", 'checkFeedPart');

            $cCertifiedId[] = $list[$i]['cCertifiedId'];
        }
    }

    return $cCertifiedId;
}

function clearFeedMoney($id)
{
    global $conn;

    $sql = "UPDATE
				tContractCase
			SET
				cCaseFeedBackMoney = 0,
				cCaseFeedBackMoney1 = 0,
				cCaseFeedBackMoney2 = 0,
				cSpCaseFeedBackMoney= 0,
				cCaseFeedback = 0,
				cCaseFeedback1 = 0,
				cCaseFeedback2 = 0,
				cFeedbackTarget = 1,
				cFeedbackTarget1 = 1,
				cFeedbackTarget2 = 2
			WHERE
				cCertifiedId = '" . $id . "'";
    $conn->Execute($sql);
}

function checkBrandFeed($cId, $branch)
{
    global $conn;

    if (!$branch) {
        return false;
    }

    $sql   = "SELECT fId FROM tFeedBackMoney WHERE fType = '2' AND fDelete = 0 AND fCertifiedId = '" . $cId . "' AND fStoreId = '" . $branch . "'";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total > 0) {
        return true; //有資料 不要新增
    } else {
        return false; //
    }
}

function SpRecall($data)
{ //特殊回饋金

    global $conn;

    $branchCount = 0;
    //有台屋、非仲一律不回饋
    if ($data['branch'] > 0) {
        $branchCount++;
        if ($data['brand'] != 1 && $data['brand'] != 49 && $data['brand'] != 2) {
            $check++;
        }
    }

    if ($data['branch1'] > 0) {
        $branchCount++;
        if ($data['brand1'] != 1 && $data['brand1'] != 49 && $data['brand1'] != 2) {
            $check++;
        }
    }

    if ($data['branch2'] > 0) {
        $branchCount++;
        if ($data['brand2'] != 1 && $data['brand2'] != 49 && $data['brand2'] != 2) {
            $check++;
        }
    }

    if ($data['branch3'] > 0) {
        $branchCount++;
        if ($data['brand3'] != 1 && $data['brand3'] != 49 && $data['brand3'] != 2) {
            $check++;
        }
    }

    if (($check == $branchCount) && $data['sSpRecall'] != 0) {
        $sSpRecall = $data['sSpRecall'] / 100;
        $spMoney   = round($data['cerifiedmoney'] * $sSpRecall);

        $str = 'cSpCaseFeedBackMoney = "' . $spMoney . '"';
    } else {
        $str = 'cSpCaseFeedBackMoney = "0"';
    }

    $sql = "UPDATE tContractCase SET " . $str . " WHERE cCertifiedId ='" . $data['cCertifiedId'] . "'";
    $conn->Execute($sql);
}

######################案件數量統計表+案件統計表###########################
//保證費配件平分
function getcCertifiedMoney($cMoney, $arr)
{
    // 保證費 要依回饋對像來看
    // 如果AB店配
    // 1.回饋給A或B 那麼保證費就算給A或B
    // 2.回饋給AB 那麼保證費就除以2各半

    if (is_array($arr)) {
        $count = count($arr);

        $m = $cMoney % $count; //餘數
        $n = floor($cMoney / $count); //商數
        $m = $m + $n; //首位被選取的金額
        $i = 0;

        foreach ($arr as $key => $value) {
            if ($i == 0) {$x = $m;} else { $x = $n;}

            $arr[$key]['money'] = $x;
            $i++;
        }
    }

    return $arr;
}

//刪除其他回饋金
function deleteFeedBackMoney($certifiedid)
{
    global $conn;

    $sql = "UPDATE
                tFeedBackMoney
            SET
                fDelete = 1
            WHERE
                fCertifiedId = '" . $certifiedid . "'
            ";
    $conn->Execute($sql);
}