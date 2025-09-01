<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

require_once dirname(__DIR__) . '/openadodbAPI.php'; //checklist一定會用
require_once dirname(__DIR__) . '/openpdodbAPI.php';
require_once dirname(__DIR__) . '/rc4.php';
require_once dirname(__DIR__) . '/Snoopy.class.php';
require_once dirname(__DIR__) . '/sms/sms_function_manually.php';

$GLOBALS['firstCompany'] = json_decode(file_get_contents('/var/www/html/lib/company.json'), true); //建經的公司資訊
$psIArr                  = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
$psIArr2                 = array('a' => 'kdsi24N', 'b' => 'Lda21rm', 'c' => 'P4qetf6', 'd' => 'Fu8ck0g', 'e' => 'Q4d4y51', 'f' => 'Z5dyg4h', 'g' => 'r4eE48h', 'h' => '3D54hw0', 'i' => 'Ko45lg4', 'j' => '75e0dRq', 'k' => 'c6W4gm3', 'l' => 'eT7wr1j', 'm' => 'E4gw7tn', 'n' => 'd45h85wN', 'o' => 'Sho25saN', 'p' => 'Oh35NOd4', 'q' => 'Ar158shI', 'r' => 'f1e5hU0', 's' => 'wED45dgh', 't' => 'Edj4k3', 'u' => 'e4g1qe1B', 'v' => 'g4Kh8rJ', 'x' => 'Du52wtO0', 'y' => 'Zs5f4iU1Y', 'z' => 'Bv13s5F', '0' => '8Fkg410d', '1' => 'Ko0fma', '2' => 'ko2re7ra', '3' => 'NF5ge1w', '4' => 'f0b1we', '5' => 'q1fxp32', '6' => 'Jh41d13', '7' => 'l5epu4G', '8' => '8G1t3fu', '9' => 'Ug8ao0');

//輸入轉成變數
$rawData = '';
$rawData = file_get_contents("php://input");

// if (empty($rawData)) {
if (preg_match("/^\{.*\}$/is", $rawData) || (preg_match("/^\[.*\]$/is", $rawData))) {
    $jsonArr = array();
    $jsonArr = json_decode($rawData);
    $rawData = decodeIn($jsonArr->Data);

    $jsonArr = array();
    $jsonArr = json_decode($rawData, true);
    foreach ($jsonArr as $k => $v) {
        parse_str(trim($k) . '=' . trim($v));
    }
    unset($jsonArr);
} else {
    foreach ($_POST as $k => $v) {
        // echo '$k = '.$k.' ,$v = '.$v."<br>\n" ;
        parse_str(trim($k) . '=' . trim($v));
    }
}
##

//濾除sql injection字串
function escapeStrOut($v = '')
{
    $v = preg_replace("/create/i", "", $v);
    $v = preg_replace("/modify/i", "", $v);
    $v = preg_replace("/rename/i", "", $v);
    $v = preg_replace("/alter/i", "", $v);
    $v = preg_replace("/drop/i", "", $v);
    $v = preg_replace("/commit/i", "", $v);
    $v = preg_replace("/grant/i", "", $v);
    $v = preg_replace("/cast/i", "", $v);
    $v = preg_replace("/select/i", "", $v);
    $v = preg_replace("/insert/i", "", $v);
    $v = preg_replace("/update/i", "", $v);
    $v = preg_replace("/replace/i", "", $v);
    $v = preg_replace("/delete/i", "", $v);

    $v = preg_replace("/[\s+\"\'\`]?from[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?or[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?and[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?xor[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?not[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?like[\s+\"\'\`]?/i", "", $v);
    $v = preg_replace("/[\s+\"\'\`]?join[\s+\"\'\`]?/i", "", $v);

    $v = preg_replace("/user/i", "", $v);
    $v = preg_replace("/union/i", "", $v);
    $v = preg_replace("/ where /i", "", $v);
    $v = preg_replace("/concat/i", "", $v);
    $v = preg_replace("/sub\_str/i", "", $v);
    $v = preg_replace("/chr\(\d+\)/i", "", $v);
    $v = preg_replace("/char\(\d+\)/i", "", $v);
    $v = preg_replace("/ascii/i", "", $v);

    $v = preg_replace("/\%[0-9a-zA-Z]{2}/", "", $v);
    $v = preg_replace("/\!+/", "", $v);
    $v = preg_replace("/\|+/", "", $v);
    $v = preg_replace("/\'+/", "", $v);
    $v = preg_replace("/\"+/", "", $v);
    $v = preg_replace("/\++/", "", $v);
    $v = preg_replace("/\&+/", "", $v);
    $v = preg_replace("/\*+/", "", $v);
    $v = preg_replace("/\\+/", "", $v);
    $v = preg_replace("/\/{2,}/", "", $v);
    $v = preg_replace("/\?+/", "", $v);
    $v = preg_replace("/\-{2,}/", "", $v);
    $v = preg_replace("/\#+/", "", $v);
    $v = preg_replace("/\=+/", "", $v);

    $v = preg_replace("/^\s+/", "", $v);
    $v = preg_replace("/\s+&/", "", $v);

    return $v;
}
##

