
<script type="text/javascript">
	$(document).ready(function() {
		$('ul.tree').checkTree();
	});
</script>
<ul class="tree" style="margin-left: 15px;">
<{foreach from=$list key=key item=item}>
<li>
		<div class="arrow collapsed"></div> <!-- arrow collapsed 合 arrow expanded開-->
	    <div class="checkbox <{$citycheck[$key]}>"></div> <!-- half_checked非全選 checked全選-->
		<input type="checkbox" name="city[]" value="<{$key}>" style="display: none;">
		<label class=""><{$key}></label>
		<ul style="display: none;">
			<{foreach from=$list[$key] key=area item=i}>
			<li>
				<div class="arrow collapsed"></div>
				<div class="checkbox <{$areacheck[$key][$area]}>"></div>
				<input type="checkbox" name="area[]" value="<{$area}>" >
				<label class=""><{$area}></label>
				<ul style="display: none;">
					<{foreach from=$list[$key][$area] key=k item=scrivener}>
					<li>
						<div class="arrow"></div>
						<div class="checkbox <{$scrivener.ck}>" id="row_<{$scrivener.sId}>"></div>
						
						
						<{if $scrivener.ck == 'checked'}>
							<input type="checkbox" value="<{$scrivener.sId}>" name="scrivener[]" checked >
						<{else}>
							<input type="checkbox" value="<{$scrivener.sId}>" name="scrivener[]"  >
						<{/if}>

						
						<label class=""><{$scrivener.sName}>[<{$scrivener.sOffice}>](<{$scrivener.salesname}>)</label>
					</li>
					<{/foreach}>
				</ul>
			</li>
			<{/foreach}>
		</ul>
	</li>
<{/foreach}>
</ul>