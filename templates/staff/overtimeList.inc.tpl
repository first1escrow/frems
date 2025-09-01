<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>第一建經加班申請審核</title>
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
            <div class="card-header">第一建經加班紀錄明細</div>
            <div class="card-body">
                <div class="item">
                    <span>申請人：</span>
                    <span><{$case.staffName}></span>
                </div>
                <div class="item">
                    <span>事由：</span>
                    <span><{$case.sApplyReason}></span>
                </div>
                <div class="item">
                    <span>日期時間：</span>
                    <span>
                        <ul style="list-style-type:none;">
                            <li><{$case.sOvertimeFromDateTime}>（起）</li>
                            <li><{$case.sOvertimeToDateTime}>（迄）</li>
                        </ul>
                    </span>
                </div>
                <div class="item">
                    <span>主管：</span>
                    <span><{$case.supervisor}></span>
                </div>
                <div class="item">
                    <span>審核時間：</span>
                    <span><{$case.sUnitApprovalDateTime}></span>
                </div>
            </div>
            <div class="card-footer" style="text-align:center;">
                <button type="button" class="btn btn-primary" style="width:100px;" onclick="closeInfo()">關閉</button>
            </div>
        </div>
    </div>
</body>

</html>
<script>
$(document).ready(function() {

});

function closeInfo() {
    parent.$.colorbox.close();
}
</script>