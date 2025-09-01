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

                $('[name="upload_format"]').change(function() {
                    if ($(this).val() == 'check') {
                        $('#notify-message').empty().text('※限EXCEL2007以上格式(.xlsx)');
                        $('[name="identity"]').val('');
                        $('[name="upload_file_identity"]').val('');

                        $('#win-date').hide();
                        $('#winDate').val('');
                    } 
                    
                    if ($(this).val() == 'identity') {
                        $('#notify-message').empty().text('※壓縮格式僅限為ZIP(.zip)');
                        $('[name="check"]').val('');
                        $('[name="upload_file"]').val('');

                        $('#win-date').hide();
                        $('#winDate').val('');
                    }

                    if (($(this).val() == 'winY') || ($(this).val() == 'winA')  || ($(this).val() == 'winX') || ($(this).val() == 'winZ1') || ($(this).val() == 'winZ2')) {
                        $('#notify-message').empty().text('※文字格式僅限為TXT(.txt)');
                        $('[name="check"]').val('');
                        $('[name="upload_file"]').val('');

                        if ($(this).val() == 'winA') {
                            $('#win-date').show();
                        } else {
                            $('#win-date').hide();
                            $('#winDate').val('');
                        }
                    }
                });

                $('#import').on('click', function() {
                    let option = $('[name="upload_format"] :selected').val();

                    if (option == 'winA') {
                        if (!$('[name="winDate"]').val()) {
                            alert('請選擇中獎日期');
                            $('#win-date').select().focus();

                            return false;
                        }
                    }

                    let file = $('[name="upload_file"]').val();
                    if (!file) {
                        alert('請選擇檔案');
                        $('[name="upload_file"]').select().focus();

                        return false;
                    }

                    $('#myform').submit();
                });
            });
			
			function dia(op) {
				$( "#dialog" ).dialog(op) ;
			}
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
         .tb_main{
            border: 1px solid #999;
        }
        .tb{
            border: 1px solid #FFFFFF;
            width: 90%;
            background-color: #FCEEEE;
        }
        .tb td{
            border-bottom: 1px solid #999;
        }
        .div-inline{ 
            display:inline;
            width: 70%;
            float: center;
            padding-bottom: 50px;
        } 
        .div-inline th{
          text-align: left;
        }
        .div-inline td{
            padding-left: 20px;
        }
        #show {
            padding: 50px;
        }
        .div-inline2{ 
            display:inline;
            width: 100%;
            float: center;
            padding-bottom: 50px;
        }
        .import-btn{
            width: 50px;
            height: 30px;
        }
        .notify{
            color: red;
            font-weight: bold;
            font-size: 10pt;
            vertical-align: sub;
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
			<ul id="menu">
			    <div id="dialog"></div>
			</ul>
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
                                        <center>
                                            <form name="myform" id="myform" method="POST" enctype="multipart/form-data" action="import_income.php">
                                                <table align="center" class="tb_main" cellpadding="10" cellspacing="10">
                                                    <tr>
                                                        <th align="center">
                                                            <select name="upload_format">
                                                                <option value="check" selected>上傳開立狀況檔案</option>
                                                                <option value="identity">上傳電子發票證明聯檔案</option>
                                                                <option value="winY">上傳中獎檔案（Y檔、已索取證明聯）</option>
                                                                <option value="winA">上傳中獎檔案（A檔、未歸戶）</option>
                                                                <option value="winX">上傳中獎檔案（X檔、未設定）</option>
                                                                <option value="winZ1">上傳中獎檔案（Z檔、已設定）</option>
                                                                <option value="winZ2">上傳中獎檔案（Z檔、已捐贈）</option>
                                                            </select>
                                                        </th>
                                                        <td align="center">
                                                            <input name="upload_file" type="file"  />
                                                        </td>
                                                        <td align="center">
                                                            <input type="button" class="import-btn" id="import" value="匯入">
                                                        </td>
                                                        <td nowrap>
                                                            <span class="notify" id="notify-message">※限EXCEL2007以上格式(.xlsx)</span>
                                                        </td>
                                                    </tr>
                                                    <tr id="win-date" style="display:none;">
                                                        <th align="right">中獎發票寄送日期：</th>
                                                        <td colspan="3">
                                                            <input type="date" name="winDate" id="winDate" value="">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
                                            <div id="show">
                                                <{$show}>
                                            </div>
                                        </center>
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
    </body>
</html>