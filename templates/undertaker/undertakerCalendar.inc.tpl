<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
         <!-- <script src='../js/fullcalendar/jquery2.2.0.min.js'></script> -->
        <!-- <script src='../js/fullcalendar/jquery-ui.js'></script> -->
        <link href='../js/fullcalendar/fullcalendar.css' rel='stylesheet' />
        <link href='../js/fullcalendar/fullcalendar.print.min.css' rel='stylesheet' media='print' />
        <script src='../js/fullcalendar/lib/moment.min.js'></script>
        <script src='../js/fullcalendar/fullcalendar.js'></script>
        <script src='../js/fullcalendar/locale-all.js'></script>
        <script type="text/javascript">
            $(document).ready(function() {
				// $('#calendar').fullCalendar({
    //                 header: {
    //                     left: 'prev,next today',
    //                     center: 'title',
    //                     right: 'month,basicWeek,basicDay' //basicDay basicWeek

    //                 },
    //                 defaultView: 'basicDay',
    //                 locale: 'zh-tw',
    //                 defaultDate: "<{$today}>",
    //                 navLinks: true,
    //                 // navLinks: true, // can click day/week names to navigate views
    //                 editable: true,
    //                 eventLimit: true, // allow "more" link when too many events
    //                 events:"getSchedule.php",
    //                 dayClick: function(date,allDay,event,view) {
    //                     var url = "editCalendar.php?cat=add&date=" + date.format();
    //                     $.colorbox({iframe:true, width:"50%", height:"60%", href:url}) ;
    //                     // console.log(date.format());
    //                     // location.href = "AddCalendar.php?date=" + date.format();
    //                     return false;
    //                 },
    //                 eventClick: function(event) {
    //                     var url = "editCalendar.php?cat=edit&id=" + event.id;
    //                     $.colorbox({iframe:true, width:"50%", height:"60%", href:url}) ;
    //                     // location.href = "EditCalendar.php?v=<?=$_GET['v']?>&id=" + event.id;
    //                    // console.log(event);
    //                     return false;
    //                 },
    //             });

            });
            function add(){
                var url = "editCalendar.php?cat=add";
                $.colorbox({iframe:true, width:"50%", height:"60%", href:url,onClosed:function(){
                   location.href='undertakerCalendar.php';
                    
                }}) ;
            }
            function edit(id){
                var url = "editCalendar.php?cat=edit&id="+id;
                $.colorbox({iframe:true, width:"50%", height:"60%", href:url,onClosed:function(){
                   location.href='undertakerCalendar.php';
                    
                }}) ;
                
            }
            function del(id){

                if (confirm("確定要刪除嗎?")) {

                    $.ajax({
                        url: 'delCalendar.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {id: id},
                    })
                    .done(function(msg) {
                        alert(msg);
                        location.href='undertakerCalendar.php';
                        // console.log("suc。cess");
                    });
                   
                }
            }

            function searchData(){
                $("#formSearch").submit();
            }
			
			
        </script>
		<style>
		.user {
             background-color: #FFFFFF;
        }
        .tb th{
             font-size: 18px;
            padding-left:15px; 
            padding-top:10px; 
            padding-bottom:10px; 
            background: #D1927C;
        }
        
        .tb td{
             font-size: 18px;
            padding-left:15px; 
            padding-top:10px; 
            padding-bottom:10px; 
            /*background: #D1927C;*/
            border: 1px solid #999;
        }
         /*input*/
            .xxx-input {
                color:#666666;
                font-size:16px;
                font-weight:normal;
                /*background-color:#FFFFFF;*/
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
                                        <h3>&nbsp;</h3>
                                        <div id="container">
                                            <div style="padding-bottom: 10px;">
                                                <input type="button" value="新增" onclick="add()" class="xxx-input">
                                                <div style="float:right;padding-right:10px;">
                                                    <form action="" method="POST" name="formSearch" id="formSearch">
                                                        <{html_options name=year options=$menuYear selected=$year onchange="searchData()"}>年
                                                        <{html_options name=month options=$menuMonth selected=$month onchange="searchData()"}>月
                                                    </form>
                                                     
                                                </div>
                                            </div>
                                            <div id="calendar">
                                                <table cellpadding="0" cellspacing="0" width="100%" class="tb">
                                                    <tr>
                                                        <th width="40%">時間</th>
                                                        <th width="10%">經辦</th>
                                                        <th width="10%">代理人</th>
                                                        <th width="20%">備註</th>
                                                        <th width="10%"></th>
                                                    </tr>
                                                    <{foreach from=$list key=key item=item}>
                                                    <tr>
                                                        <td><a href="#" onclick="edit('<{$item.uId}>')"><{$item.uDateTime}>至<{$item.uDateTime2}></a></td>
                                                        <td><{$item.Staff}></td>
                                                        <td><{$item.SubstituteStaff}></td>
                                                        <td><{$item.uNote}></td>
                                                        <td><input type="button" value="刪除" onclick="del('<{$item.uId}>')"></td>
                                                    </tr>
                                                    <{/foreach}>
                                                    
                                                </table>
                                            </div>
                                           
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