<style>
.tb{
    text-align: center;
    border: solid 1px #ccc;

}
.tb th{
    width:300px;
    background-color:#E4BEB1;
    padding:4px;
            /**/

}
.col1{
	background-color:#E4BEB1;
    padding:4px;
    font-size: 20px;
    font-family:"微軟正黑體";
    font-weight:bold;
}
.col2{
	border: 1px solid;
	text-align: left;
	padding: 5px;
}
.title{
	font-size: 25px;
	color: red;
	padding-bottom: 15px;
	font-family:"微軟正黑體";
	font-weight:bold;
}

</style>
<table class="tb" width="100%">
	<tr>
		<td colspan="2" align="center" class="col1">已更改的保證號碼</td>
	</tr>
	<tr>
		<th width="30%">保證號碼</th>
		<th>仲介店</th>
	</tr>
	<{foreach from=$arr key=key item=item}>
	<tr>
		<td class="col2"><{$item.cCertifiedId}></td>
		<td class="col2">(<{$item.bCode}>)<{$item.Brand}><{$item.bStore}></td>
	</tr>
	<{/foreach}>

</table>

<{if $countfail > 0}>
	<br>
	<table class="tb" width="100%">
	<tr>
		<td colspan="2" align="center" class="col1">更改失敗的保證號碼</td>
	</tr>
	<tr>
		<th width="30%">保證號碼</th>
		<th>仲介店</th>
	</tr>
	<{foreach from=$arr2 key=key item=item}>
	<tr>
		<td class="col2"><{$item.cCertifiedId}></td>
		<td class="col2">(<{$item.bCode}>)<{$item.Brand}><{$item.bStore}></td>
	</tr>
	<{/foreach}>

</table>
<{/if}>
<br>
<div class="title">※配件請手動更改</div>
<table class="tb" width="100%">
	<tr>
		<td colspan="2" align="center" class="col1">配件</td>
	</tr>
	<tr>
		<th width="30%">保證號碼</th>
		<th>仲介店</th>
	</tr>
	<{foreach from=$pair key=key item=item}>
	<tr>
		<td class="col2"><{$item.cCertifiedId}></td>
		<td class="col2"><{$item.allBranch}></td>
	</tr>
	<{/foreach}>
</table>