<?php
require_once dirname(__DIR__) . '/.env.php';

/*
pid 保證號
sid 地政士id
bid 店id
target 類別: income / trans(出帳) -> 賣方先動撥/仲介服務費/扣繳稅款/代清償/點交
$tid 出帳id / 入帳id
 */

class SMS_Gateway extends PDO
{
    private static $dDSN;
    private static $dUser;
    private static $dPassword;
    public $DB_link;
    public $execSQL;

    public function __construct()
    {
        global $env;

        $this->DB_link      = '';
        $this->execSQL      = '';
        $this->dbtype_sql   = $env['db']['197']['driver'];
        $this->host_sql     = $env['db']['197']['host'];
        $this->dbname_sql   = $env['db']['197']['database'];
        $this->username_sql = $env['db']['197']['username'];
        $this->password_sql = $env['db']['197']['password'];
        $this->log_path     = dirname(__DIR__) . '/log/sms/';

        $this->fet_SysId      = $env['sms']['fet']['fet_SysId'];
        $this->fet_SrcAddress = $env['sms']['fet']['fet_SrcAddress'];
        $this->acc_china      = $env['sms']['cht']['acc_china'];
        $this->pwd_china      = $env['sms']['cht']['pwd_china'];
        $this->uid            = $env['sms']['apol']['uid'];
        $this->upass          = $env['sms']['apol']['upass'];

        try {
            // utf-8
            $this->DB_link = new PDO($this->dbtype_sql . ':host=' . $this->host_sql . ';dbname=' . $this->dbname_sql, $this->username_sql, $this->password_sql);
            // 資料庫使用 UTF8 編碼
            $this->DB_link->query('SET NAMES UTF8');
            // $this->DB_link->query('SET GLOBAL interactive_timeout = 120');
            // $this->DB_link->query('SET GLOBAL wait_timeout = 120');
        } catch (PDOException $e) {
            echo "DBconnectFalse: " . $e->getMessage();
            return "DBconnectFalse: " . $e->getMessage();
        }
    }

    public function manual_send($mobile, $txt, $ok = "n", $sendname = '', $myKind = '手動', $name = '', $pid='') //手動簡訊(電話),文字,是否發送

    {
        $sys = $this->getSmsSystem();

        $sms_txt = $txt;

        $tmp  = explode(',', $mobile);
        $_all = array();
        for ($i = 0; $i < count($tmp); $i++) {
            $_all[$i]['mMobile'] = $tmp[$i];
        }

        $_total = count($_all);
        if ($ok == 'y') {
            $_s  = 0;
            $_p  = 0;
            $_f  = 0;
            $_n  = 0;
            $tid = '';

            for ($i = 0; $i < $_total; $i++) {
                //echo $_all[$i]["mMobile"]."<br>";

                if (trim($_all[$i]["mMobile"]) != "") {
                    $check_word = array("-", "%", "_"); //分隔字元
                    $mobile_tel = str_replace($check_word, "", $_all[$i]["mMobile"]); //濾除分隔字元

                    //開始發送簡訊
                    if (preg_match('/^09[0-9]{8}$/', $mobile_tel)) {
                        $sms_id = $this->sms_send($mobile_tel, $name, $sms_txt, $myKind, $pid, $tid, $sys, $sendname);

                        if ($sms_id == 's') { //發送成功(s)
                            //$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
                            $_s++;
                        } else if ($sms_id == 'p') { //部份成功(p)
                            //$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
                            $_p++;
                        } else { //發送失敗(f)
                            //$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
                            $_f++;
                        }
                    } else { //門號錯誤(n)
                        //$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
                        $_n++;
                    }
                    ##
                }
            }
            //回覆簡訊發送結果
            return $this->out_sms_code($_s, $_p, $_f, $_n);
            ##
        } else {
            //回覆簡訊發送結果
            return $_all;
            ##
        }

    }

    public function send($pid, $sid, $bid, $target, $mobile, $stxt = '', $ok = 'n')
    {

        $_all = array();

        $sys            = $this->getSmsSystem();
        $_contract_data = $this->getContractData($pid);

        switch ($target) {
            case 'cheque':
                $_data  = $_data1  = $_data4  = $manager  = array();
                $_data  = $this->getsScrivenerMobile($pid, $sid); // 取得地政士發送簡訊對象
                $_data1 = $this->getsBranchMobile($pid, $bid, '店長'); // 取得店發送簡訊對象
                $_data4 = $this->getsBranchMobile($pid, $bid, '店東'); // 取得店發送簡訊對象

                $_data = array_merge($_data, $_data1, $_data4);

                unset($_data1, $_data4);
                //增加第二組仲介簡訊發送
                $bid2   = $this->getSecBranchMobile($pid);
                $_data1 = $_data4 = array();

                if ($bid2 > 0) {
                    $_data1 = $this->getsBranchMobile($pid, $bid2, '店長'); // 取得店發送簡訊對象
                    $_data4 = $this->getsBranchMobile($pid, $bid2, '店東'); // 取得店發送簡訊對象
                    $_data  = array_merge($_data, $_data1, $_data4);

                }

                unset($_data1, $_data4);

                //增加第三組仲介簡訊發送
                $bid3   = $this->getThrBranchMobile($pid);
                $_data1 = $_data4 = array();

                if ($bid3 > 0) {
                    $_data1 = $this->getsBranchMobile($pid, $bid2, '店長'); // 取得店發送簡訊對象
                    $_data4 = $this->getsBranchMobile($pid, $bid2, '店東'); // 取得店發送簡訊對象
                    $_data  = array_merge($_data, $_data1, $_data4);
                }
                $_all = $_data;
                unset($_data, $_data1, $_data4);

                // 加入其他通訊資料 (買、賣方與買、賣方經紀人)
                $_start = count($_all); //計算目前已存入之簡訊發送對象筆數
                $_t     = 0;

                //主買方
                $bCount = 0; //計算買方人數用

                $_all[$_start + $_t]["mName"]   = $_contract_data[0]["b_name"]; //主買方姓名
                $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_mobile"]; //主買方手機
                $bCount++;
                $_t++;
                ##
                //主買方其他電話
                $other_phone = $this->get_phone(1, $pid);

                for ($i = 0; $i < count($other_phone); $i++) {
                    $_all[$_start + $_t]["mName"]   = $_contract_data[0]["b_name"];
                    $_all[$_start + $_t]["mMobile"] = $other_phone[$i]['cMobileNum'];
                    $_t++;
                }

                unset($other_phone);

                ##
                //其他買方
                $_other_owners = $this->get_others($pid, '1');

                for ($i = 0; $i < count($_other_owners); $i++) {
                    $_all[$_start + $_t]["mName"]   = $_other_owners[$i]['cName']; //其他買方姓名
                    $_all[$_start + $_t]["mMobile"] = $_other_owners[$i]['cMobileNum']; //其他買方手機
                    $bCount++;
                    $_t++;
                }
                unset($_other_owners);
                ##
                ##
                //6買方代理人
                $_other_owners = $this->get_others($pid, '6');

                for ($i = 0; $i < count($_other_owners); $i++) {
                    $_all[$_start + $_t]["mName"]   = $_other_owners[$i]['cName']; //買方代理人姓名
                    $_all[$_start + $_t]["mMobile"] = $_other_owners[$i]['cMobileNum']; //買方代理人手機

                    $_t++;
                }
                unset($_other_owners);
                ##

                //買方經紀人
                //買方經紀人
                $other_phone = $this->get_phone(3, $pid);

                for ($i = 0; $i < count($other_phone); $i++) {
                    $_all[$_start + $_t]["mName"]   = $other_phone[$i]["cName"];
                    $_all[$_start + $_t]["mMobile"] = $other_phone[$i]['cMobileNum'];
                    $_t++;
                }

                unset($other_phone);

                // if ($_contract_data[0]["b_agent_mobile"] != "") {
                //     $_all[$_start+$_t]["mName"] = $_contract_data[0]["b_agent_name"] ;             //買方經紀人(1)姓名
                //     $_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_agent_mobile"] ;        //買方經紀人(1)手機
                //     $_t ++ ;
                // }

                // if ($_contract_data[0]["b_agent_mobile2"] != "") {
                //     $_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name2"] ;        //買方經紀人(2)姓名
                //     $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile2"] ;    //買方經紀人(2)手機
                //     $_t++;
                // }

                // if ($_contract_data[0]["b_agent_mobile3"] != "") {
                //     $_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name3"] ;        //買方經紀人(3)姓名
                //     $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile3"] ;    //買方經紀人(3)手機
                //     $_t++;
                // }

                // if ($_contract_data[0]["b_agent_mobile4"] != "") {
                //     $_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name4"] ;        //買方經紀人(4)姓名
                //     $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile4"] ;    //買方經紀人(4)手機
                //     $_t++;
                // }
                ##

                //主賣方
                $oCount = 0; //計算賣方人數用

                $_all[$_start + $_t]["mName"]   = $_contract_data[0]["o_name"]; //賣方姓名
                $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_mobile"]; //賣方手機
                $oCount++;
                $_t++;
                ##

                //主賣方其他電話
                $other_phone = $this->get_phone(2, $pid);

                for ($i = 0; $i < count($other_phone); $i++) {
                    $_all[$_start + $_t]["mName"]   = $_contract_data[0]["o_name"];
                    $_all[$_start + $_t]["mMobile"] = $other_phone[$i]['cMobileNum'];
                    $_t++;
                }

                unset($other_phone);
                ##

                //其他賣方
                $_other_owners = $this->get_others($pid, '2');

                for ($i = 0; $i < count($_other_owners); $i++) {
                    $_all[$_start + $_t]["mName"]   = $_other_owners[$i]['cName']; //其他賣方姓名
                    $_all[$_start + $_t]["mMobile"] = $_other_owners[$i]['cMobileNum']; //其他賣方手機
                    $oCount++;
                    $_t++;
                }
                unset($_other_owners);
                ##

                //7賣方代理人
                $_other_owners = $this->get_others($pid, '7');

                for ($i = 0; $i < count($_other_owners); $i++) {
                    $_all[$_start + $_t]["mName"]   = $_other_owners[$i]['cName']; //7賣方代理人姓名
                    $_all[$_start + $_t]["mMobile"] = $_other_owners[$i]['cMobileNum']; //7賣方代理人手機

                    $_t++;
                }
                unset($_other_owners);
                ##

                //賣方經紀人
                $other_phone = $this->get_phone(4, $pid);

                for ($i = 0; $i < count($other_phone); $i++) {
                    $_all[$_start + $_t]["mName"]   = $other_phone[$i]["cName"];
                    $_all[$_start + $_t]["mMobile"] = $other_phone[$i]['cMobileNum'];
                    $_t++;
                }

                unset($other_phone);
                // if ($_contract_data[0]["o_agent_mobile"] != "") {
                //     $_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ;             //賣方經紀人(1)姓名
                //     $_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;        //賣方經紀人(1)手機
                //     $_t ++ ;
                // }

                // if ($_contract_data[0]["o_agent_mobile2"] != "") {
                //     $_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ;         //賣方經紀人(2)姓名
                //     $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;    //賣方經紀人(2)手機
                //     $_t++;
                // }

                // if ($_contract_data[0]["o_agent_mobile3"] != "") {
                //     $_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;        //賣方經紀人(3)姓名
                //     $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;    //賣方經紀人(3)手機
                //     $_t++;
                // }

                // if ($_contract_data[0]["o_agent_mobile4"] != "") {
                //     $_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;        //賣方經紀人(4)姓名
                //     $_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;    //賣方經紀人(4)手機
                //     $_t++;
                // }
                ##
                //簡訊內容重整
                $buyer  = $_contract_data[0]["b_name"];
                $seller = $_contract_data[0]["o_name"];

                $memo = $fetchValue[0]["_title"] . $fetchValue[0]["eRemarkContent"];
                $M    = substr($fetchValue[0]["eTradeDate"], 3, 2);
                $M    = preg_replace("/^0/", "", $M);
                $D    = substr($fetchValue[0]["eTradeDate"], 5, 2);
                $D    = preg_replace("/^0/", "", $D);
                ##

                //是否多人買方
                $bCount = $this->getOhterBuyerOwner($bCount);
                ##

                //是否多人賣方
                $oCount = $this->getOhterBuyerOwner($oCount);
                ##

                $sms_txt = '第一建經信託履約保證專戶已於' . $M . '月' . $D . '日收到保證編號' . substr($pid, 5, 9) . '（買方' . $buyer . $bCount . '賣方' . $seller . $oCount . '）存入票據金額' . $money . '元,待票據兌現後再另行簡訊通知';

                //濾除重複簡訊對象並重新排序

                $_all = $this->filter_array($_all, $manager);

                $_total = count($_all);

                if ($mobile) {

                    $_all   = array_merge($_all, $manager);
                    $_all   = $this->filter_array($_all);
                    $_total = count($_all);

                    unset($manager);

                    for ($i = 0; $i < $_total; $i++) { //全部

                        for ($j = 0; $j < count($mobile); $j++) { //有勾選到的陣列
                            // echo $arr[$j]."_".$_all[$i]['mMobile']."<br>";
                            // echo $_all[$i]['mMobile']."<br>";

                            if ($mobile[$j] == $_all[$i]['mMobile']) { //勾選到的
                                //PUSH

                                $tmp[] = $_all[$i]; //寫入陣列

                            }
                        }
                    }
                    unset($_all);

                    // echo "<pre>";
                    // print_r($manager);
                    // echo "</pre>";

                    $_all = $tmp;

                    $sms_txt = $stxt;

                }

                if ($ok == 'y') {
                    $target = '手動' . $target;
                    for ($i = 0; $i < $_total; $i++) {
                        if (trim($_all[$i]["mMobile"]) != "") {
                            $check_word = array("-", "%", "_");
                            $mobile_tel = str_replace($check_word, "", $_all[$i]["mMobile"]);
                            if (preg_match('/^09[0-9]{8}$/', $mobile_tel)) {

                                $sms_id = $this->sms_send($mobile_tel, $_all[$i]["mName"], $sms_txt, $target, $pid, $tid, $sys, $sendname);

                                if ($sms_id == 's') {
                                    $_all[$i]['mMobile'] .= '、' . $this->return_code('s');
                                }
                                //發送成功(s)
                                else if ($sms_id == 'p') {
                                    $_all[$i]['mMobile'] .= '、' . $this->return_code('p');
                                }
                                //部份成功(p)
                                else {
                                    $_all[$i]['mMobile'] .= '、' . $this->return_code('f');
                                }
                                //發送失敗(f)
                            } else {
                                $_all[$i]['mMobile'] .= '、' . $this->return_code('n');
                            }
                            //門號錯誤(n)
                        }
                    }

                    //回覆簡訊發送結果
                    if (count($_boss) > 0) {
                        return array_merge($_all, $_boss);
                    } else {
                        $show['sms'] = $_all;
                        return $show;
                    }
                    ##
                } else {
                    // echo "<pre>";
                    // print_r($manager);
                    // echo "</pre>";

                    $show['txt'] = $sms_txt;
                    if (count($manager) > 0) {
                        if ($_all) {
                            $show['sms'] = array_merge($_all, $manager);
                        } else {
                            $show['sms'] = $manager;
                        }

                    } else {
                        $show['sms'] = $_all;
                    }

                    return $show;
                    // if (count($manager) > 0) {
                    //     return array_merge($_all,$manager) ;
                    // }else{
                    //     return $_all;
                    // }

                }

                break;

            default:
                # code...
                break;
        }

    }
    //

