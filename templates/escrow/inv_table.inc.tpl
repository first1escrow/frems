<div style="border:1px solid #E8EcE9;"> 
    <table width="100%"  cellpadding="1" cellspacing="1" >
        <tr >
            <td colspan="5" bgcolor="#E8C1B4"> 賣方 NT$<span id="invoice_invoiceowner" class="currency-money1 text-right"><{$data_invoice.cInvoiceOwner}></span>元 
                <input type="hidden" name="invoice_invoiceowner" value="<{$data_invoice.cInvoiceOwner}>">
            </td>
        </tr>
        <tr>
            <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
            <td width="15%" class="tb-title2 th_title_sml">金額</td>
            <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
            <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
            <td align="left" class="tb-title2 th_title_sml">指定對象</td>
        </tr>
        <tr class="th_title_sml">
            <td ><{$data_owner.cName}> <{$data_owner.cIdentifyId}></td>
            <td>
                NT$<span id="inv_show_owner1" class="currency-money1 text-right"><{$data_owner.cInvoiceMoney}></span>元
                <input type="checkbox" class="show_owner1_check" data-identifyid="<{$data_owner.cIdentifyId}>" data-money="inv" <{if $data_owner.cInvoiceMoneyCheck == 1 }>checked <{/if}> >
                <input type="hidden" class="inv_show_owner" name="inv_show_owner1" value="<{$data_owner.cInvoiceMoney}>">
            </td>
            <td>
                <span id="owner_invdonate1">
                <{if $data_owner.cInvoiceDonate==1}>
                    <font color="red">[捐贈]</font>
                <{/if}>
                </span>
            </td>
            <td>
                <{if $data_owner.cInvoicePrint=="Y"}>
                    <font color="red">[列印]</font>
                <{/if}>
            </td>
            <td>
                <{assign var='j' value=$owner_other_count}>

                <{foreach from=$data_invoice_another key=key item=item}>

                <{if $item.cDBName =='tContractOwner'}>
                        <{$item.cName}>
                    NT$<span id="inv_show_owner<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元

                    <input type="hidden" class="inv_show_owner" name="inv_show_owner<{$j}>" value="<{$item.cInvoiceMoney}>">
                    <span id="owner_invdonate<{$j++}>">
                        <font color="red"><{$item.cInvoiceDonate}></font>
                    </span>
                    ;
                <{/if}>
                <{/foreach}>
            </td>
        </tr>
            <{assign var='i' value='2'}>
            <{foreach from=$data_owner_other key=key item=item}>
            <{assign var='id' value=$item.cId}>
            <tr>
                <td><{$item.cName}> <{$item.cIdentifyId}></td>
                <td>
                    NT$<span id="inv_show_owner<{$i}>" class="currency-money1 text-right">NT$<{$item.cInvoiceMoney}></span>元
                    <input type="checkbox"  class="show_owner_check" data-identifyid="<{$item.cIdentifyId}>" data-money="inv" <{if $item.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
                    <input type="hidden" class="inv_show_owner" name="inv_show_owner<{$i}>" value="<{$item.cInvoiceMoney}>">
                </td>
                <td> <span id="owner_invdonate<{$i++}>">
                    <{if $item.cInvoiceDonate==1}>
                        <font color="red">[捐贈]</font>
                    <{/if}>
                    </span>
                </td>
                <td>
                    <{if $item.cInvoicePrint=="Y"}>
                        <font color="red">[列印]</font>
                    <{/if}>
                </td>
                <td>
                    <{foreach from=$data_invoice_another key=key item=item}>

                    <{if $item.cDBName =='tContractOthersO' && $item.cTBId ==$id}>
                            <{$item.cName}>
                        NT$<span id="inv_show_owner<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                    <input type="hidden" class="inv_show_owner" name="inv_show_owner<{$j}>" value="<{$item.cInvoiceMoney}>">
                    <span id="owner_invdonate<{$j++}>">
                        <font color="red"><{$item.cInvoiceDonate}></font>
                    </span>
                    ;
                <{/if}>
                <{/foreach}>
            </td>
        </tr>
        <{/foreach}>
    </table>
</div>
<div style="border:1px solid #E8EcE9;">
    <table width="100%"  cellpadding="1" cellspacing="1" >
        <tr >
            <td colspan="5" bgcolor="#E8C1B4"> 買方 NT$<span id="invoice_invoicebuyer" class="currency-money1 text-right"><{$data_invoice.cInvoiceBuyer}></span>元
        <input type="hidden" name="invoice_invoicebuyer" value="<{$data_invoice.cInvoiceBuyer}>">
            </td>
        </tr>
        <tr>
            <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
            <td width="15%" class="tb-title2 th_title_sml">金額</td>
            <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
            <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
            <td align="left" class="tb-title2 th_title_sml">指定對象</td>
        </tr>
        <tr class="th_title_sml">
            <td ><{$data_buyer.cName}> <{$data_buyer.cIdentifyId}></td>
            <td>
                NT$<span id="inv_show_buyer1" class="currency-money1 text-right"><{$data_buyer.cInvoiceMoney}></span>元
                <input type="checkbox" class="show_buyer1_check" data-identifyid="<{$data_buyer.cIdentifyId}>"  data-money="inv" <{if $data_buyer.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
                <input type="hidden" class="inv_show_buyer" name="inv_show_buyer1" value="<{$data_buyer.cInvoiceMoney}>">
            </td>
            <td>
                <span id="buyer_invdonate1">
                <{if $data_buyer.cInvoiceDonate==1}>
                    <font color="red">[捐贈]</font>
                <{/if}>
                                                            
                </span>
            </td>
            <td>
                    <{if $data_buyer.cInvoicePrint=="Y"}>
                        <font color="red">[列印]</font>
                    <{/if}>
            </td>
            <td>
                <{assign var='j' value=$buyer_other_count}>

                <{foreach from=$data_invoice_another key=key item=item}>

                <{if $item.cDBName =='tContractBuyer'}>
                        <{$item.cName}>
                    NT$<span id="inv_show_buyer<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                    <input type="hidden" class="inv_show_buyer" name="inv_show_buyer<{$j}>" value="<{$item.cInvoiceMoney}>">
                    <span id="buyer_invdonate<{$j++}>">
                        <font color="red"><{$item.cInvoiceDonate}></font>
                    </span>
                    ;
                <{/if}>
                <{/foreach}>
            </td>
        </tr>
        <{assign var='i' value='2'}>
        <{foreach from=$data_buyer_other key=key item=item}>
        <{assign var='id' value=$item.cId}>
        <tr>
            <td><{$item.cName}> <{$item.cIdentifyId}></td>
            <td>
                NT$<span id="inv_show_buyer<{$i}>"  class="currency-money1 text-right">$<{$item.cInvoiceMoney}></span>元
                <input type="checkbox"  class="show_buyer_check" data-identifyid="<{$item.cIdentifyId}>" data-money="inv" <{if $item.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
                <input type="hidden" class="inv_show_buyer" name="inv_show_buyer<{$i}>" value="<{$item.cInvoiceMoney}>">
            </td>
            <td> <span id="buyer_invdonate<{$i++}>">
                <{if $item.cInvoiceDonate==1}>
                    <font color="red">[捐贈]</font>
                <{/if}>
                </span>
            </td>
            <td>
                <{if $item.cInvoicePrint=="Y"}>
                    <font color="red">[列印]</font>
                <{/if}>
            </td>
            <td>
                <{foreach from=$data_invoice_another key=key item=item}>

                <{if $item.cDBName =='tContractOthersB' && $item.cTBId ==$id}>
                        <{$item.cName}>
                    NT$<span id="inv_show_buyer<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                    <input type="hidden" class="inv_show_buyer" name="inv_show_buyer<{$j}>" value="<{$item.cInvoiceMoney}>">
                    <span id="buyer_invdonate<{$j++}>">
                        <font color="red"><{$item.cInvoiceDonate}></font>
                    </span>
                    ;
                <{/if}>
                <{/foreach}>
            </td>
        </tr>
        <{/foreach}>
    </table>
</div>
<div style="border:1px solid #E8EcE9;">
    <table width="100%"  cellpadding="1" cellspacing="1" >
        <tr >
            <td colspan="5" bgcolor="#E8C1B4"> 仲介 NT$<span id="invoice_invoicerealestate" class="currency-money1 text-right"><{$data_invoice.cInvoiceRealestate}></span>元
                <input type="hidden" name="invoice_invoicerealestate" value="<{$data_invoice.cInvoiceRealestate}>">
            </td>
        </tr>
        <tr>
                <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
                <td width="15%" class="tb-title2 th_title_sml">金額</td>
                <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
                <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
                <td align="left" class="tb-title2 th_title_sml">指定對象</td>
        </tr>
        <{assign var='i' value='1'}>
        <tr class="th_title_sml">
            <td ><{$branch_type1}></td>
            <td>
                NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney}></span>元
                <input type="checkbox"  class="show_branch_check" data-no="" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
                <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney}>">
            </td>
            <td>
                <span id="real_invdonate<{$i++}>">
                <{if $data_realstate.cInvoiceDonate==1}>
                        <font color="red">[捐贈]</font>
                <{/if}>
                </span>
            </td>
            <td>
                <{if $data_realstate.cInvoicePrint=="Y"}>
                    <font color="red">[列印]</font>
                <{/if}>
            </td>
            <td>
                <{assign var='j' value=$branch_count}>
                <{foreach from=$data_invoice_another key=key item=item}>

                    <{if $item.cDBName =='tContractRealestate'}>
                            <{$item.cName}>
                        NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                        <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                        <span id="real_invdonate<{$j++}>">
                            <font color="red"><{$item.cInvoiceDonate}></font>
                        </span>
                                                                        ;
                    <{/if}>
                <{/foreach}>                                                   
            </td>
        </tr>
        <{if $branch_type2 !=''}>
        <tr class="th_title_sml">
            <td ><{$branch_type2}></td>
            <td>
                NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney1}></span>元
                <input type="checkbox"  class="show_branch_check" data-no="1" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck1 == 1 }>checked <{/if}>>
                <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney1}>">
            </td>
            <td>
                <span id="real_invdonate<{$i++}>">
                    <{if $data_realstate.cInvoiceDonate1==1}>
                        <font color="red">[捐贈]</font>
                    <{/if}>
                </span>
            </td>
            <td>
                <{if $data_realstate.cInvoicePrint1=="Y"}>
                    <font color="red">[列印]</font>
                <{/if}>
            </td>
            <td>
                    <{foreach from=$data_invoice_another key=key item=item}>

                    <{if $item.cDBName =='tContractRealestate1'}>
                            <{$item.cName}>
                        NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                        <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                        <span id="real_invdonate<{$j++}>">
                            <font color="red"><{$item.cInvoiceDonate}></font>
                        </span>
                        ;
                    <{/if}>
                <{/foreach}>                                                        
            </td>
        </tr>
        <{/if}>
        <{if $branch_type3 !=''}>
        <tr class="th_title_sml">
            <td ><{$branch_type3}></td>
            <td>
                NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney2}></span>元
                <input type="checkbox"  class="show_branch_check" data-no="2" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck2 == 1 }>checked <{/if}>>
                <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney2}>">
            </td>
            <td>
                <span id="real_invdonate<{$i++}>">
                    <{if $data_realstate.cInvoiceDonate2==1}>
                        <font color="red">[捐贈]</font>
                    <{/if}>
                </span>
           
            <td>
                <{if $data_realstate.cInvoicePrint2=="Y"}>
                    <font color="red">[列印]</font>
                <{/if}>
            </td>
            <td>
                <{foreach from=$data_invoice_another key=key item=item}>

                    <{if $item.cDBName =='tContractRealestate2'}>
                            <{$item.cName}>
                        NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                        <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                        <span id="real_invdonate<{$j++}>">
                            <font color="red"><{$item.cInvoiceDonate}></font>
                        </span>
                        ;
                    <{/if}>
                <{/foreach}>
            </td>
        </tr>
        <{/if}>
        <{if $branch_type4 !=''}>
        <tr class="th_title_sml">
            <td ><{$branch_type4}></td>
            <td>
                NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney3}></span>元
                <input type="checkbox"  class="show_branch_check" data-no="3" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck3 == 1 }>checked <{/if}>>
                <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney3}>">
            </td>
            <td>
               <span id="real_invdonate<{$i++}>">
                    <{if $data_realstate.cInvoiceDonate3==1}>
                    <font color="red">[捐贈]</font>
                    <{/if}>
                </span>
            </td>
            <td>
                <{if $data_realstate.cInvoicePrint3=="Y"}>
                <font color="red">[列印]</font>
                <{/if}>
            </td>
            <td>
                <{foreach from=$data_invoice_another key=key item=item}>
                <{if $item.cDBName =='tContractRealestate3'}>
                <{$item.cName}>
                NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                <span id="real_invdonate<{$j++}>">
                    <font color="red"><{$item.cInvoiceDonate}></font>
                    <{if $item.cInvoicePrint=="Y"}>
                    <font color="red">[列印]</font>
                    <{/if}>
                </span>
                ;
                <{/if}>
                <{/foreach}>
            </td>
        </tr>
        <{/if}>
    </table>
