<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

class Appraisal
{
    public $apply_case_datetime;
    private $conn;

    public function __construct()
    {
        $this->conn = new first1DB;
        // $this->apply_case_datetime = '2022-09-15 00:00:00'; //建檔日期須大於此時間
        $this->apply_case_datetime = '2022-09-08 00:00:00'; //建檔日期須大於此時間
    }

    //紀錄貸款試算案件
    public function registerCase($certifiedId)
    {
        //確認案件是否符合資格
        if (empty($this->qualifyCase($certifiedId))) {
            return;
        }
        ##

        //確認案件是否已紀錄過
        if ($this->fileExist($certifiedId)) {
            return;
        }
        ##

        //紀錄案件
        $sql = 'INSERT INTO `tAppriasalNotify` (`aCertifiedId`, `aCreated_at`) VALUES (:cid, :dt);';
        return $this->conn->exeSql($sql, ['cid' => $certifiedId, 'dt' => date("Y-m-d H:i:s")]);
        ##
    }
    ##

    //確認案件是否存在
    public function fileExist($certifiedId)
    {
        $sql = 'SELECT `aId` FROM `tAppriasalNotify` WHERE `aCertifiedId` = :cid;';
        $rs  = $this->conn->one($sql, ['cid' => $certifiedId]);

        return empty($rs) ? false : true;
    }
    ##

    //確認案件是否符合資格
    public function qualifyCase($certifiedId)
    {
        $sql = 'SELECT `cApplyDate` FROM `tContractCase` WHERE `cCertifiedId` = :cid;';
        $rs  = $this->conn->one($sql, ['cid' => $certifiedId]);

        if (empty($rs) || empty($rs['cApplyDate'])) {
            return false;
        }

        return ($rs['cApplyDate'] >= $this->apply_case_datetime) ? true : false;
    }
    ##
}
