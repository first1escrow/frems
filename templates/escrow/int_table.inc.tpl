<table width="100%"  cellpadding="1" cellspacing="1" >
                                                <tr>
                                                    <td colspan="3"><div id="int_tag"><{$int_total}></div></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" bgcolor="#E8C1B4">賣方</td>
                                                </tr>
                                                <tr>
                                                    <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                    <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                    <td class="tb-title2 th_title_sml">指定對象</td>
                                                </tr>
                                                <tr class="th_title_sml">
                                                    <td><{$data_owner.cName}> <{$data_owner.cIdentifyId}></td>
                                                    <td>
                                                        NT$<span id="int_show_owner0" class="currency-money1 text-right"><{$data_owner.cInterestMoney}></span>元
                                                        <input type="checkbox"  class="show_owner1_check" data-identifyid="<{$data_owner.cIdentifyId}>" data-money="int" <{if $data_owner.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                        <{foreach from=$data_int_another key=key item=item}>
                                                            <{if $item.cDBName =='tContractOwner'}>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>
                                                </tr>
                                                 <{foreach from=$data_owner_other key=key item=item}>
                                                 <{assign var='id' value=$item.cId}>
                                                 <tr class="th_title_sml">
                                                    <td><{$item.cName}> <{$item.cIdentifyId}></td>
                                                    <td>
                                                        NT$<span id="int_show_owner<{$i++}>" class="currency-money1 text-right">NT$<{$item.cInterestMoney}></span>元
                                                        <input type="checkbox"  class="show_owner_check" data-identifyid="<{$item.cIdentifyId}>" data-money="int" <{if $item.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                        <{foreach from=$data_int_another key=key item=item}>
                                                            <{if $item.cDBName =='tContractOthersO' && $item.cTBId ==$id}>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>  
                                                </tr>                 
                                                <{/foreach}>

                                                <tr>
                                                    <td colspan="3" bgcolor="#E8C1B4">買方</td>
                                                </tr>
                                                <tr>
                                                    <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                    <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                    <td class="tb-title2 th_title_sml">指定對象</td>
                                                </tr>
                                                <tr class="th_title_sml">
                                                    <td><{$data_buyer.cName}> <{$data_buyer.cIdentifyId}></td>
                                                    <td>NT$<span id="int_show_owner0" class="currency-money1 text-right"><{$data_buyer.cInterestMoney}></span>元
                                                        <input type="checkbox"  class="show_buyer1_check" data-identifyid="<{$data_buyer.cIdentifyId}>" data-money="int" data-money="int" <{if $data_buyer.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                        <{foreach from=$data_int_another key=key item=item}>
                                                            
                                                            <{if $item.cDBName =='tContractBuyer' }>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>
                                                </tr>
                                                 <{foreach from=$data_buyer_other key=key item=item}>
                                                 <{assign var='id' value=$item.cId}>
                                                 <tr class="th_title_sml">
                                                    <td><{$item.cName}> <{$item.cIdentifyId}></td>
                                                    <td>
                                                        NT$<span id="int_show_owner<{$i++}>" class="currency-money1 text-right">NT$<{$item.cInterestMoney}></span>元
                                                        <input type="checkbox"  class="show_buyer_check" data-identifyid="<{$item.cIdentifyId}>" data-money="int" <{if $item.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                        <{foreach from=$data_int_another key=key item=item}>
                                                            
                                                            <{if $item.cDBName =='tContractOthersB' && $item.cTBId ==$id}>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>  
                                                </tr>                 
                                                <{/foreach}>
                                                <tr>
                                                    <td colspan="3" bgcolor="#E8C1B4">仲介</td>
                                                </tr>
                                                <tr>
                                                    <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                    <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                    <td class="tb-title2 th_title_sml">指定對象</td>
                                                </tr>
                                                <tr class="th_title_sml">
                                                    <td><{$branch_type1}></td>
                                                    <td>
                                                        NT$<span id="int_show_branch1" class="currency-money1 text-right"><{$data_realstate.cInterestMoney}></span>元
                                                        <input type="checkbox"  class="show_branch_check" data-no="" data-money="int" <{if $data_realstate.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                       <{foreach from=$data_int_another key=key item=item}>
                                                            <{if $item.cDBName =='tContractRealestate'}>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>
                                                </tr>
                                                <{if $branch_type2 !=''}>
                                                <tr class="th_title_sml">
                                                    <td><{$branch_type2}></td>
                                                    <td>
                                                        NT$<span id="int_show_branch2" class="currency-money1 text-right"><{$data_realstate.cInterestMoney1}></span>元
                                                        <input type="checkbox"  class="show_branch_check" data-no="1" data-money="int" <{if $data_realstate.cInterestMoneyCheck1 == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                       <{foreach from=$data_int_another key=key item=item}>
                                                            <{if $item.cDBName =='tContractRealestate1'}>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>
                                                </tr>
                                                <{/if}> 

                                                <{if $branch_type3 !=''}>
                                                <tr class="th_title_sml">
                                                    <td><{$branch_type3}></td>
                                                    <td>
                                                        NT$<span id="int_show_branch2" class="currency-money1 text-right"><{$data_realstate.cInterestMoney2}></span>元
                                                        <input type="checkbox"  class="show_branch_check" data-no="2" data-money="int" <{if $data_realstate.cInterestMoneyCheck2 == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                       <{foreach from=$data_int_another key=key item=item}>
                                                            <{if $item.cDBName =='tContractRealestate2'}>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>
                                                </tr>
                                                <{/if}>
                                                <{if $branch_type4 !=''}>
                                                <tr class="th_title_sml">
                                                    <td><{$branch_type4}></td>
                                                    <td>
                                                        NT$<span id="int_show_branch2" class="currency-money1 text-right"><{$data_realstate.cInterestMoney3}></span>元
                                                        <input type="checkbox"  class="show_branch_check" data-no="3" data-money="int" <{if $data_realstate.cInterestMoneyCheck3 == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                        <{foreach from=$data_int_another key=key item=item}>
                                                        <{if $item.cDBName =='tContractRealestate3'}>
                                                        <{$item.cName}>
                                                        NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                        <{/if}>
                                                        <{/foreach}>
                                                    </td>
                                                </tr>
                                                <{/if}>
                                                <tr>
                                                    <td colspan="3" bgcolor="#E8C1B4">地政士</td>
                                                </tr>
                                                <tr>
                                                    <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                    <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                    <td class="tb-title2 th_title_sml">指定對象</td>
                                                </tr> 
                                                <tr class="th_title_sml">
                                                    <td><{$data_scrivener.sName}></td>
                                                    <td>
                                                        NT$<span id="int_show_scrivener" class="currency-money1 text-right"><{$data_scrivener.cInterestMoney}></span>元
                                                        <input type="checkbox"  class="show_scrivener1" data-money="int" <{if $data_scrivener.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                    </td>
                                                    <td>
                                                        <{foreach from=$data_int_another key=key item=item}>
                                                            <{if $item.cDBName =='tContractScrivener'}>
                                                                <{$item.cName}> 
                                                                NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                            <{/if}>
                                                        <{/foreach}>
                                                    </td>
                                                </tr>

                                            </table>