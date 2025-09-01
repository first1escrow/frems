<?php
require_once dirname(__DIR__) . '/configs/config.class.php';

class Base extends PDO
{
    const DATE_TYPE_AD = 1;
    const DATE_TYPE_TW = 2;

    const DB_TABLE_ESCROW         = 1;
    const DB_TABLE_ESCROWCHECKOUT = 2;
    const DB_TABLE_ESCROWDETAIL   = 3;
    const DB_TABLE_ESCROWPAYMENT  = 4;

    const DATE_FORMAT_NUM_MONTH       = 1;
    const DATE_FORMAT_NUM_DATE        = 2;
    const DATE_FORMAT_NUM_TIME        = 3;
    const REPORT_DATE_FORMAT_NUM_DATE = 4;

    protected $dbh;

    public function __construct()
    {
        $dsn = 'mysql:dbname=' . $GLOBALS['DB_ESCROW_NAME'] .
            ';host=' . $GLOBALS['DB_ESCROW_LOCATION'];
        try {
            $this->dbh = new PDO($dsn,
                $GLOBALS['DB_ESCROW_USER'],
                $GLOBALS['DB_ESCROW_PASSWORD'],
                array(PDO::ATTR_PERSISTENT   => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\''));
            $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

        } catch (PDOException $e) {
            // echo "<pre>";
            // print_r($e);
            // echo "</pre>";
        }
    }

    public function GetDbConnection()
    {
        return $this->dbh;
    }

    public function SubClassToName($sb, $sb_list)
    {
        foreach ($sb_list as $key => $val) {
            if (($sb & (1 << $val['ID'])) > 0) {
                $arr_sb[] = $val['Name'];
            }
        }
        if (count($arr_sb) == 0) {
            return "";
        } else {
            return implode("、", $arr_sb);
        }
    }

    public function ToSex($key)
    {
        if ($key == '0') {
            return '女';
        } else {
            return '男';
        }
    }

    public function ToCar($key)
    {
        if ($key == '0') {
            return '無';
        } else {
            return '有';
        }
    }

    public function ToCategoryCSMail($key)
    {
        switch ($key) {
            case 0:
                return '其他';
                break;
            case 1:
                return '履約保證相關';
                break;
            case 2:
                return '加入特約地政士';
                break;
            case 3:
                return '加入合作仲介';
                break;
        }
    }

    public function ConvertOption($data, $value, $option, $hasNone = false)
    {
        $aOptions = array();
        if ($hasNone) {
            $aOptions[0] = '--------';
        }
        foreach ($data as $k => $v) {
            $aOptions[$v[$value]] = $v[$option];
        }
        return $aOptions;
    }

    public function ConvertBankOption($data, $value, $option, $hasNone = false)
    {
        $aOptions = array();
        if ($hasNone) {
            $aOptions[0] = '--------';
        }
        foreach ($data as $k => $v) {
            $aOptions[$v[$value]] = $v[$option] . '(' . $v[$value] . ')';
        }
        return $aOptions;
    }

    public function GetBudLevel()
    {
        $menu = array();
        for ($i = 1; $i < 100; $i++) {
            $menu[$i] = $i;
        }
        for ($i = 1; $i < 20; $i++) {
            $menu['B' . $i] = 'B' . $i;
        }
        return $menu;
    }

    public function CutToCertifyId($acc)
    {
        return substr($acc, -9, 9);
    }

    public function GetCategorySex()
    {
        return array(1 => '男', 0 => '女');
    }

    public function GetCategoryCar()
    {
        return array(1 => '有', 0 => '無');
    }

    public function GetCategoryCertifyID()
    {
        return array(1 => '身份證編號', 2 => '統一編號');
    }

    public function GetCategoryRealestate()
    {
        // return array(0=>'------', 1=>'加盟店', 2=>'直營');
        // return array(0=>'------', 1=>'加盟店', 2=>'直營', 3=>'非仲介成交');
        return [
            0 => '------',
            1 => '加盟店',
            2 => '台屋直營',
            3 => '非仲介成交',
            4 => '品牌直營',
        ];
    }

    public function GetCategoryBranchStatus()
    {
        return array(1 => '啟用', 2 => '停用', 3 => '暫停');
    }

    public function GetCategoryScrivenerStatus()
    {
        return array(1 => '啟用', 2 => '停用', 3 => '重複建檔', 4 => '未簽約');
    }

    public function GetCategoryIdentify()
    {
        return array(1 => '------', 2 => '身份證編號', 3 => '統一編號', 4 => '居留證號碼');
    }

    public function GetCategoryRecall()
    {
        return array(1 => '------', 2 => '整批', 3 => '結案');
    }

    public function GetCategoryUseSplit()
    {
        return array(0 => '非都市使用分區', 1 => '都市使用分區');
    }

    public function GetCategoryStoreClass()
    {
        return array(1 => '總店', 2 => '單店');
    }

    public function GetCategoryContract()
    {
        return array(1 => '土地買賣契約', 2 => '房地買賣契約');
    }

    public function ToTradeFunc($tradecode, $exportcode)
    {
        $tradecode  = trim($tradecode);
        $exportcode = trim($exportcode);
        $str        = "";

        if ($tradecode == '1930') {
            $str = '臨櫃繳款';
        } else if ($tradecode == '1787' || $tradecode == '178K') {
            $str = '金融卡轉帳存入';
        } else if ($tradecode == '178V') {
            $str = '語音系統轉入';
        } else if ($tradecode == '178W') {
            $str = '網路銀行轉入';
        } else if ($tradecode == '178T') {
            $str = '財金公司網際轉入';
        } else if ($tradecode == '178M') {
            $str = '晶片金融卡ATM轉帳存入';
        } else if ($tradecode == '1793' && $exportcode == '9999999') {
            $str = '本交票';
        } else if ($tradecode == '1793' && $exportcode != '9999999') {
            $str = '中心整批入帳';
        } else if ($tradecode == '178Y' && $exportcode == '8888888') {
            $str = '匯跨行匯款遭退匯，系統自動回存';
        } else if ($tradecode == '178Y' && $exportcode != '8888888') {
            $str = '企網轉帳支出';
        } else if ($tradecode == '1950' && $exportcode == '7777777') {
            $str = '匯入匯款自動解款';
        } else if ($tradecode == '1950' && $exportcode != '7777777') {
            $str = '臨櫃繳款';
        }

        return $str;
    }

