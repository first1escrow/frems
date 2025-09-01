<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {

              

                $('#send').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
              
            });
        </script>
        <style type="text/css">
            #tabs {
                width:800px;
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
                                    <h1>已寄送對象</h1>
                                    <form id="form_sms"  method="POST" >
                                        <input type="hidden" name="check" value="send">
                                        <table border="0" width="100%">

                                            <tr>
                                              
                                                <th width="10%"><center>簡訊對象</center></th>
                                                <th width="20%"><center>手機</center></th>
                                                <th width="70%"><center>簡訊內容</center></th>
                                              

                                            </tr>
                                            <{foreach from=$scrivener key=key item=item}>
                                            <tr>
                                                <td align="center"><{$item.mName}></td>
                                                <td align="center"><{$item.mMobile}></td>
                                                <td ><{$item.sms_txt}></td>

                                            </tr> 
                                                        
                                            <{/foreach}>        
                                               
                                                
                                        </table>
                                    </form>
                                        <br/>
                                        <center>
                                               <!-- <button id="send">寄送</button> -->
                                        </center>
                                </div>
                           
                        </div>
                </div>
                <div id="dialog"></div>
    </body>
</html>










