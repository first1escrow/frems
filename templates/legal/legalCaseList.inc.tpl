<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {  
   
}) ;
/* 編輯個人權限 */
function edit(no) {
    $('[name="id"]').val(no) ;
    $('form[name="members"]').submit() ;
}
////

/* 新增帳戶 */
function newMember() {
    $('form[name="membersNew"]').submit() ;
}

function searchData(type){
    $("[name='xls']").val('');
    if (type) {
        $("[name='xls']").val(type);
    }

    $("[name='formList']").submit();
}
function editCaseDetail(id){

    location.href = 'legalCaseEdit.php?id='+id ;
    // $.colorbox({iframe:true, width:"80%", height:"90%", href:url,onClosed:function(){
    //     location.href = 'legalCaseList.php';
    // }}) ;
}
function setCaseEnd(id){
    if (confirm("確認是否結案並移轉回經辦?")) {
        alert("已寄送");
        $.ajax({
            url: 'setCaseStatus.php',
            type: 'POST',
            dataType: 'html',
            data: {id:id,cat:2},
        }).done(function(msg) {

            console.log(msg);
        });
    }
    
    
}
function setCaseItemEnd(id){

    if (confirm("此項目確認是否已經完成?")) {
        $.ajax({
            url: 'setCaseItemStatus.php',
            type: 'POST',
            dataType: 'html',
            data: {id: id},
        })
        .done(function(msg) {
            // console.log(msg);
            alert(msg);
            location.href = 'legalCaseList.php';
        });
    
    }
    
}
</script>
<style>

.memberTB {
    border: 1px solid #ccc;
    padding: 5px;
    font-size: 10pt;
    font-weight: bold;
    text-align: center;
    background-color: #EEE0E5 ;
}
.memberCell {
    padding: 5px;
    font-size: 9pt;
    text-align: center;
    border: 1px solid #ccc;
}

#table tbody td{
    padding: 5px;
    font-size: 9pt;
    text-align: center;
    border: 1px solid #ccc;
}

</style>
</head>
<body id="dt_example">
<div id="wrapper">
    <div id="header">
        <table width="1000" border="0" cellpadding="2" cellspacing="2">
            <tr>
                <td width="233" height="72">&nbsp;</td>
                <td width="753">
                    <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                        <tr>
                            <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
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
                        <br/> 
                        <h3>&nbsp;</h3>
                        <div id="container">
                            <h1>法務列管案件</h1>
                            <form name="formList" method="POST">
                            
                            <div style="font-size:10pt;width:100%;margin:10px;">
                                案件狀態:
                                 <{html_options name=CaseStatus options=$menu_Status selected=$CaseStatus onchange="searchData('')"}>
                                
                                    <input type="button" value="匯出EXCEL" onclick="searchData('xls')">
                                
                                 <input type="hidden" name="xls" value="">
                            </div>
                                                        
                            
                                <table cellspacing="0" id="table" style="width:100%;" >
                                    <thead>
                                        <tr >
                                            <td class="memberTB" width="10%">保證號碼</td>
                                            <td class="memberTB" width="10%">到期日</td>
                                            <td class="memberTB" width="50%">需辦事項</td>
                                            <td class="memberTB" width="10%">狀態</td>
                                            <td class="memberTB" width="10%">功能</td>
                                            <td class="memberTB" width="10%">&nbsp;</td>
                                        </tr>
                                       <{foreach from=$list key=key item=item}>
                                        <tr>
                                            <td rowspan="<{count($item.data)+1}>"  class="memberCell">
                                                <a href="../escrow/formbuyowneredit.php?id=<{$key}>" target="_blank"><{$key}></a>
                                            </td>
                                            <{if count($item.data) > 0}>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            
                                            <{else}>
                                            <td class="memberCell" colspan="4">無事項</td>

                                            <{/if}>
                                            <td rowspan="<{count($item.data)+1}>" class="memberCell">
                                                <span><input type="button" value="編輯" onclick="editCaseDetail('<{$key}>')"></span>
                                                <span><input type="button" value="移轉至經辦" onclick="setCaseEnd('<{$key}>')"></span>
                                                <br>
                                            </td>
                                            
                                        </tr>
                                        <{if count($item.data) > 0}>
                                            <{foreach from=$item.data key=key2 item=item2}>
                                           
                                            <tr style="background-color: <{$item2.detailColor}>">
                                               
                                                <td class="memberCell"><{$item2.detailEndDay}></td>
                                                <td class="memberCell"><{$item2.detailNote}></td>
                                                <td class="memberCell"><{$item2.detailStatus}></td>
                                                <td class="memberCell">
                                                    <input type="button" value="已完成" onclick="setCaseItemEnd('<{$item2.detailId}>')">
                                                </td>
                                                
                                            </tr>
                                            <{/foreach}>
                                        
                                        <{/if}>
                                        <{/foreach}>

                                    </thead>
                                </table>
                                
                            </form>
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