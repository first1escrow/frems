
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
					<{foreach from=$list[$key][$area] key=k item=branch}>
					<li>
						<div class="arrow"></div>
						<div class="checkbox <{$branch.ck}>" id="row_<{$branch.bId}>"></div>
						
						
						<{if $branch.ck == 'checked'}>
							<input type="checkbox" value="<{$branch.bId}>" name="branch[]" checked >
						<{else}>
							<input type="checkbox" value="<{$branch.bId}>" name="branch[]"  >
						<{/if}>

						
						<label class=""><{$branch.brand}><{$branch.bStore}>(<{$branch.salesname}>)</label>
					</li>
					<{/foreach}>
				</ul>
			</li>
			<{/foreach}>
		</ul>
	</li>
<{/foreach}>
</ul>