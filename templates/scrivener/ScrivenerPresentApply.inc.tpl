<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
    	<link rel="stylesheet" href="../css/colorbox.css" />		
        <{include file='meta.inc.tpl'}>
        <script src="/js/IDCheck.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
            	$( "#tabs" ).tabs({
                    selected: 0
                });
                // <{if $cat  == 'add'}>
                // 	getPrice();
                // <{/if}>
                
				// $('[name="scrivener"]').combobox();
				if ("<{$disabled}>"  == 1) {
					setStatus('l2','readonly');
					setStatus('l21','readonly');
					setStatus('l22','readonly');
					setStatus('l23','readonly');

				}

				if ("<{$disabled2}>"  == 1) {
					
					setStatus('l3','disabled');
					
				}

				if ("<{$disabled3}>"  == 1) {
				
					setStatus('l4','readonly');
				}

				if ("<{$disabledStatus == 1}>") {
					$("[name='status']").attr('disabled', 'disabled');
					$("[name='status']").attr('class', $("[name='status']").attr('class')+' input-color');
				}

				if ("<{$disabledTicket}>"  == 1) {
					
					setStatus('lticket','readonly');
				}
				<{if $smarty.session.member_ScrivenerLevel > 2}>
				/* 檢核輸入統一編號是否合法 */
				if (checkUID($('[name="IdentifyId"]').val())) {
					$('#fId').html('<img src="/images/ok.png">') ;
				}else {
					$('#fId').html('<img src="/images/ng.png">') ;
				}
				<{/if}>
				// if ("<{$data.sStatus}>" == 2) {
				// 	$(".l2").attr('disabled', 'disabled');
				// }
			});
			function setStatus(cla,ss){
				$("."+cla).attr(ss, ss);
					// $(".l4").attr('class', $(".l4").attr('class')+' input-color');

				$("."+cla).each(function() {
					$(this).attr('class', $(this).attr('class')+' input-color');
				});
			}
            function getArea2(ct,ar,zp) {
                var url = '../escrow/listArea.php' ;
                var ct = $('#' + ct + ' :selected').val() ;
                
                $('#' + zp + '').val('') ;
                $('#' + zp + 'F').val('') ;
                $('#' + ar + ' option').remove() ;
                
                $.post(url,{"city":ct},function(txt) {
                    var str = '' ;
                    str = str + txt  ;
                    $('#' + ar ).append(str) ;
                }) ;
            }
            
            function getZip2(ar,zp) {
                var zips = $('#' + ar + ' :selected').val() ;

                $('#' + zp + '').val(zips);
                $('#' + zp + 'F').val(zips.substr(0,3));
            }

   
			

			function apply(ss){
				if (ss == 'save') {
					$("[name='ok']").val('ok');
					
				}
				
				$("#NewsForm").submit();
			}

			function setMoney(money){
				$("[name='money']").val(money);
			}

			function getPrice(sId){
				// var val = $("[name='gift']").val();
				$.ajax({
					url: 'getPrice.php',
					type: 'POST',
					dataType: 'html',
					data: {sId:sId},
				})
				.done(function(json) {
					var obj = jQuery.parseJSON(json);
					// console.log(obj);
					
					$("#giftchoose").html(obj.sLevel)
					if (obj.count > 1) {
						// $("#giftName").text('');
						var option = '';
						for (var i = 0; i < obj.data.length; i++) {
							if (i==0) {
								option += '<input type="radio" name="gift" value="'+obj.data[i].gId+'" onclick="setMoney('+obj.data[i].gMoney+')" checked="checked">'+obj.data[i].gCode+obj.data[i].gName;
							}else{
								option += '<input type="radio" name="gift" value="'+obj.data[i].gId+'" onclick="setMoney('+obj.data[i].gMoney+')">'+obj.data[i].gCode+obj.data[i].gName;
							}
							
						}
						$("#giftchoose").html(option);
						$("[name='money']").val(obj.gMoney);
					}else{
						// $("#giftName").text(obj.gCode+obj.gName);
						$("[name='money']").val(obj.gMoney);
						$("[name='gift']").val(obj.gId);
						$("#giftchoose").html('<input type="hidden" name="gift" value="<{$data.sGift}>">');
					}

					if (obj.Category == 2) {
			            $("[name='money']").attr('readonly', 'readonly');
			          }else{
			            $("[name='money']").removeAttr('readonly');
			          }
					
				});
				

			}
			function checkID(no,val,type){
                if (checkUID(val)) {
                    $('#'+type+'fId'+no).html('<img src="/images/ok.png">') ;
                }
                else {
                    $('#'+type+'fId'+no).html('<img src="/images/ng.png">') ;
                }
            }

            function getFeedbackData(){
            	var sId = $('[name="sId"]').val();

            	$.ajax({
            		url: 'getScrivenerF.php',
            		type: 'POST',
            		dataType: 'html',
            		data: {sId: sId},
            	}).done(function(json) {
            		if ("<{$data.sName}>" == '' ) {
            			var obj= JSON.parse(json) ;
            			// console.log(obj.AreaOption);

            			$('[name="Name"]').val(obj.fTitle);
            			$('[name="Identify"]').val(obj.fIdentity);
            			$('[name="IdentifyId"]').val(obj.fIdentityNumber);

            			$('[name="zipCF"]').val(obj.fZipR);
            			$('[name="fZipC"]').val(obj.fZipR);
            			$('[name="countryC"]').val(obj.city);
            			$('[name="areaC"] option').remove();
            			$(obj.AreaOption).appendTo('[name="areaC"]');

            			$('[name="fAddrC"]').val(obj.fAddrR);

            		
            			// console.log(j);	
            		}
            		
            	});

            	getPrice(sId);
            	
            }

            function del(id){
            	$.ajax({
            		url: 'ScrivenerPresentDel.php',
            		type: 'POST',
            		dataType: 'html',
            		data: {id: id},
            	})
            	.done(function(msg) {
            		
            		if (msg == 'ok') {
            			alert('已刪除');
            			parent.jQuery.colorbox.close();
            		}
            		
            	});
            	
            	
            }

            function delReceipt(id){
            	$.ajax({
            		url: 'ScrivenerPresentReceiptDel.php',
            		type: 'POST',
            		dataType: 'html',
            		data: {id: id},
            	})
            	.done(function(msg) {
            		if (msg == 'ok') {
            			alert('已刪除');
            			parent.jQuery.colorbox.close();
            		}
            	});
            	
            }

        </script>
        <style type="text/css">
            #tabs {
               width:90%;
               margin-left:auto; 
               margin-right:auto;
            }

            #tabs table th {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                width: 40%

            }
            
            #tabs table th .sml {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                font-size: 10px;

            }
            #tabs table td{
            	/*padding-left: 5px;
            	padding-top:10px;
                padding-bottom:10px;*/
                padding:10px 5px;
            }
		input {
			padding:5px;
			border:1px solid #CCC;
		}
		textarea{
			padding:10px;
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
		
		.l2,.l3,.l4,.l21{
			width: 300px;
		}
		
		.input-color {	
			background-color:#e8e8e8 ;
		}
		.tb-title {
            font-size: 18px;
            padding-left:15px; 
            padding-top:10px; 
            padding-bottom:10px; 
            background: #D1927C;

        }
        .input-text-sml{
                width:36px;

            }
        .cb1 {
				padding:0px 10px;
			}
			.cb1 input[type="checkbox"] {/*隱藏原生*/
			    /*display:none;*/
			    position: absolute;
			    left: -9999px;
			}
			.cb1 input[type="checkbox"] + label span {
			    display:inline-block;
				
			    width:20px;
			    height:20px;
			    margin:-3px 4px 0 0;
			    vertical-align:middle;
			    background:url(../images/check_radio_sheet2.png) left top no-repeat;
			    cursor:pointer;
				background-size:80px 20px;
				transition: none;
				-webkit-transition:none;
			}
			.cb1 input[type="checkbox"]:checked + label span {
			    background:url(../images/check_radio_sheet2.png)  -20px top no-repeat;
				background-size:80px 20px;
				transition: none;
				-webkit-transition:none;
			}
			.cb1 label {
				cursor:pointer;
				display: inline-block;
				white-space: nowrap;
				margin-right: 10px;
				font-weight: bold;
				/*-webkit-appearance: push-button;
				-moz-appearance: button;*/
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

			/*textarea*/
			.xxx-textarea {
				color:#666666;
				font-size:16px;
				font-weight:normal;
				line-height:normal;
				/*background-color:#FFFFFF;*/
				text-align:left;
				height:100px;
				padding:5px 5px;
				border:1px solid #CCCCCC;
				border-radius: 0.35em;
			}
			.xxx-textarea:focus {
			    border-color: rgba(82, 168, 236, 0.8) !important;
			    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
				-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
			    outline: 0 none;
			}
			.xxx-select {
					color:#666666;
					font-size:16px;
					font-weight:normal;
					/*background-color:#FFFFFF;*/
					text-align:left;
					height:34px;
					padding:0 0px 0 5px;
					border:1px solid #CCCCCC;
					border-radius: 0em;
				}
        </style>
    </head>
    <body id="dt_example">
		 <div id="tabs">
            	<center>
               	<form action="" method="POST" enctype="multipart/form-data" id="NewsForm" >
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>	
							<td colspan="4" class="tb-title">&nbsp;</td>
						</tr>

						<{if $smarty.session.member_ScrivenerLevel > 1}>
						<tr>
							<th>狀態：</th>
							<td colspan="3">
								<{html_options name=status options=$menuStatus class="xxx-select" selected="<{$data.sStatus}>" style="width:300px;"}>
							</td>
						</tr>
						<{/if}>
						
						<tr>
							<th>生日禮：</th>
							<td  colspan="3">
								<span id="giftName">
									
											<!-- <{$data.giftName}> -->

									
									</span>


								<span id="giftchoose">
									<{foreach from=$menuGift key=key item=item}>
											<{if $data.sGift == $item.gId}>
												<input type="radio" name="gift" value="<{$item.gId}>" onclick="setMoney('<{$item.gMoney}>')" checked="checked="><{$item.gCode}><{$item.gName}>
											<{else}>
												<input type="radio" name="gift" value="<{$item.gId}>" onclick="setMoney('<{$item.gMoney}>')" ><{$item.gCode}><{$item.gName}>
											<{/if}>
											
											
											<{/foreach}>
										<!-- <{if $data.sLevel == 3 }>
											<{foreach from=$menuGift key=key item=item}>
											<{if $data.sGift == $item.gId}>
												<input type="radio" name="gift" value="<{$item.gId}>" onclick="setMoney('<{$item.gMoney}>')" checked="checked="><{$item.gCode}><{$item.gName}>
											<{else}>
												<input type="radio" name="gift" value="<{$item.gId}>" onclick="setMoney('<{$item.gMoney}>')" ><{$item.gCode}><{$item.gName}>
											<{/if}>
											
											
											<{/foreach}>
										<{else}>
											<input type="hidden" name="gift" value="<{$data.sGift}>">
										<{/if}> -->
									
									
								</span>
								 
								<!-- <{html_options name=gift options=$menuGift class="xxx-select l2" selected="<{$data.sGift}>" onchange="getPrice()" }> --></td>
						</tr>
						<tr>
							<th>金額：</th>
							<td  colspan="3"> <input type="text" name="money" value="<{$data.sMoney}>" style="width:280px"  > </td>	
							
						</tr>
						
						<tr>
							<th>年份：</th>
							<td  colspan="3"><{html_options name=year options=$menuYear class="xxx-select l2" selected=$year onchange="apply('change')" }></td>
						</tr>
						<tr>
							<th>地政士：</th>
							<td  colspan="3">
								<select name="sId" id="" class="xxx-select l2" onchange="getFeedbackData()">
									<{$optionScrivener}>
								</select>
								<span style="color:red"><b>※紅底為達標代書</b></span>
								
								<!-- <{html_options name=sId options=$menuScrivener class="xxx-select l2" selected=$sId onchange="getFeedbackData()"}>
								 -->
							</td>
						</tr>
						<tr>
							<th>備註：</th>
							<td  colspan="3"><textarea name="note" cols="50" rows="3" class="xxx-textarea l21" ><{$data.sNote}></textarea></td>
						</tr>
						
						<tr>
							<th>主管備註：</th>
							<td  colspan="3"><textarea name="note2" cols="50" rows="3" class="xxx-textarea l3" ><{$data.sNote2}></textarea></td>
						</tr>
						
						<tr>	
							<td colspan="4" class="tb-title">
								<span class="cb1"><input type="checkbox" name="receipt" class="lticket" value="1" id="receipt" <{$data.sReceipt}>><label for="receipt" ><span></span>收據已回繳</label></span>
								<{if $smarty.session.member_pDep == 9 || $smarty.session.member_pDep == 10 || $smarty.session.member_pDep == 1}>
								<span>
									<input type="button" value="刪除" onclick="delReceipt(<{$sId}>)">
								</span>	
								<{/if}>
							</td>
						</tr>
						<tr>
							<th>姓名：</th>
							<td><input type="text" name="Name" id="" class="lticket" value="<{$data.sName}>"></td>
							<th>傳票：</th>
							<td><input type="text" name="Ticket" id="" class="lticket" value="<{$data.sTicket}>"></td>
						</tr>

						<tr>
							<th>身分別：</th>
							<td>
								<{html_options name=Identify options=$menuIden class="xxx-select lticket" selected="<{$data.sIdentify}>" style="width:150px;"}>
								
							</td>	
							<th>證件號碼：</th>
							<td>
								<input type="text" name="IdentifyId" id="" class="lticket" onkeyup="checkID('',this.value,'')" value="<{$data.sIdentifyIdNumber}>">
								<span id="fId"></span>
							</td>
						</tr>
						<tr>

						</tr>
						<tr>
							<th>戶籍地址：</th>
							<td>
								<input type="hidden" name="fZipC" id="zipC" value="<{$data.sZip}>" />
	                            <input type="text" maxlength="6" name="zipCF" value="<{$data.sZip}>" id="zipCF" class="input-text-sml text-center lticket" readonly="readonly" value="" />
								<{if $disabledTicket == 1}>

								<select name="countryC" id="countryC"  class="xxx-select lticket" style="width: 150px;" disabled="disabled">
	                                <{$listCity}>
	                            </select>

	                            <span id="areaCR">
	                            <select name="areaC" id="areaC"  class="xxx-select lticket" style="width: 150px;" disabled="disabled">
	                                <{$listArea}>
	                            </select>

								<{else}>
								<select name="countryC" id="countryC" onchange="getArea2('countryC','areaC','zipC')" class="xxx-select lticket" style="width: 150px;">
	                                <{$listCity}>
	                            </select>

	                            <span id="areaCR">
	                            <select name="areaC" id="areaC" onchange="getZip2('areaC','zipC')" class="xxx-select lticket" style="width: 150px;">
	                                <{$listArea}>
	                            </select>
								<{/if}>
	                            

	                            </span>
	                            <input style="width:500px;" name="fAddrC" value="<{$data.sAddress}>" class="lticket"/>
							</td>
						</tr>
						
						<tr>	
							<td colspan="4" align="center">
								
									<input type="hidden" name="ok">
									<input type="hidden" name="cat" value="<{$cat}>">

									<{if $cat == 'add' || $smarty.session.member_ScrivenerLevel > 1}>
										<input type="button" value="送出" onclick="apply('save')">
									<{else}>
										<{if $smarty.session.member_ScrivenerLevel == 1 && $data.sApplicant == $smarty.session.member_id}>
											<input type="button" value="送出" onclick="apply('save')">
										<{/if}>

									<{/if}>
									

									<{if $cat != 'add'}>
										<{if $smarty.session.member_ScrivenerLevel == 1 && ($data.sStatus == 0 || $data.sStatus == 1)}>
											<input type="button" value="刪除" onclick="del(<{$sId}>)">

										<{/if}>

										<{if $smarty.session.member_ScrivenerLevel == 2 && ($data.sStatus == 2)}>

												<input type="button" value="刪除" onclick="del(<{$sId}>)">
										<{/if}>


										<{if  $smarty.session.member_id == 6 || $smarty.session.member_ScrivenerLevel == 5}>
											<input type="button" value="刪除" onclick="del(<{$sId}>)">
										<{/if}>
									<{/if}>
								
							</td>
						</tr>
					</table>
					
					</form>
				</center>	
            </div>
    </body>
</html>










