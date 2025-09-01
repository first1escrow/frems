<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#export').button({
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#search').button({
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
            });
			
            function changePage(){
                $("[name='form_search']").submit();
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
        .tb td{
            border: 1px #CCC solid;
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
                                        <h1>進行中最高落點查詢</h1>
                                        <div id="container">
                                            <form name="form_search" method="POST">
                                                <table border="0" cellspacing="10" cellpadding="10">
                                                    <tr>
                                                        <th align="left"></th>
                                                        <td></td>
                                                        <th align="left"></th>
                                                        <td></td>
                                                        <td rowspan="4"><input type="submit" value="查詢" id='search'></td>
                                                    </tr>
                                                    <tr>
                                                        <th>經辦︰</th>
                                                        <td><{html_options name="people" options=$menuPeople selected=$people}></td>
                                                        <th align="left">查詢日期範圍︰</th>
                                                        <td>
                                                            <input type="text" name="date_start" class="datepickerROC" readonly style="width:100px" value="<{$date_start}>" />
                                                            ~ 
                                                            <input type="text" name="date_end" class="datepickerROC" readonly style="width:100px" value="<{$date_end}>" />
                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                <div align="right" id="b"> <!-- <input type="button" id='export' value="匯出EXCEL"> --></div>
                                                <center>
                                                    <div id="msg"></div>
                                                    
                                                    <table cellpadding="0" cellspacing="0" class="tb" width="80%">
                                                        <tr>
                                                            <th>查詢範圍天數</th>
                                                            <td colspan="2"><{$days}>天</td>
                                                        </tr>
                                                        <tr>
                                                            <th>姓名</th>
                                                            <th>查詢範圍數量加總</th>
                                                            <th>查詢範圍平均最高件數</th>  
                                                        </tr>
                                                        <{foreach from=$list_People key=key item=item}>
                                                        <tr>
                                                            <td><{$item.name}></td>
                                                            <td><{$item.count}></td>
                                                            <td><{$item.avgcount}></td>
                                                        </tr>
                                                        <{/foreach}>
                                                    </table>
                                                    <br>
                                                    <hr>
                                                    <br>
                                                    <table cellpadding="0" cellspacing="0" class="tb" width="100%">
                                                        <tr>
                                                            <th>日期</th>
                                                            <th>姓名</th>
                                                            <th>當天最高件數</th>
                                                        </tr>
                                                        <{foreach from=$data_list key=key item=item}>
                                                        <tr>
                                                            <td><{$item.rDate}></td>
                                                            <td><{$item.uName}></td>
                                                            <td><{$item.rCaseCount}></td>
                                                        </tr>
                                                        <{/foreach}>
                                                        <tr>
                                                            <td colspan="3">第 <select name="page" onchange="changePage()"><{$page_option}></select> 頁 ／共<{$total_page}>頁    顯示第 <{$limit_s}> 筆到第 <{$limit_e}> 筆的紀錄，共 <{$total}> 筆紀錄</td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </form>
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