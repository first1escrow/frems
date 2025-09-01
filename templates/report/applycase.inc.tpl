<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <{include file='meta2.inc.tpl'}> 
    <script type="text/javascript">
    $(document).ready(function() {
        $('#dialog').dialog('close');
        $(".ajax").colorbox({width:"400",height:"100"});
        
        $('#citys').change(function() {
            cityChange();
        });
        
        $('#areas').change(function() {
            areaChange();
        });

        $('#branchCitys').change(function() {
            branchCityChange();
        });

        $('#branchAreas').change(function() {
            branchAreaChange();
        });

        $('#scrivenerCitys').change(function() {
            scrivenerCityChange();
        });

        $('#scrivenerAreas').change(function() {
            scrivenerAreaChange();
        });
    });

    /* 取得縣市區域資料 */
    function cityChange() {
        let url = 'zipArea.php' ;
        let _city = $('#citys :selected').val() ;

        $.post(url,{'c':_city,'op':'1'},function(txt) {
            $('#areas').html(txt) ;
        }) ;
    }

    /* 取得區域郵遞區號 */
    function areaChange() {
        let _area = $('#areas :selected').val() ;
        $('#zip').val(_area) ;
    }

    /* 仲介地區取得縣市區域資料 */
    function branchCityChange() {
        let url = 'zipArea.php' ;
        let _city = $('#branchCitys :selected').val() ;

        $.post(url,{'c':_city,'op':'1'},function(txt) {
            $('#branchAreas').html(txt) ;
        }) ;
    }

    /* 仲介地區取得區域郵遞區號 */
    function branchAreaChange() {
        let _area = $('#branchAreas :selected').val() ;
        $('#branchZip').val(_area) ;
    }

    /* 地政士地區取得縣市區域資料 */
    function scrivenerCityChange() {
        let url = 'zipArea.php' ;
        let _city = $('#scrivenerCitys :selected').val() ;

        $.post(url,{'c':_city,'op':'1'},function(txt) {
            $('#scrivenerAreas').html(txt) ;
        }) ;
    }

    /* 地政士地區取得區域郵遞區號 */
    function scrivenerAreaChange() {
        let _area = $('#scrivenerAreas :selected').val() ;
        $('#scrivenerZip').val(_area) ;
    }

    function go(url) {
        let bk = $('[name="bank"]').val() ;	
        let sad = $('[name="sApplyDate"]').val() ;
        let ead = $('[name="eApplyDate"]').val() ;
        let sed = $('[name="sEndDate"]').val() ;
        let eed = $('[name="eEndDate"]').val() ;
        let ssd = $('[name="sSignDate"]').val() ;
        let esd = $('[name="eSignDate"]').val() ;
        let sbld = $('[name="sbankLoansDate"]').val() ;
        let ebld = $('[name="ebankLoansDate"]').val() ;

        let br = new Array();
        let sc = new Array();
        let zp = $('[name="zip"]').val() ;
        let ct = $('#citys :selected').val() ;
        let brzp = $('[name="branchZip"]').val() ;
        let brct = $('#branchCitys :selected').val() ;
        let sczp = $('[name="scrivenerZip"]').val() ;
        let scct = $('#scrivenerCitys :selected').val() ;
        let bd = $('[name="brand"]').val() ;
        let ut = $('[name="undertaker"]').val() ;
        let st = $('[name="status"]').val() ;
        let es = $('[name="realestate"]').val() ;
        let cid = $('[name="cCertifiedId"]').val() ;
        let byr = $('[name="buyer"]').val() ;
        let owr = $('[name="owner"]').val() ;
        let sales = $('[name="sales"]').val();
        let report = $('[name="report"]').val();
        let sales_performance = $('[name="sales_performance"]').val();

        let s_cat = $("[name='scrivener_category']:checked").val();
        let scrivenerBrand = new Array();

        $('input:checkbox:checked[name="scrivenerBrand[]"]').each(function(i) { scrivenerBrand[i] = this.value; });
        
        if (report == 1 && bd =='') {
            alert("請選擇品牌");
            return false;
        }

        if ($(".bStore").length > 0) {
            $(".bStore").each(function(i) {
                br[i] = $(this).attr('id');
            });		
        }

        if ($(".sStore").length > 0) {
            $(".sStore").each(function(i) {
                sc[i] = $(this).attr('id');
            });		
        }
        
        $( "#dialog" ).dialog("open") ;
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
        
        $.post(url,
            {'bank':bk,'sApplyDate':sad,'eApplyDate':ead,'sEndDate':sed,'eEndDate':eed,'sSignDate':ssd,'eSignDate':esd,'sbankLoansDate':sbld,'ebankLoansDate':ebld,'branch':br,
            'scrivener':sc,'zip':zp,'citys':ct,'branchZip':brzp,'branchCitys':brct,'scrivenerZip':sczp,'scrivenerCitys':scct,'brand':bd,'undertaker':ut,'status':st,'realestate':es,
            'cCertifiedId':cid,'buyer':byr,'owner':owr,'show_hide':'hide','scrivener_category':s_cat,"sales":sales,"scrivenerBrand":scrivenerBrand,"report":report,"sales_performance":sales_performance,
            "branchGroup":$("[name='branchGroup']").val()},
            function(txt) {
                $('#container').html(txt) ;
                $( "#dialog" ).dialog("close") ;
        }) ;
    }

    function add(cat) {
        if (cat == 'b') {
            let val = $('[name="branch"]').val();
            let text = $('#branch option[value="'+val+'"]').text();

            if ($("#b"+val).length == 1) {
                alert('已加入店家搜尋條件');
                return false;
            }

            $("#showBrach").append('<div id="'+cat+val+'" class="addStore bStore"><a href="#showBrach" onClick="del(\''+cat+'\','+val+')" >(刪除)</a>'+text+'</div><div style="clear:both"></div>');
        } else if (cat == 's') {
            let val = $('[name="scrivener"]').val();
            let text = $('#scrivener option[value="'+val+'"]').text(); 

            if ($("#s"+val).length == 1) {
                alert('已加入地政士搜尋條件');
                return false;
            }
            
            $("#showScrivener").append('<div id="'+cat+val+'" class="addStore sStore"><a href="#showScrivener" onClick="del(\''+cat+'\','+val+')">(刪除)</a>'+text+'</div><div style="clear:both"></div>');
        }
    }

    function del(cat,id) {
        $("#"+cat+id).remove();
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

	#dialog {
        background-image:url("/images/animated-overlay.gif") ;
        background-repeat: repeat-x;
        margin: 0px auto;
        width: 300px; 
        height: 30px;
	}

	.easyui-combobox{
        width: 300px;
	}

	.tdStyle1{
		width:300px;
		background-color:#F8ECE9;
		padding:4px;
	}

	.bStore,.sStore{
		width: auto;
		border: 1px solid #999;
		display: inline-block;
		padding: 2px;
	}

	#showBrach, #showScrivener{
		margin-top: 5px;
	}
    </style>
