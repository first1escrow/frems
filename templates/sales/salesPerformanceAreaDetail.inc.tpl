<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js" integrity="sha256-2krYZKh//PcchRtd+H+VyyQoZ/e3EcrkxhM8ycwASPA=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
	<script type="text/javascript">
    $(document).ready(function() {

    });

    function transfer() {
        let _tag_store = $('[name="area_replace_store"]:checked');
        let _tag_sales = $('[name="area_replace_sales"]');

        if (!_tag_sales.val() || (_tag_sales.val() <= 0)) {
            alert('請指定欲轉移業務');

            $(_tag_sales).focus().select();
            return;
        }

        if (!_tag_store.val()) {
            _tag_store.val('');
        }

        update(_tag_sales.val(), _tag_store.val(), $('[name="sDate"]').val());
    }

    function update(_sales, _store, _date) {
        let _zip = "<{$zip}>";
        let _target = "<{$target}>";
        let _url = '/includes/sales/salesPerformanceAreaUpdate.php';

        $.post(_url, {"target": _target, "zip": _zip, "sales": _sales, "store": _store, "date": _date}, function(response) {
            console.log(response);

            if (response.status == 200) {
                alert('已更新');
                parent.jQuery.colorbox.close();
            } else {
                alert(response.message);
            }
        }, 'json');
    }

    function closeColorbox() {
        parent.jQuery.colorbox.close();
    }

    function showTransferDate() {
        $('#transfer_date').show();
    }
	</script>
	<style>
    .btn {
        width: 150px;
    }
	</style>
</head>
<body>
    <div style="padding-bottom: 20px;">
        <h3>
        <{if $target == 'S'}>
        地政士區域調整
        <{else}>
        仲介區域調整
        <{/if}>
        </h3>
    </div>
    <div>
        <table class="table" style="width: 96%">
            <thead>
                <tr>
                    <th>目標區域</th>
                    <th>目前業務</th>
                    <th>欲轉移業務</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="vertical-align: middle;"><{$data.city}><{$data.area}></td>
                    <td style="vertical-align: middle;"><{$data.name}></td>
                    <td style="vertical-align: middle;"><{html_options class="form-control" name="area_replace_sales" options=$menu_sales}></td>
                    <td style="vertical-align: middle;">
                        <input type="checkbox" id="area_replace_store" name="area_replace_store" value="ALL" onclick="showTransferDate()"> 
                        <label for="area_replace_store">一併轉移區域內的所有店家業務</label>
                        <div id="transfer_date" style="display: none;">
                            轉移日期：
                            <input type="date" name="sDate" value="<{$smarty.now|date_format:"%Y-%m-%d"}>">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="padding:10px 0 20px 0;text-align: center;">
            <input type="button" value="轉移" onclick="transfer()" style="margin-right: 20px;">
            <input type="button" value="關閉" onclick="closeColorbox()">

        </div>
    </div>
    
</body>
</html>