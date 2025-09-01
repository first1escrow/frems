<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出帳建檔列表</title>
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<script src="../../js/datepickerRoc.js"></script>
<script src="../js/jquery.colorbox.js"></script>
<script type="text/javascript" language="javascript" src="/libs/datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<link rel="stylesheet" href="../../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_page.css" />
<link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_table.css" />
<link rel="stylesheet" href="../../css/datepickerROC.css" />
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />

<script>
$(document).ready(function(){
    $(".firstbank").hide();

    var aSelected = [];
    $("#book").dataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "POST", 
                    "sAjaxSource": " listManagerIBook.php?year="+$("[name='year']").val(),
                    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                        
                        if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                            $(nRow).addClass('row_selected');
                        }
                    }
    });

    /* Click event handler */
    $('#book tbody tr').on('click', function () {
        let id = this.id;
        let index = jQuery.inArray(id, aSelected);

        if (index === -1) {
            aSelected.push(id);
        } else {
            aSelected.splice(index, 1);
        }

        $('#totalCount').html(aSelected.length);
        $(this).toggleClass('row_selected');
    });

    $("#pdf").on('click', function() {
        let ck = 0;

        $(".check").css('background-color', '#FFFFFF');
        $(".check").each(function() {
            if ($(this).val() == '') {
                ck =1;
                $(this).css('background-color', '#FF6E6E');
            }
        });

        if (ck ==0 ) {
            $("[name='form_search']").attr('action', 'IBookSearchFirstPDf.php').submit();
        } else {
            alert("有欄位未填寫欄位");
        }
    });
});

function bModify(id) {
    let url = 'IBookEdit.php?id=' + id;
    $.colorbox({
        iframe: true, 
        width: "1200px", 
        height: "90%", 
        href: url,
        onClosed: function() {
            location.href ='IBookManagerList.php';
        }
    });
}

function Audit(id,pdf){
    let url = "statusIBook.php?id=" + id + "&f=list";

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'html',
        data: {id: id},
    })
    .done(function(msg) {
        if (msg == 'OK') {
            alert("審核成功");
            pdfBook(id,pdf);
            location.href ='IBookManagerList.php';
        } else {
            alert("審核失敗，請重新再試");
        }
    });
}

function pdfBook(id,p){
    $("#pdf [name='id']").val(id);
    $("[name='pdf']").attr('action', p);
    $("[name='pdf']").submit();
}

function bDel(id){
    if (confirm('確定刪除?')) {
        $.ajax({
            url: 'IBookDel.php',
            type: 'POST',
            dataType: 'html',
            data: {'id': id},
        })
        .done(function(txt) {
            alert(txt);
            location.href ='IBookManagerList.php';
        }).fail(function(jqXHR, textStatus) {
            alert(jqXHR.responseText);
        });
    }
}

function show()
{
    let val = $('[name="switch"]').val();
    if (val == 1) {
        $(".firstbank").slideUp("slow");
        $('[name="switch"]').val(0);
    } else {
        $(".firstbank").slideDown("slow");
        $('[name="switch"]').val(1);
    }
}

function search(){
    location.href="IBookManagerList.php?year="+$("[name='year']").val();
}
</script>
<style>
.tb1{
        border-radius:0.5em 0.5em 0.5em 0.5em;
        padding: 10px;
        background-color: #FFF;
        border-color: #000;
}
.focus_page {
    vertical-align: top;
    display:inline-block;
}
.focus_page li {
    float:left;
    margin: 0 3px 0 3px;
}
.focus_page li a {
    color: #b48400;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #FFFFFF;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
}
.focus_page li a:hover {
    color: #00008F;
    font-size:16px;
    background-color: #FFFF96;
    border: 1px solid #FFFF96;
}
.focus_page li.focus_end a {
    color: #00008F;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #FFFF96;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #FFFF96;
}

