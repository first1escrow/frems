<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
// require_once dirname(__FILE__).'/rc4.php' ;
// include_once 'rc4.php';

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$id = empty($_GET['id']) ? $_POST['id'] : $_GET['id'];
$cat = empty($_GET['cat']) ? $_POST['cat'] : $_GET['cat']; //1add 2modify 3delete



if ($_POST['cat']) {
  
    $count = 0;
    for ($i=0; $i < count($_POST['question']); $i++) { 
        if ($_POST['question'][$i] != '') {
            $QuestionArr[$count]['question'] = $_POST['question'][$i];
            $QuestionArr[$count]['category'] = $_POST['qCategory'][$i];

            if (is_array($_POST['itmeQ'.($i+1)])) {
                foreach ($_POST['itmeQ'.($i+1)] as $k => $v) {
                    if ($v) {
                        $QuestionArr[$count]['item'][] = $v;
                    }
                }
            }

           

            if (is_array($_POST['itemQ'.($i+1).'Score'])) {
                foreach ($_POST['itemQ'.($i+1).'Score'] as $k => $v) {
                    if ($v) {
                        $QuestionArr[$count]['itemScore'][] = $v;
                    }
                }
            }
           
            $count++;
        }
       
    }

  

    $str = base64_encode(json_encode($QuestionArr));

    ##
    $sDate = (substr($_POST['sDate'], 0,3)+1911)."-".substr($_POST['sDate'], 4)." 00:00:00";
    $eDate = (substr($_POST['eDate'], 0,3)+1911)."-".substr($_POST['eDate'], 4)." 23:59:59";
    $SendIden = ($_POST['sendIden'] == '')? '':@implode(',', $_POST['sendIden']);
    // echo print_r($_POST[''])
    // die;
    if ($_POST['cat'] == 1) {
      
        $sql = "INSERT INTO
                    tQuestionaire
                SET
                    qName = '".$_POST['name']."',
                    qDateStart = '".$sDate."',
                    qDateEnd = '".$eDate."',
                    qContent = '".$str."',
                    qSend = '".$_POST['sendMethod']."',
                    qCreator = '".$_SESSION['member_id']."',
                    qSendIden = '".$SendIden."',
                    qCreatTime = '".date('Y-m-d H:i:s')."'
               ";
        // echo $sql;
        $conn->Execute($sql);

        $_POST['cat'] = 2;

    }else if ($_POST['cat'] == 2) { //修改

		 $sql = "UPDATE
                    tQuestionaire
                SET
                    qName = '".$_POST['name']."',
                    qDateStart = '".$sDate."',
                    qDateEnd = '".$eDate."',
                    qContent = '".$str."',
                    qSend = '".$_POST['sendMethod']."',
                    qSendIden = '".$SendIden."',
                    qEditor = '".$_SESSION['member_id']."',
                    qEditeTime = '".date('Y-m-d H:i:s')."'
               ";
        // echo $sql;
        $conn->Execute($sql);
	}

    unset($str);
    // echo "<prE>";
    // print_r($_POST);

    
	// echo $sql;
}
##
$lineId = 'U4b14569b842b0d5d4613b77b94af02b6';
$cId = '000000001';
##

$today = (date('Y')-1911)."-".date('m')."-".date('d');
$sql = "SELECT qId,qName,qDateStart,qDateEnd,qContent,qSend,qSendIden FROM tQuestionaire WHERE qId = '".$id."'";
$rs = $conn->Execute($sql);

$data = $rs->fields;

$data['qDateStart'] =  ($data['qDateStart'] == '' || $data['qDateStart'] == '0000-00-00 00:00:00')?$today:(substr($data['qDateStart'], 0,4)-1911)."-".substr($data['qDateStart'], 5,2)."-".substr($data['qDateStart'], 8,2);

$data['qDateEnd'] = ($data['qDateEnd'] == '' || $data['qDateEnd'] == '0000-00-00 00:00:00')?$today:(substr($data['qDateEnd'], 0,4)-1911)."-".substr($data['qDateEnd'], 5,2)."-".substr($data['qDateEnd'], 8,2); 
$data['qContent'] = json_decode(base64_decode($data['qContent']),true);

// echo "<pre>";
// print_r($data['qContent']);
// echo "</pre>";
$data['token'] = enCrypt('lineId='.$lineId.'&qId='.$id.'&cId='.$cId);

$data['qSend'] = ($data['qSend']=='')? 0 :$data['qSend'];
$data['qSendIden'] = ($data['qSendIden']=='')?array(1,2,3):explode(',', $data['qSendIden']);


###
//字串編碼
function enCrypt($str, $seed='first1app24602') {
    global $psiArr ;
    
    $encode = '' ;
    $rc = new Crypt_RC4 ;
    $rc->setKey($seed) ;
    $encode = $rc->encrypt($str) ;
    
    return $encode ;
}
###
$smarty->assign('sendIdenMenu',array(1=>'地政士',2=>'仲介',3=>'仲介業務')); //1地政2仲介
$smarty->assign('sendMenu',array('0'=>'直接發送','1'=>'當案件結案就發送'));
$smarty->assign('data',$data);
$smarty->assign('cat',$cat);
$smarty->display('QuestionEdit.inc.tpl', '', 'line');
?>
