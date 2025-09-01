<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>

<{include file='meta.inc.tpl'}> 

<script type="text/javascript">
$(document).ready(function() {
    var aSelected = [];       
    var zip = "<{$search_zip}>";
    var ss = $('#salesman').val() ;
    var city = $("[name='country']").val();
    var year = $("[name='sYear']").val();
    var tg = $("[name='target']").val();
    var rep = $("[name='receipt']").val();
    /* Init the table */
    // console.log("sYear="+year+"&salesman="+ss);
    $("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST", 
        "sAjaxSource": "/includes/maintain/scrivenerPresentlisttb.php?sYear="+year+"&salesman="+ss+"&target="+tg+"&receipt="+rep+"&status="+$("[name='status']").val(),
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                $(nRow).addClass('row_selected');
            }
        },
        <{if $smarty.session.member_pDep == 4 || $smarty.session.member_pDep == 1}>
        "aaSorting": [[7, "asc"], [10, "desc"]],
        <{else}>
        "aaSorting": [[9, "desc"]]
        <{/if}>
        
    });
    /* Click event handler */
    $('#example tbody tr').live('click', function () {
        var id = this.id;
        var index = jQuery.inArray(id, aSelected);
        if ( index === -1 ) {
            aSelected.push( id );
        } else {
            aSelected.splice( index, 1 );
        }
        $('#totalCount').html(aSelected.length);
        $(this).toggleClass('row_selected');
    });
    $('#example tbody tr').live('dblclick', function () {
        var tmp = this.id.replace('row_', '');
        var url = 'ScrivenerPresentApply.php?cat=edit&sId='+tmp ;
        $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
            location.href ='scrivenerPresent.php';
        }}) ;            
    });
    $('#add').live('click', function () {
        var url = 'ScrivenerPresentApply.php?cat=add' ;
        $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
            location.href ='scrivenerPresent.php';
        }}) ;
    });
    $('#gift').live('click', function () {
        var url = 'PresentEdit.php' ;
        $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
            location.href ='scrivenerPresent.php';
        }}) ;
    });

     $('#lock').live('click', function () {
        var url = 'ScrivenerPresentApplyLock.php' ;
        $.colorbox({iframe:true, width:"50%", height:"50%", href:url,onClosed:function(){
            location.href ='scrivenerPresent.php';
        }}) ;
    });

    // $("#salesman").on('change', function(event) {
    //     var year = $("[name='sYear']").val();
    //     var ss = $('#salesman').val() ;         
    //     location.href= 'scrivenerPresent.php?sYear='+year+'&salesman='+ss;
    // });
    // $("[name='sYear']").on('change', function(event) {
                    
        
    // });
               
    $('#add').button( {
        icons:{
            primary: "ui-icon-info"
        }
    });
    $('#del').button( {
        icons:{
            primary: "ui-icon-locked"
        }
    });
    $('#loading').dialog('close');
});
function getData(){
    var year = $("[name='sYear']").val();
    var ss = $('#salesman').val() ;
    var tg = $("[name='target']").val();
     var rep = $("[name='receipt']").val();

     // console.log()
    location.href= 'scrivenerPresent.php?sYear='+year+'&salesman='+ss+"&target="+tg+"&receipt="+rep+"&status="+$("[name='status']").val();  
}      
function checkAll(){
    if ($("[name='checkId[]']").prop('checked') == true) {
        $("[name='checkId[]']").prop('checked', false);
    }else{
        $("[name='checkId[]']").prop('checked', true);
    }
}

function setStatus(){

    var id = new Array();
    $('input:checkbox:checked[name="checkId[]"]').each(function(i) { id[i] = this.value; });
    $.ajax({
        url: '../includes/maintain/setScrivenerStatus.php',
        type: 'POST',
        dataType: 'html',
        data: {id: id},
    }).done(function(msg) {
        
        alert(msg);
        getData();
    });
   
}    
</script>
<style>
    .grade{
        text-align: center;
    }
</style>
</head>
<body id="dt_example">
    <form name="form_edit" id="form_edit" method="POST">
        <input type="hidden" name="id" id="id" value='3' />
    </form>
    <form name="form_add" id="form_add" method="POST">
    </form>
    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
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
                                <div id="menu-lv2"></div><br/> 
                                <h1>地政士生日禮</h1>                    
                                <div id="container">
                                    <div id="dialog" class="easyui-dialog" title="" style="display:none"></div>
                                    <div>
                                        <{if $smarty.session.member_ScrivenerLevel != 3  && $smarty.session.member_ScrivenerLevel != 5}>
                                            <button id="add">申請</button>
                                        <{/if}>
                                        <{if $smarty.session.member_ScrivenerLevel > 2}>
                                            <input type="button" value="申請對象鎖住" id="lock"  >
                                        <{/if}>
                                        <{if $smarty.session.pScrivenerBirthdayForSales == 1}>
                                            <a href="../report/scrivenerBirthdayForSales.php">生日查詢</a>
                                        <{/if}>
                                        &nbsp;&nbsp;                                   
                                        <br>
                                        <div align="right"> 
                                            年度：<{html_options name=sYear values=$menuYear output=$menuYearOutput selected=$year  onchange="getData()"}>
                                            <{if $smarty.session.member_pDep != 7}>                                   
                                                業務：<select id="salesman" name='salesman' onchange="getData()"><{$salesman}></select>
                                                達標：
                                                <select name="target" id="" onchange="getData()">
                                                   <{$menuTarget}>
                                                </select>
                                                
                                                收據是否繳回：
                                                <select name="receipt" id="" onchange="getData()">
                                                    <{$menuReceipt}>
                                                </select>
                                                狀態:
                                                <select name="status" id="" onchange="getData()">
                                                    <{$menuStatus}>
                                                </select>
                                            <{else}>
                                                <input type="hidden" name="salesman" id="salesman">
                                                <input type="hidden" name="target" id="target">
                                                <input type="hidden" name="receipt">
                                                <input type="hidden" name="status">
                                            <{/if}>


                                        </div>
                                        <{if $smarty.session.member_pDep == 4 || $smarty.session.member_pDep == 1}>

                                            <div style="border:solid 1px #999;margin:5px;padding: 5px;">
                                                <span>業務主管專區</span>
                                                <input type="button" value="全選/取消全選" onclick="checkAll()">
                                                <input type="button" value="審核通過" onclick="setStatus()">
                                            </div>
                                                
                                        <{/if}>
                                        <br>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                            <thead>
                                                <tr>
                                                    <{if $smarty.session.member_pDep == 4 || $smarty.session.member_pDep == 1}>
                                                        <td width="5%">選項</td>
                                                    <{/if}>
                                                    <th width="8%" align="cneter">編號</th>
                                                    <th width="10%" align="cneter">姓名</th>
                                                    <th width="10%" align="cneter">生日</th>
                                                    <th width="10%"align="center">品項</th>
                                                    <th width="10%" align="center">金額</th>
                                                    <th width="8%" align="cneter">申請人</th>
                                                    <th width="8%" align="cneter">審核人</th>
                                                    <th width="10%" align="cneter">狀態</th>
                                                    <th width="10%" align="cneter">收據是否繳回</th>
                                                    <th width="14%" align="cneter">申請日期</th> 

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="11" class="dataTables_empty">Loading data from server</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="11"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div id="footer" style="height:50px;">
                                    <p>2012 第一建築經理股份有限公司 版權所有</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>


</body>
</html>