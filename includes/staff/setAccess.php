<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$conn = new first1DB;

$id = $_SESSION['member_id'];

$alert = '';
if ($_POST['save'] == 'ok') {
    $originPassword = $_POST['originPassword'];
    $newPassword    = $_POST['newPassword'];
    $retypePassword = $_POST['retypePassword'];

    if (empty($originPassword)) {
        exit('<center>請輸入目前存取碼</center>');
    }

    if (empty($newPassword)) {
        exit('<center>請輸入新存取碼</center>');
    }

    if (empty($retypePassword)) {
        exit('<center>請輸入確認存取碼</center>');
    }

    if ($newPassword != $retypePassword) {
        exit('<center>新存取碼與確認存取碼不一致</center>');
    }

    $sql  = 'SELECT pAccessCode FROM tPeopleInfo WHERE pId = :id;';
    $bind = [
        'id' => $id,
    ];
    $rs = $conn->one($sql, $bind);

    if (md5($originPassword) != $rs['pAccessCode']) {
        exit('<center>目前密碼錯誤</center>');
    }

    $sql  = 'UPDATE tPeopleInfo SET pAccessCode = :password WHERE pId = :id;';
    $bind = [
        'password' => md5($newPassword),
        'id'       => $id,
    ];

    $alert = $conn->exeSql($sql, $bind) ? 'alert("儲存成功");' : 'alert("儲存失敗");';
}
?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>第一建經人員基本資料</title>
    <!------------------------- RWD open ------------------------->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <!--Google icon-->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <style>
    .item {
        padding: 5px;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    </style>
</head>

<body>
    <div style="height:30px;"></div>
    <div id="area" class="container">
        <div class="card">
            <div class="card-header">變更存取碼

            </div>
            <div class="card-body">
                <form method="post">
                    <div class="card-body">
                        <div class="item">
                            <span>目前存取碼：</span>
                            <span><input type="password" id="originPassword" name="originPassword"
                                    placeholder="請輸入目前存取碼"></span>
                        </div>
                        <div class="item">
                            <span>修改存取碼：</span>
                            <span><input type="password" id="newPassword" name="newPassword"
                                    placeholder="請輸入新存取碼"></span>
                        </div>
                        <div class="item">
                            <span>確認存取碼：</span>
                            <span><input type="password" id="retypePassword" name="retypePassword"
                                    placeholder="請輸入確認存取碼"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer" style="text-align:center;">
                <button type="button" class="btn btn-primary" style="width:100px;" onclick="store()">儲存</button>
                <button type="button" class="btn btn-info" style="width:100px;" onclick="cancel()">返回</button>
            </div>
        </div>
    </div>
</body>

</html>
<script type="text/javascript">
<?php echo $alert; ?>

$(document).ready(function() {

});

function store() {
    let originPassword = $('#originPassword').val();
    let newPassword = $('#newPassword').val();
    let retypePassword = $('#retypePassword').val();

    if (originPassword == '') {
        alert('請輸入目前存取碼');
        $('#originPassword').focus();
        return;
    }

    if (newPassword == '') {
        alert('請輸入新存取碼');
        $('#newPassword').focus();
        return;
    }

    if (retypePassword == '') {
        alert('請輸入確認存取碼');
        $('#retypePassword').focus();
        return;
    }

    if (newPassword != retypePassword) {
        alert('新存取碼與確認存取碼不一致');
        $('#retypePassword').focus();
        return;
    }

    let el = '<input type="hidden" name="save" value="ok">';
    $('form').append(el).submit();
}

function cancel() {
    parent.location.reload();
}
</script>