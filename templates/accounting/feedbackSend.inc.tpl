<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<link rel="stylesheet" href="/bank/colorbox.css" />
		<script src="../js/jquery-1.7.2.min.js"></script>
		<script src="../js/jquery.colorbox.js"></script>
		<!-- <link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" /> -->
		<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
		<{include file='meta.inc.tpl'}>
       <script type="text/javascript">
            $(document).ready(function() {
                var aSelected = [];
                
               	// $( "#dialog" ).dialog("close") ;
				$(".ajax").colorbox({width:"400",height:"100"});
				$(".iframe").colorbox({iframe:true, width:"1200px", height:"90%"}) ;

				$( "[name='scrivener']" ).combobox();
				get_branch() ;
	
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
				
				// 設定模糊選擇與下拉選擇並存功能
				$.widget( "ui.combobox", {
					_create: function() {
						var input,
						self = this,
						select = this.element.hide(),
						selected = select.children( ":selected" ),
						value = selected.val() ? selected.text() : "",
						wrapper = this.wrapper = $( "<span>" ).addClass( "ui-combobox" ).insertAfter( select );

						input = $( "<input>" ).appendTo( wrapper ).val( value ).addClass( "ui-state-default ui-combobox-input" ).autocomplete({
							delay: 0,
							minLength: 0,
							source: function( request, response ) {
								var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
								response( select.children( "option" ).map(function() {
									var text = $( this ).text();
									if ( this.value && ( !request.term || matcher.test(text) ) )
										return {
											label: text.replace(
												new RegExp(
													"(?![^&;]+;)(?!<[^<>]*)(" +
													$.ui.autocomplete.escapeRegex(request.term) +
													")(?![^<>]*>)(?![^&;]+;)", "gi"
												), "<strong>$1</strong>" ),
											value: text,
											option: this
										};
								})	);
							},
							select: function( event, ui ) {
								ui.item.option.selected = true;
								self._trigger( "selected", event, {
									item: ui.item.option
								});
							},
							change: function( event, ui ) {
								if ( !ui.item ) {
									var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
										valid = false;
									select.children( "option" ).each(function() {
										if ( $( this ).text().match( matcher ) ) {
											this.selected = valid = true;
											return false;
										}
									});
									if ( !valid ) {
										// remove invalid value, as it didn't match anything
										$( this ).val( "" );
										select.val( "" );
										input.data( "autocomplete" ).term = "";
										return false;
									}
								}
							}
						})
						.addClass( "ui-widget ui-widget-content ui-corner-left" );

						input.data( "autocomplete" )._renderItem = function( ul, item ) {
							return $( "<li></li>" )
								.data( "item.autocomplete", item )
								.append( "<a>" + item.label + "</a>" )
								.appendTo( ul );
						};

						$( "<a>" )
							.attr( "tabIndex", -1 )
							.attr( "title", "Show All Items" )
							.appendTo( wrapper )
							.button({
								icons: {
									primary: "ui-icon-triangle-1-s"
								},
								text: false
							})
							.removeClass( "ui-corner-all" )
							.addClass( "ui-corner-right ui-combobox-toggle" )
							.click(function() {
								// close if already visible
								if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
									input.autocomplete( "close" );
									return;
								}

								// work around a bug (likely same cause as #5265)
								$( this ).blur();

								// pass empty string as value to search for, displaying all results
								input.autocomplete( "search", "" );
								input.focus();
							});
					},

					destroy: function() {
						this.wrapper.remove();
						this.element.show();
						$.Widget.prototype.destroy.call( this );
					}
				});
	
            });
			
		function get_branch() {
			var url = "/includes/report/get_branch.php" ;
			var cl = $('[name="bStoreClass"]').val() ;
			var bc = $('[name="bCategory"]').val() ;
			
			$.post(url,{'bStoreClass':cl,'bCategory':bc},function(txt) {
				var str = '*店名稱&nbsp;&nbsp;<select id="branch" name="branch">'+txt+'</select>' ;
				
				$('#branch1').html(str) ;
				$( "#branch" ).combobox();
				//alert(bc) ;
			}) ;
		}

		function save () {
			
			$("[name='search']").val('ok');
			$("[name='form_s']").submit();
			
		}

        </script>
		<style>
			.small_font {
				font-size: 9pt;
				line-height:1;
			}
			input.bt4 {
				padding:4px 4px 1px 4px;
				vertical-align: middle;
				background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
			}
			input.bt4:hover {
				padding:4px 4px 1px 4px;
				vertical-align: middle;
				background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
			}
			.btn {
				padding:10px 20px 10px 20px ;
				color:#212121 ;
				background-color:#F8ECE9 ;
				margin:2px ;
				border:1px outset #F8ECE0 ;
				cursor:pointer ;
			}
			.btn:hover {
				padding:10px 20px 10px 20px ;
				color:#212121 ;
				background-color:#EBD1C8 ;
				margin:2px;
				border:1px outset #F8ECE0;
				cursor:pointer;
			}
		
			.ui-combobox {
					position: relative;
					display: inline-block;
			}
			.ui-combobox-toggle {
					position: absolute;
					top: 0;
					bottom: 0;
					margin-left: -1px;
					padding: 0;
					/* adjust styles for IE 6/7 */
					*height: 1.5em;
					*top: 0.1em;
			}
			.ui-combobox-input {
				margin: 0;
				padding: 0.1em;
			}
			.ui-autocomplete-input {
				width:220px;
			}
			.ui-autocomplete {
				width:150px;
				max-height: 150px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
				/* add padding to account for vertical scrollbar */
				padding-right: 20px;
			}

			/* IE 6 doesn't support max-height
			 * we use height instead, but this forces the menu to always be this tall
			 */
			* html .ui-autocomplete {
				height: 120px;
			}

			#dialog {
				background-image:url("/images/animated-overlay.gif") ;
				background-repeat: repeat-x;
				margin: 0px auto;
			}
			.tb_f table{
				
				padding-bottom: 10px;

			}
			.tb_f td{
				
				background-color:#F8ECE9;
				padding: 5px;
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
							 <form name="form_s" id="form_s" method="POST">
					            <input type="hidden" name="search" value='' />
					        
							<table width="100%" border="0" cellpadding="4" cellspacing="1">
								<tr>
									<td height="17" bgcolor="#FFFFFF">
										<div id="menu-lv2">
                                                        
										</div>
										<br/> 
										<h3></h3>
										<div id="container">
										<div id="dialog"></div>
										<h1>回饋金寄送名單</h1>
										<center>
										<table cellspacing="0" cellpadding="0" width="90%" class="tb_f">
											<tr>
												<td width="32%">
												年度季別
													<select name="sales_year" style="width:60px;">
													<{$menu_year}>
													</select>
													年度
													<{html_options name="sales_season" style="width:80px;" options=$menu_season selected=$seasons}>
												</td>
												<td id="branch1" colspan="2" width="45%">
													*店名稱&nbsp;
													<select id="branch" name="branch">
													</select>

												</td>
												<td width="23%">
													<input type="radio" name="invert_result" checked="checked" value="" id="ira">
													<label for="ira">&nbsp;顯示回饋資料</label><br>
													<input type="radio" name="invert_result" value="1" id="irb">
													<label for="irb">&nbsp;顯示不回饋資料</label><br>
													<input type="radio" name="invert_result" value="2" id="irc">
													<label for="irc">&nbsp;顯示所有資料</label>		
												</td>
											</tr>
											<tr>
												<td >&nbsp;</td>
												<td  colspan="2" >	
													&nbsp;&nbsp;地政士&nbsp;<{html_options name="scrivener" options=$menu_scr }>
												</td>
												<td>&nbsp;</td>
															
											</tr>
										</table>
							</form>
										<div style="padding:20px;text-align:center;">
										<input type="button" value="查詢" onclick="save()" class="bt4" style="display:;width:100px;height:35px;">
										</div>
										</center>
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