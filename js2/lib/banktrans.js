//鎖住欄位
function Lock() {
  var array = "input,select,textarea";

  $(".lock").each(function () {
    // $(this).val();
    if ($(this).val()) {
      $(".lock" + $(this).val())
        .find(array)
        .each(function () {
          $(this).attr("disabled", true);
          // console.log();
        });

      $(".lock" + $(this).val() + " .combobox").combobox("destroy");
    }
  });
}

function unLock(id) {
  var array = "input,select,textarea";

  $(".lock" + id)
    .find(array)
    .each(function () {
      $(this).attr("disabled", false);
      // console.log();
    });

  $(".lock" + id + " .combobox").combobox();

  $(".lseller" + id).attr("disabled", true); //ot_seller
  $(".lbuyer" + id).attr("disabled", true); //ot_buyer
}

// checkChoiceDetail(保證號碼,項目編號,類別)
function checkChoiceDetail(vr_code, index, cat) {
  //cat
  if (cat == "2") {
    var url = "getTaxDetail.php?cid=" + vr_code + "&t=tm" + index;
  } else {
    var url =
      "getExpenseDetail.php?cid=" +
      vr_code +
      "&t=tm" +
      index +
      "&cat=" +
      $("#taxScrivener" + index).val();
  }

  $.colorbox({ href: url });
}
//顯示新增項目
function show_other() {
  //<span class="ui-icon ui-icon-triangle-1-s"></span>

  $("#other_all").toggle(showOrHide);
  if (showOrHide == true) {
    $("#other_all").hide();
    $("#pp")
      .removeClass("ui-icon ui-icon-triangle-1-s")
      .addClass("ui-icon ui-icon-triangle-1-e");
    showOrHide = false;
  } else if (showOrHide == false) {
    $("#other_all").show();
    $("#pp")
      .removeClass("ui-icon ui-icon-triangle-1-e")
      .addClass("ui-icon ui-icon-triangle-1-s");
    showOrHide = true;
  }
}

//setTxt(項目編號,select值,附言欄位名稱,出款欄位名稱,金額欄位名稱)
function setTxt(vr_code, index, val, name, name2, mName) {
  $("#" + name2 + index).combobox("destroy");

  let vv = $('[name="radiokind"]').val();
  if (vv == "點交") {
    vv = "點交(結案)";
  }

  //如果是保證費要帶保號 保證費只顯示保留點交(結案) 解約終止履保 建經發函終止
  if (val == "保證費") {
    $("#" + name + index).text($("#certifiedId").val());
    $("#" + name2 + index + " option").remove();
    $("#" + name2 + index).html(
      '<option value="" selected="selected">項目</option><option value="點交(結案)" >點交(結案)</option><option value="解除契約">解約/終止履保</option><option value="建經發函終止">建經發函終止</option>'
    );
    $("#" + mName + index).val($("[name='realCertifiedMoney']").val());
  } else {
    $("#" + name + index).text("");
    $("#" + name2 + index + " option").remove();

    let el =
      '<option value="" >項目</option>' +
      '<option value="賣方先動撥">賣方先動撥</option>' +
      '<option value="仲介服務費">仲介服務費</option>' +
      '<option value="代清償">代清償</option>' +
      '<option value="點交(結案)">點交(結案)</option>' +
      '<option value="其他">其他</option>' +
      '<option value="調帳">調帳</option>' +
      '<option value="解除契約">解約/終止履保</option>' +
      '<option value="保留款撥付">保留款撥付</option>' +
      '<option value="建經發函終止">建經發函終止</option>' +
      '<option value="預售屋">預售屋</option>' +
      '<option value="代墊利息">代墊利息</option>';
    $("#" + name2 + index).html(el);
  }

  $("#" + name2 + index).val(vv);
  $("#objKind" + index).combobox();

  //地政士銀行備註顯示
  bankNote($("#export" + index).val(), index);
}

/* 設定交易類別 */
function set_item(index, ex, exn) {
  // var index = 0 ;
  var bank = $("[name='bank_kind']").val();
  //alert('第'+no+'組,項目='+ex) ;

  $("#export" + index).val(ex);
  //同值不同項目

  $("#export" + index + " option").each(function () {
    if ($(this).text() == "聯行轉帳" && ex == "01") {
      $(this).attr("selected", true);
    } else if ($(this).text() == "臨櫃開票" && ex == "05") {
      $(this).attr("selected", true);
    }

    bankNote(ex, index);
  });
  $("#code2" + index).val(exn);
  $("#export" + index).combobox("destroy");
  setCombobox($("#export" + index));
}

/*取得交易名稱*/
function setCode2(name, v) {
  var val = $("#" + name)
    .find(":selected")
    .text();
  var bank = $("[name='bank_kind']").val();

  $("#code2" + v).val(val);

  bankNote($("#" + name).val(), v);

  // console.log(name+'_'+v);
  if ($("#" + name).val() == "04" || $("#" + name).val() == "05") {
    bankAccountAuto(v, "");
    if ($("#" + name).val() == "04") {
      $("#taxScrivener" + v).attr("disabled", "disabled");
      $("#taxScrivener" + v).hide();
    }

    if (val == "大額繳稅" && bank == "一銀") {
      $("#t_txt" + v).val("大額繳稅詳第一建經指示書編號");
    }

    if (val == "臨櫃開票" && bank == "一銀") {
      $("#t_txt" + v).val("臨櫃開票詳第一建經指示書編號");
    }
  } else {
    $("#taxScrivener" + v).show();
    $("#taxScrivener" + v).attr("disabled", "none");
  }
}

//顯示代書存摺備註欄位顯示買賣方
function bankNote(code, index) {
  var bank = $("[name='bank_kind']").val();
  var customer = $("#customer").val();
  // console.log(customer);

  if (
    (code == "01" || code == "02") &&
    $("#target" + index).val() == "地政士" &&
    bank == "永豐"
  ) {
    $("#Note" + index).show();
    $("#bankshowtxt" + index).replaceWith(
      '<input type="text" name="bankshowtxt[]" id="bankshowtxt' +
        index +
        '" maxlength="6" value="' +
        customer +
        '">'
    );
  } else if (
    (code == "01" || code == "02") &&
    $("#target" + index).val() == "地政士" &&
    bank == "台新"
  ) {
    $("#Note" + index).show();
    $("#bankshowtxt" + index).replaceWith(
      '<input type="text" name="bankshowtxt[]" id="bankshowtxt' +
        index +
        '" maxlength="6" value="' +
        customer +
        '">'
    );
  } else {
    $("#Note" + index).hide();
    $("#bankshowtxt" + index).val("");
    $("#bankshowtxt" + index).replaceWith(
      '<input type="hidden" name="bankshowtxt[]" id="bankshowtxt' +
        index +
        '" maxlength="6">'
    );
    // console.log($('#bankshowtxt'+no).attr('type'));
  }
}