//計算取得 seed
function getSeed($ch)
{
    global $psiArr2;

    return $psIArr2[$ch];
}
##

//字串編碼
function enCrypt($str, $seed = 'first1app24602')
{
    global $psiArr;

    $encode = '';
    $rc     = new Crypt_RC4;
    $rc->setKey($seed);
    $encode = $rc->encrypt($str);

    return $encode;
}
##

//字串解碼
function deCrypt($str, $seed = 'first1app24602')
{
    global $psiArr;

    $decode = '';
    $rc     = new Crypt_RC4;
    $rc->setKey($seed);
    $decode = $rc->decrypt($str);

    return $decode;
}
##

//決定是否陣列遞迴
function recursiveCheck($arr = array())
{
    foreach ($arr as $k => $v) {
        if (is_array($v)) {
            $arr[$k] = recursiveCheck($v);
        }
        //陣列、繼續遞迴
        else {
            $arr[$k] = escapeStrOut($v);
        }
        //字串檢核
    }

    return $arr;
}
##

//主程式
function escapeStr($str)
{
    if (is_array($str)) {
        return recursiveCheck($str);
    }
    //傳入變數為陣列矩陣
    else {
        return escapeStrOut($str);
    }
    //字串檢核
}
##

//產出授權碼
function genAuthCode()
{
    $authCode = '';
    for ($i = 0; $i < 4; $i++) {
        $authCode .= rand(0, 9);
    }

    return $authCode;
}
##
//案件查詢手機如果後台沒有就不要發驗證碼20170215
function checkScrivenerSms($acc, $pmobile)
{
    global $conn;

    $acc = (int) preg_replace("/^[a-z]+/i", "", $acc);
    $sql = "SELECT * FROM tScrivenerSms WHERE sMobile ='" . $pmobile . "' AND sScrivener='" . $acc . "' AND sDel = 0";
    $rs  = $conn->Execute($sql);
    // $mobile = $rs->fields['sMobile'] ;
    $total = $rs->RecordCount();

    if ($total > 0) {
        return true;
    } else {
        return false;
    }
}
//產出授權碼
function generateSMS($ide, $acc, $pmobile, $authCode)
{
    global $conn;

    // $txt = '您的授權碼為：'.$authCode.'！請於30分鐘內完成授權碼輸入，逾期將失效！第一建經提醒您' ;
    // $txt = '新安裝裝置授權碼為：'.$authCode.'(指定案件查詢對象門號為：'.$pmobile.')！請於30分鐘內完成授權碼輸入，逾期將失效！第一建經提醒您' ;
    $txt = '第一建經通知您,您有' . $pmobile . '申請「第一建經」APP授權，驗證碼' . $authCode . '，此碼30分鐘後失效；若非相關人員申請，請立即來電客服。';
    // echo $txt ; exit ;

    $acc = (int) preg_replace("/^[a-z]+/i", "", $acc);
    if ($ide == '1') {
        $sql    = 'SELECT * FROM tScrivener WHERE sId = "' . $acc . '";';
        $rs     = $conn->Execute($sql);
        $mobile = $rs->fields['sMobileNum'];
    } else if ($ide == '2') {
        $sql    = 'SELECT * FROM tBranch WHERE bId = "' . $acc . '";';
        $rs     = $conn->Execute($sql);
        $mobile = $rs->fields['bMobileNum'];
    }
    // echo "m=".$sql ; exit ;
    if (preg_match("/^09/", $mobile) && (strlen($mobile) == 10)) {
        $sms = new SMS_Gateway();
        $sms->manual_send($mobile, $txt, 'y', '發送授權碼', 'APP');

        return true;
    } else {
        return false;
    }

}
##

