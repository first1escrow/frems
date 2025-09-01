<?php
require_once dirname(__DIR__) . '/.env.php';
require_once dirname(__DIR__) . '/rc4/crypt.php';
require_once dirname(__DIR__) . '/lineNotify.php';

header("Content-Type:text/html; charset=utf-8");

/**
 * @param pid    保證號碼
 * @param sid    地政士 id
 * @param bid    仲介店 id
 * @param target 類別
 * @param tid    入出帳 id
 * @param ok     是否發送簡訊 (y/n)
 * @param text   簡訊文字
 */

class SMS_Gateway_V2 extends PDO
{
    public $DB_link;
    public $execSQL;
    private $log_path;

    public function __construct($log_path = null)
    {
        global $env;

        $this->DB_link      = '';
        $this->execSQL      = '';
        $this->dbtype_sql   = $env['db']['197']['driver'];
        $this->host_sql     = $env['db']['197']['host'];
        $this->dbname_sql   = $env['db']['197']['database'];
        $this->username_sql = $env['db']['197']['username'];
        $this->password_sql = $env['db']['197']['password'];
        $this->log_path     = empty($log_path) ? dirname(__DIR__) . '/log/sms' : $log_path;

        $this->fet_SysId      = $env['sms']['fet']['fet_SysId'];
        $this->fet_SrcAddress = $env['sms']['fet']['fet_SrcAddress'];

        if (!is_dir($this->log_path)) {
            mkdir($this->log_path, 0777, true);
        }

        try {
            $this->DB_link = new PDO($this->dbtype_sql . ':host=' . $this->host_sql . ';dbname=' . $this->dbname_sql, $this->username_sql, $this->password_sql);
            // 資料庫使用 UTF8 編碼
            $this->DB_link->query('SET NAMES UTF8');
            // $this->DB_link->query('SET GLOBAL interactive_timeout = 120');
            // $this->DB_link->query('SET GLOBAL wait_timeout = 120');

            return $this;
        } catch (PDOException $e) {
            echo "DBconnectFalse: " . $e->getMessage();

            return "DBconnectFalse: " . $e->getMessage();
        }
    }

    //回饋金相關明細
    public function feedback($bid, $target, $text = null)
    {
        $_all = [];

        switch ($target) {
            case '回饋金':
                $tmp = explode(',', $bid);
                $a   = 0;

                for ($i = 0; $i < count($tmp); $i++) {
                    $code = strtoupper(substr($tmp[$i], 0, 2));
                    $id   = (int) substr($tmp[$i], 2);

                    //依據 code 取得地政士或仲介店簡訊對象
                    $tmp2 = ($code == 'SC') ? $this->getfeedbackmobile2($id, $text) : $this->getfeedbackmobile($id, $text);
                    ##

                    for ($j = 0; $j < count($tmp2); $j++) {
                        $_all[$a] = $tmp2[$j];
                        // $_all[$a]['smsTxt']  = $text;
                        $_all[$a]["mMobile"] = $this->regularMobile($_all[$a]["mMobile"]);

                        $a++;
                    }

                    $code = $id = $tmp2 = null;
                    unset($code, $id, $tmp2);
                }

                //濾除重複簡訊對象並重新排序
                $_all = $this->filter_array($_all);
                ##
                break;

            case '回饋金2':
                $tmp = explode(',', $bid);
                $a   = 0;

                for ($i = 0; $i < count($tmp); $i++) {
                    $code = strtoupper(substr($tmp[$i], 0, 2));
                    $id   = (int) substr($tmp[$i], 2);

                    //依據 code 取得地政士或仲介店簡訊對象
                    $tmp2 = ($code == 'SC') ? $this->getfeedbackmobile2($id, $text, 1) : $this->getfeedbackmobile($id, $text, 1);
                    ##

                    for ($j = 0; $j < count($tmp2); $j++) {
                        if (!empty($tmp2[$j]['mMobile'])) {
                            $_all[$a]            = $tmp2[$j];
                            $_all[$a]["mMobile"] = $this->regularMobile($_all[$a]["mMobile"]);

                            $jsonArr['mobile'] = $_all[$a]['mMobile'];
                            $jsonArr['code']   = $tmp[$i];
                            $jsonArr['Time']   = date('Ymd');
                            $url               = $this->getShortUrl('https://escrow.first1.com.tw/login/page-price1.php?v=' . $this->enCrypt(json_encode($jsonArr)), $this->enCrypt(json_encode($jsonArr)));

                            $_all[$a]['smsTxt'] .= $url;

                            file_put_contents($this->log_path . '/feedback_sms_' . date("Ymd") . '.log', date("Y-m-d H:i:s") . "\n" . print_r($_all[$a], true) . "\n", FILE_APPEND);
                            $a++;
                        }

                        $json_Arr = null;
                        unset($json_Arr);
                    }

                    $code = $id = $tmp2 = null;
                    unset($code, $id, $tmp2);
                }
                break;

            default:
                //
                break;
        }

        return $_all;
    }
    ##

