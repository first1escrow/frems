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
            }) ;
			
			function sendDate() {
				$('#myform').submit() ;
			}
			
			function contract(cid) {
				$('form[name="form_edit"] input[name="id"]').val(cid) ;
				var _cid = $('form[name="form_edit"] input[name="id"]').val() ;
				//alert(_cid) ;
				$('form[name="form_edit"]').submit() ;
			}

            function change_sales(id){
                let currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sid', id);
                window.location.href = currentUrl.toString();
            }
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
		
		#tbList th {
			width: 100px;
			padding: 10px;
			background-color: #E4BEB1;
		}
		
		#tbList td {
			width: 100px;
			padding: 10px;
			text-align: center;
		}
		</style>
    </head>
    <body id="dt_example">
		<form name="form_edit" method="POST" action="/escrow/formbuyowneredit.php">
			<input type="hidden" name="id" value='' />
		</form>
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
            <{if $checkRemind != 1}>
            <{include file='menu1.inc.tpl'}>
            <{/if}>
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
                                        <div>
                                            <canvas id="myChart"></canvas>
                                        </div>
                                        <div id="container">
											<div>
                                                <table border="0" width="100%"><tr>
                                                    <td style="text-align:left">
                                                        業務：<{html_options name=sales options=$menu_sales selected=$sales onchange="change_sales(this.value)"}>
                                                    </td>
                                                    <td style="text-align:right">
                                                        <a href="salesMonthlyReportExcel.php?kind=<{$dataKind}>" target="_blank">
                                                            <input type="button" id="trans_build" value="下載報表" class="trans_build ui-button ui-widget ui-state-default ui-corner-all" role="button">
                                                        </a>
                                                    </td>
                                                </tr></table>
											</div>
											<div style="height:20px;"></div>
											<div>
												<center>
												<table border="0" id="tbList">
													<thead>
														<tr>
															<th>項次</th>
															<th>履保費總額</th>
															<th>平分後履保費總額</th>
															<th>回饋金成本</th>
															<th>實收</th>
															<th>件數</th>
                                                            <th>實收漲跌幅</th>
                                                            <th>件數漲跌幅</th>
														</tr>
													<thead>
													<tbody>
														<{$tbl2}>
													</tbody>
												</table>
												</center>
											</div>
                                            <div style="height:50px;"></div>
                                            <div>
                                                <center>
                                                    <table border="0" id="tbList">
                                                        <thead>
                                                        <tr>
                                                            <th>項次</th>
                                                            <th>履保費總額</th>
                                                            <th>平分後履保費總額</th>
                                                            <th>回饋金成本</th>
                                                            <th>實收</th>
                                                            <th>件數</th>
                                                        </tr>
                                                        <thead>
                                                        <tbody>
                                                        <{$tbl}>
                                                        </tbody>
                                                    </table>
                                                </center>
                                            </div>
										</div>
                                    </td>
                            </table>
                        </td>
                    </tr>
                </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                const ctx = document.getElementById('myChart');

                const chartArray1 = <{json_encode($chart_data[1])}>;
                const chartArray2 = <{json_encode($chart_data[2])}>;
                const chartArray3 = <{json_encode($chart_data[3])}>;
                const chartArray4 = <{json_encode($chart_data[4])}>;

                new Chart(ctx, {
                    data: {
                        datasets: [{
                            type: 'bar',
                            label: <{json_encode($label[1])}>,
                            data: chartArray1,
                            backgroundColor: 'rgba(54, 162, 235, 0.3)',
                            borderColor: 'rgb(54, 162, 235, 0.3)',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            yAxisID: 'y'
                        }, {
                            type: 'line',
                            label:  <{json_encode($label[3])}>,
                            data: chartArray3,
                            borderColor: 'rgb(54, 162, 235, 0.6)',
                            borderDash: [5, 5],
                            borderWidth: 2,
                            tension: 0.1,
                            yAxisID: 'y1'
                        },{
                            type: 'bar',
                            label:  <{json_encode($label[2])}>,
                            data: chartArray2,
                            backgroundColor: 'rgba(184, 28, 34, 0.3)',
                            borderColor: 'rgb(184, 28, 34, 0.3)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        }, {
                            type: 'line',
                            label:  <{json_encode($label[4])}>,
                            data: chartArray4,
                            borderColor: 'rgba(184, 28, 34, 0.6)',
                            borderWidth: 2,
                            tension: 0.1,
                            yAxisID: 'y1'
                        }],
                        labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: <{json_encode($sales_name)}>
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                beginAtZero: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: '成交件數'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                beginAtZero: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: '實收額(萬)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            </script>
    </body>
</html>