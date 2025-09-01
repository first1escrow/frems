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
				
				$('#search').live('click', function () {
                    cbxVehicle = new Array();
                    var people ='';
					
					var date_start = $("[name='date_start']").val() ;
					var date_end = $("[name='date_end']").val() ;
					
                    $('input:checkbox:checked[name="people[]"]').each(function(i) { cbxVehicle[i] = this.value; }); 

                    people = cbxVehicle.join(',');
                    dia("open") ;

                    $.ajax({
                        url: 'banktrans_report_result.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {'people': people,'date_start':date_start,'date_end':date_end},
                    })
                    .done(function(txt) {
                        // alert(txt);
                        $("#msg").html(txt);
                        dia('close');

                    })
					
                });

                $('#export').live('click', function(event) {
                    cbxVehicle = new Array();
                    var people ='';
					
					var date_start = $("[name='date_start']").val();
					var date_end = $("[name='date_end']").val();
					
                    $('input:checkbox:checked[name="people[]"]').each(function(i) { cbxVehicle[i] = this.value; }); 

                    people = cbxVehicle.join(',');
                     dia("open") ;
                     $("[name='fds']").val(date_start);
                     $("[name='fde']").val(date_end);
                     $("[name='peo']").val(people);
                     $("[name='exp']").val('ok');
                    
                    $("[name='excel_out']").submit();
                     dia('close');
                });


                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );

                $('#search').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );

               
           

            });
			
			function dia(op) {
				$( "#dialog" ).dialog(op) ;
			}

            function changePage()
            {
                cbxVehicle = new Array();
                    var people ='';
					
                    //var date_start = $("[name='date_start']").val();
					var date_start = formatDate('date_start') ;
					date_start = date_start.replace("e","") ;
					if (date_start == 'f') {
						alert('查詢日期範圍(起)有誤!!') ;
						return false ;
					}

                    //var date_end = $("[name='date_end']").val();
					var date_end = formatDate('date_end') ;
					date_end = date_end.replace("e","") ;
					if (date_end == 'f') {
						alert('查詢日期範圍(迄)有誤!!') ;
						return false ;
					}
					
                    var p = $("[name='page']").val();

                    // alert(p);

                    $('input:checkbox:checked[name="people[]"]').each(function(i) { cbxVehicle[i] = this.value; }); 


                    people = cbxVehicle.join(',');


                    $.ajax({
                        url: 'banktrans_report_result.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {'people': people,'date_start':date_start,'date_end':date_end,'page':p},
                    })
                    .done(function(txt) {
                        // alert(txt);
                        $("#msg").html(txt);

                    })
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
                                            <div>

                                            <{if in_array($smarty.session.member_id, $agents)}>
                                                                
                                            <a href="../undertaker/undertakerCalendar.php">代理人設定</a>
                                            <{/if}>
                                            </div>
                                            <form name="form_search">
                                            
                                            <table border="0" cellspacing="10" cellpadding="10">
                                                <tr>
                                                    <th align="left">經辦姓名︰</th>
                                                    <td><{html_checkboxes name="people" options=$list_people selected=$data_people separator="&nbsp;&nbsp;"}></td>
                                                    <td rowspan="4"><input type="button" value="查詢" id='search'></td>
                                                    
                                                </tr>
                                                <tr>
                                                    <th align="left">查詢日期範圍︰</th>
                                                    <td>
														<input type="text" name="date_start" class="datepickerROC" readonly style="width:100px" value="<{$date_start}>" />
														~ 
														<input type="text" name="date_end" class="datepickerROC" readonly style="width:100px" value="<{$date_end}>" />
                                                    </td>
                                                   
                                                </tr>
                                               
                                            </table>
                                            </form>
                                            
                                            <div align="right" id="b"> <!-- <input type="button" id='export' value="匯出EXCEL"> --></div>
                                            <center>
                                                <div id="msg"></div>
                                                
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