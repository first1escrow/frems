<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST = escapeStr($_POST);

$cat = $_POST['cat'];
$id  = $_POST['id']; //目前ID

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>複製回饋資料</title>
	<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript">
        $(document).ready(function() {
        	$("[name='storeId']").keyup(function() {
        		$.ajax({
        			url: '../includes/maintain/getFeedBackStoreData.php',
        			type: 'POST',
        			dataType: 'html',
        			data: {id: $("[name='storeId']").val(),cat:1},
        		})
        		.done(function(msg) {
        			$("[name='import']").removeAttr('disabled');
                    $("#data").html(msg);
        		});
        	});

        	$("[name='import']").on('click',  function() {
				$('input:checkbox:checked[name="id"]').each(function(i) {
					var id = this.value;
					var cno = parseInt($("[name='data_feedback_count']",window.parent.document).val());

                    if ((cno + i) > 0) {
                        $("#cl",window.parent.document).click();
                        cno++;
                    }

                    $('.newrow:last [name="newFeedBack[]"]',window.parent.document).val($("[name='fFeedBack_"+id+"']").val());
                    $('.newrow:last [name="newTtitle[]"]',window.parent.document).val($("[name='fTitle_"+id+"']").val());
                    $('.newrow:last [name="newMobileNum[]"]',window.parent.document).val($("[name='fMobileNum_"+id+"']").val());
                    $('.newrow:last [name="newIdentity[]"]',window.parent.document).val($("[name='fIdentity_"+id+"']").val());
                    $('.newrow:last [name="newIdentityNumber[]"]',window.parent.document).val($("[name='fIdentityNumber_"+id+"']").val());

                    //
                    $('.newrow:last [name="newRtitle[]"]',window.parent.document).val($("[name='fRtitle_"+id+"']").val());

                    $('.newrow:last [name="newzipC[]"]',window.parent.document).val($("[name='fZipC_"+id+"']").val());
                    $('.newrow:last [name="newzipCF"]',window.parent.document).val($("[name='fZipC_"+id+"']").val());
                    $('.newrow:last [name="newcountryC"]',window.parent.document).val($("[name='cityC_"+id+"']").val());
                    getArea2(cno,id,'newareaC',$("[name='cityC_"+id+"']").val()); //newareaC
                    $('.newrow:last [name="newaddrC[]"]',window.parent.document).val($("[name='fAddrC_"+id+"']").val());

                    $('.newrow:last [name="newzipR[]"]',window.parent.document).val($("[name='fZipR_"+id+"']").val());
                    $('.newrow:last [name="newzipRF"]',window.parent.document).val($("[name='fZipR_"+id+"']").val());
                    $('.newrow:last [name="newcountryR"]',window.parent.document).val($("[name='cityR_"+id+"']").val());
                    getArea2(cno,id,'newareaR',$("[name='cityR_"+id+"']").val()); //newareaC
                    $('.newrow:last [name="newaddrR[]"]',window.parent.document).val($("[name='fAddrR_"+id+"']").val());

                    //newEmail[]
                    $('.newrow:last [name="newEmail[]"]',window.parent.document).val($("[name='fEmail_"+id+"']").val());
                    $('.newrow:last [name="newAccountNum[]"]',window.parent.document).val($("[name='fAccountNum_"+id+"']").val());

                    GetBankBranchList($('.newrow:last [name="newAccountNum[]"]',window.parent.document),$('.newrow:last [name="newAccountNumB[]"]',window.parent.document),$("[name='fAccountNumB_"+id+"']").val());

                    $('.newrow:last [name="newAccount[]"]',window.parent.document).val($("[name='fAccount_"+id+"']").val());
                    $('.newrow:last [name="newAccountName[]"]',window.parent.document).val($("[name='fAccountName_"+id+"']").val());
                    $('.newrow:last [name="newNote[]"]',window.parent.document).val($("[name='fNote_"+id+"']").val());
				});

                alert('已匯入');

                $("#copy:last").css({'display':'none'}) ;
        	});
        });

 			function getArea2(cno,id,name,city) {
                var url = '../escrow/listArea.php' ;

			    $('.newrow:last #newareaCR option',window.parent.document).remove() ;

			    $.post(url,{"city":city},function(txt) {
			        var str = '' ;
			        str = str + txt  ;
			        $('.newrow:last [name="'+name+'"]',window.parent.document).append(str) ;
                    if(name == 'newareaC') {
                        $('.newrow:last [name="'+name+'"]',window.parent.document).val($("[name='fZipC_"+id+"']").val());
                    } else {
                        $('.newrow:last [name="'+name+'"]',window.parent.document).val($("[name='fZipR_"+id+"']").val());
                    }

			    }) ;
            }

            function GetBankBranchList(bank, branch, sc) {
                $(branch).prop('disabled',true) ;

                var request = $.ajax({
                    url: "/includes/maintain/bankbranchsearch.php",
                    type: "POST",
                    data: {
                        bankcode: $(bank).val()
                    },
                    dataType: "json"
                });
                request.done(function( data ) {
                    $(branch).children().remove().end();
                    $(branch).append('<option value="">------</option>')
                    $.each(data, function (key, item) {
                        if (key == sc ) {
                            $(branch).append('<option value="'+key+'" selected>'+item+'</option>');
                        } else {
                            $(branch).append('<option value="'+key+'">'+item+'</option>');
                        }
                    });
                });

                $(branch).prop('disabled',false) ;
            }
	</script>
	<style>
		table{
			width: 100%
		}
		 th{
            color: rgb(255, 255, 255);
            font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
            font-size: 12px;
            font-weight: bold;
            background-color: rgb(156, 40, 33);
            padding: 4px;
            border: 1px solid #CCCCCC;
        }
        td{
            color: rgb(51, 51, 51);
            font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
            font-size: 12px;
            padding: 4px;
            border: 1px solid #CCCCCC;
        }
	</style>
</head>
<body>
	請輸入資料來源的店家編號(格式範例:地政士SC0000;仲介TH00000)
	<form action="">
		店家編號:<input type="text" name="storeId" id="">
		<div id="data">
		</div>
		<div >
			<input type="button" name="import" value="匯入" disabled="disabled">
		</div>
	</form>
</body>
</html>
