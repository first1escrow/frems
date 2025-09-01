<!DOCTYPE html>
<html lang="en">
<head>
    <title>前台欲顯示的出款紀錄</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function back() {
        parent.$.colorbox.close()
    }
    </script>
</head>
<body>

<div class="container">
    <h3>請勾選前台欲顯示的出款紀錄</h3>
    <br>
    <form method="POST" id="form1">
        <input type="hidden" name="save" value="OK">
        <table class="table table-hover">
            <thead>
                <th nowrap>是否顯示</th>
                <th>日期</th>
                <th nowrap>帳款摘要</th>
                <th>入款金額</th>
                <th>出款金額</th>
            </thead>
            <tbody>
                <{foreach from=$records key=k item=v}>
                <tr>
                    <td style="vertical-align:middle;">
                        <input type="hidden" name="detail[<{$k}>]" value="<{$v['json']}>">
                        <input type="checkbox" name="buyerWebShow[<{$k}>]" value="<{$v['hash']}>" <{if $v['hash']|in_array:$buyer}>checked<{/if}>>買方<br>
                        <input type="checkbox" name="ownerWebShow[<{$k}>]" value="<{$v['hash']}>" <{if $v['hash']|in_array:$owner}>checked<{/if}>>賣方
                    </td>
                    <td style="vertical-align:middle;"><{$v['date']}></td>
                    <td style="vertical-align:middle;"><{$v['detail']}><span style="font-size:9pt;color:red;"><{$v['taishinSp']}></span></td>
                    <td style="vertical-align:middle;text-align:right;"><{$v['income']}></td>
                    <td style="vertical-align:middle;text-align:right;"><{$v['outgoing']}></td>
                </tr>
                <tr style="background-color: #F8ECE9;">
                    <th>備註</th>
                    <td colspan="4"><{$v['remark']}></td>
                </tr>
                <{/foreach}>
            </tbody>
        </table>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-danger">儲存</button>
            <button type="button" class="btn btn-danger" onclick="back()">返回</button>
        </div>
        
    </form>
</div>

</body>
</html>
<{$alert}>