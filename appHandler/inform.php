<?php
require_once dirname(dirname(__FILE__)) . '/openadodb.php';
require_once dirname(dirname(__FILE__)) . '/libs/phpmailer/class.phpmailer1.php';
require_once dirname(dirname(__FILE__)) . '/SFTP/Net/SFTP.php';

//開始傳真
function email_send($method, $title, $attachment)
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

    if ($method == '2') { //email
        $mail->AddAddress($title);

        //發送
        echo ' EMAIL[' . $title . "] ... ";

        $mail->Subject = '第一建經點交單';
        $mail->Body    = '第一建經點交單發送系統 ... 於 ' . date("Y-m-d H:i:s") . ' 發送';
        $mail->AddAttachment($attachment); //增加附件

        // return true ;
        if ($mail->Send()) {
            return true;
        } else {
            return false;
        }

        ##
    } else if ($method == '3') { //fax
        $mail->AddAddress('mail2fax@send.farfax.net');

        //發送
        $title = preg_replace("/\-/", "", $title);
        if (preg_match("/^\d+$/", $title)) {
            $title = 'jason.chen;2462;' . $title;
            echo ' FAX[' . $title . "] ... ";

            // $attachment = '/home/first1/app/fax/list_pdf.pdf' ;

            $mail->Subject = $title;
            // $mail->Subject = 'jason.chen;2462;0226577518;' ;
            // $mail->Subject = 'jason.chen;2462;0233229263;' ;
            $mail->Body = '';
            $mail->AddAttachment($attachment); //增加附件

            // return true ;
            if ($mail->Send()) {
                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }

        ##
    } else {
        return false;
    }

    ##
}
##

//取得遠端檔案
function getFile($filename)
{
    $host = '10.10.1.198';
    $usr  = 'twhg';
    $pwd  = 'twhG5008';

    $sftp = new Net_SFTP($host);
    if (!$sftp->login($usr, $pwd)) {
        echo "登入失敗!\n\n";
        return false;
    } else {
        $data = '';
        $data = $sftp->get($filename);

        if (!empty($data)) {
            saveFile(basename($filename), $data);
            $sftp->delete($filename);

            return true;
        } else {
            return false;
        }

    }
}
##

//存檔
function saveFile($filename, $data)
{
    global $dir;

    $fh = fopen($dir . '/' . $filename, 'w');
    fwrite($fh, $data);
    fclose($fh);

    chmod($dir . '/' . $filename, 0644);

    return true;
}
##

//刪除檔案
function deleteFile($filename)
{
    global $dir;
    $fh = $dir . '/' . $filename;

    if (is_file($fh)) {
        if (unlink($fh)) {
            return true;
        } else {
            return false;
        }

    } else {
        return false;
    }

}
##

//取得待處理案件
function startProcess()
{
    global $conn;
    global $dir;

    //取得待處理資訊
    $sql  = 'SELECT * FROM tAppSendMsg WHERE aProcessOK = "N" ORDER BY id ASC;';
    $rs   = $conn->Execute($sql);
    $list = array();
    while (!$rs->EOF) {
        echo date("Y-m-d H:i:s") . ' ';

        $list[] = $rs->fields;
        if (getFile($rs->fields['aPath'])) {
            if (email_send($rs->fields['aMethod'], $rs->fields['aTarget'], $dir . '/' . basename($rs->fields['aPath']))) {
                $sql = 'UPDATE tAppSendMsg SET aProcessOK = "Y" WHERE id = "' . $rs->fields['id'] . '";';
                $conn->Execute($sql);

                if (deleteFile(basename($rs->fields['aPath']))) {
                    echo "OK";
                } else {
                    echo "NG";
                }

            } else {
                echo "NG";
            }

        } else {
            echo "NG";
        }

        echo "\n";
        $rs->MoveNext();
    }
    // print_r($list) ; exit ;
    ##
}
##

//基本設定
$dir = dirname(__FILE__) . '/data';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
##

//執行
startProcess();
##
