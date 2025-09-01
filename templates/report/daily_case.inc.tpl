<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <{include file='meta2.inc.tpl'}>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dialog').dialog('close');
            $('#content_list').hide();

        });

        function go() {

            var cid = $('[name="cCertifiedId"]').val() ;
            var cEndDate = $('[name="cEndDate"]').val() ;
            var cCertifyDate = $('[name="cCertifyDate"]').val() ;

            if(cEndDate == '') {
                alert('實際點交日為必填');
            }

            var table1 = $("#table").dataTable({
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        extend: 'csv',
                        charset: 'UTF-8',
                        fieldSeparator: ',',
                        bom: true,
                        filename: 'DailyCase',
                        title: 'DailyCase'
                    }
                ],
                "ajax": {
                    url: "/includes/report/daily_case.php",
                    type: "POST",
                    data: {
                        cCertifiedId: cid,
                        cEndDate: cEndDate,
                        cCertifyDate: cCertifyDate,
                    }
                },
                "order": [[ 0, "asc" ]],
                "destroy":true,
                "columns": [
                        { "data": "cCertifiedId"},
                        { "data": "cEndDate"},
                        { "data": "tExportTime"},
                    ],
                    "columnDefs":[{
                       "targets": 1,
                        "data": "cEndDate",
                        "render": function (data, type, row, meta) {
                            return data.substr(0, 10);
                        }
                    },{
                       "targets": 2,
                        "data": "tExportTime",
                        "render": function (data, type, row, meta) {
                           if(null == data) {
                               return row.cBankList;
                           }
                           return data;
                        }
                    },
                ],
                drawCallback: function(settings) {
                    $('#total_count').html(settings.aiDisplay.length);
                }
            });

        }
    </script>
    <style>
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
        .tdStyle1{
            width:300px;
            background-color:#F8ECE9;
            padding:4px;
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
                            <div id="container">
                                <div id="dialog" class="easyui-dialog" title="" style="display:none"></div>
                                <div>
                                    <form name="mycal">
                                        <div style="width:550px;padding-left:20px;">
                                            結案日統計表
                                        </div>
                                        <table cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">
                                            <tr>
                                                <td  class="tdStyle1">
                                                    保證號碼
                                                    <input type="text" name="cCertifiedId" style="width:150px;" maxlength="9">
                                                </td>
                                                <td  class="tdStyle1">
                                                    實際點交日
                                                    <input type="text" name="cEndDate" class="datepickerROC" style="width:100px;">
                                                </td>
                                                <td  class="tdStyle1">
                                                    履保費出款日
                                                    <input type="text" name="cCertifyDate" class="datepickerROC" style="width:100px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="background-color:#F8ECE9;">&nbsp;</td>
                                            </tr>
                                        </table>
                                        <div style="padding:20px;text-align:center;">
                                            <input type="button" value="查詢" onclick="go()" class="bt4" style="display:;width:100px;height:35px;">
                                        </div>
                                    </form>
                                </div>
                                <table style="margin-left: auto;margin-right: auto;">
                                    <tr style="background-color:#E4BEB1;text-align:center;">
                                        <td>案件總筆數</td>
                                        <td>功能</td>
                                    </tr>
                                    <tr style="text-align:center;background-color:#F8ECE9;">
                                        <td><span id="total_count">0</span></td>
                                        <td><a href="#" onclick="$('#content_list').show();">檢視明細</a></td>
                                    </tr>
                                </table>
                                <div id="content_list">
                                    <table cellspacing="0" id="table" style="width:100%;" >
                                        <thead>
                                        <tr>
                                            <td class="" width="30%">保證號碼</td>
                                            <td class="" width="30%">實際點交日</td>
                                            <td class="" width="">履保費出款日</td>
                                        </tr>
                                        </thead>
                                    </table>
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