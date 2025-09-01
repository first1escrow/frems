<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$id   = $_POST['id'];
$cat  = $_POST['cat'];
$data = array();
$type = $_POST['type'];

$msg = '';

if ($type == 1) {
    if ($cat == 2) {
        $sql = "SELECT
                    bBranch AS storeId,
                    bNID AS title,
                    bName AS name,
                    bMobile AS mobile
                FROM
                    tBranchSms
                WHERE
                    bId = '" . $id . "'";
        $rs   = $conn->Execute($sql);
        $data = $rs->fields;

    } else if ($cat == 1) {
        $sql = "SELECT
                    sScrivener AS storeId,
                    sNID AS title,
                    sName AS name,
                    sMobile AS mobile
                FROM
                    tScrivenerSms WHERE sId = '" . $id . "'";
        $rs   = $conn->Execute($sql);
        $data = $rs->fields;
    }

    $sql = "SELECT * FROM tFeedBackStoreSms WHERE fType = '" . $cat . "' AND fStoreId = '" . $data['storeId'] . "' AND fOriginalId = '" . $id . "' AND fDelete = 0";
    $rs  = $conn->Execute($sql);
    if ($rs->EOF) {
        $sql = '
                INSERT INTO
                    tFeedBackStoreSms
                (
                    fType,
                    fTitle,
                    fStoreId,
                    fName,
                    fMobile,
                    fOriginalId
                )
                VALUES
                (
                    "' . $cat . '",
                    "' . $data['title'] . '",
                    "' . $data['storeId'] . '",
                    "' . addslashes($data['name']) . '",
                    "' . $data['mobile'] . '",
                    "' . $id . '"
                )
            ;';
        if ($conn->Execute($sql)) {
            $msg = '新增成功';
        } else {
            $msg = $conn->errorMsg();
        }
    } else {
        $sql = '
            UPDATE
                tFeedBackStoreSms
            SET
                fTitle="' . $data['title'] . '",
                fName="' . addslashes($data['name']) . '",
                fMobile="' . $data['mobile'] . '"
            WHERE
                fId="' . $rs->fields['fId'] . '"
        ;';
        if ($conn->Execute($sql)) {
            $msg = '更新成功';
        } else {
            $msg = $conn->errorMsg();
        }
    }

} elseif ($type == 2) {
    if ($cat == 2) {
        $sql  = "SELECT * FROM tBranchSms WHERE bId = '" . $id . "'";
        $rs   = $conn->Execute($sql);
        $data = $rs->fields;

        //比對是否有資料了
        $sql = "SELECT bBranchSmsId FROM tBranchFeedback WHERE bBranchSmsId = '" . $id . "'";
        $rs  = $conn->Execute($sql);

        if ($rs->EOF) { // 無資料
            $sql = "INSERT INTO
                    tBranchFeedback
                SET
                    bBranch = '" . $data['bBranch'] . "',
                    bNID = '" . $data['bNID'] . "',
                    bName = '" . addslashes($data['bName']) . "',
                    bMobile = '" . $data['bMobile'] . "',
                    bBranchSmsId = '" . $id . "'";

            if ($conn->Execute($sql)) {
                $msg = '新增成功';
            } else {
                $msg = $conn->errorMsg();
            }
        } else {
            $sql = "UPDATE
                        tBranchFeedback
                    SET
                        bNID = '" . $data['bNID'] . "',
                        bName = '" . addslashes($data['bName']) . "',
                        bMobile = '" . $data['bMobile'] . "'
                    WHERE
                        bBranchSmsId = '" . $id . "'";
            if ($conn->Execute($sql)) {
                $msg = '更新成功';
            } else {
                $msg = $conn->errorMsg();
            }
        }

    } else {
        $sql  = "SELECT * FROM tScrivenerSms WHERE sId = '" . $id . "'";
        $rs   = $conn->Execute($sql);
        $data = $rs->fields;

        $sql = "SELECT sScrivenerSmsId FROM tScrivenerFeedSms WHERE sScrivenerSmsId = '" . $id . "'";
        $rs  = $conn->Execute($sql);

        if ($rs->EOF) {
            $sql = "INSERT INTO
                        tScrivenerFeedSms
                    SET
                        sScrivener = '" . $data['sScrivener'] . "',
                        sNID = '" . $data['sNID'] . "',
                        sName = '" . $data['sName'] . "',
                        sMobile = '" . $data['sMobile'] . "',
                        sScrivenerSmsId = '" . $id . "'";

            if ($conn->Execute($sql)) {
                $msg = '新增成功';
            } else {
                $msg = $conn->errorMsg();
            }
        } else {
            $sql = "UPDATE
                        tScrivenerFeedSms
                SET
                    sNID = '" . $data['sNID'] . "',
                    sName = '" . $data['sName'] . "',
                    sMobile = '" . $data['sMobile'] . "'
                WHERE
                    sScrivenerSmsId = '" . $id . "'";

            if ($conn->Execute($sql)) {
                $msg = '更新成功';
            } else {
                $msg = $conn->errorMsg();
            }
        }
    }
} elseif ($type == 3) {
    $sql = "SELECT * FROM tFeedBackStoreSms WHERE fId = '" . $id . "'";
    $rs = $conn->Execute($sql);
    $data = $rs->fields;

    if ($cat == 2) {
        $sql = "SELECT bId FROM tBranchFeedback WHERE bFeedBackStoreSmsId = '" . $id . "'";
        $rs = $conn->Execute($sql);

        if ($rs->EOF) { // 無資料
            $sql = "INSERT INTO
                        tBranchFeedback
                    SET
                        bBranch = '" . $data['fStoreId'] . "',
                        bNID = '" . $data['fTitle'] . "',
                        bName = '" . addslashes($data['fName']) . "',
                        bMobile = '" . $data['fMobile'] . "',
                        bFeedBackStoreSmsId = '" . $id . "'";
            if ($conn->Execute($sql)) {
                $msg = '新增成功';
            } else {
                $msg = $conn->errorMsg();
            }
        } else {
            $sql = "UPDATE
                        tBranchFeedback
                    SET
                        bNID = '" . $data['fTitle'] . "',
                        bName = '" . addslashes($data['fName']) . "',
                        bMobile = '" . $data['fMobile'] . "'
                    WHERE
                        bFeedBackStoreSmsId = '" . $id . "'";
            if ($conn->Execute($sql)) {
                $msg = '更新成功';
            } else {
                $msg = $conn->errorMsg();
            }
        }
    } elseif ($cat == 3) { //個案
        $sql = "SELECT iId FROM tIndividualFeedSms WHERE iFeedBackStoreSmsId = '" . $id . "'";
        $rs = $conn->Execute($sql);

        if ($rs->EOF) { // 無資料
            $sql = "INSERT INTO
                        tIndividualFeedSms
                    SET
                        iIndividual = '" . $data['fStoreId'] . "',
                        iNID = '" . $data['fTitle'] . "',
                        iName = '" . addslashes($data['fName']) . "',
                        iMobile = '" . $data['fMobile'] . "',
                        iFeedBackStoreSmsId = '" . $id . "'";
            if ($conn->Execute($sql)) {
                $msg = '新增成功';
            } else {
                $msg = $conn->errorMsg();
            }
        } else {
            $sql = "UPDATE
                        tIndividualFeedSms
                    SET
                        iNID = '" . $data['fTitle'] . "',
                        iName = '" . addslashes($data['fName']) . "',
                        iMobile = '" . $data['fMobile'] . "'
                    WHERE
                        iFeedBackStoreSmsId = '" . $id . "'";
            if ($conn->Execute($sql)) {
                $msg = '更新成功';
            } else {
                $msg = $conn->errorMsg();
            }
        }
    } else {
        $sql = "SELECT sId FROM tScrivenerFeedSms WHERE sFeedBackStoreSmsId = '" . $id . "'";
        $rs  = $conn->Execute($sql);

        if ($rs->EOF) {
            $sql = "INSERT INTO
                        tScrivenerFeedSms
                    SET
                        sScrivener = '" . $data['fStoreId'] . "',
                        sNID = '" . $data['fTitle'] . "',
                        sName = '" . $data['fName'] . "',
                        sMobile = '" . $data['fMobile'] . "',
                        sFeedBackStoreSmsId = '" . $id . "'";

            if ($conn->Execute($sql)) {
                $msg = '新增成功';
            } else {
                $msg = $conn->errorMsg();
            }
        } else {
            $sql = "UPDATE
                        tScrivenerFeedSms
                SET
                    sNID = '" . $data['fTitle'] . "',
                    sName = '" . $data['fName'] . "',
                    sMobile = '" . $data['fMobile'] . "'
                WHERE
                    sFeedBackStoreSmsId = '" . $id . "'";

            if ($conn->Execute($sql)) {
                $msg = '更新成功';
            } else {
                $msg = $conn->errorMsg();
            }
        }
    }

} elseif ($type == 4) {

    if ($cat == 2) {
        $sql = "SELECT * FROM tBranchFeedback WHERE bId = '" . $id . "'";
        $rs  = $conn->Execute($sql);

        $data = $rs->fields;

        $sql = "SELECT * FROM tFeedBackStoreSms WHERE fType = '" . $cat . "' AND fStoreId = '" . $data['bBranch'] . "' AND fOriginalId2 = '" . $id . "'";
        $rs  = $conn->Execute($sql);
        if ($rs->EOF) {
            $sql = '
                    INSERT INTO
                        tFeedBackStoreSms
                    (
                        fType,
                        fTitle,
                        fStoreId,
                        fName,
                        fMobile,
                        fOriginalId2
                    )
                    VALUES
                    (
                        "' . $cat . '",
                        "' . $data['bNID'] . '",
                        "' . $data['bBranch'] . '",
                        "' . addslashes($data['bName']) . '",
                        "' . $data['bMobile'] . '",
                        "' . $id . '"
                    )
                ;';
            if ($conn->Execute($sql)) {
                $msg = '新增成功';
            } else {
                $msg = $conn->errorMsg();
            }
        } else {
            $sql = '
                UPDATE
                    tFeedBackStoreSms
                SET
                    fTitle="' . $data['bNID'] . '",
                    fName="' . addslashes($data['bName']) . '",
                    fMobile="' . $data['bMobile'] . '"
                WHERE
                    fId="' . $rs->fields['fId'] . '"
            ';
            if ($conn->Execute($sql)) {
                $msg = '更新成功';
            } else {
                $msg = $conn->errorMsg();
            }
        }

    } else {
        $sql  = "SELECT * FROM tScrivenerFeedSms WHERE sId = '" . $id . "'";
        $rs   = $conn->Execute($sql);
        $data = $rs->fields;

        $sql = "SELECT * FROM tFeedBackStoreSms WHERE fType = '" . $cat . "' AND fStoreId = '" . $data['sScrivener'] . "' AND fOriginalId2 = '" . $id . "'";
        $rs  = $conn->Execute($sql);

        if ($rs->EOF) {
            $sql = '
                    INSERT INTO
                        tFeedBackStoreSms
                    (
                        fType,
                        fTitle,
                        fStoreId,
                        fName,
                        fMobile,
                        fOriginalId2
                    )
                    VALUES
                    (
                        "' . $cat . '",
                        "' . $data['bNID'] . '",
                        "' . $data['sScrivener'] . '",
                        "' . addslashes($data['bName']) . '",
                        "' . $data['bMobile'] . '",
                        "' . $id . '"
                    )
                ;';
            if ($conn->Execute($sql)) {
                $msg = '新增成功';
            } else {
                $msg = $conn->errorMsg();
            }
        } else {
            $sql = '
                UPDATE
                    tFeedBackStoreSms
                SET
                    fTitle="' . $data['bNID'] . '",
                    fName="' . addslashes($data['bName']) . '",
                    fMobile="' . $data['bMobile'] . '"
                WHERE
                    fId="' . $rs->fields['fId'] . '"
            ';
            if ($conn->Execute($sql)) {
                $msg = '更新成功';
            } else {
                $msg = $conn->errorMsg();
            }
        }

    }

}

echo $msg;
