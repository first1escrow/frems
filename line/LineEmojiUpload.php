<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

if ($_POST) {
	$filename = $_FILES['image']['name'] ;
	$tmpname = $_FILES['image']['tmp_name'] ;
	$filetype = $_FILES['image']['type'] ;
	$filesize = $_FILES['image']['size'] ;    
	$file = NULL ;
		
	if($_FILES['image']['error']==0) $file = base64_encode(file_get_contents($tmpname)) ;

	$sql = "INSERT INTO tLineMoji (lTxt,lPic,lCode) VALUES ('".$_POST['txt']."','".$file."','".$_POST['code']."')";
	$conn->Execute($sql);
	// echo $sql;
	
	unset($file);
}
?>
<!DOCTYPE>
<html>
<head>
	<meta charset="UTF-8">
	<title>上傳LINE表情貼</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		文字描述：<input type="text" name="txt"><br><br>
		圖片代碼：<input type="text" name="code"><br><br>
		上傳：<input type="file" name="image" id=""><br><br>

		<input type="submit" value="上傳">
	</form>
</body>
</html>