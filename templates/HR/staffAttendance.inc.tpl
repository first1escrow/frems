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
    .card-header {
        display: flex;
        justify-content: space-between; /* 左右兩端對齊 */
        align-items: center; /* 垂直置中 */
    }
    .logout {
        float: none; /* 取消浮動 */
        margin-right: 10px; /* 保留右邊距 */
        color: #007bff;
        text-decoration: none;
        font-size: xx-small;
    }

    </style>
</head>

<body>
    <div style="height:30px;"></div>
    <div id="area" class="container">
        <div class="card">
            <div class="card-header">
                <span>打卡記錄編輯</span>
                <span><a href="Javascript:void(0);" class="logout" onclick="logout()">登出</a></span>
            </div>
            <div class="card-body">
                <div class="item">
                    <span>對象：</span>
                    <span>
                    <{html_options name="staff" style="min-width:120px;" onchange="" options=$menu_staff}>
                    </span>
                </div>
                <div class="item">
                    <span>日期：</span>
                    <span><input type="date" style="min-width:120px;" name="date" value="" max="<{$today}>"></span>
                </div>
            </div>
            <div class="card-footer" style="text-align:center;">
                <button type="button" class="btn btn-primary" style="width:100px;" onclick="query()">查詢</button>
                <button type="button" class="btn btn-success" style="width:100px;" onclick="add()">新增</button>
            </div>
        </div>

        <div style="height:50px;"></div>

        <div id="table-area" style="display: none;">
            <table id="check-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>上下班</th>
                        <th>打卡時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="modal"></div>
</body>

</html>
<script>

$(document).ready(function() {

});

function query() {
    const staff = $('[name="staff"]');
    const date = $('[name="date"]');

    if (!staff.val() || staff.val() === '0') {
        alert('請選擇員工');
        staff.focus();
        return;
    }

    if (!date.val()) {
        alert('請選擇日期');
        date.focus();
        return;
    }

    getAttendanceData(staff.val(), date.val());
}

function getAttendanceData(staffId, date) {
    $.ajax({
        url: '/includes/HR/attendanceData.php',
        type: 'POST',
        data: {
            staffId: staffId,
            date: date
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.message) {
                    alert(response.message);
                }

                populateTable(response.data);
            } else {
                alert('查詢失敗：' + response.message);
            }
        },
        error: function() {
            alert('系統錯誤，請稍後再試。');
        }
    });
}

function populateTable(data) {
    const tbody = $('#check-table tbody');
    tbody.empty();
    data.forEach(function(item) {
        const row = `<tr>
                        <td>${item.sStaffName}</td>
                        <td>${item.sInOutText}</td>
                        <td>${item.attendanceTime}</td>
                        <td><button class="btn btn-secondary" onclick="editRecord(${item.sId})">編輯</button></td>
                    </tr>`;
        tbody.append(row);
    });

    $('#table-area').show();
}

function editRecord(id) {
    $.colorbox({
        href: '/HR/attendanceModify.php?id=' + id,
        iframe: true,
        width: '50%',
        height: '80%',
        onClosed: function() {
            // 重新查詢打卡記錄
            query();
        }
    });
}

function add() {
    const staff = $('[name="staff"]');
    const date = $('[name="date"]');

    if (!staff.val() || staff.val() === '0') {
        alert('請選擇員工');
        staff.focus();
        return;
    }

    if (!date.val()) {
        alert('請選擇日期');
        date.focus();
        return;
    }

    $.colorbox({
        href: '/HR/attendanceAdd.php?staffId=' + staff.val() + '&date=' + date.val(),
        iframe: true,
        width: '50%',
        height: '80%',
        onClosed: function() {
            // 重新查詢打卡記錄
            query();
        }
    });
}

function logout() {
    $.ajax({
        url: '/includes/HR/attendanceLogout.php',
        type: 'POST',
        success: function(response) {
            if (response.success) {
                location.reload(); // 重新載入頁面
            } else {
                alert('登出失敗：' + response.message);
            }
        },
        error: function() {
            alert('系統錯誤，請稍後再試。');
        }
    });
}
</script>