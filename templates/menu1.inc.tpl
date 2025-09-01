
        <{include file='checkIn.inc.tpl'}>

        <ul class="sf-menu" id="menuBar" style="margin: 0px 0px 0px 0px;">
            <{if isset($smarty.session.member_casemanage) && $smarty.session.member_casemanage == '1'}>
            <li class="current" style="width:120px;">
                <a href="#a">案件管理</a>
                <ul>
                    <{if isset($smarty.session.member_addcase) && $smarty.session.member_addcase == '1'}>
                    <li>
                        <a href="/escrow/formbuyowneradd.php">新增案件</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_income) && $smarty.session.member_income != '2'}>
                    <li class="current">
                        <a href="/income/listinspection.php">入帳確認作業</a>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_pDep != 11 || $smarty.session.member_id == 69}>
                        <{if isset($smarty.session.member_bankcheck) && $smarty.session.member_bankcheck == '1'}>
                        <li>
                            <a href="/bank/list.php" target="_blank">出款審核作業</a>
                        </li>
                        <{else if isset($smarty.session.member_bankcheck) && $smarty.session.member_bankcheck == '0'}>
                        <li>
                            <a href="/bank/list2.php" target="_blank">出款審核作業</a>
                        </li>
                        <{/if}>
                    <{/if}>

                    <{if isset($smarty.session.member_searchcase) && $smarty.session.member_searchcase == '1'}>
                    <li>
                        <a href="/inquire/buyerownerinquery.php">案件查詢(出款建檔)</a>
                    </li>
                    <{/if}>
                    
                    <{if isset($smarty.session.member_codeChange) && $smarty.session.member_codeChange == '1'}>
                    <li>
                        <a href="/escrow/searchscrivener.php">保號->代書</a>
                    </li>

                        <{if $smarty.session.member_pDep != 7}>
                        <li>
                            <a href="/inquire/interestList.php">案件利息明細</a>
                        </li>
                        <{/if}>
                    <{/if}>

                    <{if isset($smarty.session.member_bankcheck) && $smarty.session.member_bankcheck == '1'}>
                    <li>
                        <a href="/bank/new/export_interest.php" target="_blank" class="int_packing">利息出款</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_tEcontract) && $smarty.session.member_tEcontract != '0'}>
                    <li>
                        <a href="/others/formCategoryList.php">電子簽約合約書</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_addcertifty) && $smarty.session.member_addcertifty != '0'}>
                    <li>
                        <a href="#" onclick="window.open('/bank/create.php', '_blank', config='height=700,width=650,scrollbars=yes');">申請履保合約書</a>
                    </li>
                    <li>
                        <a href="/bank/applyBankCode.php">地政士合約申請</a>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_pDep|in_array: [5, 6] || (isset($smarty.session.pTwhgCase) && $smarty.session.pTwhgCase == 1)}>
                    <li>
                        <a href="/report2/TwhgCase.php">台屋未建檔案件</a>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_id|in_array: [1, 6]}>
                    <li>
                        <a href="/report2/sellerNoteReport.php">非賣方本人備註</a>
                    </li>
                    <{/if}>
                </ul>
            </li>
            <{/if}>

            <{if isset($smarty.session.member_basicmanage) && $smarty.session.member_basicmanage == '1'}>
            <li style="width:150px;">
                <a href="#">基本資料維護</a>
                <ul>
                    <{if isset($smarty.session.pScrivenerBlackList) && $smarty.session.pScrivenerBlackList == 1}>
                    <li>
                        <a href="/maintain/scrivenerBlackList.php">地政士黑名單</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_branchgroup) && $smarty.session.member_branchgroup == '1'}>
                    <li>
                        <a href="/maintain/listbranchgroup.php">仲介群組維護</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_brand) && $smarty.session.member_brand == '1'}>
                    <li>
                        <a href="/maintain/listbrand.php">仲介品牌維護</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_branch) && $smarty.session.member_branch != '0'}>
                    <li>
                        <a href="/maintain/listbranch.php">仲介店頭維護</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_scrivener) && $smarty.session.member_scrivener != '0'}>
                    <li>
                        <a href="/maintain/listscrivener.php">地政士資料維護</a>
                    </li>
                    <{/if}>
                </ul>
            </li>
            <{/if}>

            <{if isset($smarty.session.member_pAccList) && $smarty.session.member_pAccList == '1'}>
            <li style="width:120px;">
                <a href="#">會計作業</a>
                <ul>
                    <{if isset($smarty.session.member_invoice) && $smarty.session.member_invoice|in_array: ['0', '1']}>
                    <li>
                        <a href="#">發票作業</a>
                        <ul>
                            <{if isset($smarty.session.member_bankchecklist) && $smarty.session.member_bankchecklist == '1'}>
                            <li>
                                <a href="/accounting/acc_checklist.php">銀行收款明細表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_invoice) && $smarty.session.member_invoice == '1'}>
                            <li>
                                <a href="/accounting/invoicesearch.php">文中匯入檔</a>
                            </li>

                            <li>
                                <a href="/accounting/import_income.php">上傳開立狀況表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_invoice) && ($smarty.session.member_invoice == '1' || $smarty.session.member_invoice == '0')}>
                            <li>
                                <a href="/accounting/invoiceModify.php">官網維護作業</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_invoice) && $smarty.session.member_invoice == '1'}>    
                            <li>
                                <a href="/accounting/invoicePower.php">發票修改權限</a>
                            </li>

                            <li>
                                <a href="/accounting/invoiceNoSendReport.php">紙本尚未郵寄名單</a>
                            </li>
                            
                            <li>
                                <a href="/accounting/invoiceAnalysiscase.php">發票數據統計</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_id != '31'}> 
                        <{if $smarty.session.member_pDep|in_array: [1, 9, 10]}>
                        <li>
                            <a href="#">各類報表</a>
                            <ul>
                                <{if isset($smarty.session.member_tax) && $smarty.session.member_tax == '1'}>
                                <li>
                                    <a href="/accounting/taxreceipt.php">年度扣繳憑單</a>
                                </li>
                                <{/if}>
                            </ul>
                        </li>
                        <{/if}>

                        <{if (isset($smarty.session.member_pFeedBack) && $smarty.session.member_pFeedBack == '1') || $smarty.session.member_pDep|in_array: [5, 6]}>
                        <li>
                            <a href="#">回饋金資料</a>
                            <ul>
                                <{if $smarty.session.member_pDep|in_array: [10, 9] || $smarty.session.member_id == 6}>
                                <li>
                                    <a href="/accounting/feedbackSend.php">回饋金寄送名單</a>
                                </li>

                                <li>
                                    <a href="/accounting/feedbackDataUpdate.php">回饋金對象匯入</a>
                                </li>
                                
                                <li>
                                    <a href="/report/casefeedback.php">回饋案件查詢</a>
                                </li>
                                
                                <li>
                                    <a href="#">*付款通知書</a>
                                    <ul>
                                        <li>
                                            <a href="/accounting/casefeedbackPDF2.php">季月結查詢</a>
                                        </li>
                                        <li>
                                            <a href="/accounting/caseFeedbackPayByCase.php">隨案結查詢</a>
                                        </li>
                                    </ul>
                                </li>

                                <li>
                                    <a href="/accounting/storeFeedBack.php">*店家回饋出款檔</a>
                                </li>
                                <{/if}>
                                
                                <li>
                                    <a href="/accounting/storeFeedBackUpload.php">*回饋金匯款紀錄</a>
                                </li>

                                <{if $smarty.session.member_pDep|in_array: [10, 9] || $smarty.session.member_id == 6}>
                                    <li>
                                        <a href="/accounting/import_feedSmsList.php">回饋金簡訊對象匯入</a>
                                    </li>

                                    <{if isset($smarty.session.member_pFeedBackModify) && $smarty.session.member_pFeedBackModify == '1'}>
                                    <li>
                                        <a href="/escrow/feedback_sms.php">回饋金簡訊</a>
                                    </li>
                                    <{* <li>
                                        <a href="/escrow/feedback_sms_v1.php">回饋金簡訊<span style="font-size:9pt;font-weight:bold;color:red;">(測試版)</span></a>
                                    </li> *}>
                                    <{/if}>

                                    <{if isset($smarty.session.member_acc_search) && $smarty.session.member_acc_search == '1'}>
                                    <li>
                                        <a href="/accounting/search_data.php">回饋對象明細</a>
                                    </li>
                                    <{/if}>

                                    <li>
                                        <a href="/accounting/casefeedbackSmsSearch.php">回饋金簡訊數量查詢</a>
                                    </li>
                                <{/if}>
                                <{if $smarty.session.member_id == 48}>
                                    <li>
                                        <a href="/accounting/search_data.php">回饋對象明細</a>
                                    </li>
                                <{/if}>
                            </ul>
                        </li>
                        <{/if}>
                    <{/if}>

                    <{if isset($smarty.session.pAccPayByCase) && $smarty.session.pAccPayByCase == '1' }>
                    <li>
                        <a href="#">回饋金隨案出款</a>
                        <ul>
                            <li>
                                <a href="/accounting/payByCaseAccountingConfirm.php">確認清單</a>
                            </li>
                            <li>
                                <a href="/accounting/payByCaseAccountingReceipt.php">收據繳回確認</a>
                            </li>
                            <li>
                                <a href="/accounting/makePayByCaseData.php">回饋金重整</a>
                            </li>
                        </ul>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_ScrivenerLevel) && $smarty.session.member_ScrivenerLevel > 0 }>
                    <li>
                        <a href="#">生日禮管理</a>
                        <ul>
                            <li>
                                <a href="/scrivener/qualifiedList.php">生日禮達標名單</a>
                            </li>

                            <li>
                                <a href="/scrivener/scrivenerPresent.php">地政士生日禮</a>
                            </li>

                            <{if $smarty.session.member_pDep != 7 && $smarty.session.member_pDep != 11}> 
                            <li>
                                <a href="/scrivener/PresentList.php">生日禮品項</a>
                            </li>
                            
                            <li>
                                <a href="/accounting/scrivenerPresentReport.php">地政士生日禮申請表單</a>
                            </li>

                            <li>
                                <a href="/accounting/scrivenerPresentTax.php">扣繳申報</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pScrivenerLevelList) && $smarty.session.pScrivenerLevelList == 1 }>
                            <li>
                                <a href="/scrivener/scrivenerPresentList.php">代書生日禮紀錄查詢(非禮券)</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>
                    <{/if}>   
                    
                    <{if $smarty.session.member_id == '6'}>
                    <li>
                        <a href="/payment/paymentList.php">斡旋支付</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.smsCount) && $smarty.session.smsCount == 1 }>
                    <li>
                        <a href="/report2/smsCount.php">簡訊數量</a>
                    </li>
                    <{/if}>


                </ul>
            </li>
            <{/if}>

            <{if isset($smarty.session.member_reportmanage) && $smarty.session.member_reportmanage == '1'}>
            <li style="width:120px;">
                <a href="#">報表作業</a>
                <ul>
                    <li>
                        <a href="#">案件統計報表</a>
                        <ul>
                            <{if isset($smarty.session.member_pApplyCase) && $smarty.session.member_pApplyCase == '1'}>
                            <li>
                                <a href="/report/applycase.php">案件統計表</a>
                            </li>
                            <{/if}>
                            
                            <{if isset($smarty.session.member_ScrivenerLevel) && $smarty.session.member_ScrivenerLevel == 5 }>
                            <li>
                                <a href="/scrivener/scrivenerPresent.php">地政士生日禮</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pCaseTotal) && $smarty.session.pCaseTotal == 1 }>
                            <li>
                                <a href="/report/totalReport.php">案件統計總表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pCaseReport108) && $smarty.session.pCaseReport108 == 1}>
                            <li>
                                <a href="/report/applycase2019.php">案件統計表108</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_pAnalysisCase) && $smarty.session.member_pAnalysisCase == '1'}>
                            <li>
                                <a href="/report/analysiscase.php">案件數量統計表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_pHouseExponent) && $smarty.session.member_pHouseExponent == '1'}>
                            <li>
                                <a href="/report/houseExponent.php">房價指數統計表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_pCertifiedMoney) && $smarty.session.member_pCertifiedMoney == '1'}>
                            <li>
                                <a href="/report/certified.php">保證費統計表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_pRealtyCharge) && $smarty.session.member_pRealtyCharge == '1'}>
                            <li>
                                <a href="/report/realty_service_charge.php">直營服務費統計表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_FeedBackError) && $smarty.session.member_FeedBackError == '1'}>
                            <li>
                                <a href="/report/feedBackError.php">回饋金報表</a>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id|in_array: [1, 6]}>
                            <li>
                                <a href="/report/searchPhone.php">有缺回饋通知簡訊對象表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_pBranchSalse) && $smarty.session.member_pBranchSalse == '1' }>
                            <li>
                                <a href="/report2/branchSales.php">仲介店/地政士排名</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pCaseTransSearch) && $smarty.session.pCaseTransSearch == 1}>
                            <li>
                                <a href="/report/caseTransSearch.php">案件移轉量</a>
                            </li>
                            <{/if}>
                            <{if isset($smarty.session.pDailyCase) && $smarty.session.pDailyCase == 1}>
                            <li>
                                <a href="/report/daily_case.php">結案日統計表</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>

                    <{if $smarty.session.member_pDep != 7 && ($smarty.session.member_pDep != 11 || $smarty.session.member_id == 48)}> 
                    <li>
                        <a href="#">業務統計報表</a>
                        <ul>
                            <{if isset($smarty.session.member_pSalesCase) && $smarty.session.member_pSalesCase == '1'}>
                            <li>
                                <a href="/report/salesReport.php">績效一覽表</a>
                            </li>
                            <{/if}>

                            <{if (isset($smarty.session.member_pSalesCase) && $smarty.session.member_pSalesCase == '1') && $smarty.session.member_pDep != 11}>
                            <li>
                                <a href="/report/applycaseOriginal.php">業務案件統計表2</a>
                            </li>
                            <{/if}>
                            
                            <{if isset($smarty.session.member_pRealtyCaseList) && $smarty.session.member_pRealtyCaseList != '0'}>
                            <li>
                                <a href="/report/realtycomp.php">仲介店比較表</a>
                            </li>

                            <li>
                                <a href="/report/realtycompScrivener.php">地政士比較表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pBusiness_report) && $smarty.session.pBusiness_report == '1'}>
                            <li>
                                <a href="/report/charge_report2.php">業績統計表</a>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id|in_array: [1, 2, 3, 6]}>
                            <li>
                                <a href="/report/salesMonthlyReport.php" target="_blank">月業績主管報表</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_pDep != 11 || (isset($smarty.session.member_csv) && $smarty.session.member_csv == '1')}> 
                    <li>
                        <a href="#">仲介地政士統計報表</a>
                        <ul>
                            <{if (isset($smarty.session.member_csv) && $smarty.session.member_csv == '1') || $smarty.session.member_id == 1 }>
                            <li>
                                <a href="/report/csv_download.php">仲介地政士CSV檔案下載</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_csv) && $smarty.session.member_csv == '1' && $smarty.session.member_pDep != 11}>
                            <li>
                                <a href="/report/storeClose.php">仲介地政士開關店統計</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_pRealtyCaseList) && $smarty.session.member_pRealtyCaseList != '0'}>
                            <li>
                                <a href="/report/realtycomp.php">仲介店比較表</a>
                            </li>

                            <li>
                                <a href="/report/realtycompScrivener.php">地政士比較表</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.member_pNoCaseReport) && $smarty.session.member_pNoCaseReport == '1'}>
                            <li>
                                <a href="/report/storeTrackingList.php">仲介地政士未進案名單</a>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id|in_array: [3, 6]}>
                            <li>
                                <a href="/report/scrivenerBirthday.php">地政士生日名單</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pBrandCount) && $smarty.session.pBrandCount == 1}>
                            <li>
                                <a href="/report/brandReport.php">各品牌店家名單</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>
                    <{/if}>
                    
                    <{if isset($smarty.session.member_banktrans) && $smarty.session.member_banktrans|in_array: ['1', '2'] && $smarty.session.member_pDep != 7}>
                    <li>
                        <a href="javascript:void(0);">經辦報表</a>
                        <ul>
                            <li>
                                <a href="javascript:void(0);">錯誤率查詢</a>
                                <ul>
                                    <{if isset($smarty.session.member_banktrans) && $smarty.session.member_banktrans == '1'}>
                                    <li>
                                        <a href="/banktrans/banktrans_record.php">出款錯誤紀錄</a>
                                    </li>
                                    
                                    <li>
                                        <a href="/banktrans/caseEnd_record.php">結案錯誤紀錄表-財會部門</a>
                                    </li>
                                    <{/if}>

                                    <li>
                                        <a href="/banktrans/banktrans_report.php">人員錯誤紀錄表</a>
                                    </li>
                                    
                                    <li>
                                        <a href="/report/banktrans_report.php">出款數量查詢</a>
                                    </li>
                                </ul>
                            </li>

                            <{if $smarty.session.member_id|in_array: [1, 2, 3, 6, 48] || (isset($smarty.session.UndertakerCaseCharts) && $smarty.session.UndertakerCaseCharts == 1)}>
                            <li>
                                <a href="/charts/UndertakerCaseCharts.php">區域統計表</a>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id|in_array: [1, 2, 3, 6] || (isset($smarty.session.UndertakerCaseCharts) && $smarty.session.UndertakerCaseCharts == 1)}>
                            <li>
                                <a href="/banktrans/caseProcessingCount2.php">進行中最高落點查詢</a>
                            </li>
                            <li>
                                <a href="/banktrans/bonusList.php">經辦獎勵查詢</a>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id|in_array: [1, 2, 3, 6, 22]}>
                            <li>
                                <a href="/report2/QuestionAnalysisSearch.php">問卷統計</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_transNoEnd) && $smarty.session.member_transNoEnd == '1'}>
                    <li>
                        <a href="/report/transNoEnd.php">收款未結案報表</a>
                    </li>
                    <{/if}>

                    <li>
                        <a href="/report2/bankCase.php">銀行未結案表</a>
                    </li>

                    <li>
                        <a href="/report2/prePaidInterest.php">代墊利息專戶</a>
                    </li>

                    <{if isset($smarty.session.member_pScrivenerCase) && $smarty.session.member_pScrivenerCase == '1'}>
                    <li>
                        <a href="/report/ScrivenerCaseCount.php">代書庫存有效合約書</a>
                    </li>
                    <{/if}>
                    
                    <{if isset($smarty.session.pCertifiedFeeAnalysis) && $smarty.session.pCertifiedFeeAnalysis == '1'}>
                    <li>
                        <a href="/report/certifiedFeeAnalysis.php">未收足案件統計</a>
                    </li>
                    <{/if}>

                    <{if (isset($smarty.session.member_act_report) && $smarty.session.member_act_report == '1') || $smarty.session.member_id == 1}>
                    <li>
                        <a href="#">活動報表</a>
                        <ul>
                            <{if $smarty.session.member_pDep != 7}>
                            <li>
                                <a href="/actives/actives_report.php">舊活動報表</a>
                            </li>
                            
                            <li>
                                <a href="/actives/act_202009.php">2020回饋店東</a>
                            </li>
                            <{/if}>

                            <li>
                                <a href="/actives/act_202103.php">2021地政士活動</a>
                            </li>

                            <{if $smarty.session.member_pDep != 7}>
                            <li>
                                <a href="/actives/act_202110.php">2021回饋店東</a>
                            </li>

                            <li>
                                <a href="#">2022</a>
                                <ul>
                                    <li>
                                        <a href="/actives/act_2022_1.php">履保案件送第一，年終禮券大放送!!!</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">2023</a>
                                <ul>
                                    <li>
                                        <a href="/actives/act_2023_1.php">鴻兔大展我要第一贈獎</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">2025</a>
                                <ul>
                                    <li>
                                        <a href="/actives/act_2025_1.php">蛇采飛揚我要第一贈獎</a>
                                    </li>
                                </ul>
                            </li>
                            <{/if}>
                        </ul>
                    </li>

                    <li>
                        <a href="#">回報銀行</a>
                        <ul>
                            <li>
                                <a href="#">一銀</a>
                                <ul>
                                    <{if isset($smarty.session.pSellerNoteReport) && $smarty.session.pSellerNoteReport == 1}>
                                    <li>
                                        <a href="/report2/finalPaymentNoneSellerList.php">尾款非賣方名單</a>
                                    </li>
                                    <{/if}>
                                </ul>
                            </li>

                            <li>
                                <a href="#">台新</a>
                                <ul>
                                    <{if isset($smarty.session.pTaishinReport) && $smarty.session.pTaishinReport == 1}>
                                    <li>
                                        <a href="/taishin/report.php">台新ATM企業送金存款人姓名</a>
                                    </li>
                                    <li>
                                        <a href="/report2/finalPaymentNoneSellerList.php?bank=taishin">尾款非賣方名單</a>
                                    </li>
                                    <{/if}>
                                </ul>
                            </li>

                            <li>
                                <a href="#">永豐</a>
                            </li>
                        </ul>
                    </li>
                    <{/if}>
                </ul>
            </li>
            <{/if}>

            <{if isset($smarty.session.member_systemmanage) && $smarty.session.member_systemmanage == '1'}>
            <li style="width:120px;">
                <a href="#">系統管理</a>
                <ul>
                    <{if isset($smarty.session.member_smsmanually) && $smarty.session.member_smsmanually == '1'}>
                    <li>
                        <a href="/sms/sms_manually.php" target="_blank">手動發送簡訊</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_smserror) && $smarty.session.member_smserror == '1'}>
                    <li>
                        <a href="/sms/sms_list.php?ch=f" target="_blank">異常簡訊</a>
                    </li>
                    <{/if}>
                    <{if isset($smarty.session.member_pStaffManage) && $smarty.session.member_pStaffManage == 1}>
                    <li>
                        <a href="/member/member.php">員工資料</a>
                    </li>
                    <{/if}>
                    <{if isset($smarty.session.member_upload) && $smarty.session.member_upload == '1'}>
                    <li>
                        <a href="/www/webManage.php">官網管理</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_info) && $smarty.session.member_info == '1'}>
                    <li>
                        <a href="/others/info_window.php">資訊視窗</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.pBankInfo ) && $smarty.session.pBankInfo == 1}>
                    <li>
                        <a href="/others/bankInfoList.php">銀行資訊</a>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_pDep != 7 && (isset($smarty.session.pMobileApp) && $smarty.session.pMobileApp == 1)}>
                    <li>
                        <a href="#">手機APP管理</a>
                        <ul>
                            <li>
                                <a href="/mobile/mobileNews.php">最新消息</a>
                            </li>
                            <li>
                                <a href="/mobile/mobileAccount.php">帳號管理</a>
                            </li>
                        </ul>
                    </li>
                    <{/if}>

                    <li>
                        <a href="#">LINE</a>
                        <ul>
                            <{if $smarty.session.member_id|in_array: [1, 6]}>
                            <li>
                                <a href="/others/LineMsg.php">LINE建經小幫手</a>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pLineAccount) && $smarty.session.pLineAccount == 1}>
                            <li>
                                <a href="/line/AccountList.php">LINE帳號管理</a>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id == 6}>
                            <li>
                                <a href="/line/QuestionList.php">LINE問卷管理</a>
                            </li>
                            <{/if}>

                            <li>
                                <a href="/notify/notify.php">LINE Notify 註冊管理</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#">政府機關</a>
                        <ul>
                            <li>
                                <a href="/others/LandGovermentList.php">地政事務所</a>
                            </li>

                            <li>
                                <a href="/others/TaxGovermentList.php">稅捐稽徵處</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <{/if}>
            
            <{if isset($smarty.session.member_salesmanage) && $smarty.session.member_salesmanage == '1' }>
            <li style="width:120px;">
                <a href="#">業務管理</a>
                <ul>
                    <li>
                        <a href="#">回饋金</a>
                        <ul>
                            <{if isset($smarty.session.pSalesPayByCase) && $smarty.session.pSalesPayByCase == '1' }>
                            <li>
                                <a href="#">隨案出款</a>
                                <ul>
                                    <li>
                                        <a href="/sales/payByCaseSalesConfirm.php">確認清單</a>
                                    </li>
                                    <li>
                                        <a href="/sales/payByCaseSalesReceipt.php">收據繳回確認</a>
                                    </li>
                                </ul>
                            </li>
                            <{/if}>

                            <{if isset($smarty.session.pSalesPaymentInform) && $smarty.session.pSalesPaymentInform == 1}>
                            <li>
                                <a href="#">申請狀態查詢</a>
                                <ul>
                                    <li>
                                        <a href="/sales/salesPaymentInform.php">季月結查詢</a>
                                    </li>
                                    <li>
                                        <a href="/accounting/caseFeedbackPayByCase.php">隨案結查詢</a>
                                    </li>
                                </ul>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id|in_array: [1, 3, 6, 12] || (isset($smarty.session.pFeedBackAudit) && $smarty.session.pFeedBackAudit == 1)}>
                            <li>
                                <a href="/report/feedBackAudit.php">回饋金審核</a>
                            </li>
                            <{/if}>

                            <{if $smarty.session.member_id|in_array: [1, 2, 3, 6, 12]}>
                            <li>
                                <a href="/report/feedbackReviewError.php" target="_blank">審核後回饋金覆蓋錯誤表</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>

                    <{if isset($smarty.session.pSalesTracking) && $smarty.session.pSalesTracking == '1'}>
                    <li>
                        <a href="/sales/salesTracking.php">未進案追蹤名單</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_pSalesCase) && $smarty.session.member_pSalesCase == '1'}>
                    <li>
                        <a href="/report/salesReport.php">績效一覽表</a>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_pDep != 11}>
                    <li>
                        <a href="/sales/certifiedFee.php">未收足審核</a>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_id|in_array: [3, 6]}>
                    <li>
                        <a href="/sales/salesAgents.php">業務責任區審核</a>
                    </li>
                    <{/if}>
                    
                    <{if isset($smarty.session.member_calendar) && $smarty.session.member_calendar == '1'}>
                    <li>
                        <a href="/calendar/calendar.php" target="_blank">行程記錄</a>
                    </li>
                    <{/if}>
                    
                    <{if isset($smarty.session.member_pSalesScheduleAcc) && $smarty.session.member_pSalesScheduleAcc == '1'}>
                    <li>
                        <a href="#">行程管理統計</a>
                        <ul>
                            <li>
                                <a href="/sales/scheduleCount.php">行程統計</a>
                            </li>
                            <{if isset($smarty.session.pStaffCheckIn) && ($smarty.session.pStaffCheckIn == '1')}>
                            <li>
                                <a href="/report/checkIn/salesCheckIn.php">上下班打卡統計</a>
                            </li>
                            <{/if}>
                        </ul>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_realprice) && $smarty.session.member_realprice == '1'}>
                    <li>
                        <a href="/sales/realPrice.php">實價登錄資料</a>
                    </li>

                    <li>
                        <a href="/sales/scrivenerCompare2.php">僑馥安新地政士</a>
                    </li>
                    <{/if}>

                    <{if $smarty.session.member_id|in_array: [1, 3, 6] || (isset($smarty.session.pBusinessOwnership) && $smarty.session.pBusinessOwnership == 1)}>
                    <li>
                        <a href="#">業務歸屬設定</a>
                        <ul>
                            <li>
                                <a href="/sales/salesBranchArea.php">仲介業務歸屬</a>
                            </li>

                            <li>
                                <a href="/sales/salesScrivenerArea.php">地政士業務歸屬</a>
                            </li>

                            <li>
                                <a href="/sales/salesPerformanceArea.php">績效業務分區</a>
                            </li>
                        </ul>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.member_ScrivenerLevel) && $smarty.session.member_ScrivenerLevel > 0 }>
                    <li>
                        <a href="/scrivener/scrivenerPresent.php">地政士生日禮申請</a>
                    </li>
                    <{/if}>

                    <{if isset($smarty.session.pScrivenerLevelList) && $smarty.session.pScrivenerLevelList == 1 }>
                    <li>
                        <a href="/scrivener/scrivenerPresentList.php">代書生日禮紀錄查詢(非禮券)</a>
                    </li>
                    <{/if}>

                    <li>
                        <a href="/accounting/scrivenerPresentReport.php">地政士生日禮表單</a>
                    </li>
                </ul>
            </li>
            <{/if}>

            <{if isset($smarty.session.member_casemanage) && $smarty.session.member_casemanage == '1' && $smarty.session.member_id == 6}>
            <{* <li style="width:120px;">
                <a href="#a">斡旋支付</a>
                <ul>
                    <li>
                        <a href="/payment/caseList.php">斡旋案件查詢</a>
                    </li>

                    <li>
                        <a href="/payment/paymentList.php">斡旋案件入帳確認</a>
                    </li>

                    <li>
                        <a href="/payment/paymentListTrans.php" target="_blank">斡旋案件出款確認</a>
                    </li>
                </ul>
            </li> *}>
            <{/if}>

            <{if isset($smarty.session.pLegal) && $smarty.session.pLegal == 1}>
            <li style="width:120px;">
                <a href="#">法務作業</a>
                <ul>
                    <li><a href="/legal/legalCaseList.php">案件列管</a></li>

                    <li><a href="/legal/legalCaseEventList.php">預設事項管理</a></li>
                </ul>
            </li>
            <{/if}>
            <{if isset($smarty.session.pHR) && $smarty.session.pHR == 1}>
            <li style="width:120px;">
                <a href="#">人事作業</a>
                <ul>
                    <li><a href="/staff/staffDetail.php">基本資料</a></li>

                    <{if !$smarty.session.member_id|in_array: [2, 3] }>
                    <li><a href="/staff/myCheckIn.php">出勤記錄</a></li>
                    <{/if}>
                    
                    <{if $smarty.session.member_id != 2}>
                    <li><a href="/staff/myLeave.php">休假記錄</a></li>
                    <{/if}>
                    
                    <li><a href="/staff/reviewConfirm.php">簽核記錄</a></li>

                    <{if isset($smarty.session.pHRCalender) && $smarty.session.pHRCalender > 0}>
                    <li><a href="/HR/leaveCalender.php">休假日曆</a></li>
                    <{/if}>

                    <{if isset($smarty.session.pHRReport) && $smarty.session.pHRReport == 1}>
                    <li><a href="/HR/summary.php">假勤報表</a></li>
                    <{/if}>
                    
                    <{if isset($smarty.session.pHRHolidaySetting) && $smarty.session.pHRHolidaySetting == 1}>
                    <li><a href="/HR/holiday.php">假日設定</a></li>
                    <li><a href="/HR/staffDefaultLeave.php">員工可休假設定</a></li>
                    <{/if}>

                    <{if isset($smarty.session.pHRStaffInformation) && $smarty.session.pHRStaffInformation == 1}>
                    <li><a href="/HR/staffInformation.php">員工基本資料</a></li>
                    <{/if}>

                    <{if isset($smarty.session.pHRStaffOvertime) && $smarty.session.pHRStaffOvertime == 1}>
                    <li><a href="/HR/staffOvertime.php">員工加班紀錄</a></li>
                    <{/if}>

                    <{if isset($smarty.session.pHRStaffAttendanceIrregularity) && $smarty.session.pHRStaffAttendanceIrregularity == 1}>
                    <li><a href="/HR/staffAttendanceIrregularity.php">員工異常紀錄</a></li>
                    <{/if}>

                    <{if isset($smarty.session.pHRStaffDefaultLeaveHistory) && $smarty.session.pHRStaffDefaultLeaveHistory == 1}>
                    <li><a href="/HR/staffDefaultLeaveHistory.php">員工可休假時數設定歷史紀錄</a></li>
                    <{/if}>

                    <{if isset($smarty.session.pHRStaffLeave) && $smarty.session.pHRStaffLeave == 1}>
                    <li><a href="/HR/staffLeave.php">員工假單簽核明細查詢</a></li>
                    <{/if}>
                </ul>
            </li>
            <{/if}>
        </ul>

        <div id="dialog-message">
            
        </div>
