<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    // setTimeout(function () {
    //     $('.cmc_overlay').hide();
    // }, 2000);

    // $('.cmc_overlay').show();
});

</script>
<style>
.ul-class {
    list-style: none;
    position: relative;
}

.ul-class li {
    /* display: inline; */
    font-size: 10pt;
}

.border-block {
    margin: 20px;
    padding: 10px;
    border: 1px solid #CCC;
    border-radius: 10px;
}
</style>
</head>
<body id="dt_example">
    <div class="cmc_overlay" style="display:none;">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

    <form method="POST">
        <div>
            <button type="submit" class="btn btn-primary">儲存</button>
        </div>

        <div>
            <input type="hidden" name="cId" value="<{$cId}>">

            <{foreach from=$buyers key=k item=v}>
            <div class="border-block">
                <div>
                    <span><h3>買方：<{$v.name}>（<{$v.identify_id}>）</h3></span>
                </div>
                <div>
                    <div>
                        <fieldset>
                        <{foreach from=$v.lands key=ka item=va}>
                        <{if isset($va.before) && $va.before}>
                        <{foreach from=$va.before key=kb item=vb}>
                        <label><input type="checkbox" name="before[]" value="<{$v.cCertifiedId}>_<{$v.target}>_<{$vb.cLandItem}>_<{$vb.cItem}>_<{$v.identify_id}>" <{if $vb.selected == 'Y'}>checked<{/if}>>&nbsp;前次明細：</label>
                        <ul class="ul-class">
                            <li><{$va.cLand1}>段<{$va.cLand2}>小段、地號：<{$va.cLand3}></li>
                            <li>前次移轉現值或原規定地價：<{$vb.cMoveDate}>、<{$vb.cLandPrice}>元/M<sup>2</sup></li>
                            <li>權利範圍：<{$vb.power}></li>
                        </ul>
                        <{/foreach}>
                        <{/if}>
                        <{/foreach}>
                        </fieldset>
                    </div>
                </div>
                <div style="height:20px;"></div>
            </div>
            <{/foreach}>

            <{foreach from=$owners key=k item=v}>
            <div class="border-block">
                <div>
                    <span><h3>賣方：<{$v.name}>（<{$v.identify_id}>）</h3></span>
                </div>
                <div>
                    <div>
                        <fieldset>
                        <{foreach from=$v.lands key=ka item=va}>
                        <{if isset($va.before) && $va.before}>
                        <{foreach from=$va.before key=kb item=vb}>
                        <label><input type="checkbox" name="before[]" value="<{$v.cCertifiedId}>_<{$v.target}>_<{$vb.cLandItem}>_<{$vb.cItem}>_<{$v.identify_id}>" <{if $vb.selected == 'Y'}>checked<{/if}>>&nbsp;前次明細：</label>
                        <ul class="ul-class">
                            <li><{$va.cLand1}>段<{$va.cLand2}>小段、地號：<{$va.cLand3}></li>
                            <li>前次移轉現值或原規定地價：<{$vb.cMoveDate}>、<{$vb.cLandPrice}>元/M<sup>2</sup></li>
                            <li>權利範圍：<{$vb.power}></li>
                        </ul>
                        <{/foreach}>
                        <{/if}>
                        <{/foreach}>
                        </fieldset>
                    </div>
                </div>
                <div style="height:20px;"></div>
            </div>
            <{/foreach}>
        </div>
    </form>
</body>
</html>