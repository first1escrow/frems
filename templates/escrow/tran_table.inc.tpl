<table  width="100%"  class="detail_row" cellspacing="0">
    <tr>
        <td colspan="7" class="tb-title">帳務收支明細 <a href="javascript:open_confirm_call('<{$data_case.cCertifiedId}>','')" style="font-size:9pt;">(照會)</a></td>
    </tr>
    <tr>
        <th style="width:110px;" >日期</th>
        <th style="width:150px;">帳款摘要</th>
        <th style="width:110px;">收入</th>
        <th style="width:90px;">支出</th>
        <th style="width:90px;">小計</th>
        <th style="width:170px;" >備註</th>
        <!-- <th style="width:50px;" >不顯示<br>在官網</th> -->
                                              
    </tr>
    <{$tbl}>
    <tr >
        <th style="width:110px;" colspan="6">&nbsp;</th>
                                              
    </tr>
    <tr style="background-color:#FFFFFF;">
        <td>&nbsp;</td>
        <td style="text-align:right;">專戶收支餘額：</td>
         <td colspan="3" style="text-align:right;"><{$total}>&nbsp;</td>
        <td colspan="2">(收入-支出)&nbsp;</td>
    </tr>
    
</table>