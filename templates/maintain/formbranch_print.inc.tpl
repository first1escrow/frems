<!DOCTYPE html>
<html >
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
	
	<title>列印</title>
	<style type="text/css">
            #tabs {
               width:980px;
               margin-left:auto; 
               margin-right:auto;
            }

            #tabs table th {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                border: 1px solid #CCC;
            }
            
            #tabs table th .sml {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                font-size: 10px;
            }

            #tabs table td{
            	border: 1px solid #CCC;
            }

            #users {
                margin-left:auto; 
                margin-right:auto;
                width:750px;
            }

            #detail {
                margin-left:auto; 
                margin-right:auto;
                width:750px;
            }

            #ec_money{
                text-align:right;
            }

            #pay_income{
                text-align:right;
            }

            #pay_spend {
                text-align:right;
            }

            #pay_total {
                text-align:right;
            }
            
            .input-text-per{
                width:96%;
            }
           
            .input-text-big {
                width:120px;
            }
            
            .input-text-mid{
                width:80px;
            }
            
            .input-text-sml{
                width:36px;
            }
            
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            
            .no-border {
                border-top:0px ;
                border-left:0px ;
                border-right:0px ;
            }
            
            .tb-title {
                font-size: 18px;
                padding-left:15px; 
                padding-top:10px; 
                padding-bottom:10px; 
                background: #E4BEB1;
            }
            
            .th_title_sml {
                font-size: 10px;
            }
            
            .sign-red{
                color: red;
            }
			.small_font {
				font-size: 9pt;
				line-height:1;
			}
            #tb001 {
                border-style:solid ;
                border-color:#CCC ;
                border-width:1px ;
                text-align:left ;
            }
            #dialog2 {
                background-image:url("/images/animated-overlay.gif") ;
                background-repeat: repeat-x;
                margin: 0px auto;
            } 
        </style>
