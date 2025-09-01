<?php
class Intolog
{
    public function writelog($act = '', $txt = '')
    {
        if ($act) {
            $path = $GLOBALS['FILE_PATH'] . 'log';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $usrName    = $_SESSION['member_name']; //�n�J�m�W
            $usrAccount = $_SESSION['member_acc']; //�n�J�b��
            $usrIP      = ''; //�n�JIP
            $usrDate    = date("YmdHis"); //�n�J���

            //���o�ϥΪ̯u��IP
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $usrIP = $_SERVER['HTTP_CLIENT_IP'];
            } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $usrIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $usrIP = $_SERVER['REMOTE_ADDR'];
            }
            ##

            //�g�Jlog
            $fh   = fopen($path . '/' . $act . '.log', 'a+');
            $line = $usrName . ',' . $usrAccount . ',' . $usrIP . ',' . $usrDate;
            if ($txt) {
                $line .= ',' . $txt;
            }

            fwrite($fh, $line . "\r\n");
            fclose($fh);
            ##

            return true;
        } else {
            return false;
        }
    }
}
