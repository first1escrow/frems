<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
		<link rel="stylesheet" href="/css/colorbox.css" />
        <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
		<script src="/js/jquery.colorbox.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript">
			var _index = 10 ;

            $("link[href='/libs/datatables/media/css/demo_table.css']").remove();
            $(document).ready(function() {
				getMarguee(<{$smarty.session.member_id}>) ;
				setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)
				
				$('.chgyr').change(function() {
					$('#chYear').submit() ;
				}) ;
				
				var aSelected = [];
				$(".iframe").colorbox({iframe:true, width:"950px", height:"90%"}) ;
                        
                /* Init the table */
				var url = "/includes/income/inspectionlisttb.php?f=" + $('[name="f"]').val() + "&t=" + $('[name="t"]').val();
                var oTable = $("#example").dataTable({
                    "createdRow": function(row, data, dataIndex) {
                        if (dataIndex % 2 == 0) {
                            $(row).css("background-color", "#F8ECE9");
                        }
                    },
                    "searchDelay": 2000,
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "POST", 
                    "sAjaxSource": url,
                    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                        if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                            $(nRow).addClass('row_selected');
                        }
                    },
                    "ordering": false,
                    "language": {
                        "processing": "",
                        "loadingRecords": "載入中...",
                        "lengthMenu": "顯示 _MENU_ 項結果",
                        "zeroRecords": "沒有符合的結果",
                        "info": "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                        "infoEmpty": "顯示第 0 至 0 項結果，共 0 項",
                        "infoFiltered": "(從 _MAX_ 項結果中過濾)",
                        "infoPostFix": "",
                        "search": "搜尋:",
                        "paginate": {
                            "first": "第一頁",
                            "previous": "上一頁",
                            "next": "下一頁",
                            "last": "最後一頁"
                        },
                        "aria": {
                            "sortAscending": ": 升冪排列",
                            "sortDescending": ": 降冪排列"
                        }
                    }
                });

				/* MoveMouseOut event handler */
                $('#example tbody tr').mouseout(function (e) {
					hide_w('w_word') ;
				}) ;
				
				/* MoveMouseOver event handler */
                $('#example tbody tr').live('mouseover', function (e) {
                    var id = this.id; 
					var tmp = id.replace('row_','') ;
					var arr = tmp.split('_') ;
					
					$(this).find('td:eq(2)').mouseover( function(event) {
						show_w(event,'w_word',arr[1]) ;
					}) ;
					
					$(this).mouseout(function() {
						hide_w('w_word') ;
					}) ;
                }) ;
				
                /* Click event handler */
                $('#example tbody tr').live('click', function () {
                    var id = this.id; 
					var tmp = id.replace('row_','') ;
					var arr = tmp.split('_') ;
					
                    var index = jQuery.inArray(id, aSelected);
                    if ( index === -1 ) {
                        aSelected.push( id );
                    } else {
                        aSelected.splice( index, 1 );
                    }
                    $('#totalCount').html(aSelected.length);
					$('#example tbody tr').removeClass('row_selected') ;
                    $(this).toggleClass('row_selected');
                } );
                $('#example tbody tr').live('dblclick', function () {
                    var tmp = this.id.replace('row_', '');
                    var arr = tmp.split('_');
                    $('form[name=form_edit]').attr('action', '/income/formedit.php');
                    $('form[name=form_edit] input[name=certifyid]').val(arr[0]);
                    $('form[name=form_edit] input[name=id]').val(arr[1]);
                    $('form[name=form_edit]').submit();
                } );

                $('#add').live('click', function () {
                    $('form[name=form_add]').attr('action', '/escrow/formbuyowneradd.php');
                    $('form[name=form_add]').submit();
                } );

                $('#del').live('click', function () {
//                    var request = $.ajax({
//                        url: "/escrow/formdel.php",
//                        type: "POST",
//                        data: {
//                            ids:aSelected
//                        },
//                        dataType: "html"
//                    });
//                    request.done(function( msg ) {
////                        alert(msg);
//                        window.location='/escrow/buyownersearch.php';
//                    });
                } );
				
				$('#check_income').click(function() {
					$('#check_income').prop('disabled',true) ;
					var url = 'read_bank_data.php' ;
					$.post(url,function() {
					}) ;
					
					setTimeout("$('#check_income').prop('disabled',false)",11000) ;	
					setInterval("count_down()",1000);
					setTimeout("location = '/income/listinspection.php'",10000) ;
				}) ;
				
                $('#add').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#del').button( {
                    icons:{
                        primary: "ui-icon-locked"
                    }
                } );
                $('#check_income').button( {
                    icons:{
                        primary: "ui-icon-refresh"
                    }
                } );
                $('#loading').dialog('close');
            } );
			
			//倒數計時顯示
			function count_down() {
				$('#check_income').text('（系統查詢中...請等候!!）') ;
				$('body').css('cursor','wait') ;
			}
			
			//依據滑鼠位置顯示提醒視窗
			function show_w(e,tn,msg,posX,posY) {
				var tags = tn ;	
				set_w_word(tags) ;
				
				$.post('msg.php',{'id':msg},function(txt) {
					$('#' + tags).html(txt) ;
					$('#'+tags).css({'position':'absolute','left':(e.pageX+10),'top':(e.pageY+10),'display':''});
				}) ;
			} 

			//關閉提醒視窗
			function hide_w(tn) {
				var tags = tn ;
				$('#'+tags).css({'display':'none'});
			} 
			
			//設定提醒視窗
			function set_w_word(tags) {
				var id_tag = '#' + tags ;
				$(id_tag).css({
					'padding':'5px',
					'background-color':'#FFFFFF',
					'width':'300px',
					'height':'50px',
					'font-size':'9pt',
					'margin':'0px auto',
					'line-height':'1.5em',
					'border-width':'1px',
					'border-style':'solid',
					'border-color':'#00000000',
					'text-align':'left'
				}) ;
			}

        </script>
        <style>
        th {
            font-size: 10pt;
        }
        </style>
    </head>
    <body id="dt_example">
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="certifyid" id="certifyid" value='' />
            <input type="hidden" name="id" id="id" value='' />
        </form>
        <form name="form_add" id="form_add" method="POST">
        </form>
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                         <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right">
										<div id="abgne_marquee" style="display:none;">
											<ul>
											</ul>
										</div>
									</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=150,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <div id="mainNav">
                <table width="1000" border="0" cellpadding="0" cellspacing="0">
                    <tr>

                    </tr>
                </table>
            </div>
            <div id="content">
                <body>
                    <div class="abgne_tab">
                        <{include file='menu1.inc.tpl'}>
                        <div class="tab_container">
                            <div id="menu-lv2">
                                                        
                                                        </div>
                                                    <br/> 
                            <div id="tab" class="tab_content">
								<table width="980" border="0" cellpadding="4" cellspacing="1">
									<tr>
										<td>
											<a class="iframe" href="/bank/expense_cheque.php">支票通知</a> &nbsp;&nbsp;

										</td>
										<td style="text-align:right;">
											<button id="check_income" style="width:200px;height:30px;">查詢銀行入帳資料</button>
										</td>
									</tr>
                                    
                                    <tr>
                                        <td colspan="2">
                                            <a class="iframe" href="/income/expense_cheque_taishin.php">台新即時支票通知(票據金額為加總後的金額)</a>
                                        </td>
                                    </tr>
                                    
								</table>
								<div style="height:10px;"></div>
								<table width="980" border="0" cellpadding="4" cellspacing="1">
									<tr style="padding:5px;background-color:white;">
										<td style="padding: 15px;">
											<form method="POST" id="chYear">
											年度：<select name="f" class="chgyr"><{$yr.f}></select> 年度 ~ <select name="t" class="chgyr"><{$yr.t}></select> 年度
											</form>
										</td>
									</tr>
								</table>
                                <table width="980" border="0" cellpadding="4" cellspacing="1">
                                    <tr>
                                        <td bgcolor="#DBDBDB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
												<tr>
                                                    <td height="17" bgcolor="#FFFFFF"><h3>&nbsp;</h3>
                                                        <div id="container">
                                                            <div id="dynamic">
                                                                <div id="container">
                                                                    <div id="dynamic">
                                                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="12%">存匯入日期</th>
                                                                                    <th width="5%">時間</th>
                                                                                    <th width="13%">保證證號</th>
                                                                                    <th width="15%">存匯入戶名</th>
                                                                                    <th width="15%">存匯入金額</th>
                                                                                    <th width="15%">交易狀態</th>
                                                                                    <th width="13%">地政士姓名</th>
                                                                                    <th width="12%">入帳狀態</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td colspan="8" class="dataTables_empty">讀取資料中...</td>
                                                                                </tr>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th colspan="8"></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                                </td>
                                                                </tr>
                                                                </table></td>
                                                                </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        </div></div>
                                                        <div id="footer">
                                                            <p>2012 第一建築經理股份有限公司 版權所有</p>
                                                        </div>
														<div id="w_word"></div>
                                                        </body>
                                                        </html>