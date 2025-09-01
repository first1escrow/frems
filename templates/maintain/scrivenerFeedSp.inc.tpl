<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<script></script> 
<script src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    });
    function Edit(){
       
        var input = $('input');
        var textarea = $('textarea');
        var select = $('select');
        var arr_input = new Array();
        var reg = /.*\[]$/ ;

                    
                   
        $.each(input, function(key,item) {
                        if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }
                        }else if ($(item).is(':radio')) {
                            if ($(item).is(':checked')) {
                                arr_input[$(item).attr("name")] = $(item).val();
                            }

                        } else {
                            if (reg.test($(item).attr("name"))) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                    
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();

                            }else{  
                                arr_input[$(item).attr("name")] = $(item).attr("value");
                            }
                            
                        }
        });
        
        var obj_input = $.extend({}, arr_input);
        
        //仲介品牌回饋比率編輯
        $.ajax({
                url: 'scrivenerFeedSpEdit.php',
                type: 'POST',
                dataType: 'html',
                data: obj_input,
            }).done(function(txt) {
                alert('成功');
                $("#showData").empty();
                $("#showData").html(txt);
            });
                        
        $("#nReacllBranch").val('');
        $("#nRecallScrivener").val('');
        
        
        $("[name='sSpRecall']", window.parent.document).val(0);

    }
    function Del(id){
        $("[name='del']").val(id);
        Edit();
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
    <input type="hidden" name="id" value="<{$sId}>">
</form>
<h1>地政士特殊回饋</h1>
   
<div id="tabs-contract">
    <div style="padding-left:15px;">
        <form action="" method="POST">

            <div id="copy">
                <table>
                    <tr>
                        
                        <td>仲介回饋比率：<input type="text" name="ReacllBranch[]" id="nReacllBranch" value="" style="width:50px"></td>
                        <td>地政士回饋比率：<input type="text" name="RecallScrivener[]" id="nRecallScrivener" value="" style="width:50px"></td>
                        <input type="hidden" name="del" value="">
                        <input type="hidden" name="sId" value="<{$sId}>">
                    </tr>
                </table>
                
                
                  
            </div>
            <div class="test">
                <input type="button" value="增加" onclick="Edit()">
                <input type="hidden" name="ok" value="0">
            </div>   
            
        
        <br><br>
        <div id="showData">
            <table>
                <{assign var='count' value=1}> 
                <{foreach from=$list key=key item=item}>  
                    <tr>
                        <td>仲介回饋比率：<input type="text" name="ReacllBranch[]" id="" value="<{$key}>" style="width:50px"></td>
                        <td>地政士回饋比率：<input type="text" name="RecallScrivener[]" id="" value="<{$item}>" style="width:50px"></td>
                        <td>
                            <input type='button' onclick='Edit()' value='修改'>&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type='button' onclick='Del(<{$count}>)' value='刪除' alt="<{$count++}>">    
                        </td>
                    </tr>
                <{/foreach}>

            </table>
            
        </div>
        </form>
    </div>
</div>

</body>
</html>










