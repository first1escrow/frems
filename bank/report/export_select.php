<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

if ($_SESSION["member_pDep"] == 5 && $_SESSION["member_id"] != 1) {
    $str = " AND tOwner ='" . $_SESSION['member_name'] . "'";
}

$sql = "SELECT * FROM tBankTrans WHERE tOk = '2' " . $str . " GROUP BY tVR_Code";
$rs  = $conn->Execute($sql);
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>出款確認單</title>
    <link rel="stylesheet" type="text/css" href="../../libs/datatables/media/css/demo_page.css" />
    <link rel="stylesheet" type="text/css" href="../../libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css"
        rel="Stylesheet" />
    <!-- <link href="../../css/combobox.css" rel="stylesheet"> -->

    <script type="text/javascript" src="../../libs/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="../../libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $.widget("ui.combobox", {
            _create: function() {
                var input,
                    self = this,
                    select = this.element.hide(),
                    selected = select.children(":selected"),
                    value = selected.val() ? selected.text() : "",
                    wrapper = this.wrapper = $("<span>")
                    .addClass("ui-combobox")
                    .insertAfter(select);

                input = $("<input>")
                    .appendTo(wrapper)
                    .val(value)
                    .addClass("ui-state-default ui-combobox-input")
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: function(request, response) {
                            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request
                                .term), "i");
                            response(select.children("option").map(function() {
                                var text = $(this).text();
                                if (this.value && (!request.term || matcher
                                        .test(text)))
                                    return {
                                        label: text.replace(
                                            new RegExp(
                                                "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete
                                                .escapeRegex(request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)",
                                                "gi"
                                            ), "<strong>$1</strong>"),
                                        value: text,
                                        option: this
                                    };
                            }));
                        },
                        select: function(event, ui) {
                            ui.item.option.selected = true;
                            self._trigger("selected", event, {
                                item: ui.item.option
                            });
                            select.trigger("change");
                        },
                        change: function(event, ui) {
                            if (!ui.item) {
                                var matcher = new RegExp("^" + $.ui.autocomplete
                                        .escapeRegex($(this).val()) + "$", "i"),
                                    valid = false;
                                select.children("option").each(function() {
                                    if ($(this).text().match(matcher)) {
                                        this.selected = valid = true;
                                        $("[name='']")
                                        return false;
                                    }
                                });
                                if (!valid) {
                                    // remove invalid value, as it didn't match anything
                                    $(this).val("");
                                    select.val("");
                                    input.data("autocomplete").term = "";
                                    return false;
                                }
                            }
                        }
                    })
                    .addClass("ui-widget ui-widget-content ui-corner-left");

                input.data("autocomplete")._renderItem = function(ul, item) {
                    return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append("<a>" + item.label + "</a>")
                        .appendTo(ul);
                };

                $("<a>")
                    .attr("tabIndex", -1)
                    .attr("title", "Show All Items")
                    .appendTo(wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass("ui-corner-all")
                    .addClass("ui-corner-right ui-combobox-toggle")
                    .click(function() {
                        // close if already visible
                        if (input.autocomplete("widget").is(":visible")) {
                            input.autocomplete("close");
                            return;
                        }

                        // work around a bug (likely same cause as #5265)
                        $(this).blur();

                        // pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                        input.focus();
                    });
            },

            destroy: function() {
                this.wrapper.remove();
                this.element.show();
                $.Widget.prototype.destroy.call(this);
            }
        });
        $('[name=ac]').combobox();
    });
    </script>
    <style type="text/css">
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
        width: 160px;
    }

    .ui-autocomplete {
        width: 160px;
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        padding-right: 20px;
    }

    .ui-autocomplete {
        width: 160px;
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        padding-right: 20px;
    }

    .ui-autocomplete-input {
        width: 120px;
    }
    </style>
</head>

<body id="dt_example">
    <form id="form1" name="form1" method="post" action="export_list_all.php">
        請選擇 專屬帳號:
        <label for="ac"></label>
        <select name="ac" id="ac">
            <?php while (!$rs->EOF) {?>
            <option value="<?php echo $rs->fields["tMemo"]; ?>"><?php echo $rs->fields["tMemo"]; ?></option>
            <?php $rs->MoveNext();}?>

        </select>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="button" id="button" value="送出" />
    </form>
</body>

</html>