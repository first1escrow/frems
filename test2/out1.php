<?php
include('../../openadodb.php') ;
include('../../web_addr.php') ;
include_once '../../session_check.php' ;

$save = $_POST["save"];


$vr = empty($_POST['vr']) 
        ? $_GET["vr"]
        : $_POST["vr"];

// $vr = $_POST['vr'] ;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出帳建檔作業 v2.0</title>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src='codebase/message.js'></script>
	<link rel="stylesheet" type="text/css" href="codebase/themes/message_default.css">
<style>
.tb th{
	background-color: #E4BEB1;
}
.tb td{
	background-color: #F8ECE9;
	padding:5px;
}
.font12{
	font-size:12px;
}
.ui-combobox {
		position: relative;
		display: inline-block;
}
.ui-combobox-toggle {
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		padding: 0;
		/* adjust styles for IE 6/7 */
		*height: 1.5em;
		*top: 0.1em;
}
.ui-combobox-input {
	margin: 0;
	padding: 0.1em;
}
.ui-autocomplete {
	width:150px;
	max-height: 150px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar */
	padding-right: 20px;
}

/* IE 6 doesn't support max-height
 * we use height instead, but this forces the menu to always be this tall
 */
* html .ui-autocomplete {
	height: 150px;
}
</style>
<script>
// (function( $ ) {
// 		window.resizeTo(1500,1000) ;
// 		window.moveTo(100,100) ;
// 		$.widget( "ui.combobox", {
// 			_create: function() {
// 				var input,
// 					self = this,
// 					select = this.element.hide(),
// 					selected = select.children( ":selected" ),
// 					value = selected.val() ? selected.text() : "",
// 					wrapper = this.wrapper = $( "<span>" )
// 						.addClass( "ui-combobox" )
// 						.insertAfter( select );

// 				input = $( "<input>" )
// 					.appendTo( wrapper )
// 					.val( value )
// 					.addClass( "ui-state-default ui-combobox-input" )
// 					.autocomplete({
// 						delay: 0,
// 						minLength: 0,
// 						source: function( request, response ) {
// 							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
// 							response( select.children( "option" ).map(function() {
// 								var text = $( this ).text();
// 								if ( this.value && ( !request.term || matcher.test(text) ) )
// 									return {
// 										label: text.replace(
// 											new RegExp(
// 												"(?![^&;]+;)(?!<[^<>]*)(" +
// 												$.ui.autocomplete.escapeRegex(request.term) +
// 												")(?![^<>]*>)(?![^&;]+;)", "gi"
// 											), "<strong>$1</strong>" ),
// 										value: text,
// 										option: this
// 									};
// 							}) );
// 						},
// 						select: function( event, ui ) {
// 							ui.item.option.selected = true;
// 							self._trigger( "selected", event, {
// 								item: ui.item.option
// 							});
// 						},
// 						change: function( event, ui ) {
// 							if ( !ui.item ) {
// 								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
// 									valid = false;
// 								select.children( "option" ).each(function() {
// 									if ( $( this ).text().match( matcher ) ) {
// 										this.selected = valid = true;
// 										return false;
// 									}
// 								});
// 								if ( !valid ) {
// 									// remove invalid value, as it didn't match anything
// 									$( this ).val( "" );
// 									select.val( "" );
// 									input.data( "autocomplete" ).term = "";
// 									return false;
// 								}
// 							}
// 						}
// 					})
// 					.addClass( "ui-widget ui-widget-content ui-corner-left" );

// 				input.data( "autocomplete" )._renderItem = function( ul, item ) {
// 					return $( "<li></li>" )
// 						.data( "item.autocomplete", item )
// 						.append( "<a>" + item.label + "</a>" )
// 						.appendTo( ul );
// 				};

// 				$( "<a>" )
// 					.attr( "tabIndex", -1 )
// 					.attr( "title", "Show All Items" )
// 					.appendTo( wrapper )
// 					.button({
// 						icons: {
// 							primary: "ui-icon-triangle-1-s"
// 						},
// 						text: false
// 					})
// 					.removeClass( "ui-corner-all" )
// 					.addClass( "ui-corner-right ui-combobox-toggle" )
// 					.click(function() {
// 						// close if already visible
// 						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
// 							input.autocomplete( "close" );
// 							return;
// 						}

// 						// work around a bug (likely same cause as #5265)
// 						$( this ).blur();

// 						// pass empty string as value to search for, displaying all results
// 						input.autocomplete( "search", "" );
// 						input.focus();
// 					});
// 			},

// 			destroy: function() {
// 				this.wrapper.remove();
// 				this.element.show();
// 				$.Widget.prototype.destroy.call( this );
// 			}
// 		});
// 	})( jQuery );
// $(function() {
// 		$( "#vr_code" ).combobox();
		
// 	});
// var _pos=1;