//通知經辦 $ide = 身分別(1=代書、2=仲介), acc = 帳號, msg = 通知內容
function informByEmail($ide, $acc, $msg, $title)
{
    global $conn;
    // print_r($msg) ;

    if ($ide == '1') {
        //代書訊息通知
        $sql = 'SELECT *, (SELECT pHiFaxAccount FROM tPeopleInfo WHERE a.sUndertaker1=pId) as HiBox FROM tScrivener AS a WHERE sId = "' . (int) substr($acc, 2) . '";';
        $rs  = $conn->Execute($sql);
        if (!$rs->EOF) {
            $sql = '
				INSERT INTO
					tAppInform
				SET
					aStaffId = "' . $rs->fields['sUndertaker1'] . '",
					aEmail = "' . $rs->fields['HiBox'] . '",
					aId = "' . $acc . '",
					aTitle = "' . $rs->fields['sName'] . ' ' . $title . '",
					aContent = "' . base64_encode($msg) . '",
					aCreateTime = "' . date("Y-m-d H:i:s") . '"
			;';
            if ($conn->Execute($sql)) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }

        ##
    } else {
        //仲介訊息通知

        ##
    }

    return true;
}
##
// 日期格式轉換
function DateFormate($val)
{
    $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", "", $val));
    $tmp = explode('-', $val);

    // if (preg_match("/0000/",$tmp[0])) {    $tmp[0] = '000' ; }
    // else { $tmp[0] -= 1911 ; }

    $val = $tmp[0] . '/' . $tmp[1] . '/' . $tmp[2];
    unset($tmp);

    return $val;
}

//檢查票據是否兌現(日期檢查)
//$_date=>原始日期, $_dateType=>回覆日期格式('ymd','y','m','d','ym','md'), $_dateForm=>民國('r')、西元('b'), $_delimiter=>分隔符號, $_minus=>加減日數, $_sat=>是否過六日
function tDate_check($_date, $_dateForm = 'ymd', $_dateType = 'r', $_delimiter = '', $_minus = 0, $_sat = 0)
{
    $_aDate[0] = (substr($_date, 0, 3) + 1911);
    $_aDate[1] = substr($_date, 3, 2);
    $_aDate[2] = substr($_date, 5);

    //$_cheque_date = implode('-',$_tDate) ;

    //是否遇六日要延後(六延兩天、日延一天)
    if ($_sat == '1') {
        $_ss = 0;
        $_ss = date("w", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0]));
        if ($_ss == '0') { //如果是星期日的話，則延後一天
            if ($_minus < 0) {
                $_minus = $_minus + $_minus + $_minus;
            } else {
                $_minus = $_minus + $_minus;
            }
        } else if ($_ss == '6') { //如果是星期六的話，則延後兩天
            if ($_minus < 0) {
                $_minus = $_minus + $_minus;
            } else {
                $_minus = $_minus + $_minus + $_minus;
            }
        }
    }
    ##

    $_t = date("Y-m-d", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0])); //設定日期為 t+1 天
    unset($_aDate);

    $_aDate = explode('-', $_t);

    if ($_dateType == 'r') { //若要回覆日期格式為"民國"
        $_aDate[0] = $_aDate[0] - 1911;
    } else { //若要回覆日期格式為"西元"

    }

    //決定回覆日期格式
    switch ($_dateForm) {
        case 'y': //年
            return $_aDate[0];
            break;
        case 'm': //月
            return $_aDate[1];
            break;
        case 'd': //日
            return $_aDate[2];
            break;
        case 'ym': //年月
            return $_aDate[0] . $_delimiter . $_aDate[1];
            break;
        case 'md': //月日
            return $_aDate[1] . $_delimiter . $_aDate[2];
            break;
        case 'ymd': //年月日
            return $_aDate[0] . $_delimiter . $_aDate[1] . $_delimiter . $_aDate[2];
            break;
        default:
            break;
    }
    ##
}
##

//取得銀行名稱與代碼
function getBank($head, $branch)
{
    global $conn;
    $bank = array();

    $sql = 'SELECT bBank4_name FROM tBank WHERE bBank3 = "' . $head . '" AND bBank4 = "";';
    $rs  = $conn->Execute($sql);
    if (!$rs->EOF) {
        $bank['bankHead']   = $rs->fields['bBank4_name'];
        $bank['bankHeadNo'] = $head;
    }

    $sql = 'SELECT bBank4_name FROM tBank WHERE bBank3 = "' . $head . '" AND bBank4 = "' . $branch . '";';
    $rs  = $conn->Execute($sql);

    if (!$rs->EOF) {
        $bank['bankBranch']   = $rs->fields['bBank4_name'];
        $bank['bankBranchNo'] = $branch;
    }

    return $bank;
}
##