    private function writeLog($k, $smsTxt, $aryData, $pid, $tid, $_apol_id, $_tel = '')
    {

        $fs = '/home/httpd/html/first.twhg.com.tw/sms/log/' . date("Ymd") . ".log";
        $fp = fopen($fs, 'a+');
        fwrite($fp, "============[" . date("Y-m-d H:i:s") . "]===========================\n");
        fwrite($fp, "TARGET:" . $k . "\n");
        fwrite($fp, "SMS:" . $smsTxt . "\n");
        fwrite($fp, "DATA:" . $_tel . "\n");
        $_total = count($aryData);

        for ($i = 0; $i < $_total; $i++) {
            $_tmp = $aryData[$i]["mMobile"];
            if (preg_match("/$_tmp/", $_tel)) {
                $mobile      = $aryData[$i]["mMobile"];
                $mobile_name = $aryData[$i]["mName"];
                fwrite($fp, 'Mobile:' . $aryData[$i]["mName"] . "/" . $aryData[$i]["mMobile"] . "\n");
                break;
            }
            unset($_tmp);
        }

        //$_new_str = implode(",", $mobile);
        //$_new_str2 = implode(",", $mobile_name);
        $_new_str  = $_tel;
        $_new_str2 = $mobile_name;

        $sql = "INSERT INTO `tSMS_Log` (`tPID`, `tKind`, `tSMS`, `tTo`,`tName`,tTransId,tTID,sSend_Time ) VALUES ('" . substr($pid, 5, 9) . "', '$k', '$smsTxt', '$_new_str','$_new_str2','$tid','$_apol_id','" . date("Y-m-d H:i:s") . "')";
        fwrite($fp, $sql . "\n");

        fwrite($fp, "=============================================================\n");
        fclose($fp);
        //

        $this->DB_link->exec($sql);
        //
    }

    //新增簡訊 Log 資料至資料表
    private function writeDB($target, $smsTxt, $pid, $tid, $msg_id, $_tel = '', $mobile_name = '', $sendname)
    {
        $sql = 'INSERT INTO tSMS_Log (tPID,tKind,tSMS,tTo,tName,tTransId,tTID,tSendName,sSend_Time) VALUES ("' . substr($pid, 5, 9) . '","' . $target . '","' . $smsTxt . '","' . $_tel . '","' . $mobile_name . '","' . $tid . '","' . $msg_id . '","' . $sendname . '","' . date("Y-m-d H:i:s") . '") ;';
        //echo "writeDB SQL=".$sql."<br>\n" ;
        $this->DB_link->exec($sql);
    }
    ##

    //亞太電信版 sms_log 寫入
    private function writeLog_apol($k, $smsTxt, $pid, $tid, $_apol_id, $_tel = '', $mobile_name = '')
    {
        //echo "PID=".$pid ; exit ;
        $fs = $this->log_path . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');

        fwrite($fp, '============[' . date("Y-m-d H:i:s") . "]===========================\n");
        fwrite($fp, 'TARGET:' . $k . "\n");
        fwrite($fp, 'SMS:' . $smsTxt . "\n");
        fwrite($fp, 'Mobile:' . $_tel . "\n");
        fwrite($fp, 'DATA:' . $mobile_name . '/' . $_tel . "\n");
        fwrite($fp, "=============================================================\n");

        fclose($fp);
        //
    }
    ##

    //中華電信版 sms_log 寫入
    private function writeLog_cht($target, $smsTxt, $pid, $tid, $msg_id, $_tel = '', $mobile_name = '')
    {
        //$fs = '/home/httpd/html/first.twhg.com.tw/sms/log/cht_'.date("Ymd").".log" ;
        //$fs = '/home/httpd/html/first2.twhg.com.tw/sms/log/cht_'.date("Ymd").".log" ;
        $fs = $this->log_path . 'cht_' . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');

        fwrite($fp, '============[' . date("Y-m-d H:i:s") . "]===========================\n");
        fwrite($fp, 'TARGET:' . $target . "\n");
        fwrite($fp, 'SMS:' . $smsTxt . "\n");
        fwrite($fp, 'Mobile:' . $_tel . "\n");
        fwrite($fp, 'DATA:' . $mobile_name . '/' . $_tel . "\n");
        fwrite($fp, "=============================================================\n");

        fclose($fp);
        //
    }
    ##

