<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<script type="text/javascript">
$(document).ready(function() {
	showcount();

    $('#import').on('click', function() {
        let file = $('[name="upload_file"]').val();
        if (!file) {
            alert('請選擇檔案');
            $('[name="upload_file"]').select().focus();
            return false;
        }

        var bk = $('[name="bank"]').val();
        var sc = $('[name="bStoreClass"]').val();
        var sy = $('[name="sales_year"]').val();
        var se = $('[name="sales_season"]').val();
        var sales_year_end = $('[name="sales_year_end"]').val();
        var sales_season_end = $('[name="sales_season_end"]').val();
        var cd = $('[name="certifiedid"]').val();
        var ir = $('[name="invert_result"]').val();
        var bck = $('[name="storeSearch"]').val();
        var br = $('[name="branch"]').val();
        var scr = $('[name="scrivener"]').val();
        var bc = $('[name="bCategory"]').val();
        var ft = $('[name="filetype"]').val();
        var status = $('[name="status"]').val();
        var bd = $('[name="brand"]').val();
        var timeCategory = $('[name="timeCategory"]').val();
        var act = 's';
        let url = 'casefeedbackPDF2_delete.php';

        var file_data = $('[name="upload_file"]').prop('files')[0];   //取得上傳檔案屬性
        var form_data = new FormData();  //建構new FormData()
        form_data.append('file', file_data);  //物件加到file後面
        form_data.append('bk', bk);
        form_data.append('sc', sc);
        form_data.append('sy', sy);
        form_data.append('se', se);
        form_data.append('sales_year_end', sales_year_end);
        form_data.append('sales_season_end', sales_season_end);
        form_data.append('cd', cd);
        form_data.append('ir', ir);
        form_data.append('ir', ir);
        form_data.append('bck', bck);
        form_data.append('br', br);
        form_data.append('scr', scr);
        form_data.append('bc', bc);
        form_data.append('ft', ft);
        form_data.append('status', status);
        form_data.append('bd', bd);
        form_data.append('timeCategory', timeCategory);
        form_data.append('act', act);


        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: url,
            data: form_data,
            processData : false,
            contentType: false,
            success: function (data) {
                $('#container').html(data) ;
                $( "#dialog" ).dialog("close") ;
                alert('刪除成功');
            },
            error: function (e) {
                $( "#dialog" ).dialog("close") ;
                console.log(e);
            }
        });

    });
}) ;

function downloadPDF(id) {
	$('[name="dl"]').attr('action','pdf/pdfPrint_2020.php');
	$('[name="id"]').val(id);
	$('[name="dl"]').submit();
}

function checkALL() {
	var all = $('[name="all"]').prop('checked');

	if (all == true) {
		$('[name="allForm[]"]').prop('checked', true);
	} else {
		$('[name="allForm[]"]').prop('checked', false);
	}

	showcount();
}

function showcount(){
	var checkedCount = 0;
	var checkedMoney = 0;
	
	$(".Row").each(function() {
		$(this).find('[name="allForm[]"]').prop('checked');
		if ($(this).find('[name="allForm[]"]').prop('checked') ==  true) {
			checkedCount++;

			var money = parseInt($(this).find('.m').html().replace(/,/g, ''));
			checkedMoney = checkedMoney+money;
		}
	});

	$("#count").html(checkedCount);
	$("#money").html(checkedMoney.toLocaleString());
}

function CaseFun(id,cat){
	$.ajax({
		url: 'casefeedbackPDF2_CaseFun.php',
		type: 'POST',
		dataType: 'html',
		data: {id: id,cat:cat},
	})
	.done(function(msg) {
		alert(msg);

		if (cat == 1) {
			if ($("#lock"+id).attr('value') == '開啟') {
				$("#lock"+id).attr('value', '關閉');
			}else{
				$("#lock"+id).attr('value', '開啟');
			}
		}else{
			searchResult('s');
		}
	});
}


