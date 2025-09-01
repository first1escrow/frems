<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>催告期限通知</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    <{if empty($records)}>
        // alert('今日無催告到期案件');
        window.close();
    <{/if}>

    window.resizeTo(600, 900);

    // $('.cmc_overlay').hide();
    // $('.cmc_overlay').show();
});

function colorbx(url) {
	$.colorbox({href:url});
}

function reload() {
    window.location.reload();
}

function legalChecked(id, cId) {
    let url = '/includes/legal/legalNotifyChecked.php';

    let _checked = $('#lStatus_'+id).is(':checked') ? 'Y' : 'N';
    $.post(url, {'cId': cId, 'checked': _checked}, function(response) {
        if (response.status == 200) {
            let el = $('#block_' + id).clone();
            $('#block_' + id).remove();

            // if ($('#lStatus_'+id).is(':checked')) {
            if (_checked == 'Y') {
                el.insertAfter(".blocks:last");
            } else {
                $('#info-area').prepend(el);
            }
        } else {
            alert('更新失敗！(' + response.message + ')');
        }
    }, 'json')
    .fail(function() {
        alert('系統失敗！無法完成操作');
    });

    // if ($('#lStatus_'+id).is(':checked')) {
    //     let el = $('#block_' + id).clone();
    //     $('#block_' + id).remove();
    //     el.insertAfter(".blocks:last");
    // } else {
    //     let el = $('#block_' + id).clone();
    //     $('#block_' + id).remove();
    //     $('#info-area').prepend(el);
    // }
}
</script>
<style>
#container {
    padding: 20px;
}

.data-rows {
    border: 1px solid #CCC;
    border-radius: 15px;
}

.data-rows > div {
    padding: 5px;
}

.data-rows:nth-child(odd) {
    /* background: #CCCCCC; */
}

</style>
</head>
<body id="dt_example">
    <div class="cmc_overlay" style="display:none;">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

    <div id="container">
        <div style="padding-bottom: 15px;">
            <button class="btn btn-danger" onclick="reload()">重新整理</button>
        </div>
        <div id="info-area">
            <{foreach $records as $record}>
            <div class="blocks" id="block_<{$record.lId}>">
                <div class="data-rows">
                    <div class="row" style="padding: 10px 10px 0px 20px;">
                        <div class="col">
                            <input type="checkbox" id="lStatus_<{$record.lId}>" value="Y" <{if $record.lStatus == "Y"}>checked<{/if}> onclick="legalChecked('<{$record.lId}>', '<{$record.lCertifiecId}>')">
                            <label for="lStatus_<{$record.lId}>">已處理</label>
                        </div>
                    </div>

                    <hr>
                    
                    <div class="row">
                        <div class="col-3">
                            <div>保證號碼：</div>
                            <div><{$record.lCertifiecId}></div>
                        </div>

                        <div class="col-3">
                            <div>催告日期：</div>
                            <div><input style="width:100px;" value="<{$record.lDate}>" disabled></div>
                        </div>

                        <div class="col-6">
                            <div>事項：</div>
                            <div><input style="width:200px;" value="<{$record.item}>" disabled></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">備註：</div>
                        
                    </div>

                    <div class="row">
                        <div class="col"><textarea disabled style="width:99%;height: 100px;"><{$record.lRemark}></textarea></div>
                    </div>
                </div>
                <div style="margin-bottom:20px;"></div>
            </div>
            <{/foreach}>
        </div>
    </div>
</body>
</html>