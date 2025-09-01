<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<script></script> 
<script src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    });
    function Edit(type,id){
        
        if (type == 'add') {
            var brand = $("[name='newBrand']").val();
            var recall = $("[name='newRecall']").val();
            var recallb = $("[name='newsReacllBrand']").val();

            $("[name='newBrand']").val('');
            $("[name='newRecall']").val('');
            $("[name='newsReacllBrand']").val('');

        }else{
            var brand = $("[name='Brand"+id+"']").val();
            var recall = $("[name='Recall"+id+"']").val();
            var recallb = $("[name='ReacllBrand"+id+"']").val();
        }
        
        //仲介品牌回饋比率編輯
        $.ajax({
                url: 'brandForScrEdit.php',
                type: 'POST',
                dataType: 'html',
                data: {"brand": brand,"recall":recall,"recallb":recallb,"type":type,"sId":"<{$sId}>","id":id},
            }).done(function(txt) {
                alert('成功');
                $("#showData").empty();
                $("#showData").html(txt);
            });
                        

        
        
        

    }
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
    #copy{
        float: left;
        display:inline; 
        width: 80%;
        
    }
    .test{
        
        text-align: left;
        display:inline; 
        width: 10%;
        
    }

    #showData{
        border-radius: 0.5em 0.5em 0.5em 0.5em;
        border: 1px solid #DDDDDD;
    }

    
    
  
}

    
</style>
</head>
<body>
<div id="show"></div>
<form id="reloadPage" method="POST">
    <input type="hidden" name="id" value="<{$data.sId}>">
</form>
<h1>品牌回饋代書</h1>
   
<div id="tabs-contract">
    <div style="padding-left:15px;">
        <form action="" method="POST">

            <div id="copy">
                <table>
                    <tr>
                        <td>品牌：<{html_options name=newBrand options=$menu_brand selected=$data.bBrand}></td>
                        <td>品牌回饋比率：<input type="text" name="newsReacllBrand" id="" value="" style="width:50px"></td>
                        <td>地政士回饋比率：<input type="text" name="newRecall" id="" value="" style="width:50px"></td>
                    </tr>
                </table>
                
                
                  
            </div>
            <div class="test"><input type="button" value="增加" onclick="Edit('add','')"><input type="hidden" name="ok" value="0"></div>   
            
        </form>
        <br><br>
        <div id="showData">
           <table>
                    <{foreach from=$list key=key item=item}>
                    <tr>
                        <td>品牌：<{html_options name="Brand<{$item.sId}>" options=$menu_brand selected=$item.sBrand}></td>
                        <td>品牌回饋比率：<input type="text" name="ReacllBrand<{$item.sId}>" id="" value="<{$item.sReacllBrand}>" style="width:50px"></td>
                        <td>地政士回饋比率：<input type="text" name="Recall<{$item.sId}>" id="" value="<{$item.sRecall}>" style="width:50px"></td>
                        <td><input type='button' onclick='Edit("mod","<{$item.sId}>")' value='修改'>&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type='button' onclick='Edit("del","<{$item.sId}>")' value='刪除'></td>
                    </tr>
                    <{/foreach}>
                </table>
            
        </div>
    </div>
</div>

</body>
</html>










