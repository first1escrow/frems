<?php
require_once __DIR__ . '/advance.class.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

class SMS extends Advance
{
    public $mKindBranch          = 0;
    public $mKindScrivener       = 0;
    public $mKeySubject          = "";
    public $mKeyScrivener        = "";
    public $mKeyBranch           = "";
    public $mColumnSubject       = null;
    public $mColumnScrivener     = null;
    public $mColumnBranch        = null;
    const CATEGORY_NUM_SCRIVENER = 1;
    const CATEGORY_NUM_BRANCH    = 2;

    public function __construct()
    {
        parent::__construct();
        $this->mKindBranch      = 1 << 0;
        $this->mKindScrivener   = 1 << 1;
        $this->mKeySubject      = "id";
        $this->mKeyScrivener    = "sNID";
        $this->mKeyBranch       = "bNID";
        $this->mColumnSubject   = ['tName', 'tMobile', 'tDefault'];
        $this->mColumnScrivener = ['sName', 'sMobile', 'sDefault'];
        $this->mColumnBranch    = ['bName', 'bMobile', ''];
    }

    public function CombineSmsList($arr_subject, $arr_list, $category)
    {
        $column = null;
        $key    = null;
        switch ($category) {
            case self::CATEGORY_NUM_SCRIVENER:
                $key    = $this->mKeyScrivener;
                $column = $this->mColumnScrivener;
                break;
            case self::CATEGORY_NUM_BRANCH:
                $key    = $this->mKeyBranch;
                $column = $this->mColumnBranch;
                break;
        }

        foreach ($arr_subject as $k1 => $v1) {
            foreach ($column as $k2 => $v2) {
                $arr_subject[$k1][$v2] = '';
            }
        }

        foreach ($arr_list as $k => $v) {
            foreach ($arr_subject as $k2 => $v2) {
                if ($v[$key] == $v2[$this->mKeySubject]) {
                    foreach ($column as $k3 => $v3) {
                        $arr_subject[$k2][$v3] = $v[$v3];
                    }
                }
            }
        }

        return $arr_subject;
    }

