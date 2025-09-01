<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
// include_once '/home/httpd/html/first2.twhg.com.tw/schedule/db/openadodb.php';
// include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

$id = $_POST['id'];

$sql = "SELECT qId,qSend,qSendIden FROM tQuestionaire WHERE qId = '".$_POST['id']."'";
$rs = $conn->Execute($sql);

$idenS = $idenR = $idenO = false;

// $token = enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$cId);
// $url = "http://www.first1.com.tw/line/question/Questionaire.php?v=".;
$SendIden = explode(',', $rs->fields['qSendIden']);
//1地政士2仲介3仲介業務  S=代書、R=仲介、O=建經業務、B=經紀人、T=測試用
foreach ($SendIden as $k => $v) {
    if ($v == 1) {
       $idenS = true;
    }elseif ($v == 2) {
       $idenR = true;
    }elseif ($v == 3) {
        $idenO = true;
    }
}


// print_r($lineSend);
// die;
if ($rs->fields['qSend'] == 0) {
    $i = 0;
    foreach ($lineSend as $k => $v) {
        echo $v['lLineId']."\r\n";
        $token = enCrypt('lineId='.$v['lLineId'].'&qId='.$id);
        $url = "http://www.first1.com.tw/line/question/Questionaire.php?v=".$token;
        echo $url."\r\n";

        $i++;
        


        
    }

}elseif ($rs->fields['qSend'] == 1) {
    $sDate = date('Y-m-d')." 00:00:00";
    $eDate = date('Y-m-d')." 23:59:59";

    $sDate = '2017-06-19 00:00:00';
    $eDate = '2017-06-19 23:59:59';

    $where = 'cEndDate >= "'.$sDate.'" AND cEndDate <= "'.$eDate.'"';
         
    //找尋結案案件
    $sql = "SELECT
                cc.cCertifiedId,
                cs.cSmsTarget AS scrivenerSms,
                cr.cBranchNum AS cBranchNum,
                CONCAT((Select bCode From `tBrand` c Where c.bId = cr.cBrand ),LPAD(cr.cBranchNum,5,'0')) as bCode,
                CONCAT((Select bCode From `tBrand` c Where c.bId = cr.cBrand1 ),LPAD(cr.cBranchNum1,5,'0')) as bCode1,
                CONCAT((Select bCode From `tBrand` c Where c.bId = cr.cBrand2 ),LPAD(cr.cBranchNum2,5,'0')) as bCode2
            FROM
                tContractCase AS cc
            LEFT JOIN
                tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
            LEFT JOIN
                tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
            WHERE ".$where;
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {

        if ($idenS) {
            $Arr = explode(',', $rs->fields['scrivenerSms']);
            foreach ($Arr as $k => $v) {
                $lineId = getLineToken('S',$v);
                if ($lineId) {
                    // $scrivener[$rs->fields['cCertifiedId']] =  enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$rs->fields['cCertifiedId']);
                    setQuestionaireAnswer($lineId,$rs->fields['cCertifiedId'],$id,enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$rs->fields['cCertifiedId']));

                }
            }
            unset($Arr);
        }

        if ($idenR) {
            if ($rs->fields['bCode']) {
                $lineId = getLineToken('R',$rs->fields['bCode']);
                if ($lineId) {
                    setQuestionaireAnswer($lineId,$rs->fields['cCertifiedId'],$id,enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$rs->fields['cCertifiedId']));
                }    
            }
            
            if ($rs->fields['bCode1']) {
                $lineId = getLineToken('R',$rs->fields['bCode1']);
                if ($lineId) {
                    setQuestionaireAnswer($lineId,$rs->fields['cCertifiedId'],$id,enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$rs->fields['cCertifiedId']));
                }
            }

            if ($rs->fields['bCode2']) {
                $lineId = getLineToken('R',$rs->fields['bCode2']);
                if ($lineId) {

                    setQuestionaireAnswer($lineId,$rs->fields['cCertifiedId'],$id,enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$rs->fields['cCertifiedId']));
                }
            }
        }
       
        $rs->MoveNext();
    }
   
   
   
}

function setQuestionaireAnswer($lId,$cId,$qId,$token){
    global $conn;

    $sql = "SELECT * FROM tQuestionaireAnswer WHERE qLineId = '".$lId."' AND qCertifiedId = '".$cId."' AND qQuestionId = '".$qId."'";
    $rs = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total == 0) {
        $sql = "INSERT INTO tQuestionaireAnswer SET qLineId = '".$lId."',qCertifiedId = '".$cId."',qQuestionId = '".$qId."'";
        $conn->Execute($sql);
        sendLineMessage($lId,$token,$cId);
    }

    
}
function sendLineMessage($lId,$token,$cId){

    $lId = 'U4b14569b842b0d5d4613b77b94af02b6';
    $token = '848a2ba9be8f8bdca5ff9bb02c94d8eb7006e692d1621a0dc77e6811f1f89ab5a5a2bb61fa11a47053c88fd37049164143017eda65164e9220466ea2';
    $url = "http://www.first1.com.tw/line/question/Questionaire.php?v=".$token;
   
    $data['lineId'] = $lId;
    $data['url'] = $url;
    $data['title'] ='滿意度調查';
    $data['text'] = '您的案件保證號碼:'.$cId.'已結案，麻煩您撥空填寫滿意度調查';
    $data['label'] = '開始填寫';

    $url = "https://firstbotnew.azurewebsites.net/bot/api/linePushBubble.php?v=".enCrypt(json_encode($data));
    file_get_contents($url);
    die;
   
}
function getLineToken($cat,$keyStr){
    global $conn;

    if ($cat == 'S') {
        $str = 'lIdentity = "'.$cat.'" AND lCaseMobile2 = "'.$keyStr.'"';
    }elseif ($cat == 'R') {
        $str = 'lIdentity = "'.$cat.'" AND lTargetCode = "'.$keyStr.'"';
    }elseif ($cat == 'B') {
        $str = 'lIdentity = "'.$cat.'" AND lCaseMobile2 = "'.$keyStr.'"';
    }
    $sql = "SELECT lLineId FROM tLineAccount WHERE ".$str;

    
    

    $rs = $conn->Execute($sql);

    return $rs->fields['lLineId'];
}
// $data['token'] = enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$cId);


//字串編碼
function enCrypt($str, $seed='first1app24602') {
    global $psiArr ;
    
    $encode = '' ;
    $rc = new Crypt_RC4 ;
    $rc->setKey($seed) ;
    $encode = $rc->encrypt($str) ;
    
    return $encode ;
}


// $data['lineId'] = 'U4b14569b842b0d5d4613b77b94af02b6';

// $data['txt'] = '問卷';

// $data['url'] = "http://www.first1.com.tw/line/question/Questionaire.php?v=".$_GET['v'];
// // $msg = 'https://firstbotnew.azurewebsites.net/';

// $str = json_encode($data);

// $url = "https://firstbotnew.azurewebsites.net/bot/api/linePushBubble.php?v=".$str;
    
?>