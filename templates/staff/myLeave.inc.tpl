<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<{include file='meta.inc.tpl'}>

<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<!--Google icon-->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/superfish/1.7.10/js/superfish.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js"></script>
<style>
table {
    text-align: center;
    font-size: 10pt;
}

.page {
    padding: 10px 15px;
    vertical-align: middle;
    margin: 10px 10px;
    cursor: pointer;

    border: 1px solid #a09898;
    border-radius: 5px;
    color: #C0C0C0;
}

#page-btn-refresh {
    color: #000;
}
.page-active {
    background-color: #e90303;
    color: white;
    font-weight: bold;
    font-size: 16px;
}
</style>
</head>
<body id="dt_example">
    <div class="cmc_overlay" style="display:none;">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="233" height="72">&nbsp;</td>
                    <td width="753">
                        <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right">
                                    <div id="abgne_marquee" style="display:none;">
                                        <ul>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">
                                    <h1><{include file='welcome.inc.tpl'}></h1>
                                </td>
                            </tr>
                            <tr>
                                <td width="81%" align="right"></td>
                                <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table> 
        </div>
        <{include file='menu1.inc.tpl'}>
        <table width="1000" border="0" cellpadding="4" cellspacing="0">
            <tr>
                <td bgcolor="#DBDBDB">
                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                        <tr>
                            <td height="17" bgcolor="#FFFFFF">
                                <div id="menu-lv2">
                                
                                </div>

                                <div id="container">
                                
                                    <div style="display:inline-flex;float:left;">
                                        <div id="page-btn-leave" class="page" onclick="page('leave')">休假作業</div>
                                        <div id="page-btn-overtime" class="page" onclick="page('overtime')">加班作業</div>
                                    </div>

                                    <div style="clear:both;"></div>

                                    <{* <h1 id="capital" style="text-align:left;">休假記錄</h1> *}>

                                    <div id="page-leave" style="display: none;">
                                        <div style="text-align:right;">
                                            <a href="javascript:void(0);" class="iframe" style="padding: 5px 10px; margin: 10px 0;" onclick="apply()">休假申請</a>
                                            <{* <a href="javascript:void(0);" style="padding: 5px 10px; margin: 10px 0;" onclick="refresh('leave')">重新整理</a> *}>
                                        </div>

                                        <table id="leave-table" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>申請時間</th>
                                                    <th>申請人</th>
                                                    <th>假別</th>
                                                    <th>請假日期</th>
                                                    <th>進度</th>
                                                    <th>狀態</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7" class="dataTables_empty">讀取資料中...</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div id="page-overtime" style="display: none;">
                                        <div style="text-align:right;">
                                            <a href="javascript:void(0);" class="iframe" style="padding: 5px 10px; margin: 10px 0;" onclick="overtimeApply()">加班申請</a>
                                            <{* <a href="javascript:void(0);" style="padding: 5px 10px; margin: 10px 0;" onclick="refresh('overtime')">重新整理</a> *}>
                                        </div>

                                        <table id="overtime-table" class="display" style="width:100%;text-aligm:left;">
                                            <thead>
                                                <tr>
                                                    <th>申請時間</th>
                                                    <th>申請人</th>
                                                    <th>加班時間</th>
                                                    <th>事由</th>
                                                    <th>進度</th>
                                                    <th>狀態</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7" class="dataTables_empty">讀取資料中...</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <div id="footer" style="height:50px;">
            <p>2012 第一建築經理股份有限公司 版權所有</p>
        </div>
    </div>
</body>
</html>
<script type="text/javascript">

const leaveTable = $('#leave-table').DataTable({
    ajax: {
        url: '/includes/staff/getMyLeaveData.php',
        type: 'POST'
    },
    order: [[ 0, "desc" ]],
    columns: [
        { "data": "sCreatedAt" },
        { "data": "applicantName" },
        { "data": "leaveName" },
        { "data": "leaveDateTime" },
        { "data": "processing" },
        { "data": "status" },
        { "data": null }
    ],
    columnDefs: [
        { 
            className: "dt-center", 
            targets: "_all" 
        },
        {
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                if ((row.sStatus === 'N') && !row.sAgentApprovalDateTime && !row.sUnitApprovalDateTime && !row.sManagerApprovalDateTime) {
                    return '<button style="padding:5px;" onclick=cancel(' + row.sId + ')>取消</button>';
                }

                if (row.sStatus === 'Y') {
                    if (row.revoke === 'Y') {
                        return '<button style="padding:5px;" onclick=revoke(' + row.sId + ')>撤銷</button>';
                    }

                    if (row.revoke && row.revoke !== 'N') {
                        return '<span>' + row.revoke + '</span>';
                    }
                }

                return '';
            }
        }
    ],
    searching: false,
    paging: true,
    lengthChange: false,
    processing: true,
    language: {
        "processing": "",
        "loadingRecords": "載入中...",
        "lengthMenu": "顯示 _MENU_ 項結果",
        "zeroRecords": "沒有符合的結果",
        "info": "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
        "infoEmpty": "顯示第 0 至 0 項結果，共 0 項",
        "infoFiltered": "(從 _MAX_ 項結果中過濾)",
        "infoPostFix": "",
        "search": "搜尋:",
        "emptyTable": "無資料",
        "paginate": {
            "first": "第一頁",
            "previous": "上一頁",
            "next": "下一頁",
            "last": "最後一頁"
        },
        "aria": {
            "sortAscending": ": 升冪排列",
            "sortDescending": ": 降冪排列"
        }
    }
});

