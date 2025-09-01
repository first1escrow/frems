<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>回饋金寄送名單明細</title>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
	<script type="text/javascript">
		$(document).ready(function() {

		});

        function getCheckedValue() {
            var arr = new Array;
            $('[name="uuId[]"]').each(function() {
                if ($(this).prop('checked')) {
                    arr.push($(this).val());
                }
            });

            return arr;
        }

        function back() {
            location.replace('feedback_sms_feedback_send.php');
        }

		function goSend() {
            var data = getCheckedValue();
            if (data.length <= 0) {
                alert('請選取欲發送的紀錄');
                return;
            }

            $('#action-area').hide();
            var sn = data.join(',');
            $.ajax({
                url: 'feedback_sms_send_detail_mq.php',
                type: 'POST',
                dataType: 'html',
                data: {"sn": sn},
            })
            .done(function(txt) {
                switch (txt) {
                    case 'Y' : 
                        alert('已排定系統發送中...');
                    break;

                    case 'P' :
                        alert('部分發送失敗');
                    break;

                    default :
                        alert('發送異常');
                    break;
                }

                $('#NewsForm').submit();
            })
        
            return;

			// parent.$.fn.colorbox.close();//關閉視窗
		}

        function Del() {
            var data = getCheckedValue();
            if (data.length <= 0) {
                alert('請選取欲刪除的紀錄');
                return;
            }

            $('#action-area').hide();
            if (confirm('確認要刪除選取的紀錄?') === true) {
                var sn = data.join(',');
                $.ajax({
                    url: 'feedback_sms_send_detail_delete.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {"sn": sn},
                })
                .done(function(txt) {
                    if (txt == 'Y') {
                        alert('已刪除');
                    } else {
                        alert('操作異常');
                    }

                    $('#NewsForm').submit();
                })
            }
        
            return;
        }
	</script>
	<style>
		body{
			background-color: #F8ECE9
		}
		table th{
			
			padding: 5px;
			border: 1px solid #999;
		}
		table td{
			
			text-align: left;
			padding: 5px;
			border: 1px solid #999;
		}
		input {
			padding:10px;
			border:1px solid #CCC;
		}
	</style>
</head>
<body>
<center>
	<h1>回饋金寄送名單詳細內容</h1>
	<form method="POST"  id="NewsForm" >
        <input type="hidden" name="batch" value=<{$batch}>>

		<div style="padding-top:15px">
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>類型</th>
                        <th>店名</th>
                        <th>姓名</th>
                        <th>手機號碼</th>
                        <th>建立時間</th>
                    </tr>
                </thead>
                <tbody>
                    <{foreach from=$data key=k item=v}>
                    <tr>
                        <td style="text-align:center;vertical-align:top;" rowspan="2"><input type="checkbox" name="uuId[]" id="" value="<{$v.uuid}>" checked></td>
                        <td><{$v.sBrand}></td>
                        <td><{$v.sStore}></td>
                        <td><{$v.sName}></td>
                        <td><{$v.sMobile}></td>
                        <td><{$v.sCreated_at}></td>
                    </tr>
                    <tr>
                        <td colspan="5"><{$v.sSMS}></td>
                    </tr>
                    <{/foreach}>
                </tbody>
            </table>
		</div>
		<br>
		<div>
			<div id="action-area" style="padding-left:30px;float:center;display:inline">
				<input type="button" value="返回" class="btn" onclick="back()">
                &nbsp;
				<input type="button" value="確認" class="btn" onclick="goSend()">
                &nbsp;
				<input type="button" value="刪除" onclick="Del()" class="btn">
			</div>
			
		</div>
		
	</form>
</center>
	
</body>
</html>