    //遠傳電信版 sms_log 寫入
    private function writeLog_fet($target, $smsTxt, $pid, $tid, $msg_id, $_tel = '', $mobile_name = '')
    {
        $fs = $this->log_path . 'fet_' . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');

        fwrite($fp, '============[' . date("Y-m-d H:i:s") . "]===========================\n");
        fwrite($fp, 'TARGET:' . $target . "\n");
        fwrite($fp, 'SMS:' . $smsTxt . "\n");
        fwrite($fp, 'Mobile:' . $_tel . "\n");
        fwrite($fp, 'DATA:' . $mobile_name . '/' . $_tel . "\n");
        fwrite($fp, "=============================================================\n");

        fclose($fp);
        //
    }
    ##

    //取出回饋金簡訊對象
    private function getfeedbackmobile($bid)
    {
        $tmp        = array();
        $this->sql2 = "SELECT
						(SELECT bName FROM tBrand AS a WHERE a.bId=branch.bBrand) AS brand,
						branch.bStore,
						(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=b.bNID ) AS title,
						b.bName AS mName,
						b.bMobile AS mMobile,
						b.bBranch
					FROM
						tBranchFeedback  AS b
					LEFT JOIN
						tBranch AS branch ON branch.bId = b.bBranch

					WHERE
						b.bBranch  ='" . $bid . "'";
        // echo $this->sql2;

        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $tmp = $getMD2->fetchALL(PDO::FETCH_ASSOC);

        return $tmp;

    }

    //取出回饋金簡訊對象(scrivener)
    private function getfeedbackmobile2($bid)
    {
        $tmp        = array();
        $this->sql2 = "SELECT
						sId,
						sName AS mName,
						sMobileNum AS mMobile,
						sOffice AS bStore

	 				FROM
	 					tScrivener AS s
	 				WHERE
						sId ='" . $bid . "';
	 				";
        //             echo $this->sql2;

        // die;

        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $tmp = $getMD2->fetchALL(PDO::FETCH_ASSOC);

        // echo "<pre>";
        // print_r($tmp);
        // echo "</pre>";

        return $tmp;

    }

    //取出地政士簡訊接收對象
    private function getsScrivenerMobile($pid, $sid)
    {
        $aryTemp1 = array();
        $aryTemp2 = array();
        $_T       = array();

        $pid        = substr($pid, 5, 9);
        $this->sql2 = "SELECT  `cId`,  `cCertifiedId`,  `cScrivener`,  `cSmsTarget`,  `cAssistant`,  `cBankAccount`,  `cZip`,  `cAddress` FROM tContractScrivener WHERE cScrivener=$sid and cCertifiedId='$pid'";
        //echo $this->sql2."<br>";
        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $aryTemp2 = $getMD2->fetchALL(PDO::FETCH_ASSOC);
        $_T       = explode(",", $aryTemp2[0]['cSmsTarget']);
        $_sql_str = '';
        if (count($_T) > 0) {
            //$_sql_str = ' AND a.sNID IN ("'.join('","',$_T).'") ' ;
            $_sql_str = ' AND a.sMobile IN ("' . join('","', $_T) . '") ';
        }

        $this->execSQL = '
			SELECT
				a.sName as mName,
				a.sMobile as mMobile
			FROM
				tScrivenerSms AS a
			INNER JOIN
				tScrivener AS b ON a.sScrivener = b.sId
			INNER JOIN
				tTitle_SMS AS c ON a.sNID = c.id
			WHERE
				a.sScrivener = "' . $sid . '" AND a.sDel = 0 ' . $_sql_str . ';
		';
        //echo $this->execSQL."<br>";
        $getMD = $this->DB_link->prepare($this->execSQL);
        $getMD->execute();
        //echo $getMD->rowCount();
        $aryTemp1 = $getMD->fetchALL(PDO::FETCH_ASSOC);

        return $aryTemp1;
    }
    ##

    //取出地政士服務費簡訊接收對象
    private function getsScrivenerMobile2($pid, $sid)
    {
        $aryTemp1 = array();
        $aryTemp2 = array();
        $_T       = array();

        $pid        = substr($pid, 5, 9);
        $this->sql2 = "SELECT  `cId`,  `cCertifiedId`,  `cScrivener`,  `cSmsTarget`,  `cAssistant`,  `cBankAccount`,  `cZip`,  `cAddress` ,`cSend2` FROM tContractScrivener WHERE cScrivener=$sid and cCertifiedId='$pid'";
        //echo $this->sql2."<br>";
        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $aryTemp2 = $getMD2->fetchALL(PDO::FETCH_ASSOC);
        $_T       = explode(",", $aryTemp2[0]['cSend2']);
        $_sql_str = '';
        if (count($_T) > 0) {
            //$_sql_str = ' AND a.sNID IN ("'.join('","',$_T).'") ' ;
            $_sql_str = ' AND a.sMobile IN ("' . join('","', $_T) . '") ';
        }

        $this->execSQL = '
			SELECT
				a.sName as mName,
				a.sMobile as mMobile
			FROM
				tScrivenerSms AS a
			INNER JOIN
				tScrivener AS b ON a.sScrivener = b.sId
			INNER JOIN
				tTitle_SMS AS c ON a.sNID = c.id
			WHERE
				a.sScrivener = "' . $sid . '" AND a.sDel = 0 ' . $_sql_str . ';
		';
        //echo $this->execSQL."<br>";
        $getMD = $this->DB_link->prepare($this->execSQL);
        $getMD->execute();
        //echo $getMD->rowCount();
        $aryTemp1 = $getMD->fetchALL(PDO::FETCH_ASSOC);

        return $aryTemp1;
    }
    ##

    //取出店簡訊接收對象
    private function getsBranchMobile($pid, $bid, $title)
    {
        $aryTemp2 = array();
        $pid      = substr($pid, 5, 9);

        if ($title != '') {
            //取得合約書仲介順序
            $smsTarget     = array();
            $this->execSQL = 'SELECT * FROM tContractRealestate WHERE cCertifyId="' . $pid . '";';
            $getMD         = $this->DB_link->prepare($this->execSQL);
            $getMD->execute();

            if ($getMD->rowCount() > 0) {
                $rs = $getMD->fetchALL(PDO::FETCH_ASSOC);

                $v = $rs[0];
                unset($rs);

                for ($i = 0; $i < 3; $i++) {
                    $index = '';
                    if ($i > 0) {
                        $index = $i;
                    }

                    if ($v['cBranchNum' . $index] == $bid) { //若符合仲介店家編號
                        $smsTarget = explode(',', $v['cSmsTarget' . $index]); //取出合約書的仲介簡訊號碼
                        break;
                    }
                }
            }
            ##
            if (($title == '會計') || ($title == '秘書')) {
                $str = " AND a.bDefault = 1"; //只要預設對象
            }

            $this->sql2 = '
				SELECT
					a.bName as mName,
					a.bMobile as mMobile
				FROM
					tBranchSms AS a
				JOIN
					tTitle_SMS AS b ON b.id=a.bNID
				WHERE
					a.bBranch="' . $bid . '"
					AND b.tKind="0"
					AND b.tTitle = "' . $title . '"
					AND a.bCheck_id = 0
					AND a.bDel = 0
					' . $str . '
			';

            $getMD2 = $this->DB_link->prepare($this->sql2);
            $getMD2->execute();

            $arr = $getMD2->fetchALL(PDO::FETCH_ASSOC);

            $x = 0;
            for ($i = 0; $i < count($arr); $i++) {
                foreach ($smsTarget as $k => $v) {
                    if ($arr[$i]['mMobile'] == $v) {
                        $aryTemp2[$x]['mName']   = $arr[$i]['mName'];
                        $aryTemp2[$x]['mMobile'] = $arr[$i]['mMobile'];
                        $x++;
                    }
                }

            }

            //若輸入對象為會計或秘書，則需從基本資料中強制加入該身分的簡訊號碼
            if (($title == '會計') || ($title == '秘書')) {

                $aryTemp2 = array_merge($aryTemp2, $arr);
                unset($arr2);
            }

            ##
            //取得額外職稱
            if ($title == '店長') {
                $sql = "SELECT a.bName as mName, a.bMobile as mMobile FROM tBranchSms AS a JOIN tTitle_SMS AS b ON b.id=a.bNID WHERE a.bDel = 0 AND a.bBranch=" . $bid . " AND b.tCheck =1 AND a.bCheck_id =" . $pid;

                $getMD3 = $this->DB_link->prepare($sql);
                $getMD3->execute();

                $arr3 = $getMD3->fetchALL(PDO::FETCH_ASSOC);

                $x = 0;
                for ($i = 0; $i < count($arr3); $i++) {
                    foreach ($smsTarget as $k => $v) {
                        if ($arr3[$i]['mMobile'] == $v) {
                            $Temp[$x]['mName']   = $arr3[$i]['mName'];
                            $Temp[$x]['mMobile'] = $arr3[$i]['mMobile'];
                            $x++;
                        }
                    }
                }
                if (count($Temp) > 0) {
                    $aryTemp2 = array_merge($aryTemp2, $Temp);

                }

            }

            ##
        }

        return $aryTemp2;

    }
    ##
    private function checkBranch($pid, $no)
    {

        $pid = substr($pid, 5, 9);

        $sql = 'SELECT cBranchNum,cServiceTarget,cBranchNum1,cServiceTarget1,cBranchNum2,cServiceTarget2 as bid FROM tContractRealestate WHERE cCertifyId="' . $pid . '";';

        $getMD = $this->DB_link->prepare($sql);
        $getMD->execute();
        $data = $getMD->fetchALL(PDO::FETCH_ASSOC);

        if ($no == 1) {
            return $data[0]['cServiceTarget'];
        } elseif ($no == 2) {
            return $data[0]['cServiceTarget1'];
        } elseif ($no == 3) {
            return $data[0]['cServiceTarget2'];
        }
    }
    //取得保證號碼之第二組仲介
    private function getSecBranchMobile($pid)
    {
        // 取得第二家仲介店編號
        $_no = '';
        $pid = substr($pid, 5, 9); //取得保證號碼

        $this->sql2 = 'SELECT cBranchNum1 as bid FROM tContractRealestate WHERE cCertifyId="' . $pid . '";';

        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $_no = $getMD2->fetchALL(PDO::FETCH_ASSOC);
        return $_no[0]['bid'];
    }
    ##
    //取得主買賣其他電話 $_id 1買 2賣
    private function get_phone($_id, $pid)
    {
        $pid = substr($pid, 5, 9); //取得保證號碼

        $sql    = 'SELECT cMobileNum,cName  FROM tContractPhone  WHERE cCertifiedId ="' . $pid . '" AND cIdentity = "' . $_id . '";';
        $getMD2 = $this->DB_link->prepare($sql);
        $getMD2->execute();
        $arr = $getMD2->fetchALL(PDO::FETCH_ASSOC);

        return $arr;
    }
    ##
    //取得保證號碼之第三組仲介
    private function getThrBranchMobile($pid)
    {
        // 取得第三家仲介店編號
        $_no = '';
        $pid = substr($pid, 5, 9); //取得保證號碼

        $this->sql3 = 'SELECT cBranchNum2 as bid FROM tContractRealestate WHERE cCertifyId="' . $pid . '";';

        $getMD3 = $this->DB_link->prepare($this->sql3);
        $getMD3->execute();
        $_no = $getMD3->fetchALL(PDO::FETCH_ASSOC);
        return $_no[0]['bid'];
    }
    ##

