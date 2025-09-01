<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

<title>出帳建檔列表</title>
<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script src="/js/jquery.colorbox.js"></script>
<script type="text/javascript" language="javascript" src="/libs/datatables/media/js/jquery.dataTables.js"></script>
<link rel="stylesheet" href="/css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_page.css" />
<link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_table.css" />
<script>
$(document).ready(function(){
    let aSelected = [];

    $("#book").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST", 
        "sAjaxSource": " listIBook.php",
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                $(nRow).addClass('row_selected');
            }
        }
    });

    /* Click event handler */
    $('#book tbody tr').live('click', function () {
        let id = this.id;
        let index = jQuery.inArray(id, aSelected);

        if (index === -1) {
            aSelected.push( id );
        } else {
            aSelected.splice(index, 1);
        }

        $('#totalCount').html(aSelected.length);
        $(this).toggleClass('row_selected');
    });
});

function bModify(id) {
    $('#formModify input[name="id"]').val(id);
    $('#formModify').submit();

    let url = 'AddIBook2.php?id='+id ;
    $.colorbox({
        iframe:true, width:"1200px", height:"90%", href:url,onClosed:function() {
            location.href ='IBookList.php';
        }
    });
}

function pdfBook(id, p) {
    $("#pdf [name='id']").val(id);
    $("[name='pdf']").attr('action', p);
    $("[name='pdf']").submit();
}

function Add() {
    let url = 'AddIBook2.php?type=add' ;
    $.colorbox({
        iframe:true, width:"1200px", height:"90%", href:url,onClosed:function() {
            location.href ='IBookList.php';
        }
    });
}

function bDel(id) {
    if (confirm('確定刪除?')) {
        $.ajax({
            url: 'IBookDel.php',
            type: 'POST',
            dataType: 'html',
            data: {'id': id},
        })
        .done(function(txt) {
            if (txt) {
                alert('已刪除');
            }

            location.href ='IBookList.php';
        });
    }
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
.btn {
    color: #000;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #FFBFBF;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
    border-radius:0.5em 0.5em 0.5em 0.5em;
}
.btn:hover {
    color: #00008F;
    font-size:16px;
    background-color: #FFFF96;
    border: 1px solid #FFFF96;
}
.btn.focus_end{
    color: #000;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #FFFF96;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #FFFF96;
    border-radius:0.5em 0.5em 0.5em 0.5em;
}
</style>
</head>

<body>
<div style="width:1024px; margin-bottom:5px; height:22px; background-color: #CCC">
    <div style="float:left;margin-left: 10px;"> <font color=red><strong>指示書</strong></font> </div>
    <div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
    <{if $smarty.session.member_pDep == 5 || $smarty.session.member_id == 6 }>
    <div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
    <{/if}>

    <{if $smarty.session.member_bankcheck ==1 }>
    <div style="float:left; margin-left: 10px;"> <a href="/bank/list.php">未審核列表</a></div>
    <div style="float:left; margin-left: 10px;"> <a href="/bank/list_ok.php">已審核列表</a></div>
    <div style="float:left; margin-left: 10px;"> <a href="/bank/pay_check.php">銀行出款確認</a></div>

    <{if $smarty.session.pBankBook != 0}>
    <div style="float:left;margin-left: 10px;"> <a href="IBookManagerList.php">指示書列表</a> </div>
    <{/if}>

    <div style="float:left; margin-left: 10px;"> <a href="/bank/sms_check.php">簡訊發送</a></div>
    <div style="float:left; margin-left: 10px;"> 【<a class='iframe2' href="/bank/report/report.php">銀行對帳單</a>】</div>
    <{/if}>
</div>
<div>
    <form  method="POST" name="pdf" id="pdf" target="_blank">
        <input type="hidden" name="id" value=""/>
    </form>

    <form  method="POST" action="AddIBook2.php" name="formAdd" id="formAdd" target="_blank">
        <input type="hidden" name="type" value="add" />
    </form>

    <form  method="POST" name="reload">
    </form>

    <br><br>

    <div style="width:1016px">
        <div style="text-align:left">
            <input type="button" value="新增" onclick="Add()" class="btn" />      
        </div>

        <br /><br />
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="book">
            <thead>
                <tr>
                    <th width="15%">指示日期</th>
                    <th width="10%">銀行</th>
                    <th width="8%">指示書<br>編號</th>
                    <th width="10%">指示書<br>類別</th>
                    <th width="10%">金額</th>
                    <th width="8%">狀態</th>
                    <th width="8%">建立者</th>
                    <th width="14%">編輯</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="dataTables_empty">Loading data from server</td>
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
</body>
</html>
