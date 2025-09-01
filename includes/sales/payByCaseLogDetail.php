<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

$alert = '';

$id = empty($_GET['id']) ? '' : $_GET['id'];

$conn = new first1DB;

$sql = 'SELECT * FROM tFeedBackMoneyPayByCaseLog WHERE fCertifiedId = "'.$id.'" ORDER BY fLogCreated_at DESC';
$rs  = $conn->all($sql);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <title>Log</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script src="/js/1.13.3/combobox.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
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
    <div class="div-css">
        <div class="ui-widget" style="border: 1px solid #CCC;padding:10px;border-radius:10px;">
            <?php foreach ($rs as $v) {?>
                <?php echo $v['fId']; ?>
            <?php }?>
        </div>

    </div>
</body>

</html>