    private function getContractData($pid)
    {
        /* 輸出內容
        Array
        (
        [0] => Array
        (
        [b_name] => 石玉光
        [b_mobile] => 0937990947
        [o_name] => 廖淳凱
        [o_mobile] => 0937132940
        [b_agent_name] =>
        [b_agent_mobile] =>
        [o_agent_name] =>
        [o_agent_mobile] =>
        )

        )
         */
        $aryTemp2 = array();
        $pid      = substr($pid, 5, 9);
        //$this->sql2 = "SELECT a.cName AS b_name, a.cMobileNum AS b_mobile, b.cName AS o_name,b.cMobileNum AS o_mobile,a.sAgentName1 AS b_agent_name,a.sAgentMobile1 AS b_agent_mobile,b.sAgentName1 AS o_agent_name,a.sAgentName2 AS b_agent_name2,a.sAgentMobile2 AS b_agent_mobile2,a.sAgentName3 AS b_agent_name3,a.sAgentMobile3 AS b_agent_mobile3,a.sAgentName4 AS b_agent_name4,a.sAgentMobile4 AS b_agent_mobile4,b.sAgentMobile1 AS o_agent_mobile,b.sAgentName2 AS o_agent_name2,b.sAgentMobile2 AS o_agent_mobile2,b.sAgentName3 AS o_agent_name3,b.sAgentMobile3 AS o_agent_mobile3,b.sAgentName4 AS o_agent_name4,b.sAgentMobile4 AS o_agent_mobile4 FROM tContractBuyer AS a INNER JOIN tContractOwner AS b ON a.cCertifiedId = b.cCertifiedId WHERE a.cCertifiedId = '$pid'";
        $this->sql2 = '
		SELECT
			a.cName AS b_name,
			a.cMobileNum AS b_mobile,
			a.sAgentName1 AS b_agent_name,
			a.sAgentMobile1 AS b_agent_mobile,
			a.sAgentName2 AS b_agent_name2,
			a.sAgentMobile2 AS b_agent_mobile2,
			a.sAgentName3 AS b_agent_name3,
			a.sAgentMobile3 AS b_agent_mobile3,
			a.sAgentName4 AS b_agent_name4,
			a.sAgentMobile4 AS b_agent_mobile4,
			b.cName AS o_name,
			b.cMobileNum AS o_mobile,
			b.sAgentName1 AS o_agent_name,
			b.sAgentMobile1 AS o_agent_mobile,
			b.sAgentName2 AS o_agent_name2,
			b.sAgentMobile2 AS o_agent_mobile2,
			b.sAgentName3 AS o_agent_name3,
			b.sAgentMobile3 AS o_agent_mobile3,
			b.sAgentName4 AS o_agent_name4,
			b.sAgentMobile4 AS o_agent_mobile4
		FROM
			tContractBuyer AS a
		INNER JOIN
			tContractOwner AS b ON a.cCertifiedId = b.cCertifiedId
		WHERE
			a.cCertifiedId = "' . $pid . '";
		';
        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $aryTemp2 = $getMD2->fetchALL(PDO::FETCH_ASSOC);
        return $aryTemp2;

    }

    //產生簡訊檢查碼
    private function genCheckCode($last_id, $mobile)
    {
        $sms_check_code = str_pad(($last_id + $mobile), 13, '0', STR_PAD_LEFT); //編碼原則：長度 最長 13 碼數字
        $n13            = substr($sms_check_code, 0, 1); //欄位順序：由右至左
        $n12            = substr($sms_check_code, 1, 1); //運算規則：
        $n11            = substr($sms_check_code, 2, 1); //=================================================
        $n10            = substr($sms_check_code, 3, 1); //奇數碼相加之總和(n1 + n3 + n5 + n7 + n9 + n11 + n13) = A
        $n9             = substr($sms_check_code, 4, 1); //偶數碼相加之總和(n2 + n4 + n6 + n8 + n10 + n12) = B
        $n8             = substr($sms_check_code, 5, 1); // A + (B x 6) = C
        $n7             = substr($sms_check_code, 6, 1); //取末三碼即為檢查碼(未滿三碼時左補零)
        $n6             = substr($sms_check_code, 7, 1);
        $n5             = substr($sms_check_code, 8, 1);
        $n4             = substr($sms_check_code, 9, 1);
        $n3             = substr($sms_check_code, 10, 1);
        $n2             = substr($sms_check_code, 11, 1);
        $n1             = substr($sms_check_code, 12, 1);

        $_odd  = $n1 + $n3 + $n5 + $n7 + $n9 + $n11 + $n13;
        $_even = ($n2 + $n4 + $n6 + $n8 + $n10 + $n12) * 6;

        $sms_check_code = $_odd + $_even;
        $sms_check_code = str_pad(substr($sms_check_code, -3), 3, '0', STR_PAD_LEFT);

        return $sms_check_code;
    }
    ##

    //遠傳電訊簡訊發送
    private function send_fet_sms($mobile, $mobile_name, $txt, $tg, $pid, $tid, $sendname)
    {
        $from_addr      = '0936019428'; //顯示的發話方號碼
        $url            = 'http://61.20.32.60:6600/mpushapi/smssubmit'; //遠傳API網址
        $fet_SysId      = $this->fet_SysId; //API帳號代號
        $fet_SrcAddress = $this->fet_SrcAddress; //發送訊息的來源位址(20個數字)
        $sms_str        = '';
        $_error_code    = '';

        //預設簡訊 ID
        $messageid = $msgid = 'Fake_' . uniqid();
        ##

        //登錄資料庫位置並取的 ID 以便進行簡訊驗證編碼運算
        $last_id = $this->sms_regist2DB($from_addr, $mobile);
        ##

        //運算產生簡訊檢查碼
        $sms_check_code = $this->genCheckCode($last_id, $mobile);
        ##

        //編輯傳送簡訊字串
        //$txt .= ' 訊息碼' . $sms_check_code ; //簡訊內容加上(簡訊檢查碼)
        $max_len = strlen(base64_encode($txt)); //計算簡訊長度(Base64加密後)

        $sms_str = '<?xml version="1.0" encoding="UTF-8"?>' .
        '<SmsSubmitReq>' .
        '<SysId>' . $fet_SysId . '</SysId>' .
        '<SrcAddress>' . $fet_SrcAddress . '</SrcAddress>' .
        '<DestAddress>' . $mobile . '</DestAddress>' .
        '<SmsBody>' . base64_encode($txt) . '</SmsBody>' .
            '<DrFlag>true</DrFlag>' .
            '</SmsSubmitReq>';
        ##

        //開始傳送簡訊、透過curl發送
        $url .= '?xml=' . urlencode($sms_str); //透過GET方式，傳送愈發送的簡訊資料
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl 錯誤!! Id:' . curl_errno($ch) . ', Error:' . curl_error($ch) . "\n";
            exit;
        }
        curl_close($ch);
        ##

        /*
        //假簡訊解果回傳
        $output = '<?xml version="1.0" encoding="UTF-8"?>
        <SubmitRes><ResultCode>00000</ResultCode><ResultText>Request successfully processed.</ResultText><MessageId>'.$messageid.'</MessageId></SubmitRes>' ;

        //$eee = $output ;
        //$eee = $url ;
        ##
         */

