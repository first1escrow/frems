<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="UTF-8">
	<title>回饋金寄送名單</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
            $('#example').DataTable({
                "searching": false,
                "ordering":  false,
                "lengthChange": false,
                "language": {
                    "info": "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                    "infoEmpty": "顯示第 0 至 0 項結果，共 0 項",
                    "emptyTable": "無待發送資料",
                    "paginate": {
                        "first": "第一頁",
                        "previous": "上一頁",
                        "next": "下一頁",
                        "last": "最後一頁"
                    }
                }
            });
		});

		function win_close() {
			parent.$.fn.colorbox.close();//關閉視窗
		}

        function detail(id) {
            $('[name="batch"]').val(id);
            $('#NewsForm').submit();
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
	</style>
</head>
<body>
<center>
	<h1>回饋金寄送名單確認</h1>
	<form method="POST"  id="NewsForm" >
        <input type="hidden" name="batch">

		<div style="padding-top:15px">
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>對象</th>
                        <th>簡訊內容</th>
                        <th>建立時間</th>
                    </tr>
                </thead>
                <tbody>
                    <{foreach from=$data key=k item=v}>
                    <tr>
                        <td style="text-align:center;"><button onclick="detail('<{$v.batch}>')">明細</button></td>
                        <td><{$v.target}></td>
                        <td><{$v.sms}></td>
                        <td><{$v.datetime}></td>
                    </tr>
                    <{/foreach}>
                </tbody>
            </table>
		</div>
		<br>
		<div>
			<div style="padding-left:30px;float:center;display:inline">
				<!-- <input type="button" value="關閉" class="btn" onclick="win_close()">-->
                <button onclick="win_close()">關閉</button>
			</div>
		</div>
		
	</form>
</center>
	
</body>
</html>
