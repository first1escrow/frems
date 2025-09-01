<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>第一建經員工加班報表下載</title>
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
            <div class="card-header">第一建經員工加班報表下載</div>
            <div class="card-body">
                <form id="mydorm" method="POST" action="staffOvertimeExcel.php">
                    <{if $error}>
                    <div class="item">
                        <h3>{$error}</h3>
                    </div>
                    <{else}>
                    <div class="item">
                        <span>員工對象：</span>
                        <span><{html_options name="staff" options=$staff_menu selected=$staff_selected}></span>
                    </div>
                    <div class="item">
                        <span>時間範圍：</span>
                        <span>
                            <input type="date" name="from" value="<{$fromDate}>"> ~ <input type="date" name="to" value="<{$toDate}>">
                        </span>
                    </div>
                    <{/if}>
                </form>
            </div>
            <div class="card-footer" style="text-align:center;">
                <{if !$error}>
                <button type="button" class="btn btn-primary" style="width:100px;" onclick="downloadReport()">下載</button>
                <{/if}>
                <button type="button" class="btn btn-secondary" style="width:100px;" onclick="closeWindow()">關閉</button>
            </div>
        </div>
    </div>
</body>

</html>
<script>
$(document).ready(function() {

});

function downloadReport() {
    let staff = $("select[name='staff']").val();
    let from = $("input[name='from']").val();
    let to = $("input[name='to']").val();

    if (from == "" || to == "") {
        alert("請指定時間範圍");
        return;
    }

    let el = document.createElement('input');
    el.type = 'hidden';
    el.name = "report";
    el.value = "download";

    $("#mydorm").append(el).submit();
}

function closeWindow() {
    parent.$.colorbox.close();
}
</script>