</head>
<body id="dt_example">
    <form name="form_edit" id="form_edit" method="POST">
        <input type="hidden" name="id" id="id" value='3' />
    </form>

    <form name="form_add" id="form_add" method="POST"></form>

    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="233" height="72">&nbsp;</td>
                    <td width="753">
                        <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                            </tr>
                            <tr>
                                <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table> 
        </div>

        <{include file='menu1.inc.tpl'}>

        <table width="1000" border="0" cellpadding="4" cellspacing="0">
            <tr>
                <td bgcolor="#DBDBDB">
                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                        <tr>
                            <td height="17" bgcolor="#FFFFFF">
                                <div id="menu-lv2"></div>
                                <br/> 
                                <h3>&nbsp;</h3>
                                <div id="container">
                                    <div id="dialog" class="easyui-dialog" title="" style="display:none"></div>

                                    <div>
                                        <form name="mycal">
	                                        <div style="width:550px;padding-left:20px;">
	                                            <{if $smarty.session.member_id|in_array:[3, 6, 13, 36]}>
                                                <a href='/report/totalReportFor3.php' target="_blank">案件統計</a>
                                                <a href="/report/analysiscase.php">統計表</a>
	                                            <{/if}>
	                                        </div>

                                            <{if $smarty.session.member_pDep == 11 && $smarty.session.member_id != 48}>
                                            <table cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">
                                                <tr>
                                                    <td class="tdStyle1">
                                                        結案日期(起)
                                                        <input type="text" name="sEndDate" class="datepickerROC" style="width:100px;">
                                                        結案日期(迄)
                                                        <input type="text" name="eEndDate" class="datepickerROC" style="width:100px;">
                                                        <input type="hidden" name="report" value="0">
                                                    </td>
                                                </tr>
                                            </table>
                                            <{else}>
                                            <table cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">
                                                <tr>
                                                    <td class="tdStyle1">
                                                        系統別&nbsp;&nbsp;*　
                                                        <select name="bank" size="1" style="width:150px;">
                                                            <option value="">全部</option>
                                                            <{$contract_bank}>
                                                        </select>
                                                    </td>
                                                    <td class="tdStyle1">
                                                        進案日期(起)
                                                        <input type="text" name="sApplyDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                    <td class="tdStyle1">
                                                        進案日期(迄)
                                                        <input type="text" name="eApplyDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="tdStyle1">
                                                        保證號碼　&nbsp;
                                                        <input type="text" name="cCertifiedId" style="width:150px;" maxlength="9">
                                                    </td>
                                                    <td class="tdStyle1">
                                                        結案日期(起)
                                                        <input type="text" name="sEndDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                    <td class="tdStyle1">
                                                        結案日期(迄)
                                                        <input type="text" name="eEndDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="tdStyle1">&nbsp;</td>
                                                    <td class="tdStyle1">
                                                        簽約日期(起)
                                                        <input type="text" name="sSignDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                    <td class="tdStyle1">
                                                        簽約日期(迄)
                                                        <input type="text" name="eSignDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="tdStyle1">&nbsp;</td>
                                                    <td class="tdStyle1">
                                                        履保費出款日(起)
                                                        <input type="text" name="sbankLoansDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                    <td class="tdStyle1">
                                                        履保費出款日(迄)
                                                        <input type="text" name="ebankLoansDate" class="datepickerROC" style="width:100px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" style="background-color:#F8ECE9;">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="width:600px;background-color:#E4BEB1;padding:4px;">
                                                        地政士類別
                                                        <input type="radio" name='scrivener_category' value="" checked>全部 <input type="radio" name="scrivener_category" value="1">台灣房屋加盟
                                                    </td>
                                                    <td class="tdStyle1">
                                                        仲介品牌　
                                                        <select name="brand" size="1" style="width:130px;" >
                                                            <option value="">全部</option>
                                                            <{$brand}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="width:600px;background-color:#E4BEB1;padding:4px;">
                                                        地政士合作品牌
                                                        <{html_checkboxes name=scrivenerBrand options=$menu_brand  separator='&nbsp;&nbsp;'}>
                                                    </td>
                                                    <td class="tdStyle1">
                                                        仲介商類型
                                                        <select name="realestate" size="1" style="width:130px;">
                                                            <option value="">全部</option>
                                                            <{$category}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="width:600px;background-color:#E4BEB1;padding:4px;">
                                                        報表樣式:<{html_options name=report options=$menu_report}>
                                                    </td>
                                                    <td class="tdStyle1">
                                                        仲介群組　
                                                        <select name="branchGroup" id="branchGroup">
                                                            <option value="">全部</option>
                                                            <{$menu_group}>	
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="width:600px;background-color:#E4BEB1;padding:4px;"></td>
                                                    <td  class="tdStyle1">
                                                        案件地區　
                                                        <select name="country" id="citys" class="keyin2b">
                                                            <{$citys}>
                                                        </select>
                                                        <select name="area" id="areas" class="keyin2b">
                                                            <option value="">全部</option>
                                                        </select>
                                                        <input type="hidden" name="zip" id="zip" readonly="readonly" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="width:300px;background-color:#E4BEB1;padding:4px;"></td>
                                                    <td class="tdStyle1">
                                                        案件狀態　
                                                        <select name="status" size="1" style="width:130px;">
                                                            <option value="">全部</option>
                                                            <{$status}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:300px;background-color:#E4BEB1;padding:4px;"></td>
                                                    <td style="width:300px;background-color:#E4BEB1;">&nbsp;</td>
                                                    <td  class="tdStyle1">
                                                        承辦人　　
                                                        <select name="undertaker" size="1" style="width:130px;">
                                                            <option value="">全部</option>
                                                            <{$undertaker}>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <{if $smarty.session.member_pDep != 7}>
                                                <tr>
                                                    <td colspan="2" style="width:300px;background-color:#E4BEB1;padding:4px;">&nbsp;</td>
                                                    <td  class="tdStyle1">
                                                        負責業務&nbsp;&nbsp;&nbsp;
                                                        <select name="sales" size="1" style="width:130px;">
                                                            <option value="">全部</option>
                                                            <{$menuSalse}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <{/if}>

                                                <{if $smarty.session.member_pDep != 7}>
                                                <tr>
                                                    <td colspan="2" style="width:300px;background-color:#E4BEB1;padding:4px;">&nbsp;</td>
                                                    <td  class="tdStyle1">
                                                        績效業務&nbsp;&nbsp;&nbsp;
                                                        <select name="sales_performance" size="1" style="width:130px;">
                                                            <option value="">全部</option>
                                                            <{$menuSalse}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <{/if}>

                                                <tr>
                                                    <td colspan="2"  class="tdStyle1">
                                                        仲介店名　
                                                        <select name="branch" id="branch" class="easyui-combobox">
                                                            <option></option>
                                                            <{$branch_search}>
                                                        </select>

                                                        <input type="button" value="增加" onclick="add('b')" class="xxx-button">
                                                        <font color="red" style="font-size: 12px;">(選擇完請按下增加鈕)</font>

                                                        <div id="showBrach"></div>
                                                    </td>
                                                    <td  class="tdStyle1">
                                                        仲介地區　
                                                        <select name="branchCountry" id="branchCitys" class="keyin2b">
                                                            <{$citys}>
                                                        </select>
                                                        <select name="branchArea" id="branchAreas" class="keyin2b">
                                                            <option value="">全部</option>
                                                        </select>
                                                        <input type="hidden" name="branchZip" id="branchZip" readonly="readonly" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"  class="tdStyle1">
                                                        地政士名稱
                                                        <select name="scrivener" id="scrivener" class="easyui-combobox">
                                                            <option></option>
                                                            <{$scrivener_search}>
                                                        </select>

                                                        <input type="button" value="增加" onclick="add('s')" class="xxx-button">
                                                        <font color="red" style="font-size: 12px;">(選擇完請按下增加鈕)</font>

                                                        <div id="showScrivener"></div>
                                                    </td>
                                                    <td  class="tdStyle1">
                                                        地政士地區　
                                                        <select name="scrivenerCountry" id="scrivenerCitys" class="keyin2b">
                                                            <{$citys}>
                                                        </select>
                                                        <select name="scrivenerArea" id="scrivenerAreas" class="keyin2b">
                                                            <option value="">全部</option>
                                                        </select>
                                                        <input type="hidden" name="scrivenerZip" id="scrivenerZip" readonly="readonly" />
                                                    </td>
                                                </tr>
                                            </table>
                                            <{/if}>

                                            <div style="padding:20px;text-align:center;">
                                                <input type="button" value="查詢" onclick="go('applycase_result.php')" class="bt4" style="display:;width:100px;height:35px;">
                                                <input type="button" value="匯出 excel 檔" onclick="xls('excel.php')" class="bt4" style="display:none;width:100px;height:35px;display:;">
                                            </div>
                                        </form>
                                    </div>
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