//檢查帳戶是否已被登錄
function checkRegist($account, $identity, $dId)
{
    global $conn;

    if (($account == 'a123456') && ($identity == '1')) {
        global $deviceId;
        $deviceId = '000000000';
        return true;
    } elseif (($account == 'first1234') && ($identity == '1')) {
        global $deviceId;
        $deviceId = 'first';
        return true;
    } elseif (($account == 'SC0903') && ($identity == '1')) { //SC0903代書是由我們這裡建立帳號，怕跟他原本留的資料手機型號不同
        global $deviceId;
        $deviceId = 'G1AZCY03W929';
        return true;
    }
    // else if (($account == 'TH00252') && ($identity == '2')) {
    //     global $deviceId ;
    //     $deviceId = '1111111111' ;
    //     return true ;
    // }
    else {
        $sql = 'SELECT * FROM tAppAccount WHERE aAccount="' . $account . '" AND aIdentity = "' . $identity . '" AND aDeviceId = "' . $dId . '";';

        $rs = $conn->Execute($sql);

        if ($rs->EOF) {
            $logTxt = '';
            $logTxt = date("Y-m-d H:i:s") . "\nError_Code=405, Error_Msg=查無帳戶資料\n帳號:" . $account . "裝置ID:" . $dId . "\n\n";
            writeFH(dirname(__FILE__) . '/log/access_scrivener.log', $logTxt, 'a+');
            return false;
        }
        return true;
    }
}
##

//Push to line
function pushMsg($userId, $msg)
{
    $result = file_get_contents('https://www.design.e-twlink.com/first1/lineBot/first1LinePush.php?userId=' . $userId . '&msg=' . urlencode($msg));

    if ($result) {
        return true;
    } else {
        return false;
    }

    ##
}
##

//取得遠端IP
function getRemoteIP()
{
    $myip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $myip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $myip = $_SERVER['REMOTE_ADDR'];
    }

    return $myip;
}
##

//設定 slack 帳號使用者資訊
function setSlackUserProfile($aId)
{
    global $conn;
    $url = 'https://slack.com/api/users.profile.set';

    if (!empty($aId)) {
        //設定參數
        $sql = 'SELECT b.aSlackToken, b.SlackId, a.aName, a.aAccount FROM tAppAccount AS a, tAppSlack AS b WHERE a.aSlackId=b.aId AND a.aId = "' . $aId . '"';
        $rs  = $conn->Execute($sql);
        $acc = mb_substr($rs->fields['aAccount'], 0, 21);
        if (!$rs->EOF) {

            $args = array(
                'token'   => $rs->fields['aSlackToken'],
                'pretty'  => 1,
                'user'    => $rs->fields['SlackId'],
                'profile' => json_encode(array('last_name' => $rs->fields['aName'], 'username' => $acc)),
            );
            ##

            //觸發執行
            $snoopy = new Snoopy;
            $snoopy->submit($url, $args);
            $html = $snoopy->results;
            ##

            $list = json_decode($html, true);

            if ($list['ok'] == 1) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }

    } else {
        return false;
    }

}
##

//設定 Slack 頻道
function setGroup($id)
{
    global $conn;

    $sql = "SELECT
                b.aSlackToken,
                b.SlackId,
                a.aName,
                a.aParentId,
                (SELECT (SELECT pSlackId FROM tPeopleInfoAccount AS d WHERE d.pInfoId = c.sUndertaker1) FROM tScrivener AS c WHERE c.sId = SUBSTR(a.aParentId,3)) AS Undertaker,
                (SELECT (SELECT pSlackToken FROM tPeopleInfoAccount AS d WHERE d.pInfoId = c.sUndertaker1) FROM tScrivener AS c WHERE c.sId = SUBSTR(a.aParentId,3)) AS UndertakerSlackToken,
                b.aId AS tAppSlack
            FROM
                tAppAccount AS a,
                tAppSlack AS b
            WHERE
                a.aSlackId=b.aId AND a.aId ='" . $id . "'";
    $rs = $conn->Execute($sql);

    $name = checkGroupName(strtolower($rs->fields['aParentId'])); //要做檢查是否有重複群組名稱

    $tAppSlackAid = $rs->fields['tAppSlack'];
    $userToken    = $rs->fields['aSlackToken'];

    $undertaker = $rs->fields['Undertaker']; //經辦ID

    // $userToken = 'xoxp-61019054816-61677452003-95778464870-36dce6c2e56ab0619c8c39f579c8af34'; //我的
    // $undertaker = 'U2K11RJA0';
    //建立群組  代書建立群組 邀請經辦入群
    $args  = array('token' => $userToken, 'pretty' => 1, 'name' => $name);
    $group = creatGroup($args);

    if ($group) {
        //經辦入群組
        $args = array('token' => $userToken, 'pretty' => 1, 'channel' => $group, 'user' => $undertaker);
        inviteGroup($args);

        $sql = "UPDATE tAppSlack SET aSlackGroup ='" . $group . "' WHERE aId = '" . $tAppSlackAid . "'";
        $conn->Execute($sql);
    }
}