        //取出需求資料
        $output = str_replace("\n", "", $output);
        if (preg_match("/<SubmitRes><ResultCode>(.*)<\/ResultCode><ResultText>(.*)<\/ResultText>(.*)<\/SubmitRes>/", $output, $matches)) {
            $code        = trim($matches[1]); //結果代碼
            $description = trim($matches[2]); //結果說明

            if ($code == '00000') {
                preg_match("/<MessageId>(.*)<\/MessageId>/", $matches[3], $_id); //遠傳簡訊查詢ID
                $messageid = trim($_id[1]);
                $reason    = $_res    = '已發送';
                $_res .= ' [' . $messageid . ']';
                $_error_code = 's'; //發送成功、代碼"s"
            } else {
                $messageid = $msgid; //若本筆產生錯誤，則重新指定 message id

                $reason = '發送失敗';
                $_res   = $reason . ' -[ ' . $description . ' ]' . "\n";
                $_res .= '************ error messages ************' . "\n";
                $_res .= $txt . "\n";
                $_res .= '****************************************' . "\n";
                $_error_code = $code;
            }
        } else {
            //網路連線錯誤失敗
            $reason      = '無法建立網路連線';
            $_error_code = $code = '99999';
            ##
        }
        ##

        //寫入資料庫
        $this->sms_update2DB($last_id, $messageid, $code, $mobile, $from_addr, '2', $sms_check_code, $reason, $this->fet_sms_code($code), '');
        $this->writeDB($tg, $txt, $pid, $tid, $messageid, $mobile, $mobile_name, $sendname);
        ##

        //寫入Log
        $this->writeLog_fet($tg, $txt, $pid, $tid, $messageid, $mobile, $mobile_name);
        $this->smsLog_fet($_res, $mobile, $tg, $pid, $max_len, $txt);
        ##