function showSms(){
	var radiokind = $("[name='radiokind']:checked").val();
	var vr = $("[name='vr']").html();
	var smsSend = $("[name='smsSend']:checked").val();
	// console.log(radiokind);
	if (radiokind == '' || radiokind == undefined) {
		alert("請選擇出款項目");
		return false;
	}

	if (smsSend == 2) {

		$.ajax({
			url: 'getBankTranSms.php',
			type: 'POST',
			dataType: 'html',
			data: {radiokind:radiokind,vr:<?=$vr?>},
		})
		.done(function(msg) {

			$("#smsShow").html(msg);
		});
	}else{
		$("#smsShow").html('');
	}

	
}
function checkALL(){
	var all = $('[name="all"]').prop('checked');

	// console.log(all);
	if (all == true) {
		$('[name="allForm[]"]').prop('checked', true);
	}else{
		$('[name="allForm[]"]').prop('checked', false);
	}
	
}

function checkSms(){
	var smsSend = $("[name='smsSend']:checked").val();
	var checkCount = 0;
	if (smsSend == 2) {
		$("[name='allForm[]']").each(function() {
			if ($(this).prop('checked')) {
				checkCount++;
			}
		});

		if (checkCount == 0) {
			alert("請選擇寄送對象");
			return false;
		}
	}

	$('[name="form1"]').submit();
}
</script>
</head>

<body>
<div style="width:1290px; margin-bottom:5px; height:22px; background-color: #CCC">
<?php
//<div style="float:left;margin-left: 10px;"> <font color=red><strong>建檔</strong></font> </div>
?>
<div style="float:left;margin-left: 10px;"> <a href="<?=$web_addr?>/bank/list2.php">待修改資料</a> </div>
<?php
//if ($_SESSION["member_id"] != '1' and $_SESSION["member_id"] != '5' ) { 
if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
?>
<div style="float:left; margin-left: 10px;"> <a href="<?=$web_addr?>/bank/list.php">未審核列表</a></div>
<?php } ?>
</div>
<form id="form1" name="form1" method="post" action="out2.php">
  <table width="682" border="0">
    <tr>
      <td colspan="4">專屬帳號:
		<input type="text" value="<?=$vr?>" disabled="disabled">
		<input type="hidden" name="vr_code" value="<?=$vr?>">
		<input name="saveX" type="hidden" id="saveX" value="ok" />
	  </td>
    </tr>
    <tr>
      <td colspan="4">項目選擇:      </td>
    </tr>
    <tr>
      <td width="77">&nbsp;</td>
      <td width="264"><input type="radio" name="radiokind" id="radio" value="點交" onclick="showSms()"/>
        <label for="radiokind"></label>
		<label for="objKind[]">點交</label>
	  </td>
      <td width="77">&nbsp;</td>
      <td width="264">
      	<input type="radio" name="radiokind" id="radio9" value="賣方仲介服務費" onclick="showSms()"/>賣方仲介服務費
      	
        
	  </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="radio" name="radiokind" id="radio2" value="賣方先動撥" onclick="showSms()"/>
        賣方先動撥</td>
	  <td>&nbsp;</td>
	  <td><input type="radio" name="radiokind" id="radio4" value="買方仲介服務費" onclick="showSms()"/>買方仲介服務費</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="radio" name="radiokind" id="radio3" value="扣繳稅款" onclick="showSms()"/>
        扣繳稅款</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="radio" name="radiokind" id="radio5" value="代清償" onclick="showSms()"/>
      代清償、調帳</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="radio" name="radiokind" id="radio6" value="解除契約" onclick="showSms()"/>
      解約/終止履保</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
     <tr>
      <td>&nbsp;</td>
      <td><input type="radio" name="radiokind" id="radio7" value="保留款撥付" onclick="showSms()"/>
      保留款撥付</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="radio" name="radiokind" id="radio8" value="建經發函終止" onclick="showSms()"/>
      建經發函終止</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr>
    	<td colspan="4">簡訊發送：</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    	<td colspan="3"><input type="radio" name="smsSend" id="" value="0" checked="" onclick="showSms()" />預設</td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    	<td colspan="3">
    		<input type="radio" name="smsSend" id="" value="2" onclick="showSms()" />自選對象
    		<!-- <div id="smsShow"></div> -->
    		<span id="smsShow">
    			
    		</span>
    	</td>
    </tr>

    <tr>
    	<td>&nbsp;</td>
    	<td colspan="3"><input type="radio" name="smsSend" id="" value="1" onclick="showSms()" />全不寄送</td>
    </tr>
     <tr>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td align="right"><input type="button" name="button" id="button" value="下一步" onclick="checkSms()" /></td>
    </tr>
  </table>
</form>
<script type="text/javascript">
<?php if ($_REQUEST["ok"] == "1") { ?>
							dhtmlx.alert({
								type:"alert-error",
								text:"新增成功"
							});
<?php } ?>							
</script>
</body>
</html>