</head>
<body id="dt_example" onload="window.print();">
<div id="wrapper">
	<div id="content">
		<div id="tabs">
			 <div id="tabs-contract">
                <form name="form_branch">
                <table border="0" width="100%" id="print">
                    <tr>
                        <td width="14%"></td>
                        <td width="19%"></td>
                        <td width="14%"></td>
                        <td width="19%"></td>
                        <td width="14%"></td>
                        <td width="19%"></td>
                    </tr>
                    <tr>
                        <th>仲介店編號︰</th>
                        <td>
                           <input type="hidden" name="id" value="<{$data.bId}>">
                           <input type="text"  maxlength="10" class="input-text-big" value="<{$data.bCode2}>" disabled='disabled' />
                        </td>
                        <th>密碼輸入︰</th>
                        <td>
                            <input type="text" name="password1" maxlength="12" class="input-text-big" value="<{$data.bPassword}>"  />
                            <br/>
                            密碼長度6~12碼，密碼必同時包含大、小寫英文字母阿拉伯數字0-9英文小寫視為不同密碼
                        </td>
                        <th>再次確認密碼︰</th>
                        <td>
                            <input type="password" name="password2" maxlength="12" class="input-text-big" value="<{$data.bPassword}>"  />
                        </td>
                    </tr>
                    <tr>
                        <th>仲介品牌名稱︰</th>
                        <td>
                            <{html_options name=bBrand options=$menu_brand selected=$data.bBrand}>
                        </td>
                        <th>仲介店名︰</th>
                        <td>
                            <input type="text" name="bStore" maxlength="20" class="input-text-per" value="<{$data.bStore}>"  />
                        </td>
                        <th>仲介商類型︰</th>
                        <td>
                            <{html_options name=bCategory options=$menu_categoryrealestate selected=$data.bCategory}>
                        </td>
                    </tr>
                    <tr>
                        <th>仲介公司︰</th>
                        <td colspan="3">
                            <input type="text" name="bName" maxlength="30" class="input-text-per" value="<{$data.bName}>"  />
                        </td>
                        <th>狀態︰</th>
                        <td>
                            <{html_options name=bStatus options=$menu_categorybranchstatus selected=$data.bStatus}>
                        </td>
                    </tr>
                    <tr>
                        <th>統一編號︰</th>
                        <td>
                            <input type="text" name="bSerialnum" maxlength="8" style="width:150px;" class="input-text-per" value="<{$data.bSerialnum}>"  />
							<span id="rId"></span>
                        </td>
                        <th>群組︰</th>
                        <td><{html_options name=bGroup options=$menu_group selected=$data.bGroup}></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
					
                    <tr>
                        <th>負責業務︰</th>
                        <td>
                             <{if $smarty.session.pBusinessEdit == '1' && $is_edit==1}>
                                 <{html_options name=bSales options=$menu_sales }>
                            <{elseif $smarty.session.pBusinessEdit == '0' && $is_edit==1}>
                                 <{html_options name=bSales options=$menu_sales disabled=disabled}>
                            <{else}>
                                <{html_options name=bSales options=$menu_sales }>
                            <{/if}>
                           
						
                        <{if $addBranch != '1'}>
                            <{if $smarty.session.pBusinessEdit == '1' || $smarty.session.pBusinessView == '1'}>
                                <input type="button" style="padding:5px;" value="Add" onclick="add()">
                            <{else}>
                                <input type="button" style="padding:5px;" value="Add"  disabled>
                            <{/if}>
							
						<{/if}>
						
							<span style="margin-left:10px;" id="salesList">
								<{$bSales}>
							</span>
                        </td>
						
                        <th>
							業務覆核：
						</th>
						
						<td colspan="3">
							<{if $smarty.session.member_id == '3' or $smarty.session.member_id == '6'}>
							<span style="margin-left:10px;" id="salesConfirm">
								<{$stage}>
							</span>
							<{/if}>
						</td>
                    </tr>
				
					<tr>
                        <th>總店/單店︰</th>
						<td>
							<label for="bStoreClass1">
								<input type="radio" id="bStoreClass1" onclick="show_hide_branch()" name="bStoreClass"<{if $data.bStoreClass == 1 }> checked<{/if}> value="1">總店　
							</label>
							<label for="bStoreClass2">
								<input type="radio" id="bStoreClass2" onclick="show_hide_branch()" name="bStoreClass"<{if $data.bStoreClass != 1 }> checked<{/if}> value="2">單店
							</label>							
							<br><br>
							<span style="font-size:9pt;">(如選擇總店，請務必設定分店)</span>
                        </td>
                        <th>分店︰</th>
                        <td colspan="3">
							<input type="text" id="bClassBranch" style="width:300px;display:<{if $data.bStoreClass!=1}>none<{/if}>;" name="bClassBranch" value="<{$data.bClassBranch}>" />
							<a href="get_branch.php" id="get_branch" style="display:<{if $data.bStoreClass!=1}>none<{/if}>;" class="small_font ajax">選擇</a>
							<br>
							<span style="font-size:9pt;">(可自行輸入店頭編號或搜尋選擇分店；中間區隔以";"為主)</span>
                        </td>
					</tr>
                    <tr>
                        <th>前台顯示設定︰</th>
                        <td>
							<label for="bAccDetail">
								<input type="checkbox" id="bAccDetail" name="bAccDetail"<{if $data.bAccDetail == 1 || $data.bAccDetail == ''}> checked<{/if}> value="1">帳務明細查詢
							</label>
                        </td>
                        <th>前台顯示設定︰</th>
                        <td>
							<label for="bCaseDetail">
								<input type="checkbox" id="bCaseDetail" name="bCaseDetail"<{if $data.bCaseDetail == 1 || $data.bCaseDetail == ''}> checked<{/if}> value="1">案件明細查詢
							</label>
                        </td>
                        <th>前台顯示設定︰</th>
                        <td>
							<label for="bFeedbackCase">
								<input type="checkbox" id="bFeedbackCase" name="bFeedbackCase"<{if $data.bFeedbackCase == 1}> checked<{/if}> value="1">回饋案件查詢
							</label>
                        </td>
                    </tr>
                    <tr>
                        <th>店東︰</th>
                        <td>
                            <input type="text" name="bManager" maxlength="10" class="input-text-per" value="<{$data.bManager}>"  />
                        </td>
                        <th>聯絡電話︰</th>
                        <td>
                            <input type="text" name="bTelArea" maxlength="3" class="input-text-sml" value="<{$data.bTelArea}>" /> -
                            <input type="text" name="bTelMain" maxlength="10" class="input-text-mid" value="<{$data.bTelMain}>" />
                        </td>
                        <th>傳真號碼︰</th>
                        <td>
                            <input type="text" name="bFaxArea" maxlength="3" class="input-text-sml" value="<{$data.bFaxArea}>" /> -
                            <input type="text" name="bFaxMain" maxlength="10" class="input-text-mid" value="<{$data.bFaxMain}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>行動電話︰</th>
                        <td><input type="text" name="bMobileNum" maxlength="14" class="input-text-per" value="<{$data.bMobileNum}>" /> </td>
                        <th>電子郵件︰</th>
                        <td><input type="text" name="bEmail" maxlength="255" class="input-text-per" value="<{$data.bEmail}>" /></td>
                    </tr>
                    <tr>
                        <th><span style='color:#FF0000;'>*</span>聯絡地址︰</th>
                        <td colspan="4">
                            <input type="hidden" name="zip" id="zip" value="<{$data.bZip}>" />
                            <input type="text" maxlength="6" name="zipF" id="zipF" class="input-text-sml text-center" readonly="readonly" value="<{$data.bZip|substr:0:3}>" />
                            <select class="input-text-big" name="country" id="country" onchange="getArea('country','area','zip')">
                                <{$listCity}>
                            </select>
							<span id="areaR">
                            <select class="input-text-big" name="area" id="area" onchange="getZip('area','zip')">
                                <{$listArea}>
                            </select>
							</span>
                            <input style="width:330px;" name="addr" value="<{$data.bAddress}>" />
                        </td>
						<td>
							<{if $data.bAddress != ''}>
							<a href="../others/maps.php?zips=<{$data.bZip}>&addr=<{$data.bAddress}>" style="font-size:10pt;" class="iframe">查看地圖</a>
							<{/if}>
						</td>
                    </tr>
                    <tr>
                        <th>本票同意書︰</th>
                        <td>
                            <{html_checkboxes name='bCashierOrderHas' options=$menu_cashierorderhas selected=$data.bCashierOrderHas separator=' '}>
                        </td>
                        <th>本票票號︰</th>
                        <td>
                            <input type="text" name="bCashierOrderNumber" maxlength="20" class="input-text-per" value="<{$data.bCashierOrderNumber}>" /> 
                        </td>
                        <th>本票金額︰</th>
                        <td>
                            <input type="text" name="bCashierOrderMoney" size="13" class="text-right" value="<{$data.bCashierOrderMoney}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>收票承辦人︰</th>
                        <td>
                            <{html_options name=bCashierOrderPpl options=$menu_ppl selected=$data.bCashierOrderPpl}>
                        </td>
                        <th>發票(法人)︰</th>
                        <td>
                            <input type="text" name="bInvoice1" maxlength="255" class="input-text-per" value="<{$data.bInvoice1}>" />
                        </td>
                        <th>發票(自然人)︰</th>
                        <td>
                            <input type="text" name="bInvoice2" maxlength="255" class="input-text-per" value="<{$data.bInvoice2}>" />
                        </td>
                    </tr>
                     <tr>
                        <th>開票日期︰</th>
                        <td>
                            <input type="text" name="bCashierOrderDate" onclick="showdate(form_branch.bCashierOrderDate)" maxlength="15" class="calender input-text-big" value="<{$data.bCashierOrderDate}>" readonly />
                        </td>
                        <th>確核交存日期︰</th>
                        <td>
                           <input type="text" name="bCashierOrderSave" onclick="showdate(form_branch.bCashierOrderSave)" maxlength="15" class="calender input-text-big" value="<{$data.bCashierOrderSave}>" readonly />
                        </td>
                        <td></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <th>本票備註︰</th>
                        <td colspan="5"><input type="text" name="bCashierOrderRemark" maxlength="255" class="input-text-per" value="<{$data.bCashierOrderRemark}>" /></td>
                    </tr>
                     <{if $smarty.session.member_pFeedBackModify!='0'}>
                    <tr>
                        <th>回饋金備註︰</th>
                        <td colspan="5"><textarea name="bRenote" class="input-text-per" <{$_disabled}> ><{$data.bRenote}></textarea></td>
                    </tr>
                    <{/if}>
                    <tr>
                        <th>備註說明︰</th>
                        <td colspan="5">
                            <textarea name="bCashierOrderMemo" class="input-text-per"><{$data.bCashierOrderMemo}></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>可用系統︰</th>
                        <td>
                            <{html_checkboxes name='bSystem' options=$menu_categorybank_twhg selected=$data.bSystem separator=' <br /> '}>
                        </td>
                        <{if $smarty.session.member_pFeedBackModify!='0'}>
                        <th>回饋比率︰</th>
                        <td>
                            <!-- 萬分之 -->
                            百分之
							<input type="text" name="bRecall" maxlength="5" style="width:30px;" class="input-text-big" value="<{$data.bRecall}>" <{$_disabled}> />
                        </td>
                        <{else}>
                        <td colspan="2">&nbsp;</td>
                        <{/if}>
                        <th>保證費率</th>
                        <td>
                            <input type="text" name="bCertified" maxlength="15" class="input-text-big" value="<{$data.bCertified}>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="tb-title">
                            指定解匯帳戶
                        </td>
                    </tr>
                    <tr>
                        <th>總行(一)︰</th>
                        <td>
                            <{html_options name=bAccountNum1 options=$menu_bank selected=$data.bAccountNum1 class="acc_disabled"}>
                        </td>
                        <th>分行(一)︰</th>
                        <td>
                            <select name="bAccountNum2" class="input-text-per acc_disabled">
							<{$menu_branch}>
                            </select>
                        </td>
                        <th>指定帳號(一)︰</th>
                        <td>
                            <input type="text" name="bAccount3" maxlength="14" class="input-text-per acc_disabled" value="<{$data.bAccount3}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(一)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount4" class="input-text-per acc_disabled" value="<{$data.bAccount4}>" />
                        </td>
                        <td></td>
                        <th>停用(一)</th>
                        <td>
							<{html_checkboxes name="bAccountUnused" options=$menu_accunused selected=$data.bAccountUnused }>
						</td>
                    </tr>
                    <tr>
                        <th>總行(二)︰</th>
                        <td>
                            <{html_options name=bAccountNum11 options=$menu_bank selected=$data.bAccountNum11 class="acc_disabled1"}>
                        </td>
                        <th>分行(二)︰</th>
                        <td>
                            <select name="bAccountNum21" class="input-text-per acc_disabled1">
							<{$menu_branch21}>
                            </select>
                        </td>
                        <th>指定帳號(二)︰</th>
                        <td>
                            <input type="text" name="bAccount31" maxlength="14" class="input-text-per acc_disabled1" value="<{$data.bAccount31}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(二)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount41" class="input-text-per acc_disabled1" value="<{$data.bAccount41}>" />
                        </td>
                        <td></td>
                        <th>停用(二)</th>
                        <td>
							<{html_checkboxes name="bAccountUnused1" options=$menu_accunused selected=$data.bAccountUnused1}>
						</td>
                    </tr>
                     <tr>
                        <th>總行(三)︰</th>
                        <td>
                            <{html_options name=bAccountNum12 options=$menu_bank selected=$data.bAccountNum12 class="acc_disabled2"}>
                        </td>
                        <th>分行(三)︰</th>
                        <td>
                            <select name="bAccountNum22" class="input-text-per acc_disabled2">
                            <{$menu_branch22}>
                            </select>
                        </td>
                        <th>指定帳號(三)︰</th>
                        <td>
                            <input type="text" name="bAccount32" maxlength="14" class="input-text-per acc_disabled2" value="<{$data.bAccount32}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(三)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount42" class="input-text-per acc_disabled2" value="<{$data.bAccount42}>" />
                        </td>
                        <td></td>
                         <th>停用(三)</th>
                        <td>
							<{html_checkboxes name="bAccountUnused2" options=$menu_accunused selected=$data.bAccountUnused2}>
						</td>
                    </tr>

					<{if $is_edit == '1' }>
                    <tr>
                        <td colspan="6" class="tb-title">
                            簡訊發送對象<div style="float:right;padding-right:10px;">
								<a href="formbranchsms.php?bId=<{$data.bId}>" class="iframe" style="font-size:9pt;">編修簡訊對象</a>
							</div>
                        </td>
                    </tr>
                    <{foreach from=$data_sms key=key item=item}>
                    <tr>
                        <th>職稱︰</th>
                        <td>
                            <input type="text" class="input-text-mid" value="<{$item.tTitle}>" disabled='disabled'>
							<input type="checkbox" value="<{$item.bMobile}>"<{$item.defaultSms}> disabled='disabled'>
                        </td>
                        <th>姓名︰</th>
                        <td>
                            <input type="text" maxlength="14" class="input-text-per" value="<{$item.bName}>" disabled='disabled'>
                        </td>
                        <th>行動電話︰</th>
                        <td>
                            <input type="text" maxlength="10" class="input-text-per" value="<{$item.bMobile}>" disabled='disabled'>
                        </td>
                    </tr>
                    <{/foreach}>
					<{/if}>
                    <tr>
                        <td colspan="6" class="tb-title">
                            回饋金對象資料
                        </td>
                    </tr>
                    <tr>
                        <th>回饋方式︰</th>
                        <td>
                            <{html_options name="bFeedBack" options=$menu_categoryrecall selected=$data.bFeedBack}>
                        </td>
                        <th>姓名/抬頭︰</th>
                        <td>
                            <input type="text" name="bTtitle" maxlength="15" class="input-text-big" value="<{$data.bTtitle}>" />
                        </td>
                        <th>店長行動電話︰</th>
                        <td><input type="text" name="bMobileNum2" maxlength="10" class="input-text-big" value="<{$data.bMobileNum2}>" /></td>
                    </tr>
                    <tr>
                        <th>身份別︰</th>
                        <td>
                            <{html_options name=bIdentity options=$menu_categoryidentify selected=$data.bIdentity}>
                        </td>
                        <th>證件號碼︰</th>
                        <td>
                            <input type="text" name="bIdentityNumber" maxlength="15" class="input-text-big" value="<{$data.bIdentityNumber}>" />
							<span id="fId"></span>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>聯絡地址︰</th>
                        <td colspan="5">
                            <input type="hidden" name="zip3" id="zip3" value="<{$data.bZip3}>" />
                            <input type="text" maxlength="6" name="zip3F" id="zip3F" class="input-text-sml text-center" readonly="readonly" value="<{$data.bZip3}>" />
                            <select class="input-text-big" name="country3" id="country3" onchange="getArea('country3','area3','zip3')">
                                <{$listCity3}>
                            </select>
							<span id="area3R">
                            <select class="input-text-big" name="area3" id="area3" onchange="getZip('area3','zip3')">
                                <{$listArea3}>
                            </select>
							</span>
                            <input style="width:500px;" name="addr3" value="<{$data.bAddr3}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶藉地址︰</th>
                        <td colspan="5">
                            <input type="hidden" name="zip2" id="zip2" value="<{$data.bZip2}>" />
                            <input type="text" maxlength="6" name="zip2F" id="zip2F" class="input-text-sml text-center" readonly="readonly" value="<{$data.bZip2}>" />
                            <select class="input-text-big" name="country2" id="country2" onchange="getArea('country2','area2','zip2')">
								<{$listCity2}>
                            </select>
							<span id="area2R">
                            <select class="input-text-big" name="area2" id="area2" onchange="getZip('area2','zip2')">
                                <{$listArea2}>
                            </select>
							</span>
                            <input style="width:500px;" name="addr2" value="<{$data.bAddr2}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>電子郵件︰</th>
                        <td colspan="3">
                            <input type="text" name="bEmail2" maxlength="255" class="input-text-per" value="<{$data.bEmail2}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>總行︰</th>
                        <td>
                            <{html_options name=bAccountNum5 options=$menu_bank selected=$data.bAccountNum5 }>
                        </td>
                        <th>分行︰</th>
                        <td>
                            <select name="bAccountNum6" class="input-text-per">
							<{$menu_branch6}>
                            </select>
                        </td>
                        <th>指定帳號︰</th>
                        <td>
                            <input type="text" name="bAccount7" maxlength="14" class="input-text-per" value="<{$data.bAccount7}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount8" maxlength="20" class="input-text-per" value="<{$data.bAccount8}>" />
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                     <tr>
                        <td colspan="6" class="tb-title">
                            回饋金簡訊對象資料<div style="float:right;padding-right:10px;">
                                <a href="formbranchfeedback.php?bId=<{$data.bId}>" class="iframe" style="font-size:9pt;">編修簡訊對象</a></div>

                        </td>
                    </tr>
                   <{foreach from=$data_feedsms key=key item=item}>
                    <tr>
                        <th>職稱︰</th>
                        <td>
                            <input type="text" class="input-text-mid" value="<{$item.tTitle}>" disabled='disabled'>
                           
                        </td>
                        <th>姓名︰</th>
                        <td>
                            <input type="text" maxlength="14" class="input-text-per" value="<{$item.bName}>" disabled='disabled'>
                        </td>
                        <th>行動電話︰</th>
                        <td>
                            <input type="text" maxlength="10" class="input-text-per" value="<{$item.bMobile}>" disabled='disabled'>
                        </td>
                    </tr>
                    <{/foreach}>
                    <tr>
                        <td colspan="6" class="tb-title">
                            季回饋金額

                        </td>
                    </tr>

                    <tr>
                        <td colspan="6">
							<{html_options name="FBYear" style="width:100px;" options=$FBYear selected=$FBYearSelect}>
							年度　
                            第一季
							<input type="text" name="fbs1" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                            第二季
							<input type="text" name="fbs2" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                            第三季
							<input type="text" name="fbs3" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                            第四季
							<input type="text" name="fbs4" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                        </td>
                    </tr>
                    <tr>
                            <td colspan="6" class="tb-title">&nbsp;</td>
                    </tr>
                    <tr>

                        <th>建立時間：</th>
                        <td><{$data.bCreat_time}></td>
                        <th >最後修改人：</th>
                        <td><{$data.bEditor}></td>
                        <th >修改時間：</th>
                        <td><{$data.bModify_time}></td>
                          
                    </tr>
             </table>
                </form>
                
                
            </div>
		</div>
	</div>
</div>
</body>
</html>