<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css" rel="stylesheet"
        type="text/css">

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            searching: false,
            dom: 'Bfrtip',
            buttons: [
                'excel'
            ],
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
    });

    function getQualifiedList() {
        let _year = $('[name="_year"] option:selected').val();
        let _month = $('[name="_month"] option:selected').val();
        let _level = $('[name="_level"] option:selected').val();

        $('[name="year"]').val(_year);
        $('[name="month"]').val(_month);
        $('[name="level"]').val(_level);

        $('#form1').submit();
    }
    </script>
    <style>

    </style>
</head>

<body id="dt_example">
    <div style="padding-bottom: 20px;text-align: right;">
        <span style="padding-right:10px;">
            查詢年度：<{html_options name="_year" options=$yearOptions selected=$selectedYear}>
        </span>
        <span style="padding-right:20px;">
            生日月份：<{html_options name="_month" options=$monthOptions selected=$selectedMonth}>
        </span>
        <span style="padding-right:10px;">
            地政士等級：<{html_options name="_level" options=$levelOptions selected=$selectedLevel}>
        </span>
        <button style="padding:5px;" onclick="getQualifiedList()">查詢</button>
    </div>

    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>編號</th>
                <th>姓名</th>
                <th>生日</th>
                <th>等級</th>
                <th>業務</th>
            </tr>
        </thead>
        <tbody>
            <{foreach from=$data key=k item=v}>
            <tr>
                <td><{$v.code}></td>
                <td><{$v.name}></td>
                <td><{$v.birthday}></td>
                <td><{$v.level}></td>
                <td><{$v.sales}></td>
            </tr>
            <{/foreach}>
        </tbody>
        <tfoot>
            <tr>
                <th>編號</th>
                <th>姓名</th>
                <th>生日</th>
                <th>等級</th>
                <th>業務</th>
            </tr>
        </tfoot>
    </table>

    <form id="form1" method="POST">
        <input type="hidden" name="year">
        <input type="hidden" name="month">
        <input type="hidden" name="level">
    </form>

</body>

</html>