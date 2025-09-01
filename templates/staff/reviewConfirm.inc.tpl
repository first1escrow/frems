<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<{include file='meta.inc.tpl'}>

<!--Google icon-->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/js/colorbox-master/colorbox.css" />

<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script type="text/javascript" src="/js/colorbox-master/jquery.colorbox.js"></script>
<style>
#container {
    /* width: 80%; */
    margin: 0 auto;
    padding: 20px;
}

#example {
    /* text-align: center; */
    font-size: 10pt;
}

#example th {
    text-align: center;
    font-weight: bold;
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

.page-active {
    background-color: #e90303;
    color: white;
    font-weight: bold;
    font-size: 16px;
}
</style>
</head>
<body>
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
                                <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                                    <h1 style="text-align:left;">簽核記錄</h1>

                                    <div style="text-align:right;">
                                        <a href="javascript:void(0);" style="padding: 5px 10px; margin: 10px 0;" onclick="refresh()">重新整理</a>
                                    </div>

                                    <div style="display:inline-flex;">
                                        <div class="page" id="page-btn-N" onclick="page('N')">待審核</div>
                                        <{if $smarty.session.pHRConfirmedList == 1}>
                                        <div class="page" id="page-btn-Y" onclick="page('Y')">已審核</div>
                                        <{/if}>
                                    </div>

                                    <div id="page-N" style="display: none;">
                                        <table id="unconfirm" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>申請日期</th>
                                                    <th>申請人</th>
                                                    <th>申請項目</th>
                                                    <th>申請內容</th>
                                                    <th>狀態</th>
                                                    <th>審核</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="page-Y" style="display: none;">
                                        <div id="tabs">
                                            <ul>
                                                <li><a href="#tabs-1" style="color:#000;">補卡審核紀錄</a></li>
                                                <li><a href="#tabs-2" style="color:#000;">請假審核紀錄</a></li>
                                                <li><a href="#tabs-3" style="color:#000;">加班審核紀錄</a></li>
                                            </ul>

                                            <div id="tabs-1">
                                                <table id="history-1" class="display" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>補卡日期</th>
                                                            <th>申請人</th>
                                                            <th>申請項目</th>
                                                            <th>申請內容</th>
                                                            <th>狀態</th>
                                                            <th>審核時間</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div id="tabs-2">
                                                <table id="history-2" class="display" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>請假日期</th>
                                                            <th>申請人</th>
                                                            <th>申請項目</th>
                                                            <th>狀態</th>
                                                            <th>審核時間</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div id="tabs-3">
                                                <table id="history-3" class="display" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>加班申請日期</th>
                                                            <th>申請人</th>
                                                            <th>申請項目</th>
                                                            <th>狀態</th>
                                                            <th>審核時間</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
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
const table = $('#unconfirm').DataTable({
    ajax: {
        url: '/includes/staff/getReviewData.php',
        type: 'POST',
    },
    columnDefs: [
        {
            "targets": 0,
            "render": function (data, type, row, meta) {
                return row.createdAt;
            }
        },
        {
            "targets": 1,
            "render": function (data, type, row, meta) {
                return row.staffName;
            }
        },
        {
            "targets": 2,
            "render": function (data, type, row, meta) {
                return row.reviewTypeName;
            }
        },
        {
            "targets": 3,
            "render": function (data, type, row, meta) {
                return row.description;
            }
        },
        {
            "targets": 4,
            "render": function (data, type, row, meta) {
                return row.status;
            }
        },
        {
            "targets": 5,
            "render": function (data, type, row, meta) {
                return `<button type="button" style="padding:5px;" onclick="verify('${row.reviewType}', ${row.sId})">審核</button>`;
            }
        }
    ],
    searching: false,
    lengthChange: false,
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

const table1 = $('#history-1').DataTable({
    ajax: {
        url: '/includes/staff/getCheckInReviewData.php',
        type: 'POST',
    },
    order:[
        [0, 'desc']
    ],
    columnDefs: [
        {
            "targets": 0,
            "render": function (data, type, row, meta) {
                return row.applyDate;
            }
        },
        {
            "targets": 1,
            "render": function (data, type, row, meta) {
                return row.staffName;
            }
        },
        {
            "targets": 2,
            "render": function (data, type, row, meta) {
                return row.reviewTypeName;
            }
        },
        {
            "targets": 3,
            "render": function (data, type, row, meta) {
                return row.description;
            }
        },
        {
            "targets": 4,
            "render": function (data, type, row, meta) {
                return row.status;
            }
        },
        {
            "targets": 5,
            "render": function (data, type, row, meta) {
                return row.approvalDateTime;
            }
        }
    ],
    searching: true,
    lengthChange: false,
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

const table2 = $('#history-2').DataTable({
    ajax: {
        url: '/includes/staff/getLeaveReviewData.php',
        type: 'POST',
    },
    order:[
        [0, 'desc']
    ],
    columnDefs: [
        {
            "targets": 0,
            "render": function (data, type, row, meta) {
                return row.description;
            }
        },
        {
            "targets": 1,
            "render": function (data, type, row, meta) {
                return row.staffName;
            }
        },
        {
            "targets": 2,
            "render": function (data, type, row, meta) {
                return row.reviewTypeName;
            }
        },
        {
            "targets": 3,
            "render": function (data, type, row, meta) {
                return row.status;
            }
        },
        {
            "targets": 4,
            "render": function (data, type, row, meta) {
                return row.approvalDateTime;
            }
        }
    ],
    searching: true,
    lengthChange: false,
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

const table3 = $('#history-3').DataTable({
    ajax: {
        url: '/includes/staff/getOvertimeReviewData.php',
        type: 'POST',
    },
    order:[
        [0, 'desc']
    ],
    columnDefs: [
        {
            "targets": 0,
            "render": function (data, type, row, meta) {
                return row.description;
            }
        },
        {
            "targets": 1,
            "render": function (data, type, row, meta) {
                return row.staffName;
            }
        },
        {
            "targets": 2,
            "render": function (data, type, row, meta) {
                return row.reviewTypeName;
            }
        },
        {
            "targets": 3,
            "render": function (data, type, row, meta) {
                return row.status;
            }
        },
        {
            "targets": 4,
            "render": function (data, type, row, meta) {
                return row.approvalDateTime;
            }
        }
    ],
    searching: true,
    lengthChange: false,
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
    page('N');
    $( "#tabs" ).tabs();
});

function page(status) {
    $('#page-N').hide();
    $('#page-Y').hide();

    $('#page-btn-N').removeClass('page-active');
    $('#page-btn-Y').removeClass('page-active');

    $(`#page-btn-${status}`).addClass('page-active');

    $(`#page-${status}`).show();
}

function refresh() {
    table.ajax.reload();
    table1.ajax.reload();
    table2.ajax.reload();
    table3.ajax.reload();
}

function verify(reviewType, sId) {
    let url = '/includes/staff/reviewConfirm.php';

    $.colorbox({
        href: url,
        data: {
            reviewType,
            sId
        },
        onClosed: function() {
            refresh();
        }
    });

    return;
}
</script>