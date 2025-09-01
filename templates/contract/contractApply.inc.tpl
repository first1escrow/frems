<div class="contract-fixBlock">
	黃色為固定格
</div>
<div class="contract-fixBlock1 category1">
  <div style="float: left;padding-right: 30px;">立約人</div>
  <div style="float: left;">買方：</div><div style="float: left;border-bottom: 1px solid black;width: 20%;">&nbsp;</div>
  <div style="clear:both;"></div>
  <div style="float: left;padding-left: 75px;">賣方：</div><div style="float: left;border-bottom: 1px solid black;width: 20%;">&nbsp;</div>
  <div style="clear:both;"></div>
  <div style="float: left;padding-left: 75px;">經紀業：</div><div style="float: left;border-bottom: 1px solid black;width: 20%;">&nbsp;</div>
   <div style="clear:both;"></div>
</div>
<div class="contract-fixBlock fix4" id="fix4" style="display: none">
  <div style="">第四條:買賣價金存滙及專戶帳號</div>
  <div style="margin-left: 50px;">甲方應給付之不動產買賣價金，除第一期簽約款得以現金或即期支票給付（受款人為「XXX商業銀行收托信託財產專戶」並記載禁止背書轉讓）委由OOO存滙入履保專戶外，其餘各期款項應由甲方自行匯款（下列帳號）不得委由第三人代為滙款，違反約定因而造成甲方受有損失者，不在第一建經保證責任範圍。</div>
  <div style="margin-left: 50px;float: left;">銀行別:XXXX銀行OO分行</div> <div style="margin-left: 30px;float: left;">戶名:XXX商業銀行收托信託財產專戶</div>
  <div style="clear:both;"></div>
  <div style="float: left;margin-left: 50px;">帳號:</div><div style="float: left;border-bottom: 1px solid black;width: 20%;">&nbsp;</div>(末九碼為保證碼)
   <div style="clear:both;"></div>
   <div style="">第五條:地政士委任</div>
   <div style="margin-left: 50px;">甲乙雙方同意委由地政士（以下簡稱特約地政士）辦理所有權移轉、抵押權設定、塗銷登記、稅費繳交、協助辦理代償及其他相關事項。</div>
   <div style="clear:both;"></div>
</div>

<div class="contract-fixBlock fix9_1" id="fix9_1" style="display: none">
  <div style="">一、有關專戶價金之結算與撥付，由第一建經依三方履約結果進行認定，並指示XXX商業銀行作為撥付下列款項之依據：</div>
  
</div>

<div class="contract-fixBlock category2" style="display: none">
  <div style="float: left;padding-right: 30px;">立約人</div>
  <div style="float: left;">買方：</div><div style="float: left;border-bottom: 1px solid black;width: 20%;">&nbsp;</div>
  <div style="clear:both;"></div>
  <div style="float: left;padding-left: 75px;">賣方：</div><div style="float: left;border-bottom: 1px solid black;width: 20%;">&nbsp;</div>
  <div style="clear:both;"></div>
</div>

<div class="contract-fixBlock category2" >
	<div style="">第一條、土地標示（下列標示事項如有未詳盡或未記載者，悉依地政機關之登記簿謄本記載為準）</div>
	<div style="">
	  	<table cellpadding="0" cellspacing="0" border="1">	
		<tr>
			<td colspan="5" width="50%">土地坐落</td>
			<td width="10%" align="center">面積</td>
			<td rowspan="2" width="15%">權利範圍</td>
			<td rowspan="2" width="25%">都市計畫使用分區或<br/>非都市土地使用類別</td>
		</tr>
		<tr>
			<td>縣市</td>
			<td>市區鄉鎮</td>
			<td>段</td>
			<td >小段</td>
			<td >地號</td>
			<td >平方公尺</td>
		</tr>
	  	</table>
	</div>
  	<div style="clear:both;"></div>
</div>
<div class="contract-fixBlock category2 c2_fix1" id="c2_fix1" style="display: none">
	<div>
		二、買賣雙方同意除第一期簽約款買方得以現金或即期支票給付外，其他各期之買賣價金買方應自行存滙入如下之履約保證專戶內(以下簡稱履保專戶)
	</div>
	<div>
		銀行別：___________________________戶  名：___________________________
		專屬帳號：___________________________
	</div>
	<div style="clear:both;"></div>
	
</div>

<div class="contract-Block">
	<ul id="ItemArea">
		<{foreach from=$data['data'] key=key2 item=item2}>
		

	  	<li class="item" id="item<{$key2}>">
	  		<div class="item-block">
				<div class="item-block-no">
					
					<select name="Indent[]" id="" onchange="setlistItme('item','listItem','no')" class="Indent">
						<{foreach from=$menuIndent key=key item=item}>
							<{if $key == $item2['Indent']}>
								<{assign var='selected' value='selected=selected'}> 
							<{else}>
								<{assign var='selected' value=''}> 
							<{/if}>
							<option value="<{$key}>" <{$selected}>><{$item}></option>
						<{/foreach}>
					</select>
					<select name="listItem[]"  onchange="setlistItme('item','listItem','no')" class="listItem">
						<{foreach from=$menulistItem key=key item=item}>
							<{if $key == $item2['listItem']}>
								<{assign var='selected' value='selected=selected'}> 
							<{else}>
								<{assign var='selected' value=''}> 
							<{/if}>
							<option value="<{$key}>" <{$selected}>><{$item}></option>
						<{/foreach}>
					</select>

					<span class="no"><{$item2.no}></span>
				</div>
	  			<div class="item-block-left">
					<textarea name="contract[]" class="xxx-textarea"><{$item2.contract}></textarea>
				</div>
				<div class="item-block-right">
					<input type="button" value="新增" onclick="add()">
					<input type="button" value="刪除" onclick="delItem('item<{$key2}>')" class="delItem">
					
				</div>
	  		</div>
	  		
			<div style="clear:both;"></div>
	  	</li>

	  	<{/foreach}>

		<li class="item" id="item<{$dataCount}>">
	  		<div class="item-block">
				<div class="item-block-no">
					
					<select name="Indent[]" id="" onchange="setlistItme('item','listItem','no')" class="Indent">
 						<{foreach from=$menuIndent key=key item=item}>
							
							<option value="<{$key}>" <{$selected}>><{$item}></option>
						<{/foreach}>
					</select>
					<select name="listItem[]"  onchange="setlistItme('item','listItem','no')" class="listItem">
							<{foreach from=$menulistItem key=key item=item}>
								<option value="<{$key}>"><{$item}></option>
							<{/foreach}>
					</select>
					<span class="no"></span>
				</div>
	  			<div class="item-block-left">
	  			
						<textarea name="contract[]" class="xxx-textarea"></textarea>
					
				</div>
				<div class="item-block-right">
					<input type="button" value="新增" onclick="add()">
					<input type="button" value="刪除" onclick="delItem('item<{$dataCount}>')" class="delItem">
					
				</div>
	  		</div>
	  		
			<div style="clear:both;"></div>
	  	</li>

	</ul>
</div>
<div class="contract-fixBlock apply">
	<div>此   致</div>
	<div>第一建築經理股份有限公司</div>
	<div>甲方簽章：___________________________</div>
	<div>乙方簽章：___________________________</div>
	<div>身分證（營利事業）統一編號及地址：依不動產買賣契約書所載</div>
	<div>丙方簽章：___________________________</div>
	<div>見證人：___________________________地政士</div>
	<div>身分證（營利事業）統一編號及地址：</div>
</div>