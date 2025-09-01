<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#export').live('click', function () {
                    let action = $('[name=action]').val();
                    let fds = $('[name=fds]').val();
                    let fde = $('[name=fde]').val();
                    let report = $('[name=report]:checked').val();
                    let certifiedId = $('[name=certifiedId]').val();

                    if (action == 'date') {
                        if (fds == '' || fde == '') {
                            alert('請輸入日期');
                            return;
                        }
                    }
                    
                    if (action == 'certifiedId') {
                        let cIdBlock = $('#cId-block');
                        let cId = cIdBlock.html();
                        let cIdArr = cId.split(',');

                        if (!cId) {
                            alert('請輸入保證號碼');
                            return;
                        }

                        let valid = true;
                        cIdArr.forEach(function (item) {
                            if (item.length != 9) {
                                valid = false;
                                return;
                            }
                        });

                        if (!valid) {
                            alert('保證號碼格式錯誤');
                            return;
                        }

                        $('[name="certifiedId"]').val(cIdArr.join(','));
                    }

                    if (!report) {
                        alert('請選擇報表');
                        return;
                    }
                    
                    $('[name="form_search"]').submit();
                });

                $('#export').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#date-block').on('click', function() {
                    $('#date-block input').prop('disabled', false);
                    $(this).addClass('highlight');
                    $('#certifiedId-block').removeClass('highlight');
                    $('#certifiedId-block input').prop('disabled', true);

                    $('[name="certifiedId"]').val('');
                    $('[name="action"]').val('date');
                });

                $('#certifiedId-block').on('click', function() {
                    $('#certifiedId-block input').prop('disabled', false);
                    $(this).addClass('highlight');
                    $('#date-block').removeClass('highlight');
                    $('#date-block input').prop('disabled', true);

                    $('[name="fds"]').val('');
                    $('[name="fde"]').val('');
                    $('[name="action"]').val('certifiedId');
                });

                $('#date-block').click();
            });

            function insert() {
                let certifiedId = $('[name="cId"]').val();
                if (!certifiedId) {
                    alert('請輸入保證號碼');
                    $('[name="cId"]').focus();
                    return;
                }

                if (certifiedId.length != 9) {
                    alert('保證號碼格式錯誤');
                    return;
                }

                let cIdBlock = $('#cId-block');
                let cId = cIdBlock.html();
                let cIdArr = cId.split(',');

                if (cIdArr.indexOf(certifiedId) > -1) {
                    alert('保證號碼已存在');
                    return;
                }

                if (cId) {
                    cId += ',';
                }
                cIdBlock.html(cId + certifiedId);

                $('[name="cId"]').val('');
            }

            function remove() {
                let certifiedId = $('[name="cId"]').val();
                if (certifiedId == '') {
                    alert('請輸入保證號碼');
                    $('[name="cId"]').focus();
                    return;
                }

                let cIdBlock = $('#cId-block');
                let cId = cIdBlock.html();
                let cIdArr = cId.split(',');

                if (cIdArr.indexOf(certifiedId) == -1) {
                    alert('保證號碼不存在');
                    return;
                }

                cIdArr.splice(cIdArr.indexOf(certifiedId), 1);
                cIdBlock.html(cIdArr.join(','));

                $('[name="cId"]').val('');
            }
        </script>
        <style type="text/css">
            .block {
                /* padding-bottom:10px; */
                border: 1px solid #CCC;
                padding: 5px;
                /* border-radius: 5px; */
                width: 400px;
                margin-bottom: 10px;
            }

            .highlight {
                border: 5px solid #59a511;
                border-radius: 10px;
            }
        </style>
    </head>
    <body id="dt_example">
        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753">
                            <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right">
                                        <h1><{include file='welcome.inc.tpl'}></h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"></td>
                                    <td width="14%" align="center">
                                        <h2> 登入者 <{$smarty.session.member_name}></h2>
                                    </td>
                                    <td width="5%" height="30" colspan="2">
                                        <h3><a href="/includes/member/logout.php">登出</a></h3>
                                    </td>
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
                                        <form name="form_search" method="post" action="winton.php">
                                            <input type="hidden" name="action"/>
                                            <div>
                                                <div id="date-block" class="block">
                                                    <span>匯出日期︰　　</span>
                                                    <span><input type="text" name="fds" style="width:100px;" class="datepickerROC" readonly /> ~ <input type="text" name="fde" style="width:100px;" class="datepickerROC" readonly /></span>
                                                </div>
                                                <div id="certifiedId-block" class="block">
                                                    <span>匯出保證號碼︰</span>
                                                    <span>
                                                        <input type="hidden" name="certifiedId" />
                                                        <input type="text" name="cId" style="width:100px;" maxlength="9" />
                                                        <input type="button" style="padding:5px;" onclick="insert()" value="加入" />
                                                        <input type="button" style="padding:5px;" onclick="remove()" value="刪除" />
                                                    </span>
                                                    <div id="cId-block" style="font-size:10pt;color:blue"></div>
                                                </div>
                                                <div>
                                                    <span><label><input type="radio" name="report" value="5">&nbsp;2022客供商檔</label></span>
                                                    <span><label><input type="radio" name="report" value="6">&nbsp;2022進銷檔</label></span>
                                                </div>
                                            </div>
                                        </form>
                                        <center><button id="export">匯出Excel</button></center>
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
        </div>
    </body>
</html>