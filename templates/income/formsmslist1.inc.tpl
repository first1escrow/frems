<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <link rel="stylesheet" href="/css/colorbox.css" />
        <script src="/js/jquery.colorbox.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#save').on('click', function () {
                    let checked_item = 0;
                    $('.checked_verify').each(function() {
                        if ($(this).is(':checked')) {
                            checked_item++;
                        }
                    });

                    if (checked_item == 0) {
                        alert('請選擇對象');
                        return false;
                    }

                    let content = 0;
                    $('.contents').each(function() {
                        if ($(this).val() != '') {
                            content++;
                        }
                    });

                    if (content == 0) {
                        alert('請輸入簡訊內容');
                        return false;
                    }

                    if (confirm("確定要發送簡訊通知？")==true) {
                        $.ajax({
                            url: '/income/v1/formsms.php',
                            type: 'POST',
                            dataType: 'html',
                            data: $('#form').serialize(),
                        }).done(function() {
                            alert('簡訊已依序發送中');
                            parent.$.colorbox.close();
                        }).fail(function(jqXHR, textStatus) {
                            console.log(jqXHR.responseText);
                            alert('簡訊發送失敗');
                        });
                    }
                });

                $('#cancel').live('click', function () {
                    parent.$.colorbox.close();
                });

                $('#save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );

                $('#cancel').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
            });

            function clickAll(cat) {
                let all_checked = $("[name='"+cat+"_all']").prop("checked");
                let dom = $("[name='"+cat+"_serial[]']");
                dom.prop('checked', all_checked);
            }
        </script>
        <style type="text/css">
            body{
                text-align: center;
                font-size: 16px;
            }

            #tabs {
                width:80%;
                margin-left:auto; 
                margin-right:auto;
            }

            #tabs table th,td {
                border-bottom: 1px #CCC solid;
                padding-top:10px;
            }

            #tabs table th {
                text-align:right;
                /* background: #E4BEB1; */
                padding-top:10px;
                padding-bottom:10px;
            }

            #tabs table th .sml {
                text-align:right;
                /* background: #E4BEB1; */
                padding-top:10px;
                padding-bottom:10px;
                font-size: 12px;
            }

            .input-text-per{
                width:96%;
            }

            .input-text-big {
                width:120px;
            }

            .input-text-mid{
                width:80px;
            }

            .input-text-sml{
                width:36px;
            }

            .content td{
                padding-top:5px;
                font-size: 14px;
            }

            .sms_block{
                border: 1px #CCC solid;
            }

            .sms_block_title{
                font-family: "微軟正黑體","新細明體";
                font-size: 1.2em;
                font-weight: normal;
                line-height: 1.6em;
                color: red;
                text-align:left;
                padding-left:20px;
                background-color: #E4BEB1;
            }
        </style>
    </head>
    <body >
        <form id="form">
            <div id="content">
                <div id="tabs">
                    <div id="tabs-contract">
                        <form id="form" method="post">
                        <input type="hidden" name="expenseId" value="<{$id}>">
                        <input type="hidden" name="cId" value="<{$cId}>">
                        <input type="hidden" name="targetType" value="<{$targetType}>">
                        
                        <{foreach from=$list key=key item=item}>
                            <{if $key == 'owner'}>
                                <{assign var='title' value='賣方(經紀人)'}>
                                <{assign var='content' value=$item['content']}>
                            <{/if}>

                            <{if $key == 'ownerBoss'}>
                                <{assign var='title' value='賣方仲介店'}>
                                <{assign var='content' value=$item['content']}>
                            <{/if}>

                            <{if $key == 'buyer'}>
                                <{assign var='title' value='買方(經紀人)'}>
                                <{assign var='content' value=$item['content']}>
                            <{/if}>

                            <{if $key == 'buyerBoss'}>
                                <{assign var='title' value='買方或買賣方仲介店'}>
                                <{assign var='content' value=$item['content']}>
                            <{/if}>

                            <{if $key == 'scrivener'}>
                                <{assign var='title' value='地政士'}>
                                <{assign var='content' value=$item['content']}>
                            <{/if}>

                            <{if $item['target']|@count eq 0}>
                                <{continue}>
                            <{/if}>
                            <div class="sms_block">
                                <div class="sms_block_title"><{$title}>簡訊</div>
                                <table border="0" width="100%">
                                    <tr>
                                        <th width="10%"><center><label>選擇<input type="checkbox" name="<{$key}>_all" onclick="clickAll('<{$key}>')" checked style="margin-left:5px;"></label></center></th>
                                        <th width="10%"><center>身份</center></th>
                                        <th width="30%"><center>姓名</center></th>
                                        <th width="30%"><center>手機</center></th>
                                    </tr>
                                    <{foreach from=$item['target'] key=k item=v}>
                                    <tr>
                                        <td align="center"><input type="checkbox" class="checked_verify" name="<{$key}>_serial[]" value="<{$v.serial}>" checked></td>
                                        <td align="center"><input type="hidden" name="<{$key}>_title[<{$v.serial}>][]" value="<{$v.title}>"><{$v.title}></td>                                                   
                                        <td align="center"><input type="hidden" name="<{$key}>_name[<{$v.serial}>][]" value="<{$v.name}>"><{$v.name}></td>
                                        <td align="center"><input type="hidden" name="<{$key}>_mobile[<{$v.serial}>][]" value="<{$v.mobile}>"><{$v.mobile}></td>
                                        <{* <td align="center"><input type="checkbox" class="checked_verify" name="<{$key}>_sms[]" value="<{$v.mobile}>" checked></td>
                                        <td align="center"><input type="hidden" name="<{$key}>_title[<{$v.mobile}>][]" value="<{$v.title}>"><{$v.title}></td>                                                   
                                        <td align="center"><input type="hidden" name="<{$key}>_name[<{$v.mobile}>][]" value="<{$v.name}>"><{$v.name}></td>
                                        <td align="center"> <{$v.mobile}></td> *}>
                                    </tr>
                                    <{/foreach}>
                                    <tr>
                                        <td colspan="4">
                                        <{if $key == 'scrivener'}>
                                            <{foreach from=$item['content'] key=k item=v}>
                                            <div style="padding:10px 2px 10px 2px;;">
                                                <textarea class="contents" name="<{$key}>_content[]" style="width:100%" rows="3"><{$v}></textarea>
                                            </div>
                                            <{/foreach}>
                                        <{else}>
                                            <div style="padding:10px 2px 10px 2px;;">
                                                <textarea class="contents" name="<{$key}>_content[]" style="width:100%" rows="3" <{if $content == '' and ($key == 'owner' or $key == 'ownerBoss')}>placeholder="因款項只有買方的部分，所以賣方不發送"<{/if}>><{$content}></textarea>
                                            </div>
                                        <{/if}>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <{/foreach}>
                        </form>
                    </div>

                    <div style="margin-top:30px;">
                        <center>
                            <button type="button" id="save">送出</button>
                            <button type="button" id="cancel">取消</button>
                        </center>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>
