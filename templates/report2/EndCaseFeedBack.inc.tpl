<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                var ck = "<{$smarty.session.member_id}>";
               
                if ( ck!= 6 && ck !=1) {
                    alert("非權限使用者");
                    location.href="http://www.first1.com.tw/";

                }

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
                
               $('#mod').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
            });
            
            
            function show(op) {
                $( "#dialog" ).dialog(op) ;
            }

            function update(){

                if (confirm("確定是否要更改")) {
                    show('open');
                    var dateStart = $("[name='StartDate']").val();
                    var dateEnd = $("[name='EndDate']").val();

                    $.ajax({
                        url: 'feedTarget.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"dateStart": dateStart,"dateEnd":dateEnd},
                    })
                    .done(function(msg) {
                       
                        $("#show").html(msg);
                        show('close');
                    });
                }

                
                
            }

        </script>
        <style>
        #dialog {
            background-image:url("../images/animated-overlay.gif") ;
            background-repeat: repeat-x;
            margin: 0px auto;
        }

        #search{
            width: 80px;
            height: 50px;
            font-size: 15px;
        }
        #b{
            padding-top: 10px;
            padding-bottom: 10px;


        }
       .tb{
            text-align: center;
            border: solid 1px #ccc;

        }
        .tb th{
            width:300px;
            background-color:#E4BEB1;
            padding:4px;
                    /**/

        }
        </style>
    </head>
    <body id="dt_example">
        <form name="excel_out" method="POST">
            <input type="hidden" name="fds">
            <input type="hidden" name="fde">
            <input type="hidden" name="peo">
            <input type="hidden" name="exp">
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
                                        <h3>&nbsp;</h3>
                                        <div id="container">
                                        <center>
                                            <form name="form_search">
                                            <div style="padding-bottom:15px;">
                                            ※沒有合作契約書的仲介，回饋對象改為地政士(已結案、解約/終止履保、發函終止)
                                            <br>
                                            ※有無合作契約書請參照回饋金對象帳戶是否存在
                                            </div>
                                                <table border="0" cellspacing="5" cellpadding="5" class="tb">
                                                    <!-- <tr>
                                                        <th colspan="2" align="left">
                                                            查詢時間︰
                                                            <input type="radio" name="cat" value="1" checked="checked">進案日期
                                                            <input type="radio" name="cat" value="2">簽約日期
                                                            <input type="radio" name="cat" value="3">結案日期
                                                        </th>
                                                    </tr> -->
                                                    
                                                    <tr>
                                                        <td>結案日期：</td>
                                                        <td><input type="text" name="StartDate" class="datepickerROC" style="width:100px;"> (起)&nbsp;～&nbsp;<input type="text" name="EndDate" class="datepickerROC" style="width:100px;"> (迄)
                                                            
                                                        </td>
                                                       
                                                    </tr>
                                                   <tr>
                                                       <td colspan="2">&nbsp;</td>
                                                   </tr>
                                                    <tr>
                                                        <td colspan="2">

                                                            <input type="button" id="mod" value="更改" onclick="update()">
                                                            
                                                        </td>

                                                    </tr>
                                                   
                                                </table>
                                            </form>
                                            <br>
                                            
                                            
                                                <div id="show"></div>
                                                
                                            
                                                
                                            </center>
                                            <!-- <button id="export">匯出Excel</button> -->
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