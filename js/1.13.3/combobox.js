//<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
//<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
//<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">

$(function() {
    $.widget("custom.combobox", {
        _create: function() {
            this.wrapper = $("<span>")
                .addClass("custom-combobox")
                .insertAfter(this.element);

            this.element.hide();
            this._createAutocomplete();
            this._createShowAllButton();
        },

        _createAutocomplete: function() {
            let selected = this.element.children(":selected"),
                value = selected.val() ? selected.text().trim() : "";

            this.input = $("<input>")
                .appendTo(this.wrapper)
                .val(value)
                .attr("title", "")
                .attr("style", "width: 900px")
                .addClass(
                    "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left"
                )
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    source: this._source.bind(this)
                })
                .tooltip({
                    classes: {
                        "ui-tooltip": "ui-state-highlight"
                    }
                });

            this._on(this.input, {
                autocompleteselect: function(event, ui) {
                    ui.item.option.selected = true;
                    this._trigger("select", event, {
                        item: ui.item.option
                    });
                },

                autocompletechange: "_removeIfInvalid"
            });
        },

        _createShowAllButton: function() {
            let input = this.input,
                wasOpen = false;

            $("<a>")
                .attr("tabIndex", -1)
                .attr("title", "顯示全部")
                .tooltip()
                .appendTo(this.wrapper)
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text: false
                })
                .removeClass("ui-corner-all")
                .addClass("custom-combobox-toggle ui-corner-right")
                .on("mousedown", function() {
                    wasOpen = input.autocomplete("widget").is(":visible");
                })
                .on("click", function() {
                    input.trigger("focus");

                    // Close if already visible
                    if (wasOpen) {
                        return;
                    }

                    // Pass empty string as value to search for, displaying all results
                    input.autocomplete("search", "");
                });
        },

        _source: function(request, response) {
            let matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
            response(this.element.children("option").map(function() {
                var text = $(this).text().trim();
                if (this.value && (!request.term || matcher.test(text)))
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
            }));
        },

        _removeIfInvalid: function(event, ui) {
            // Selected an item, nothing to do
            if (ui.item) {
                return;
            }

            // Search for a match (case-insensitive)
            let value = this.input.val().trim(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
            this.element.children("option").each(function() {
                if ($(this).text().toLowerCase() === valueLowerCase) {
                    this.selected = valid = true;
                    return false;
                }
            });

            // Found a match, nothing to do
            if (valid) {
                return;
            }

            // Remove invalid value
            this.input
                .val("")
                .attr("title", value + " 無符合項目")
                .tooltip("open");
            this.element.val("");
            this._delay(function() {
                this.input.tooltip("close").attr("title", "");
            }, 2500);
            this.input.autocomplete("instance").term = "";
        },

        _destroy: function() {
            this.wrapper.remove();
            this.element.show();
        }
    });

    //   $( "#combobox" ).combobox();
    //   $( "#toggle" ).on( "click", function() {
    //     $( "#combobox" ).toggle();
    //   });
});