</div>
<div style="border:1px solid #E8EcE9;">
    <table width="100%"  cellpadding="1" cellspacing="1" >
    <tr >
        <td colspan="5" bgcolor="#E8C1B4"> 地政士 NT$<span id="invoice_invoicescrivener" class="currency-money1 text-right"><{$data_invoice.cInvoiceScrivener}></span>元
            <input type="hidden"  name="invoice_invoicescrivener" value="<{$data_invoice.cInvoiceScrivener}>">
        </td>
    </tr>
    <tr>
            <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
            <td width="15%" class="tb-title2 th_title_sml">金額</td>
            <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
            <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
            <td align="left" class="tb-title2 th_title_sml">指定對象</td>
    </tr>
    <tr class="th_title_sml">
        <td ><{$data_scrivener.sName}></td>
        <td>
            NT$<span id="inv_show_scrivener1" class="currency-money1 text-right"><{$data_scrivener.cInvoiceMoney}></span>元
            <input type="checkbox"  class="show_scrivener1" data-money="inv" <{if $data_scrivener.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
            <input type="hidden" class="inv_show_scrivener" name="inv_show_scrivener1" value="<{$data_scrivener.cInvoiceMoney}>">

        </td>
        <td>
            <span id="scr_invdonate1">
                <{if $data_scrivener.cInvoiceDonate==1}>
                    <font color="red">[捐贈]</font>
                <{/if}>
            </span>
        </td>
        <td>
                <{if $data_scrivener.cInvoicePrint=="Y"}>
                    <font color="red">[列印]</font>
                <{/if}>
        </td>
        <td>
            <{assign var='j' value='2'}>
            <{foreach from=$data_invoice_another key=key item=item}>

                <{if $item.cDBName =='tContractScrivener'}>
                        <{$item.cName}>
                    NT$<span id="inv_show_scrivener<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                    <input type="hidden" class="inv_show_scrivener" name="inv_show_scrivener<{$j}>" value="<{$item.cInvoiceMoney}>">
                    <span id="scr_invdonate<{$j++}>">
                        <font color="red"><{$item.cInvoiceDonate}></font>
                    </span>
                    ;
                <{/if}>
            <{/foreach}>                                                     
        </td>
    </tr>
    </table>
</div>
<{if $data_invoice.cInvoiceOther !=0}>
<div>
    捐創世基金會
    NT$<span id="invoice_invoiceother" class="currency-money1 text-right"><{$data_invoice.cInvoiceOther}></span>元
    <input type="hidden" name="invoice_invoiceother" value="<{$data_invoice.cInvoiceOther}>">
</div>
<{/if}>
