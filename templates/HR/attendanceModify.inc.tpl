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
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- 以 jsDelivr CDN 為例 -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/jquery-colorbox@1.6.4/example1/colorbox.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-colorbox@1.6.4/jquery.colorbox-min.js"></script>
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
            <div class="card-header">編輯打卡記錄</div>
            <div class="card-body">
                <div class="item">
                    <span>對象：</span>
                    <span>
                        <input type="hidden" name="id" value="<{$data.sId}>">
                        <{$data.staffName}>
                    </span>
                </div>
                <div class="item">
                    <span>日期：</span>
                    <span>
                        <{$data.date}>
                    </span>
                </div>
                <div class="item">
                    <span>班別：</span>
                    <span>
                        <select name="inOut" style="min-width:120px;">
                            <option value="">請選擇</option>
                            <option value="IN" <{if $data.sInOut == 'IN'}>selected<{/if}>>上班</option>
                            <option value="OUT" <{if $data.sInOut == 'OUT'}>selected<{/if}>>下班</option>
                        </select>
                    </span>
                </div>
                <div class="item">
                    <span>時間：</span>
                    <span>
                        <input type="time" name="time" value="<{$data.time}>">
                    </span>
            </div>
            <div class="card-footer" style="text-align:center;">
                <button type="button" class="btn btn-success" style="width:100px;" onclick="update()">更新</button>
                <button type="button" class="btn btn-danger" style="width:100px;" onclick="del()">刪除</button>
            </div>
        </div>
    </div>

    <div id="modal"></div>
</body>

</html>
<script>

$(document).ready(function() {

});

function update() {
    const id = $('[name="id"]');
    const inOut = $('[name="inOut"]');
    const time = $('[name="time"]');

    if (!id.val() || id.val() === '0') {
        alert('無法確認出勤紀錄');
        return;
    }

    if (!inOut.val()) {
        alert('請選擇上下班');
        inOut.focus();
        return;
    }

    if (!time.val()) {
        alert('請輸入時間');
        time.focus();
        return;
    }

    $.ajax({
        url: '/includes/HR/attendanceUpdate.php',
        type: 'POST',
        data: {
            id: id.val(),
            inOut: inOut.val(),
            time: time.val()
        },
        success: function(response) {
            if (response.success) {
                alert('打卡記錄已更新');
                parent.$.colorbox.close(); // 關閉 colorbox
                return;
            }

            if (response.message) {
                alert(response.message);
                return;
            }
        },
        error: function(xhr, status, error) {
            alert('新增失敗：' + error);
        }
    });
}

function del() {
    const id = $('[name="id"]');

    if (!id.val() || id.val() === '0') {
        alert('無法確認出勤紀錄');
        return;
    }
    
    if (!confirm('確定要刪除這筆打卡紀錄嗎？')) {
        return;
    }

    $.ajax({
        url: '/includes/HR/attendanceDelete.php',
        type: 'POST',
        data: {
            id: id.val()
        },
        success: function(response) {
            if (response.success) {
                alert('打卡記錄已刪除');
                parent.$.colorbox.close(); // 關閉 colorbox
                return;
            }

            if (response.message) {
                alert(response.message);
                return;
            }
        },
        error: function(xhr, status, error) {
            alert('新增失敗：' + error);
        }
    });
}
</script>