<?php
if ($_SESSION['member_id'] == 6) {
    ini_set("display_errors", "On"); 
    error_reporting(E_ALL & ~E_NOTICE);
}

include_once '../../session_check.php' ;
include('../../openadodb.php') ;
// require_once '../../checklist/fpdf/chinese-unicode.php' ;

if ($_SESSION["member_pDep"] == 5 && $_SESSION["member_id"] != 1) {
    $str = " AND tOwner ='".$_SESSION['member_name']."'";
}


$CertifiedId = array();
$sql = "select tCode2,tMemo,tObjKind from tBankTrans WHERE tOk='2' ".$str." group by tVR_Code,tObjKind ORDER BY tVR_Code ASC";
$rs = $conn->Execute($sql);
$i = 0;
while( !$rs->EOF ) { 
    $kind = ($rs->fields['tCode2'] == '大額繳稅' || $rs->fields['tCode2'] == '臨櫃開票')? '【'.$rs->fields['tCode2'].'】':'';

    $CertifiedId[$i]['value'] = $rs->fields['tMemo']."_".$rs->fields['tObjKind'];
    $CertifiedId[$i]['text'] = $rs->fields['tMemo']."_".$rs->fields['tObjKind'].$kind; //
    $CertifiedId[$i]['certifiedId'] = $rs->fields['tMemo'];

    $i++;

    $rs->moveNext();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

<title>出款確認單</title>
<!-- <link rel="stylesheet" type="text/css" href="../../libs/datatables/media/css/demo_page.css" /> -->
<!-- <link rel="stylesheet" type="text/css" href="../../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" /> -->
<!-- <link href="../../css/combobox.css" rel="stylesheet"> -->

<script type="text/javascript" src="../../js/jquery-1.12.4.min.js"></script>
<!-- <script type="text/javascript" src="../../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script> -->
<script type="text/javascript">
$(document).ready(function() {

});
function showCertified(){
     var input = $('input');
    var arr_input = new Array();
    var reg = /.*\[]$/ ; //reg.test($(item).attr("name"))
    $.each(input, function(key,item) {
        if(reg.test($(item).attr("name"))){
            if ($(item).is(':checkbox')) {
                if ($(item).is(':checked')) {
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();
                    }           
                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();         
                }         
            }else{
                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                    arr_input[$(item).attr("name")] = new Array();          
                }
                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();          
            }
        }else {
            arr_input[$(item).attr("name")] = $(item).attr("value");                       
        }
                    
    });

    arr_input['id'] = $("[name='search']").val(); 
    var obj_input = $.extend({}, arr_input);

    $.ajax({
        url: 'export_select_Search.php',
        type: 'POST',
        dataType: 'html',
        data: obj_input,
    })
    .done(function(msg) {
        // console.log(msg);

        $("#show").html(msg);
    });
    
}
function send(){
   

    var input = $('input');
    var arr_input = new Array();
    var reg = /.*\[]$/ ; //reg.test($(item).attr("name"))
    $.each(input, function(key,item) {
        if(reg.test($(item).attr("name"))){
            if ($(item).is(':checkbox')) {
                if ($(item).is(':checked')) {
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();
                    }           
                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();         
                }         
            }else{
                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                    arr_input[$(item).attr("name")] = new Array();          
                }
                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();          
            }
        }else {
            arr_input[$(item).attr("name")] = $(item).attr("value");                       
        }
                    
    });
    arr_input['cat'] = 'msg'; 
    var obj_input = $.extend({}, arr_input);
    // console.log(obj_input);
    $.ajax({
        url: 'export_list_all_result.php',
        type: 'POST',
        dataType: 'html',
        data: obj_input
    })
    .done(function(msg) {
        
        if (msg) {
            alert(msg);
            
        }
         $("#form1").submit();
    });
    
}
</script>
<style type="text/css">
body {
    font-family: "微軟正黑體", "Microsoft JhengHei", "黑體-繁", "Heiti TC", "華文黑體", "STHeiti", "儷黑 Pro", "LiHei Pro Medium", "新細明體", "PMingLiU", "細明體", "MingLiU", "serif";
    line-height: normal;
    font-size: 100%;
}
.cb1 {
    padding:0px 0px;
}
.cb1 input[type="checkbox"] {/*隱藏原生*/
    /*display:none;*/
    position: absolute;
    left: -9999px;
}
.cb1 input[type="checkbox"] + label span {
    display:inline-block;
    width:20px;
    height:20px;
    margin:-3px 4px 0 0;
    vertical-align:middle;
    background:url("images/check_radio_sheet2.png") left top no-repeat;
    cursor:pointer;
    background-size:80px 20px;
    transition: none;
    -webkit-transition:none;
}
.cb1 input[type="checkbox"]:checked + label span {
    background:url("images/check_radio_sheet2.png") -20px top no-repeat;
    background-size:80px 20px;
    transition: none;
    -webkit-transition:none;
}
.cb1 label {
    cursor:pointer;
    display: inline-block;
    margin-right: 10px;
    /*-webkit-appearance: push-button;
    -moz-appearance: button;*/
}
/*button*/
.xxx-button {
    color:#FFFFFF;
    font-size:16px;
    font-weight:normal;
    background-color:#a63c38;
    text-align: center;
    white-space:nowrap;
    height:34px;
    padding:0 10px;
    border:1px solid #a63c38;
    border-radius: 0.35em;
}
.xxx-button:hover {
    background-color:#333333;
    border:1px solid #333333;
}
/*input*/
.xxx-input {
    color:#666666;
    font-size:16px;
    font-weight:normal;
    background-color:#FFFFFF;
    text-align:left;
    height:34px;
    padding:0 5px;
    border:1px solid #CCCCCC;
    border-radius: 0.35em;
}
.xxx-input:focus {
    border-color: rgba(82, 168, 236, 0.8) !important;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
    outline: 0 none;
}
</style>
</head>

<body>

<center>
保證號碼查詢：<input type="text" name="search" onkeyup="showCertified()"  class="xxx-input" placeholder="保證號碼查詢" /><br />    
<div style="border:1px #CCC solid;width: 50% ;margin-top: 50px;padding:5px;">

    請勾選 專屬帳號: 
    <form id="form1" name="form1" method="post" action="export_list_all_result.php" >
       
        <div id="show">


            
        
        <?php foreach ($CertifiedId  as $value):  ?>
            <div style="padding: 5px; text-align: left;margin-left: 30%">
                <span class=""><input type="checkbox" name="CertifiedId[]" value="<?=$value['value']?>" id="CertifiedId<?=$value['certifiedId']?>" ><label for="CertifiedId<?=$value['certifiedId']?>"><span></span><?=$value['text']?></label></span>
            </div>       
        <?php endforeach ?>

        </div>
        <input type="button" name="button" id="button" value="送出" class="xxx-button" onclick="send()" />
    </form>
</div>
 </center>
<!-- <form id="form1" name="form1" method="post" action="export_list_all_result.php">

  <label for="ac"></label>
  <select name="ac" id="ac">
  <?php while( !$rs->EOF ) { ?>
  	<option value="<?php echo $rs->fields["tMemo"];?>"><?php echo $rs->fields["tMemo"];?></option>
  <?php $rs->MoveNext(); } ?>
  
  </select>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="button" id="button" value="送出" /> -->
</form>
</body>
</html>
