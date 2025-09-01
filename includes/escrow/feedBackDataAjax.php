<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(__DIR__) . '/maintain/feedBackData.php';

$_POST = escapeStr($_POST) ;
$scrivenerId = $_POST['sId'];


$sql = 'SELECT fId, fAccountName, fAccountNum, fAccountNumB, fAccount FROM tFeedBackData WHERE fType = 1 and fStatus = 0 and fStop = 0 and fStoreId = "'.$scrivenerId.'";';
$rs = $conn->Execute($sql);
if($rs->RecordCount() == 1) {
    echo '';
    exit();
}

 $record = '
                <tr>
                    <td colspan="6" class="tb-title">選擇其他地政士回饋帳戶(若有2個以上回饋帳戶需選擇)</td>
                </tr>
            ';
while (!$rs->EOF) {
    $record .=' <tr>
                    <td colspan="6" ><input type="radio" name="fOtherFeedbackDataId" value="'.$rs->fields['fId'].'">戶名：'.$rs->fields['fAccountName'].'、帳號：'.$rs->fields['fAccountNum'].$rs->fields['fAccountNumB'].'-'.$rs->fields['fAccount'].'</td>
               </tr>';
    $rs->MoveNext();
}
echo $record;
?>