function searchResult(act) {
	var status = $('[name="caseStatus"]').val();
	var bk = $('[name="bank"]').val() ;
	var sc = $('[name="bStoreClass"]').val() ;
	var sy = $('[name="sales_year"]').val() ;
	var se = $('[name="sales_season"]').val() ;
	var cd = $('[name="certifiedid"]').val() ;
	var ir = $('[name="invert_result"]').val() ;
	var bck = $('[name="storeSearch"]').val();
	var br = $('[name="branch"]').val();
	var scr = $('[name="scrivener"]').val();
	var bc = $('[name="bCategory"]').val();

	if (status == 'a' && act == 's') {
		var url = 'casefeedbackPDF2_result_old.php' ;
	} else {
		var url = 'casefeedbackPDF2_result.php' ;
	}

	$( "#dialog" ).dialog("open") ;

	$.post(url,
		{'bank':bk,'bStoreClass':sc,'branch':br,'bCategory':bc,'invert_result':ir, 'sales_year_end':$('[name="sales_year_end"]').val(),'sales_season_end':$('[name="sales_season_end"]').val(),
		'sales_year':sy,'sales_season':se,'certifiedid':cd,'scrivener':scr,'bck':bck,'status':status,'act':act,'bd':$("[name='brand']").val(),"timeCategory":$("[name='timeCategory']:checked").val()},
		function(txt) {
			$('#container').html(txt) ;
			$( "#dialog" ).dialog("close") ;
	}).fail(function (jqXHR, textStatus, errorThrown) {
        /*打印jqXHR对象的信息*/
        // console.log(jqXHR.responseText); //必要的时候编码一下:encodeURIComponent(jqXHR.responseText);
        // console.log(jqXHR.status);
        // console.log(jqXHR.readyState);
        // console.log(jqXHR.statusText);
        /*打印其他两个参数的信息*/
        // console.log(textStatus);
        // console.log(errorThrown);
        
        $( "#dialog" ).dialog("close") ;

        alert(jqXHR.statusText);
    }) ;
}
</script>
<style>
.xxx-button {
	color:#FFFFFF;
	font-size:14px;
	font-weight:normal;
	
	text-align: center;
	white-space:nowrap;
	height:40px;
	
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

.xxx-button2 {
	color:#FFFFFF;
	font-size:14px;
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

.tb {
	padding:5px;
	margin-bottom: 20px;
	background-color:#FFFFFF;

}

.tb th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
	font-size: 12px;
}

.tb td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
	font-size: 12px;
}
</style>
</head>
<body>
    <{if $status == 1}>
        <div>
            <form name="myform" id="myform" method="POST" enctype="multipart/form-data" >
                <div>
                    <input name="upload_file" type="file"/>
                    <button  type="button" id="import" value="匯入">匯入</button> <span >※限EXCEL2007以上格式(.xlsx)上傳檔名不要有特殊符號或空白</span>
                </div>
            </form>
        </div>
    <{/if}>
    <center>
        <div>
            <table cellspacing="0" cellpadding="0" border="0" class="tb">
                <tr>
                    <th><input type="checkbox" name="all" checked="" onclick="checkALL()"></th>
                    <th>編號</th>
                    <th>店家名稱</th>
                    <th>請款方式</th>
                    <th>結算時間</th>
                    <th>狀態</th>
                    <th>回饋金額</th>
                    <th>建立時間</th>
                    <th>PDF</th>
                    <th>解鎖</th>
                    <th>刪除</th>
                </tr>

                <{foreach from=$list key=key item=item}>
                    <tr class="Row">
                        <td><input type="checkbox" name="allForm[]" value="<{$item.sId}>" onclick="showcount()" checked> </td>
                        <td><{$item.code}></td>
                        <td><{$item.sStoreName}></td>
                        <td><{$item.method}></td>
                        <td><{$item.sEndTime}>~<{$item.sEndTime2}></td>
                        <td><{$item.status}></td>
                        <td><span class="m"><{$item.sFeedBackMoneyTotal|number_format}></span></td>
                        <td><{$item.sCreatTime}></td>
                        <td><input type="button" value="PDF" class="xxx-button2" onclick="downloadPDF(<{$item.sId}>)"></td>
                        <td><input type="button" value="<{$item.Lock}>" class="xxx-button2" onclick="CaseFun(<{$item.sId}>,1)" id="lock<{$item.sId}>"></td>
                        <td><input type="button" value="刪除" class="xxx-button2" onclick="CaseFun(<{$item.sId}>,2)"></td>
                    </tr>
                <{/foreach}>
                <{if empty($list)}>
                <{/if}>
            </table>

            <form action="" method="POST" id="formResult">
                <input type="hidden" name="bank" value="<{$bank}>">
                <input type="hidden" name="bStoreClass" value="<{$bStoreClass}>">
                <input type="hidden" name="sales_year" value="<{$sales_year}>">
                <input type="hidden" name="sales_year_end" value="<{$sales_year_end}>">
                <input type="hidden" name="sales_season" value="<{$sales_season}>">
                <input type="hidden" name="sales_season_end" value="<{$sales_season_end}>">
                <input type="hidden" name="bCategory" value="<{$bCategory}>">
                <input type="hidden" name="certifiedid" value="<{$certifiedid}>">
                <input type="hidden" name="branch" value="<{$branch}>">
                <input type="hidden" name="scrivener" value="<{$scrivener}>">
                <input type="hidden" name="storeSearch" value="<{$storeSearch}>">
                <input type="hidden" name="status" value="<{$status}>">
                <input type="hidden" name="act" value="<{$act}>">
                <input type="hidden" name="bd" value="<{$brand}>">
                <input type="hidden" name="filetype" value="<{$filetype}>">
                <input type="hidden" name="brand" value="<{$brand}>">
                <input type="hidden" name="timeCategory" value="<{$timeCategory}>">
            </form>

            <div>店家勾選數量: <span id="count"></span>&nbsp;金額:<span id="money"></span></div>

            <input type="button" value="官網發佈" class="xxx-button" onclick="WebRelease()">
            <input type="button" value="返回" class="xxx-button" onclick="javascript:location.href='casefeedbackPDF2.php'">

            <form action="" name="dl" method="POST" target="_blank">
                <input type="hidden" name="id" value="">
            </form>
    </center>
</body>
</html>