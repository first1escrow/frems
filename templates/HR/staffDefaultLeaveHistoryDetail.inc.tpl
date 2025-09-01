<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>休假時數設定歷史紀錄</title>
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
        <h3>休假時數設定歷史紀錄</h3>
        <{* <p>特休假</p>             *}>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>假別</th>
                    <th>設定日期</th>
                    <th>預設時數</th>
                    <th>剩餘時數</th>
                </tr>
            </thead>
            <tbody>
            <{foreach from=$list item=item}>
                <tr>
                    <td><{$item.leaveName}></td>
                    <td><{$item.sDate}></td>
                    <td><{$item.sLeaveDefault}></td>
                    <td><{$item.sLeaveBalance}></td>
                </tr>
            <{/foreach}>
            </tbody>
        </table>
        <div>
            <button type="button" class="btn btn-primary" onclick="back()">返回</button>
        </div>
    </div>
</body>

</html>
<script>
$(document).ready(function() {

});

function back() {
    parent.$.colorbox.close();
}

</script>