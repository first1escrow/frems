<div style="margin-bottom: 5px;float: right;">
    <input type="button" value="儲存" name="save" onclick="saveProcess()">
</div>
<{if $template == 0}>
<table cellspacing="0" cellpadding="0" border="0" class="tb">
        <tr>
            <th width="15%">地政士</th>
            <th width="12%">品牌仲介類別</th>
           
            <th width="8%">類別</th>
            <th width="5%">份數</th>
            <th width="8%">申請人</th>
            <th width="10%">申請日</th>
            <th width="10%">製作中(人)</th>
            <th width="10%">出貨日</th>
            
            <th width="20%">備註</th>
            
        </tr>
        <{foreach from=$list key=key item=item}>
        <tr>
            <td><{$item.scrivener}></td>
            <td><{$item.bShowBrand}></td>
            
            <td><{$item.bApplication}></td>
            <td><{$item.bCount}></td>
            <td><{$item.Applicant}></td>
            <td><{$item.bDate}></td>
            <td>
                <input type="hidden" name="id[]" value="<{$item.bId}>">
                <{html_options name="producer[]" options=$menuPeople selected=$item.bProducer }>    
            </td>
           
            
            <td><input type="text" name="shipdate[]" value="<{$item.bShipDate}>" class="datepickerROC2" style="width: 80px;"></td>
            <td><input type="text" name="note[]" id="" value="<{$item.bNote}>" style></td>
            
        </tr>
        <{/foreach}>
</table>
<{else}>
<table cellspacing="0" cellpadding="0" border="0" class="tb">
        <tr>
            <th width="12%">地政士</th>
            <th width="10%">編號</th>
            <th width="10%">銀行</th>
            <th width="5%">份數</th>
            <th width="10%">申請人</th>
            <th width="15%">申請日</th>
            <th width="10%">製作中(人)</th>
            <th width="18%">出貨日</th>
            <th width="10%">備註</th>
            
        </tr>
        <{foreach from=$list key=key item=item}>
        <{if $item.bShipDate != '000-00-00' && $item.bShipDate != ''}>
            <{assign var='disabled' value='disabled=disabled'}> 
            <{assign var='color' value='#CCCCCC'}>
        <{else}>
            <{assign var='disabled' value=''}> 
            <{assign var='color' value='#FFFFFF'}>
        <{/if}>
        <tr >
            <td style="background-color: <{$color}>"><span title="<{$item.code}>"><{$item.scrivener}></span></td>
            <td style="background-color: <{$color}>"><a href="#" onclick="showApplyFrom('<{$item.bNo}>')"><{$item.bNo}></a></td>
            <td style="background-color: <{$color}>"><{$item.bankName}></td>
            <td style="background-color: <{$color}>"><{$item.bCount}></td>
            <td style="background-color: <{$color}>"><{$item.Applicant}></td>
            <td style="background-color: <{$color}>"><{$item.bDate}></td>
            <td style="background-color: <{$color}>">
                <input type="hidden" name="id[]" value="<{$item.bId}>">
                <{html_options name="producer[]" options=$menuPeople selected=$item.bProducer }>    
            </td>
           
            
            <td style="text-align: center;background-color: <{$color}>">
                <input type="checkbox" onclick="" name="ck[]" <{$item.bUrgentChecked}>>急件
                <input type="text" name="urgentdate[]" id="" value="<{$item.bUrgentDate}>" class="datepickerROC2"  style="width: 80px;">
                
                <div style="margin-bottom: 5px;margin-top: 5px;"><hr></div>
                

                <input type="text" name="shipdate[]" value="<{$item.bShipDate}>" class="datepickerROC2" style="width: 80px;"  >
            </td>
            <td style="background-color: <{$color}>">
                <textarea name="note[]" id="" cols="20" rows="5"><{$item.bNote}></textarea>
            </td>
               
            
        </tr>
        <{/foreach}>
</table>
<{/if}>


<script type="text/javascript">
$(document).ready(function() {
   $( ".datepickerROC2" ).datepicker({
        yearRange: '1912:'+((new Date).getFullYear()+10),
        //beforeShowDay: "2015-12-17",
        yearSuffix: "", //將年改為空白
        changeYear: true, //手動修改年
        changeMonth: true, //手動修改月
        //showWeek: true, //顯示第幾周
        //firstDay: 1, //0為星期天
        showOtherMonths: true, //在本月中顯示其他月份
        selectOtherMonths: true, //可以在本月中選擇其他月份
        //showButtonPanel: true, //顯示bottom bar
        //closeText: '清除', //將離開改為清除
        //appendText: "yyy-mm-dd",
        dateFormat: "yy-mm-dd",
        onSelect: function (dateText, inst) {
            var dateFormate = inst.settings.dateFormat == null ? "yy/mm/dd" : inst.settings.dateFormat; //取出格式文字
            var reM = /m+/g;
            var reD = /d+/g;
            var objDate = { y: inst.selectedYear - 1911 < 0 ? inst.selectedYear : inst.selectedYear - 1911,
                m: String(inst.selectedMonth+1).length != 1 ? inst.selectedMonth + 1 : "0" + String(inst.selectedMonth + 1),
                d: String(inst.selectedDay).length != 1 ? inst.selectedDay : "0" + String(inst.selectedDay)
            };
            $.each(objDate, function (k, v) {
                var re = new RegExp(k + "+");
                dateFormate = dateFormate.replace(re, v);
            });
            inst.input.val(dateFormate);
        }
    });
});

    
</script>