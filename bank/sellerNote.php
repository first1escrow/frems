<?php
include_once '../openadodb.php' ;

$sql = "SELECT tMemo FROM tBankTrans WHERE tOk = 2 GROUP BY tMemo";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $CertifiedId[] = $rs->fields['tMemo'];

    $rs->MoveNext();
}


if ($_POST) {

    if (is_array($CertifiedId)) {
       if (in_array($_POST['cId'], $CertifiedId)) {
            $sql = "UPDATE tBankTransSellerNote SET tAnother ='".@implode(',', $_POST['anotherseller'])."',tAnotherNote = '".$_POST['anothersellerNote']."' WHERE tCertifiedId = '".$_POST['cId']."'";
            
            if ($conn->Execute($sql)) {
                echo "<script>alert('修改成功')</script>";
            }else{
                echo "<script>alert('修改失敗，請稍後再試看看')</script>";
            }
       }else{
         // $msg = '已審核通過';
         echo "<script>alert('頁面資料過舊，此保證號碼已審核通過，禁止更改')</script>";
       }
    }else{
        echo "<script>alert('頁面資料過舊，此保證號碼已審核通過，禁止更改')</script>";
    }

   
}

$str = "'".implode("','", $CertifiedId)."'";


$sql= "SELECT * FROM tBankTransSellerNote WHERE eSend = 0 AND tCertifiedId IN (".$str.")";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $list[] = $rs->fields;

    $rs->MoveNext();
}



?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>賣方備註</title>
    <style>
        td {
            border: 1px #CCC solid;

        }
        th{
            width:300px;
            background-color:#E4BEB1;
            padding:4px;
            /**/

        }

    </style>
</head>
<body>

    <table cellpadding="0" cellspacing="0" width="100%">
        <?php
        for ($i=0; $i < count($list); $i++) { 
            $tmp = explode(',', $list[$i]["tAnother"]);
         ?>
            <form action="" method="POST">
            <tr>
                <th width="90%">保證號碼<?=$list[$i]['tCertifiedId']?><input type="hidden" name="cId" value="<?=$list[$i]['tCertifiedId']?>"></th>
                <td rowspan="2" align="center"><input type="submit" value="修改"></td>
            </tr>
            <tr>
                <td width="90%">
                    賣方本人非出款對象
                    <input type="checkbox" name="anotherseller[]" <?php if (in_array('1', $tmp)) { echo 'checked="checked"';}?> value="1"/>1.賣方匯第三人
                    <input type="checkbox" name="anotherseller[]" <?php if (in_array('2', $tmp)) { echo 'checked="checked"';}?> value="2"/>2.多數賣方指定匯其中一人或數人
                    <input type="checkbox" name="anotherseller[]" <?php if (in_array('3', $tmp)) { echo 'checked="checked"';}?> value="3"/>
                    3.代理人受領
                    <input type="checkbox" name="anotherseller[]" <?php if (in_array('4', $tmp)) { echo 'checked="checked"';}?> value="4"/>
                    4.代理人指定匯第三人帳戶
                    <input type="checkbox" name="anotherseller[]" <?php if (in_array('5', $tmp)) { echo 'checked="checked"';}?> value="5"/>
                    5.其他: <input type="text" name="anothersellerNote" style="width: 100px;" value="<?=$list[$i]['tAnotherNote']?>"/>
                </td>
            </tr>
            
            </form>
        <?php 
            unset($tmp);
            }
        ?>
        
    </table>
</body>
</html>