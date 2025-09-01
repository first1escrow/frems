<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {

                $('#send').live('click', function () {
                    $('#form_sms').submit();
                });
              
                 $('#all').live('click', function () {
                    
                    if($("#all").prop("checked")){

                        $('input[name="people[]"]').each( function(i) {
                            $(this).prop("checked", true);
                        });

                    }else
                    {
                        $('input[name="people[]"]').each( function(i) {
                            $(this).prop("checked", false);
                        });
                        
                    }
                });

                $('#send').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
              
            });
        </script>
        <style type="text/css">
            #tabs {
                width:1000px;
                margin-left:auto; 
                margin-right:auto;
            }

            #tabs table th {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
            }

            #tabs table th .sml {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                font-size: 10px;
            }

            #tabs-contract h1
            {
                font-size: 20px;
            }
           
           
        </style>
    </head>
    <body id="dt_example">
            <div id="content">
                            <div id="tabs">
                                <div id="tabs-contract">
                                    <h1>簡訊對象</h1>
                                    <form id="form_sms"  method="POST" >
                                        <input type="hidden" name="check" value="send">
                                        <table border="0" width="100%">

                                            <tr>
                                                <th width="10%"><center><input type="checkbox"  id="all"></center></th>
                                                <th width="10%"><center>編號</center></th>
                                                <th width="5%"><center>點數</center></th>
                                                <th width="15%"><center>地政士姓名</center></th>
                                                <th width="30%"><center>地政士事務所</center></th>
                                                <th width="10%"><center>簡訊對象</center></th>
                                                <th width="20%"><center>手機</center></th>
                                               
                                            </tr>
                                            <{foreach from=$scrivener key=key item=item}>
                                            <tr>
                                                <td align="center">
                                                    <input type="checkbox" name="people[]" value="<{$item.sId}>">
                                                    <!-- <input type="button" name="point[]" value="<{$item.point}>"> -->
                                                </td>
                                                <td align="center"><{$item.cScrivener}></td>
                                                <td align="center"><{$item.point}></td>
                                                <td align="center"><{$item.MainName}></td>
                                                <td align="center"><{$item.office}></td>
                                                <td align="center"><{$item.smsName}></td>
                                                <td align="center"><{$item.smsMobile}></td>
                                            </tr> 
                                                        
                                            <{/foreach}>        
                                               
                                                
                                        </table>
                                    </form>
                                        <br/>
                                        <center>
                                               <button id="send">寄送</button>
                                        </center>
                                </div>
                           
                        </div>
                </div>
                <div id="dialog"></div>
    </body>
</html>










