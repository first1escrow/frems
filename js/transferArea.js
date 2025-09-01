function transferArea(certifiedId, target, identifyId) {
  $("#dialog").empty();
  let url = "/includes/escrow/getTransferArea.php";

  $("#dialog").dialog({
    width: 500,
    title: "移轉範圍",
    buttons: {
      Update: {
        text: "更新",
        id: "transfer-btn-" + target + "-" + identifyId,
        click: function () {
          let res = transferRecordUpdate(certifiedId, target, identifyId);
          if (res == "Y") {
            alert("移轉範圍資訊不完整(分子分母需大於 0)");
          } else {
            alert("更新完成");
            $(this).dialog("close");
          }
        }
      },
      Cancel: {
        text: "返回",
        click: function () {
          $(this).dialog("close");
        }
      }
    }
  });

  $.post(
    url,
    {
      cId: certifiedId,
      identifyId: identifyId,
      target: target
    },
    function (response) {
      let el =
        '<div style="margin:0px auto;width:400px;"><div class="transfer-class">類別</div><div class="transfer-power">移轉範圍</div><div style="clear:both;"></div><hr>';

      $.each(response.data, function (k, v) {
        let type = v.type == "L" ? "地號" : "建號";

        el += '<div class="transfer-class">' + type + "：" + v.no + "</div>";
        el += '<div class="transfer-power">';
        el +=
          '<input type="number" name="transfer-power1[]" style="width:100px;text-align:right;" value="' +
          v.power1 +
          '">／';
        el +=
          '<input type="number" name="transfer-power2[]" style="width:100px;text-align:right;" value="' +
          v.power2 +
          '">';
        el += "</div>";
        el += '<div style="clear:both;">';

        if (v.before && ["2", "4"].includes(target)) {
          el += '<div class="">';

          $.each(v.before, function (ka, va) {
            el +=
              '<div class="transfer-before">　前次&nbsp;&nbsp;<input type="checkbox" name="before[]" value="' +
              identifyId +
              "-" +
              va.cLandItem +
              "-" +
              va.cItem +
              '"';

            el += va.checked == "Y" ? " checked" : "";

            el +=
              ">&nbsp;地價：" +
              va.cLandPrice +
              "、日期：" +
              va.date +
              "、範圍：" +
              va.power +
              "</div>";
          });
        }

        el += "</div >";
        el += '<div style="clear:both;height:5px;">';

        el +=
          '<input type="hidden" name="transfer-type[]" value="' + v.type + '">';
        el +=
          '<input type="hidden" name="transfer-item[]" value="' + v.item + '">';
        el +=
          '<input type="hidden" name="transfer-cCertifiedId[]" value="' +
          certifiedId +
          '">';
        el += "</div><div style='height:5px;'></div>";
      });

      el += "</div>";

      $("#dialog").html(el).dialog("open");
      if (response.data.length === 0) {
        $("#transfer-btn-" + target + "-" + identifyId).hide();
      }
    },
    "json"
  ).fail(function (xhr, textStatus, errorThrown) {
    alert(xhr.responseText);
  });
}

function transferRecordUpdate(certifiedId, target, identifyId) {
  let cId = certifiedId;
  let type = $('[name="transfer-type[]"]')
    .map(function () {
      return $(this).val();
    })
    .get();
  let item = $('[name="transfer-item[]"]')
    .map(function () {
      return $(this).val();
    })
    .get();
  let power1 = $('[name="transfer-power1[]"]')
    .map(function () {
      return $(this).val();
    })
    .get();
  let power2 = $('[name="transfer-power2[]"]')
    .map(function () {
      return $(this).val();
    })
    .get();
  let before = $('[name="before[]"]:checked')
    .map(function () {
      return $(this).val();
    })
    .get();

  //   let empty = "N";
  //   $.each(power1, function (k, v) {
  //     if (v == "" || v == undefined || v == null || v < 0) {
  //       empty = "Y";
  //     }
  //   });

  //   $.each(power2, function (k, v) {
  //     if (v == "" || v == undefined || v == null || v < 0) {
  //       empty = "Y";
  //     }
  //   });

  //   if (empty == "Y") {
  //     return empty;
  //   }

  let data = {
    cId: cId,
    type: type,
    identifyId: identifyId,
    item: item,
    power1: power1,
    power2: power2,
    target: target,
    before: before
  };

  let url = "/includes/escrow/getTransferAreaUpdate.php";
  $.post(
    url,
    data,
    function (response) {
      return "OK";
    },
    "json"
  ).fail(function (xhr, textStatus, errorThrown) {
    alert(xhr.responseText);
  });
}
