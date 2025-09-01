<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<script></script> 
<script src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("[name='save']").click(function() {
           $("[name='form']").submit();
        });
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

  
}

    
</style>
</head>
<body>
<div id="show"></div>
<form id="reloadPage" method="POST">
    <input type="hidden" name="id" value="<{$data.sId}>">

</form>
<h1>銀行資訊編輯</h1>
   
<div id="tabs-contract">
<center>
<form action="" method="POST" name="form">
        <table>
            <tr>
                <th>銀行名稱：</th>
                <td>
                    <!-- <input type="text" name="bank" id=""  style="width:400px" value="<{$data.bBank}>"> -->
                     <{html_options name=bank options=$menu_bank selected=$data.bBank }>
                </td>
            </tr>
            <tr>
                <th>網址：</th>
                <td><input type="text" name="bankUrl" id=""  style="width:400px" value="<{$data.bUrl}>"></td>
            </tr>
            <tr>
                <th>常用照會電話</th>
                <td>
                    <input type="text" name="phoneArea" id="" value="<{$data.bPhoneArea}>" maxlength="4" style="width:40px" placeholder="區碼">-
                    <input type="text" name="phone" maxlength="10" style="width:80px" value="<{$data.bPhone}>" placeholder="電話">#
                    <input type="text" name="phoneExt" maxlength="6" style="width:40px" value="<{$data.bPhoneExt}>" placeholder="分機">
                </td>
            </tr>
            <tr>
                <th>備註：</th>
                <td><!-- <input type="text" name="bankNote" id=""  style="width:400px" maxlength="255" value="<{$data.bNote}>"> -->
                    <textarea name="bankNote" id="" cols="50" rows="10"><{$data.bNote}></textarea>
                </td>

            </tr>
            <tr>
                <td colspan="2" align="center">&nbsp;</td>
            </tr>
        </table>
        <div style="">
            <input type="button" name="save" value="儲存">
            <input type="hidden" name="cat" value="<{$cat}>">
            <input type="hidden" name="id" value="<{$id}>">
        </div>
    
</form>
<br><br>



</div>
</center>
</body>
</html>










