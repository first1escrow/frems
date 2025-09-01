<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        
        <script type="text/javascript">
            $(document).ready(function() {              
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
             
                $('#add').on('click', function () {
                    var url = 'PresentEdit.php?cat=add' ;
                    $.colorbox({iframe:true, width:"30%", height:"50%", href:url,onClosed:function(){
                       location.href ='PresentList.php';
                    }}) ;

                });

              
            });
            function Edit(id,cat){
                 var url = 'PresentEdit.php?cat='+cat+'&id='+id ;
                $.colorbox({iframe:true, width:"30%", height:"50%", href:url,onClosed:function(){
                    location.href ='PresentList.php';
                }}) ;
            }
            function dia(op) {
                $( "#dialog" ).dialog(op) ;
            }
           
            function delP(id){
                $.ajax({
                    url: 'PresentDel.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: id},
                })
                .done(function(msg) {
                   alert(msg);
                   location.href ='PresentList.php';
                });
                
            }
            
        </script>
        <style>
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }
        
        .tb td{
            border-bottom: 1px solid #999;
            padding: 5px;
            font-size: 10pt;
        }
        
        .tb th {
            text-align:center;
            border-bottom: 1px solid #999;
        }
        
        .div-inline{ 
            display:inline;
            width: 30%;
            float: left;
            padding-bottom: 50px;


            /*padding-right: 20px;*/
        } 
        
        #show {
            padding: 50px;
           
        }
        input {
            padding:5px;
            border:1px solid #CCC;
        }
        .btn {
            color: #000;
            font-family: Verdana;
            font-size: 14px;
            font-weight: bold;
            line-height: 14px;
            background-color: #CCCCCC;
            text-align:center;
            display:inline-block;
            padding: 8px 12px;
            border: 1px solid #DDDDDD;
            /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
        }
        .btn:hover {
            color: #000;
            font-size:12px;
            background-color: #999999;
            border: 1px solid #CCCCCC;
        }
        .btn.focus_end{
            color: #000;
            font-family: Verdana;
            font-size: 14px;
            font-weight: bold;
            line-height: 14px;
            background-color: #CCCCCC;
            text-align:center;
            display:inline-block;
            padding: 8px 12px;
            border: 1px solid #FFFF96;
            /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
            <ul id="menu">
            <div id="dialog"></div>
            </ul>
                
                <table width="1000" border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td bgcolor="#DBDBDB">
                            <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                <tr>
                                    <td height="17" bgcolor="#FFFFFF">
                                        <div id="menu-lv2">
                                                        
                                        </div>
                                        <br/> 
                                        <h1>生日禮品項</h1>
                                        <div id="container">
                                            <center>
                                                <form name="myform" id="myform" method="POST" enctype="multipart/form-data">
                                                   
                                                   <div style="margin-bottom:10px; width:900px; text-align:left;">
                                                       <input type="button" value="新增"  id="add" class="btn"> 
                                                    </div>
                                                    <!-- <div id="show">
                                                   
                                                </div> -->
                                                    <table align="center" class="tb" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr style="height:40px;background-color:#FFE4E1;">
                                                            <th style="width:25%;">編號</th>
                                                            <th style="width:25%;">物品名稱</th>
                                                             <th style="width:25%;">金額</th>
                                                            <th style="width:25%;">編輯</th>
                                                        </tr>
                                                    <{foreach from=$list key=k item=v}>
                                                        <{if $k is even}>
                                                            <{$color = '#FFFAFA;'}>
                                                        <{else}>
                                                            <{$color = '#FCEEEE;'}>
                                                        <{/if}>
                                                        <tr style="background-color:<{$color}>">
                                                            <td style="width:25%;" align="center"><{$v.gCode}></td>
                                                            <td style="width:25%;" align="center"><{$v.gName}></td>
                                                            <td style="width:25%;" align="center"><{$v.gMoney}></td>
                                                            <td style="width:25%;" align="center">
                                                                <input type="button" onclick="Edit('<{$v.gId}>','edit')" value="修改"id="add" class="btn">
                                                                    &nbsp;&nbsp;&nbsp;
                                                                <input type="button" onclick="delP('<{$v.gId}>')" value="刪除"id="add" class="btn">
                                                            </td>
                                                        </tr>
                                                    <{/foreach}>
                                                    </table>
                                                    
                                                </form>
                                                <br>
                                                <form name="del" method="POST">
                                                    <input type="hidden" name="id" value="">
                                                </form>
                                               
                                                
                                                
                                           </center>
                                           
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>