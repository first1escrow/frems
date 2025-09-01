<?php
    include_once '../openadodb.php';

    $_POST = escapeStr($_POST);

    $cCertifiedId = $_POST['cId'];
    $cat          = $_POST['cat'];

    $sql = "SELECT * FROM tContractNote WHERE cCertifiedId = '" . $cCertifiedId . "' AND cCategory ='" . $cat . "' AND cDel = 0 ORDER BY cModify_Time ASC";

    $rs = $conn->Execute($sql);

    // 初始化 $list 為空陣列，避免未定義及 count() 的致命錯誤
    $list = [];

    while (! $rs->EOF) {
        $list[] = $rs->fields;
        $rs->MoveNext();
    }
?>

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="tb-title2 th_title_sml" colspan="5" width="80%">內容</td>
        <td class="tb-title2 th_title_sml">時間</td>
    </tr>
    <?php
    for ($i = 0; $i < count($list); $i++) {?>
    	<tr>
	        <td colspan="5" style="border:1px solid #CCC"><?php echo $list[$i]['cNote']?></td>
	        <td style="border:1px solid #CCC"><?php echo $list[$i]['cModify_Time']?></td>
	    </tr>
    <?php }

    ?>
</table>