.btn {
    color: #000;
    font-family: Verdana;
    font-size: 12px;
    font-weight: bold;
    line-height: 12px;
    background-color: #FFBFBF;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn:hover {
    color: #00008F;
    font-size:12px;
    background-color: #FFFF96;
    border: 1px solid #FFFF96;
}
.btn.focus_end{
    color: #000;
    font-family: Verdana;
    font-size: 12px;
    font-weight: bold;
    line-height: 12px;
    background-color: #FFFF96;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #FFFF96;
    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.firstbank{
    float: left;
    /*border: 1px solid #FFFF96;*/
    width: 1400px;
    height:22px;
}
 .tb{
    text-align: center;
    border: solid 1px #ccc;

}
.tb th{
    background-color:#E4BEB1;
    padding-top: 5px;
    padding-bottom: 5px;
            /**/
}

.btn2 {
    color: #000;
    font-family: Verdana;
    font-size: 18px;
    font-weight: bold;
    line-height: 18px;
    background-color: #EAEBFF;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn2:hover {
    color: #00008F;
    font-size:18px;
    background-color: #FFFF96;
    border: 1px solid #FFFF96;
}
.btn2.focus_end{
    color: #000;
    font-family: Verdana;
    font-size: 18px;
    font-weight: bold;
    line-height: 18px;
    background-color: #FFFF96;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #FFFF96;
    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.xxx-select {
            color:#666666;
            font-size:16px;
            font-weight:normal;
            background-color:#FFFFFF;
            text-align:left;
            height:34px;
            padding:0 0px 0 5px;
            border:1px solid #CCCCCC;
            border-radius: 0em;
}
.xxx-select:focus {
            border-color: rgba(82, 168, 236, 0.8) !important;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
            outline: 0 none;
}
</style>
</head>

<body>
<div style="width:1400px; margin-bottom:5px; height:22px; background-color: #CCC">
<div style="float:left;margin-left: 10px;"> <a href="IBookList.php">指示書</a></div>
<div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
<{if $smarty.session.member_pDep == 5 || $smarty.session.member_id == 6 }>
<div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
<{/if}>
<{if $smarty.session.member_bankcheck ==1 }>

<div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/list_ok.php">已審核列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/pay_check.php">銀行出款確認</a></div>
<div style="float:left;margin-left: 10px;"> <font color=red><strong>指示書列表</strong></font> </div>
<div style="float:left; margin-left: 10px;"><a href="/bank/returnMoneyList.php">返還代墊列表</a></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/sms_check.php">簡訊發送</a></div>
<div style="float:left; margin-left: 10px;"> 【<a class='iframe2' href="/bank/report/report.php">銀行對帳單</a>】</div>
<{/if}>
</div>
<div>
<form  method="POST" name="pdf" id="pdf" target="_blank">
   <input type="hidden" name="id" value=""/>
</form>
<form  method="POST" name="reload">
</form>


<br>
<div style="width:1400px">
<div style="margin-bottom: 10px;">
    年份
    <{html_options name=year options=$menuYear selected=$year class="xxx-select" onchange="search()"}>
</div>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="book">
    <thead>
        <tr>
            <th width="12%">指示日期</th>
            <th width="10%">銀行</th>
            <th width="8%">指示書<br>編號</th>
            <th width="10%">指示書<br>類別</th>
            <th width="10%">金額</th>
            <th width="8%">狀態</th>
            <th width="8%">建立者</th>
            <th width="8%">待審核<br>修改者</th>
            <th width="8%">已審核<br>修改者</th>
            <th width="17%">編輯</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="10" class="dataTables_empty">Loading data from server</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="10"></th>
        </tr>
    </tfoot>
</table>
</div>
<br><br><br>

<br><br>
<{if $smarty.session.pBankBook > 1}>
<center>
<div style="float:left;width: 1280px;">
    <span style="font-size:22px;"><b>一銀、台新撥款核對表</b></span>    
    <input type="button" value="縮放" onclick="show()" class="btn2" />
    <input type="hidden" name="switch" value="0">
</div>
<div class="firstbank">
   <form name="form_search" method="post" target="_blank">
        <table border="0" cellspacing="5" cellpadding="5" class="tb"> 
            <tr>
                <td>銀行</td>
                <td>
                    <label><input type="radio" name="bank" id="" value="1"/>一銀(桃園)</label>
                    <label><input type="radio" name="bank" id="" value="7" checked/>一銀(城東)</label>
                    <label><input type="radio" name="bank" id="" value="5" />台新</label>
                </td>
            </tr>                                    
            <tr>
                <td>日期起迄︰</td>
                <td>
                    <input type="text" name="StartDate" class="datepickerROC check" style="width:100px;"> (起)&nbsp;～&nbsp;<input type="text" name="EndDate" class="datepickerROC check" style="width:100px;"> (迄)
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td>指示日期：</td>
                <td align="left"><input type="text" name="Date" class="datepickerROC check" style="width:100px;"></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="button" id="pdf" value="產出PDF" class="btn">
                    <input type="hidden" name="ck" value="1">
                </td>
            </tr>
        </table>
    </form>
</div>
</center>
<{/if}>
</div>
</body>
</html>
