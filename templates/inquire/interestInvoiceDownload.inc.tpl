<!DOCTYPE html>
<html>
<head>
    <title>利息與發票下載</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript">
    $(document).ready(function() {

    });
    </script>
    <style>
    </style>
</head>
<body id="dt_example">
    <form method="POST">
        <div style="height:100px;width:300px;text-align:center;">
            <input type="date" name="date" value="<{$today}>">
            <button class="btn btn-danger" type="submit">下載</button>
        </div>
    </form>
</body>
</html>