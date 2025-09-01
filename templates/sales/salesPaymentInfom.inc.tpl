<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<{include file='meta2.inc.tpl'}>
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css?v=20221215" />
<script type="text/javascript">
var scriveners = [];
var realty = [];

$(document).ready(function() {
    $('#dialog').dialog('close');
    $('.cmc_overlay').hide();
});

function query() {
    let _store = $('[name="sStore"]').val();

    if (_store) {
        let _match = _store.match(/\[(.*)\]/); 
        if (_match[1]) {
            addStore(_match[1])
        }
    }

    if (realty.length > 0) {
        $('[name="branch"]').val(realty.join(','));
    }

    if (scriveners.length > 0) {
        $('[name="scrivener"]').val(scriveners.join(','));
    }

	$('#menuBar').hide();
    $('.cmc_overlay').show();

    $('#form1').hide().submit();
    $('[name="my_iframe"]').show();
}

function add() {
    let _store = $('[name="sStore"]').val();

    let _match = _store.match(/\[(.*)\]/);
    if (_match[1]) {
        let _exists = false;
        $('.addStore').each(function(index) {
            if (_match[1] == $(this).prop('id')) {
                _exists = true;
            }
        });
        
        if (_exists === false) {
           $("#selectedTarget").append('<div id="' + _match[1] + '" class="addStore bStore" style="background-color: #F8ECE9;"><a href="Javascript:void(0);" onClick="del(\'' + _match[1] + '\')" >(刪除)</a>' + _store + '</div>');
        }
        
        addStore(_match[1]);

        $('[name="sStore"]').val('');
    } else {
        alert('指定對象錯誤或不完整');
    }
}

function del(id){
	$("#"+id).remove();
    delStore(id);
}

function cls() {
    $('[name="sStore"]').val('');
}

function addStore(_store) {
    let _matches = _store.match(/^([A-Z]{2})(\d{4,5})$/);

    if (_matches[1] == 'SC') {
        scriveners.push(parseInt(_matches[2]));
        scriveners = [...new Set(scriveners)];
    } else {
        realty.push(parseInt(_matches[2]));
        realty = [...new Set(realty)];
    }
}

function delStore(_store) {
    console.log('del = ' + _store)
    let _match = _store.match(/^([A-Z]{2})(\d{4,5})$/);

    if (_match[1] == 'SC') {
        let _key = scriveners.indexOf(parseInt(_match[2]));
        if (_key > -1) {
            scriveners.splice(_key, 1);
        }
    } else {
        let _key = realty.indexOf(parseInt(_match[2]));
        if (_key > -1) {
            realty.splice(_key, 1);
        }
    }
}

function resizeIframe(obj) {
    let _h = obj.contentWindow.document.body.scrollHeight + 20;
    obj.style.height =  _h + 'px';
}
</script>
<style>
#dialog {
	background-image:url("/images/animated-overlay.gif") ;
	background-repeat: repeat-x;
	margin: 0px auto;
	width: 300px;
	height: 30px;
}

.store{
	/*border: 1px solid #999;*/
	background-color:#F8ECE9;
	padding-bottom: 20px;
	width:900px;
}

.addStore{
	background-color: white;
	padding-top: 5px;
	padding-bottom: 5px;
	width:400px;
	border-bottom: 1px #CCC solid;
}

/*button*/
.xxx-button {
    color:#FFFFFF;
	font-size:12px;
	font-weight:normal;
	
	text-align: center;
	white-space:nowrap;
	height:20px;
	
	background-color: #a63c38;
    border: 1px solid #a63c38;
    border-radius: 0.35em;
    font-weight: bold;
    padding: 0 20px;
    margin: 5px auto 5px auto;
}

.xxx-button:hover {
	background-color:#333333;
	border:1px solid #333333;
}

.xxx-select {
	color: #666666;
	font-size: 14px;
	font-weight: normal;
	background-color: #FFFFFF;
	text-align: left;
	height: 24px;
	padding: 0 0px 0 5px;
	border: 1px solid #CCCCCC;
	border-radius: 0em;
	font-family: "微軟正黑體", serif;
}

</style>
</head>
<body id="dt_example">
    <div class="cmc_overlay">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>


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
                                <div id="menu-lv2">
                                
                                </div>
                                <br/> 
                                <h3>&nbsp;</h3>
                                <h1 style="padding-left: 5px;">回饋金申請狀態查詢</h1>
                                <div id="dialog" class="easyui-dialog" style="display:none"></div>
                                <div id="container">
                                    <form id="form1" method="POST" action="salesPaymentInformResult.php" target="my_iframe">
                                        <input type="hidden" name="bCategory" value="1,2,3">
                                        <input type="hidden" name="timeCategory" value="1">
                                        <input type="hidden" name="status" value="1">
                                        <input type="hidden" name="scrivener" value="">
                                        <input type="hidden" name="branch" value="">

                                        <table cellspacing="0" cellpadding="0" style="width:900px;padding-top:20px;">
                                            <tr>
                                                <td colspan="2" style="width:300px;background-color:#E4BEB1;padding:4px;">&nbsp;
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="width:600px;background-color:#F8ECE9;padding:4px;">
                                                    年度季別
                                                    <select name="sales_year" style="width:60px;" class="xxx-select">
                                                    <{$menu_year}>
                                                    </select>
                                                    年度
                                                    <{html_options name="sales_season" style="width:80px;" options=$menu_season selected=$seasons class="xxx-select"}>(起)~
                                                    年度季別
                                                    <select name="sales_year_end" style="width:60px;" class="xxx-select">
                                                    <{$menu_year}>
                                                    </select>
                                                    年度
                                                    <{html_options name="sales_season_end" style="width:80px;" options=$menu_season selected=$seasons class="xxx-select"}>(迄)
                                                </td>
                                                <td style="vertical-align:middle;background-color:#F8ECE9;padding:4px;">
                                                    狀態
                                                    <{html_radios name='caseStatus' options=$menu_caseStatus selected=$caseStatus}>
                                                </td>
                                            </tr>

                                            <tr style="background-color: #F8ECE9;">
                                                <td colspan="2" style="padding-left: 5px;">
                                                    指定地政士或仲介對象店編
                                                    <input list="stores" name="sStore" style="width: 500px;" value="<{$data.sStore}>">
                                                    <input type="button" value="增加" onclick="add()" class="xxx-button">
                                                    <input type="button" value="清除" onclick="cls()" class="xxx-button">
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="store">
                                            <div id="selectedTarget" style="padding-left:20px;">
                                                
                                            </div>
                                        </div>

                                        <div style="padding:20px;text-align:center;">
                                            <input type="button" value="查詢" onclick="query()" class="xxx-button" style="display:;width:100px;height:35px;font-size:16px;">
                                        </div>

                                        <div id="dwn">
                                        </div>
                                    </form>
                                    
                                    <iframe name="my_iframe" width="99%" frameBorder="0" style="display:none;" onload="resizeIframe(this)"></iframe>
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

    <datalist id="stores">
    <{foreach from=$stores key=key item=item}>
    <option value="<{$item.name}>[<{$item.id}>]">
    <{/foreach}>
    </datalist>
</body>
</html>