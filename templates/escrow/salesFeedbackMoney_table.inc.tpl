<{foreach from=$SalesReview key=key item=item}>
<table cellpadding="0" cellspacing="0" width="100%" border="1">
<tr>
    <th colspan="6" >
    <{if $item.Status == 0}>
        <input type="button" value="修改" onclick="openFeedbackModify(<{$item.fId}>)">
        &nbsp;
        &nbsp;
        &nbsp;
        &nbsp;
        <input type="button" value="刪除" onclick="delSalesFeed(<{$item.fId}>)">
    <{/if}>
    </th>
</tr>
<tr>
    <th width="15%">案號︰</th>
    <td width="20%"><{$id}></td>
    <th width="10%">保證費金額︰</th>
    <td width="20%"><{$cMoney|number_format:0}></td>
    <th width="10%">狀態︰</th>
    <td width="20%"><{$item.fStatus}></td>
</tr>
<tr>
    <td colspan="6" class="tb-title">回饋對象</td>
</tr>
<tr>
    <th width="15%">仲介店名︰</th>
    <td colspan="5">
    <{$item.BranchName}>
</tr>
<tr>
    <th width="15%">案件回饋︰</th>
    <td>
        <span style="background-color:#CCC;">
        <{if $item.fCaseFeedback == 0}>
            回饋，金額：<{$item.fCaseFeedBackMoney|number_format:0}>元
        <{else}>
            不回饋
        <{/if}> 
        </span>
    </td>                                     
    <th>回饋對象︰</th>
    <td colspan="3">
        <{if $item.fFeedbackTarget  == 1}>
            仲介
        <{else}>
            地政士
        <{/if}> 
    </td>                                                                                         
</tr>
<{if $item.BranchName2 != ''}>
<tr >
    <th>仲介店名︰</th>
    <td colspan="5"><{$item.BranchName2}></td>
</tr>
<tr >
    <th>案件回饋︰</th>
    <td>
        <span style="background-color:#CCC;">
        <{if $item.fCaseFeedback2 == 0}>
            回饋，金額：<{$item.fCaseFeedBackMoney2|number_format:0}>元
        <{else}>
            不回饋
        <{/if}> 
        </span>
    </td>                                      
<th>回饋對象︰</th>
<td colspan="3">
    <{if $item.fFeedbackTarget2 == 1}>
        仲介
    <{else}>
        地政士
    <{/if}> 
                                            
</td>
</tr>
<{/if}>
<{if $item.BranchName3 != ''}>
<tr >
    <th>仲介店名︰</th>
    <td colspan="5"><{$item.BranchName3}></td>
</tr>
<tr >
    <th>案件回饋︰</th>
    <td>
        <span style="background-color:#CCC;">
        <{if $item.fCaseFeedback3 == 0}>
            回饋，金額：<{$item.fCaseFeedBackMoney3|number_format:0}>元
        <{else}>
            不回饋
        <{/if}> 
        </span>
    </td>                                  
    <th>回饋對象︰</th>
    <td colspan="3">
        <{if $item.fFeedbackTarget3 == 1}>
            仲介
        <{else}>
            地政士
        <{/if}>  
    </td>
</tr>
<{/if}>
<{if $item.BranchName6 != ''}>
<tr >
    <th>仲介店名︰</th>
    <td colspan="5"><{$item.BranchName6}></td>
</tr>
<tr >
    <th>案件回饋︰</th>
    <td>
        <span style="background-color:#CCC;">
        <{if $item.fCaseFeedback6 == 0}>
            回饋，金額：<{$item.fCaseFeedBackMoney6|number_format:0}>元
        <{else}>
            不回饋
        <{/if}> 
        </span>
    </td>                                  
    <th>回饋對象︰</th>
    <td colspan="3">
        <{if $item.fFeedbackTarget6 == 1}>
            仲介
        <{else}>
            地政士
        <{/if}>  
    </td>
</tr>
<{/if}>
<{if isset($item.ScrivenerSPFeedMoney) && $item.ScrivenerSPFeedMoney > 0}>
<tr> 
    <th>地政士事務所</th>
    <td colspan="2"><{$scrivener_office}></td>
    <th>特殊回饋︰</td>
    <td colspan="3">
        <span style="background-color:#CCC;"><{if $item.ScrivenerSPFeedMoney}><{$item.ScrivenerSPFeedMoney|number_format:0}><{else}>0<{/if}>元</span>
    </td>
</tr>
<{/if}>
<tr>
    <td colspan="6" class="tb-title">其他回饋對象</td>                              
</tr>
<{foreach from=$item.data key=key item=item2}>
<tr>
    <th>回饋對象：</th>
    <td>
        <{if $item2.fFeedbackTarget == 1}>
            地政士
        <{else}>
            仲介
         <{/if}>
    </td>
    <th>店名：</th>
    <td><{$item2.Name}></td>
    <th>回饋金：</th>
    <td>
        <span style="background-color:#CCC;"><{$item2.fCaseFeedBackMoney|number_format:0}>元</span>
    </td>
</tr>
<{if $item2.fCaseFeedBackNote != ''}>
<tr>
    <th>原因:</th>
    <td colspan="5"><{$item2.fCaseFeedBackNote}></td>
</tr>
<{/if}>
<{/foreach}>
<{foreach from=$delNote key=key item=item}>
    <{if $item.fNote != ''}>       
        <tr>
                                                
            <th>店名：</th>
            <td><{$item.Code}><{$item.Name}></td>
            <th>回饋金：</th>
            <td><{$item.fCaseFeedBackMoney}></td>
            <th>刪除原因:</th>
            <td ><{$item.fNote}></td>
        </tr>
    <{/if}>       
<{/foreach}> 
    
<{if $item.fNote != ''}>
    <tr>
        <td colspan="6" class="tb-title">備註：</td>                              
    </tr>
    <tr>
        <td colspan="6" style="padding-left: 10px;"><{$item.fNote}></td>
    </tr>
<{/if}>
<tr>
    <th>申請</th>
    <td colspan="2"><{$item.fCreator}>(<{$item.fApplyTime}>)</td>
    <th>審核</th>
    <td colspan="2">
        <{if $item.fAuditor != ''}>
            <{$item.fAuditor}>(<{$item.fAuditorTime}>)
        <{/if}>       
    </td> 
</tr>

</table>
<br>
<{/foreach}>