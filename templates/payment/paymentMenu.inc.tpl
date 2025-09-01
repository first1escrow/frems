<style>
    .tab{
        background: #CCC;
    }
    .tab-focus{
        background: orange;
        text-decoration:none;
    }
</style>
<div class="tab">
    <{if $cat == 2}>
        <a href="paymentListTrans.php?cat=2" class="tab-focus">未審核列表</a>
    <{else}>
        <a href="paymentListTrans.php?cat=2" class="">未審核列表</a>
    <{/if}>

    <{if $cat == 1}>
        <a href="paymentListTrans.php?cat=1" class="tab-focus">已審核列表</a>
    <{else}>
        <a href="paymentListTrans.php?cat=1" class="">已審核列表</a>
    <{/if}>

    <{if $cat == 3}>
        <a href="paymentListCheck.php?cat=3" class="tab-focus">銀行出款確認</a>
    <{else}>
        <a href="paymentListCheck.php?cat=3" class="">銀行出款確認</a>
    <{/if}>
            
           
        
</div>