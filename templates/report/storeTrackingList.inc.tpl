<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {
    $('[name="branchA"]').combobox() ;
    $('[name="branchB"]').combobox() ;

  
    
    $('#formList').click(function() {
       
        $('[name="export"]').val('ok') ;
        $('[name="formList"]').submit() ;
    }) ;
    
    $('#formList').button({
        icons:{
            primary: "ui-icon-document"
        }
    }) ;
}) ;

//取得縣市的鄉鎮市
function get_area() {
    var url = 'zipArea.php' ;
    var _city = $('[name="city"] option:selected').val() ;
    
    if (_city == "") {
    
    }
    else {
        $.post(url,{c:_city},function(txt) {
            $('[name="area"]').empty().html(txt) ;
        }) ;
    }
}
function searchStore(){
    $("#branchArea").css('display', 'none');
    $("#scrivenerArea").css('display', 'none');
    $("#showBrach").empty();
    $("#showScrivener").empty();

    
    if ($('[name="storeType"]:checked').val() == 1) {
        if ($('[name="cat"]:checked').val() == 'b') {
            $("#branchArea").css('display', "block");
        }else if ($('[name="cat"]:checked').val() == 's') {
            $("#scrivenerArea").css('display', "block");
        }
    }
}
function add(cat){

    if (cat == 'b') {
        var val = $('[name="branch"]').val();
        
        var text = $('#branch option[value="'+val+'"]').text();     
        if ($("#b"+val).length == 1) {
            alert('已加入店家搜尋條件');
            return false;
        }

        $("#showBrach").append('<div id="'+cat+val+'" class="addStore bStore"><input type="hidden" name="branchId[]" value="'+val+'"><a href="#showBrach" onClick="del(\''+cat+'\','+val+')" >(刪除)</a>'+text+'</div><div style="clear:both"></div>');

        
    }else if(cat == 's'){
        var val = $('[name="scrivener"]').val();
        var text = $('#scrivener option[value="'+val+'"]').text(); 

        if ($("#s"+val).length == 1) {
            alert('已加入地政士搜尋條件');
            return false;
        }
        
        $("#showScrivener").append('<div id="'+cat+val+'" class="addStore sStore"><input type="hidden" name="scrivenerId[]" value="'+val+'"><a href="#showScrivener" onClick="del(\''+cat+'\','+val+')">(刪除)</a>'+text+'</div><div style="clear:both"></div>');
        
        
    }
}
////



</script>
<style>
.ui-autocomplete-input {
    width:210px;
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
                        <h1>未進案名單</h1>                      
                            <form name="formList" method="POST">
                                <div>

                                    <div style="margin:0px auto;width:500px;text-align:left;padding:10px;border:1px solid #ccc;">
                                        <div style="height:30px;">
                                            選擇地區：
                                            <span>
                                            <select name="city" style="width:90px;" onchange="get_area()">
                                            <option value="" selected="selected">全部縣市</option>
                                            <{$menuArea}>
                                            </select>
                                            </span>
                                            <span>
                                            <select name="area" style="width:90px;">
                                            <option value="" selected="selected">全區</option>
                                            </select>
                                            </span>
                                       
                                        </div>
                                        <div style="height:30px;">
                                            選擇身分
                                            <input type="radio" name="cat" value="b" checked onclick="searchStore()">仲介店
                                            <input type="radio" name="cat" value="s" onclick="searchStore()">地政士
                                        </div>
                                        <div>
                                            店家選擇
                                             <{html_radios name=storeType options=$menuStoreType selected=$storeType onclick="searchStore()"}>

                                             <span id="branchArea" style="display: none">
                                                 仲介店名　
                                                    <select name="branch" id="branch" class="easyui-combobox">
                                                    <option></option>
                                                    <{$menuBranch}>
                                                    </select>
                                                    <input type="button" value="增加" onclick="add('b')" class="xxx-button">
                                                    <font color="red">※選擇完後請按下增加</font>
                                                    <div id="showBrach"></div>
                                             </span>

                                             <span id="scrivenerArea" style="display: none">
                                                 地政士名稱
                                                    <select name="scrivener" id="scrivener" class="easyui-combobox">
                                                    <option></option>
                                                    <{$menuScrivener}>
                                                    </select>
                                                    <input type="button" value="增加" onclick="add('s')" class="xxx-button">
                                                    <font color="red">※選擇完後請按下增加</font>
                                                    <div id="showScrivener"></div>
                                             </span>
                                        </div>

                                        
                                       
                                        
                                    </div>
                                    
                                    <div style="height:5px;">&nbsp;</div>
                                    
                                    
                                    
                                    <div style="height:50px;">&nbsp;</div>
                                    
                                    <div style="text-align:center;">
                                        <input type="hidden" name="export">
                                        <!--<input type="button" value="輸出報表" style="width:100px;" onclick="formList()">-->
                                        <button id="formList">輸出報表</button>
                                    </div>
                                    
                                    <div style="height:10px;">&nbsp;</div>
                                    
                                </div>
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