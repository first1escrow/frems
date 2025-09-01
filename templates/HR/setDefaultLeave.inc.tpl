<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>休假預設時數設定</title>
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
    <form id="form1" method="post">
        <div class="card">
            <div class="card-header">休假預設時數設定</div>
            <div class="card-body">
                <div class="item">
                    <b>
                    <span>休假人：</span>
                    <span>
                        <{if $data[0].sStaffId}>
                        <{$data[0].sStaffName}><input type="hidden" name="sStaffId" value="<{$data[0].sStaffId}>">
                        <{else}>
                        <select name="sStaffId" style="width:100px;" onchange="staffSelect()">
                            <option value="">請選擇</option>
                            <{foreach item=staff from=$staffs}>
                                <option value="<{$staff.pId}>"><{$staff.pName}></option>
                            <{/foreach}>
                        </select>
                        <{/if}>
                    </span>
                    </b>
                </div>
                <div class="item table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th style="white-space: nowrap;">類別</th>
                                <th>預設時數</th>
                                <th>剩餘時數</th>
                                <th>備註</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach item=leave from=$data}>
                                <tr>
                                    <td>
                                        <{$leave.sLeaveName}>
                                        <input type="hidden" class="sLeaveId" name="sLeaveId[]" value="<{$leave.sLeaveId}>">
                                    </td>
                                    <td>
                                        <input type="number" class="sLeaveDefault" name="sLeaveDefault[]" value="<{$leave.sLeaveDefault}>" style="width:100px;">
                                    </td>
                                    <td>
                                        <input type="number" class="sLeaveBalance" name="sLeaveBalance[]" value="<{$leave.sLeaveBalance}>" style="width:100px;">
                                    </td>
                                    <td>
                                        <textarea class="sLeaveRemark" name="sLeaveRemark[]" rows="2" style="width:100%;"><{$leave.sLeaveRemark}></textarea>
                                    </td>
                                </tr>
                            <{/foreach}>
                        </tbody>
                    </table>
                </div>
                <div class="item" style="text-align: center";>
                    <span>
                        <button type="button" class="btn btn-primary" onclick="update()">儲存</button>
                    </span>
                    <span>
                        <!-- <button type="button" class="btn btn-warning" onclick="setAll()">全部歸零</button> -->
                    </span>
                    <span>
                        <button type="button" class="btn btn-info" onclick="back()">返回</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script>
<{$alert}>

$(document).ready(function() {

});

function update() {
    let emptyValue = false;

    if ($('select[name="sStaffId"]').val() == '') {
        emptyValue = true;
        alert('請選擇休假人');
        $('select[name="sStaffId"]').focus();
        return;
    }

    if (emptyValue) {
        return;
    }

    $('#form1').submit();
}

function back() {
    parent.$.colorbox.close();
}

function setAll() {
    $('.sLeaveDefault').each(function() {
        $(this).val(0);
    });

    $('.sLeaveBalance').each(function() {
        $(this).val(0);
    });

    $('.sDefaultLeaveRemark').each(function() {
        $(this).val('');
    });
}

function staffSelect() {
    let staff = $('select[name="sStaffId"]').val();

    if (!staff) {
        $('.sLeaveDefault').each(function() {
            $(this).val(0);
        });

        $('.sLeaveBalance').each(function() {
            $(this).val(0);
        });

        $('.sDefaultLeaveRemark').each(function() {
            $(this).val('');
        });

        return;
    }

    let url = '/includes/HR/getDefaultLeave.php';
    $.post(url, {
        staff: staff
    }, function(data) {
        console.log(data);

        data.forEach(function(item) {
            let sLeaveId = $('.sLeaveId');

            sLeaveId.each(function() {
                if ($(this).val() == item.sLeaveId) {
                    $(this).closest('tr').find('input').eq(1).val(item.sLeaveDefault);
                    $(this).closest('tr').find('input').eq(2).val(item.sLeaveBalance);
                    $(this).closest('tr').find('textarea').val(item.sLeaveRemark);
                }
            });
            
        });
    }, 'json').fail(function(xhr, textStatus, errorThrown) {
        alert(textStatus);
    });
}
</script>