    //決定發送系統
    public function sms_send($mobile_tel, $mobile_name, $sms_txt, $target = '', $pid = '', $tid = '', $sys = 2)
    {
        return $this->send_fet_sms($mobile_tel, $mobile_name, $sms_txt, $target, $pid, $tid);
    }
    ##

    //遠傳電訊簡訊發送
    private function send_fet_sms($mobile, $mobile_name, $txt, $tg, $pid, $tid)
    {
        $StartTime  = date('Y-m-d H:i:s');
        $StartTime2 = microtime(true);

        //遠傳 SMS API 參數設定
        $from_addr      = '0936019428'; //顯示的發話方號碼
        $url            = 'http://61.20.32.60:6600/mpushapi/smssubmit'; //遠傳 API 網址
        $fet_SysId      = $this->fet_SysId; //API 帳號代號
        $fet_SrcAddress = $this->fet_SrcAddress; //發送訊息的來源位址(20個數字)
        $sms_str        = '';
        $_error_code    = '';
        ##

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
            lineNotify('Curl 錯誤!! Id:' . curl_errno($ch) . ', Error:' . curl_error($ch));

            curl_close($ch);
            exit;
        }

        curl_close($ch);
        ##

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
        $this->writeDB($tg, $txt, $pid, $tid, $messageid, $mobile, $mobile_name);
        ##

        //寫入Log
        $this->writeLog_fet($tg, $txt, $pid, $tid, $messageid, $mobile, $mobile_name);
        $this->smsLog_fet($_res, $mobile, $tg, $pid, $max_len, $txt);
        ##

        $EndTime  = date('Y-m-d H:i:s');
        $EndTime2 = microtime(true);

