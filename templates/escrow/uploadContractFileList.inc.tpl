<div style="clear:both"></div>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="table-title" width="70%">檔案名稱</td>
        <td class="table-title" width="20%">時間</td>
        <{if $cat != 'view'}>
        <td class="table-title" width="10%">&nbsp;</td>
        <{/if}>
    </tr>
    <{foreach from=$fileList key=key item=item}>
    <tr>
        <td class="table-content"><a href="<{$item.url}>" target="_balnk"><{$item.name}></a></td>
        <td class="table-content"><{$item.modifyTime}></td>
        <{if $cat != 'view'}>
        <td class="table-content"> <a href="#" onclick="UploadFileDelete('<{$item.name}>')">刪除</a></td>
        <{/if}>
    </tr>
    <{/foreach}>
</table>