    public function GetCategoryInvoice()
    {
        return array(1 => '二聯式', 2 => '三聯式');
    }

    public function SelectToNumber($arr)
    {
        $value = implode(",", $arr);
        return $value;
    }

    public function NumberToSelect($num)
    {
        $selects = array();
        for ($i = 300; $i > 0; $i--) {
            if (($num / (1 << $i)) >= 1) {
                $selects[] = $i;
                $num -= (1 << $i);
            }
        }
        return $selects;
    }

    public function GetListSmsId($rs_tit)
    {
        $list = array();
        foreach ($rs_tit as $k => $v) {
            $list[] = "'" . $v['id'] . "'";
        }
        return " (" . implode(",", $list) . ") ";
    }

    public function FieldToSelect($var)
    {
        if (empty($var)) {
            return array();
        } else {
            return explode(",", $var);
        }
    }

    public function SelectToField($arr)
    {
        if (empty($arr)) {
            return "";
        } else {
            return implode(",", $arr);
        }
    }

    public function ConvertDateToRoc($date, $type)
    {
        $roc = '';

        $reg[0] = '/^(19|20)\d\d-(0[1-9]|1[012])/';
        $reg[1] = '/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';
        $reg[2] = '/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]) [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/';

        switch ($type) {
            case self::DATE_FORMAT_NUM_MONTH:
                if (preg_match($reg[0], $date)
                    || preg_match($reg[1], $date)
                    || preg_match($reg[2], $date)) {
                    $year  = substr($date, 0, 4);
                    $year  = $year - 1911;
                    $month = substr($date, 5, 2);
                    $roc   = $year . '-' . $month;
                } else {
                    $roc = $date;
                }
                break;
            case self::DATE_FORMAT_NUM_DATE:
                if (preg_match($reg[1], $date)
                    || preg_match($reg[2], $date)) {
                    $year  = substr($date, 0, 4);
                    $year  = $year - 1911;
                    $month = substr($date, 5, 2);
                    $day   = substr($date, 8, 2);
                    $roc   = $year . '-' . $month . '-' . $day;
                } else {
                    $roc = $date;
                    // echo 'A';
                }
                break;
            case self::DATE_FORMAT_NUM_TIME:
                if (preg_match($reg[2], $date)) {
                    $year  = substr($date, 0, 4);
                    $year  = $year - 1911;
                    $month = substr($date, 5, 2);
                    $day   = substr($date, 8, 2);
                    $time  = substr($date, 11, 5);
                    $roc   = $year . '-' . $month . '-' . $day . ' ' . $time;
                } else {
                    $roc = $date;
                }
                break;
            case self::REPORT_DATE_FORMAT_NUM_DATE: //報表用
                if (preg_match($reg[1], $date)
                    || preg_match($reg[2], $date)) {
                    $year  = substr($date, 0, 4);
                    $year  = $year - 1911;
                    $month = substr($date, 5, 2);
                    $day   = substr($date, 8, 2);
                    $roc   = $year . '-' . $month . '-' . $day;
                } elseif (preg_match("/0000-00-00/", $date)) {
                    $roc = "000-00-00";
                } else {
                    $roc = $date;
                    // echo 'A';
                }
                break;
        }
        return $roc;
    }

    public function ConvertDateToAD($date, $type)
    {
        $ad = '';

        $reg[0] = '/^([01]?\d\d)-(0[1-9]|1[012])/';
        $reg[1] = '/^([01]?\d\d)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';
        $reg[2] = '/^([01]?\d\d)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]) [0-9]{2}\:[0-9]{2}\:[0-9]{2}$/';

        switch ($type) {
            case self::DATE_FORMAT_NUM_MONTH:
                if (preg_match($reg[0], $date)
                    || preg_match($reg[1], $date)
                    || preg_match($reg[2], $date)) {
                    $arr   = explode('-', $date);
                    $year  = trim($arr[0]) + 1911;
                    $month = $arr[1];
                    $ad    = $year . '-' . $month;
                } else {
                    $ad = $date;
                }
                break;
            case self::DATE_FORMAT_NUM_DATE:
                if (preg_match($reg[1], $date)
                    || preg_match($reg[2], $date)) {
                    $arr   = explode('-', $date);
                    $year  = trim($arr[0]) + 1911;
                    $month = trim($arr[1]);
                    $arr   = explode(' ', $arr[2]);
                    $day   = $arr[0];
                    $ad    = $year . '-' . $month . '-' . $day;
                } else {
                    $ad = $date;
                }
                break;
            case self::DATE_FORMAT_NUM_TIME:
                if (preg_match($reg[2], $date)) {
                    $arr   = explode('-', $date);
                    $year  = trim($arr[0]) + 1911;
                    $month = trim($arr[1]);
                    $arr   = explode(' ', $arr[2]);
                    $day   = $arr[0];
                    $time  = $arr[1];
                    $ad    = $year . '-' . $month . '-' . $day . ' ' . $time;
                } else {
                    $ad = $date;
                }
                break;
        }
        return $ad;
    }

}
