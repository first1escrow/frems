<table cellspacing="0" cellpadding="0" border="0" class="tb" width="100%">
	<tr>
		<th>品牌</th>
		<th>店家名稱</th>
		<th>公司名稱</th>
		<th>地址</th>
		<!-- <th>連絡電話</th> -->
	</tr>
	<{foreach from=$list key=key item=item}>
	<tr>
		<td><{$item.brandName}></td>
		<td><{$item.sname}></td>
		<td><{$item.scompany}></td>
		<td><{$item.city}><{$item.area}><{$item.addr}></td>
		<!-- <td><{$item.tel}></td> -->
	</tr>
	<{/foreach}>
</table>