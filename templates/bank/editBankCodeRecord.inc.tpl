<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>地政士申請紀錄</title>
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

    .banner-height {
        min-height: 50px;
    }

    .btn-fixed-width {
        width: 80px;
    }

    .btn-wide {
        width: 100px;
        white-space: nowrap;
    }
    </style>
</head>

<body>
    <div style="height:30px;"></div>
    <div id="area" class="container">
        <div class="card">
            <div class="card-header banner-height">地政士申請紀錄</div>
            <div class="card-body">
                <div class="item">
                    <span>地政士：</span>
                    <span><{$data.scrivenerName}> (<{$data.scrivenereId}>)</span>
                </div>
                <div class="item">
                    <span>申請時間：</span>
                    <span><{$data.aApplyDateTime}></span>
                </div>
                <div class="item">
                    <span>申請來源：</span>
                    <span><{$data.aFrom}></span>
                </div>
                <div class="item">
                    <span>合約銀行：</span>
                    <span><{$data.escrowBankFullName}>(<{$data.escrowBankBranchFullName}>)</span>
                </div>
                <div class="item">
                    <span>合約書版本：</span>
                    <span><{$data.brand}></span>
                </div>
                <div class="item">
                    <span>仲介類型：</span>
                    <span><{$data.category}></span>
                </div>
                <div class="item">
                    <span>合約類別：</span>
                    <span><{$data.application}></span>
                </div>
                <div class="item">
                    <span>申請數量：</span>
                    <span><{$data.aQuantity}></span>
                </div>
                <div class="item">
                    <span>申請狀態：</span>
                    <span><{$data.process}></span>
                </div>
            </div>
            <div class="card-footer operaition banner-height">
                <{if isset($smarty.session.member_addcertifty) && $smarty.session.member_addcertifty != '0'}>
                    <{if $data.aProcessed == '1' }>
                    <span class="operation-area"><button type="button" class="btn btn-primary" onclick="apply(<{$data.aId}>, 2)">製作</button></span>
                    <span class="operation-area"><button type="button" class="btn btn-danger" onclick="apply(<{$data.aId}>, 5)">撤銷</button></span>
                    <{/if}>
                <{/if}>
                <span class="operation-area"><button type="button" class="btn btn-secondary" onclick="closeIframe()">關閉</button></span>
            </div>
        </div>
    </div>
    <form method="POST" action="/bank/create.php" id="createForm" target="_blank">
        <input type="hidden" name="aId" value="<{$data.aId}>">
        <input type="hidden" name="Lnum" value="<{$data.apply.Lnum}>">
        <input type="hidden" name="Bnum" value="<{$data.apply.Bnum}>">
        <input type="hidden" name="Snum" value="<{$data.apply.Snum}>">
        <input type="hidden" name="bank" value="<{$data.apply.bank}>">
        <input type="hidden" name="man" value="<{$data.apply.man}>">
        <input type="hidden" name="bBrand" value="<{$data.apply.bBrand}>">
        <input type="hidden" name="ver" value="<{$data.apply.ver}>">
        <input type="hidden" name="key" value="<{$data.apply.key}>">
        <input type="hidden" name="save" value="<{$data.apply.save}>">
    <form>
</body>

</html>
<script>
$(document).ready(function() {
    // 可以在這裡添加其他初始化代碼
});

function apply(id, action) {
    $.ajax({
        url: '/includes/bank/updateApplyBankCodeRecord.php',
        type: 'POST',
        data: {
            id: id,
            action: action
        },
        success: function(response) {
            if (response === 'success') {
                // 成功處理後的操作
                if (action == 2) {
                    $('#createForm').submit();
                    alert('已申請地政士履保合約書');
                } else if (action == 3) {
                    alert('已完成出貨');
                } else if (action == 5) {
                    alert('已撤銷申請');
                } else {
                    alert(response);
                }
            } else {
                alert('操作失敗：' + response);
            }
            
            parent.$.colorbox.close();
        },
        error: function(xhr, status, error) {
            // 處理錯誤情況
            alert('申請失敗：' + xhr.responseText);
            console.log(xhr, status, error);
        }
    });
}

function closeIframe() {
    parent.$.colorbox.close();
}
</script>
