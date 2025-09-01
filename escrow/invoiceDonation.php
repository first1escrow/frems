<?php
    require_once dirname(__DIR__) . '/first1DB.php';
    require_once dirname(__DIR__) . '/session_check.php';

    $alert = '';

    $identify_id = isset($_GET['identify_id']) ? $_GET['identify_id'] : '';
    $target      = empty($_GET['target']) ? '' : $_GET['target'];

    $conn = new first1DB;
    // print_r($conn);exit;

    if (isset($_POST['identify_id'], $_POST['target'], $_POST['donation']) && $_POST['identify_id'] && $_POST['target'] && $_POST['donation']) {
        //儲存發票捐贈單位
        $sql = 'INSERT INTO
                tInvoiceDonationDefault
            (
                iIdentifyId,
                iDonationCode,
                iSettingFrom,
                iCreatedAt
            ) VALUES (
                :id,
                :donation,
                :settingFrom,
                NOW()
            );';
        $conn->exeSql($sql, [
            'id'          => isset($_POST['identify_id']) ? $_POST['identify_id'] : '',
            'donation'    => isset($_POST['donation']) ? $_POST['donation'] : '',
            'settingFrom' => 'S',
        ]);

        $alert = 'alert("設定完成");';

        $identify_id = isset($_POST['identify_id']) ? $_POST['identify_id'] : '';
        $target      = isset($_POST['target']) ? $_POST['target'] : '';
        $donation    = isset($_POST['donation']) ? $_POST['donation'] : '';

    }

    if (! preg_match("/^\w+$/", $identify_id)) {
        exit('未知的身分證號');
    }

    if (empty($target) || ! in_array($target, ['owner', 'buyer', 'other_owner', 'other_buyer'])) {
        exit('無法確認對象身分');
    }

    //是否有預設愛心碼
    $sql = 'SELECT iDonationCode FROM tInvoiceDonationDefault WHERE iIdentifyId = :identify_id ORDER BY iCreatedAt DESC LIMIT 1';
    $rs  = $conn->one($sql, ['identify_id' => $identify_id]);

    $default_donation = empty($rs) ? '8585' : $rs['iDonationCode']; //若查無預設愛心碼，則預設為 8585(財團法人台灣兒童暨家庭扶助基金會)
                                                                    // echo $default_donation;

    //
    $sql = 'SELECT iCode, iAlias, iName FROM tInvoiceDonationCode ORDER BY iName ASC';
    $rs  = $conn->all($sql);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <title>捐贈發票設定</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script src="/js/1.13.3/combobox.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script>
    <?php echo $alert ?>

    $(document).ready(function() {
        $("#combobox").combobox({
            select: function(event, ui) {
                console.log(ui.item.value);
            }
        });

        $("#combobox option:selected").val('8585');
    });
    </script>
    <style>
    body {
        font-family: '微軟正黑體', sans-serif;
        font-size: 16px;
    }

    .div-css {
        margin-top: 50px;
    }

    .custom-combobox {
        position: relative;
        display: inline-block;
    }

    .custom-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
    }

    .custom-combobox-input {
        margin: 0;
        padding: 5px 10px;
    }
    </style>
</head>

<body>
    <form method="POST">
        <div class="div-css">
            <div class="ui-widget" style="border: 1px solid #CCC;padding:10px;border-radius:10px;">
                <label>
                    請選擇發票捐贈單位：
                </label>
                <select name="donation" id="combobox">
                    <?php foreach ($rs as $v) {?>
                    <option value="<?php echo $v['iCode']; ?>"<?php if ($v['iCode'] == $default_donation) {
        echo ' selected';
}
    ?>>
                        <?php echo $v['iName']; ?>（捐贈碼：<?php echo $v['iCode']; ?>）
                    </option>
                    <?php }?>
                </select>
            </div>

        </div>
        <div style="margin-top: 20px;">
            <center>
                <button type="submit" style="padding: 10px;">設定</button>
                <button type="button" style="padding: 10px;" onclick="parent.$.colorbox.close()">關閉</button>
                <input type="hidden" name="identify_id" value="<?php echo $identify_id; ?>" />
                <input type="hidden" name="target" value="<?php echo $target; ?>" />
            </center>
        </div>
    </form>
</body>

</html>