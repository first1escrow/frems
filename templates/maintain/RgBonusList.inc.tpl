<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<script></script> 
<link rel="stylesheet" type="text/css" href="../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="../js/jquery-1.7.2.min.js"></script>

<script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
<script src="/js/datepickerRoc.js"></script>
<link rel="stylesheet" href="/css/datepickerROC.css" />

<script type="text/javascript">
    $(document).ready(function() {
    });
   
</script>
<style type="text/css">
    body{

       background: #F8ECE9;
    }
    h1{
        margin-top: 2em;
        font-size: 1.3em;
        font-weight: bold;
        line-height: 1.6em;
        color: #4E6CA3;
        border-bottom: 1px solid #B0BED9;
        clear: both;
    }
    
    th{
        line-height: 20px;
        font-size: 14px;
        padding: 5px;
        border:1px solid #000;
    }

    td{
        line-height: 20px;
        font-size: 14px;
        padding: 5px;
        border:1px solid #000;
    }
    
}

    
</style>
</head>
<body>
<h1>加值紀錄</h1>
<div id="tabs-contract">
    <div style="padding-left:15px;padding-bottom:15px">
         <form action="" method="POST">
            時間<input type="text" name="date" id="" class="datepickerROC" style="width:100px;">
            <input type="hidden" name="acc" value="">
            <input type="submit" value="查詢">
        </form>
    </div>
   
    <div style="padding-left:15px;">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th>加值時間</th>
                <th>加值金額</th>
                <th>加值人員</th>
            </tr>
            <{foreach from=$list key=key item=item}>
            <tr>
                <td><{$item.rTime}></td>
                <td><{$item.rMoney}></td>
                <td><{$item.Name}></td>
            </tr>
            <{/foreach}>
        </table>
    </div>
</div>

</body>
</html>










