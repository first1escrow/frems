<?php
require_once dirname(dirname(__FILE__)) . '/openadodb.php';
require_once dirname(dirname(__FILE__)) . '/libs/phpmailer/class.phpmailer1.php';
require_once dirname(dirname(__FILE__)) . '/SFTP/Net/SFTP.php';
//寫入檔案
function writeFH($_pp, $_data, $mode = 'w+')
{
    $fh = fopen($_pp, $mode);
    fwrite($fh, $_data);
    fclose($fh);

    chmod($_pp, 0666);
}
##

//開始傳真
function email_send($title, $adr, $msg)
{
    //設定郵件資訊並發送
    $mail = new PHPMailer();
    $mail->IsSMTP(); //使用SMTP發信
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "";
    $mail->Host       = '192.168.1.73'; //SMTP server
    $mail->Port       = 25;
    $mail->Username   = "www_sender";
    $mail->Password   = "!www_sender!";
    $mail->CharSet    = "utf-8"; //設定郵件編碼
    $mail->IsHTML(false); //設定郵件內容為HTML

    $mail->SetFrom('www_sender@twhg.com.tw', '第一建築經理股份有限公司自動發送系統'); //設定寄件者信箱
    $mail->AddReplyTo('www_sender@twhg.com.tw', '第一建築經理股份有限公司自動發送系統'); //設定回信信箱

    $mail->AddAddress($adr);

    //發送
    echo ' EMAIL[' . $adr . "] ... ";

    $mail->Subject = $title;
    $mail->Body    = $msg;
    // $mail->AddAttachment($attachment) ;        //增加附件

    // return true ;
    if ($mail->Send()) {
        return true;
    } else {
        return false;
    }

    ##
    ##
}
##

//取得待處理案件
function startProcess($id)
{
    global $conn;
    global $dir;
    global $msg;
    global $logDir;

    if (!empty($id)) {
        //取得待處理資訊
        $sql  = 'SELECT * FROM tAppInform WHERE id = "' . $id . '";';
        $rs   = $conn->Execute($sql);
        $list = array();
        if (!$rs->EOF) {
            if ($rs->fields['aProcessOK'] == 'N') {
                // echo date("Y-m-d H:i:s").' ' ;
                $msg .= date("Y-m-d H:i:s") . ' ';

                if (email_send($rs->fields['aTitle'], $rs->fields['aEmail'], base64_decode($rs->fields['aContent']))) {
                    $sql = 'UPDATE tAppInform SET aProcessOK = "Y" WHERE id = "' . $rs->fields['id'] . '";';
                    $conn->Execute($sql);

                    echo "完成";
                }
                // else echo "NG(2)" ;
                else {
                    echo "失敗";
                    $msg .= "失敗（無法寄出通知 " . $id . "）";
                }

                // echo "\n" ;
                $msg .= "\n";
                writeFH($logDir, $msg);
                // pushover('APP錯誤', $msg) ;
            } else {
                // echo date("Y-m-d H:i:s")."  已經處理過!!\n" ;
                $msg .= date("Y-m-d H:i:s") . "  已經處理過!!\n";
                writeFH($logDir, $msg);
                // pushover('APP錯誤', $msg) ;
            }
        } else {
            // echo date("Y-m-d H:i:s")."  無法取得待處理通知事項!!\n" ;
            $msg .= date("Y-m-d H:i:s") . "  無法取得待處理通知事項(" . $id . ")!!\n";
            writeFH($logDir, $msg);
            // pushover('APP錯誤', $msg) ;
        }
        // print_r($list) ; exit ;
        ##
    } else {
        $msg .= date("Y-m-d H:i:s") . "  錯誤的 ID !!\n";
        writeFH($logDir, $msg);
        // pushover('APP錯誤', $msg) ;
    }
}
##

//基本設定
$dir = dirname(__FILE__) . '/data';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
// $logDir = dirname(__FILE__).'/log/appInform1.log' ;
$logDir = dirname(__FILE__) . '/log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
$logDir .= '/appInform1.log';
##

//執行
$id  = '';
$msg = '';

if (preg_match("/^\d+$/", $_REQUEST['id'])) {
    $id = $_REQUEST['id'];
} else {
    $msg .= date("Y-m-d H:i:s") . "  錯誤的 ID !!\n";
    writeFH($logDir, $msg, 'a+');
    // pushover('APP錯誤', $msg) ;
    exit;
}

startProcess($id);
##