    public function GetSmsSubject($kind, $id = null)
    {
        $arr_kind  = [];
        $sql       = "";
        $sql_where = "";
        $sql_main  = "";
        $sql_order = "";
        $loop_key  = 20;

        $sql_main = " SELECT * FROM  `tTitle_SMS` WHERE 1 ";

        for ($i = 0; $i <= $loop_key; $i++) {
            if (($kind & (1 << $i)) > 0) {
                $arr_kind[] = $i;
            }
        }

        $sql_where .= " AND tKind in (" . implode(",", $arr_kind) . ")";

        if ($id != null) {
            $sql_where .= " AND id = '" . $id . "' ";
        }

        $sql_order = " ORDER BY tKind, tTitle, id ";

        $sql  = $sql_main . $sql_where . $sql_order;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function CheckScrivener($sc, $cnt)
    {
        $sql = " SELECT count(*) cnt FROM `tScrivenerSms` WHERE sScrivener = '" . $sc . "'; ";
        // echo $sql;
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['cnt'] == $cnt;
    }

    public function GetScrivenerList($sc)
    {
        $sql  = " SELECT * FROM tScrivenerSms where sScrivener  ='" . $sc . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs;
    }

    public function SaveScrivener($data)
    {
        for ($i = 0; $i < count($data['sSn']); $i++) {
            if ($data["sName"][$i] != '' && $data["sMobile"][$i] != '') {
                $sql = " UPDATE
                    `tScrivenerSms` SET
                        `sName` =  '" . $data["sName"][$i] . "',
                        `sMobile` =  '" . $data["sMobile"][$i] . "'
                 WHERE  `sScrivener` = '" . $data['scid'] . "' AND
                        `sId` = '" . $data['sSn'][$i] . "'; ";

                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
            } else {
                $sql = " UPDATE
                    `tScrivenerSms` SET
                        `sDel` =  '1'
                 WHERE  `sScrivener` = '" . $data['scid'] . "' AND
                        `sId` = '" . $data['sSn'][$i] . "'; ";
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
            }

        }
    }

    public function GetScrivenerDefault($scid)
    {
        $arr_default = [];
        $list        = $this->GetScrivenerList($scid);
        foreach ($list as $k => $v) {
            if ($v['sDefault'] == '1') {
                $arr_default[] = $v['sNID'];
            }
        }
        return $arr_default;
    }

    public function SaveScrivenerDefault($arrNid, $scid, $Name, $arrSend = '')
    {
        $sql = " UPDATE
                    `tScrivenerSms` SET
                        `sDefault` =  '0',
                        `sSend` =  '0'
                 WHERE `sScrivener` = '" . $scid . "' AND sDel = 0;  ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        if (count($arrNid) == 0 && count($arrSend) == 0) {
            return;
        }
        //sName
        foreach ($arrNid as $k => $v) {
            $tmp = explode('_', $v);

            if (preg_match("/\d+/", $tmp[0])) {
                // $name = $Name[$k];
                $sql = " UPDATE
                    `tScrivenerSms` SET
                        `sDefault` =  '1'
					WHERE
                        `sScrivener` = '" . $scid . "'
                     AND
                        sName = '" . $tmp[1] . "'
                     AND
                        `sMobile` = '" . $tmp[0] . "' AND sDel = 0; ";
                // echo $sql."<br>";
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();

            }
            unset($tmp);
        }

        if (is_array($arrSend)) {
            foreach ($arrSend as $k => $v) {
                $tmp = explode('_', $v);

                if (preg_match("/\d+/", $tmp[0])) {
                    $sql = " UPDATE
                        `tScrivenerSms` SET
                            `sSend` =  '1'
                        WHERE
                            `sScrivener` = '" . $scid . "'
                         AND
                            sName = '" . $tmp[1] . "'
                         AND
                            `sMobile` = '" . $tmp[0] . "'  AND sDel = 0; ";
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                }
            }
        }

    }

    public function AddScrivener($data, $sc)
    {
        for ($i = 0; $i < count($data['sms_sMobile']); $i++) {
            if ($data["sms_sName"][$i] != '' || $data["sms_sMobile"][$i] != '') {
                $sql = " INSERT INTO `tScrivenerSms`
                 (`sId`, `sScrivener`, `sNID`, `sName`, `sMobile`,`sSend`)
                     VALUES
                 (NULL, '" . $sc . "', '" . $data["sms_sNID"][$i] . "', '" . $data["sms_sName"][$i] . "', '" . $data["sms_sMobile"][$i] . "','" . $data["sms_" . $i] . "'); ";
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();

            }

        }
    }

    public function DelScrivener($sc)
    {
        $sql = " DELETE FROM tScrivenerSms Where sScrivener = '" . $sc . "'; ";

        write_log($sc . ',刪除簡訊對象,', 'scrivenersms');
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute();
    }

    public function CheckBranch($branch, $cnt)
    {
        $sql  = " SELECT count(*) cnt FROM `tBranchSms` WHERE bBranch = '" . $branch . "' AND bDel = 0; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['cnt'] == $cnt;
    }

    public function GetBranchList($branch)
    {
        $sql = "SELECT
                    *
                FROM
                     tBranchSms
                WHERE  bBranch = '" . $branch . "'  AND bDel = 0 ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function SaveBranch($data)
    {
        $cnt = count($data["tMobile"]);
        for ($i = 0; $i < $cnt; $i++) {
            $sql = " UPDATE
                    `tBranchSms` SET
                        `bName` =  '" . $data["tName"][$i] . "',
                        `bMobile` =  '" . $data["tMobile"][$i] . "'
                 WHERE  `bBranch` = '" . $data['id'] . "' AND
                        `bId` = '" . $data['tID'][$i] . "'; ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
    }

    public function DelBranch($branch)
    {
        $sql  = " DELETE FROM tBranchSms Where bBranch = '" . $branch . "'; ";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute();
    }

    public function AddBranch($data, $branch)
    {
        $cnt = count($data["tMobile"]);
        for ($i = 0; $i < $cnt; $i++) {
            if ($data['tMobile'][$i] != '') {
                $default = 0;
                foreach ($data['defaultSms'] as $k => $v) {
                    if ($data['tMobile'][$i] == $v) {
                        $default = 1;
                        break;
                    }
                }

                $sql  = 'INSERT INTO tBranchSms (bId,bBranch,bNID,bName,bMobile,bDefault) VALUES (NULL,"' . $branch . '","' . $data["tNID"][$i] . '","' . $data["tName"][$i] . '","' . $data["tMobile"][$i] . '","' . $default . '");';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
            }
        }

        //
        if ($data['sms_tMobile'] != '') {
            $sql = " INSERT INTO `tBranchSms`
			(`bId`, `bBranch`, `bNID`, `bName`, `bMobile`)
				VALUES
			(NULL, '" . $branch . "', '" . $data["sms_tNID"] . "', '" . $data["sms_tName"] . "', '" . $data["sms_tMobile"] . "'); ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
        ##
    }

    public function CombineSelectScrivener($list, $var)
    {
        $arr = $this->FieldToSelect($var);
        foreach ($arr as $k => $v) {
            foreach ($list as $k1 => $v1) {
                if ($v == $v1['id']) {
                    $list[$k1]['isSelect'] = '1';
                }
            }
        }
        return $list;
    }

    public function SaveSmsTargetScrivener($id, $arr)
    {
        $var  = $this->SelectToField($arr);
        $sql  = " Update tContractScrivener Set cSmsTarget = '" . $var . "' Where cCertifiedId = '" . $id . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
    }

    public function SaveUniqidScrivener($certified_id, $uniqid, $sc, $var)
    {

    }

    public function GetUniqidScrivener($certified_id)
    {

    }

}
