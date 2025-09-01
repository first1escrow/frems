<?php
require_once __DIR__ . '/advance.class.php';
require_once __DIR__ . '/staff.class.php';

use First1\V1\Staff\Staff;

class Member extends Advance
{

    public function __construct()
    {
        parent::__construct();
    }

    public function GetMemberInfo($id, $job)
    {
        $sql = " SELECT *  FROM `tPeopleInfo`
                 WHERE
                    `pId` = '" . $id . "' AND `pJob` = '" . $job . "' ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function CheckPassword($account, $password)
    {
        $authority           = [];
        $peopleInfoAuthority = [];
        $temp                = [];
        $is_pass             = false;
        $sql                 = " SELECT * FROM  `tPeopleInfo` WHERE pAccount = '" . $account . "' AND pPassword = '" . $password . "'; ";
        $stmt                = $this->dbh->prepare($sql);
        $stmt->execute();
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        $is_pass = ! empty($member);

        if ($is_pass) {
            $staff = new Staff;

            //基本資料
            $_SESSION['member_job']        = $member['pJob'];
            $_SESSION['member_acc']        = $member['pAccount'];
            $_SESSION['member_name']       = $member['pName'];
            $_SESSION['member_id']         = $member['pId'];
            $_SESSION['member_pDep']       = $member['pDep'];
            $_SESSION['member_test']       = $member['pTest'];
            $_SESSION['member_supervisor'] = $staff->isSupervisor($member['pId']);

            unset($_COOKIE['member_id'], $_COOKIE['member_pDep']);

            setcookie('member_id', $member['pId'], 0, "/");
            setcookie('member_pDep', $member['pDep'], 0, "/");
            //

            //權限列表
            $sql  = "SELECT pName,pSessionName,pId FROM tPeopleInfoAuthority";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $temp = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($temp as $k => $v) {
                $peopleInfoAuthority[$v['pId']] = ($v['pSessionName']) ? $v['pSessionName'] : $v['pName'];
            }
            unset($temp);

            //部門權限
            $sql  = "SELECT * FROM tPowerList WHERE pId = '" . $member['pDep'] . "'";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $depAuthority = $stmt->fetch(PDO::FETCH_ASSOC);
            $authority    = json_decode($depAuthority['pFunction'], true);
            unset($depAuthority);
            if ($member['pAuthority'] != '') {
                //個人權限
                $Personalauthority = json_decode($member['pAuthority'], true);

                //如果有個人權限以個人權限的為主
                foreach ($authority as $key => $value) {
                    if ($Personalauthority[$key] != $value && $Personalauthority[$key] != '') {
                        $authority[$key] = $Personalauthority[$key];

                    }
                }

                //20220713 修正指定部門以外權限無法指定問題
                foreach ($Personalauthority as $k => $v) {
                    if (! array_key_exists($k, $authority)) {
                        $authority[$k] = $Personalauthority[$k];
                    }
                }
                ##
            }

            foreach ($authority as $k => $v) {
                $k = str_replace('authority_', '', $k);
                // 檢查 $peopleInfoAuthority 中是否存在該鍵值
                if (isset($peopleInfoAuthority[$k])) {
                    $_SESSION[$peopleInfoAuthority[$k]] = $v;
                }
            }
            ##

            //20220721 將 session 儲存至 cookie 中
            setcookie('member_session', json_encode($_SESSION), 0, "/");
            ##
        }

        return $is_pass;
    }

    public function Logout()
    {
        unset($_SESSION['member_job']);
        unset($_SESSION['member_acc']);
        unset($_SESSION['member_name']);
        unset($_SESSION['member_id']);
        @session_destroy();

        unset($_COOKIE['member_id'], $_COOKIE['member_pDep'], $_COOKIE['member_session']);

        // 修正 PHP 8+ setcookie null 參數問題 - 使用空字串替代 null
        setcookie('member_id', '', time() - 3600, '/');
        setcookie('member_pDep', '', time() - 3600, '/');
        setcookie('member_session', '', time() - 3600, '/');
    }

}
