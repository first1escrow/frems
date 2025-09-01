<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
		$(document).ready(function() {
		  
		});
		
		function del(no) {
			//alert(no) ;
			$.ajax({
				url: 'delCertifiedId.php',
				type: 'POST',
				dataType: 'text',
				data: { 'cId' : no},
				success: function(txt) {
					if (txt == 'T') {
						$('#'+no).remove() ;
					}
					else {
						alert('錯誤!!無法作廢刪除保證號碼!!') ;
					}
				},
				error: function() {
					alert('錯誤!!連線失敗!!') ;
				}				
			}) ;
		}
            
        </script>
        <style type="text/css">
            #tabs {
                
                margin-left:auto; 
                margin-right:auto;
                border: 1px solid #CCC;
            }
            #tabs tr {

            }

            #tabs table th {
                text-align:center;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
            }
             #tabs table td {
				text-align:center;
				border-top: 1px solid #CCC;
             }

             .btn {
                color: #000;
                font-family: Verdana;
                font-size: 14px;
                font-weight: bold;
                line-height: 14px;
                background-color: #CCCCCC;
                text-align:center;
                display:inline-block;
                padding: 8px 12px;
                border: 1px solid #DDDDDD;
                        /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
            }
            .btn:hover {
                color: #000;
                font-size: 14px;
                background-color: #999999;
                border: 1px solid #CCCCCC;
            }
            .btn.focus_end{
                color: #000;
                font-family: Verdana;
                font-size: 14px;
                font-weight: bold;
                line-height: 14px;
                background-color: #CCCCCC;
                text-align:center;
                display:inline-block;
                padding: 8px 12px;
                border: 1px solid #FFFF96;
                /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
            }

          
        </style>
    </head>
    <body id="dt_example">
            <div id="content">
                <form action="" method="POST">
                        <div id="tabs">
                                <div id="tabs-contract">
                                    
                                    <table border="0" width="100%">
                                        <tr>
                                            <th width="10%">銀行</th>
                                            <th width="15%">版本</th>
                                            <th width="15%">保證號碼</th>
                                            <th width="15%">群義流水號</th>
                                            <th width="15%">申請日期</th>
                                            <th width="10%">申請人</th>
                                            <th width="10%">作廢/刪除</th>
                                           
                                        </tr>
                                        <tbody>
                                        
                                                <{foreach from=$data key=key item=item}>
                                            <tr id="<{$item.bAccount}>">
                                                <td bgcolor="<{$item.color}>"><{$item.bank}></td>
                                                <td bgcolor="<{$item.color}>"><{$item.BrandName}></td>
                                            	<td bgcolor="<{$item.color}>"><{$item.certifiedId}><input type="hidden" name="certifiedId[]" value="<{$item.bAccount}>"></td>  
                                                <td bgcolor="<{$item.color}>"><input type="text" name="no[]" id="" value="<{$item.bNo72}>"></td>    
                                                <td bgcolor="<{$item.color}>"><{$item.bCreateDate}></td>  
                                                <td bgcolor="<{$item.color}>"><{$item.pName}></td>                                       
                                            	<td bgcolor="<{$item.color}>"><a style="font-size:9pt;" href="javascript:del('<{$item.bAccount}>');">刪除</a></td>
                                            </tr>
                                                <{/foreach}>

                                    </table>

                                    
                                    
                                </div>
                           
                        </div>
                        <div style="text-align: center;margin-top: 10px;"><input type="submit" value="送出" class="btn"></div>
                </form>    
            </div>
				
    </body>
</html>