function checkGroupName($name)
{
    $ck = 0;

    $token = 'xoxp-61019054816-61677452003-95778464870-36dce6c2e56ab0619c8c39f579c8af34'; //
    $args  = array('token' => $token, 'pretty' => 1);

    $url    = 'https://slack.com/api/rtm.start';
    $snoopy = new Snoopy;
    $snoopy->submit($url, $args);
    $html = $snoopy->results;

    $list = json_decode($html, true);

    foreach ($list['groups'] as $k => $v) {

        if (preg_match("/$name.*/", $v['name'])) {
            // echo $v['name']."\r\n";
            $ck++;
        }
    }

    if ($ck > 0) { //如果已經建立名稱則改名子
        $name = $name . '_' . str_pad(($ck + 1), 3, '0', STR_PAD_LEFT);
    }

    return $name;
}

function creatGroup($args)
{
    $url    = 'https://slack.com/api/groups.create';
    $snoopy = new Snoopy;
    $snoopy->submit($url, $args);
    $html = $snoopy->results;

    $list = json_decode($html, true);

    // print_r($list);
    if ($list['ok'] == true) {
        return $list['group']['id'];
    } else {
        return false;
    }
}

function inviteGroup($args)
{
    $url    = 'https://slack.com/api/groups.invite';
    $snoopy = new Snoopy;
    $snoopy->submit($url, $args);
    $html = $snoopy->results;

    $list = json_decode($html, true);

    if ($list['ok'] == true) {
        return $list['group']['id'];
    } else {
        return false;
    }

}
##

//寫檔紀錄
function writeFH($path, $txt, $type = 'a+')
{
    $fh = fopen($path, $type);
    fwrite($fh, $txt);
    fclose($fh);
}
##

//讀檔
function readFH($path)
{
    return file_get_contents($path);
}
##

//編碼輸出
function encodeOut($data = '')
{
    if (!empty($data)) {
        $aes = new AES('twhg2016first1newappauthkeyforae');

        return $aes->encode($data);
    } else {
        return false;
    }

}
##

//解碼取得
function decodeIn($data = '')
{
    if (!empty($data)) {
        $iv   = substr($data, 0, 16);
        $data = substr($data, 16);

        $aes = new AES('twhg2016first1newappauthkeyforae');

        return $aes->decode($data, $iv);
    } else {
        return false;
    }

}
##

//AES 編碼(AES-256-CBC)
class AES
{
    private $cipherMethod;
    private $key;
    private $iv;

    public function __construct($key = 'sMwYNtWb95mYHtwdjqRHfi3xENECUxdl')
    {
        $this->cipherMethod = 'AES-256-CBC';
        $this->key          = $key;
    }

    public function encode($rawString, $iv = '')
    {
        $this->iv        = empty($iv) ? $this->getIV(openssl_cipher_iv_length($this->cipherMethod)) : $iv;
        $encryptedString = openssl_encrypt($rawString, $this->cipherMethod, $this->key, 0, $this->iv);

        return $this->iv . $encryptedString;
    }

    public function decode($encryptedString, $iv = '')
    {
        $this->iv        = empty($iv) ? $this->getIV(openssl_cipher_iv_length($this->cipherMethod)) : $iv;
        $decryptedString = openssl_decrypt($encryptedString, $this->cipherMethod, $this->key, 0, $this->iv);

        return $decryptedString;
    }

    private function getIV($maxLength = 16)
    {
        $chTable = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $iv      = '';

        while (strlen($iv) < 16) {
            $iv .= substr($chTable, rand(0, strlen($chTable)), 1);
            if (strlen($iv) >= 16) {
                break;
            }

        }

        return $iv;
    }
}
##
