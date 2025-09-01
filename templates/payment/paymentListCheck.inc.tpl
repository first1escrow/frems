<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>付款確認</title>
<link rel="stylesheet" href="/css/colorbox.css" />
<script src="/js/jquery-1.7.2.min.js"></script>
<script src="/js/jquery.colorbox.js"></script>

<script>
$(document).ready(function(){
                //Examples of how to assign the ColorBox event to elements
                $(".group1").colorbox({rel:'group1'});
                $(".group2").colorbox({rel:'group2', transition:"fade"});
                $(".group3").colorbox({rel:'group3', transition:"none", width:"75%", height:"75%"});
                $(".group4").colorbox({rel:'group4', slideshow:true});
                $(".ajax").colorbox();
                $(".youtube").colorbox({iframe:true, innerWidth:425, innerHeight:344});
                $(".iframe").colorbox({
                    iframe:true, width:"1100", height:"500",                    
                    onClosed:function(){ reload_page(); }
                });
                $(".iframe2").colorbox({
                    iframe:true, width:"1000", height:"900"                 

                });
                $(".iframe3").colorbox({
                    iframe:true, width:"450", height:"500"                  

                });
                $(".inline").colorbox({inline:true, width:"50%"});
                $(".callbacks").colorbox({
                    onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                    onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                    onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                    onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                    onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                });
                
                //Example of preserving a JavaScript event for inline calls.
                $("#click").click(function(){ 
                $('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
                    return false;
                });
                $("#export_file").click(function() {
                    $.colorbox({    
                        iframe:true, width:"1100", height:"500",                        
                        href: "/bank/_export_all.php?x=<?php echo $b;?>&y=<?php echo $export;?>",
                        onClosed:function(){ reload_page(); }
                    });
                });

});
function reload_page(){
    location.reload();
}
function open_w(url){   
    window.open (url , 'newwindow', 'height=500, width=1100, top=250, left=250, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')    
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function open_w_all(x,y){   
    // x -> bank , y -> export
    url = '/bank/_export_all.php?x=' + x  + "&y=" + y;
    window.open (url , 'newwindow2', 'height=600, width=1100, top=150, left=350, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')   
}
</script>
<style>
li {
    height:20px;
}

</style>
</head>

<body>
<{include file='paymentMenu.inc.tpl'}>
<div style="margin-top: 10px;">
    <div id="bank_c">

        <div style="overflow:auto; height:500px;border: 1px solid #666;background-color: #CCC;padding: 8px; x">
            <ul>
            <{foreach from=$data key=key item=item}>    
            <li>
                <a class="iframe" href="paymentDetail.php?sn=<{$item.tExport_nu}>&ts=<{$item.tExport_time}>&tm=<{$item.M}>">
                    <strong>媒體檔匯出時間: </strong><{$item.tExport_time}>  <strong>出帳金額:</strong> <{$item.M}> 元&nbsp;
                    (<{$item.tPayOk}>)<span><{$item.tObjKind2}><span></a>
            </li>
            <{/foreach}>
            </ul>
        </div>
    </div>
</div>
</body>
</html>