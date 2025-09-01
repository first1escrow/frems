<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$bId = $_REQUEST['bId'];

if ($_POST['save'] == 'ok') {
    //取得上傳檔案資訊
    $filename = $_FILES['image']['name'];
    $tmpname  = $_FILES['image']['tmp_name'];
    $filetype = $_FILES['image']['type'];
    $filesize = $_FILES['image']['size'];
    $file     = null;

    if ($_FILES['image']['error'] == 0) {
        $file = base64_encode(file_get_contents($tmpname));
    }

    if (!empty($bId)) {
        // $sql = 'SELECT `bId` FROM `tBranchStamp` WHERE 1 AND `bBranchId` = "'.$bId.'" ORDER BY `bId` DESC LIMIT 1;';
        // $rs = $conn->Execute($sql);

        // if ($rs->EOF) {
        //     $sql = 'INSERT INTO `tBranchStamp` SET `bBranchId` = "'.$bId.'", `bLastModify` = "'.date("Y-m-d H:i:s").'", `bStamp` = "'.$file.'";';
        // } else {
        //     $sql = 'UPDATE `tBranchStamp` SET `bLastModify` = "'.date("Y-m-d H:i:s").'", `bStamp` = "'.$file.'" WHERE `bId` = "'.$rs->fields['bId'].'" AND `bBranchId` = "'.$bId.'";';
        // }

        $sql = 'INSERT INTO `tBranchStamp` (`bBranchId`, `bStamp`) VALUES ("' . $bId . '", "' . $file . '") ON DUPLICATE KEY UPDATE `bStamp` = "' . $file . '";';
        if ($conn->Execute($sql)) {
            echo '
				<script>
                    var str = \'<div onclick="newImg()" style="cursor:pointer;"><img src="showStamp.php?bId=' . $bId . '&d=' . uniqid() . '" style="width:236px;height:150px;"></div>\' ;
                    parent.$("#showImg").empty().html(str) ;
                    parent.$.fn.colorbox.close();
				</script>
			';
        }
    }
}

$sql = 'SELECT `bBranchId` FROM `tBranchStamp` WHERE `bBranchId` = "' . $bId . '";';
$rs  = $conn->Execute($sql);

$stamp = ($rs->EOF) ? null : $rs->fields['bBranchId'];
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head runat="server">
    <meta http-equiv="cache-control" content="no-store" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>仲介大小章圖檔上傳</title>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="http://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="http://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
    <script>
    $(function() {
        function format_float(num, pos) {
            var size = Math.pow(10, pos);
            return Math.round(num * size) / size;
        }

        function preview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.preview').attr('src', e.target.result);
                    var KB = format_float(e.total / 1024, 2);
                    $('.size').text("檔案大小：" + KB + " KB");
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#image").on("change", function() {
            preview(this);
        })
    })

    function checkValid() {
        var f = $('#image').val()
        var re = /\.(jpg|png)$/i; //允許的圖片副檔名
        if (!re.test(f)) {
            alert("只允許上傳 JPG 或 PNG 影像檔");
            event.returnValue = false;
        }
    }

    function del(no) {
        if (confirm('是否要刪除該圖章檔案') === true) {
            $.post('stampDelete.php', {
                'bId': no
            }, function(response) {
                if (response == 'OK') {
                    alert('圖章檔案已刪除!!');

                    parent.$("#showImg").empty().html('未指定圖檔 ...');
                    parent.$.fn.colorbox.close();
                } else {
                    alert('刪除失敗');
                }
            })
        }

        return false;
    }
    </script>
</head>

<body>
    <form enctype="multipart/form-data" method="post" action="stamp.php" onsubmit="checkValid()">
        <input type="file" name="image" id="image" />
        <input type="hidden" name="save" value="ok">
        <input type="hidden" name="bId" value="<?=$bId?>">
        <div style="border: 1px solid #CCC; border-radius: 10px; padding: 10px;margin-top: 20px; text-align: center;">

            <input type="submit" value="確定上傳" />
            <?php
if (!empty($stamp)) {
    echo '　<input type="button" onclick="del(' . $stamp . ')" value="刪除圖檔">';
}
?>

        </div>
    </form>
    <div style="margin-top:10px;">
        <img class="preview" style="max-width: 150px; max-height: 150px;">
        <div class="size"></div>
    </div>
</body>

</html>