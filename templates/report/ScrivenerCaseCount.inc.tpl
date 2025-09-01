<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
    <meta http-equiv="X-UA-Compatible" content="IE=9"/>
<link rel="stylesheet" href="colorbox.css" />
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />

<{include file='meta.inc.tpl'}>         
<script type="text/javascript">
$(document).ready(function() {
    var aSelected = [];
    
    $( "#dialog" ).dialog({
        autoOpen: false,
        modal: true,
        minHeight:50,
        show: {
            effect: "blind",
            duration: 1000
        },
        hide: {
            effect: "explode",
            duration: 1000
        }
    });
    $(".ui-dialog-titlebar").hide() ;
    

});





        </script>
        <style>
        #export{
            width: 80px;
            height: 20px;
            font-size: 15px;
        }
        #b{
            padding-top: 10px;
            padding-bottom: 10px;


        }
        .tb{
           
            border: solid 1px #ccc;

        }
        .tb th{
           
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .tb td{
           
            padding-top: 5px;
            padding-bottom: 5px;
        }
        </style>
    </head>
    <body id="dt_example">
       
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
                                        <div id="menu-lv2">
                                                        
                                        </div>
                                        <br/> 
                                        <h3>&nbsp;</h3>
                                        <div id="container">
                                        <div id="dialog"></div>
<div>
<center>
<form name="mycal" method="POST">

<h1>代書庫存有效合約書</h1>
 <table border="0" cellspacing="0" cellpadding="0" class="tb">
                                             
                                                    <tr>
                                                        <th>年度：</th>
                                                        <td>
                                                        <select name="date_start_y" id="">
                                                        <{for $i =$smarty.now|date_format:"%Y" to 2012 step -1}>
                                                            <{$year = ($i - 1911)}>
                                                            <option value="<{$year+1911}>"<{if $year == $smarty.now|date_format:"%Y"}> selected="selected"<{/if}>><{$year}></option>
                                                        <{/for}>
                                                        </select>
                                                        (起)～
                                                        
                                                        <select name="date_end_y" id="">
                                                            <{for $i =$smarty.now|date_format:"%Y" to 2012 step -1}>
                                                                <{$year = ($i - 1911)}>
                                                                <option value="<{$year+1911}>"<{if $year == $smarty.now|date_format:"%Y"}> selected="selected"<{/if}>><{$year}></option>
                                                            <{/for}>
                                                            </select>
                                                           (迄)      
                                                        </td>
                                                       
                                                    </tr>
                                                    <tr>
                                                        <th>業務</th>
                                                        <td><{html_options name=sales options=$menuSales}></td>
                                                    </tr>
                                                     <tr>
                                                        <th>地政士</th>
                                                        <td><{html_options name=scrivener options=$menuScrivener}></td>
                                                    </tr>
                                                    <tr>
                                                        <th>合約版本:</th>
                                                        <td>
                                                           <select name="bBrand" id="bBrand">
                                                            <option value="">全部</option>
                                                            <option value="1">台灣房屋</option>
                                                            <option value="49">優美地產</option>
                                                            <option value="2">非仲介成交</option>
                                                            </select>
                                                        </td>
                                                        

                                                    </tr>
                                                    
                                                   <tr>
                                                       <td colspan="2">&nbsp;</td>
                                                   </tr>
                                                    <tr>
                                                        <td colspan="2" align="center">

                                                            <input type="submit" value="查詢" class="bt4" style="display:;width:100px;height:35px;">

                                                            <input type="hidden" name="ck" value="1">
                                                        </td>

                                                    </tr>
                                                   
                                                </table>

</form>
</center>
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