const overtimeTable = $('#overtime-table').DataTable({
    ajax: {
        url: '/includes/staff/getMyOvertimeData.php',
        type: 'POST'
    },
    order: [[ 0, "desc" ]],
    columns: [
        { "data": "sCreatedAt" },
        { "data": "applicantName" },
        { "data": "overtimeDateTime" },
        { "data": "reason" },
        { "data": "processing" },
        { "data": "status" },
        { "data": null }
    ],
    columnDefs: [
        { 
            className: 'dt-center dt-head-center dt-body-center', 
            targets: "_all" 
        },
        {
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                if ((row.sStatus === 'N') && !row.sUnitApprovalDateTime) {
                    return '<button style="padding:5px;" onclick=cancelOvertime(' + row.sId + ')>取消</button>';
                }

                if (row.sStatus === 'Y') {
                    if (row.revoke === 'Y') {
                        return '<button style="padding:5px;" onclick=revokeOvertime(' + row.sId + ')>撤銷</button>';
                    }

                    if (row.revoke && row.revoke !== 'N') {
                        return '<span>' + row.revoke + '</span>';
                    }
                }

                return '';
            }
        }
    ],
    searching: false,
    paging: true,
    lengthChange: false,
    processing: true,
    language: {
        "processing": "",
        "loadingRecords": "載入中...",
        "lengthMenu": "顯示 _MENU_ 項結果",
        "zeroRecords": "沒有符合的結果",
        "info": "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
        "infoEmpty": "顯示第 0 至 0 項結果，共 0 項",
        "infoFiltered": "(從 _MAX_ 項結果中過濾)",
        "infoPostFix": "",
        "search": "搜尋:",
        "emptyTable": "無資料",
        "paginate": {
            "first": "第一頁",
            "previous": "上一頁",
            "next": "下一頁",
            "last": "最後一頁"
        },
        "aria": {
            "sortAscending": ": 升冪排列",
            "sortDescending": ": 降冪排列"
        }
    }
});

$(document).ready(function() {
    page('leave');
});

function refresh(type=null) {
    if (type === 'leave') {
        leaveTable.ajax.reload();
        return;
    }

    if (type === 'overtime') {
        overtimeTable.ajax.reload();
        return;
    }

    leaveTable.ajax.reload();
    overtimeTable.ajax.reload();
}

function cancelOvertime(id) {
    if (confirm('確定要取消加班申請嗎？')) {
        $.ajax({
            url: '/includes/staff/cancelOvertime.php',
            type: 'POST',
            data: {
                id
            },
            success: function(data) {
                alert(data);
                refresh('overtime');
            },
            error: function(xhr, status, error) {
                let errorMessage = xhr.responseText ? ' (' + xhr.responseText + ')' : '';
                alert('取消失敗' + errorMessage);
                refresh('overtime');
            }
        });
    }
}

function revokeOvertime(id) {
    if (confirm('確定要撤銷已核可的加班申請嗎？')) {
        $.ajax({
            url: '/includes/staff/revokeOvertime.php',
            type: 'POST',
            data: {
                id
            },
            success: function(data) {
                alert(data);
                refresh('overtime');
            },
            error: function(xhr, status, error) {
                let errorMessage = xhr.responseText ? ' (' + xhr.responseText + ')' : '';
                alert('撤銷失敗' + errorMessage);
                refresh('overtime');
            }
        });
    }
}

function cancel(id) {
    if (confirm('確定要取消休假申請嗎？')) {
        $.ajax({
            url: '/includes/staff/cancelLeave.php',
            type: 'POST',
            data: {
                id
            },
            success: function(data) {
                alert(data);
                refresh('leave');
            },
            error: function(xhr, status, error) {
                let errorMessage = xhr.responseText ? ' (' + xhr.responseText + ')' : '';
                alert('取消失敗' + errorMessage);
                refresh('leave');
            }
        });
    }
}

function revoke(id) {
    if (confirm('確定要撤銷已核可的休假申請嗎？')) {
        $.ajax({
            url: '/includes/staff/revokeLeave.php',
            type: 'POST',
            data: {
                id
            },
            success: function(data) {
                alert(data);
                refresh('leave');
            },
            error: function(xhr, status, error) {
                let errorMessage = xhr.responseText ? ' (' + xhr.responseText + ')' : '';
                alert('撤銷失敗' + errorMessage);
                refresh('leave');
            }
        });
    }
}

function apply() {
    let url = '/staff/leaveApply.php';
    $.colorbox({
        iframe:true, 
        width:"1200px", 
        height:"90%", 
        href:url, 
        onClosed:function() {
            refresh('leave');
       }
    }); ;
}

function overtimeApply() {
    let url = '/staff/overtimeApply.php';
    $.colorbox({
        iframe:true, 
        width:"1200px", 
        height:"90%", 
        href:url, 
        onClosed:function() {
            refresh('overtime');
       }
    }); ;
}

function page(status) {
    $('#page-leave').hide();
    $('#page-overtime').hide();

    $('#page-btn-leave').removeClass('page-active');
    $('#page-btn-overtime').removeClass('page-active');

    let text = status === 'leave' ? '休假記錄' : '加班記錄';
    $('#capital').text(text);

    $(`#page-btn-${status}`).addClass('page-active');

    refresh(status);
    $(`#page-${status}`).show();
}

</script>