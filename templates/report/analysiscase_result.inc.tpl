<script type="text/javascript" src="/js/jquery-easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/js/jquery-easyui/locale/easyui-lang-zh_TW.js"></script>


<div class="easyui-tabs" >
    <div title="總表" style="padding:10px">
        <!-- <h2><{$item.name}></h2> -->
                <table cellpadding="0" cellspacing="0" width="100%" class="tb"> 
                   
                    <tr>
                        <th>&nbsp;</th>
                        <th>年度</th>
                        <th>案件總筆數</th>
                        <th>買賣總價金額</th>
                        <th>合約總保證費金額</th>
                        <th>回饋總金額</th>
                        <th>收入</th> 
                    </tr>


                   
        <{foreach from=$title key=key item=item}>
                     <!-- <tr>
                        <td colspan="6" style="background-color: #FFB01C"><{$item.name}></td>
                    </tr>
             -->
                    <{foreach from=$data key=date item=item2}>
                    <tr>
                        <td><{$item.name}></td>
                        <td><{$date}></td>
                        <td><{$item2[$item.id].count|number_format}></td>
                        <td><{$item2[$item.id].totalMoney|number_format}></td>
                        <td><{$item2[$item.id].certifiedMoney|number_format}></td>
                        <td><{$item2[$item.id].feedbackMoney|number_format}></td>
                        <td><{($item2[$item.id].certifiedMoney-$item2[$item.id].feedbackMoney)|number_format}></td> 
                    </tr>
                   
                     <{/foreach}>
                
           
        <{/foreach}>

        <tr>
            <td colspan="2">總計</td>
            <td><{$totalCaseCount|number_format}></td>
            <td><{$totalCaseMoney|number_format}></td>
            <td><{$totalCertifiedMoney|number_format}></td>
            <td><{$totalFeedBackMoney|number_format}></td>
            <td><{($totalCertifiedMoney-$totalFeedBackMoney)|number_format}></td> 

        </tr>
        </table>
    </div>
    <{foreach from=$title key=key item=item}>
        <div title="<{$item.name}>" style="padding:10px">
            <h2><{$item.name}></h2>
            <table cellpadding="0" cellspacing="0" width="100%" class="tb"> 
               
                <tr>
                    <th>年度</th>
                    <th>案件總筆數</th>
                    <th>買賣總價金額</th>
                    <th>合約總保證費金額</th>
                    <th>回饋總金額</th>
                    <th>收入</th> 
                </tr>
                <{foreach from=$data key=date item=item2}>
                <tr>
                    <td><{$date}></td>
                    <td><{$item2[$item.id].count|number_format}></td>
                    <td><{$item2[$item.id].totalMoney|number_format}></td>
                    <td><{$item2[$item.id].certifiedMoney|number_format}></td>
                    <td><{$item2[$item.id].feedbackMoney|number_format}></td>
                    <td><{($item2[$item.id].certifiedMoney-$originalDataTotal.feedbackMoney)|number_format}></td> 
                </tr>
               
                 <{/foreach}>
            </table>
        </div>
    <{/foreach}>
</div>


    <!-- <div class="block">
        <table cellpadding="0" cellspacing="0" width="100%" class="tb2"> 
        <tr>
            <th colspan="6">案件統計表</th>
        </tr>
        <tr>
            <th>年度</th>
            <th>案件總筆數</th>
            <th>買賣總價金額</th>
            <th>合約總保證費金額</th>
            <th>回饋總金額</th>
            <th>收入</th> 
        </tr>
        <tr>
            <td><{$year}></td>
            <td><{$originalDataTotal.count|number_format}></td>
            <td><{$originalDataTotal.totalMoney|number_format}></td>
            <td><{$originalDataTotal.certifiedMoney|number_format}></td>
            <td><{$originalDataTotal.feedbackMoney|number_format}></td>
            <td><{($originalDataTotal.certifiedMoney-$originalDataTotal.feedbackMoney)|number_format}></td> 
        </tr>
        <tr>
            <td><{$year2}></td>
            <td><{$originalDataTotal1.count|number_format}></td>
            <td><{$originalDataTotal1.totalMoney|number_format}></td>
            <td><{$originalDataTotal1.certifiedMoney|number_format}></td>
            <td><{$originalDataTotal1.feedbackMoney|number_format}></td>
            <td><{($originalDataTotal1.certifiedMoney-$originalDataTotal1.feedbackMoney)|number_format}></td> 
        </tr>
        </table>
    </div>
 -->