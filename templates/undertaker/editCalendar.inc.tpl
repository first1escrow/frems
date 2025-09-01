<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <link rel="stylesheet" href="../css/colorbox.css" />        
        <{include file='meta.inc.tpl'}>
        <script src="/js/IDCheck.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                
            });

            function edit(){
                $("#form").submit();
            }
            
        </script>
        <style type="text/css">
            #tabs {
               width:90%;
               margin-left:auto; 
               margin-right:auto;
            }

            #tabs table th {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                width: 40%

            }
            
            #tabs table th .sml {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                font-size: 10px;

            }
            #tabs table td{
                /*padding-left: 5px;
                padding-top:10px;
                padding-bottom:10px;*/
                padding:10px 5px;
            }
        input {
            padding:5px;
            border:1px solid #CCC;
        }
        textarea{
            padding:10px;
            border:1px solid #CCC;
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
            font-size:12px;
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
        
        .l2,.l3,.l4,.l21{
            width: 300px;
        }
        
        .input-color {  
            background-color:#e8e8e8 ;
        }
        .tb-title {
            font-size: 18px;
            padding-left:15px; 
            padding-top:10px; 
            padding-bottom:10px; 
            background: #D1927C;

        }
        .input-text-sml{
                width:36px;

            }
        .cb1 {
                padding:0px 10px;
            }
            .cb1 input[type="checkbox"] {/*隱藏原生*/
                /*display:none;*/
                position: absolute;
                left: -9999px;
            }
            .cb1 input[type="checkbox"] + label span {
                display:inline-block;
                
                width:20px;
                height:20px;
                margin:-3px 4px 0 0;
                vertical-align:middle;
                background:url(../images/check_radio_sheet2.png) left top no-repeat;
                cursor:pointer;
                background-size:80px 20px;
                transition: none;
                -webkit-transition:none;
            }
            .cb1 input[type="checkbox"]:checked + label span {
                background:url(../images/check_radio_sheet2.png)  -20px top no-repeat;
                background-size:80px 20px;
                transition: none;
                -webkit-transition:none;
            }
            .cb1 label {
                cursor:pointer;
                display: inline-block;
                white-space: nowrap;
                margin-right: 10px;
                font-weight: bold;
                /*-webkit-appearance: push-button;
                -moz-appearance: button;*/
            }
            /*input*/
            .xxx-input {
                color:#666666;
                font-size:16px;
                font-weight:normal;
                /*background-color:#FFFFFF;*/
                text-align:left;
                height:34px;
                padding:0 5px;
                border:1px solid #CCCCCC;
                border-radius: 0.35em;
            }
            .xxx-input:focus {
                border-color: rgba(82, 168, 236, 0.8) !important;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
                outline: 0 none;
            }

            /*textarea*/
            .xxx-textarea {
                color:#666666;
                font-size:16px;
                font-weight:normal;
                line-height:normal;
                /*background-color:#FFFFFF;*/
                text-align:left;
                height:100px;
                padding:5px 5px;
                border:1px solid #CCCCCC;
                border-radius: 0.35em;
            }
            .xxx-textarea:focus {
                border-color: rgba(82, 168, 236, 0.8) !important;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
                outline: 0 none;
            }
            .xxx-select {
                    color:#666666;
                    font-size:16px;
                    font-weight:normal;
                    /*background-color:#FFFFFF;*/
                    text-align:left;
                    height:34px;
                    padding:0 0px 0 5px;
                    border:1px solid #CCCCCC;
                    border-radius: 0em;
                }
        </style>
    </head>
    <body id="dt_example">
         <div id="tabs">
                <center>
                <form action="" method="POST" id="form" >
                    <table cellspacing="0" cellpadding="0" width="100%" class="tb">
                        <tr>    
                            <td colspan="4" class="tb-title">&nbsp;</td>
                        </tr>

                        <tr>
                            <th>經辦</th>
                            <td colspan="3">
                                 <{html_options name=undertaker options=$menuUndertaker selected=$data.uStaff class="xxx-select"}>
                            </td>
                        </tr>
                        <tr>
                            <th>代理人</th>
                            <td colspan="3"><{html_options name=substituteStaff options=$menuUndertaker selected=$data.uSubstituteStaff class="xxx-select"}></td>
                        </tr>
                        <tr>
                            <th>開始時間</th>
                            <td colspan="3">
                                <input type="text" name="DateStart" class="datepickerROC" value="<{$data.sDate}>" >
                                <select name="DateStartTime" id="" class="xxx-select"><{$data.sTimeMenu}></select>
                               
                                

                            </td>
                        </tr>
                        <tr>
                            <th>結束時間</th>
                            <td colspan="3">
                               
                                <input type="text" name="DateEnd" class="datepickerROC" value="<{$data.eDate}>" >
                                <select name="DateEndTime" id="" class="xxx-select"><{$data.eTimeMenu}></select>

                            </td>
                        </tr>
                        <tr>
                            <th>備註</th>
                            <td colspan="3">
                                <textarea name="Note" id="" cols="30" rows="10" class="xxx-textarea"><{$data.uNote}></textarea>
                            </td>
                        </tr>
                        <tr>    
                            <td colspan="4" align="center">
                                <input type="hidden" name="ok">
                                <input type="hidden" name="cat" value="<{$cat}>">
                                <input type="button" value="送出" onclick="edit()">
                            </td>
                        </tr>
                    </table>
                    
                    </form>
                </center>   
            </div>
    </body>
</html>