        return $_error_code;
    }
    ##

    //取出回饋金簡訊對象(branch)
    public function getfeedbackmobile($bid, $text, $cat = null)
    {
        $tmp = array();

        if ($cat == 1) {
            $this->sql2 = "SELECT
		 					fs.fName AS mName,
		 					fs.fMobile AS mMobile,
		 					a.bName AS brand,
                            a.bCode AS code,
		 					(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=fs.fTitle ) AS title,
                            b.bId,
				            b.bStore
	 					FROM
	 						tFeedBackStoreSms AS fs
	 					LEFT JOIN
	 						tBranch AS b ON b.bId = fs.fStoreId
                        LEFT JOIN
                            tBrand AS a ON b.bBrand = a.bId
	 					WHERE
	 						fs.fType = 2 AND fs.fStoreId = '" . $bid . "' AND fs.fDelete = 0";
        } else {
            $this->sql2 = "SELECT
							a.bName AS brand,
                            a.bCode AS code,
							branch.bStore,
							(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=b.bNID ) AS title,
							b.bName AS mName,
							b.bMobile AS mMobile,
                            branch.bId,
							b.bBranch
						FROM
							tBranchFeedback  AS b
						LEFT JOIN
							tBranch AS branch ON branch.bId = b.bBranch
                        LEFT JOIN
                            tBrand AS a ON branch.bBrand = a.bId
						WHERE
							b.bBranch  ='" . $bid . "'";
        }

        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $rs = $getMD2->fetchALL(PDO::FETCH_ASSOC);

        foreach ($rs as $k => $v) {
            $rs[$k]['smsTxt'] = $this->feedbackFilter($bid, $text);
        }

        return $rs;
    }
    ##

    //取出回饋金簡訊對象(scrivener)
    public function getfeedbackmobile2($bid, $text, $cat = null)
    {
        $tmp = array();

        if ($cat == 1) {
            $this->sql2 = "SELECT
							fs.fName AS mName,
							fs.fMobile AS mMobile,
							(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=fs.fTitle ) AS title,
							s.sOffice AS bStore,
                            s.sId,
							s.sName
						FROM
							tFeedBackStoreSms AS fs
						LEFT JOIN
							tScrivener AS s ON s.sId=fs.fStoreId
						WHERE
							fs.fType = 1 AND fs.fStoreId = '" . $bid . "' AND fs.fDelete = 0";
        } else {
            $this->sql2 = "SELECT
							s.sId,
							sf.sName AS mName,
							sf.sMobile  AS mMobile,
							s.sOffice AS bStore,
							(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=sf.sNID ) AS title
						FROM
							tScrivenerFeedSms AS sf
						LEFT JOIN
							tScrivener AS s ON s.sId=sf.sScrivener
						WHERE
							s.sId ='" . $bid . "';";
        }

        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();
        $rs = $getMD2->fetchALL(PDO::FETCH_ASSOC);

        foreach ($rs as $k => $v) {
            $rs[$k]['brand']  = '地政士';
            $rs[$k]['code']   = 'SC';
            $rs[$k]['smsTxt'] = $this->feedbackFilter($bid, $text, 'SC');
        }

        return $rs;
    }
    ##

    //回饋金過濾特定字串
    private function feedbackFilter($id, $msg, $code = null)
    {
        $match = [];
        preg_match("/^(.*)\<first1\>.*\<\/first1\>(.*)$/iu", $msg, $match);

        if (!empty($match[1]) && !empty($match[2])) {
            if ($code == 'SC') {
                $data  = $this->getScrivenerName($id);
                $store = $this->filterWords($data['store'], '\(待停用\)');
            } else {
                $data  = $this->getBranchName($id);
                $store = $this->filterWords($data['brand'], '\(待停用\)') . $this->filterWords($data['store'], '\(待停用\)');
            }

            $msg = $match[1] . $store . $match[2] . "\r\n";

            $data = $store = null;
            unset($data, $store);
        }

        $code = $id = $match = null;
        unset($code, $id, $match);

        return $msg;
    }
    ##

    /**
     * 取得地政士事務所名稱
     * param integer target_id: 地政士編號
     */
    private function getScrivenerName($target_id)
    {
        $sql = '
            SELECT
                s.sOffice AS store
            FROM
                tFeedBackStoreSms AS fs
            LEFT JOIN
                tScrivener AS s ON s.sId=fs.fStoreId
            WHERE
                fs.fType = 1 AND fs.fStoreId = "' . $target_id . '" AND fs.fDelete = 0
        ;';

        $getMD = $this->DB_link->prepare($sql);
        $getMD->execute();

        return $getMD->fetch(PDO::FETCH_ASSOC);
    }
    ##

    /**
     * 取得仲介品牌與店名稱
     * param string target_type: 品牌代碼
     */
    private function getBranchName($target_id)
    {
        $sql = '
            SELECT
                (SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) AS brand,
                b.bStore as store
            FROM
                tFeedBackStoreSms AS fs
            LEFT JOIN
                tBranch AS b ON b.bId = fs.fStoreId
            WHERE
                fs.fType = 2 AND fs.fStoreId = "' . $target_id . '" AND fs.fDelete = 0
        ;';

        $getMD = $this->DB_link->prepare($sql);
        $getMD->execute();

        return $getMD->fetch(PDO::FETCH_ASSOC);
    }
    ##

    /**
     * 濾除(待停用)字樣
     * param string $text: 欲過濾字串
     * param string $filter: 需過濾的字詞
     */
    private function filterWords($text, $filter)
    {
        return preg_replace("/$filter/iu", '', $text);
    }
    ##

    //取得案件資訊
    public function getContractData($pid)
    {
        $aryTemp2 = array();
        $pid      = substr($pid, 5, 9);

        $this->sql2 = '
			SELECT
				cs.cScrivener,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cr.cBranchNum3,
				cr.cServiceTarget,
				cr.cServiceTarget1,
				cr.cServiceTarget2,
				cr.cServiceTarget3,
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
			LEFT JOIN
				tContractScrivener AS cs ON cs.cCertifiedId=a.cCertifiedId
			LEFT JOIN
				tContractRealestate AS cr ON cr.cCertifyId =a.cCertifiedId
			WHERE
				a.cCertifiedId = "' . $pid . '";
		';

        $getMD2 = $this->DB_link->prepare($this->sql2);
        $getMD2->execute();

        return $getMD2->fetchALL(PDO::FETCH_ASSOC);
    }
    ##

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

    //新增簡訊 Log 資料至資料表
    private function writeDB($target, $smsTxt, $pid, $tid, $msg_id, $_tel = '', $mobile_name = '')
    {
        $smsTxt = preg_replace("/\'+/", "", $smsTxt);
        $smsTxt = preg_replace("/\"+/", "", $smsTxt);

        $sql = 'INSERT INTO tSMS_Log (tPID,tKind,tSMS,tTo,tName,tTransId,tTID,sSend_Time) VALUES ("' . substr($pid, 5, 9) . '","' . $target . '","' . $smsTxt . '","' . $_tel . '","' . $mobile_name . '","' . $tid . '","' . $msg_id . '","' . date("Y-m-d H:i:s") . '");';
        $this->DB_link->exec($sql);
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
        if ($_code == '0') {
            $_checked = 'n';
        } else if ($_code == '00000') {
            $_checked = 'n';
        } else {
            $_checked = 'y';
        }

        $sql        = 'UPDATE tSMS_Check SET tTaskID="' . $_tid . '",tChecked="' . $_checked . '",tReason="' . $_reason . '",tCode="' . $_code . '",tMDN="' . $_mdn . '",tMSISDN="' . $_telNo . '",tRtnDateTime="' . $_RDT . '",tSystem="' . $_system . '",tCheckCode="' . $sms_check_code . '",tRegistTime="' . date("Y-m-d H:i:s") . '" WHERE id="' . $_lastId . '";';
        $insertData = $this->DB_link->prepare($sql);
        $insertData->execute();
    }
    ##

    //遠傳電信版 sms_log 寫入
    private function writeLog_fet($target, $smsTxt, $pid, $tid, $msg_id, $_tel = '', $mobile_name = '')
    {
        $fs = $this->log_path . '/fet_' . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');

        fwrite($fp, '============[' . date("Y-m-d H:i:s") . "]===========================\n");
        fwrite($fp, 'TARGET:' . $target . "\n");
        fwrite($fp, 'SMS:' . $smsTxt . "\n");
        fwrite($fp, 'Mobile:' . $_tel . "\n");
        fwrite($fp, 'DATA:' . $mobile_name . '/' . $_tel . "\n");
        fwrite($fp, "=============================================================\n");

        fclose($fp);
    }
    ##

    //遠傳電信 Log 紀錄
    private function smsLog_fet($txtlog, $mobile, $tg_txt, $pid, $len, $smstxt)
    {
        $fs = $this->log_path . '/sms_fet_' . date("Ymd") . '.log';
        $fp = fopen($fs, 'a+');
        fwrite($fp, "===[" . $tg_txt . "]=[" . substr($pid, 5) . "]========[" . date("Y-m-d H:i:s") . "]===========[" . $len . "]=======CHT=========================\n");
        fwrite($fp, $mobile . "\n");
        fwrite($fp, $smstxt . "\n");
        fwrite($fp, $txtlog . "\n");
        fwrite($fp, "===============================================================================================================\n");
        fclose($fp);
    }
    ##

    //簡訊發送結果碼
    private function return_code($ch)
    {
        switch ($ch) {
            case 's':
                return '簡訊已發出';
                break;

            case 'p':
                return '單筆多封簡訊部分發出!!明細請至簡訊明細查詢';
                break;

            case 'f':
                return '簡訊失敗!!詳細內容請至簡訊明細查詢';
                break;

            case 'n':
                return '門號格式錯誤';
                break;

            case 's1':
                return '系統開始發送簡訊';
                break;

            case 'f1':
                return '系統發送簡訊失敗！詳情請查詢簡訊明細...';
                break;

            case 'fn':
                return '系統發送簡訊失敗(fn)！詳情請查詢簡訊明細...';
                break;

            case 'n1':
                return '簡訊門號格式錯誤！';
                break;

            case 'u1':
                return '系統開始發送部分簡訊！詳情請查詢簡訊明細...';
                break;

            default:
                return '未知的錯誤';
                break;
        }
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

    //濾除重複簡訊對象並重新排序
    public function filter_array($a, $boss = null)
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
        if (is_array($boss)) {
            $b = $this->filter_array2($b, $boss);
        }

        return $b;
    }

    private function filter_array2($a, $b)
    {
        $count = count($a);
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < count($b); $j++) {
                if ($a[$i]['mMobile'] == $b[$j]['mMobile']) {
                    if ($a[$i]['mMobile'] != '') {
                        $tmp[$a[$i]['mMobile']]++;
                    }
                }

                if ($tmp[$a[$i]['mMobile']] > 0 || $a[$i]['mMobile'] == '') {
                    unset($a[$i]);
                }
            }
        }

        return array_merge($a);
    }
    ##

    //遠傳API發送回傳代碼
    private function fet_sms_code($code)
    {
        include_once 'sms_return_code_fet.php';

        return $return_code[$code];
    }
    ##

    //編碼
    private function enCrypt($str, $seed = 'firstfeedSms')
    {
        $rc = new Crypt_RC4;
        $rc->setKey($seed);

        return $rc->encrypt($str);
    }
    ##

    //取得官網回饋金登入短網址
    private function getShortUrl($url, $key)
    {
        $sql = "SELECT * FROM tShortUrl WHERE sCategory = '0' AND sKey = '" . $key . "'";

        $rs = $this->DB_link->prepare($sql);
        $rs->execute();

        $ShortUrlData = $rs->fetch(PDO::FETCH_ASSOC);

        if ($ShortUrlData['sShortUrl'] != '') {
            return $ShortUrlData['sShortUrl'];
        } else {
            $target = "https://escrow.first1.com.tw/url/url.php";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $target);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("url" => $url)));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($result, true);
            if ($data['code'] == 200) { //成功
                $sql = "INSERT INTO tShortUrl SET sCategory = '0',sKey = '" . $key . "',sUrl ='" . $url . "',sShortUrl = '" . $data['url'] . "'";
                $rs  = $this->DB_link->prepare($sql);
                $rs->execute();

                return $data['url'];
            } else { //失敗就走原本的
                return $url;
            }
        }
        // sleep(1);
        // return $url;
    }
    ##

    //濾除手機的分隔字元
    public function regularMobile($mobile)
    {
        $mobile = trim($mobile);

        if (!empty($mobile)) {
            $filter_word = ['-', '%', '_'];
            $mobile      = str_replace($filter_word, '', $mobile);
        }

        return $mobile;
    }
    ##

    //判定發送狀態並 +1
    private function resultCount($sms_id, &$_s, &$_p, &$_f)
    {
        switch ($sms_id) {
            case 's': //發送成功(s)
                $_s++;
                break;

            case 'p': //部份成功(p)
                $_p++;
                break;

            default: //發送失敗(f)
                $_f++;
                break;
        }
    }
    ##
}
