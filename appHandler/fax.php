<?php
require_once dirname(dirname(__FILE__)) . '/openadodb.php';
require_once dirname(dirname(__FILE__)) . '/libs/phpmailer/class.phpmailer1.php';
require_once dirname(dirname(__FILE__)) . '/SFTP/Net/SFTP.php';
// die;
//寫入檔案
function writeFH($_pp, $_data, $mode = 'w+')
{
    $fh = fopen($_pp, $mode);
    fwrite($fh, $_data);
    fclose($fh);

    // chmod($_pp, 0666) ;
}

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
    $host = '10.10.1.150';
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

function getFile2($filename)
{

    global $dir;

    $uploadfile = $dir . '/' . basename($filename);
    // echo $filename."<bR>";
    // echo $uploadfile."<br>";

    if (is_file($filename)) {
        // echo 'GO';
        if (rename($filename, $uploadfile)) {
            return true;
        } else {
            return false;
        }

    } else {
        return false;
    }

    // rename($filename,$uploadfile);
    // die;
    // copy($filename,$uploadfile);

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
function startProcess($id)
{
    global $conn;
    global $dir;
    global $msg;
    global $logDir;

    if (!empty($id)) {
        //取得待處理資訊
        // $sql = 'SELECT * FROM tAppSendMsg WHERE aProcessOK = "N" ORDER BY id ASC;' ;
        $sql  = 'SELECT * FROM tAppSendMsg WHERE id = "' . $id . '";';
        $rs   = $conn->Execute($sql);
        $list = array();
        if (!$rs->EOF) {
            if ($rs->fields['aProcessOK'] == 'N') {
                // echo date("Y-m-d H:i:s").' ' ;
                $msg .= date("Y-m-d H:i:s") . ' ';

                $list = $rs->fields;
                if (getFile2($rs->fields['aPath'])) {
                    if (email_send($rs->fields['aMethod'], $rs->fields['aTarget'], $dir . '/' . basename($rs->fields['aPath']))) {
                        $sql = 'UPDATE tAppSendMsg SET aProcessOK = "Y" WHERE id = "' . $rs->fields['id'] . '";';
                        $conn->Execute($sql);

                        if (deleteFile(basename($rs->fields['aPath']))) {
                            echo "完成";
                        }

                        // else echo "NG(1)" ;
                        else {
                            echo "失敗";
                            $msg .= "失敗（無法刪除已下載檔案）";
                        }
                    }
                    // else echo "NG(2)" ;
                    else {
                        echo "失敗";
                        $msg .= "失敗（無法寄出下載檔案 " . getFile2($rs->fields['aPath']) . "）";
                    }
                }
                // else echo "NG(3)" ;
                else {
                    $msg .= "失敗（無法下載檔案 " . $rs->fields['aPath'] . "）";
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
            // echo date("Y-m-d H:i:s")."  無法取得待處理事項!!\n" ;
            $msg .= date("Y-m-d H:i:s") . "  無法取得待處理事項!!\n";
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

$logDir = dirname(__FILE__) . '/log/app.log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
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