        return $_error_code;
        //return 'A:'.$eee ;
    }
    ##

    //中華電信簡訊特碼(txt 字串須為Big5)
    private function send_cht_sms($mobile, $mobile_name, $txt, $tg, $pid, $tid)
    {
        $acc_china   = $this->acc_china; //中華電信帳號
        $pwd_china   = $this->pwd_china; //中華電信密碼
        $from_addr   = '0911510792'; //發話方電話號碼
        $max_ch      = 68; //最大簡訊文字數量
        $sms_success = 0; //發送成功簡訊數量
        $_error_code = ''; //簡訊錯誤碼

        //登錄資料庫位置並取的 ID 以便進行簡訊驗證編碼運算
        $last_id = $this->sms_regist2DB($from_addr, $mobile);
        ##

        //運算產生簡訊檢查碼
        $sms_check_code = $this->genCheckCode($last_id, $mobile);
        ##

        //修正簡訊文字與編碼格式
        //$txt .= ' 訊息碼' . $sms_check_code ; //於簡訊最後面加入簡訊檢核碼
        $txt_big5 = $this->n_to_w($txt); //將訊息中的半型數字轉為全型數字
        $txt_big5 = mb_convert_encoding($txt_big5, 'BIG5', 'UTF-8'); //將簡訊內容轉成Big-5編碼
        $max_len  = mb_strlen($txt_big5, 'big5'); //計算簡訊長度
        $_divid   = 1; //預設發送一則簡訊
        ##

        //若單封簡訊長度超長
        if ($max_len > $max_ch) {
            $_divid = ceil($max_len / $max_ch); //簡訊發送次數(單筆內容多筆發送)
        }
        ##

        //分批發送簡訊
        for ($i = 0; $i < $_divid; $i++) {
            $_start = $i * $max_ch;
            //echo "_start=$_start,i=$i,max_ch=$max_ch<br><br>\n" ;
            $_big5_str = mb_substr($txt_big5, $_start, $max_ch, 'big5');
            $_utf8_str = mb_convert_encoding($_big5_str, 'UTF-8', 'BIG5');
            //echo 'str='.mb_convert_encoding($_big5_str,'utf8','big5'),"<br><br>\n" ;
            $sms_success++;

            //https 版本
            $url = 'https://imsp.emome.net:4443/imsp/sms/servlet/SubmitSM'; //網址
            $url .= '?account=' . $acc_china . '&password=' . $pwd_china; //帳號密碼
            $url .= '&from_addr_type=0&from_addr=' . $from_addr; //發話方手機號碼
            $url .= '&to_addr_type=0&to_addr=' . $mobile; //發送至手機號碼
            $url .= '&msg_expire_time=0&msg_type=0'; //設定資料格式
            $url .= '&msg=' . urlencode($_big5_str); //發送內容
            ##

            //預設簡訊 ID
            $messageid = $msgid = 'Fake_' . uniqid();
            ##

            //開始發送簡訊
            $res = $this->file_get_contents_curl($url, 1); // 1 : https 連線方式、2 : http 連線方式
            //echo "RES=".$res."<br>\n" ;
            ##

            //假資料測試
            //$res = "<html>\n<header>\n</header>\n<body>\n".$mobile.'|0|'.$messageid."|Success<br>\n</body>\n</html>" ;
            ##

            //取得發送簡訊回傳之相關訊息
            $res = str_replace("\n", "", $res);
            if (preg_match("/<html><header><\/header><body>(.*)\|(.*)\|(.*)\|(.*)<br><\/body><\/html>/", $res, $_data)) { //連線正常取得回傳訊息
                $_tel        = trim($_data[1]); //收訊端手機號碼
                $code        = trim($_data[2]); //回傳代碼
                $messageid   = trim($_data[3]); //中華電信簡訊 ID
                $description = trim($_data[4]); //描述

                //if ($i == 1) { $code = 2 ; }    /////////////////////////////////////// 為了測試單筆多封簡訊 ////////////

                if ($code == '0') {
                    //$reason = $_res = '發送成功' ;
                    $reason = $_res = '已發送';
                    $_res .= ' [' . $messageid . ']';
                } else {
                    $messageid = $msgid; //若本筆產生錯誤，則重新指定 message id

                    $reason = '發送失敗';
                    $_res   = $reason . ' -[ ' . $url . ' ]' . "\n";
                    $_res .= '************ error messages ************' . "\n";
                    $_res .= $res . "\n";
                    $_res .= '****************************************' . "\n";

                    $_error_code = $code; //記錄錯誤代碼(最後一次錯誤...)
                    $sms_success--;
                }
            } else { //網路發生錯誤時之處置
                $_tel   = $mobile;
                $reason = '發送失敗';
                $_res   = $reason . ' -[ ' . $url . " ] - [ 網路連線錯誤!! ] \n";

                $_error_code = $code = '77'; //記錄錯誤代碼(網路錯誤 77 )
                $sms_success--;
            }
            ##

            //若中華電信加值簡訊遭拒則改用亞太發送簡訊
            //if ($code == '48') {    //確認亞太系統發送簡訊
            if (($code == '48') && (!preg_match("/^H/", $messageid))) { //確認亞太系統發送簡訊
                unset($_data);

                $returnAns = $this->send_apol_sms($mobile, $_utf8_str, $tg, $pid, $tid);
                //$returnAns = '<Response><Reason>開始發送1</Reason><Code>0</Code><MDN>0980013768</MDN><TaskID>'.$messageid.'</TaskID><RtnDateTime>20130506132520</RtnDateTime></Response>' ;
                if (preg_match("/<Reason>(.*)<\/Reason><Code>(.*)<\/Code><MDN>(.*)<\/MDN><TaskID>(.*)<\/TaskID><RtnDateTime>(.*)<\/RtnDateTime>/", $returnAns, $_data)) {
                    $apol_reason = $_data[1]; //代碼說明
                    $apol_code   = $_data[2]; //回傳代碼
                    $apol_mdn    = $_data[3]; //企業門號
                    $apol_tid    = $_data[4]; //交易代號
                    $apol_RDT    = $_data[5]; //平台回應時間
                } else {
                    $apol_code = '999999999'; //亞太系統發送失敗 code = 999999999(自行定義)
                }

                //若回傳成功(0)時，成功數量統計加 1
                if ($apol_code == '0') {
                    $sms_success++;
                }
                ##

                //若無企業門號則手動指定企業門號
                if (!$apol_mdn) {
                    $apol_mdn = '0980013768';
                }
                ##

                //若無交易代號則產生唯一鍵值取代亞太回傳碼
                if (!$apol_tid) {
                    $apol_tid = $messageid;
                }
                ##

                //若無平台回應時間則手動產生平台回應時間
                if (!$apol_RDT) {
                    $apol_RDT = date("YmdHis");
                }
                ##

                //依據代碼，取得亞太回傳結果
                $apol_reason = $this->apol_send_code($apol_code);
                ##
                //echo "tid=$apol_tid,reason=$apol_reason,code=$apol_code,mdn=$apol_mdn,RDT=$apol_RDT,mobile=$mobile<br>\n" ; exit ;
                //將簡訊發送完成紀錄寫到資料庫中
                //$last_id = $this->apol_sms_check_register($apol_tid,$apol_reason,$apol_code,$apol_mdn,$apol_RDT,$mobile) ;
                if ($i <= 0) {
                    $this->sms_update2DB($last_id, $apol_tid, $apol_code, $_tel, $apol_mdn, '3', $sms_check_code, $apol_reason, '', $apol_RDT);
                } else {
                    $last_id = $this->sms_regist2DB($apol_mdn, $mobile);
                    $this->sms_update2DB($last_id, $apol_tid, $apol_code, $_tel, $apol_mdn, '3', '', $apol_reason, '', $apol_RDT);
                }
                $this->writeDB($tg, $_utf8_str, $pid, $tid, $apol_tid, $mobile, $mobile_name);
                ##

                //寫入sms_log資料表
                $this->writeLog_apol($tg, $_utf8_str, $pid, $tid, $apol_tid, $mobile, $mobile_name);
                ##

                //產生簡訊Log
                //($_res=>簡訊發出狀態,$mobile=>接收端手機號碼,$tg=>發送對象,$pid=>保證號碼,$max_len=>簡訊長度,$_utf8_str=>簡訊本文)
                $this->smsLog_apol($returnAns, $mobile, $tg, $pid, mb_strlen($_utf8_str, 'utf-8'), $_utf8_str);
                ##
            }
            ##
            else { //中華系統發送簡訊
                //將簡訊發送完成紀錄寫到資料庫中
                //($messageid=>簡訊回傳碼,$reason=>簡訊發送狀態(伺服器錯誤),$_code=>回傳狀態碼,$_desc=>狀態描述,$from_addr=>發送端手機號碼,$_tel=>接收端手機號碼)
                if ($i <= 0) {
                    $this->sms_update2DB($last_id, $messageid, $code, $_tel, $from_addr, '1', $sms_check_code, $reason, $this->cht_sms_code($code), '');
                } else {
                    $last_id = $this->sms_regist2DB($from_addr, $mobile);
                    $this->sms_update2DB($last_id, $messageid, $code, $_tel, $from_addr, '1', '', $reason, $this->cht_sms_code($code), '');
                }
                $this->writeDB($tg, mb_convert_encoding($_big5_str, 'utf8', 'big5'), $pid, $tid, $messageid, $mobile, $mobile_name);
                ##
            }
            ##

            //寫入sms_log資料表
            //($target=>對象,$smsTxt=>簡訊內容,$pid=>保證號碼(tVR_Code),$tid=>Expense ID,$msg_id=>查詢用ID,$_tel=>接收端手機號碼,$mobile_name=>接收者姓名)
            $this->writeLog_cht($tg, mb_convert_encoding($_big5_str, 'utf8', 'big5'), $pid, $tid, $messageid, $mobile, $mobile_name);
            ##

            //產生簡訊Log
            //($_res=>簡訊發出狀態,$mobile=>接收端手機號碼,$tg=>發送對象,$pid=>保證號碼,$max_len=>簡訊長度,$txt=>簡訊本文)
            $this->smsLog_cht($_res, $mobile, $tg, $pid, $max_len, mb_convert_encoding($_big5_str, 'utf8', 'big5'));
            ##

        }
        ##

        //回傳簡訊發送結果
        if ($sms_success <= 0) {
            return $_error_code; //失敗
        } else if ($_divid == $sms_success) {
            return 's'; //成功
        } else {
            return 'p'; //部份成功
        }
        ##
    }
    ##

    // ---- APOL SMS MODULE
    private function send_apol_sms($mobile, $txt, $tg, $pid, $tid, $sys = 1)
    {
        $from_addr   = '0980013768';
        $sms_success = 0;
        $msg_id      = 'Fake_' . uniqid();

        //若為自主發送簡訊
        if ($sys != 1) {
            //登錄資料庫位置並取的 ID 以便進行簡訊驗證編碼運算
            $last_id = $this->sms_regist2DB($from_addr, $mobile);
            ##

            //運算產生簡訊檢查碼
            $sms_check_code = $this->genCheckCode($last_id, $mobile);
            ##

            //於簡訊最後面加入簡訊檢核碼並將半形文數字轉為全形
            //$txt .= ' 訊息碼' . $sms_check_code ;
            $txt = $this->n_to_w($txt);
            ##
        }
        ##

        //$pid = substr($pid,5,9) ;
        $_len     = mb_strlen($txt, "utf-8");
        $api_kind = "APIRTRequest";
        $url      = 'xsms.aptg.com.tw';

        $fp = fsockopen($url, 80, $errno, $errstr, 30);
        if (!$fp) {
            echo 'Could not open connection.';
            return 'error';
        } else {
            $xmlpacket = '<soap-env:Envelope xmlns:soap-env=\'http://schemas.xmlsoap.org/soap/envelope/\'>
			    <soap-env:Header/>
			    <soap-env:Body>
			        <Request>
			            <MDN>' . $from_addr . '</MDN>
			            <UID>' .$this->uid. '</UID>
			            <UPASS>' .$this->upass. '</UPASS>
			            <Subject>' . $tg . "_" . substr($pid, 5, 9) . '</Subject>
			            <Retry>Y</Retry>
			            <AutoSplit>Y</AutoSplit><Message>' . $txt . '</Message>
			            <MDNList><MSISDN>' . $mobile . '</MSISDN></MDNList>
			        </Request>
			    </soap-env:Body>
			</soap-env:Envelope>';
            $contentlength = strlen($xmlpacket);
            //echo "<pre>";
            //print_r($xmlpacket);

            $out = "POST /XSMSAP/api/" . $api_kind . " HTTP/1.1\r\n";
            $out .= "Host: 210.200.219.138\r\n";
            $out .= "Connection: close\r\n";
            $out .= "Content-type: text/xml;charset=utf-8\r\n";
            $out .= "Content-length: $contentlength\r\n\r\n";
            $out .= "$xmlpacket";

            fwrite($fp, $out);
            $theOutput = '';
            while (!feof($fp)) {
                $theOutput .= fgets($fp, 128);
            }

            fclose($fp);
            //echo $theOutput."\n";
            $res = $theOutput;
            //$res = '<Response><Reason>開始發送</Reason><Code>0</Code><MDN>'.$from_addr.'</MDN><TaskID>'.$msg_id.'</TaskID><RtnDateTime>20130506132520</RtnDateTime></Response>' ;

            //若為自主發送簡訊
            if ($sys != 1) {
                //取得回傳代碼
                if (preg_match("/<Reason>(.*)<\/Reason><Code>(.*)<\/Code><MDN>(.*)<\/MDN><TaskID>(.*)<\/TaskID><RtnDateTime>(.*)<\/RtnDateTime>/", $res, $_data)) {
                    $apol_reason = trim($_data[1]); //代碼說明
                    $apol_code   = trim($_data[2]); //回傳代碼
                    $apol_mdn    = trim($_data[3]); //企業門號
                    $apol_tid    = trim($_data[4]); //交易代號
                    $apol_RDT    = trim($_data[5]); //平台回應時間
                } else {
                    $apol_code = '999999999'; //亞太系統發送失敗 code = 999999999(自行定義)
                }
                ##

                //若回傳成功(0)時，成功數量統計加 1
                if ($apol_code == '0') {
                    $sms_success++;
                }
                ##

                //若無企業門號則手動指定企業門號
                if (!$apol_mdn) {
                    $apol_mdn = $from_addr;
                }
                ##

                //若無交易代號則產生唯一鍵值取代亞太回傳碼
                if (!$apol_tid) {
                    $apol_tid = $msg_id;
                }
                ##

                //若無平台回應時間則手動產生平台回應時間
                if (!$apol_RDT) {
                    $apol_RDT = date("YmdHis");
                }
                ##

                //依據代碼，取得亞太回傳結果
                $apol_reason = $this->apol_send_code($apol_code);
                ##
                //將簡訊發送完成紀錄寫到資料庫中
                //echo "tid=$apol_tid,reason=$apol_reason,code=$apol_code,mdn=$apol_mdn,RDT=$apol_RDT,mobile=$mobile<br>\n" ; exit ;
                $this->sms_update2DB($last_id, $apol_tid, $apol_code, $_tel, $apol_mdn, '3', $sms_check_code, $apol_reason, '', $apol_RDT);
                $this->writeDB($tg, $txt, $pid, $tid, $apol_tid, $mobile, $mobile_name);
                ##

                //寫入sms_log資料表
                $this->writeLog_apol($tg, $txt, $pid, $tid, $apol_tid, $mobile, $mobile_name);
                ##

                //產生簡訊Log
                //($_res=>簡訊發出狀態,$mobile=>接收端手機號碼,$tg=>發送對象,$pid=>保證號碼,$max_len=>簡訊長度,$_utf8_str=>簡訊本文)
                $this->smsLog_apol($res, $mobile, $tg, $pid, $_len, $txt);
                ##

                //回傳簡訊發送結果
                if ($sms_success <= 0) {
                    return $_error_code; //失敗
                } else {
                    return 's'; //成功
                }
                ##
            }
            ##
            else {
                return $res;
            }
        }
    }
    ##

    //
    private function send_sms($mobile, $name, $txt, $tg, $pid)
    {
        $pid = substr($pid, 5, 9);
        if ($mobile != "") { // 手機不為空值才會執行.
            $_len = mb_strlen($txt, "big5");
            if ($_len > 70) {
                $_t = ceil($_len / 70);
                for ($_i = 0; $_i < $_t; $_i++) {
                    $_start        = $_i * 70;
                    $_split_txt    = mb_substr($txt, $_start, 70, "big5");
                    $url           = 'https://smexpress.mitake.com.tw:8800/SmSendGet.asp?username=0921946427&password=first168&dstaddr=' . $mobile . '&DestName=' . $name . '&smbody=' . $_split_txt;
                    $_res          = $this->file_get_contents_curl($url);
                    $_res_utf8     = iconv("big5", "utf-8", $_res);
                    $_split_txt_u8 = iconv("big5", "utf-8", $_split_txt);
                    $this->smsLog($_res_utf8, $mobile, $name, $tg, $pid, $_len, $_split_txt_u8);

                }
            } else {
                $url = 'https://smexpress.mitake.com.tw:8800/SmSendGet.asp?username=0921946427&password=first168&dstaddr=' . $mobile . '&DestName=' . $name . '&smbody=' . $txt;
                //$_res = $this->file_get_contents_curl($url);
                $_res      = "------------";
                $_res_utf8 = iconv("big5", "utf-8", $_res);
                $_txt_utf8 = iconv("big5", "utf-8", $txt);
                $this->smsLog($_res_utf8, $mobile, $name, $tg, $pid, $_len, $_txt_utf8);
            }
            //var_dump($_res_utf8);
        }

    }

    //遠傳電信 Log 紀錄
    private function smsLog_fet($txtlog, $mobile, $tg_txt, $pid, $len, $smstxt)
    {
        $fs = $this->log_path . 'sms_fet_' . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');
        fwrite($fp, "===[" . $tg_txt . "]=[" . substr($pid, 5) . "]========[" . date("Y-m-d H:i:s") . "]===========[" . $len . "]=======CHT=========================\n");
        fwrite($fp, $mobile . "\n");
        fwrite($fp, $smstxt . "\n");
        fwrite($fp, $txtlog . "\n");
        fwrite($fp, "===============================================================================================================\n");
        fclose($fp);
    }
    ##

    //中華電信 Log 紀錄
    private function smsLog_cht($txtlog, $mobile, $tg_txt, $pid, $len, $smstxt)
    {
        $fs = $this->log_path . 'sms_cht_' . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');
        fwrite($fp, "===[" . $tg_txt . "]=[" . substr($pid, 5) . "]========[" . date("Y-m-d H:i:s") . "]===========[" . $len . "]=======CHT=========================\n");
        fwrite($fp, $mobile . "\n");
        fwrite($fp, $smstxt . "\n");
        fwrite($fp, $txtlog . "\n");
        fwrite($fp, "===============================================================================================================\n");
        fclose($fp);
    }
    ##

    //亞太電信 Log 紀錄
    private function smsLog_apol($txtlog, $mobile, $tg_txt, $pid, $len, $smstxt)
    {
        $fs = $this->log_path . 'sms_' . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');
        fwrite($fp, "===[" . $tg_txt . "]=[" . substr($pid, 5) . "]========[" . date("Y-m-d H:i:s") . "]===========[" . $len . "]=======APOL========================\n");
        fwrite($fp, $mobile . "\n");
        fwrite($fp, $smstxt . "\n");
        fwrite($fp, $txtlog . "\n");
        fwrite($fp, "===============================================================================================================\n");
        fclose($fp);
    }
    ##

    //
    private function smsLog($txtlog, $mobile, $name, $tg_txt, $pid, $len, $smstxt)
    {
        $fs = '/home/httpd/html/first.twhg.com.tw/sms/log/sms_' . date("Ymd") . ".log";
        $fp = fopen($fs, 'a+');
        fwrite($fp, "===[" . $tg_txt . "]=[" . $pid . "]========[" . date("Y-m-d H:i:s") . "]=[" . $mobile . " " . $name . "]==[" . $len . "]======\n");
        fwrite($fp, $smstxt . "\n");
        fwrite($fp, $txtlog . "\n");
        fwrite($fp, "===============================================================================================================\n");
        fclose($fp);
    }
    //
    private function file_get_contents_curl($url, $ver = 1)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);

        //選擇使用的 http 連線方式 1:https、2:http
        if ($ver == 1) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        ##

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        //var_dump($data);
        curl_close($ch);

        return $data;
    }
    //
    private function sms_check_register($_tid, $_reason = '', $_code, $_mdn = '', $_RDT = '', $_telNo)
    {
        if (preg_match("/伺服器錯誤/", $_reason)) {$_checked = 'y';} else { $_checked = 'n';}

        $sql        = 'INSERT INTO tSMS_Check (tTaskId,tChecked,tReason,tCode,tMDN,tMSISDN,tRtnDateTime) VALUES ("' . $_tid . '","' . $_checked . '","' . $_reason . '","' . $_code . '","' . $_mdn . '","' . $_telNo . '","' . $_RDT . '");';
        $insertData = $this->DB_link->prepare($sql);
        $insertData->execute();
    }

    //亞太電信簡訊發送紀錄
    private function apol_sms_check_register($_tid, $_reason = '', $_code, $_mdn = '', $_RDT = '', $_telNo)
    {
        if ($_code == '0') {$_checked = 'n';} else { $_checked = 'y';}

        $sql        = 'INSERT INTO tSMS_Check (tTaskID,tChecked,tReason,tCode,tMDN,tMSISDN,tRtnDateTime) VALUES ("' . $_tid . '","' . $_checked . '","' . $_reason . '","' . $_code . '","' . $_mdn . '","' . $_telNo . '","' . $_RDT . '");';
        $insertData = $this->DB_link->prepare($sql);
        $insertData->execute();
        return $this->DB_link->lastInsertId();
    }
    ##

    //中華電信簡訊發送紀錄
    private function cht_sms_check_register($_tid, $_reason = '', $_code, $_desc = '', $_mdn = '', $_telNo)
    {
        if (preg_match("/發送失敗/", $_reason)) {$_checked = 'y';} else { $_checked = 'n';}

        $sql        = 'INSERT INTO tSMS_Check (tTaskId,tChecked,tReason,tCode,tMDN,tMSISDN,tSystem) VALUES ("' . $_tid . '","' . $_checked . '","' . $_desc . '","' . $_code . '","' . $_mdn . '","' . $_telNo . '","1");';
        $insertData = $this->DB_link->prepare($sql);
        $insertData->execute();
        return $this->DB_link->lastInsertId();
    }
    ##

    //簡訊檢查登錄到資料庫中
    private function sms_regist2DB($_mdn = '', $_msisdn = '')
    {
        $sql        = 'INSERT INTO tSMS_Check (tChecked,tMDN,tMSISDN,tRegistTime) VALUES ("n","' . $_mdn . '","' . $_msisdn . '","' . date("Y-m-d H:i:s") . '");';
        $insertData = $this->DB_link->prepare($sql);
        $insertData->execute();

        return $this->DB_link->lastInsertId();
    }
    ##

    //簡訊檢查更新到資料庫中
    private function sms_update2DB($_lastId, $_tid, $_code, $_telNo, $_mdn, $_system, $sms_check_code, $_reason = '', $_desc = '', $_RDT = '')
    {
        if ($_code == '0') {$_checked = 'n';} else if ($_code == '00000') {$_checked = 'n';} else { $_checked = 'y';}

        $sql        = 'UPDATE tSMS_Check SET tTaskID="' . $_tid . '",tChecked="' . $_checked . '",tReason="' . $_reason . '",tCode="' . $_code . '",tMDN="' . $_mdn . '",tMSISDN="' . $_telNo . '",tRtnDateTime="' . $_RDT . '",tSystem="' . $_system . '",tCheckCode="' . $sms_check_code . '",tRegistTime="' . date("Y-m-d H:i:s") . '" WHERE id="' . $_lastId . '" ;';
        $insertData = $this->DB_link->prepare($sql);
        $insertData->execute();
    }
    ##

    //中華電信錯誤代碼解析
    private function cht_sms_code($no = 0)
    {
        // '77' 為網路錯誤所自行加入之錯誤碼
        $code_des = array(
            '0'  => '已發出、系統將開始發送簡訊',
            '2'  => '訊息傳送失敗',
            '3'  => '訊息預約時間超過48小時',
            '5'  => '訊息從Big-5轉碼到UCS失敗',
            '11' => '參數錯誤',
            '12' => '訊息的失效時間數值錯誤',
            '13' => 'SMS訊息的訊息種類不屬於合法的message type',
            '14' => '用戶具備改發訊息權限，請填發訊號碼',
            '15' => '簡訊號碼格式錯誤',
            '16' => '系統無法執行msisdn<->subno，請稍後再試',
            '17' => '系統無法找出對應此subno支電話號碼，請查明subno是否正確',
            '18' => '請檢查受訊方號碼格式是否正確',
            '19' => '受訊號碼數目超過系統限制(目前為20)',
            '20' => '訊息長度不正確',
            '22' => '帳號或是密碼錯誤',
            '23' => '你登入的IP未在系統註冊',
            '24' => '帳號已停用',
            '33' => '企業預付帳號沒金額，請儲值',
            '34' => '企業預付儲值系統發生介接錯誤，請洽服務人員',
            '35' => '抱歉、企業預付系統扣款錯誤、請再試',
            '36' => '抱歉、企業預付扣款系統鎖住，暫時無法使用、請再試',
            '37' => '企業預付扣款帳號鎖住，暫時無法使用(可能多條連線同時發訊所產生、請再重試)',
            '41' => '發訊內容含有系統不允許發送字集，請修改訊息內容再發訊',
            '43' => '這個受訊號碼是空號(此錯誤碼只會發生在限發CHT的用戶發訊時產生)',
            '44' => '無法判斷號碼是否屬於中華電信門號。無法決定費率，而停止發訊',
            '45' => '放心講客戶餘額不足、無法發訊',
            '46' => '無法決定計費客戶屬性、而停止服務',
            '47' => '該特碼帳號無法提供預付式客戶使用',
            '48' => '受訊客戶要求拒收加值簡訊、請不要重送',
            '49' => '顯示於手機之發訊號碼格式不對',
            '50' => '放心講系統扣款錯誤、請再試',
            '51' => '預付客戶餘額不足、無法發訊',
            '52' => '抱歉、預付式系統扣款錯誤、請再試',
            '77' => '網路連線錯誤、請連絡相關人員',
        );

        if ($no < 0) {
            return '中華電信系統或是資料庫故障';
        } else {
            return $code_des[$no];
        }
    }
    ##

    // 半形(narrow)、全形(wide)互換 -- 數字版
    private function n_to_w($strs, $types = '0')
    {
        $nt = array(
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        );
        $wt = array(
            "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        );

        if ($types == '0') {
            $strtmp = str_replace($nt, $wt, $strs); // narrow to wide (半形轉全形)
        } else {
            $strtmp = str_replace($wt, $nt, $strs); // wide to narrow (全形轉半形)
        }

        return $strtmp;
    }
    ##

    //簡訊發送結果碼
    private function return_code($ch)
    {
        switch ($ch) {
            case 's':
                $ans = '簡訊已發出';
                break;
            case 'p':
                $ans = '單筆多封簡訊部分發出!!明細請至簡訊明細查詢';
                break;
            case 'f':
                $ans = '簡訊失敗!!詳細內容請至簡訊明細查詢';
                break;
            case 'n':
                $ans = '門號格式錯誤';
                break;
            case 's1':
                $ans = '系統開始發送簡訊';
                break;
            case 'f1':
                $ans = '系統發送簡訊失敗！詳情請查詢簡訊明細...';
                break;
            case 'fn':
                $ans = '系統發送簡訊失敗(fn)！詳情請查詢簡訊明細...';
                break;
            case 'n1':
                $ans = '簡訊門號格式錯誤！';
                break;
            case 'u1':
                $ans = '系統開始發送部分簡訊！詳情請查詢簡訊明細...';
                break;
            default;
                $ans = '未知的錯誤';
                break;
        }
        return $ans;
    }
    ##

    //回覆簡訊發送結果(出款項目專用)
    private function out_sms_code($ss = 0, $pp = 0, $ff = 0, $nn = 0)
    {
        $aa = $ss + $pp + $ff + $nn; //計算所有簡訊發送總和

        if ($aa == 0) {
            return '找不到發送簡訊號碼!!';
        } else if ($ss == $aa) { //簡訊完全發送成功 (s1)
            return $this->return_code('s1');
        } else if ($ff == $aa) { //簡訊完全發送失敗 1 (f1)
            return $this->return_code('f1');
        } else if ($nn == $aa) { //簡訊號碼格式全部錯誤 (n1)
            return $this->return_code('n1');
        } else if (($ff + $nn) == $_a) { //簡訊完全發送失敗 2 (fn)
            return $this->return_code('fn');
        } else { //簡訊部分發送成功、部分失敗 (u1)
            return $this->return_code('u1');
        }
    }
    ##

    //取得其他買賣方資料 $_ide 2: 賣方、1: 買方、6買方代理人、7賣方代理人
    private function get_others($_pid, $_ide)
    {
        $_sql = '
			SELECT
				*
			FROM
				tContractOthers
			WHERE
				cCertifiedId="' . substr($_pid, 5) . '"
				AND cIdentity="' . $_ide . '"
		';

        $_getData = $this->DB_link->prepare($_sql);
        $_getData->execute();
        $_myData = $_getData->fetchALL(PDO::FETCH_ASSOC);

        $_returnArr = array();

        for ($i = 0; $i < count($_myData); $i++) {
            $_returnArr[$i]['cName']      = $_myData[$i]['cName'];
            $_returnArr[$i]['cMobileNum'] = $_myData[$i]['cMobileNum'];
        }

        //print_r($_returnArr) ;
        return $_returnArr;
        //return $_sql ;
    }
    ##
    private function getProperty($cid) //建物地址

    {

        $_sql = '
			SELECT
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=p.cZip) AS bCity,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=p.cZip) AS bArea,
				p.cAddr AS bAddr,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=l.cZip) AS lCity,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=l.cZip) AS lArea,
				l.cLand1 AS lAddr
			FROM
				tContractProperty AS p
			LEFT JOIN
				tContractLand AS l ON l.cCertifiedId=p.cCertifiedId
			WHERE
				p.cCertifiedId="' . $cid . '"
		';
        // echo "<br>".$_sql."<br>";

        $_getData = $this->DB_link->prepare($_sql);
        $_getData->execute();
        $_myData = $_getData->fetchALL(PDO::FETCH_ASSOC);

        // echo $_myData[0]['bAddr']."<bR>";
        if ($_myData[0]['bAddr'] != '') {
            preg_match("/(\D(.*)[路|街|段]{1})?(.*)?/isu", $_myData[0]['bAddr'], $arr);

            $addr = $_myData[0]['bCity'] . $_myData[0]['bArea'] . $arr[1];
        } else {

            $addr = $_myData[0]['lCity'] . $_myData[0]['lArea'] . $_myData[0]['lAddr'] . '段';
        }

        //print_r($_returnArr) ;
        return $addr;
        //return $_sql ;
    }
    private function getBankDate($tid)
    {
        $_sql = '
			SELECT
				tBankLoansDate
			FROM
				tBankTrans
			WHERE
				tId="' . $tid . '"
		';

        $_getData = $this->DB_link->prepare($_sql);
        $_getData->execute();
        $data = $_getData->fetchALL(PDO::FETCH_ASSOC);

        $tmp = explode('-', $data[0]['tBankLoansDate']);

        $date = $tmp[1] . "月" . $tmp[2] . "日";

        return $date;
    }
    //亞太簡訊傳送回傳代碼轉換表(發送)
    private function apol_send_code($code)
    {
        // '999999999' 為網路錯誤所自行加入之錯誤碼
        $code_list = array(
            '0'         => '已發出、系統將開始發送簡訊',
            '16777217'  => '認證失敗(用戶/企業代表號不存在或密碼錯誤)',
            '16777218'  => '來源IP未授權使用',
            '16777219'  => '指定帳號不存在(或空白)',
            '33554433'  => '額度不足(或合約已開通未儲值)',
            '33554434'  => '連線數超過上限',
            '33554435'  => '回撥門號未申請授權使用',
            '33554436'  => '國際簡訊未授權使用',
            '33554437'  => '國內簡訊未授權使用',
            '33554438'  => '國外簡訊未授權使用',
            '33554439'  => '合約已終止，停止使用',
            '33554440'  => '帳號已終止，停止使用',
            '33554441'  => '帳號已鎖定(密碼錯誤超過三次以上)',
            '33554448'  => '未授權此功能',
            '33554449'  => '他網國際漫遊簡訊客戶不得發送Unicode字碼',
            '50331649'  => '參數不足',
            '50331650'  => '交易代號不存在',
            '50331651'  => '門號格式錯誤',
            '50331652'  => '日期格式錯誤',
            '50331653'  => '其他格式錯誤',
            '50331654'  => '接收門號數量超過上限',
            '50331655'  => '簡訊本文含有非法關鍵字',
            '50331656'  => '簡訊長度過長',
            '50331657'  => '長簡訊則數已超過上限',
            '50331664'  => '簡訊主旨不存在(或空白)',
            '50331665'  => 'API簡訊發送起始時間需晚於API呼叫時間',
            '50331666'  => 'API簡訊發送結束時間',
            '50331667'  => '簡訊已全部送出，無法異動(刪除簡訊失敗/預約簡訊本文修改失敗)',
            '50331668'  => '變更密碼失敗(長度不足或過長)',
            '50331669'  => '異動點數長度不符',
            '50331670'  => '異動點數格式錯誤',
            '51450129'  => '系統維護時段，暫停使用',
            '286331153' => '例外錯誤',
            '999999999' => '網路連線錯誤、請連絡相關人員',
        );

        //print_r ($code_list[]) ; exit ;
        return $code_list[$code];
    }
    ##

    //亞太簡訊查詢代碼轉換表(查詢、TaskStatus)
    private function apol_return_code_TS($code)
    {
        $code_list = array(
            '00' => '上傳成功',
            '01' => '預約中',
            '11' => '系統正在處裡(展開明細中)',
            '12' => '系統已將簡訊送至簡訊中心',
            '21' => '使用者取消',
            '22' => '非法簡訊',
            '23' => '點數不足',
            '24' => '上傳失敗',
            '25' => '傳送失敗',
            '30' => '傳送失敗(展開明細失效)',
            '99' => '傳送完成',
        );

        return $code_list[$code];
    }
    ##

    //亞太簡訊查詢代碼轉換表(查詢、Status)
    private function apol_return_code_S($code)
    {
        $code_list = array(
            '99' => '成功',
            '21' => '使用者取消',
            '25' => '傳送失敗',
            '26' => '逾時失敗',
            '27' => '空號失敗',
            '28' => '傳送失敗(UNKNOWN)',
            '29' => '傳送失敗(REJECTD)',
            '30' => '傳送中(尚未得到簡訊狀態)',
        );

        return $code_list[$code];
    }
    ##

    //濾除重複簡訊對象並重新排序
    private function filter_array($a)
    {

        $count = count($a);

        for ($i = 0; $i < $count; $i++) {

            if ($a[$i]['mMobile'] != '') { //20150414 代理人空的太多 為了顯示出名子加上此判斷
                $b[$a[$i]['mMobile']]++;
            }

            if ($b[$a[$i]['mMobile']] > 1) {

                unset($a[$i]);

            }

        }

        $b = array_merge($a);

        return $b;
    }
    ##

    //判定買or賣方人數
    private function getOhterBuyerOwner($no)
    {
        if ($no > 1) {
            return '等' . $no . '人';
        } else {
            return '';
        }

    }
    ##

    //遠傳API發送回傳代碼
    private function fet_sms_code($code)
    {
        include_once 'sms_return_code_fet.php';

        return $return_code[$code];
    }
    ##

    //決定發送系統
    private function sms_send($mobile_tel, $mobile_name, $sms_txt, $target, $pid, $tid, $sys = 0, $sendname)
    {
        if ($sys == 3) { //其他(亞太電信)
            $sms_id = $this->send_apol_sms($mobile_tel, $sms_txt, $target, $pid, $tid, $sys);
        } else if ($sys == 1) { //中華電信
            $sms_id = $this->send_cht_sms($mobile_tel, $mobile_name, $sms_txt, $target, $pid, $tid);
        } else if ($sys == 2) { //遠傳電信
            $sms_id = $this->send_fet_sms($mobile_tel, $mobile_name, $sms_txt, $target, $pid, $tid, $sendname);
        } else {
            exit;
        }

        return $sms_id;
    }
    ##

    //取得指定之電信業者
    private function getSmsSystem()
    {
        $_sql = 'SELECT * FROM tSmsSystem WHERE sUsed="1" ORDER BY sId ASC LIMIT 1;';

        $_getData = $this->DB_link->prepare($_sql);
        $_getData->execute();
        $_myData = $_getData->fetchALL(PDO::FETCH_ASSOC);

        return ($_myData[0]['sSystemVendorCode']);
    }
    ##

}
