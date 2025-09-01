<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="../js2/bootstrap/css/bootstrap.min.css" rel="stylesheet" >
	<script src="../js2/jquery.js" ></script>

	<title>重新開立發票</title>
	<script>
		$(document).ready(function(){

			<{if $msg != ''}>
				alert("<{$msg}>");
			<{/if}>
		});
	</script>
</head>
<body>
	<div class="container">
		<h3>重新開立發票</h3>
		<form name="myform" id="myform" method="POST" >
			<fieldset>
			
				<label for="certifiedId" >保證號碼</label>
				<input type="text" id="certifiedId" name="certifiedId" placeholder="請填入保證號碼">
			
			<input type="submit" value="送出" class="btn btn-primary">
			</fieldset>
	    </form>
	</div>
    <script src="../js2/bootstrap/js/bootstrap.bundle.min.js" ></script>
</body>
</html>