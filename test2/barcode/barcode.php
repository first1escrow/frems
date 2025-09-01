<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
// require_once('../../tcpdf/tcpdf.php');
require_once('../../tcpdf/tcpdf_barcodes_1d.php');



$code = (!empty($_POST['Code'])) ? $_POST['Code']:'EXAMPLE';


// set the barcode content and type
$barcodeobj = new TCPDFBarcode($code, 'C128');

// output the barcode as PNG image
$data = $barcodeobj->getBarcodePngData(2, 30, array(0,0,0));


//getBarcodeHTML($w=2, $h=30, $color='black')
// $img= $barcodeobj->getBarcodeHTML(2, 30, 'black');







?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>BarCode</title>
</head>
<body>
<form action="" method="POST">
	Code:<input type="text" name="Code" value="">
	<input type="submit" value="送出">
</form>
<br>
	<?php echo '<img src="data:image/png;base64,' . base64_encode($data) . '" />'; ?>
</body>
</html>