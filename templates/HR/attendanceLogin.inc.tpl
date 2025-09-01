<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>第一建經假勤記錄編輯</title>
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
    .card {
        max-width: 400px;
        text-align: center;
        margin: 0 auto;
        margin-top: 50px;
    }

    .item {
        padding: 5px;

    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .operaition {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }

    .operation-area {
        margin-right: 8px; 
        cursor: pointer;
    }
    </style>
</head>

<body>
    <div style="height:30px;"></div>
    <div id="container">
        <div class="card">
        <div class="card-header">請輸入存取碼</div>
        <div class="card-body">
            <form id="form1" method="post" action="attendanceLogin.php">
                <div class="item">
                    <span><input type="password" name="access-code" placeholder="請輸入存取碼"></span>
                </div>
                <div class="card-footer operaition">
                    <button class="btn btn-primary operation-area" onclick="access()">確認</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<script type="text/javascript">
<{$alert}>

$(document).ready(function() {     
    $('[name="access-code"]').focus();
    $('body').keydown(function(event) {
        if (event.keyCode == 13) { // Enter key
            access();
        }
    });
});

function access() {
    let accessCode = $('input[name="access-code"]').val();
    if (accessCode == '') {
        alert('請輸入存取碼');
        return;
    }

    $('#form1').submit();
}
</script>