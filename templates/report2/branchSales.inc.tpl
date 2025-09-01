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
                $(".ui-dialog-titlebar").hide() ;
                
               

                $('#export').live('click', function(event) {

                    
                     // show("open") ;
                   
                    
                    $("[name='form_search']").submit();
                     // show('close');
                });

                $("#all").on('click', function() {
                    
                    if ($("#all").prop("checked")) {
                        $("input[name='city[]']").each(function(){
                            $(this).prop("checked",true);
                        })
                    }else{
                        $("input[name='city[]']").each(function(){
                            $(this).prop("checked",false);
                        })
                    }
                });
               
                // $('#export').button( {
                //     icons:{
                //         primary: "ui-icon-document"
                //     }
                // });

           
           

            });
            
            function show(op) {
                $( "#dialog" ).dialog(op) ;
            }

            function checkALL(){

            }
          

        </script>
        <style>
        .btn:hover {
            color: #000;
            font-size: 12px;
            background-color: #999999;
            border: 1px solid #CCCCCC;
        }

        .btn {
            color: #000;
            font-family: Verdana;
            font-size: 12px;
            font-weight: bold;
            line-height: 12px;
            background-color: #CCCCCC;
            text-align: center;
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #DDDDDD;
            /* border-radius: 0.5em 0.5em 0.5em 0.5em; */
        }

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
            width: 80%;
        }
        .tb th{
           
            background-color:#E4BEB1;
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: right;
            border: solid 1px #FFF;
            width: 15%;
            
        }
        .tb td{
           border: solid 1px #FFF;
            background-color:#F8ECE9;
            padding-top: 5px;
            padding-bottom: 5px;
            padding-left: 5px;
            /*text-align: left;*/
            /**/

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
                                    <td width="81%" align="right"></td>
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
                                        <h1>仲介店/地政士排名</h1>
                                        <div id="container">
                                            <center>
                                            <form name="form_search" method="post">

                                                <table border="0" cellspacing="0" cellpadding="0" class="tb">
                                                    <tr>
                                                        <th>類別︰</th>
                                                        <td align="left">
                                                            <input type="radio" name="cat2" id="" value="2" checked>仲介
                                                            <input type="radio" name="cat2" id="" value="1">地政士
                                                        </td>
                                                    </tr>
                                                    <tr >
                                                        <th>案件仲介商類型</th>
                                                        <td align="left">
                                                            <select name="realestate" size="1" style="width:130px;">
                                                                <option value="">全部</option>
                                                                <{$menuCategory}>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <th>查詢時間︰</th>
                                                        <td align="left">
                                                            
                                                            <input type="radio" name="cat" value="1" checked="checked">進案日期
                                                            <input type="radio" name="cat" value="2">簽約日期
                                                            <input type="radio" name="cat" value="3">結案日期
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>時間：</th>
                                                        <td align="left"><input type="text" name="StartDate" class="datepickerROC" style="width:100px;"> (起)&nbsp;～&nbsp;<input type="text" name="EndDate" class="datepickerROC" style="width:100px;"> (迄)
                                                            
                                                        </td>
                                                       
                                                    </tr>
                                                    <tr>
                                                        <th>排名：</th>
                                                        <td align="left">
                                                            <input type="radio" name="sort" value="1" checked>件數
                                                            <input type="radio" name="sort" value="2">業績
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>店地區：</th>
                                                        <td align="left">
                                                            <input type="checkbox" name="city[]" id="all" value="0"> 全選<br>   
                                                            <{$menu_City}>
                                                           
                                                        </td>
                                                    </tr>
                                                  <!--  <tr>
                                                       <td colspan="2" align="center"></td>
                                                   </tr> -->
                                                    
                                                </table>
                                                <div style="padding-top:20px;">
                                                    <input type="button" id="export" class="btn" value="匯出EXCEL">
                                                    <input type="hidden" name="ck" value="1">
                                                </div>
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