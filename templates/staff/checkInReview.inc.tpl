<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>簽核紀錄</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<!--Google icon-->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<style>
table {
    text-align: center;
    font-size: 10pt;
}
</style>
</head>
<body>
    <h1>簽核列表</h1>
    <div id="container">
        <table id="example" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>申請日期</th>
                    <th>申請人</th>
                    <th>補簽日期</th>
                    <th>簽到/簽退</th>
                    <th>核准人</th>
                    <th>事由</th>
                    <th>狀態</th>
                    <th>待審核</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="8" class="dataTables_empty">讀取資料中...</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
<script type="text/javascript">
const url = '/includes/staff/checkInReviewData.php';
const table = $('#example').DataTable({
    ajax: {
        url,
        type: 'POST'
    },
    columns: [
        { "data": "sCreatedAt" },
        { "data": "staffName" },
        { "data": "sApplyDate" },
        { "data": "applyType" },
        { "data": "approvalName" },
        { "data": "sReason" },
        { "data": null },
        { "data": null }
    ],
    columnDefs: [
        { className: "dt-head-center", targets: [ 0, 1, 2, 3, 4, 5, 6, 7 ] },
        {
            "targets": 6,
            "render": function (data, type, row, meta) {
                if (row.sStatus == 'Y') {
                    return '<span style="font-size:10pt;">已通過</span>';
                }

                if (row.sStatus == 'N') {
                    return '<span style="font-size:10pt;">未審核</span>';
                }

                if (row.sStatus == 'R') {
                    return '<span style="font-size:10pt;">未通過</span>';
                }

                return row.sStatus;
            }
        },
        {
            "targets": 7,
            "orderable": false,
            "render": function (data, type, row, meta) {
                if (row.sStatus == 'N') {
                    return '<a href="JavaScript:void(0);" onclick="approve(' + row.sId + ')">審核</a>';
                }

                return '';
            }
        }
    ],
    // createdRow: function( row, data, dataIndex ) {
    //     $(row).css({'background-color':data.css});
    // },
    order: [[6, 'asc']],
    searching: false,
    lengthChange: false,
    processing: true,
    serverSide: true,
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

});

function approve(id) {
    $.ajax({
        url: '/includes/staff/checkInReviewApprove.php',
        type: 'POST',
        data: {
            id
        },
        success: function(data) {
            location.href = decodeURI(data);
            return;
        },
        error: function(err) {
            alert('操作失敗(' + err.responseText + ')');
        }
    });
}
</script>