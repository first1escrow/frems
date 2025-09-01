<?php
require_once dirname(__DIR__) . '/class/slack.class.php';

use First1\V1\Notify\Slack;

function Audit($member_id, $certifiedId)
{
    global $conn;
    // echo 'a';
    $sql = 'SELECT pDep,pId FROM tPeopleInfo WHERE pId = "' . $member_id . '"';

    $rs         = $conn->Execute($sql);
    $checkSales = 1; // 業務
    $col        = '';

    if ($rs->fields['pDep'] == 4 || $member_id == 1 || $member_id == 12) {
        $checkSales = 2; //主管
    }

    // if ($member_id == 6 ) {
    //     $checkSales = 2;
    // }

    // print_r($certifiedId);
    // echo $checkSales;
    if (is_array($certifiedId)) {
        foreach ($certifiedId as $k => $v) {
            $str = '';
            if ($checkSales == 1) {
                $str = 'cStatus="1",cInspetor = "' . $member_id . '",cInspetorTime ="' . date('Y-m-d H:i:s') . '"';
                // echo "http://first.twhg.com.tw/includes/escrow/sendLineMessage.php?cId=".$v."&cat=2";

                //  file_get_contents("https://www.first1.com.tw/line/firstSales/includes/sendLineMessage.php?cId=".$v."&cat=2");

                $url  = 'https://www.first1.com.tw/line/firstSales/includes/sendLineMessage.php?cId=' . $v . '&cat=2';
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'GET',
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0
                ]);

                $response = curl_exec($curl);

                curl_close($curl);

                // die;
            } elseif ($checkSales == 2) {
                //如果主管自己審核自己的案件，則幫他直接審核通過
                $sql = "SELECT cId FROM tContractIncome WHERE cCertifiedId = '" . $v . "' AND cInspetor =''";
                $rs  = $conn->Execute($sql);

                if ($rs->fields['cId']) {
                    $str = 'cInspetor = "' . $member_id . '",cInspetorTime ="' . date('Y-m-d H:i:s') . '",';
                }
                $str .= 'cStatus="2",cInspetor2 = "' . $member_id . '",cInspetorTime2 ="' . date('Y-m-d H:i:s') . '"';
            }

            $sql = "SELECT cId FROM tContractIncome WHERE cCertifiedId = '" . $v . "' AND cInspetor2 =0 ";
            $rs  = $conn->Execute($sql);

            if (! $rs->EOF) {
                $sql = "UPDATE tContractIncome SET " . $str . " WHERE cCertifiedId = '" . $v . "'";
                // echo $sql."<br>";

                $conn->Execute($sql);
            }

        }

    } else {
        $str = '';
        if ($checkSales == 1) {
            $str = 'cStatus="1",cInspetor = "' . $member_id . '",cInspetorTime ="' . date('Y-m-d H:i:s') . '"';
            // file_get_contents("https://www.first1.com.tw/line/firstSales/includes/sendLineMessage.php?cId=".$certifiedId."&cat=2");

            $url  = 'https://www.first1.com.tw/line/firstSales/includes/sendLineMessage.php?cId=' . $certifiedId . '&cat=2';
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ]);

            $response = curl_exec($curl);

            curl_close($curl);

            if($response === false) {
                Slack::channelSend('履保未收足審核案件推送失敗' . $certifiedId);
            }
            
        } elseif ($checkSales == 2) {
            //如果主管自己審核自己的案件，則幫他直接審核通過
            $sql = "SELECT cId FROM tContractIncome WHERE cCertifiedId = '" . $certifiedId . "' AND cInspetor =''";
            $rs  = $conn->Execute($sql);

            if ($rs->fields['cId']) {
                $str = 'cInspetor = "' . $member_id . '",cInspetorTime ="' . date('Y-m-d H:i:s') . '",';
            }
            $str .= 'cStatus="2",cInspetor2 = "' . $member_id . '",cInspetorTime2 ="' . date('Y-m-d H:i:s') . '"';
        }

        $sql = "SELECT cId FROM tContractIncome WHERE cCertifiedId = '" . $certifiedId . "' AND cInspetor2 =0 ";
        $rs  = $conn->Execute($sql);
        if (! $rs->EOF) {
            $sql = "UPDATE tContractIncome SET " . $str . " WHERE cCertifiedId = '" . $certifiedId . "'";
            // echo $sql."<br>";
            $conn->Execute($sql);
        }

    }

}

// $member_id = ($sales['lpId'])?$sales['lpId']:$_SESSION['member_id'];
