<?php

# 設定檔案存放目錄位置
if (!is_dir($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}

# 設定檔案名稱
$uploadfile = $_FILES['upload_file']['name'];
$uploadfile = $uploaddir . $uploadfile;

if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile)) {
    $_file = $uploaddir . $_FILES["upload_file"]["name"];
} else {
    exit('檔案上傳錯誤');
}
